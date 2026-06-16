<?php
$active_menu = 'resources';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'resources', 'view');

$companyId = oecrm_current_company_id($conn);
$status = trim($_GET['status'] ?? '');
$q = trim($_GET['q'] ?? '');
$where = 'r.company_id=?';
$types = 'i';
$params = [$companyId];
if (in_array($status, ['planned', 'active', 'completed', 'cancelled'], true)) {
    $where .= ' AND r.status=?';
    $types .= 's';
    $params[] = $status;
}
if ($q !== '') {
    $where .= ' AND (e.name LIKE ? OR c.display_name LIKE ? OR p.projectName LIKE ?)';
    $like = '%' . $q . '%';
    $types .= 'sss';
    array_push($params, $like, $like, $like);
}
$sql = 'SELECT r.*,e.name employee_name,e.employeeCode,c.display_name client_name,p.projectName,
        (r.billing_rate-r.salary_cost) margin
        FROM resource_allocations r
        INNER JOIN employeestbl e ON e.id=r.employee_id
        INNER JOIN clients c ON c.id=r.client_id
        LEFT JOIN projectstbl p ON p.id=r.project_id
        WHERE ' . $where . '
        ORDER BY FIELD(r.status,"active","planned","completed","cancelled"),r.start_date DESC';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$rows = mysqli_stmt_get_result($stmt);
$stats = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total,SUM(status="active") active,COUNT(DISTINCT CASE WHEN status="active" THEN employee_id END) allocated,SUM(CASE WHEN status="active" THEN billing_rate-salary_cost ELSE 0 END) margin FROM resource_allocations WHERE company_id=' . (int) $companyId));
$employees = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM employeestbl WHERE company_id=' . (int) $companyId . ' AND status=0'));
$bench = max(0, (int) $employees['total'] - (int) $stats['allocated']);
$flash = $_SESSION['resource_flash'] ?? '';
$error = $_SESSION['resource_error'] ?? '';
unset($_SESSION['resource_flash'], $_SESSION['resource_error']);
?>
<div id="page-wrapper" class="compact-admin-page resource-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
    <div class="resource-summary">
        <div><i class="fa fa-users"></i><span><small>Active Allocations</small><strong><?php echo (int) $stats['active']; ?></strong></span></div>
        <div><i class="fa fa-user-circle"></i><span><small>Allocated Employees</small><strong><?php echo (int) $stats['allocated']; ?></strong></span></div>
        <div><i class="fa fa-coffee"></i><span><small>Bench Resources</small><strong><?php echo $bench; ?></strong></span></div>
        <div><i class="fa fa-line-chart"></i><span><small>Estimated Margin</small><strong><?php echo number_format((float) $stats['margin'], 2); ?></strong></span></div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading"><span>Resource Allocations</span><a class="btn btn-primary btn-sm" href="resourceAllocation.php"><i class="fa fa-plus"></i> New Allocation</a></div>
        <div class="panel-body">
            <form class="client-filters" method="get">
                <input class="form-control" name="q" value="<?php echo oecrm_h($q); ?>" placeholder="Search employee, client or project">
                <select class="form-control" name="status"><option value="">All statuses</option><?php foreach (['active', 'planned', 'completed', 'cancelled'] as $value): ?><option value="<?php echo $value; ?>" <?php echo $status === $value ? 'selected' : ''; ?>><?php echo ucfirst($value); ?></option><?php endforeach; ?></select>
                <button class="btn btn-default"><i class="fa fa-search"></i> Search</button>
            </form>
            <div class="table-responsive">
                <table class="table foundation-table">
                    <thead><tr><th>Resource</th><th>Client / Project</th><th>Allocation</th><th>Period</th><th>Billing</th><th>Cost</th><th>Margin</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php if (mysqli_num_rows($rows) === 0): ?><tr><td colspan="9" class="empty-cell">No resource allocations found.</td></tr><?php endif; ?>
                    <?php while ($row = mysqli_fetch_assoc($rows)): ?>
                        <tr>
                            <td><strong><?php echo oecrm_h($row['employee_name']); ?></strong><small><?php echo oecrm_h($row['employeeCode']); ?></small></td>
                            <td><?php echo oecrm_h($row['client_name']); ?><small><?php echo oecrm_h($row['projectName'] ?: 'General allocation'); ?></small></td>
                            <td><?php echo ucwords(str_replace('_', ' ', $row['allocation_type'])); ?><small><?php echo number_format($row['allocation_percent'], 0); ?>%</small></td>
                            <td><?php echo date('d M Y', strtotime($row['start_date'])); ?><small><?php echo $row['end_date'] ? 'to ' . date('d M Y', strtotime($row['end_date'])) : 'Ongoing'; ?></small></td>
                            <td><?php echo oecrm_h($row['currency_code']) . ' ' . number_format($row['billing_rate'], 2); ?><small><?php echo ucfirst($row['billing_cycle']); ?></small></td>
                            <td><?php echo number_format($row['salary_cost'], 2); ?></td>
                            <td class="<?php echo $row['margin'] < 0 ? 'text-danger' : 'text-success'; ?>"><?php echo number_format($row['margin'], 2); ?></td>
                            <td><span class="client-status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                            <td>
                                <a class="icon-action" href="resourceAllocation.php?id=<?php echo (int) $row['id']; ?>" title="Edit allocation"><i class="fa fa-pencil"></i></a>
                                <?php if ($row['status'] !== 'cancelled' && oecrm_can($conn, 'resources', 'delete')): ?>
                                    <form method="post" action="resourceAction.php" style="display:inline" data-confirm="Cancel this resource allocation?">
                                        <?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="cancel"><input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                        <button class="icon-action danger" title="Cancel allocation"><i class="fa fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
