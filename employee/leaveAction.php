<?php
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../leave.php";
$isAdmin = strpos($_SERVER["SCRIPT_NAME"], "/admin/") !== false;
require_once __DIR__ .
    ($isAdmin ? "/../admin/dbconnect.php" : "/dbconnect.php");
if ($isAdmin) {
    oecrm_require_admin_login();
} else {
    oecrm_require_employee_login();
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit();
}
oecrm_require_csrf();
$action = $_POST["action"] ?? "";
if ($action === "apply") {
    $employeeId = $isAdmin
        ? (int) ($_POST["employee_id"] ?? 0)
        : (int) $_SESSION["employeeId"];
    $leaveTypeId = (int) ($_POST["leave_type_id"] ?? 0);
    $subject = trim($_POST["subject"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $start = $_POST["start_date"] ?? "";
    $end = $_POST["end_date"] ?? "";
    $half = !empty($_POST["half_day"]);
    if (!$employeeId || !$leaveTypeId || !$subject || !$start || !$end) {
        http_response_code(400);
        exit("Required leave fields are missing.");
    }
    $emp = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT company_id FROM employeestbl WHERE id=" . (int) $employeeId
        )
    );
    try {
        $calc = oecrm_leave_calculate(
            $conn,
            $employeeId,
            $leaveTypeId,
            $start,
            $end,
            $half
        );
        $period = oecrm_employee_leave_period($conn, $employeeId, $start);
        $endPeriod = oecrm_employee_leave_period($conn, $employeeId, $end);
    } catch (DomainException $e) {
        http_response_code(400);
        exit($e->getMessage());
    }
    if ($half && empty($calc["policy"]["allow_half_day"])) {
        http_response_code(400);
        exit("Half-day leave is not allowed for this leave type.");
    }
    if ($half && $start !== $end) {
        http_response_code(400);
        exit("Half-day leave must use the same start and end date.");
    }
    if (
        $period["start_date"] !== $endPeriod["start_date"] ||
        $period["end_date"] !== $endPeriod["end_date"] ||
        $end > $period["end_date"]
    ) {
        http_response_code(400);
        exit("A leave request cannot cross a contract leave year or contract end date. Submit separate requests for each contract year.");
    }
    if (
        empty($calc["policy"]["is_paid"]) &&
        date("Y", strtotime($start)) !== date("Y", strtotime($end))
    ) {
        http_response_code(400);
        exit("Unpaid leave requests cannot cross calendar years.");
    }
    $year = !empty($calc["policy"]["is_paid"])
        ? (int) date("Y", strtotime($period["start_date"]))
        : (int) date("Y", strtotime($start));
    mysqli_begin_transaction($conn);
    $employeeLock = mysqli_prepare(
        $conn,
        "SELECT id FROM employeestbl WHERE id=? FOR UPDATE"
    );
    mysqli_stmt_bind_param($employeeLock, "i", $employeeId);
    mysqli_stmt_execute($employeeLock);
    $lockedEmployee = mysqli_fetch_assoc(mysqli_stmt_get_result($employeeLock));
    mysqli_stmt_close($employeeLock);
    if (!$lockedEmployee) {
        mysqli_rollback($conn);
        http_response_code(404);
        exit("Employee not found.");
    }
    try {
        $paidBalance = !empty($calc["policy"]["is_paid"])
            ? oecrm_paid_leave_balance($conn, $employeeId, $start)
            : null;
    } catch (DomainException $e) {
        mysqli_rollback($conn);
        http_response_code(400);
        exit($e->getMessage());
    }
    $paidDays = $paidBalance
        ? min((float) $calc["payable_days"], (float) $paidBalance["available"])
        : 0;
    $balanceDays = !empty($calc["policy"]["is_paid"])
        ? $paidDays
        : (float) $calc["payable_days"];
    $unpaid = !empty($calc["policy"]["is_paid"])
        ? max(0, $calc["payable_days"] - $paidDays)
        : $calc["payable_days"];
    $overlapStmt = mysqli_prepare(
        $conn,
        "SELECT id FROM leave_requests WHERE employee_id=? AND status IN ('pending','approved') AND start_date<=? AND end_date>=? LIMIT 1"
    );
    mysqli_stmt_bind_param($overlapStmt, "iss", $employeeId, $end, $start);
    mysqli_stmt_execute($overlapStmt);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($overlapStmt))) {
        mysqli_stmt_close($overlapStmt);
        mysqli_rollback($conn);
        http_response_code(400);
        exit("Leave dates overlap an existing request.");
    }
    mysqli_stmt_close($overlapStmt);
    $typeName = "";
    $ts = mysqli_prepare($conn, "SELECT name FROM leavetypetbl WHERE id=?");
    mysqli_stmt_bind_param($ts, "i", $leaveTypeId);
    mysqli_stmt_execute($ts);
    mysqli_stmt_bind_result($ts, $typeName);
    mysqli_stmt_fetch($ts);
    mysqli_stmt_close($ts);
    $legacy = mysqli_prepare(
        $conn,
        "INSERT INTO leave_master(emp_id,subject,description,type,start_date,end_date,is_approved,remarks,created_at) VALUES(?,?,?,?,?, ?,0,'',CURRENT_TIMESTAMP)"
    );
    mysqli_stmt_bind_param(
        $legacy,
        "isssss",
        $employeeId,
        $subject,
        $description,
        $typeName,
        $start,
        $end
    );
    mysqli_stmt_execute($legacy);
    $legacyId = mysqli_insert_id($conn);
    mysqli_stmt_close($legacy);
    $credited = !empty($calc["policy"]["is_paid"])
        ? $paidBalance["entitlement"]
        : (float) $calc["policy"]["annual_entitlement"];
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO leave_requests(legacy_leave_id,employee_id,company_id,leave_type_id,subject,description,start_date,end_date,requested_days,sandwich_days,payable_days,unpaid_days) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)"
    );
    mysqli_stmt_bind_param(
        $stmt,
        "iiiissssdddd",
        $legacyId,
        $employeeId,
        $emp["company_id"],
        $leaveTypeId,
        $subject,
        $description,
        $start,
        $end,
        $calc["requested_days"],
        $calc["sandwich_days"],
        $calc["payable_days"],
        $unpaid
    );
    mysqli_stmt_execute($stmt);
    $requestId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO employee_leave_balances(employee_id,leave_type_id,balance_year,credited,pending) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE credited=VALUES(credited),pending=pending+VALUES(pending)"
    );
    mysqli_stmt_bind_param(
        $stmt,
        "iiidd",
        $employeeId,
        $leaveTypeId,
        $year,
        $credited,
        $balanceDays
    );
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_commit($conn);
    oecrm_audit(
        $conn,
        "leave_requests",
        "apply",
        "leave_request",
        $requestId,
        "Leave applied",
        null,
        $calc
    );
    $_SESSION["leave_flash"] = "Leave request submitted.";
    header("Location: " . ($isAdmin ? "manageLeave.php" : "leave_index.php"));
    exit();
}
if (in_array($action, ["approve", "reject"], true)) {
    $requestId = (int) ($_POST["request_id"] ?? 0);
    oecrm_require_permission($conn, "leave_requests", $action);
    $note = trim($_POST["note"] ?? "");
    $stmt = mysqli_prepare(
        $conn,
        'SELECT r.*,p.is_paid FROM leave_requests r JOIN leave_policies p ON p.company_id=r.company_id AND p.leave_type_id=r.leave_type_id WHERE r.id=? AND r.status="pending" FOR UPDATE'
    );
    mysqli_stmt_bind_param($stmt, "i", $requestId);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$r) {
        http_response_code(404);
        exit("Pending leave not found.");
    }
    $requestPeriod = !empty($r["is_paid"])
        ? oecrm_employee_leave_period($conn, (int) $r["employee_id"], $r["start_date"])
        : null;
    $year = $requestPeriod
        ? (int) date("Y", strtotime($requestPeriod["start_date"]))
        : (int) date("Y", strtotime($r["start_date"]));
    $balanceDays = !empty($r["is_paid"])
        ? max(0, (float) $r["payable_days"] - (float) $r["unpaid_days"])
        : (float) $r["payable_days"];
    mysqli_begin_transaction($conn);
    $status = $action === "approve" ? "approved" : "rejected";
    $legacyStatus = $action === "approve" ? 1 : 2;
    $actor = (int) $_SESSION["adminId"];
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE leave_requests SET status=?,approver_note=?,approved_by=?,approved_at=NOW() WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "ssii", $status, $note, $actor, $requestId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE leave_master SET is_approved=?,remarks=? WHERE id=?"
    );
    mysqli_stmt_bind_param(
        $stmt,
        "isi",
        $legacyStatus,
        $note,
        $r["legacy_leave_id"]
    );
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if ($action === "approve") {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE employee_leave_balances SET pending=GREATEST(0,pending-?),used=used+? WHERE employee_id=? AND leave_type_id=? AND balance_year=?"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "ddiii",
            $balanceDays,
            $balanceDays,
            $r["employee_id"],
            $r["leave_type_id"],
            $year
        );
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE employee_leave_balances SET pending=GREATEST(0,pending-?) WHERE employee_id=? AND leave_type_id=? AND balance_year=?"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "diii",
            $balanceDays,
            $r["employee_id"],
            $r["leave_type_id"],
            $year
        );
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_commit($conn);
    oecrm_audit(
        $conn,
        "leave_requests",
        $action,
        "leave_request",
        $requestId,
        "Leave " . $status,
        null,
        ["note" => $note]
    );
    $_SESSION["leave_flash"] = "Leave " . $status . ".";
    header("Location: manageLeave.php");
    exit();
}
http_response_code(400);
exit("Invalid action.");
