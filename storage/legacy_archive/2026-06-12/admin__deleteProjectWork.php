<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();oecrm_require_csrf();oecrm_require_permission($conn,'project_tasks','edit');
$id=oecrm_int_param($_GET,'delete');$companyId=oecrm_current_company_id($conn);
$stmt=mysqli_prepare($conn,'SELECT h.* FROM taskhourstbl h JOIN tasktbl t ON t.id=h.taskId WHERE h.id=? AND t.company_id=?');mysqli_stmt_bind_param($stmt,'ii',$id,$companyId);mysqli_stmt_execute($stmt);$row=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);if(!$row){http_response_code(404);exit('Work log not found.');}
$stmt=mysqli_prepare($conn,'DELETE FROM taskhourstbl WHERE id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);oecrm_audit($conn,'project_tasks','delete','task_worklog',$id,'Task work log deleted',$row);header('Location:projectBoard.php?id='.(int)$row['projectId']);exit;
