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

if (!oecrm_can($conn, 'clients', 'delete') && !oecrm_can($conn, 'clients', 'edit') && !oecrm_legacy_can($conn, 'add_lead') && !oecrm_legacy_can($conn, 'view_lead')) {
    $_SESSION['followup_type_flash'] = 'You do not have permission to delete follow-up types.';
    header('Location:manageFollowupType.php');
    exit;
}

$id = oecrm_int_param($_POST, 'deleteFollowupType');
$stmt = mysqli_prepare($conn, 'SELECT id,name FROM followup_type_tbl WHERE id=? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$type = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$type) {
    $_SESSION['followup_type_flash'] = 'Follow-up type not found.';
    header('Location:manageFollowupType.php');
    exit;
}

$name = (string) $type['name'];
$usedStmt = mysqli_prepare($conn, 'SELECT COUNT(*) total FROM lead_followup WHERE followup_type=?');
mysqli_stmt_bind_param($usedStmt, 's', $name);
mysqli_stmt_execute($usedStmt);
$used = mysqli_fetch_assoc(mysqli_stmt_get_result($usedStmt));
mysqli_stmt_close($usedStmt);

if ((int) ($used['total'] ?? 0) > 0) {
    $_SESSION['followup_type_flash'] = 'Follow-up type is in use and cannot be deleted.';
    header('Location:manageFollowupType.php');
    exit;
}

$deleteStmt = mysqli_prepare($conn, 'DELETE FROM followup_type_tbl WHERE id=?');
mysqli_stmt_bind_param($deleteStmt, 'i', $id);
$deleted = mysqli_stmt_execute($deleteStmt);
$affected = mysqli_stmt_affected_rows($deleteStmt);
mysqli_stmt_close($deleteStmt);

$_SESSION['followup_type_flash'] = ($deleted && $affected > 0) ? 'Follow-up type deleted successfully.' : 'Follow-up type could not be deleted.';
header('Location:manageFollowupType.php');
exit;
