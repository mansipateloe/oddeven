<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();oecrm_require_permission($conn,'attendance_review','view');
$query=[];if(!empty($_GET['employeeId']))$query['employee_id']=(int)$_GET['employeeId'];if(!empty($_GET['month'])&&!empty($_GET['year']))$query['month']=sprintf('%04d-%02d',(int)$_GET['year'],(int)$_GET['month']);
header('Location: attendanceReview.php'.($query?'?'.http_build_query($query):''));exit;
