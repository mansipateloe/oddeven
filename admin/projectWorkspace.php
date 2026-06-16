<?php
$active_menu = 'projects';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'projects', 'view');
$companyId = oecrm_current_company_id($conn);
$stats = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total,SUM(status="pending") pending,SUM(status="inprogress") active,SUM(status="completed") completed,SUM(enddate<CURDATE() AND status NOT IN ("completed","cancel")) overdue FROM projectstbl WHERE company_id=' . (int) $companyId));
$projects = mysqli_query($conn, 'SELECT p.*,c.display_name client_name,(SELECT COUNT(*) FROM tasktbl t WHERE CAST(t.projectId AS UNSIGNED)=p.id) task_count,(SELECT COUNT(*) FROM tasktbl t WHERE CAST(t.projectId AS UNSIGNED)=p.id AND t.status IN ("closed","completed","2")) done_tasks,(SELECT COUNT(*) FROM project_team_members tm WHERE tm.project_id=p.id AND tm.left_at IS NULL) team_count FROM projectstbl p LEFT JOIN clients c ON c.id=p.client_id WHERE p.company_id=' . (int) $companyId . ' ORDER BY FIELD(p.status,"inprogress","pending","completed","cancel"),p.enddate');
$flash = $_SESSION['project_flash'] ?? '';
unset($_SESSION['project_flash']);
?>
<div id="page-wrapper" class="compact-admin-page project-workspace">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="resource-summary">
        <div><i class="fa fa-folder-open"></i><span><small>Total Projects</small><strong><?php echo (int) $stats['total']; ?></strong></span></div>
        <div><i class="fa fa-spinner"></i><span><small>In Progress</small><strong><?php echo (int) $stats['active']; ?></strong></span></div>
        <div><i class="fa fa-check-circle"></i><span><small>Completed</small><strong><?php echo (int) $stats['completed']; ?></strong></span></div>
        <div><i class="fa fa-exclamation-triangle"></i><span><small>Overdue</small><strong><?php echo (int) $stats['overdue']; ?></strong></span></div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading"><span>Project Portfolio</span><a href="projectEditor.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Project</a></div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead><tr><th>Project</th><th>Client</th><th>Priority</th><th>Tasks</th><th>Progress</th><th>Deadline</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($projects) === 0): ?><tr><td colspan="8" class="empty-cell">No projects created yet.</td></tr><?php endif; ?>
                <?php while ($project = mysqli_fetch_assoc($projects)):
                    $progress = (int) $project['progress_percent'];
                    if (!$progress && (int) $project['task_count'] > 0) {
                        $progress = (int) round(((int) $project['done_tasks'] * 100) / (int) $project['task_count']);
                    }
                ?>
                    <tr>
                        <td><strong><?php echo oecrm_h($project['projectName']); ?></strong><small>Budget: <?php echo number_format((float) $project['amount'], 2); ?></small></td>
                        <td><?php echo oecrm_h($project['client_name'] ?: $project['customerName']); ?></td>
                        <td><span class="priority-<?php echo oecrm_h($project['priority']); ?>"><?php echo ucfirst($project['priority']); ?></span></td>
                        <td><?php echo (int) $project['done_tasks']; ?> / <?php echo (int) $project['task_count']; ?></td>
                        <td><div class="project-progress"><span style="width:<?php echo $progress; ?>%"></span></div><small><?php echo $progress; ?>%</small></td>
                        <td><?php echo date('d M Y', strtotime($project['enddate'])); ?></td>
                        <td><span class="client-status <?php echo oecrm_h($project['status']); ?>"><?php echo ucfirst($project['status']); ?></span></td>
                        <td>
                            <a class="btn btn-xs btn-primary" title="View" href="projectBoard.php?id=<?php echo (int) $project['id']; ?>"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-xs btn-warning" title="Edit" href="projectEditor.php?id=<?php echo (int) $project['id']; ?>"><i class="fa fa-pencil"></i></a>
                            <?php if ($project['status'] !== 'cancel' && oecrm_can($conn, 'projects', 'delete')): ?><a class="btn btn-xs btn-danger" title="Archive" data-confirm="Archive this project? Tasks will be cancelled and historical data will remain available." href="deleteProject.php?delete=<?php echo (int) $project['id']; ?>"><i class="fa fa-archive"></i></a><?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
