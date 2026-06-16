<?php
require_once __DIR__.'/../security.php';
require_once __DIR__.'/../foundation.php';
require_once __DIR__.'/../leave.php';

$isAdmin = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;
require_once __DIR__.($isAdmin ? '/../admin/dbconnect.php' : '/dbconnect.php');

if ($isAdmin) {
    oecrm_require_admin_login();
} else {
    oecrm_require_employee_login();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
oecrm_require_csrf();
$action = $_POST['action'] ?? '';

if ($action === 'apply') {
    $companyId = $isAdmin ? oecrm_current_company_id($conn) : 0;
    $employeeId = $isAdmin ? (int)($_POST['employee_id'] ?? 0) : (int)$_SESSION['employeeId'];
    $leaveTypeId = (int)($_POST['leave_type_id'] ?? 0);
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start = $_POST['start_date'] ?? '';
    $end = $_POST['end_date'] ?? '';
    $half = !empty($_POST['half_day']);

    if (!$employeeId || !$leaveTypeId || !$subject || !$start || !$end) {
        http_response_code(400);
        exit('Required leave fields are missing.');
    }

    $employeeSql = 'SELECT id,company_id FROM employeestbl WHERE id=? AND status=0'.($isAdmin ? ' AND company_id=?' : '');
    $employeeStmt = mysqli_prepare($conn, $employeeSql);
    if ($isAdmin) {
        mysqli_stmt_bind_param($employeeStmt, 'ii', $employeeId, $companyId);
    } else {
        mysqli_stmt_bind_param($employeeStmt, 'i', $employeeId);
    }
    mysqli_stmt_execute($employeeStmt);
    $employee = mysqli_fetch_assoc(mysqli_stmt_get_result($employeeStmt));
    mysqli_stmt_close($employeeStmt);
    if (!$employee) {
        http_response_code(404);
        exit('Active employee not found for this company.');
    }

    $calc = oecrm_leave_calculate($conn, $employeeId, $leaveTypeId, $start, $end, $half);
    $year = (int)date('Y', strtotime($start));
    $balance = oecrm_leave_balance($conn, $employeeId, $leaveTypeId, $year);
    $available = (float)($balance['available'] ?? 0);
    $unpaid = !empty($calc['policy']['is_paid']) ? max(0, $calc['payable_days'] - $available) : $calc['payable_days'];

    $overlapStmt = mysqli_prepare($conn, "SELECT id FROM leave_requests WHERE employee_id=? AND status IN ('pending','approved') AND start_date<=? AND end_date>=? LIMIT 1");
    mysqli_stmt_bind_param($overlapStmt, 'iss', $employeeId, $end, $start);
    mysqli_stmt_execute($overlapStmt);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($overlapStmt))) {
        mysqli_stmt_close($overlapStmt);
        http_response_code(400);
        exit('Leave dates overlap an existing request.');
    }
    mysqli_stmt_close($overlapStmt);

    mysqli_begin_transaction($conn);
    try {
        $typeName = '';
        $typeStmt = mysqli_prepare($conn, 'SELECT name FROM leavetypetbl WHERE id=?');
        mysqli_stmt_bind_param($typeStmt, 'i', $leaveTypeId);
        mysqli_stmt_execute($typeStmt);
        mysqli_stmt_bind_result($typeStmt, $typeName);
        mysqli_stmt_fetch($typeStmt);
        mysqli_stmt_close($typeStmt);
        if ($typeName === '') {
            throw new RuntimeException('Leave type not found.');
        }

        $legacyStmt = mysqli_prepare($conn, "INSERT INTO leave_master(emp_id,subject,description,type,start_date,end_date,is_approved,remarks,created_at) VALUES(?,?,?,?,?,?,0,'',CURRENT_TIMESTAMP)");
        mysqli_stmt_bind_param($legacyStmt, 'isssss', $employeeId, $subject, $description, $typeName, $start, $end);
        mysqli_stmt_execute($legacyStmt);
        $legacyId = mysqli_insert_id($conn);
        mysqli_stmt_close($legacyStmt);

        $requestStmt = mysqli_prepare($conn, 'INSERT INTO leave_requests(legacy_leave_id,employee_id,company_id,leave_type_id,subject,description,start_date,end_date,requested_days,sandwich_days,payable_days,unpaid_days) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($requestStmt, 'iiiissssdddd', $legacyId, $employeeId, $employee['company_id'], $leaveTypeId, $subject, $description, $start, $end, $calc['requested_days'], $calc['sandwich_days'], $calc['payable_days'], $unpaid);
        mysqli_stmt_execute($requestStmt);
        $requestId = mysqli_insert_id($conn);
        mysqli_stmt_close($requestStmt);

        $balanceStmt = mysqli_prepare($conn, 'UPDATE employee_leave_balances SET pending=pending+? WHERE employee_id=? AND leave_type_id=? AND balance_year=?');
        mysqli_stmt_bind_param($balanceStmt, 'diii', $calc['payable_days'], $employeeId, $leaveTypeId, $year);
        mysqli_stmt_execute($balanceStmt);
        mysqli_stmt_close($balanceStmt);
        mysqli_commit($conn);
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }

    oecrm_audit($conn, 'leave_requests', 'apply', 'leave_request', $requestId, 'Leave applied', null, $calc);
    $_SESSION['leave_flash'] = 'Leave request submitted.';
    header('Location: '.($isAdmin ? 'manageLeave.php' : 'leave_index.php'));
    exit;
}

if (in_array($action, ['approve', 'reject'], true)) {
    oecrm_require_permission($conn, 'leave_requests', $action);
    $requestId = (int)($_POST['request_id'] ?? 0);
    $companyId = oecrm_current_company_id($conn);
    $note = trim($_POST['note'] ?? '');

    mysqli_begin_transaction($conn);
    try {
        $requestStmt = mysqli_prepare($conn, 'SELECT * FROM leave_requests WHERE id=? AND company_id=? AND status="pending" FOR UPDATE');
        mysqli_stmt_bind_param($requestStmt, 'ii', $requestId, $companyId);
        mysqli_stmt_execute($requestStmt);
        $request = mysqli_fetch_assoc(mysqli_stmt_get_result($requestStmt));
        mysqli_stmt_close($requestStmt);
        if (!$request) {
            throw new RuntimeException('Pending leave not found for the active company.');
        }

        $year = (int)date('Y', strtotime($request['start_date']));
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $legacyStatus = $action === 'approve' ? 1 : 2;
        $actor = (int)$_SESSION['adminId'];

        $statusStmt = mysqli_prepare($conn, 'UPDATE leave_requests SET status=?,approver_note=?,approved_by=?,approved_at=NOW() WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($statusStmt, 'ssiii', $status, $note, $actor, $requestId, $companyId);
        mysqli_stmt_execute($statusStmt);
        mysqli_stmt_close($statusStmt);

        $legacyStmt = mysqli_prepare($conn, 'UPDATE leave_master SET is_approved=?,remarks=? WHERE id=?');
        mysqli_stmt_bind_param($legacyStmt, 'isi', $legacyStatus, $note, $request['legacy_leave_id']);
        mysqli_stmt_execute($legacyStmt);
        mysqli_stmt_close($legacyStmt);

        if ($action === 'approve') {
            $balanceStmt = mysqli_prepare($conn, 'UPDATE employee_leave_balances SET pending=GREATEST(0,pending-?),used=used+? WHERE employee_id=? AND leave_type_id=? AND balance_year=?');
            mysqli_stmt_bind_param($balanceStmt, 'ddiii', $request['payable_days'], $request['payable_days'], $request['employee_id'], $request['leave_type_id'], $year);
        } else {
            $balanceStmt = mysqli_prepare($conn, 'UPDATE employee_leave_balances SET pending=GREATEST(0,pending-?) WHERE employee_id=? AND leave_type_id=? AND balance_year=?');
            mysqli_stmt_bind_param($balanceStmt, 'diii', $request['payable_days'], $request['employee_id'], $request['leave_type_id'], $year);
        }
        mysqli_stmt_execute($balanceStmt);
        mysqli_stmt_close($balanceStmt);
        mysqli_commit($conn);
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        http_response_code(404);
        exit($e->getMessage());
    }

    oecrm_audit($conn, 'leave_requests', $action, 'leave_request', $requestId, 'Leave '.$status, null, ['note' => $note]);
    $_SESSION['leave_flash'] = 'Leave '.$status.'.';
    header('Location: manageLeave.php');
    exit;
}

http_response_code(400);
exit('Invalid action.');
