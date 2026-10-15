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
<style>
.task-list-page .task-search-field { width: 100%; max-width: 480px; margin-bottom: 18px; }
.task-list-page .task-search-field label { display: block; margin-bottom: 6px; color: #344054; }
.task-list-page .task-search-field input { width: 100%; height: 40px; padding: 8px 12px; font-size: 14px; }
.task-list-page .task-search-field small { display: block; margin-top: 6px; color: #667085; line-height: 1.5; }
.task-list-page .foundation-heading .resource-actions { flex-wrap: wrap; align-items: flex-end; }
.task-list-page .task-new-project label { display: block; margin-bottom: 6px; font-size: 12px; color: #344054; }
.task-list-page .task-new-project select { min-width: 220px; height: 38px; }
.task-list-page .task-company-context { margin-bottom: 16px; color: #667085; line-height: 1.5; }

.task-list-page table td:nth-child(2) { overflow-wrap: anywhere; }
@media (max-width: 700px) {
    .task-list-page .foundation-heading { flex-wrap: wrap; }
    .task-list-page .foundation-heading .resource-actions { width: 100%; }
    .task-list-page .task-new-project, .task-list-page .task-new-project select { width: 100%; min-width: 0; }
}
</style>
<div id="page-wrapper" class="compact-admin-page project-workspace task-list-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading">
            <span>Task Management</span>
            <div class="resource-actions">
                <div class="task-new-project"><label for="taskProjectSelect">Project for new task</label><select class="form-control input-sm" id="taskProjectSelect" data-oecrm-native="1" <?php echo $projectCount === 0 ? 'disabled' : ''; ?>>
                    <option value=""><?php echo $projectCount > 0 ? 'Choose a project' : 'No projects in this company'; ?></option>
                    <?php while ($project = mysqli_fetch_assoc($projects)): ?><option value="<?php echo (int) $project['id']; ?>"><?php echo oecrm_h($project['projectName']); ?></option><?php endwhile; ?>
                </select></div>
                <button type="button" class="btn btn-primary btn-sm" onclick="goAddTask()" <?php echo $projectCount === 0 ? 'disabled' : ''; ?>><i class="fa fa-plus"></i> Add Task</button>
            </div>
        </div>
        <div class="panel-body">
            <p class="task-company-context">Showing <?php echo (int) $taskCount; ?> task<?php echo $taskCount === 1 ? '' : 's'; ?> for <strong><?php echo oecrm_h($companyName); ?></strong>. Tasks are listed for the active company selected in the sidebar.</p>
            <div class="task-search-field">
                <label for="taskSearch">Search Tasks</label>
                <input type="search" id="taskSearch" class="form-control" placeholder="Search tasks..." aria-controls="dataTables-example" aria-describedby="taskSearchHint">
                <small id="taskSearchHint">Search by task, project, owner, priority, due date or status.</small>
            </div>
            <table class="table foundation-table" id="dataTables-example">
                <thead><tr><th>Project</th><th>Task</th><th>Owner</th><th>Priority</th><th>Due</th><th>Status</th><th>Attachment</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($tasks) === 0): ?><tr><td colspan="8" class="empty-cell">No tasks found for <?php echo oecrm_h($companyName); ?>.</td></tr><?php endif; ?>
                <?php while ($task = mysqli_fetch_assoc($tasks)): ?>
                    <tr>
                        <td><?php echo oecrm_h($task['projectName'] ?: '-'); ?></td>
                        <td><strong><?php echo oecrm_h($task['taskTitle'] ?: $task['task_details']); ?></strong></td>
                        <td><?php echo oecrm_h($task['employee_name'] ?: '-'); ?></td>
                        <td><?php echo ucfirst($task['priority']); ?></td>
                        <td><?php echo oecrm_h($task['expectedDate']); ?></td>
                        <td><?php echo oecrm_h(ucwords(str_replace('_', ' ', $task['status']))); ?></td>
                        <td><?php if ((int)($task['attachment_count'] ?? 0) > 0): ?><a class="btn btn-xs btn-default" href="projectBoard.php?id=<?php echo (int) $task['projectId']; ?>" title="View attachments"><i class="fa fa-paperclip"></i> <?php echo (int) $task['attachment_count']; ?></a><?php else: ?>-<?php endif; ?></td>
                        <td>
                            <a class="btn btn-xs btn-primary" title="View" href="projectBoard.php?id=<?php echo (int) $task['projectId']; ?>"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-xs btn-warning" title="Edit" href="taskEditor.php?id=<?php echo (int) $task['id']; ?>"><i class="fa fa-pencil"></i></a>
                            <form method="post" action="deleteTask.php" style="display:inline;" data-confirm="Delete this task? Timesheet history will be preserved.">
                                <?php echo oecrm_csrf_field(); ?>
                                <input type="hidden" name="delete" value="<?php echo (int) $task['id']; ?>">
                                <input type="hidden" name="project_id" value="<?php echo (int) $task['projectId']; ?>">
                                <button type="submit" class="btn btn-xs btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
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
        responsive: true,
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

