<?php
$active_menu = 'projects';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'projects', 'view');
$companyId = oecrm_current_company_id($conn);
$company = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT display_name FROM companies WHERE id=' . (int) $companyId));
$companyName = $company['display_name'] ?? 'the selected company';
$projects = mysqli_query($conn, 'SELECT id,projectName FROM projectstbl WHERE company_id=' . (int) $companyId . ' ORDER BY projectName');
$tasks = mysqli_query($conn, 'SELECT t.*,p.projectName,(SELECT COUNT(*) FROM task_attachments a WHERE a.task_id=t.id) attachment_count,GROUP_CONCAT(DISTINCT e.name ORDER BY ta.is_primary DESC,e.name SEPARATOR ", ") employee_name FROM tasktbl t LEFT JOIN projectstbl p ON p.id=CAST(t.projectId AS UNSIGNED) LEFT JOIN task_assignees ta ON ta.task_id=t.id LEFT JOIN employeestbl e ON e.id=ta.employee_id WHERE t.status <> "cancel" AND t.company_id=' . (int) $companyId . ' GROUP BY t.id ORDER BY t.id DESC');
$taskCount = mysqli_num_rows($tasks);
$projectCount = mysqli_num_rows($projects);
$flash = $_SESSION['project_flash'] ?? '';
unset($_SESSION['project_flash']);
?>
<link rel="stylesheet" href="../dist/css/task-list.css?v=20261023-1">
<div id="page-wrapper" class="compact-admin-page project-workspace task-list-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading">
            <div><h2>Task Management <span class="task-count"><?php echo (int) $taskCount; ?></span></h2>
                <small>Manage assignments, priorities and deadlines.</small></div>
            <span class="task-company"><i class="fa fa-building-o" aria-hidden="true"></i> <?php echo oecrm_h($companyName); ?></span>
        </div>
        <div class="panel-body">
            <div class="task-toolbar">
                <div class="task-search-field">
                    <label for="taskSearch">Search Tasks</label>
                    <input type="search" id="taskSearch" class="form-control" placeholder="Search task, project or owner..." aria-controls="dataTables-example" aria-describedby="taskSearchHint">
                    <small id="taskSearchHint">Search by task, project, owner, priority, due date or status.</small>
                </div>
                <div class="task-create-controls">
                <div class="task-new-project"><label for="taskProjectSelect">Project for new task</label><select class="form-control input-sm" id="taskProjectSelect" data-oecrm-native="1" <?php echo $projectCount === 0 ? 'disabled' : ''; ?>>
                    <option value=""><?php echo $projectCount > 0 ? 'Choose a project' : 'No projects in this company'; ?></option>
                    <?php while ($project = mysqli_fetch_assoc($projects)): ?><option value="<?php echo (int) $project['id']; ?>"><?php echo oecrm_h($project['projectName']); ?></option><?php endwhile; ?>
                </select></div>
                <button type="button" id="taskAddButton" class="btn btn-primary" onclick="goAddTask()" disabled><i class="fa fa-plus"></i> Add Task</button>
                </div>
            </div>
            <?php if ($projectCount === 0): ?><div class="alert alert-info task-project-empty">Create a project for this company before adding tasks. <a href="projectEditor.php">Create project</a></div><?php endif; ?>
            <div class="task-table-scroll" role="region" aria-label="Task list" tabindex="0">
            <table class="table foundation-table" id="dataTables-example">
                <thead><tr><th>Project</th><th>Task</th><th>Owner</th><th>Priority</th><th>Due</th><th>Status</th><th>Attachment</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($tasks) === 0): ?><tr><td colspan="8" class="empty-cell">No tasks found for <?php echo oecrm_h($companyName); ?>.</td></tr><?php endif; ?>
                <?php while ($task = mysqli_fetch_assoc($tasks)): ?>
                    <tr>
                        <td><?php echo oecrm_h($task['projectName'] ?: '-'); ?></td>
                        <td><strong class="task-title"><?php echo oecrm_h($task['taskTitle'] ?: $task['task_details']); ?></strong><small class="task-reference">#<?php echo (int) $task['id']; ?></small></td>
                        <td><?php echo oecrm_h($task['employee_name'] ?: '-'); ?></td>
                        <td><span class="task-priority task-priority-<?php echo oecrm_h($task['priority']); ?>"><?php echo oecrm_h(ucfirst($task['priority'])); ?></span></td>
                        <td class="task-due"><?php echo oecrm_h($task['expectedDate'] ?: '—'); ?></td>
                        <td><span class="task-status task-status-<?php echo oecrm_h($task['status']); ?>"><?php echo oecrm_h(ucwords(str_replace('_', ' ', $task['status']))); ?></span></td>
                        <td><?php if ((int)($task['attachment_count'] ?? 0) > 0): ?><a class="btn btn-xs btn-default" href="projectBoard.php?id=<?php echo (int) $task['projectId']; ?>" title="View attachments"><i class="fa fa-paperclip"></i> <?php echo (int) $task['attachment_count']; ?></a><?php else: ?>-<?php endif; ?></td>
                        <td><div class="task-row-actions">
                            <a class="btn btn-default" title="View task project" aria-label="View task project" href="projectBoard.php?id=<?php echo (int) $task['projectId']; ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a class="btn btn-default" title="Edit task" aria-label="Edit task" href="taskEditor.php?id=<?php echo (int) $task['id']; ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                            <form method="post" action="deleteTask.php" style="display:inline;" data-confirm="Delete this task? Timesheet history will be preserved.">
                                <?php echo oecrm_csrf_field(); ?>
                                <input type="hidden" name="delete" value="<?php echo (int) $task['id']; ?>">
                                <input type="hidden" name="project_id" value="<?php echo (int) $task['projectId']; ?>">
                                <button type="submit" class="btn btn-default task-delete" title="Delete task" aria-label="Delete task"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                            </form>
                        </div></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var projectSelect = document.getElementById('taskProjectSelect');
    var addButton = document.getElementById('taskAddButton');
    function updateAddButton() { addButton.disabled = !projectSelect.value; }
    projectSelect.addEventListener('change', updateAddButton);
    updateAddButton();
    var tableElement = document.getElementById('dataTables-example');
    var search = document.getElementById('taskSearch');
    if (!tableElement || !search || !window.jQuery || !jQuery.fn.DataTable) return;
    // Let DataTables render its own empty state without a colspan data row.
    var emptyCell = tableElement.querySelector('tbody .empty-cell');
    if (emptyCell) emptyCell.parentNode.remove();
    var table = jQuery(tableElement).DataTable({
        dom: 'lrtip',
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [],
        autoWidth: false,
        responsive: false,
        columnDefs: [{targets: [6, 7], searchable: false, orderable: false}],
        language: {
            lengthMenu: 'Show _MENU_ tasks',
            info: 'Showing _START_ to _END_ of _TOTAL_ tasks',
            infoEmpty: 'Showing 0 tasks',
            infoFiltered: '(filtered from _MAX_ tasks)',
            emptyTable: <?php echo json_encode('No tasks found for ' . $companyName . '.', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
            zeroRecords: 'No tasks match your search.'
        }
    });
    search.addEventListener('input', function () {
        table.search(search.value.trim()).draw();
    });
    table.search(search.value.trim()).draw();
});
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

