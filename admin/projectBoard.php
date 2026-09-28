<?php
$active_menu = 'projects';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../projectDeadlineHelpers.php';

oecrm_require_permission($conn, 'projects', 'view');
$companyId = oecrm_current_company_id($conn);
oecrm_project_deadline_ensure_schema($conn);
$id = (int) ($_GET['id'] ?? 0);
$project = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT p.*,c.display_name client_name FROM projectstbl p LEFT JOIN clients c ON c.id=p.client_id WHERE p.id=' . (int) $id . ' AND p.company_id=' . (int) $companyId));
if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}
$originalDeadline = oecrm_project_original_deadline($project);
$currentDeadline = oecrm_project_current_deadline($project);
$deadlineExtensions = (int) ($project['deadline_extended_count'] ?? 0);
$overdueDays = ($currentDeadline && strtotime($currentDeadline) < strtotime(date('Y-m-d')) && !in_array($project['status'], ['completed', 'cancel'], true))
    ? (int) floor((strtotime(date('Y-m-d')) - strtotime($currentDeadline)) / 86400)
    : 0;
$remainingDays = $currentDeadline ? (int) ceil((strtotime($currentDeadline) - strtotime(date('Y-m-d'))) / 86400) : null;

$tasks = mysqli_query($conn, 'SELECT t.*,
    (SELECT GROUP_CONCAT(e1.name ORDER BY ta1.is_primary DESC,e1.name SEPARATOR ", ") FROM task_assignees ta1 JOIN employeestbl e1 ON e1.id=ta1.employee_id WHERE ta1.task_id=t.id AND ta1.is_primary=1) task_owners,
    (SELECT GROUP_CONCAT(e2.name ORDER BY e2.name SEPARATOR ", ") FROM task_assignees ta2 JOIN employeestbl e2 ON e2.id=ta2.employee_id WHERE ta2.task_id=t.id AND ta2.is_primary=0) qa_members,
    (SELECT GROUP_CONCAT(CONCAT(a.id, ":", a.original_name) ORDER BY a.id SEPARATOR "||") FROM task_attachments a WHERE a.task_id=t.id) attachment_list
    FROM tasktbl t WHERE CAST(t.projectId AS UNSIGNED)=' . (int) $id . ' AND t.company_id=' . (int) $companyId . ' ORDER BY FIELD(t.status,"open","in_progress","in_review","to_be_tested","staging_server","production","on_hold","completed","closed","cancelled"),t.expectedDate');
$teamCount = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM project_team_members tm JOIN employeestbl e ON e.id=tm.employee_id WHERE tm.project_id=' . (int) $id . ' AND tm.left_at IS NULL AND e.status=0'));
$teamMembers = mysqli_query($conn, 'SELECT e.employeeCode,e.name,e.designation FROM project_team_members tm JOIN employeestbl e ON e.id=tm.employee_id WHERE tm.project_id=' . (int) $id . ' AND tm.left_at IS NULL AND e.status=0 ORDER BY e.name');
$milestones = mysqli_query($conn, 'SELECT * FROM project_milestones WHERE project_id=' . (int) $id . ' ORDER BY due_date');
$deadlineHistory = mysqli_query($conn, 'SELECT h.*,t.taskTitle FROM project_deadline_history h LEFT JOIN tasktbl t ON t.id=h.task_id WHERE h.project_id=' . (int) $id . ' AND h.company_id=' . (int) $companyId . ' ORDER BY h.created_at DESC,h.id DESC LIMIT 8');
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
    <style>.deadline-risk{display:inline-block;margin-top:4px;border-radius:999px;padding:2px 7px;background:#fff4e5;color:#b54708;font-size:11px;font-weight:700}.deadline-history-item{border-bottom:1px solid #eef0f6;padding:10px 0}.deadline-history-item:last-child{border-bottom:0}.deadline-history-item small{display:block;color:#667085}.deadline-history-item strong{display:block;color:#18213b}</style>
    <div class="project-board-head">
        <div><small>Client</small><strong><?php echo oecrm_h($project['client_name'] ?: $project['customerName']); ?></strong></div>
        <div><small>Original Deadline</small><strong><?php echo oecrm_h(oecrm_project_deadline_format($originalDeadline)); ?></strong></div>
        <div><small>Current Deadline</small><strong><?php echo oecrm_h(oecrm_project_deadline_format($currentDeadline)); ?></strong><em><?php echo $deadlineExtensions; ?> extension(s)</em></div>
        <div><small>Budget + Extra Scope</small><strong><?php echo number_format((float) $project['amount'] + (float) ($project['extra_scope_value'] ?? 0), 2); ?></strong><em>Base: <?php echo number_format((float) $project['amount'], 2); ?> | Extra: <?php echo number_format((float) ($project['extra_scope_value'] ?? 0), 2); ?></em></div>
        <div><small>Timeline</small><strong><?php echo $overdueDays > 0 ? $overdueDays . ' days overdue' : ($remainingDays === null ? '-' : max(0, $remainingDays) . ' days left'); ?></strong></div>
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
                        <?php while ($task = mysqli_fetch_assoc($tasks)): $isRiskTask = oecrm_project_task_beyond_deadline($task['expectedDate'] ?? null, $currentDeadline); ?>
                            <tr>
                                <td><strong><?php echo oecrm_h($task['taskTitle'] ?: $task['task_details']); ?></strong><br><small><?php echo oecrm_h(strlen($task['task_details']) > 80 ? substr($task['task_details'], 0, 80) . '...' : $task['task_details']); ?></small></td>
                                <td><?php echo ucfirst($task['priority']); ?></td>
                                <td><?php echo oecrm_h($task['expectedDate']); ?><?php if ($isRiskTask): ?><br><span class="deadline-risk">Beyond deadline</span><?php endif; ?></td>
                                <td><?php echo oecrm_h(ucwords(str_replace('_', ' ', $task['status']))); ?></td>
                                <td>
                                    <button type="button" class="icon-action js-admin-task-detail" title="View" data-title="<?php echo oecrm_h($task['taskTitle'] ?: $task['task_details']); ?>" data-project="<?php echo oecrm_h($project['projectName']); ?>" data-client="<?php echo oecrm_h($project['client_name'] ?: $project['customerName']); ?>" data-owner="<?php echo oecrm_h($task['task_owners'] ?: '-'); ?>" data-qa="<?php echo oecrm_h($task['qa_members'] ?: 'No QA assigned.'); ?>" data-start="<?php echo oecrm_h($task['assignDate']); ?>" data-due="<?php echo oecrm_h($task['expectedDate']); ?>" data-priority="<?php echo oecrm_h(ucfirst($task['priority'])); ?>" data-status="<?php echo oecrm_h(ucwords(str_replace('_', ' ', $task['status']))); ?>" data-estimated="<?php echo number_format((float)$task['estimated_hours'], 2); ?> hrs" data-description="<?php echo oecrm_h($task['task_details'] ?: 'No description added.'); ?>" data-attachments="<?php echo oecrm_h($task['attachment_list'] ?: ''); ?>"><i class="fa fa-eye"></i></button>
                                    <a class="btn btn-xs btn-info" title="Edit" href="taskEditor.php?id=<?php echo (int) $task['id']; ?>"><i class="fa fa-pencil"></i></a>
                                    <form method="post" action="deleteTask.php" style="display:inline;">
                                        <?php echo oecrm_csrf_field(); ?>
                                        <input type="hidden" name="delete" value="<?php echo (int) $task['id']; ?>">
                                        <input type="hidden" name="project_id" value="<?php echo (int) $id; ?>">
                                        <button type="submit" class="btn btn-xs btn-danger" title="Delete" data-confirm="Delete this task?"><i class="fa fa-trash"></i></button>
                                    </form>
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
                    <?php while ($member = mysqli_fetch_assoc($teamMembers)): ?><div><i class="fa fa-user"></i><span><strong><?php echo oecrm_h($member['name']); ?></strong><small><?php echo oecrm_h($member['employeeCode'] . ($member['designation'] ? ' | ' . $member['designation'] : '')); ?></small></span></div><?php endwhile; ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Milestones</div>
                <div class="panel-body project-side-list">
                    <?php if (mysqli_num_rows($milestones) === 0): ?><p>No milestones yet.</p><?php endif; ?>
                    <?php while ($milestone = mysqli_fetch_assoc($milestones)): ?><div><i class="fa fa-flag"></i><span><strong><?php echo oecrm_h($milestone['title']); ?></strong><small><?php echo oecrm_h($milestone['due_date'] . ' | ' . $milestone['status']); ?></small></span></div><?php endwhile; ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Deadline History</div>
                <div class="panel-body project-side-list">
                    <?php if (!$deadlineHistory || mysqli_num_rows($deadlineHistory) === 0): ?><p>No deadline changes yet.</p><?php endif; ?>
                    <?php if ($deadlineHistory): while ($history = mysqli_fetch_assoc($deadlineHistory)): ?>
                        <div class="deadline-history-item">
                            <strong><?php echo oecrm_h(oecrm_project_deadline_format($history['old_deadline']) . ' → ' . oecrm_project_deadline_format($history['new_deadline'])); ?></strong>
                            <small><?php echo oecrm_h(ucwords(str_replace('_', ' ', $history['change_type']))); ?><?php echo $history['taskTitle'] ? ' | Task: ' . oecrm_h($history['taskTitle']) : ''; ?></small>
                            <small><?php echo oecrm_h($history['reason']); ?></small>
                            <?php if ((float) $history['scope_value'] > 0): ?><small>Extra value: <?php echo number_format((float) $history['scope_value'], 2); ?></small><?php endif; ?>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </aside>
    </div>
</div>
<div class="modal fade" id="adminTaskDetailModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">&times;</button><h4 id="adminTaskDetailTitle">Task Details</h4></div><div class="modal-body"><div class="detail-grid"><div><small>Project</small><strong id="adminTaskDetailProject"></strong></div><div><small>Client</small><strong id="adminTaskDetailClient"></strong></div><div><small>Assigned To</small><strong id="adminTaskDetailOwner"></strong></div><div><small>QA</small><strong id="adminTaskDetailQa"></strong></div><div><small>Start Date</small><strong id="adminTaskDetailStart"></strong></div><div><small>Due Date</small><strong id="adminTaskDetailDue"></strong></div><div><small>Priority</small><strong id="adminTaskDetailPriority"></strong></div><div><small>Status</small><strong id="adminTaskDetailStatus"></strong></div><div><small>Estimated</small><strong id="adminTaskDetailEstimated"></strong></div></div><hr><h5>Description</h5><p id="adminTaskDetailDescription"></p><h5>Attachments</h5><div id="adminTaskDetailAttachments" class="task-attachment-list"></div></div></div></div></div>
<script>
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.js-admin-task-detail').forEach(function(btn){
    btn.addEventListener('click',function(){
      document.getElementById('adminTaskDetailTitle').textContent=btn.dataset.title||'Task Details';
      document.getElementById('adminTaskDetailProject').textContent=btn.dataset.project||'-';
      document.getElementById('adminTaskDetailClient').textContent=btn.dataset.client||'-';
      document.getElementById('adminTaskDetailOwner').textContent=btn.dataset.owner||'-';
      document.getElementById('adminTaskDetailQa').textContent=btn.dataset.qa||'-';
      document.getElementById('adminTaskDetailStart').textContent=btn.dataset.start||'-';
      document.getElementById('adminTaskDetailPriority').textContent=btn.dataset.priority||'-';
      document.getElementById('adminTaskDetailDue').textContent=btn.dataset.due||'-';
      document.getElementById('adminTaskDetailStatus').textContent=btn.dataset.status||'-';
      document.getElementById('adminTaskDetailEstimated').textContent=btn.dataset.estimated||'-';
      document.getElementById('adminTaskDetailDescription').textContent=btn.dataset.description||'No description added.';
      var box=document.getElementById('adminTaskDetailAttachments'); box.innerHTML='';
      var items=(btn.dataset.attachments||'').split('||').filter(Boolean);
      if(!items.length){box.innerHTML='<span class="text-muted">No attachment.</span>';} else {
        items.forEach(function(item){var parts=item.split(':'); var id=parts.shift(); var name=parts.join(':'); var a=document.createElement('a'); a.className='btn btn-default btn-sm'; a.href='taskAttachment.php?id='+encodeURIComponent(id); a.innerHTML='<i class="fa fa-paperclip"></i> '; a.appendChild(document.createTextNode(name)); box.appendChild(a);});
      }
      jQuery('#adminTaskDetailModal').modal('show');
    });
  });
});
</script>
<?php include 'footer.php'; ?>
