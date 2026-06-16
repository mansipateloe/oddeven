<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$employeeId = (int) $_SESSION['employeeId'];
$projects = mysqli_query($conn, 'SELECT DISTINCT p.id,p.projectName,co.display_name company_name
    FROM projectstbl p
    LEFT JOIN project_team_members tm ON tm.project_id=p.id AND tm.employee_id=' . $employeeId . ' AND tm.left_at IS NULL
    LEFT JOIN tasktbl assigned_task ON CAST(assigned_task.projectId AS UNSIGNED)=p.id
    LEFT JOIN task_assignees assigned_to ON assigned_to.task_id=assigned_task.id AND assigned_to.employee_id=' . $employeeId . '
    LEFT JOIN companies co ON co.id=p.company_id
    WHERE tm.employee_id=' . $employeeId . ' OR assigned_to.employee_id=' . $employeeId . '
    ORDER BY p.projectName');
$tasks = mysqli_query($conn, 'SELECT DISTINCT t.id,CAST(t.projectId AS UNSIGNED) project_id,t.taskTitle,t.status
    FROM tasktbl t
    JOIN task_assignees ta ON ta.task_id=t.id
    WHERE ta.employee_id=' . $employeeId . ' AND t.status NOT IN ("2","cancel","cancelled")
    ORDER BY t.taskTitle');
$entries = mysqli_query($conn, 'SELECT t.*,p.projectName,co.display_name company_name,tk.taskTitle
    FROM timesheet_entries t
    JOIN projectstbl p ON p.id=t.project_id
    LEFT JOIN tasktbl tk ON tk.id=t.task_id
    LEFT JOIN companies co ON co.id=p.company_id
    WHERE t.employee_id=' . $employeeId . '
    ORDER BY work_date DESC,t.id DESC LIMIT 60');
$flash = $_SESSION['timesheet_flash'] ?? '';
$error = $_SESSION['timesheet_error'] ?? '';
unset($_SESSION['timesheet_flash'], $_SESSION['timesheet_error']);
?>
<div id="page-wrapper" class="employee-timesheet-page">
    <?php if($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
    <div class="panel panel-default employee-project-dropdown-panel">
        <div class="panel-heading"><span>Daily Timesheet</span><small>Record hours against an admin-assigned task</small></div>
        <div class="panel-body">
            <form method="post" action="timesheetSave.php" class="employee-timesheet-form">
                <?php echo oecrm_csrf_field(); ?>
                <div class="form-group"><label>Work Date</label><input class="form-control" type="date" name="work_date" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required></div>
                <div class="form-group"><label>Project</label><select class="form-control" name="project_id" data-placeholder="Select assigned project" required><option value="">Select assigned project</option><?php while($project=mysqli_fetch_assoc($projects)): ?><option value="<?php echo (int)$project['id']; ?>"><?php echo oecrm_h($project['projectName'].' - '.$project['company_name']); ?></option><?php endwhile; ?></select></div>
                <div class="form-group"><label>Assigned Task</label><select class="form-control" name="task_id" data-placeholder="Select assigned task" required><option value="">Select assigned task</option><?php while($task=mysqli_fetch_assoc($tasks)): ?><option value="<?php echo (int)$task['id']; ?>" data-project="<?php echo (int)$task['project_id']; ?>"><?php echo oecrm_h($task['taskTitle'].' - '.ucwords(str_replace('_',' ',$task['status']))); ?></option><?php endwhile; ?></select></div>
                <div class="form-group"><label>Total Hours</label><input class="form-control" type="number" min=".25" max="24" step=".25" name="hours" required></div>
                <div class="form-group"><label>Billable Hours</label><input class="form-control" type="number" min="0" max="24" step=".25" name="billable_hours" value="0"></div>
                <div class="form-group timesheet-worklog"><label>Work Description</label><textarea class="form-control" name="description" rows="3" required></textarea></div>
                <div class="timesheet-submit"><button class="btn btn-primary" name="save_mode" value="submitted"><i class="fa fa-paper-plane"></i> Submit for Approval</button><button class="btn btn-default" name="save_mode" value="draft"><i class="fa fa-save"></i> Save Draft</button></div>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Recent Entries</div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Date</th><th>Project</th><th>Task</th><th>Company</th><th>Description</th><th>Hours</th><th>Billable</th><th>Status</th></tr></thead>
                <tbody>
                <?php if(mysqli_num_rows($entries)===0): ?><tr><td colspan="8">No entries yet.</td></tr><?php endif; ?>
                <?php while($entry=mysqli_fetch_assoc($entries)): ?><tr><td><?php echo date('d M Y',strtotime($entry['work_date'])); ?></td><td><?php echo oecrm_h($entry['projectName']); ?></td><td><?php echo oecrm_h($entry['taskTitle']?:'-'); ?></td><td><?php echo oecrm_h($entry['company_name']); ?></td><td><?php echo oecrm_h($entry['description']); ?></td><td><?php echo number_format($entry['hours'],2); ?></td><td><?php echo number_format($entry['billable_hours'],2); ?></td><td><?php echo ucfirst($entry['status']); ?></td></tr><?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
    var project=document.querySelector('select[name="project_id"]');
    var task=document.querySelector('select[name="task_id"]');
    function filterTasks(){
        var selected=project.value;
        Array.prototype.forEach.call(task.options,function(option,index){
            if(index===0)return;
            option.hidden=!selected||option.dataset.project!==selected;
        });
        if(task.selectedOptions.length&&task.selectedOptions[0].hidden)task.value='';
        task.dispatchEvent(new Event('change',{bubbles:true}));
    }
    project.addEventListener('change',filterTasks);
    filterTasks();
});
</script>
<?php include 'footer.php'; ?>
