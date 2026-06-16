<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();oecrm_require_csrf();oecrm_require_permission($conn,'client_communications','create');
$id=(int)($_POST['id']??0);$leadId=(int)($_POST['lead_id']??0);$companyId=oecrm_current_company_id($conn);
$leadType=trim($_POST['lead_type']??'');$followupType=trim($_POST['followup_type']??'');$remarks=trim($_POST['remarks']??'');$nextDate=$_POST['next_followup_date']?:null;$nextTime=$_POST['next_followup_time']?:null;$status=$_POST['status']??'pending';
$stmt=mysqli_prepare($conn,'SELECT f.* FROM lead_followup f JOIN leads l ON l.lead_id=f.lead_id WHERE f.id=? AND f.lead_id=? AND l.company_id=?');mysqli_stmt_bind_param($stmt,'iii',$id,$leadId,$companyId);mysqli_stmt_execute($stmt);$old=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);if(!$old){http_response_code(404);exit('Follow-up not found.');}
$stmt=mysqli_prepare($conn,'UPDATE lead_followup SET lead_type=?,followup_type=?,remarks=?,next_followup_date=?,next_followup_time=?,status=? WHERE id=?');mysqli_stmt_bind_param($stmt,'ssssssi',$leadType,$followupType,$remarks,$nextDate,$nextTime,$status,$id);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
oecrm_audit($conn,'client_communications','update','lead_followup',$id,'Lead follow-up updated',$old,['status'=>$status]);header('Location:lead_details.php?leadId='.$leadId);exit;
