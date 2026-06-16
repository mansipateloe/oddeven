<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
oecrm_require_employee_login();
header('Location: viewProject.php');
exit;
