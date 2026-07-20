<?php
$active_menu = 'projects';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$companyId = oecrm_current_company_id($conn);
$id = (int) ($_GET['id'] ?? 0);
$projectId = (int) ($_GET['project_id'] ?? 0);
oecrm_require_permission($conn, 'projects', 'edit');

$task = null;
$selectedAssignee = 0;
if ($id) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM tasktbl WHERE id=? AND company_id=?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
    mysqli_stmt_execute($stmt);
    $task = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$task) {
        http_response_code(404);
        exit('Task not found.');
    }
    $projectId = (int) $task['projectId'];
    $assigneeResult = mysqli_query($conn, 'SELECT employee_id FROM task_assignees WHERE task_id=' . (int) $id . ' ORDER BY is_primary DESC,assigned_at LIMIT 1');
    $assigneeRow = $assigneeResult ? mysqli_fetch_assoc($assigneeResult) : null;
    $selectedAssignee = (int) ($assigneeRow['employee_id'] ?? 0);
}

$project = null;
if ($projectId) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM projectstbl WHERE id=? AND company_id=?');
    mysqli_stmt_bind_param($stmt, 'ii', $projectId, $companyId);
    mysqli_stmt_execute($stmt);
    $project = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}
if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}

$team = mysqli_query($conn, 'SELECT e.id,e.employeeCode,e.name FROM project_team_members tm JOIN employeestbl e ON e.id=tm.employee_id WHERE tm.project_id=' . (int) $projectId . ' AND tm.left_at IS NULL AND e.status=0 ORDER BY e.name');
if (mysqli_num_rows($team) === 0) {
    $team = mysqli_query($conn, 'SELECT id,employeeCode,name FROM employeestbl WHERE status=0 ORDER BY name');
}
$qaMembers = mysqli_query($conn, 'SELECT e.employeeCode,e.name FROM project_team_members tm JOIN employeestbl e ON e.id=tm.employee_id WHERE tm.project_id=' . (int) $projectId . ' AND tm.left_at IS NULL AND e.status=0 AND e.designation LIKE "%QA%" ORDER BY e.name');
if (mysqli_num_rows($qaMembers) === 0) {
    $qaMembers = mysqli_query($conn, 'SELECT employeeCode,name FROM employeestbl WHERE company_id=' . (int) $companyId . ' AND status=0 AND (designation LIKE "%QA%" OR designation LIKE "%Quality Assurance%") ORDER BY name');
}
$taskStatuses = [
    'open' => 'Open',
    'in_progress' => 'In Progress',
    'completed' => 'Completed',
    'in_review' => 'In Review',
    'to_be_tested' => 'To be Tested',
    'on_hold' => 'On Hold',
    'cancelled' => 'Cancelled',
    'staging_server' => 'Staging Server',
    'production' => 'Production',
    'closed' => 'Closed',
];
$error = $_SESSION['task_error'] ?? '';
unset($_SESSION['task_error']);
?>
<div id="page-wrapper" class="compact-admin-page resource-form-page">
    <div class="foundation-titlebar">
        <a href="projectBoard.php?id=<?php echo (int) $projectId; ?>"><i class="fa fa-arrow-left"></i> <?php echo oecrm_h($project['projectName']); ?></a>
        <h2><?php echo $id ? 'Edit Task' : 'Add Task'; ?></h2>
    </div>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading">Task Details</div>
        <div class="panel-body">
            <style>
                .resource-form select.form-control{
                    height:38px;
                    background:#fff;
                    white-space:nowrap;
                    overflow:hidden;
                    text-overflow:ellipsis;
                    padding-right:34px;
                }
                .resource-form .select2-container{
                    width:100% !important;
                }
                .resource-form .select2-container .select2-choice{
                    height:38px;
                    line-height:36px;
                    overflow:hidden;
                    text-overflow:ellipsis;
                }
                .resource-form .select2-container .select2-search input,
                .resource-form .select2-search input{
                    width:100% !important;
                    box-sizing:border-box;
                }
            </style>
            <form method="post" action="taskAction.php" class="resource-form" enctype="multipart/form-data">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $id ? 'update_task' : 'create_task'; ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="project_id" value="<?php echo (int) $projectId; ?>">
                <div class="form-group"><label>Task Title</label><input class="form-control" name="task_title" required value="<?php echo oecrm_h($task['taskTitle'] ?? ''); ?>"></div>
                <div class="form-group">
                    <label>Assign To</label>
                    <select class="form-control" name="developer_id" required>
                        <option value="">Select member</option>
                        <?php while ($employee = mysqli_fetch_assoc($team)): ?>
                            <option value="<?php echo (int) $employee['id']; ?>" <?php echo $selectedAssignee === (int) $employee['id'] ? 'selected' : ''; ?>><?php echo oecrm_h($employee['employeeCode'] . ' - ' . $employee['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>QA Reviewers</label>
                    <div class="form-control" style="height:auto;min-height:38px;background:#f8f9fb">
                        <?php if (mysqli_num_rows($qaMembers) === 0): ?>
                            No active QA designation employee is available in this company.
                        <?php else: ?>
                            <?php $qaNames=[]; while ($qa = mysqli_fetch_assoc($qaMembers)) $qaNames[] = $qa['employeeCode'] . ' - ' . $qa['name']; echo oecrm_h(implode(', ', $qaNames)); ?>
                        <?php endif; ?>
                    </div>
                    <small>QA is selected from the project team first; otherwise active QA employees from the same company are assigned.</small>
                </div>
                <div class="form-group"><label>Priority</label><select class="form-control" name="priority"><?php foreach (['low', 'medium', 'high', 'critical'] as $value): ?><option value="<?php echo $value; ?>" <?php echo ($task['priority'] ?? 'medium') === $value ? 'selected' : ''; ?>><?php echo ucfirst($value); ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Status</label><select class="form-control" name="status"><?php foreach ($taskStatuses as $value => $label): ?><option value="<?php echo $value; ?>" <?php echo ($task['status'] ?? 'open') === $value ? 'selected' : ''; ?>><?php echo $label; ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Assign Date</label><input class="form-control" type="date" name="assign_date" value="<?php echo oecrm_h($task['assignDate'] ?? date('Y-m-d')); ?>"></div>
                <div class="form-group"><label>Expected Date</label><input class="form-control" type="date" name="expected_date" value="<?php echo oecrm_h($task['expectedDate'] ?? ''); ?>"></div>
                <div class="form-group"><label>Estimated Hours</label><input class="form-control" type="number" min="0" step=".25" name="estimated_hours" value="<?php echo oecrm_h($task['estimated_hours'] ?? '0'); ?>"></div>
                <div class="form-group resource-notes"><label>Description</label><textarea class="form-control" name="task_details" rows="4"><?php echo oecrm_h($task['task_details'] ?? ''); ?></textarea></div>
                <div class="resource-actions">
                    <button class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $id ? 'Update Task' : 'Create Task'; ?></button>
                    <a href="viewTask.php" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
