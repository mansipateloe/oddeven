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
oecrm_require_permission($conn, 'finance', 'delete');

$id = oecrm_int_param($_POST, 'deleteProfessionalTax');
$row = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM professionalTaxTbl WHERE id=' . (int) $id));
if (!$row) {
    http_response_code(404);
    exit('Professional tax record not found.');
}

$stmt = mysqli_prepare($conn, 'DELETE FROM professionalTaxTbl WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['professional_tax_flash'] = 'Professional tax deleted.';
header('Location:manageProfessionalTax.php');
exit;
