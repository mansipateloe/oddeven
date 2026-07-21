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
$dueCount = 0;
$result = mysqli_query($conn, 'SELECT id,employeeCode,name,designation,joiningDate,salary,companyEmail,personalEmail,status FROM employeestbl WHERE company_id=' . (int) $companyId . ' AND status=0 ORDER BY name');
while ($row = mysqli_fetch_assoc($result)) {
    $latest = oecrm_employee_latest_continuation($conn, (int) $row['id'], $companyId);
    $row['latest'] = $latest;
    $row['term'] = oecrm_employee_continuation_term($row, $latest);
    if (!empty($row['term']['is_due'])) {
        $dueCount++;
    }
    $employees[] = $row;
}

$historyCountRow = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM employee_continuation_history WHERE company_id=' . (int) $companyId));
$historyCount = (int) ($historyCountRow['total'] ?? 0);

$historySql = 'SELECT h.*,e.employeeCode,e.name,e.designation,l.reference_no,l.status letter_status FROM employee_continuation_history h JOIN employeestbl e ON e.id=h.employee_id LEFT JOIN hr_letters l ON l.id=h.letter_id WHERE h.company_id=? ORDER BY h.id DESC LIMIT 300';
$stmt = mysqli_prepare($conn, $historySql);
mysqli_stmt_bind_param($stmt, 'i', $companyId);
mysqli_stmt_execute($stmt);
$history = mysqli_stmt_get_result($stmt);
?>
<style>
.continuation-page{background:#f5f7fb;min-height:100vh;padding-bottom:36px}
.continuation-hero{background:linear-gradient(135deg,#5447ff 0%,#7c5cff 48%,#25c6da 100%);border-radius:22px;color:#fff;padding:24px 28px;margin-bottom:22px;box-shadow:0 18px 42px rgba(84,71,255,.22);display:flex;align-items:center;justify-content:space-between;gap:16px;overflow:hidden;position:relative}
.continuation-hero:after{content:"";position:absolute;right:-80px;top:-80px;width:220px;height:220px;background:rgba(255,255,255,.12);border-radius:50%}
.continuation-hero small{display:block;color:rgba(255,255,255,.78);font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px}
.continuation-hero h2{margin:0 0 7px;font-size:28px;font-weight:800;color:#fff}
.continuation-hero p{margin:0;color:rgba(255,255,255,.86)}
.continuation-hero .btn{background:#fff;color:#5447ff;border:0;border-radius:13px;font-weight:800;padding:11px 16px;box-shadow:0 8px 22px rgba(35,42,63,.16);position:relative;z-index:1}
.continuation-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:20px}
.continuation-stat{background:#fff;border:1px solid #e8ecf5;border-radius:18px;padding:18px;box-shadow:0 10px 28px rgba(35,42,63,.07);display:flex;align-items:center;gap:14px}
.continuation-stat .icon{width:48px;height:48px;border-radius:15px;display:grid;place-items:center;color:#fff;font-size:20px;background:#635bff;box-shadow:0 9px 22px rgba(99,91,255,.2)}
.continuation-stat.warning .icon{background:#ff9f43}.continuation-stat.success .icon{background:#00b894}
.continuation-stat small{display:block;color:#8a93a8;font-weight:700;text-transform:uppercase;font-size:11px;letter-spacing:.06em}.continuation-stat strong{font-size:24px;color:#20263a;line-height:1}
.continuation-card{background:#fff;border:1px solid #e8ecf5;border-radius:20px;box-shadow:0 12px 32px rgba(35,42,63,.08);margin-bottom:20px;overflow:hidden}
.continuation-card-head{padding:19px 22px;border-bottom:1px solid #edf0f7;display:flex;align-items:center;justify-content:space-between;gap:12px;background:#fff}
.continuation-card-head h3{margin:0;color:#20263a;font-size:19px;font-weight:800}.continuation-card-head small{color:#8a93a8;font-weight:600}.continuation-card-body{padding:22px}
.continuation-form-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:16px}.continuation-form-grid.two{grid-template-columns:1fr 1fr 1fr}.continuation-form .form-group{margin-bottom:16px}.continuation-form label{font-weight:800;color:#28304a;margin-bottom:7px}.continuation-form .form-control{height:42px;border-radius:11px;border:1px solid #dbe1ef;box-shadow:none}.continuation-form textarea.form-control{height:auto;min-height:96px}.continuation-check{height:42px!important;border-radius:11px!important;display:flex;align-items:center;gap:8px;background:#f8f9ff!important;color:#34405f}.continuation-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}.continuation-actions .btn{border-radius:11px;font-weight:800;padding:10px 16px}
.continuation-table{margin:0}.continuation-table thead th{background:#f7f8fc!important;border-bottom:1px solid #e4e8f2!important;color:#66708b;text-transform:uppercase;font-size:11px;letter-spacing:.06em;font-weight:800;padding:14px!important}.continuation-table tbody td{vertical-align:middle!important;padding:15px!important;border-top:1px solid #eef1f7!important;color:#34405f}.continuation-table strong{display:block;color:#20263a;font-weight:800}.continuation-table small{display:block;color:#8a93a8;margin-top:3px}.term-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:800}.term-pill.running{background:#e8f4ff;color:#2476d2}.term-pill.due{background:#fff3df;color:#d57800}.term-pill.done{background:#e9fbf2;color:#128c53}.period-box{background:#f8f9fc;border:1px solid #edf0f7;border-radius:12px;padding:9px 11px;display:inline-block;min-width:170px}.letter-link{border-radius:10px!important;font-weight:800}.empty-cell{padding:26px!important;color:#8a93a8!important;text-align:center!important}
@media(max-width:991px){.continuation-hero{align-items:flex-start;flex-direction:column}.continuation-stats{grid-template-columns:1fr}.continuation-form-grid,.continuation-form-grid.two{grid-template-columns:1fr}}
</style>
<div id="page-wrapper" class="compact-admin-page continuation-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>

    <div class="continuation-hero">
        <div>
            <small>People & HR / Renewal</small>
            <h2>Employee Continuation & Renewal</h2>
            <p>Manage yearly employment continuation, renewal history and continuation letters from one place.</p>
        </div>
        <a class="btn" href="hrLetters.php?type=continuation"><i class="fa fa-file-text-o"></i> Continuation Letters</a>
    </div>

    <div class="continuation-stats">
        <div class="continuation-stat"><span class="icon"><i class="fa fa-users"></i></span><div><small>Active Employees</small><strong><?php echo count($employees); ?></strong></div></div>
        <div class="continuation-stat warning"><span class="icon"><i class="fa fa-clock-o"></i></span><div><small>Due For Renewal</small><strong><?php echo $dueCount; ?></strong></div></div>
        <div class="continuation-stat success"><span class="icon"><i class="fa fa-history"></i></span><div><small>History Records</small><strong><?php echo $historyCount; ?></strong></div></div>
    </div>

    <div class="continuation-card" id="renewForm">
        <div class="continuation-card-head"><div><h3>Renew / Continue Employee</h3><small>Dates auto-fill with the next yearly term; edit if HR needs a custom period.</small></div></div>
        <div class="continuation-card-body">
            <form method="post" action="employeeContinuationAction.php" class="continuation-form">
                <?php echo oecrm_csrf_field(); ?>
                <div class="continuation-form-grid">
                    <div class="form-group">
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
                    <div class="form-group"><label>Renewal Date</label><input type="date" name="renewal_date" class="form-control" value="<?php echo date('Y-m-d'); ?>"></div>
                    <div class="form-group"><label>New Period From</label><input type="date" name="renewed_from" id="renewedFrom" class="form-control"></div>
                    <div class="form-group"><label>New Period To</label><input type="date" name="renewed_to" id="renewedTo" class="form-control"></div>
                </div>
                <div class="continuation-form-grid two">
                    <div class="form-group"><label>New Designation</label><input name="new_designation" class="form-control" placeholder="Leave blank if unchanged"></div>
                    <div class="form-group"><label>Revised Salary</label><input name="revised_salary" class="form-control" placeholder="Leave blank if unchanged"></div>
                    <div class="form-group"><label>Letter Draft</label><label class="form-control continuation-check"><input type="checkbox" name="create_letter" value="1" checked> Generate continuation letter</label></div>
                </div>
                <div class="form-group"><label>Remarks</label><textarea name="remarks" class="form-control" rows="3" placeholder="Renewal notes / HR remarks"></textarea></div>
                <div class="continuation-actions"><button class="btn btn-primary"><i class="fa fa-refresh"></i> Continue Employee</button><a href="manageEmployee.php" class="btn btn-default">Cancel</a></div>
            </form>
        </div>
    </div>

    <div class="continuation-card">
        <div class="continuation-card-head"><div><h3>Renewal Due / Current Terms</h3><small>Active employees with current and next employment term.</small></div></div>
        <div class="table-responsive">
            <table class="table continuation-table">
                <thead><tr><th>Employee</th><th>Joining</th><th>Current Term</th><th>Next Term</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php if (!$employees): ?><tr><td colspan="6" class="empty-cell">No active employees found.</td></tr><?php endif; ?>
                <?php foreach ($employees as $employee): $term = $employee['term']; ?>
                    <tr>
                        <td><strong><?php echo oecrm_h($employee['name']); ?></strong><small><?php echo oecrm_h($employee['employeeCode'] . ' / ' . $employee['designation']); ?></small></td>
                        <td><?php echo oecrm_continuation_format_date($employee['joiningDate']); ?></td>
                        <td><span class="period-box"><strong>Year <?php echo (int) $term['current_term_no']; ?></strong><small><?php echo oecrm_continuation_format_date($term['current_from']) . ' to ' . oecrm_continuation_format_date($term['current_to']); ?></small></span></td>
                        <td><span class="period-box"><strong>Year <?php echo (int) $term['next_term_no']; ?></strong><small><?php echo oecrm_continuation_format_date($term['next_from']) . ' to ' . oecrm_continuation_format_date($term['next_to']); ?></small></span></td>
                        <td><span class="term-pill <?php echo $term['is_due'] ? 'due' : 'running'; ?>"><i class="fa <?php echo $term['is_due'] ? 'fa-exclamation-circle' : 'fa-play-circle'; ?>"></i><?php echo $term['is_due'] ? 'Due for renewal' : 'Running'; ?></span></td>
                        <td><a class="btn btn-xs btn-primary" href="employeeContinuations.php?employee_id=<?php echo (int) $employee['id']; ?>#renewForm"><i class="fa fa-refresh"></i> Renew</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="continuation-card">
        <div class="continuation-card-head"><div><h3>Continuation History</h3><small>Every renewal is preserved year-wise with generated letter reference.</small></div></div>
        <div class="table-responsive">
            <table class="table continuation-table" id="continuationHistoryTable">
                <thead><tr><th>Employee</th><th>Term</th><th>Previous Period</th><th>Renewed Period</th><th>Renewal Date</th><th>Letter</th><th>Remarks</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($history) === 0): ?><tr><td colspan="7" class="empty-cell">No continuation history yet.</td></tr><?php endif; ?>
                <?php while ($row = mysqli_fetch_assoc($history)): ?>
                    <tr>
                        <td><strong><?php echo oecrm_h($row['name']); ?></strong><small><?php echo oecrm_h($row['employeeCode'] . ' / ' . $row['designation']); ?></small></td>
                        <td><span class="term-pill done"><i class="fa fa-check-circle"></i>Year <?php echo (int) $row['term_no']; ?></span><small><?php echo ucfirst($row['status']); ?></small></td>
                        <td><span class="period-box"><?php echo oecrm_continuation_format_date($row['previous_from']) . ' to ' . oecrm_continuation_format_date($row['previous_to']); ?></span></td>
                        <td><span class="period-box"><?php echo oecrm_continuation_format_date($row['renewed_from']) . ' to ' . oecrm_continuation_format_date($row['renewed_to']); ?></span></td>
                        <td><?php echo oecrm_continuation_format_date($row['renewal_date']); ?></td>
                        <td><?php if ($row['letter_id']): ?><a target="_blank" class="btn btn-xs btn-default letter-link" href="hrLetterView.php?id=<?php echo (int) $row['letter_id']; ?>"><i class="fa fa-file-text-o"></i> <?php echo oecrm_h($row['reference_no']); ?></a><?php else: ?>-<?php endif; ?></td>
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
