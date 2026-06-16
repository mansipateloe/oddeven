<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$employeeId = (int) $_SESSION['employeeId'];
$stmt = mysqli_prepare($conn, 'SELECT DISTINCT t.*,ta.is_primary,e.designation,p.projectName,co.display_name company_name
    FROM tasktbl t
    JOIN task_assignees ta ON ta.task_id=t.id
    JOIN employeestbl e ON e.id=ta.employee_id
    JOIN projectstbl p ON p.id=CAST(t.projectId AS UNSIGNED)
    LEFT JOIN companies co ON co.id=p.company_id
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
                <thead><tr><th>Project</th><th>Company</th><th>Task</th><th>Priority</th><th>Due</th><th>Estimated</th><th>Status</th><th>Update</th></tr></thead>
                <tbody>
                <?php if(mysqli_num_rows($tasks)===0): ?><tr><td colspan="8">No tasks assigned.</td></tr><?php endif; ?>
                <?php while($task=mysqli_fetch_assoc($tasks)): ?>
                    <tr>
                        <td><?php echo oecrm_h($task['projectName']); ?></td>
                        <td><?php echo oecrm_h($task['company_name'] ?: '-'); ?></td>
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
                    </tr>
                <?php endwhile; mysqli_stmt_close($stmt); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
