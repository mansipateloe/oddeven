<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$employeeId = (int) $_SESSION['employeeId'];
$stmt = mysqli_prepare($conn, 'SELECT DISTINCT p.*,c.display_name client_name,
    (SELECT GROUP_CONCAT(CONCAT(e.name, IF(tm.role_name IS NULL OR tm.role_name="", "", CONCAT(" - ", tm.role_name))) ORDER BY e.name SEPARATOR ", ") FROM project_team_members tm JOIN employeestbl e ON e.id=tm.employee_id WHERE tm.project_id=p.id AND tm.left_at IS NULL AND e.status=0) team_members,
    (SELECT GROUP_CONCAT(e.name ORDER BY e.name SEPARATOR ", ") FROM project_team_members tm JOIN employeestbl e ON e.id=tm.employee_id WHERE tm.project_id=p.id AND tm.left_at IS NULL AND e.status=0 AND (e.designation LIKE "%QA%" OR e.designation LIKE "%Quality Assurance%" OR tm.role_name LIKE "%QA%")) qa_members
    FROM projectstbl p
    LEFT JOIN project_team_members tm ON tm.project_id=p.id AND tm.employee_id=? AND tm.left_at IS NULL
    LEFT JOIN tasktbl assigned_task ON CAST(assigned_task.projectId AS UNSIGNED)=p.id
    LEFT JOIN task_assignees assigned_to ON assigned_to.task_id=assigned_task.id AND assigned_to.employee_id=?
    LEFT JOIN clients c ON c.id=p.client_id
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
                <thead><tr><th>#</th><th>Project</th><th>Client</th><th>Start</th><th>Deadline</th><th>Progress</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php $index=1; if(mysqli_num_rows($projects)===0): ?><tr><td colspan="8">No projects assigned.</td></tr><?php endif; ?>
                <?php while($project=mysqli_fetch_assoc($projects)): ?>
                    <tr>
                        <td><?php echo $index++; ?></td>
                        <td><strong><?php echo oecrm_h($project['projectName']); ?></strong></td>
                        <td><?php echo oecrm_h($project['client_name'] ?: $project['customerName']); ?></td>
                        <td><?php echo oecrm_h($project['startdate']); ?></td>
                        <td><?php echo oecrm_h($project['enddate']); ?></td>
                        <td><?php echo (int)$project['progress_percent']; ?>%</td>
                        <td><?php echo oecrm_h(ucwords(str_replace('_',' ',$project['status']))); ?></td>
                        <td><button type="button" class="icon-action js-project-detail" title="View" data-title="<?php echo oecrm_h($project['projectName']); ?>" data-client="<?php echo oecrm_h($project['client_name'] ?: $project['customerName']); ?>" data-team="<?php echo oecrm_h($project['team_members'] ?: 'No active team assigned.'); ?>" data-qa="<?php echo oecrm_h($project['qa_members'] ?: 'No QA assigned.'); ?>" data-start="<?php echo oecrm_h($project['startdate']); ?>" data-end="<?php echo oecrm_h($project['enddate']); ?>" data-status="<?php echo oecrm_h(ucwords(str_replace('_',' ',$project['status']))); ?>" data-priority="<?php echo oecrm_h(ucfirst($project['priority'])); ?>" data-progress="<?php echo (int)$project['progress_percent']; ?>%" data-budget="<?php echo oecrm_h(number_format((float)$project['amount'], 2)); ?>" data-platform="<?php echo oecrm_h($project['platform'] ?: '-'); ?>" data-type="<?php echo oecrm_h($project['projectType'] ?: '-'); ?>" data-description="<?php echo oecrm_h($project['description'] ?: 'No description added.'); ?>"><i class="fa fa-eye"></i></button></td>
                    </tr>
                <?php endwhile; mysqli_stmt_close($stmt); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="projectDetailModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">&times;</button><h4 id="projectDetailTitle">Project Details</h4></div><div class="modal-body"><div class="detail-grid"><div><small>Client</small><strong id="projectDetailClient"></strong></div><div><small>Assigned Team</small><strong id="projectDetailTeam"></strong></div><div><small>QA</small><strong id="projectDetailQa"></strong></div><div><small>Start Date</small><strong id="projectDetailStart"></strong></div><div><small>Due Date</small><strong id="projectDetailEnd"></strong></div><div><small>Status</small><strong id="projectDetailStatus"></strong></div><div><small>Priority</small><strong id="projectDetailPriority"></strong></div><div><small>Progress</small><strong id="projectDetailProgress"></strong></div><div><small>Budget</small><strong id="projectDetailBudget"></strong></div><div><small>Platform</small><strong id="projectDetailPlatform"></strong></div><div><small>Project Type</small><strong id="projectDetailType"></strong></div></div><hr><h5>Description</h5><p id="projectDetailDescription"></p></div></div></div></div>
<script>
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.js-project-detail').forEach(function(btn){
    btn.addEventListener('click',function(){
      document.getElementById('projectDetailTitle').textContent=btn.dataset.title||'Project Details';
      document.getElementById('projectDetailClient').textContent=btn.dataset.client||'-';
      document.getElementById('projectDetailTeam').textContent=btn.dataset.team||'-';
      document.getElementById('projectDetailQa').textContent=btn.dataset.qa||'-';
      document.getElementById('projectDetailStart').textContent=btn.dataset.start||'-';
      document.getElementById('projectDetailEnd').textContent=btn.dataset.end||'-';
      document.getElementById('projectDetailStatus').textContent=btn.dataset.status||'-';
      document.getElementById('projectDetailPriority').textContent=btn.dataset.priority||'-';
      document.getElementById('projectDetailProgress').textContent=btn.dataset.progress||'0%';
      document.getElementById('projectDetailBudget').textContent=btn.dataset.budget||'-';
      document.getElementById('projectDetailPlatform').textContent=btn.dataset.platform||'-';
      document.getElementById('projectDetailType').textContent=btn.dataset.type||'-';
      document.getElementById('projectDetailDescription').textContent=btn.dataset.description||'No description added.';
      jQuery('#projectDetailModal').modal('show');
    });
  });
});
</script>
<?php include 'footer.php'; ?>
