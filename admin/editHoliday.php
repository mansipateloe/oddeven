<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
oecrm_require_admin_login();
$id=(int)($_GET['editHoliday']??0);
header('Location: manageHoliday.php' . ($id?'?edit='.$id:''));
exit;
