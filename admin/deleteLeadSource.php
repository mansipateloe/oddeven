<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}
oecrm_require_csrf();

if (!oecrm_can($conn, 'clients', 'delete') && !oecrm_can($conn, 'clients', 'edit') && !oecrm_legacy_can($conn, 'add_lead') && !oecrm_legacy_can($conn, 'view_lead')) {
    $_SESSION['lead_source_flash'] = 'You do not have permission to delete lead sources.';
    $_SESSION['lead_source_flash_type'] = 'error';
    header('Location:manageLeadSource.php');
    exit;
}

$id = oecrm_int_param($_POST, 'deleteLeadSource');
$stmt = mysqli_prepare($conn, 'SELECT id,name FROM lead_source_tbl WHERE id=? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$leadSource = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$leadSource) {
    $_SESSION['lead_source_flash'] = 'Lead source not found.';
    $_SESSION['lead_source_flash_type'] = 'error';
    header('Location:manageLeadSource.php');
    exit;
}

$usedStmt = mysqli_prepare($conn, 'SELECT COUNT(*) total FROM leads WHERE lead_source=?');
mysqli_stmt_bind_param($usedStmt, 'i', $id);
mysqli_stmt_execute($usedStmt);
$used = mysqli_fetch_assoc(mysqli_stmt_get_result($usedStmt));
mysqli_stmt_close($usedStmt);

if ((int) ($used['total'] ?? 0) > 0) {
    $_SESSION['lead_source_flash'] = 'Lead source is in use and cannot be deleted.';
    $_SESSION['lead_source_flash_type'] = 'warning';
    header('Location:manageLeadSource.php');
    exit;
}

$deleteStmt = mysqli_prepare($conn, 'DELETE FROM lead_source_tbl WHERE id=?');
mysqli_stmt_bind_param($deleteStmt, 'i', $id);
$deleted = mysqli_stmt_execute($deleteStmt);
$affected = mysqli_stmt_affected_rows($deleteStmt);
mysqli_stmt_close($deleteStmt);

if ($deleted && $affected > 0) {
    $_SESSION['lead_source_flash'] = 'Lead source deleted successfully.';
    $_SESSION['lead_source_flash_type'] = 'success';
} else {
    $_SESSION['lead_source_flash'] = 'Lead source could not be deleted.';
    $_SESSION['lead_source_flash_type'] = 'error';
}
header('Location:manageLeadSource.php');
exit;
