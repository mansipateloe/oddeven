<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
oecrm_require_admin_login();
header('Location: projectWorkspace.php');
exit;
