<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

oecrm_require_csrf();
oecrm_require_permission($conn, 'employees', 'edit');

$id = oecrm_int_param($_POST, 'deleteDesignation');

$stmt = mysqli_prepare($conn, 'SELECT * FROM designation WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) {
    http_response_code(404);
    exit('Designation not found.');
}

$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) total FROM employeestbl WHERE designation=?');
mysqli_stmt_bind_param($stmt, 's', $row['designation']);
mysqli_stmt_execute($stmt);
$used = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ((int) $used['total'] > 0) {
    $_SESSION['designation_flash'] = 'Designation is assigned to employees and cannot be deleted.';
    $_SESSION['designation_warning'] = true;
} else {
    $stmt = mysqli_prepare($conn, 'DELETE FROM designation WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    oecrm_audit($conn, 'employees', 'delete', 'designation', $id, 'Designation deleted', $row);
    $_SESSION['designation_flash'] = 'Designation deleted.';
    $_SESSION['designation_warning'] = false;
}

header('Location: manageDesignation.php');
exit;
