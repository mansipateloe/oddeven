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
if (!oecrm_can($conn, 'finance', 'delete') && !oecrm_can($conn, 'settings', 'delete') && !oecrm_legacy_can($conn, 'settings') && !oecrm_legacy_can($conn, 'invocie') && !oecrm_legacy_can($conn, 'expense')) {
    $_SESSION['currency_flash'] = ['type' => 'danger', 'message' => 'You do not have permission to delete currencies.'];
    header('Location:manageCurrency.php');
    exit;
}
$id = oecrm_int_param($_POST, 'deleteCurrency');
$stmt = mysqli_prepare($conn, 'DELETE FROM currency_master WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
$deleted = mysqli_stmt_execute($stmt);
$affected = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);
$_SESSION['currency_flash'] = ($deleted && $affected > 0)
    ? ['type' => 'success', 'message' => 'Currency deleted successfully.']
    : ['type' => 'warning', 'message' => 'Currency not found or could not be deleted.'];
header('Location:manageCurrency.php');
exit;
