<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}
oecrm_require_csrf();

function oecrm_delete_leave_type_table_exists($conn, $table)
{
    $table = mysqli_real_escape_string($conn, $table);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    return $result && mysqli_num_rows($result) > 0;
}

function oecrm_delete_leave_type_count($conn, $sql, $id)
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return 0;
    }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return (int) ($row['total'] ?? 0);
}

if (!oecrm_can($conn, 'leave_policies', 'delete') && !oecrm_can($conn, 'leave_policies', 'edit') && !oecrm_legacy_can($conn, 'settings') && !oecrm_legacy_can($conn, 'leave')) {
    $_SESSION['leave_type_flash'] = ['type' => 'danger', 'message' => 'You do not have permission to delete leave types.'];
    header('Location:manageLeaveType.php');
    exit;
}

$id = (int) ($_POST['deleteLeaveType'] ?? $_POST['leave_type_id'] ?? 0);
$stmt = mysqli_prepare($conn, 'SELECT id,name FROM leavetypetbl WHERE id=? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$leaveType = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$leaveType) {
    $_SESSION['leave_type_flash'] = ['type' => 'warning', 'message' => 'Leave Type not found.'];
    header('Location:manageLeaveType.php');
    exit;
}

$requestCount = oecrm_delete_leave_type_table_exists($conn, 'leave_requests')
    ? oecrm_delete_leave_type_count($conn, 'SELECT COUNT(*) total FROM leave_requests WHERE leave_type_id=?', $id)
    : 0;
$balanceCount = oecrm_delete_leave_type_table_exists($conn, 'employee_leave_balances')
    ? oecrm_delete_leave_type_count($conn, 'SELECT COUNT(*) total FROM employee_leave_balances WHERE leave_type_id=? AND (credited<>0 OR used<>0)', $id)
    : 0;

if ($requestCount > 0 || $balanceCount > 0) {
    $_SESSION['leave_type_flash'] = ['type' => 'warning', 'message' => 'This Leave Type is already used and cannot be deleted.'];
    header('Location:manageLeaveType.php');
    exit;
}

mysqli_begin_transaction($conn);
try {
    if (oecrm_delete_leave_type_table_exists($conn, 'employee_leave_balances')) {
        $stmt = mysqli_prepare($conn, 'DELETE FROM employee_leave_balances WHERE leave_type_id=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    if (oecrm_delete_leave_type_table_exists($conn, 'leave_policies')) {
        $stmt = mysqli_prepare($conn, 'DELETE FROM leave_policies WHERE leave_type_id=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    $stmt = mysqli_prepare($conn, 'DELETE FROM leavetypetbl WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $deleted = mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    if (!$deleted || $affected < 1) {
        throw new RuntimeException('Delete failed');
    }
    mysqli_commit($conn);
    $_SESSION['leave_type_flash'] = ['type' => 'success', 'message' => 'Leave Type deleted successfully.'];
} catch (Throwable $e) {
    mysqli_rollback($conn);
    $_SESSION['leave_type_flash'] = ['type' => 'danger', 'message' => 'Leave Type could not be deleted.'];
}

header('Location:manageLeaveType.php');
exit;
