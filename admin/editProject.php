<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
oecrm_require_admin_login();
$id = (int) ($_GET['edit'] ?? $_GET['id'] ?? 0);
header('Location: projectEditor.php' . ($id ? '?id=' . $id : ''));
exit;
