<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
<<<<<<< HEAD
if (!(oecrm_is_super_admin() || oecrm_can($conn, 'roles', 'delete') || oecrm_legacy_can($conn, 'employee') || oecrm_legacy_can($conn, 'settings'))) {
    http_response_code(403);
    exit('You do not have permission to manage user roles.');
}
=======
oecrm_require_permission($conn, 'roles', 'delete');
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}

oecrm_require_csrf();

$id = oecrm_int_param($_POST, 'id');
$companyId = oecrm_current_company_id($conn);

<<<<<<< HEAD
$stmt = mysqli_prepare($conn, 'SELECT id,name FROM user_type WHERE id=? AND is_deleted=0 AND org_id IN (0,?) LIMIT 1');
=======
$stmt = mysqli_prepare(
    $conn,
    'SELECT id, name FROM user_type WHERE id=? AND is_deleted=0 AND org_id IN (0,?)'
);
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
mysqli_stmt_execute($stmt);
$role = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$role) {
<<<<<<< HEAD
    $_SESSION['role_flash'] = ['type' => 'warning', 'message' => 'Role not found or already deleted.'];
=======
    http_response_code(404);
    exit('Role not found.');
}

$stmt = mysqli_prepare(
    $conn,
    'SELECT COUNT(*) total FROM employeestbl WHERE access_role=? AND is_admin_access=1 AND status=0'
);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$usage = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ((int) ($usage['total'] ?? 0) > 0) {
    $_SESSION['role_error'] = 'This role is assigned to active employees and cannot be deleted.';
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
    header('Location: all_user_roles.php');
    exit;
}

<<<<<<< HEAD
$usageStmt = mysqli_prepare($conn, 'SELECT COUNT(*) total FROM employeestbl WHERE access_role=? AND is_admin_access=1 AND status=0');
mysqli_stmt_bind_param($usageStmt, 'i', $id);
mysqli_stmt_execute($usageStmt);
$usage = mysqli_fetch_assoc(mysqli_stmt_get_result($usageStmt));
mysqli_stmt_close($usageStmt);

if ((int) ($usage['total'] ?? 0) > 0) {
    $_SESSION['role_flash'] = ['type' => 'warning', 'message' => 'This role is assigned to active employees and cannot be deleted.'];
    header('Location: all_user_roles.php');
    exit;
}

$actor = (int) ($_SESSION['adminId'] ?? 0);
$updatedAt = date('Y-m-d H:i:s');
mysqli_begin_transaction($conn);
$deletePerms = mysqli_prepare($conn, 'DELETE FROM role_permissions WHERE role_id=? AND company_id=?');
mysqli_stmt_bind_param($deletePerms, 'ii', $id, $companyId);
mysqli_stmt_execute($deletePerms);
mysqli_stmt_close($deletePerms);

$deleteRole = mysqli_prepare($conn, 'UPDATE user_type SET is_deleted=1,updated_by=?,updated_at=? WHERE id=? AND is_deleted=0 AND org_id IN (0,?)');
mysqli_stmt_bind_param($deleteRole, 'isii', $actor, $updatedAt, $id, $companyId);
$deleted = mysqli_stmt_execute($deleteRole);
$affected = mysqli_stmt_affected_rows($deleteRole);
mysqli_stmt_close($deleteRole);

if (!$deleted || $affected < 1) {
    mysqli_rollback($conn);
    $_SESSION['role_flash'] = ['type' => 'danger', 'message' => 'Role could not be deleted.'];
    header('Location: all_user_roles.php');
    exit;
}

mysqli_commit($conn);
oecrm_audit($conn, 'roles', 'delete', 'role', $id, 'Role deactivated', $role, ['is_deleted' => 1]);
$_SESSION['role_flash'] = ['type' => 'success', 'message' => 'Role deleted successfully.'];
=======
$stmt = mysqli_prepare(
    $conn,
    'UPDATE user_type SET is_deleted=1, updated_by=?, updated_at=? WHERE id=?'
);
$actor = (int) $_SESSION['adminId'];
$updatedAt = date('Y-m-d H:i:s');
mysqli_stmt_bind_param($stmt, 'isi', $actor, $updatedAt, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

oecrm_audit($conn, 'roles', 'delete', 'role', $id, 'Role deactivated', $role, ['is_deleted' => 1]);

>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
header('Location: all_user_roles.php');
exit;