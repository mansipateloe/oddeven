<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();oecrm_require_csrf();oecrm_require_permission($conn,'clients','delete');
$id=oecrm_int_param($_GET,'delete');$companyId=oecrm_current_company_id($conn);
$stmt=mysqli_prepare($conn,'SELECT * FROM leads WHERE lead_id=? AND company_id=? AND is_active=1');mysqli_stmt_bind_param($stmt,'ii',$id,$companyId);mysqli_stmt_execute($stmt);$lead=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);
if(!$lead){http_response_code(404);exit('Lead not found.');}
$stmt=mysqli_prepare($conn,'UPDATE leads SET is_active=0,status="closed" WHERE lead_id=? AND company_id=?');mysqli_stmt_bind_param($stmt,'ii',$id,$companyId);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
oecrm_audit($conn,'clients','archive','lead',$id,'Lead archived',$lead,['is_active'=>0,'status'=>'closed']);$_SESSION['lead_flash']='Lead archived successfully.';header('Location:leads.php');exit;
