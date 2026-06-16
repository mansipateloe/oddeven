<?php
$active_menu = 'projects';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'projects', 'view');
$companyId = oecrm_current_company_id($conn);
$projects = mysqli_query($conn, 'SELECT id,projectName FROM projectstbl WHERE company_id=' . (int) $companyId . ' ORDER BY projectName');
$tasks = mysqli_query($conn, 'SELECT t.*,p.projectName,GROUP_CONCAT(DISTINCT e.name ORDER BY ta.is_primary DESC,e.name SEPARATOR ", ") employee_name FROM tasktbl t LEFT JOIN projectstbl p ON p.id=CAST(t.projectId AS UNSIGNED) LEFT JOIN task_assignees ta ON ta.task_id=t.id LEFT JOIN employeestbl e ON e.id=ta.employee_id WHERE t.company_id=' . (int) $companyId . ' GROUP BY t.id ORDER BY t.id DESC');
$flash = $_SESSION['project_flash'] ?? '';
unset($_SESSION['project_flash']);
?>
<div id="page-wrapper" class="compact-admin-page project-workspace">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading">
            <span>Task Management</span>
            <div class="resource-actions">
                <select class="form-control input-sm" id="taskProjectSelect">
                    <option value="">Select project for new task</option>
                    <?php while ($project = mysqli_fetch_assoc($projects)): ?><option value="<?php echo (int) $project['id']; ?>"><?php echo oecrm_h($project['projectName']); ?></option><?php endwhile; ?>
                </select>
                <button type="button" class="btn btn-primary btn-sm" onclick="goAddTask()"><i class="fa fa-plus"></i> Add Task</button>
            </div>
        </div>
        <div class="panel-body">
            <table class="table foundation-table" id="dataTables-example">
                <thead><tr><th>Project</th><th>Task</th><th>Owner</th><th>Priority</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($tasks) === 0): ?><tr><td colspan="7" class="empty-cell">No tasks created yet.</td></tr><?php endif; ?>
                <?php while ($task = mysqli_fetch_assoc($tasks)): ?>
                    <tr>
                        <td><?php echo oecrm_h($task['projectName'] ?: '-'); ?></td>
                        <td><strong><?php echo oecrm_h($task['taskTitle'] ?: $task['task_details']); ?></strong></td>
                        <td><?php echo oecrm_h($task['employee_name'] ?: '-'); ?></td>
                        <td><?php echo ucfirst($task['priority']); ?></td>
                        <td><?php echo oecrm_h($task['expectedDate']); ?></td>
                        <td><?php echo oecrm_h(ucwords(str_replace('_', ' ', $task['status']))); ?></td>
                        <td>
                            <a class="btn btn-xs btn-primary" title="View" href="projectBoard.php?id=<?php echo (int) $task['projectId']; ?>"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-xs btn-warning" title="Edit" href="taskEditor.php?id=<?php echo (int) $task['id']; ?>"><i class="fa fa-pencil"></i></a>
                            <a class="btn btn-xs btn-danger" title="Delete" data-confirm="Delete this task?" href="deleteTask.php?delete=<?php echo (int) $task['id']; ?>&project_id=<?php echo (int) $task['projectId']; ?>"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function goAddTask() {
    var projectId = document.getElementById('taskProjectSelect').value;
    if (!projectId) {
        alert('Please select project first.');
        return;
    }
    window.location.href = 'taskEditor.php?project_id=' + encodeURIComponent(projectId);
}
</script>
<?php include 'footer.php'; ?>
