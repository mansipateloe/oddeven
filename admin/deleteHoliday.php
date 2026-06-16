<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();oecrm_require_csrf();oecrm_require_permission($conn,'settings','edit');
$id=oecrm_int_param($_GET,'deleteHoliday');$companyId=oecrm_current_company_id($conn);
$stmt=mysqli_prepare($conn,'SELECT * FROM holidaytbl WHERE id=? AND company_id IN (0,?) ORDER BY company_id DESC LIMIT 1');mysqli_stmt_bind_param($stmt,'ii',$id,$companyId);mysqli_stmt_execute($stmt);$row=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);
if(!$row){http_response_code(404);exit('Holiday not found.');}
$used=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM attendance_sessions s JOIN employeestbl e ON e.id=s.employee_id WHERE e.company_id=$companyId AND s.attendance_date='".mysqli_real_escape_string($conn,$row['holidayDate'])."'"));
if((int)$used['total']>0){$_SESSION['holiday_flash']='Holiday cannot be deleted because attendance exists for that date.';}else{$stmt=mysqli_prepare($conn,'DELETE FROM holidaytbl WHERE id=? AND company_id IN (0,?)');mysqli_stmt_bind_param($stmt,'ii',$id,$companyId);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);oecrm_audit($conn,'settings','delete','holiday',$id,'Holiday deleted',$row);$_SESSION['holiday_flash']='Holiday deleted.';}header('Location:manageHoliday.php');exit;
