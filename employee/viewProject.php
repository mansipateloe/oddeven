<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$employeeId = (int) $_SESSION['employeeId'];
$stmt = mysqli_prepare($conn, 'SELECT DISTINCT p.*,c.display_name client_name,co.display_name company_name
    FROM projectstbl p
    LEFT JOIN project_team_members tm ON tm.project_id=p.id AND tm.employee_id=? AND tm.left_at IS NULL
    LEFT JOIN tasktbl assigned_task ON CAST(assigned_task.projectId AS UNSIGNED)=p.id
    LEFT JOIN task_assignees assigned_to ON assigned_to.task_id=assigned_task.id AND assigned_to.employee_id=?
    LEFT JOIN clients c ON c.id=p.client_id
    LEFT JOIN companies co ON co.id=p.company_id
    WHERE tm.employee_id=? OR assigned_to.employee_id=?
    ORDER BY p.enddate,p.projectName');
mysqli_stmt_bind_param($stmt, 'iiii', $employeeId, $employeeId, $employeeId, $employeeId);
mysqli_stmt_execute($stmt);
$projects = mysqli_stmt_get_result($stmt);
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="panel panel-default">
        <div class="panel-heading">My Assigned Projects</div>
        <div class="panel-body table-responsive">
            <table class="table table-striped table-bordered">
                <thead><tr><th>#</th><th>Project</th><th>Company</th><th>Client</th><th>Start</th><th>Deadline</th><th>Progress</th><th>Status</th></tr></thead>
                <tbody>
                <?php $index=1; if(mysqli_num_rows($projects)===0): ?><tr><td colspan="8">No projects assigned.</td></tr><?php endif; ?>
                <?php while($project=mysqli_fetch_assoc($projects)): ?>
                    <tr>
                        <td><?php echo $index++; ?></td>
                        <td><strong><?php echo oecrm_h($project['projectName']); ?></strong></td>
                        <td><?php echo oecrm_h($project['company_name'] ?: '-'); ?></td>
                        <td><?php echo oecrm_h($project['client_name'] ?: $project['customerName']); ?></td>
                        <td><?php echo oecrm_h($project['startdate']); ?></td>
                        <td><?php echo oecrm_h($project['enddate']); ?></td>
                        <td><?php echo (int)$project['progress_percent']; ?>%</td>
                        <td><?php echo oecrm_h(ucwords(str_replace('_',' ',$project['status']))); ?></td>
                    </tr>
                <?php endwhile; mysqli_stmt_close($stmt); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
