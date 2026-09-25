<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();
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
