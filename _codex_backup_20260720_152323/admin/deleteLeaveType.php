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
oecrm_require_permission($conn, 'leave_policies', 'edit');

$id = oecrm_int_param($_POST, 'deleteLeaveType');
$stmt = mysqli_prepare($conn, 'SELECT id,name FROM leavetypetbl WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$leaveType = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$leaveType) {
    http_response_code(404);
    exit('Leave type not found.');
}

$inUseQueries = [
    'SELECT COUNT(*) total FROM leave_policies WHERE leave_type_id=?',
    'SELECT COUNT(*) total FROM leave_requests WHERE leave_type_id=?',
    'SELECT COUNT(*) total FROM employee_leave_balances WHERE leave_type_id=?',
];

foreach ($inUseQueries as $sql) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if ((int)($row['total'] ?? 0) > 0) {
        $_SESSION['leave_type_flash'] = 'This leave type is already in use and cannot be deleted.';
        header('Location:manageLeaveType.php');
        exit;
    }
}

$stmt = mysqli_prepare($conn, 'DELETE FROM leavetypetbl WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['leave_type_flash'] = 'Leave type deleted successfully.';
oecrm_audit($conn, 'leave_settings', 'delete', 'leave_type', $id, 'Leave type deleted', $leaveType);
header('Location:manageLeaveType.php');
exit;
