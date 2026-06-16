<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();oecrm_require_permission($conn,'payroll','view');header('Location: payrollManagement.php');exit;
