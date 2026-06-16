<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
oecrm_require_admin_login();
$taskId = (int)($_GET['id'] ?? $_GET['edit'] ?? 0);
header('Location: taskEditor.php'.($taskId ? '?id='.$taskId : ''));
exit;
