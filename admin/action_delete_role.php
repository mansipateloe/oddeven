<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
oecrm_require_permission($conn, 'roles', 'delete');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}

oecrm_require_csrf();

$id = oecrm_int_param($_POST, 'id');
$companyId = oecrm_current_company_id($conn);

$stmt = mysqli_prepare(
    $conn,
    'SELECT id, name FROM user_type WHERE id=? AND is_deleted=0 AND org_id IN (0,?)'
);
mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
mysqli_stmt_execute($stmt);
$role = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$role) {
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
    header('Location: all_user_roles.php');
    exit;
}

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

header('Location: all_user_roles.php');
exit;