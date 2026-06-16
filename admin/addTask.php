<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
oecrm_require_admin_login();
$projectId = (int)($_GET['project_id'] ?? $_GET['view'] ?? 0);
header('Location: taskEditor.php'.($projectId ? '?project_id='.$projectId : ''));
exit;
