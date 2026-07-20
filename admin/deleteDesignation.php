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

if (!oecrm_can($conn, 'employees', 'delete') && !oecrm_can($conn, 'employees', 'edit') && !oecrm_legacy_can($conn, 'employee') && !oecrm_legacy_can($conn, 'settings')) {
    $_SESSION['designation_flash'] = 'You do not have permission to delete designations.';
    header('Location:manageDesignation.php');
    exit;
}

$id = oecrm_int_param($_POST, 'deleteDesignation');
$stmt = mysqli_prepare($conn, 'SELECT id,designation FROM designation WHERE id=? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$designation = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$designation) {
    $_SESSION['designation_flash'] = 'Designation not found.';
    header('Location:manageDesignation.php');
    exit;
}

$name = (string) $designation['designation'];
$usedStmt = mysqli_prepare($conn, 'SELECT COUNT(*) total FROM employeestbl WHERE designation=?');
mysqli_stmt_bind_param($usedStmt, 's', $name);
mysqli_stmt_execute($usedStmt);
$used = mysqli_fetch_assoc(mysqli_stmt_get_result($usedStmt));
mysqli_stmt_close($usedStmt);

if ((int) ($used['total'] ?? 0) > 0) {
    $_SESSION['designation_flash'] = 'Designation is assigned to employees and cannot be deleted.';
    header('Location:manageDesignation.php');
    exit;
}

$deleteStmt = mysqli_prepare($conn, 'DELETE FROM designation WHERE id=?');
mysqli_stmt_bind_param($deleteStmt, 'i', $id);
$deleted = mysqli_stmt_execute($deleteStmt);
$affected = mysqli_stmt_affected_rows($deleteStmt);
mysqli_stmt_close($deleteStmt);

$_SESSION['designation_flash'] = ($deleted && $affected > 0) ? 'Designation deleted successfully.' : 'Designation could not be deleted.';
header('Location:manageDesignation.php');
exit;
