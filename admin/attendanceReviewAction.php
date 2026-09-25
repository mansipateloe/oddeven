<?php
require_once __DIR__ . "/dbconnect.php";
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../foundation.php";
require_once __DIR__ . "/../attendance.php";
oecrm_require_admin_login();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}
oecrm_require_csrf();
$action = $_POST["action"] ?? "";
$return = "attendanceReview.php";
if (!empty($_POST["return_query"])) {
    $return .= "?" . $_POST["return_query"];
}
$actor = (int) $_SESSION["adminId"];
$companyId = oecrm_current_company_id($conn);
function period_locked($conn, $companyId, $date)
{
    $y = (int) date("Y", strtotime($date));
    $m = (int) date("n", strtotime($date));
    $stmt = mysqli_prepare(
        $conn,
        "SELECT is_locked FROM attendance_period_locks WHERE company_id=? AND period_year=? AND period_month=?"
    );
    mysqli_stmt_bind_param($stmt, "iii", $companyId, $y, $m);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return !empty($r["is_locked"]);
}
if ($action === "manual_event") {
    oecrm_require_permission($conn, "attendance_review", "correct");
    $employeeId = (int) ($_POST["employee_id"] ?? 0);
    $eventType = $_POST["event_type"] ?? "";
    $eventInput = $_POST["event_time"] ?? "";
    $reason = trim($_POST["reason"] ?? "");
    $allowed = [
        "sign_in",
        "lunch_in",
        "lunch_out",
        "break_in",
        "break_out",
        "sign_out",
    ];
    if (
        !$employeeId ||
        !in_array($eventType, $allowed, true) ||
        !strtotime($eventInput) ||
        strlen($reason) < 5
    ) {
        http_response_code(400);
        exit("Employee, event time and reason are required.");
    }
    $eventTime = new DateTime($eventInput, new DateTimeZone("Asia/Kolkata"));
    $attendanceDate = $eventTime->format("Y-m-d");
    if (period_locked($conn, $companyId, $attendanceDate)) {
        http_response_code(423);
        exit("This attendance period is locked.");
    }
    $employee = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT id FROM employeestbl WHERE id=" .
                $employeeId .
                " AND company_id=" .
                $companyId .
                " AND status=0"
        )
    );
    if (!$employee) {
        http_response_code(404);
        exit("Active employee not found.");
    }
    $shift = oecrm_employee_shift($conn, $employeeId, $attendanceDate);
    if (!$shift) {
        throw new RuntimeException(
            "No active shift is assigned for this date."
        );
    }
    list($scheduledStart, $scheduledEnd) = oecrm_shift_schedule(
        $shift,
        $attendanceDate
    );
    $session = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT * FROM attendance_sessions WHERE employee_id=$employeeId AND attendance_date='" .
                mysqli_real_escape_string($conn, $attendanceDate) .
                "' LIMIT 1"
        )
    );
    mysqli_begin_transaction($conn);
    try {
        if (!$session) {
            if ($eventType !== "sign_in") {
                throw new RuntimeException(
                    "Add Sign In before other attendance events."
                );
            }
            $weeklyOff = oecrm_shift_is_weekly_off(
                $conn,
                (int) $shift["id"],
                $attendanceDate
            );
            $holiday = oecrm_is_holiday($conn, $attendanceDate, $companyId);
            $baseStatus = $holiday
                ? "holiday"
                : ($weeklyOff
                    ? "weekly_off"
                    : "incomplete");
            $startSql = $scheduledStart->format("Y-m-d H:i:s");
            $endSql = $scheduledEnd->format("Y-m-d H:i:s");
            $eventSql = $eventTime->format("Y-m-d H:i:s");
            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO attendance_sessions(employee_id,shift_id,attendance_date,scheduled_start,scheduled_end,actual_in,attendance_status,review_status,reviewed_by,reviewed_at,review_notes) VALUES(?,?,?,?,?,?,?,"corrected",?,NOW(),?)'
            );
            mysqli_stmt_bind_param(
                $stmt,
                "iisssssis",
                $employeeId,
                $shift["id"],
                $attendanceDate,
                $startSql,
                $endSql,
                $eventSql,
                $baseStatus,
                $actor,
                $reason
            );
            mysqli_stmt_execute($stmt);
            $sessionId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        } else {
            $sessionId = (int) $session["id"];
        }
        $eventSql = $eventTime->format("Y-m-d H:i:s");
        $eventNote = "Admin manual entry: " . $reason;
        $eventId = 0;
        $backdatedSignIn = false;
        if ($eventType === "sign_in" && $session) {
            $existingSignIn = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT id,event_time FROM attendance_events WHERE session_id=" .
                        $sessionId .
                        ' AND event_type="sign_in" ORDER BY event_time,id LIMIT 1'
                )
            );
            if (!$existingSignIn) {
                throw new RuntimeException(
                    "Existing attendance session has no Sign In event. Use attendance correction."
                );
            }
            if (
                strtotime($eventSql) >= strtotime($existingSignIn["event_time"])
            ) {
                throw new RuntimeException(
                    "Corrected Sign In must be earlier than the existing Sign In time."
                );
            }
            $eventId = (int) $existingSignIn["id"];
            $stmt = mysqli_prepare(
                $conn,
                'UPDATE attendance_events SET event_time=?,ip_address="admin",user_agent="Admin portal",notes=? WHERE id=?'
            );
            $correctionNote =
                "Admin corrected Sign In from " .
                $existingSignIn["event_time"] .
                " to " .
                $eventSql .
                ": " .
                $reason;
            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $eventSql,
                $correctionNote,
                $eventId
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $backdatedSignIn = true;
        } else {
            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO attendance_events(session_id,employee_id,event_type,event_time,ip_address,user_agent,notes) VALUES(?,?,?,?,"admin","Admin portal",?)'
            );
            mysqli_stmt_bind_param(
                $stmt,
                "iisss",
                $sessionId,
                $employeeId,
                $eventType,
                $eventSql,
                $eventNote
            );
            mysqli_stmt_execute($stmt);
            $eventId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        }
        $calculation = oecrm_recalculate_attendance_session(
            $conn,
            $sessionId,
            "corrected",
            $actor,
            $reason
        );
        mysqli_commit($conn);
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        $_SESSION["attendance_review_error"] = $e->getMessage();
        header("Location: " . $return);
        exit();
    }
    $legacyColumns = [
        "sign_in" => "signinTime",
        "lunch_in" => "lunchinTime",
        "lunch_out" => "lunchoutTime",
        "break_in" => "breakinTime",
        "break_out" => "breakoutTime",
        "sign_out" => "signoutTime",
    ];
    oecrm_legacy_attendance_sync(
        $conn,
        $employeeId,
        $attendanceDate,
        $legacyColumns[$eventType],
        $eventTime->format("H:i")
    );
    if ($eventType === "sign_out") {
        oecrm_legacy_attendance_sync(
            $conn,
            $employeeId,
            $attendanceDate,
            "workHours",
            sprintf(
                "%02d:%02d",
                intdiv((int) $calculation["effective_minutes"], 60),
                (int) $calculation["effective_minutes"] % 60
            )
        );
    }
    oecrm_audit(
        $conn,
        "attendance",
        $backdatedSignIn
            ? "admin_correct_sign_in"
            : "admin_manual_" . $eventType,
        "attendance_session",
        $sessionId,
        $backdatedSignIn
            ? "Admin corrected employee Sign In time"
            : "Admin added attendance event",
        null,
        [
            "employee_id" => $employeeId,
            "event_id" => $eventId,
            "event" => $eventType,
            "event_time" => $eventSql,
            "reason" => $reason,
        ]
    );
    $_SESSION["attendance_review_flash"] = $backdatedSignIn
        ? "Sign In time corrected successfully. Attendance now calculates from " .
            $eventTime->format("h:i A") .
            "."
        : "Attendance event added successfully. No approval is required.";
    header("Location: " . $return);
    exit();
}
if (in_array($action, ["correct", "approve", "reject"], true)) {
    $sessionId = oecrm_int_param($_POST, "session_id");
    $stmt = mysqli_prepare(
        $conn,
        "SELECT s.*,sh.required_minutes FROM attendance_sessions s JOIN employeestbl e ON e.id=s.employee_id JOIN shifts sh ON sh.id=s.shift_id WHERE s.id=? AND e.company_id=?"
    );
    mysqli_stmt_bind_param($stmt, "ii", $sessionId, $companyId);
    mysqli_stmt_execute($stmt);
    $session = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$session) {
        http_response_code(404);
        exit("Attendance record not found.");
    }
    if (period_locked($conn, $companyId, $session["attendance_date"])) {
        http_response_code(423);
        exit("This attendance period is locked.");
    }
    if ($action === "correct") {
        oecrm_require_permission($conn, "attendance_review", "correct");
        $reason = trim($_POST["reason"] ?? "");
        $in = $_POST["actual_in"] ?? "";
        $out = $_POST["actual_out"] ?? "";
        $break = max(0, (int) ($_POST["break_minutes"] ?? 0));
        if (strlen($reason) < 10 || !$in) {
            http_response_code(400);
            exit("Sign-in and a correction reason are required.");
        }
        $inDt = new DateTime($in);
        $outDt = $out ? new DateTime($out) : null;
        if ($outDt && $outDt <= $inDt) {
            http_response_code(400);
            exit("Sign-out must be after sign-in.");
        }
        $total = $outDt
            ? max(
                0,
                (int) floor(
                    ($outDt->getTimestamp() - $inDt->getTimestamp()) / 60
                )
            )
            : 0;
        $effective = max(0, $total - $break);
        $required = (int) $session["required_minutes"];
        $overtime = max(0, $effective - $required);
        $scheduledEnd = new DateTime($session["scheduled_end"]);
        $early = $outDt
            ? max(
                0,
                (int) floor(
                    ($scheduledEnd->getTimestamp() - $outDt->getTimestamp()) /
                        60
                )
            )
            : 0;
        $status = $outDt
            ? ($effective >= $required / 2
                ? ((int) $session["late_minutes"] > 0
                    ? "late"
                    : "present")
                : "half_day")
            : "incomplete";
        $inSql = $inDt->format("Y-m-d H:i:s");
        $outSql = $outDt ? $outDt->format("Y-m-d H:i:s") : null;
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO attendance_corrections(session_id,employee_id,old_actual_in,new_actual_in,old_actual_out,new_actual_out,old_break_minutes,new_break_minutes,reason,requested_by,approved_by) VALUES(?,?,?,?,?,?,?,?,?,?,?)"
            );
            mysqli_stmt_bind_param(
                $stmt,
                "iissssiisii",
                $sessionId,
                $session["employee_id"],
                $session["actual_in"],
                $inSql,
                $session["actual_out"],
                $outSql,
                $session["break_minutes"],
                $break,
                $reason,
                $actor,
                $actor
            );
            mysqli_stmt_execute($stmt);
            $correctionId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            $stmt = mysqli_prepare(
                $conn,
                'UPDATE attendance_sessions SET actual_in=?,actual_out=?,total_minutes=?,break_minutes=?,effective_minutes=?,early_exit_minutes=?,overtime_minutes=?,attendance_status=?,review_status="corrected",reviewed_by=?,reviewed_at=NOW(),review_notes=? WHERE id=?'
            );
            mysqli_stmt_bind_param(
                $stmt,
                "ssiiiiisisi",
                $inSql,
                $outSql,
                $total,
                $break,
                $effective,
                $early,
                $overtime,
                $status,
                $actor,
                $reason,
                $sessionId
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_commit($conn);
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            throw $e;
        }
        oecrm_legacy_attendance_sync(
            $conn,
            $session["employee_id"],
            $session["attendance_date"],
            "signinTime",
            $inDt->format("H:i")
        );
        if ($outDt) {
            oecrm_legacy_attendance_sync(
                $conn,
                $session["employee_id"],
                $session["attendance_date"],
                "signoutTime",
                $outDt->format("H:i")
            );
            oecrm_legacy_attendance_sync(
                $conn,
                $session["employee_id"],
                $session["attendance_date"],
                "workHours",
                sprintf("%02d:%02d", intdiv($effective, 60), $effective % 60)
            );
        }
        oecrm_audit(
            $conn,
            "attendance_review",
            "correct",
            "attendance_session",
            $sessionId,
            "Attendance corrected",
            $session,
            [
                "actual_in" => $inSql,
                "actual_out" => $outSql,
                "break_minutes" => $break,
                "reason" => $reason,
            ]
        );
        $_SESSION["attendance_review_flash"] =
            "Attendance corrected successfully.";
    } else {
        oecrm_require_permission($conn, "attendance_review", $action);
        $review = $action === "approve" ? "approved" : "rejected";
        $notes = trim($_POST["reason"] ?? "");
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE attendance_sessions SET review_status=?,reviewed_by=?,reviewed_at=NOW(),review_notes=? WHERE id=?"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "sisi",
            $review,
            $actor,
            $notes,
            $sessionId
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        oecrm_audit(
            $conn,
            "attendance_review",
            $action,
            "attendance_session",
            $sessionId,
            "Attendance " . $review,
            null,
            ["review_status" => $review, "notes" => $notes]
        );
        $_SESSION["attendance_review_flash"] = "Attendance " . $review . ".";
    }
    header("Location: " . $return);
    exit();
}
if (in_array($action, ["lock", "unlock"], true)) {
    oecrm_require_permission($conn, "attendance_review", $action);
    $year = (int) ($_POST["year"] ?? 0);
    $month = (int) ($_POST["month"] ?? 0);
    $reason = trim($_POST["reason"] ?? "");
    if ($year < 2020 || $month < 1 || $month > 12 || $reason === "") {
        http_response_code(400);
        exit("Valid period and reason are required.");
    }
    if ($action === "lock") {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO attendance_period_locks(company_id,period_year,period_month,locked_at,locked_by,lock_reason,is_locked) VALUES(?,?,?,NOW(),?,?,1) ON DUPLICATE KEY UPDATE locked_at=NOW(),locked_by=VALUES(locked_by),lock_reason=VALUES(lock_reason),unlocked_at=NULL,unlocked_by=0,unlock_reason=NULL,is_locked=1"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "iiiis",
            $companyId,
            $year,
            $month,
            $actor,
            $reason
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $start = sprintf("%04d-%02d-01", $year, $month);
        $end = date("Y-m-t", strtotime($start));
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE attendance_sessions s JOIN employeestbl e ON e.id=s.employee_id SET s.is_locked=1 WHERE e.company_id=? AND s.attendance_date BETWEEN ? AND ?"
        );
        mysqli_stmt_bind_param($stmt, "iss", $companyId, $start, $end);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION["attendance_review_flash"] = "Attendance period locked.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE attendance_period_locks SET is_locked=0,unlocked_at=NOW(),unlocked_by=?,unlock_reason=? WHERE company_id=? AND period_year=? AND period_month=?"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "isiii",
            $actor,
            $reason,
            $companyId,
            $year,
            $month
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $start = sprintf("%04d-%02d-01", $year, $month);
        $end = date("Y-m-t", strtotime($start));
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE attendance_sessions s JOIN employeestbl e ON e.id=s.employee_id SET s.is_locked=0 WHERE e.company_id=? AND s.attendance_date BETWEEN ? AND ?"
        );
        mysqli_stmt_bind_param($stmt, "iss", $companyId, $start, $end);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION["attendance_review_flash"] = "Attendance period unlocked.";
    }
    oecrm_audit(
        $conn,
        "attendance_review",
        $action,
        "attendance_period",
        $year . "-" . $month,
        "Attendance period " . $action . "ed",
        null,
        ["year" => $year, "month" => $month, "reason" => $reason]
    );
    header("Location: " . $return);
    exit();
}
http_response_code(400);
exit("Invalid action.");