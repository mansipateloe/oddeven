<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed.');}
oecrm_require_csrf();
$mode=$_POST['mode']??'';
oecrm_require_permission($conn,'employees',$mode==='create'?'create':'edit');

function employee_post($key){return trim((string)($_POST[$key]??''));}
$employeeId=$mode==='update'?oecrm_int_param($_POST,'employee_id'):0;
$companyId=(int)($_POST['company_id']??oecrm_current_company_id($conn));
$departmentId=max(0,(int)($_POST['department_id']??0));
$employeeCode=employee_post('employeeCode');$designation=employee_post('designation');$name=employee_post('name');$birthdate=employee_post('birthdate');$username=employee_post('employeeUname');$companyEmail=employee_post('companyEmail');$personalEmail=employee_post('personalEmail');$mobile1=employee_post('mobile1');$mobile2=employee_post('mobile2');$skype=employee_post('skypeUname');$joiningDate=employee_post('joiningDate');$salary=employee_post('salary');$bankName=employee_post('bankName');$ifsc=employee_post('bankIFSCno');$holder=employee_post('bankAcHolderName');$accountNo=employee_post('bankAcNo');$address=employee_post('address');
$employmentType=employee_post('employment_type')?:'permanent';$employmentStatus=employee_post('employment_status')?:'active';$confirmationDate=employee_post('confirmation_date')?:null;$noticeDays=max(0,(int)($_POST['notice_period_days']??0));$exitDate=employee_post('exit_date')?:null;$exitReason=employee_post('exit_reason');
$allowedTypes=['permanent','probation','contract','intern','consultant'];$allowedStatuses=['active','inactive','notice_period','resigned','terminated','retired'];
if(!$companyId||$employeeCode===''||$designation===''||$name===''||$username===''||$companyEmail===''||!in_array($employmentType,$allowedTypes,true)||!in_array($employmentStatus,$allowedStatuses,true)){http_response_code(400);exit('Please complete all required employee fields.');}
if($joiningDate===''){http_response_code(400);exit('Joining date is required.');}
if($departmentId){$stmt=mysqli_prepare($conn,'SELECT id FROM departments WHERE id=? AND company_id=? AND status=1');mysqli_stmt_bind_param($stmt,'ii',$departmentId,$companyId);mysqli_stmt_execute($stmt);if(!mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))){mysqli_stmt_close($stmt);http_response_code(400);exit('Selected department does not belong to the selected company.');}mysqli_stmt_close($stmt);}
$status=in_array($employmentStatus,['active','notice_period'],true)?0:1;
mysqli_begin_transaction($conn);
try{
 $dupChecks = [
   ['employeeCode','employee code','SELECT id FROM employeestbl WHERE company_id=? AND employeeCode=? AND id<>? LIMIT 1','ss'],
   ['employeeUname','username','SELECT id FROM employeestbl WHERE company_id=? AND employeeUname=? AND id<>? LIMIT 1','ss'],
   ['companyEmail','company email','SELECT id FROM employeestbl WHERE company_id=? AND companyEmail=? AND id<>? LIMIT 1','ss'],
   ['mobile1','mobile number','SELECT id FROM employeestbl WHERE company_id=? AND mobile1=? AND id<>? LIMIT 1','ss'],
 ];
 foreach($dupChecks as [$field,$label,$sql]){
   $value = $$field;
   if($value==='') continue;
   $stmt=mysqli_prepare($conn,$sql);
   mysqli_stmt_bind_param($stmt,'ssi',$companyId,$value,$employeeId);
   mysqli_stmt_execute($stmt);
   $exists=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
   mysqli_stmt_close($stmt);
   if($exists){ throw new RuntimeException(ucfirst($label).' already exists for this company.'); }
 }
 if($mode==='create'){
  $password=employee_post('employeeUpass');if(strlen($password)<6){throw new RuntimeException('Password must contain at least 6 characters.');}$password=oecrm_password_hash($password);
  $stmt=mysqli_prepare($conn,'INSERT INTO employeestbl(company_id,department_id,employeeCode,designation,name,birthdate,employeeUname,employeeUpass,companyEmail,personalEmail,mobile1,mobile2,skypeUname,joiningDate,salary,bankName,bankIFSCno,bankAcHolderName,bankAcNo,address,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
  mysqli_stmt_bind_param($stmt,'iissssssssssssssssssi',$companyId,$departmentId,$employeeCode,$designation,$name,$birthdate,$username,$password,$companyEmail,$personalEmail,$mobile1,$mobile2,$skype,$joiningDate,$salary,$bankName,$ifsc,$holder,$accountNo,$address,$status);mysqli_stmt_execute($stmt);$employeeId=mysqli_insert_id($conn);mysqli_stmt_close($stmt);$oldStatus=null;
 }elseif($mode==='update'){
  $stmt=mysqli_prepare($conn,'SELECT e.*,p.employment_status FROM employeestbl e LEFT JOIN employee_profiles p ON p.employee_id=e.id WHERE e.id=?');mysqli_stmt_bind_param($stmt,'i',$employeeId);mysqli_stmt_execute($stmt);$old=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);if(!$old){throw new RuntimeException('Employee not found.');}$oldStatus=$old['employment_status'];$password=employee_post('employeeUpass');$passwordHash=$password===''?$old['employeeUpass']:oecrm_password_hash($password);
  $stmt=mysqli_prepare($conn,'UPDATE employeestbl SET company_id=?,department_id=?,employeeCode=?,designation=?,name=?,birthdate=?,employeeUname=?,employeeUpass=?,companyEmail=?,personalEmail=?,mobile1=?,mobile2=?,skypeUname=?,joiningDate=?,salary=?,bankName=?,bankIFSCno=?,bankAcHolderName=?,bankAcNo=?,address=?,status=? WHERE id=?');
  mysqli_stmt_bind_param($stmt,'iissssssssssssssssssii',$companyId,$departmentId,$employeeCode,$designation,$name,$birthdate,$username,$passwordHash,$companyEmail,$personalEmail,$mobile1,$mobile2,$skype,$joiningDate,$salary,$bankName,$ifsc,$holder,$accountNo,$address,$status,$employeeId);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
 }else{throw new RuntimeException('Invalid employee save mode.');}
 $stmt=mysqli_prepare($conn,'INSERT INTO employee_profiles(employee_id,employment_type,employment_status,confirmation_date,notice_period_days,exit_date,exit_reason,current_address,permanent_address) VALUES(?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE employment_type=VALUES(employment_type),employment_status=VALUES(employment_status),confirmation_date=VALUES(confirmation_date),notice_period_days=VALUES(notice_period_days),exit_date=VALUES(exit_date),exit_reason=VALUES(exit_reason),current_address=IF(current_address IS NULL OR current_address="",VALUES(current_address),current_address),permanent_address=IF(permanent_address IS NULL OR permanent_address="",VALUES(permanent_address),permanent_address)');
 mysqli_stmt_bind_param($stmt,'isssissss',$employeeId,$employmentType,$employmentStatus,$confirmationDate,$noticeDays,$exitDate,$exitReason,$address,$address);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
 if($oldStatus!==$employmentStatus){$today=date('Y-m-d');$actor=(int)$_SESSION['adminId'];$stmt=mysqli_prepare($conn,'INSERT INTO employee_status_history(employee_id,old_status,new_status,effective_date,reason,changed_by) VALUES(?,?,?,?,?,?)');mysqli_stmt_bind_param($stmt,'issssi',$employeeId,$oldStatus,$employmentStatus,$today,$exitReason,$actor);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);}
 mysqli_commit($conn);oecrm_audit($conn,'employees',$mode==='create'?'create':'update','employee',$employeeId,$mode==='create'?'Employee created':'Employee updated',null,['company_id'=>$companyId,'department_id'=>$departmentId,'employment_status'=>$employmentStatus]);$_SESSION['employee_flash']=$mode==='create'?'Employee created successfully.':'Employee updated successfully.';header('Location: employeeProfile.php?id='.$employeeId);exit;
}catch(Throwable $e){mysqli_rollback($conn);http_response_code(400);exit($e->getMessage());}
