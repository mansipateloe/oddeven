<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();oecrm_require_csrf();oecrm_require_permission($conn,'client_communications','create');
$id=oecrm_int_param($_GET,'id');$companyId=oecrm_current_company_id($conn);
$stmt=mysqli_prepare($conn,'SELECT f.*,l.company_id FROM lead_followup f JOIN leads l ON l.lead_id=f.lead_id WHERE f.id=? AND l.company_id=?');mysqli_stmt_bind_param($stmt,'ii',$id,$companyId);mysqli_stmt_execute($stmt);$row=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);
if(!$row){http_response_code(404);exit('Follow-up not found.');}
$stmt=mysqli_prepare($conn,'UPDATE lead_followup SET status="cancelled" WHERE id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
oecrm_audit($conn,'client_communications','cancel','lead_followup',$id,'Lead follow-up cancelled',$row,['status'=>'cancelled']);header('Location:lead_details.php?leadId='.(int)$row['lead_id']);exit;
