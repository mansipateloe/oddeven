<?php
include 'dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();
oecrm_require_csrf();
oecrm_require_permission($conn, 'projects', 'edit');

$id = oecrm_int_param($_GET, 'delete');
$projectId = oecrm_int_param($_GET, 'project_id');
$companyId = oecrm_current_company_id($conn);
$task = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM tasktbl WHERE id=' . (int) $id . ' AND company_id=' . (int) $companyId));
if (!$task) {
    http_response_code(404);
    exit('Task not found.');
}
$stmt = mysqli_prepare($conn, "UPDATE tasktbl SET status='cancel',board_status='done' WHERE id = ? AND company_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

oecrm_audit($conn, 'tasks', 'archive', 'task', $id, 'Task archived', $task, ['status'=>'cancel']);
$_SESSION['project_flash'] = 'Task archived successfully. Timesheet history was preserved.';
header('Location:' . ($projectId ? 'projectBoard.php?id=' . $projectId : 'viewTask.php'));
exit;
?>
