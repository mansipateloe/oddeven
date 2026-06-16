<?php
$active_menu = 'employee_exit';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'employee_exit', 'view');
$companyId = oecrm_current_company_id($conn);
$rows = mysqli_query($conn, 'SELECT x.*,e.name,e.employeeCode,c.display_name company_name FROM employee_exits x JOIN employeestbl e ON e.id=x.employee_id LEFT JOIN companies c ON c.id=x.company_id WHERE x.company_id=' . (int) $companyId . ' ORDER BY x.id DESC');
$employees = mysqli_query($conn, 'SELECT e.id,e.employeeCode,e.name,c.display_name company_name FROM employeestbl e LEFT JOIN companies c ON c.id=e.company_id WHERE e.status=0 ORDER BY c.display_name,e.name');
$flash = $_SESSION['exit_flash'] ?? '';
unset($_SESSION['exit_flash']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading">Initiate Employee Exit</div>
        <div class="panel-body">
            <form method="post" action="exitAction.php" class="exit-init-form">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="action" value="initiate">
                <select class="form-control exit-native-select" name="employee_id" data-oecrm-native="1" required>
                    <option value="">Select active employee</option>
                    <?php while ($employee = mysqli_fetch_assoc($employees)): ?>
                        <option value="<?php echo (int) $employee['id']; ?>"><?php echo oecrm_h(($employee['company_name'] ? $employee['company_name'] . ' - ' : '') . $employee['employeeCode'] . ' - ' . $employee['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <input class="form-control" type="date" name="last_working_date" required>
                <select class="form-control exit-native-select" name="exit_type" data-oecrm-native="1"><?php foreach (['resignation', 'termination', 'retirement', 'other'] as $value): ?><option value="<?php echo $value; ?>"><?php echo ucfirst($value); ?></option><?php endforeach; ?></select>
                <button class="btn btn-primary">Initiate Exit</button>
                <a class="btn btn-default" href="employeeExits.php">Cancel</a>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Exit & Full and Final Settlement</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead><tr><th>Employee</th><th>Last Working Date</th><th>Type</th><th>Clearance</th><th>Final Settlement</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($rows) === 0): ?><tr><td colspan="7" class="empty-cell">No exit processes found.</td></tr><?php endif; ?>
                <?php while ($row = mysqli_fetch_assoc($rows)): $pending = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM exit_checklist WHERE exit_id=' . (int) $row['id'] . ' AND status="pending"')); ?>
                    <tr>
                        <td><strong><?php echo oecrm_h($row['name']); ?></strong><small><?php echo oecrm_h($row['employeeCode'] . ' | ' . ($row['company_name'] ?: '')); ?></small></td>
                        <td><?php echo date('d M Y', strtotime($row['last_working_date'])); ?></td>
                        <td><?php echo ucfirst($row['exit_type']); ?></td>
                        <td><?php echo (int) $pending['total']; ?> pending</td>
                        <td><?php echo number_format($row['final_settlement'], 2); ?></td>
                        <td><span class="client-status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <td><a class="icon-action" href="exitSettlement.php?id=<?php echo (int) $row['id']; ?>"><i class="fa fa-arrow-right"></i></a></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
