<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$employeeId = (int) $_SESSION['employeeId'];
$stmt = mysqli_prepare($conn, 'SELECT DISTINCT t.*,ta.is_primary,e.designation,p.projectName,
    c.display_name client_name,
    (SELECT GROUP_CONCAT(e1.name ORDER BY ta1.is_primary DESC,e1.name SEPARATOR ", ") FROM task_assignees ta1 JOIN employeestbl e1 ON e1.id=ta1.employee_id WHERE ta1.task_id=t.id AND ta1.is_primary=1) task_owners,
    (SELECT GROUP_CONCAT(e2.name ORDER BY e2.name SEPARATOR ", ") FROM task_assignees ta2 JOIN employeestbl e2 ON e2.id=ta2.employee_id WHERE ta2.task_id=t.id AND ta2.is_primary=0) qa_members,
    (SELECT GROUP_CONCAT(CONCAT(a.id, ":", a.original_name) ORDER BY a.id SEPARATOR "||") FROM task_attachments a WHERE a.task_id=t.id) attachment_list
    FROM tasktbl t
    JOIN task_assignees ta ON ta.task_id=t.id
    JOIN employeestbl e ON e.id=ta.employee_id
    JOIN projectstbl p ON p.id=CAST(t.projectId AS UNSIGNED)
    LEFT JOIN clients c ON c.id=p.client_id
    WHERE ta.employee_id=?
    ORDER BY FIELD(t.status,"open","in_progress","in_review","to_be_tested","staging_server","production","on_hold","completed","closed","cancelled"),t.expectedDate,t.id DESC');
mysqli_stmt_bind_param($stmt, 'i', $employeeId);
mysqli_stmt_execute($stmt);
$tasks = mysqli_stmt_get_result($stmt);
$flash = $_SESSION['task_flash'] ?? '';
$error = $_SESSION['task_error'] ?? '';
unset($_SESSION['task_flash'], $_SESSION['task_error']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if($flash):?><div class="alert alert-success"><?php echo oecrm_h($flash);?></div><?php endif;?>
    <?php if($error):?><div class="alert alert-danger"><?php echo oecrm_h($error);?></div><?php endif;?>
    <div class="panel panel-default employee-task-table-panel">
        <div class="panel-heading">My Assigned Tasks</div>
        <div class="panel-body table-responsive employee-task-table-wrap">
            <table class="table table-striped table-bordered">
                <thead><tr><th>Project</th><th>Task</th><th>Priority</th><th>Due</th><th>Estimated</th><th>Status</th><th>Update</th><th>View</th></tr></thead>
                <tbody>
                <?php if(mysqli_num_rows($tasks)===0): ?><tr><td colspan="8">No tasks assigned.</td></tr><?php endif; ?>
                <?php while($task=mysqli_fetch_assoc($tasks)): ?>
                    <tr>
                        <td><?php echo oecrm_h($task['projectName']); ?></td>
                        <td><strong><?php echo oecrm_h($task['taskTitle'] ?: $task['task_details']); ?></strong><br><small><?php echo oecrm_h($task['task_details']); ?></small></td>
                        <td><?php echo oecrm_h(ucfirst($task['priority'])); ?></td>
                        <td><?php echo oecrm_h($task['expectedDate']); ?></td>
                        <td><?php echo number_format((float)$task['estimated_hours'],2); ?> hrs</td>
                        <td><span class="lead-status"><?php echo oecrm_h(ucwords(str_replace('_',' ',$task['status']))); ?></span></td>
                        <td>
                            <?php
                            $isQa = ((int)$task['is_primary'] === 0 && stripos((string)$task['designation'], 'QA') !== false);
                            $qaWorkflowStatuses = ['in_review','to_be_tested','staging_server','production'];
                            $waitingForQa = !$isQa && in_array($task['status'], $qaWorkflowStatuses, true);
                            ?>
                            <?php if($waitingForQa):?>
                                <span class="qa-review-waiting"><i class="fa fa-hourglass-half"></i> Waiting for QA Review</span>
                            <?php elseif(!in_array($task['status'],['cancel','cancelled','closed'],true)):?>
                            <form method="post" action="taskStatusAction.php" class="employee-task-update">
                                <?php echo oecrm_csrf_field();?>
                                <input type="hidden" name="task_id" value="<?php echo (int)$task['id'];?>">
                                <select class="form-control employee-task-status" name="status" data-placeholder="Select status">
                                    <?php
                                    $developerTransitions=['open'=>['in_progress'=>'Start Work','on_hold'=>'On Hold'],'in_progress'=>['completed'=>'Submit to QA','on_hold'=>'On Hold'],'on_hold'=>['in_progress'=>'Resume Work']];
                                    $qaTransitions=['in_review'=>['to_be_tested'=>'To be Tested','on_hold'=>'On Hold','cancelled'=>'Cancelled'],'to_be_tested'=>['in_review'=>'Back to Review','staging_server'=>'Staging Server','on_hold'=>'On Hold','cancelled'=>'Cancelled'],'staging_server'=>['to_be_tested'=>'Back to Testing','production'=>'Production','on_hold'=>'On Hold','cancelled'=>'Cancelled'],'production'=>['staging_server'=>'Back to Staging','closed'=>'Close Task','on_hold'=>'On Hold','cancelled'=>'Cancelled'],'on_hold'=>['in_review'=>'Resume Review','cancelled'=>'Cancelled']];
                                    $statusOptions=$isQa?($qaTransitions[$task['status']]??[]):($developerTransitions[$task['status']]??[]);
                                    ?><option value="">Select next status</option><?php foreach($statusOptions as $value=>$label):?><option value="<?php echo $value;?>"><?php echo $label;?></option><?php endforeach;?>
                                </select>
                                <button class="btn btn-primary btn-sm" title="Update status"><i class="fa fa-save"></i></button>
                            </form>
                            <?php endif;?>
                        </td>
                        <td><button type="button" class="icon-action js-task-detail" title="View" data-title="<?php echo oecrm_h($task['taskTitle'] ?: $task['task_details']); ?>" data-project="<?php echo oecrm_h($task['projectName']); ?>" data-client="<?php echo oecrm_h($task['client_name'] ?: '-'); ?>" data-owner="<?php echo oecrm_h($task['task_owners'] ?: '-'); ?>" data-qa="<?php echo oecrm_h($task['qa_members'] ?: 'No QA assigned.'); ?>" data-start="<?php echo oecrm_h($task['assignDate']); ?>" data-due="<?php echo oecrm_h($task['expectedDate']); ?>" data-priority="<?php echo oecrm_h(ucfirst($task['priority'])); ?>" data-estimated="<?php echo number_format((float)$task['estimated_hours'],2); ?> hrs" data-status="<?php echo oecrm_h(ucwords(str_replace('_',' ',$task['status']))); ?>" data-description="<?php echo oecrm_h($task['task_details'] ?: 'No description added.'); ?>" data-attachments="<?php echo oecrm_h($task['attachment_list'] ?: ''); ?>"><i class="fa fa-eye"></i></button></td>
                    </tr>
                <?php endwhile; mysqli_stmt_close($stmt); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="taskDetailModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">&times;</button><h4 id="taskDetailTitle">Task Details</h4></div><div class="modal-body"><div class="detail-grid"><div><small>Project</small><strong id="taskDetailProject"></strong></div><div><small>Client</small><strong id="taskDetailClient"></strong></div><div><small>Assigned To</small><strong id="taskDetailOwner"></strong></div><div><small>QA</small><strong id="taskDetailQa"></strong></div><div><small>Start Date</small><strong id="taskDetailStart"></strong></div><div><small>Due Date</small><strong id="taskDetailDue"></strong></div><div><small>Priority</small><strong id="taskDetailPriority"></strong></div><div><small>Estimated</small><strong id="taskDetailEstimated"></strong></div><div><small>Status</small><strong id="taskDetailStatus"></strong></div></div><hr><h5>Description</h5><p id="taskDetailDescription"></p><h5>Attachments</h5><div id="taskDetailAttachments" class="task-attachment-list"></div></div></div></div></div>
<script>
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.js-task-detail').forEach(function(btn){
    btn.addEventListener('click',function(){
      document.getElementById('taskDetailTitle').textContent=btn.dataset.title||'Task Details';
      document.getElementById('taskDetailProject').textContent=btn.dataset.project||'-';
      document.getElementById('taskDetailClient').textContent=btn.dataset.client||'-';
      document.getElementById('taskDetailOwner').textContent=btn.dataset.owner||'-';
      document.getElementById('taskDetailQa').textContent=btn.dataset.qa||'-';
      document.getElementById('taskDetailStart').textContent=btn.dataset.start||'-';
      document.getElementById('taskDetailDue').textContent=btn.dataset.due||'-';
      document.getElementById('taskDetailPriority').textContent=btn.dataset.priority||'-';
      document.getElementById('taskDetailEstimated').textContent=btn.dataset.estimated||'-';
      document.getElementById('taskDetailStatus').textContent=btn.dataset.status||'-';
      document.getElementById('taskDetailDescription').textContent=btn.dataset.description||'No description added.';
      var box=document.getElementById('taskDetailAttachments'); box.innerHTML='';
      var items=(btn.dataset.attachments||'').split('||').filter(Boolean);
      if(!items.length){box.innerHTML='<span class="text-muted">No attachment.</span>';} else {
        items.forEach(function(item){var parts=item.split(':'); var id=parts.shift(); var name=parts.join(':'); var a=document.createElement('a'); a.className='btn btn-default btn-sm'; a.href='taskAttachment.php?id='+encodeURIComponent(id); a.innerHTML='<i class="fa fa-paperclip"></i> '; a.appendChild(document.createTextNode(name)); box.appendChild(a);});
      }
      jQuery('#taskDetailModal').modal('show');
    });
  });
});
</script>
<?php include 'footer.php'; ?>
