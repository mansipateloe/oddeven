<?php
$active_menu = 'projects';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'projects', 'view');
$companyId = oecrm_current_company_id($conn);
$id = (int) ($_GET['id'] ?? 0);
$project = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT p.*,c.display_name client_name FROM projectstbl p LEFT JOIN clients c ON c.id=p.client_id WHERE p.id=' . (int) $id . ' AND p.company_id=' . (int) $companyId));
if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}

$tasks = mysqli_query($conn, 'SELECT t.* FROM tasktbl t WHERE CAST(t.projectId AS UNSIGNED)=' . (int) $id . ' AND t.company_id=' . (int) $companyId . ' ORDER BY FIELD(t.status,"open","in_progress","in_review","to_be_tested","staging_server","production","on_hold","completed","closed","cancelled"),t.expectedDate');
$teamCount = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM project_team_members tm JOIN employeestbl e ON e.id=tm.employee_id WHERE tm.project_id=' . (int) $id . ' AND tm.left_at IS NULL AND e.status=0'));
$milestones = mysqli_query($conn, 'SELECT * FROM project_milestones WHERE project_id=' . (int) $id . ' ORDER BY due_date');
$flash = $_SESSION['project_flash'] ?? '';
unset($_SESSION['project_flash']);
?>
<div id="page-wrapper" class="compact-admin-page project-board">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="foundation-titlebar">
        <a href="projectWorkspace.php"><i class="fa fa-arrow-left"></i> Projects</a>
        <h2><?php echo oecrm_h($project['projectName']); ?></h2>
        <div class="resource-actions">
            <a class="btn btn-primary btn-sm" href="taskEditor.php?project_id=<?php echo (int) $id; ?>"><i class="fa fa-plus"></i> Add Task</a>
            <a class="btn btn-warning btn-sm" href="projectEditor.php?id=<?php echo (int) $id; ?>"><i class="fa fa-pencil"></i> Edit Project</a>
        </div>
    </div>
    <div class="project-board-head">
        <div><small>Client</small><strong><?php echo oecrm_h($project['client_name'] ?: $project['customerName']); ?></strong></div>
        <div><small>Deadline</small><strong><?php echo date('d M Y', strtotime($project['enddate'])); ?></strong></div>
        <div><small>Budget</small><strong><?php echo number_format((float) $project['amount'], 2); ?></strong></div>
        <div><small>Progress</small><strong><?php echo (int) $project['progress_percent']; ?>%</strong><em>Manual field; portfolio auto-calculates from completed tasks when this is 0.</em></div>
    </div>
    <div class="project-board-grid">
        <section>
            <div class="panel panel-default">
                <div class="panel-heading foundation-heading"><span>Tasks</span><a href="taskEditor.php?project_id=<?php echo (int) $id; ?>" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Add Task</a></div>
                <div class="panel-body">
                    <table class="table foundation-table">
                        <thead><tr><th>Task</th><th>Priority</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php if (mysqli_num_rows($tasks) === 0): ?><tr><td colspan="5" class="empty-cell">No tasks yet.</td></tr><?php endif; ?>
                        <?php while ($task = mysqli_fetch_assoc($tasks)): ?>
                            <tr>
                                <td><strong><?php echo oecrm_h($task['taskTitle'] ?: $task['task_details']); ?></strong><br><small><?php echo oecrm_h(strlen($task['task_details']) > 80 ? substr($task['task_details'], 0, 80) . '...' : $task['task_details']); ?></small></td>
                                <td><?php echo ucfirst($task['priority']); ?></td>
                                <td><?php echo oecrm_h($task['expectedDate']); ?></td>
                                <td><?php echo oecrm_h(ucwords(str_replace('_', ' ', $task['status']))); ?></td>
                                <td>
                                    <a class="btn btn-xs btn-info" title="View/Edit" href="taskEditor.php?id=<?php echo (int) $task['id']; ?>"><i class="fa fa-pencil"></i></a>
                                    <a class="btn btn-xs btn-danger" title="Delete" data-confirm="Delete this task?" href="deleteTask.php?delete=<?php echo (int) $task['id']; ?>&project_id=<?php echo (int) $id; ?>"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <aside>
            <div class="panel panel-default">
                <div class="panel-heading">Active Team</div>
                <div class="panel-body project-side-list">
                    <div><i class="fa fa-users"></i><span><strong><?php echo (int) $teamCount['total']; ?> active members</strong></span></div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Milestones</div>
                <div class="panel-body project-side-list">
                    <?php if (mysqli_num_rows($milestones) === 0): ?><p>No milestones yet.</p><?php endif; ?>
                    <?php while ($milestone = mysqli_fetch_assoc($milestones)): ?><div><i class="fa fa-flag"></i><span><strong><?php echo oecrm_h($milestone['title']); ?></strong><small><?php echo oecrm_h($milestone['due_date'] . ' | ' . $milestone['status']); ?></small></span></div><?php endwhile; ?>
                </div>
            </div>
        </aside>
    </div>
</div>
<?php include 'footer.php'; ?>
