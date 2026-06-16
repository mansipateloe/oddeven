<?php
$active_menu = 'payroll';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'salary_structures', 'view');
$companyId = oecrm_current_company_id($conn);
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
    oecrm_require_permission($conn, 'salary_structures', 'edit');
    $id = (int) $_POST['id'];
    $employeeId = (int) $_POST['employee_id'];
    $mode = in_array($_POST['salary_mode'] ?? 'fixed', ['fixed', 'hourly'], true) ? $_POST['salary_mode'] : 'fixed';
    $gross = max(0, (float) $_POST['gross_salary']);
    $hourly = max(0, (float) ($_POST['hourly_rate'] ?? 0));
    $basic = max(0, (float) $_POST['basic_salary']);
    $hra = max(0, (float) $_POST['hra']);
    $special = max(0, (float) $_POST['special_allowance']);
    $other = max(0, (float) $_POST['other_allowance']);
    $professionalTax = isset($_POST['professional_tax_enabled']) ? 1 : 0;
    $overtimePolicy = ($_POST['overtime_policy'] ?? 'tracking_only') === 'paid' ? 'paid' : 'tracking_only';
    $overtimeRate = max(0, (float) ($_POST['overtime_rate'] ?? 0));
    $retentionType = in_array($_POST['retention_type'] ?? 'none', ['none', 'fixed', 'percent'], true) ? $_POST['retention_type'] : 'none';
    $retentionValue = max(0, (float) $_POST['retention_value']);
    $stmt = mysqli_prepare($conn, 'UPDATE salary_structures SET salary_mode=?,gross_salary=?,hourly_rate=?,basic_salary=?,hra=?,special_allowance=?,other_allowance=?,professional_tax_enabled=?,overtime_policy=?,overtime_rate=?,retention_type=?,retention_value=? WHERE id=? AND employee_id=? AND company_id=?');
    mysqli_stmt_bind_param($stmt, 'sddddddisdsdiii', $mode, $gross, $hourly, $basic, $hra, $special, $other, $professionalTax, $overtimePolicy, $overtimeRate, $retentionType, $retentionValue, $id, $employeeId, $companyId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    oecrm_audit($conn, 'salary_structures', 'update', 'salary_structure', $id, 'Salary policy updated', null, $_POST);
    $flash = 'Salary policy updated.';
}

$list = mysqli_query($conn, 'SELECT s.*,e.employeeCode,e.name,e.designation FROM salary_structures s JOIN employeestbl e ON e.id=s.employee_id WHERE s.company_id=' . (int) $companyId . ' AND s.status=1 ORDER BY e.name');
?>
<div id="page-wrapper" class="compact-admin-page payroll-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="panel panel-default"><div class="panel-heading">Employee Salary Policies</div><div class="panel-body"><div class="salary-structure-grid">
    <?php while ($structure=mysqli_fetch_assoc($list)): ?>
        <form method="post" class="salary-structure-card">
            <?php echo oecrm_csrf_field(); ?><input type="hidden" name="id" value="<?php echo (int)$structure['id']; ?>"><input type="hidden" name="employee_id" value="<?php echo (int)$structure['employee_id']; ?>">
            <header><div><strong><?php echo oecrm_h($structure['name']); ?></strong><small><?php echo oecrm_h($structure['employeeCode'].' - '.$structure['designation']); ?></small></div><span><?php echo strtoupper($structure['salary_mode']); ?></span></header>
            <div class="salary-fields">
                <div><label>Salary Mode</label><select class="form-control" name="salary_mode"><option value="fixed" <?php echo $structure['salary_mode']==='fixed'?'selected':''; ?>>Fixed Monthly</option><option value="hourly" <?php echo $structure['salary_mode']==='hourly'?'selected':''; ?>>Hourly</option></select></div>
                <div><label>Gross Monthly</label><input type="number" step=".01" class="form-control" name="gross_salary" value="<?php echo $structure['gross_salary']; ?>"></div>
                <div><label>Hourly Rate</label><input type="number" step=".01" class="form-control" name="hourly_rate" value="<?php echo $structure['hourly_rate']; ?>"></div>
                <div><label>Basic</label><input type="number" step=".01" class="form-control" name="basic_salary" value="<?php echo $structure['basic_salary']; ?>"></div>
                <div><label>HRA</label><input type="number" step=".01" class="form-control" name="hra" value="<?php echo $structure['hra']; ?>"></div>
                <div><label>Special</label><input type="number" step=".01" class="form-control" name="special_allowance" value="<?php echo $structure['special_allowance']; ?>"></div>
                <div><label>Other</label><input type="number" step=".01" class="form-control" name="other_allowance" value="<?php echo $structure['other_allowance']; ?>"></div>
                <div><label>Overtime Policy</label><select class="form-control" name="overtime_policy"><option value="tracking_only" <?php echo $structure['overtime_policy']==='tracking_only'?'selected':''; ?>>Tracking Only</option><option value="paid" <?php echo $structure['overtime_policy']==='paid'?'selected':''; ?>>Paid</option></select></div>
                <div><label>Overtime Rate / Hour</label><input type="number" step=".01" class="form-control" name="overtime_rate" value="<?php echo $structure['overtime_rate']; ?>"></div>
                <div><label>Retention</label><select class="form-control" name="retention_type"><?php foreach(['none','fixed','percent'] as $value): ?><option value="<?php echo $value; ?>" <?php echo $structure['retention_type']===$value?'selected':''; ?>><?php echo ucfirst($value); ?></option><?php endforeach; ?></select></div>
                <div><label>Retention Value</label><input type="number" step=".01" class="form-control" name="retention_value" value="<?php echo $structure['retention_value']; ?>"></div>
            </div>
            <footer><label><input type="checkbox" name="professional_tax_enabled" <?php echo $structure['professional_tax_enabled']?'checked':''; ?>> Professional Tax</label><button class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Save</button></footer>
        </form>
    <?php endwhile; ?>
    </div></div></div>
</div>
<?php include 'footer.php'; ?>
