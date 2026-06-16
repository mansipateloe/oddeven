<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
require_once __DIR__.'/../foundation.php';

oecrm_require_admin_login();
oecrm_require_csrf();
oecrm_require_permission($conn, 'project_tasks', 'edit');
oecrm_audit($conn, 'project_tasks', 'blocked_legacy_delete', 'task_worklog', (int)($_GET['delete'] ?? 0), 'Legacy task work-log delete was blocked');
$_SESSION['task_error'] = 'Legacy work logs are read-only. Manage work through the Timesheet module.';
header('Location: viewTask.php');
exit;
