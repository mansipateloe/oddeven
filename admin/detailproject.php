<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
oecrm_require_admin_login();
$projectId = (int)($_GET['id'] ?? $_GET['view'] ?? $_GET['edit'] ?? 0);
header('Location: '.($projectId ? 'projectBoard.php?id='.$projectId : 'projectWorkspace.php'));
exit;
