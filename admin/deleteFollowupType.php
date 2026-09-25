<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
<<<<<<< HEAD
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
=======
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
oecrm_require_csrf();oecrm_require_permission($conn,'clients','edit');
$id=oecrm_int_param($_POST,'deleteFollowupType');$companyId=oecrm_current_company_id($conn);
$stmt=mysqli_prepare($conn,'SELECT * FROM followup_type_tbl WHERE id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);$row=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);if(!$row){http_response_code(404);exit('Follow-up type not found.');}
$stmt=mysqli_prepare($conn,'SELECT COUNT(*) total FROM lead_followup f JOIN leads l ON l.lead_id=f.lead_id WHERE f.followup_type=? AND l.company_id=? AND f.status<>"cancelled"');mysqli_stmt_bind_param($stmt,'si',$row['name'],$companyId);mysqli_stmt_execute($stmt);$used=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);
if((int)$used['total']>0){
    $_SESSION['followup_type_flash']='Follow-up type is in use and cannot be deleted.';
    $_SESSION['followup_type_flash_type']='warning';
}else{
    $stmt=mysqli_prepare($conn,'DELETE FROM followup_type_tbl WHERE id=?');
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    oecrm_audit($conn,'clients','delete','followup_type',$id,'Follow-up type deleted',$row);
    $_SESSION['followup_type_flash']='Follow-up type deleted.';
    $_SESSION['followup_type_flash_type']='success';
}
header('Location:manageFollowupType.php');exit;
>>>>>>> 4149906d51df3b8c49a887d1195ad99bf370ef70
