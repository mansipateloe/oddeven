<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
oecrm_require_csrf();oecrm_require_permission($conn,'clients','edit');
$id=oecrm_int_param($_POST,'deleteLeadSource');$companyId=oecrm_current_company_id($conn);
$stmt=mysqli_prepare($conn,'SELECT * FROM lead_source_tbl WHERE id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);$row=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);if(!$row){http_response_code(404);exit('Lead source not found.');}
$stmt=mysqli_prepare($conn,'SELECT COUNT(*) total FROM leads WHERE lead_source=? AND company_id=? AND is_active=1');mysqli_stmt_bind_param($stmt,'ii',$id,$companyId);mysqli_stmt_execute($stmt);$used=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);
if((int)$used['total']>0){$_SESSION['lead_source_flash']='Lead source is in use and cannot be deleted.';}else{$stmt=mysqli_prepare($conn,'DELETE FROM lead_source_tbl WHERE id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);oecrm_audit($conn,'clients','delete','lead_source',$id,'Lead source deleted',$row);$_SESSION['lead_source_flash']='Lead source deleted.';}header('Location:manageLeadSource.php');exit;
