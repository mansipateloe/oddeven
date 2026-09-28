<?php
$active_menu = 'employee';
$active_submenu = 'employee_continuations';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../hr_letters.php';
require_once __DIR__ . '/../employee_continuations.php';

oecrm_require_permission($conn, 'employees', 'view');
$companyId = oecrm_current_company_id($conn);
$actor = (int) ($_SESSION['adminId'] ?? 0);
oecrm_ensure_employee_continuation_tables($conn);
if (oecrm_can($conn, 'hr_letter_templates', 'create') || oecrm_can($conn, 'hr_letters', 'create')) {
    oecrm_ensure_continuation_letter_template($conn, $companyId, $actor);
}

$selectedEmployeeId = (int) ($_GET['employee_id'] ?? 0);
$flash = $_SESSION['continuation_flash'] ?? '';
$error = $_SESSION['continuation_error'] ?? '';
unset($_SESSION['continuation_flash'], $_SESSION['continuation_error']);

$employees = [];
$result = mysqli_query($conn, 'SELECT id,employeeCode,name,designation,joiningDate,salary,companyEmail,personalEmail,status FROM employeestbl WHERE company_id=' . (int) $companyId . ' AND status=0 ORDER BY name');
while ($row = mysqli_fetch_assoc($result)) {
    $latest = oecrm_employee_latest_continuation($conn, (int) $row['id'], $companyId);
    $row['latest'] = $latest;
    $row['term'] = oecrm_employee_continuation_term($row, $latest);
    $employees[] = $row;
}

$historySql = 'SELECT h.*,e.employeeCode,e.name,e.designation,l.reference_no,l.status letter_status FROM employee_continuation_history h JOIN employeestbl e ON e.id=h.employee_id LEFT JOIN hr_letters l ON l.id=h.letter_id WHERE h.company_id=? ORDER BY h.id DESC LIMIT 300';
$stmt = mysqli_prepare($conn, $historySql);
mysqli_stmt_bind_param($stmt, 'i', $companyId);
mysqli_stmt_execute($stmt);
$history = mysqli_stmt_get_result($stmt);
?>
<div id="page-wrapper" class="compact-admin-page continuation-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>

    <div class="foundation-titlebar">
        <div>
            <small>People & HR / Employee Continuations</small>
            <h2>Employee Continuation & Renewal</h2>
            <p>Yearly continuation history and renewal letter draft management.</p>
        </div>
        <a class="btn btn-default" href="hrLetters.php?type=continuation"><i class="fa fa-file-text-o"></i> Continuation Letters</a>
    </div>

    <div class="panel panel-default" id="renewForm">
        <div class="panel-heading foundation-heading"><span>Renew / Continue Employee</span><small>Blank dates use next yearly term automatically</small></div>
        <div class="panel-body">
            <form method="post" action="employeeContinuationAction.php" class="resource-form continuation-form">
                <?php echo oecrm_csrf_field(); ?>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Employee</label>
                        <select name="employee_id" id="continuationEmployee" class="form-control" required>
                            <option value="">Select employee</option>
                            <?php foreach ($employees as $employee): $term = $employee['term']; ?>
                                <option value="<?php echo (int) $employee['id']; ?>" data-from="<?php echo oecrm_h($term['next_from']); ?>" data-to="<?php echo oecrm_h($term['next_to']); ?>" <?php echo $selectedEmployeeId === (int) $employee['id'] ? 'selected' : ''; ?>>
                                    <?php echo oecrm_h($employee['employeeCode'] . ' - ' . $employee['name'] . ' - ' . $employee['designation']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 form-group"><label>Renewal Date</label><input type="date" name="renewal_date" class="form-control" value="<?php echo date('Y-m-d'); ?>"></div>
                    <div class="col-md-3 form-group"><label>New Period From</label><input type="date" name="renewed_from" id="renewedFrom" class="form-control"></div>
                    <div class="col-md-3 form-group"><label>New Period To</label><input type="date" name="renewed_to" id="renewedTo" class="form-control"></div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group"><label>New Designation</label><input name="new_designation" class="form-control" placeholder="Leave blank if unchanged"></div>
                    <div class="col-md-4 form-group"><label>Revised Salary</label><input name="revised_salary" class="form-control" placeholder="Leave blank if unchanged"></div>
                    <div class="col-md-4 form-group"><label>Create Letter Draft</label><label class="form-control" style="font-weight:400"><input type="checkbox" name="create_letter" value="1" checked> Generate continuation letter draft</label></div>
                </div>
                <div class="form-group"><label>Remarks</label><textarea name="remarks" class="form-control" rows="3" placeholder="Renewal notes / HR remarks"></textarea></div>
                <button class="btn btn-primary"><i class="fa fa-refresh"></i> Continue Employee</button>
                <a href="manageEmployee.php" class="btn btn-default">Cancel</a>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading foundation-heading"><span>Renewal Due / Current Terms</span><small>Active employees only</small></div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead><tr><th>Employee</th><th>Joining</th><th>Current Term</th><th>Next Term</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php if (!$employees): ?><tr><td colspan="6" class="empty-cell">No active employees found.</td></tr><?php endif; ?>
                <?php foreach ($employees as $employee): $term = $employee['term']; ?>
                    <tr>
                        <td><strong><?php echo oecrm_h($employee['name']); ?></strong><small><?php echo oecrm_h($employee['employeeCode'] . ' / ' . $employee['designation']); ?></small></td>
                        <td><?php echo oecrm_continuation_format_date($employee['joiningDate']); ?></td>
                        <td>Year <?php echo (int) $term['current_term_no']; ?><small><?php echo oecrm_continuation_format_date($term['current_from']) . ' to ' . oecrm_continuation_format_date($term['current_to']); ?></small></td>
                        <td>Year <?php echo (int) $term['next_term_no']; ?><small><?php echo oecrm_continuation_format_date($term['next_from']) . ' to ' . oecrm_continuation_format_date($term['next_to']); ?></small></td>
                        <td><span class="label <?php echo $term['is_due'] ? 'label-warning' : 'label-info'; ?>"><?php echo $term['is_due'] ? 'Due for renewal' : 'Running'; ?></span></td>
                        <td><a class="btn btn-xs btn-primary" href="employeeContinuations.php?employee_id=<?php echo (int) $employee['id']; ?>#renewForm"><i class="fa fa-refresh"></i> Renew</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading foundation-heading"><span>Continuation History</span><small>Every renewal is preserved year-wise</small></div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table" id="continuationHistoryTable">
                <thead><tr><th>Employee</th><th>Term</th><th>Previous Period</th><th>Renewed Period</th><th>Renewal Date</th><th>Letter</th><th>Remarks</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($history) === 0): ?><tr><td colspan="7" class="empty-cell">No continuation history yet.</td></tr><?php endif; ?>
                <?php while ($row = mysqli_fetch_assoc($history)): ?>
                    <tr>
                        <td><strong><?php echo oecrm_h($row['name']); ?></strong><small><?php echo oecrm_h($row['employeeCode'] . ' / ' . $row['designation']); ?></small></td>
                        <td>Year <?php echo (int) $row['term_no']; ?><small><?php echo ucfirst($row['status']); ?></small></td>
                        <td><?php echo oecrm_continuation_format_date($row['previous_from']) . ' to ' . oecrm_continuation_format_date($row['previous_to']); ?></td>
                        <td><?php echo oecrm_continuation_format_date($row['renewed_from']) . ' to ' . oecrm_continuation_format_date($row['renewed_to']); ?></td>
                        <td><?php echo oecrm_continuation_format_date($row['renewal_date']); ?></td>
                        <td><?php if ($row['letter_id']): ?><a target="_blank" class="btn btn-xs btn-default" href="hrLetterView.php?id=<?php echo (int) $row['letter_id']; ?>"><i class="fa fa-file-text-o"></i> <?php echo oecrm_h($row['reference_no']); ?></a><?php else: ?>-<?php endif; ?></td>
                        <td><?php echo oecrm_h($row['remarks']); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
(function(){
  var select = document.getElementById('continuationEmployee'), from = document.getElementById('renewedFrom'), to = document.getElementById('renewedTo');
  function fillDates(){var option=select.options[select.selectedIndex]; if(!option){return;} if(!from.value){from.value=option.dataset.from||'';} if(!to.value){to.value=option.dataset.to||'';}}
  if(select){select.addEventListener('change', function(){from.value='';to.value='';fillDates();}); fillDates();}
})();
</script>
<?php include 'footer.php'; ?>
