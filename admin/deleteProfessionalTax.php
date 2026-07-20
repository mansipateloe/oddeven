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
if (!oecrm_can($conn, 'payroll', 'delete') && !oecrm_can($conn, 'finance', 'delete') && !oecrm_can($conn, 'salary_structures', 'delete') && !oecrm_legacy_can($conn, 'settings') && !oecrm_legacy_can($conn, 'employee') && !oecrm_legacy_can($conn, 'invocie')) {
    $_SESSION['professional_tax_flash'] = ['type' => 'danger', 'message' => 'You do not have permission to delete professional tax.'];
    header('Location:manageProfessionalTax.php');
    exit;
}
$id = oecrm_int_param($_POST, 'deleteProfessionalTax');
$stmt = mysqli_prepare($conn, 'DELETE FROM professionaltaxtbl WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
$deleted = mysqli_stmt_execute($stmt);
$affected = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);
$_SESSION['professional_tax_flash'] = ($deleted && $affected > 0)
    ? ['type' => 'success', 'message' => 'Professional tax deleted successfully.']
    : ['type' => 'warning', 'message' => 'Professional tax not found or could not be deleted.'];
header('Location:manageProfessionalTax.php');
exit;
