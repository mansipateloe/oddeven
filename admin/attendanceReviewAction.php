<?php
<<<<<<< HEAD
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../attendance.php';

oecrm_require_admin_login();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

oecrm_require_csrf();

$action = $_POST['action'] ?? '';
$return = 'attendanceReview.php';
if (!empty($_POST['return_query'])) {
    $return .= '?' . $_POST['return_query'];
}

$actor = (int) $_SESSION['adminId'];
$companyId = oecrm_current_company_id($conn);

function attendance_review_period_locked($conn, $companyId, $date)
{
    $y = (int) date('Y', strtotime($date));
    $m = (int) date('n', strtotime($date));
    $stmt = mysqli_prepare($conn, 'SELECT is_locked FROM attendance_period_locks WHERE company_id=? AND period_year=? AND period_month=?');
    mysqli_stmt_bind_param($stmt, 'iii', $companyId, $y, $m);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return !empty($row['is_locked']);
}

function attendance_review_active_employee($conn, $employeeId, $companyId)
{
    $stmt = mysqli_prepare($conn, 'SELECT id,company_id,status FROM employeestbl WHERE id=? AND company_id=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ii', $employeeId, $companyId);
    mysqli_stmt_execute($stmt);
    $employee = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $employee && (int) $employee['status'] === 0 ? $employee : null;
}

function attendance_review_session_by_date($conn, $employeeId, $attendanceDate)
{
    $stmt = mysqli_prepare($conn, 'SELECT * FROM attendance_sessions WHERE employee_id=? AND attendance_date=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'is', $employeeId, $attendanceDate);
    mysqli_stmt_execute($stmt);
    $session = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $session ?: null;
}

function attendance_review_datetime_for_date($date, $time)
{
    $time = trim((string) $time);
    if ($time === '') {
        throw new RuntimeException('Time is required.');
    }
    return new DateTime($date . ' ' . $time, new DateTimeZone('Asia/Kolkata'));
}

function attendance_review_sync_legacy($conn, $employeeId, $attendanceDate, array $eventTimes, $effectiveMinutes)
{
    $legacyMap = [
        'sign_in' => 'signinTime',
        'lunch_in' => 'lunchinTime',
        'lunch_out' => 'lunchoutTime',
        'break_in' => 'breakinTime',
        'break_out' => 'breakoutTime',
        'sign_out' => 'signoutTime',
    ];

    foreach ($legacyMap as $eventType => $column) {
        if (empty($eventTimes[$eventType]) || !($eventTimes[$eventType] instanceof DateTime)) {
            continue;
        }
        oecrm_legacy_attendance_sync($conn, $employeeId, $attendanceDate, $column, $eventTimes[$eventType]->format('H:i'));
    }

    oecrm_legacy_attendance_sync(
        $conn,
        $employeeId,
        $attendanceDate,
        'workHours',
        sprintf('%02d:%02d', intdiv((int) $effectiveMinutes, 60), (int) $effectiveMinutes % 60)
    );
}

if ($action === 'bulk_fill') {
    oecrm_require_permission($conn, 'attendance_review', 'correct');

    $employeeId = (int) ($_POST['employee_id'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $signInTime = trim((string) ($_POST['sign_in_time'] ?? ''));
    $signOutTime = trim((string) ($_POST['sign_out_time'] ?? ''));
    $lunchInTime = trim((string) ($_POST['lunch_in_time'] ?? ''));
    $lunchOutTime = trim((string) ($_POST['lunch_out_time'] ?? ''));
    $breakInTime = trim((string) ($_POST['break_in_time'] ?? ''));
    $breakOutTime = trim((string) ($_POST['break_out_time'] ?? ''));
    $reason = trim((string) ($_POST['reason'] ?? ''));
    $includeOffDays = !empty($_POST['include_off_days']);

    if (
        !$employeeId ||
        !$startDate ||
        !$endDate ||
        $signInTime === '' ||
        $signOutTime === '' ||
        $lunchInTime === '' ||
        $lunchOutTime === '' ||
        $breakInTime === '' ||
        $breakOutTime === '' ||
        strlen($reason) < 5
    ) {
        http_response_code(400);
        exit('Employee, date range, attendance times and reason are required.');
    }

    $employee = attendance_review_active_employee($conn, $employeeId, $companyId);
    if (!$employee) {
        http_response_code(404);
        exit('Active employee not found.');
    }

    $start = DateTime::createFromFormat('Y-m-d', $startDate, new DateTimeZone('Asia/Kolkata'));
    $end = DateTime::createFromFormat('Y-m-d', $endDate, new DateTimeZone('Asia/Kolkata'));
    if (!$start || !$end || $start > $end) {
        http_response_code(400);
        exit('Valid date range is required.');
    }

    $cursor = clone $start;
    while ($cursor <= $end) {
        if (attendance_review_period_locked($conn, $companyId, $cursor->format('Y-m-d'))) {
            http_response_code(423);
            exit('This attendance period is locked.');
        }
        $cursor->modify('+1 day');
    }

    $created = 0;
    $skippedExisting = 0;
    $skippedOffDays = 0;
    $skippedNoShift = 0;
    $failed = 0;

    $cursor = clone $start;
    while ($cursor <= $end) {
        $attendanceDate = $cursor->format('Y-m-d');
        $shift = oecrm_employee_shift($conn, $employeeId, $attendanceDate);
        if (!$shift) {
            $skippedNoShift++;
            $cursor->modify('+1 day');
            continue;
        }

        $weeklyOff = oecrm_shift_is_weekly_off($conn, (int) $shift['id'], $attendanceDate);
        $holiday = oecrm_is_holiday($conn, $attendanceDate, $companyId);
        if (!$includeOffDays && ($weeklyOff || $holiday)) {
            $skippedOffDays++;
            $cursor->modify('+1 day');
            continue;
        }

        if (attendance_review_session_by_date($conn, $employeeId, $attendanceDate)) {
            $skippedExisting++;
            $cursor->modify('+1 day');
            continue;
        }

        try {
            $signIn = attendance_review_datetime_for_date($attendanceDate, $signInTime);
            $signOut = attendance_review_datetime_for_date($attendanceDate, $signOutTime);
            $lunchIn = attendance_review_datetime_for_date($attendanceDate, $lunchInTime);
            $lunchOut = attendance_review_datetime_for_date($attendanceDate, $lunchOutTime);
            $breakIn = attendance_review_datetime_for_date($attendanceDate, $breakInTime);
            $breakOut = attendance_review_datetime_for_date($attendanceDate, $breakOutTime);

            if ($signOut <= $signIn) {
                $signOut->modify('+1 day');
            }

            $timeline = [
                'sign_in' => $signIn,
                'lunch_in' => $lunchIn,
                'lunch_out' => $lunchOut,
                'break_in' => $breakIn,
                'break_out' => $breakOut,
                'sign_out' => $signOut,
            ];

            $ordered = ['sign_in', 'lunch_in', 'lunch_out', 'break_in', 'break_out', 'sign_out'];
            $previousTime = null;
            foreach ($ordered as $eventType) {
                if (!$timeline[$eventType] instanceof DateTime) {
                    continue;
                }
                if ($previousTime && $timeline[$eventType] <= $previousTime) {
                    throw new RuntimeException('Attendance event times must be in chronological order.');
                }
                $previousTime = $timeline[$eventType];
            }

            [$scheduledStart, $scheduledEnd] = oecrm_shift_schedule($shift, $attendanceDate);
            $requiredMinutes = (int) $shift['required_minutes'];
            $actualInSql = $signIn->format('Y-m-d H:i:s');
            $actualOutSql = $signOut->format('Y-m-d H:i:s');
            $totalMinutes = max(0, (int) floor(($signOut->getTimestamp() - $signIn->getTimestamp()) / 60));
            $breakMinutes = 0;
            foreach ([['lunch_in', 'lunch_out'], ['break_in', 'break_out']] as $pair) {
                [$startKey, $endKey] = $pair;
                $breakMinutes += max(0, (int) floor(($timeline[$endKey]->getTimestamp() - $timeline[$startKey]->getTimestamp()) / 60));
            }
            $effectivePreview = max(0, $totalMinutes - $breakMinutes);
            $lateMinutes = max(0, (int) floor(($signIn->getTimestamp() - $scheduledStart->getTimestamp()) / 60));
            $attendanceStatus = $holiday ? 'holiday' : ($weeklyOff ? 'weekly_off' : ($effectivePreview >= ($requiredMinutes / 2) ? ($lateMinutes > 0 ? 'late' : 'present') : 'half_day'));

            mysqli_begin_transaction($conn);
            try {
                $stmt = mysqli_prepare(
                    $conn,
                    'INSERT INTO attendance_sessions(employee_id,shift_id,attendance_date,scheduled_start,scheduled_end,actual_in,attendance_status,review_status,reviewed_by,reviewed_at,review_notes) VALUES(?,?,?,?,?,?,?,"corrected",?,NOW(),?)'
                );
                $scheduledStartSql = $scheduledStart->format('Y-m-d H:i:s');
                $scheduledEndSql = $scheduledEnd->format('Y-m-d H:i:s');
                mysqli_stmt_bind_param(
                    $stmt,
                    'iisssssis',
                    $employeeId,
                    $shift['id'],
                    $attendanceDate,
                    $scheduledStartSql,
                    $scheduledEndSql,
                    $actualInSql,
                    $attendanceStatus,
                    $actor,
                    $reason
                );
                mysqli_stmt_execute($stmt);
                $sessionId = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);

                $signInNote = 'Bulk month fill: ' . $reason;
                $eventType = 'sign_in';
                $stmt = mysqli_prepare($conn, 'INSERT INTO attendance_events(session_id,employee_id,event_type,event_time,ip_address,user_agent,notes) VALUES(?,?,?,?,"admin","Admin portal",?)');
                mysqli_stmt_bind_param($stmt, 'iisss', $sessionId, $employeeId, $eventType, $actualInSql, $signInNote);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                foreach (['lunch_in' => $lunchIn, 'lunch_out' => $lunchOut, 'break_in' => $breakIn, 'break_out' => $breakOut] as $eventType => $eventTime) {
                    $eventSql = $eventTime->format('Y-m-d H:i:s');
                    $eventNote = 'Bulk month fill: ' . $reason;
                    $stmt = mysqli_prepare($conn, 'INSERT INTO attendance_events(session_id,employee_id,event_type,event_time,ip_address,user_agent,notes) VALUES(?,?,?,?,"admin","Admin portal",?)');
                    mysqli_stmt_bind_param($stmt, 'iisss', $sessionId, $employeeId, $eventType, $eventSql, $eventNote);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                $signOutNote = 'Bulk month fill: ' . $reason;
                $eventType = 'sign_out';
                $stmt = mysqli_prepare($conn, 'INSERT INTO attendance_events(session_id,employee_id,event_type,event_time,ip_address,user_agent,notes) VALUES(?,?,?,?,"admin","Admin portal",?)');
                mysqli_stmt_bind_param($stmt, 'iisss', $sessionId, $employeeId, $eventType, $actualOutSql, $signOutNote);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $calculation = oecrm_recalculate_attendance_session($conn, $sessionId, 'corrected', $actor, $reason);
                attendance_review_sync_legacy($conn, $employeeId, $attendanceDate, $timeline, $calculation['effective_minutes']);
                oecrm_audit(
                    $conn,
                    'attendance',
                    'admin_bulk_fill',
                    'attendance_session',
                    $sessionId,
                    'Bulk attendance fill',
                    null,
                    [
                        'employee_id' => $employeeId,
                        'date' => $attendanceDate,
                        'sign_in' => $actualInSql,
                        'sign_out' => $actualOutSql,
                        'reason' => $reason,
                    ]
                );
                mysqli_commit($conn);
                $created++;
            } catch (Throwable $e) {
                mysqli_rollback($conn);
                throw $e;
            }
        } catch (Throwable $e) {
            $failed++;
            $_SESSION['attendance_review_error'] = $e->getMessage();
        }

        $cursor->modify('+1 day');
    }

    $_SESSION['attendance_review_flash'] = sprintf(
        'Bulk month fill complete. Created %d day(s), skipped %d existing, %d off day(s), %d without shift, %d failed.',
        $created,
        $skippedExisting,
        $skippedOffDays,
        $skippedNoShift,
        $failed
    );
    header('Location: ' . $return);
    exit;
}

if ($action === 'manual_event') {
    oecrm_require_permission($conn, 'attendance_review', 'correct');

    $employeeId = (int) ($_POST['employee_id'] ?? 0);
    $eventType = $_POST['event_type'] ?? '';
    $eventInput = $_POST['event_time'] ?? '';
    $reason = trim($_POST['reason'] ?? '');
    $allowed = ['sign_in', 'lunch_in', 'lunch_out', 'break_in', 'break_out', 'sign_out'];

    if (!$employeeId || !in_array($eventType, $allowed, true) || !strtotime($eventInput) || strlen($reason) < 5) {
        http_response_code(400);
        exit('Employee, event time and reason are required.');
    }

    $eventTime = new DateTime($eventInput, new DateTimeZone('Asia/Kolkata'));
    $attendanceDate = $eventTime->format('Y-m-d');

    if (attendance_review_period_locked($conn, $companyId, $attendanceDate)) {
        http_response_code(423);
        exit('This attendance period is locked.');
    }

    $employee = attendance_review_active_employee($conn, $employeeId, $companyId);
    if (!$employee) {
        http_response_code(404);
        exit('Active employee not found.');
    }

    $shift = oecrm_employee_shift($conn, $employeeId, $attendanceDate);
    if (!$shift) {
        throw new RuntimeException('No active shift is assigned for this date.');
    }

    [$scheduledStart, $scheduledEnd] = oecrm_shift_schedule($shift, $attendanceDate);
    $session = attendance_review_session_by_date($conn, $employeeId, $attendanceDate);

    mysqli_begin_transaction($conn);
    try {
        if (!$session) {
            if ($eventType !== 'sign_in') {
                throw new RuntimeException('Add Sign In before other attendance events.');
            }

            $weeklyOff = oecrm_shift_is_weekly_off($conn, (int) $shift['id'], $attendanceDate);
            $holiday = oecrm_is_holiday($conn, $attendanceDate, $companyId);
            $baseStatus = $holiday ? 'holiday' : ($weeklyOff ? 'weekly_off' : 'incomplete');
            $startSql = $scheduledStart->format('Y-m-d H:i:s');
            $endSql = $scheduledEnd->format('Y-m-d H:i:s');
            $eventSql = $eventTime->format('Y-m-d H:i:s');

=======
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
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO attendance_sessions(employee_id,shift_id,attendance_date,scheduled_start,scheduled_end,actual_in,attendance_status,review_status,reviewed_by,reviewed_at,review_notes) VALUES(?,?,?,?,?,?,?,"corrected",?,NOW(),?)'
            );
<<<<<<< HEAD
            mysqli_stmt_bind_param($stmt, 'iisssssis', $employeeId, $shift['id'], $attendanceDate, $startSql, $endSql, $eventSql, $baseStatus, $actor, $reason);
=======
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
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
            mysqli_stmt_execute($stmt);
            $sessionId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        } else {
<<<<<<< HEAD
            $sessionId = (int) $session['id'];
        }

        $eventSql = $eventTime->format('Y-m-d H:i:s');
        $eventNote = 'Admin manual entry: ' . $reason;
        $eventId = 0;
        $backdatedSignIn = false;

        if ($eventType === 'sign_in' && $session) {
            $existingSignIn = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    'SELECT id,event_time FROM attendance_events WHERE session_id=' . $sessionId . ' AND event_type="sign_in" ORDER BY event_time,id LIMIT 1'
                )
            );
            if (!$existingSignIn) {
                throw new RuntimeException('Existing attendance session has no Sign In event. Use attendance correction.');
            }
            if (strtotime($eventSql) >= strtotime($existingSignIn['event_time'])) {
                throw new RuntimeException('Corrected Sign In must be earlier than the existing Sign In time.');
            }
            $eventId = (int) $existingSignIn['id'];
            $stmt = mysqli_prepare($conn, 'UPDATE attendance_events SET event_time=?, ip_address="admin", user_agent="Admin portal", notes=? WHERE id=?');
            $correctionNote = 'Admin corrected Sign In from ' . $existingSignIn['event_time'] . ' to ' . $eventSql . ': ' . $reason;
            mysqli_stmt_bind_param($stmt, 'ssi', $eventSql, $correctionNote, $eventId);
=======
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
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $backdatedSignIn = true;
        } else {
<<<<<<< HEAD
            $stmt = mysqli_prepare($conn, 'INSERT INTO attendance_events(session_id,employee_id,event_type,event_time,ip_address,user_agent,notes) VALUES(?,?,?,?,"admin","Admin portal",?)');
            mysqli_stmt_bind_param($stmt, 'iisss', $sessionId, $employeeId, $eventType, $eventSql, $eventNote);
=======
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
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
            mysqli_stmt_execute($stmt);
            $eventId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        }
<<<<<<< HEAD

        $calculation = oecrm_recalculate_attendance_session($conn, $sessionId, 'corrected', $actor, $reason);
        mysqli_commit($conn);
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        $_SESSION['attendance_review_error'] = $e->getMessage();
        header('Location: ' . $return);
        exit;
    }

    $legacyColumns = [
        'sign_in' => 'signinTime',
        'lunch_in' => 'lunchinTime',
        'lunch_out' => 'lunchoutTime',
        'break_in' => 'breakinTime',
        'break_out' => 'breakoutTime',
        'sign_out' => 'signoutTime',
    ];
    oecrm_legacy_attendance_sync($conn, $employeeId, $attendanceDate, $legacyColumns[$eventType], $eventTime->format('H:i'));
    if ($eventType === 'sign_out') {
        oecrm_legacy_attendance_sync($conn, $employeeId, $attendanceDate, 'workHours', sprintf('%02d:%02d', intdiv((int) $calculation['effective_minutes'], 60), (int) $calculation['effective_minutes'] % 60));
    }

    oecrm_audit(
        $conn,
        'attendance',
        $backdatedSignIn ? 'admin_correct_sign_in' : 'admin_manual_' . $eventType,
        'attendance_session',
        $sessionId,
        $backdatedSignIn ? 'Admin corrected employee Sign In time' : 'Admin added attendance event',
        null,
        [
            'employee_id' => $employeeId,
            'event_id' => $eventId,
            'event' => $eventType,
            'event_time' => $eventSql,
            'reason' => $reason,
        ]
    );

    $_SESSION['attendance_review_flash'] = $backdatedSignIn
        ? 'Sign In time corrected successfully. Attendance now calculates from ' . $eventTime->format('h:i A') . '.'
        : 'Attendance event added successfully. No approval is required.';
    header('Location: ' . $return);
    exit;
}

if (in_array($action, ['correct', 'approve', 'reject'], true)) {
    $sessionId = oecrm_int_param($_POST, 'session_id');
    $stmt = mysqli_prepare(
        $conn,
        'SELECT s.*,sh.required_minutes FROM attendance_sessions s JOIN employeestbl e ON e.id=s.employee_id JOIN shifts sh ON sh.id=s.shift_id WHERE s.id=? AND e.company_id=?'
    );
    mysqli_stmt_bind_param($stmt, 'ii', $sessionId, $companyId);
    mysqli_stmt_execute($stmt);
    $session = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$session) {
        http_response_code(404);
        exit('Attendance record not found.');
    }

    if (attendance_review_period_locked($conn, $companyId, $session['attendance_date'])) {
        http_response_code(423);
        exit('This attendance period is locked.');
    }

    if ($action === 'correct') {
        oecrm_require_permission($conn, 'attendance_review', 'correct');
        $reason = trim($_POST['reason'] ?? '');
        $in = $_POST['actual_in'] ?? '';
        $out = $_POST['actual_out'] ?? '';
        $break = max(0, (int) ($_POST['break_minutes'] ?? 0));
        if (strlen($reason) < 10 || !$in) {
            http_response_code(400);
            exit('Sign-in and a correction reason are required.');
=======
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
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
        }
        $inDt = new DateTime($in);
        $outDt = $out ? new DateTime($out) : null;
        if ($outDt && $outDt <= $inDt) {
            http_response_code(400);
<<<<<<< HEAD
            exit('Sign-out must be after sign-in.');
        }
        $total = $outDt ? max(0, (int) floor(($outDt->getTimestamp() - $inDt->getTimestamp()) / 60)) : 0;
        $effective = max(0, $total - $break);
        $required = (int) $session['required_minutes'];
        $overtime = max(0, $effective - $required);
        $scheduledEnd = new DateTime($session['scheduled_end']);
        $early = $outDt ? max(0, (int) floor(($scheduledEnd->getTimestamp() - $outDt->getTimestamp()) / 60)) : 0;
        $status = $outDt ? ($effective >= ($required / 2) ? ((int) $session['late_minutes'] > 0 ? 'late' : 'present') : 'half_day') : 'incomplete';
        $inSql = $inDt->format('Y-m-d H:i:s');
        $outSql = $outDt ? $outDt->format('Y-m-d H:i:s') : null;

=======
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
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare(
                $conn,
<<<<<<< HEAD
                'INSERT INTO attendance_corrections(session_id,employee_id,old_actual_in,new_actual_in,old_actual_out,new_actual_out,old_break_minutes,new_break_minutes,reason,requested_by,approved_by) VALUES(?,?,?,?,?,?,?,?,?,?,?)'
            );
            mysqli_stmt_bind_param($stmt, 'iissssiisii', $sessionId, $session['employee_id'], $session['actual_in'], $inSql, $session['actual_out'], $outSql, $session['break_minutes'], $break, $reason, $actor, $actor);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

=======
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
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
            $stmt = mysqli_prepare(
                $conn,
                'UPDATE attendance_sessions SET actual_in=?,actual_out=?,total_minutes=?,break_minutes=?,effective_minutes=?,early_exit_minutes=?,overtime_minutes=?,attendance_status=?,review_status="corrected",reviewed_by=?,reviewed_at=NOW(),review_notes=? WHERE id=?'
            );
<<<<<<< HEAD
            mysqli_stmt_bind_param($stmt, 'ssiiiiisisi', $inSql, $outSql, $total, $break, $effective, $early, $overtime, $status, $actor, $reason, $sessionId);
=======
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
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_commit($conn);
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            throw $e;
        }
<<<<<<< HEAD

        oecrm_legacy_attendance_sync($conn, $session['employee_id'], $session['attendance_date'], 'signinTime', $inDt->format('H:i'));
        if ($outDt) {
            oecrm_legacy_attendance_sync($conn, $session['employee_id'], $session['attendance_date'], 'signoutTime', $outDt->format('H:i'));
            oecrm_legacy_attendance_sync($conn, $session['employee_id'], $session['attendance_date'], 'workHours', sprintf('%02d:%02d', intdiv($effective, 60), $effective % 60));
        }
        oecrm_audit($conn, 'attendance_review', 'correct', 'attendance_session', $sessionId, 'Attendance corrected', $session, ['actual_in' => $inSql, 'actual_out' => $outSql, 'break_minutes' => $break, 'reason' => $reason]);
        $_SESSION['attendance_review_flash'] = 'Attendance corrected successfully.';
    } else {
        oecrm_require_permission($conn, 'attendance_review', $action);
        $review = $action === 'approve' ? 'approved' : 'rejected';
        $notes = trim($_POST['reason'] ?? '');
        $stmt = mysqli_prepare($conn, 'UPDATE attendance_sessions SET review_status=?,reviewed_by=?,reviewed_at=NOW(),review_notes=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'sisi', $review, $actor, $notes, $sessionId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        oecrm_audit($conn, 'attendance_review', $action, 'attendance_session', $sessionId, 'Attendance ' . $review, null, ['review_status' => $review, 'notes' => $notes]);
        $_SESSION['attendance_review_flash'] = 'Attendance ' . $review . '.';
    }

    header('Location: ' . $return);
    exit;
}

if (in_array($action, ['lock', 'unlock'], true)) {
    oecrm_require_permission($conn, 'attendance_review', $action);
    $year = (int) ($_POST['year'] ?? 0);
    $month = (int) ($_POST['month'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    if ($year < 2020 || $month < 1 || $month > 12 || $reason === '') {
        http_response_code(400);
        exit('Valid period and reason are required.');
    }

    if ($action === 'lock') {
        $stmt = mysqli_prepare(
            $conn,
            'INSERT INTO attendance_period_locks(company_id,period_year,period_month,locked_at,locked_by,lock_reason,is_locked) VALUES(?,?,?,NOW(),?,?,1) ON DUPLICATE KEY UPDATE locked_at=NOW(),locked_by=VALUES(locked_by),lock_reason=VALUES(lock_reason),unlocked_at=NULL,unlocked_by=0,unlock_reason=NULL,is_locked=1'
        );
        mysqli_stmt_bind_param($stmt, 'iiiis', $companyId, $year, $month, $actor, $reason);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = date('Y-m-t', strtotime($start));
        $stmt = mysqli_prepare($conn, 'UPDATE attendance_sessions s JOIN employeestbl e ON e.id=s.employee_id SET s.is_locked=1 WHERE e.company_id=? AND s.attendance_date BETWEEN ? AND ?');
        mysqli_stmt_bind_param($stmt, 'iss', $companyId, $start, $end);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['attendance_review_flash'] = 'Attendance period locked.';
    } else {
        $stmt = mysqli_prepare($conn, 'UPDATE attendance_period_locks SET is_locked=0,unlocked_at=NOW(),unlocked_by=?,unlock_reason=? WHERE company_id=? AND period_year=? AND period_month=?');
        mysqli_stmt_bind_param($stmt, 'isiii', $actor, $reason, $companyId, $year, $month);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = date('Y-m-t', strtotime($start));
        $stmt = mysqli_prepare($conn, 'UPDATE attendance_sessions s JOIN employeestbl e ON e.id=s.employee_id SET s.is_locked=0 WHERE e.company_id=? AND s.attendance_date BETWEEN ? AND ?');
        mysqli_stmt_bind_param($stmt, 'iss', $companyId, $start, $end);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['attendance_review_flash'] = 'Attendance period unlocked.';
    }
    oecrm_audit($conn, 'attendance_review', $action, 'attendance_period', $year . '-' . $month, 'Attendance period ' . $action . 'ed', null, ['year' => $year, 'month' => $month, 'reason' => $reason]);
    header('Location: ' . $return);
    exit;
}

http_response_code(400);
exit('Invalid action.');
=======
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
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
