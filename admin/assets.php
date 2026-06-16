<?php
$active_menu = 'assets';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'assets', 'view');

$companyId = oecrm_current_company_id($conn);
$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$where = 'a.company_id=?';
$types = 'i';
$params = [$companyId];
if (in_array($status, ['available', 'allocated', 'repair', 'retired', 'lost'], true)) {
    $where .= ' AND a.lifecycle_status=?';
    $types .= 's';
    $params[] = $status;
}
if ($q !== '') {
    $where .= ' AND (a.asset_code LIKE ? OR a.serial_number LIKE ? OR a.brand LIKE ? OR a.model LIKE ?)';
    $like = '%' . $q . '%';
    $types .= 'ssss';
    array_push($params, $like, $like, $like, $like);
}
$sql = 'SELECT a.*,e.name employee_name,e.employeeCode
        FROM assets a
        LEFT JOIN asset_allocations al ON al.asset_id=a.id AND al.status="allocated"
        LEFT JOIN employeestbl e ON e.id=al.employee_id
        WHERE ' . $where . '
        ORDER BY FIELD(a.lifecycle_status,"allocated","available","repair","lost","retired"),a.asset_code';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$rows = mysqli_stmt_get_result($stmt);
$stats = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total,SUM(lifecycle_status="available") available,SUM(lifecycle_status="allocated") allocated,SUM(lifecycle_status="repair") repair FROM assets WHERE company_id=' . (int) $companyId));
$flash = $_SESSION['asset_flash'] ?? '';
$error = $_SESSION['asset_error'] ?? '';
unset($_SESSION['asset_flash'], $_SESSION['asset_error']);
?>
<div id="page-wrapper" class="compact-admin-page asset-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
    <div class="resource-summary">
        <div><i class="fa fa-laptop"></i><span><small>Total Assets</small><strong><?php echo (int) $stats['total']; ?></strong></span></div>
        <div><i class="fa fa-cubes"></i><span><small>Available</small><strong><?php echo (int) $stats['available']; ?></strong></span></div>
        <div><i class="fa fa-user"></i><span><small>Allocated</small><strong><?php echo (int) $stats['allocated']; ?></strong></span></div>
        <div><i class="fa fa-wrench"></i><span><small>Under Repair</small><strong><?php echo (int) $stats['repair']; ?></strong></span></div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading"><span>Asset Register</span><a class="btn btn-primary btn-sm" href="assetEditor.php"><i class="fa fa-plus"></i> Add Asset</a></div>
        <div class="panel-body">
            <form method="get" class="client-filters">
                <input class="form-control" name="q" value="<?php echo oecrm_h($q); ?>" placeholder="Search code, serial, brand or model">
                <select class="form-control" name="status"><option value="">All statuses</option><?php foreach (['available', 'allocated', 'repair', 'retired', 'lost'] as $value): ?><option value="<?php echo $value; ?>" <?php echo $status === $value ? 'selected' : ''; ?>><?php echo ucfirst($value); ?></option><?php endforeach; ?></select>
                <button class="btn btn-default"><i class="fa fa-search"></i> Search</button>
            </form>
            <div class="table-responsive">
                <table class="table foundation-table">
                    <thead><tr><th>Asset</th><th>Type</th><th>Serial Number</th><th>Condition</th><th>Assigned To</th><th>Warranty</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php if (mysqli_num_rows($rows) === 0): ?><tr><td colspan="8" class="empty-cell">No assets found.</td></tr><?php endif; ?>
                    <?php while ($asset = mysqli_fetch_assoc($rows)): ?>
                        <tr>
                            <td><strong><?php echo oecrm_h($asset['asset_code']); ?></strong><small><?php echo oecrm_h(trim($asset['brand'] . ' ' . $asset['model'])); ?></small></td>
                            <td><?php echo ucfirst($asset['asset_type']); ?></td>
                            <td><?php echo oecrm_h($asset['serial_number'] ?: '-'); ?></td>
                            <td><?php echo ucfirst($asset['condition_status']); ?></td>
                            <td><?php echo oecrm_h($asset['employee_name'] ?: '-'); ?><small><?php echo oecrm_h($asset['employeeCode']); ?></small></td>
                            <td><?php echo $asset['warranty_until'] ? date('d M Y', strtotime($asset['warranty_until'])) : '-'; ?></td>
                            <td><span class="client-status <?php echo $asset['lifecycle_status']; ?>"><?php echo ucfirst($asset['lifecycle_status']); ?></span></td>
                            <td>
                                <a class="icon-action" href="assetEditor.php?id=<?php echo (int) $asset['id']; ?>" title="Open asset"><i class="fa fa-arrow-right"></i></a>
                                <?php if ($asset['lifecycle_status'] !== 'retired' && oecrm_can($conn, 'assets', 'delete')): ?>
                                    <form method="post" action="assetAction.php" style="display:inline" data-confirm="Retire this asset? Active allocation will be returned and history will remain.">
                                        <?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="retire"><input type="hidden" name="id" value="<?php echo (int) $asset['id']; ?>">
                                        <button class="icon-action danger" title="Retire"><i class="fa fa-trash"></i></button>
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
