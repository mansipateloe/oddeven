<?php
$active_menu = 'digital_assets';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'subscriptions', 'view');
$companyId = oecrm_current_company_id($conn);
$rows = mysqli_query($conn, 'SELECT * FROM digital_subscriptions WHERE company_id=' . (int) $companyId . ' ORDER BY renewal_date IS NULL,renewal_date');
$stats = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total,SUM(status="active") active,SUM(status="active" AND renewal_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 30 DAY)) due,SUM(status="active" AND renewal_date<CURDATE()) expired FROM digital_subscriptions WHERE company_id=' . (int) $companyId));
$flash = $_SESSION['subscription_flash'] ?? '';
unset($_SESSION['subscription_flash']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="resource-summary">
        <div><i class="fa fa-cloud"></i><span><small>Total Subscriptions</small><strong><?php echo (int) $stats['total']; ?></strong></span></div>
        <div><i class="fa fa-check"></i><span><small>Active</small><strong><?php echo (int) $stats['active']; ?></strong></span></div>
        <div><i class="fa fa-clock-o"></i><span><small>Due in 30 Days</small><strong><?php echo (int) $stats['due']; ?></strong></span></div>
        <div><i class="fa fa-exclamation"></i><span><small>Expired</small><strong><?php echo (int) $stats['expired']; ?></strong></span></div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading"><span>Digital Assets & Subscriptions</span><a href="subscriptionEditor.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Subscription</a></div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead><tr><th>Name</th><th>Type</th><th>Provider</th><th>Renewal</th><th>Cost</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($rows) === 0): ?><tr><td colspan="7" class="empty-cell">No subscriptions found.</td></tr><?php endif; ?>
                <?php while ($row = mysqli_fetch_assoc($rows)): ?>
                    <tr>
                        <td><strong><?php echo oecrm_h($row['name']); ?></strong><small><?php echo oecrm_h($row['username']); ?></small></td>
                        <td><?php echo ucwords(str_replace('_', ' ', $row['asset_type'])); ?></td>
                        <td><?php echo oecrm_h($row['provider']); ?></td>
                        <td><?php echo $row['renewal_date'] ? date('d M Y', strtotime($row['renewal_date'])) : '-'; ?></td>
                        <td><?php echo oecrm_h($row['currency_code']) . ' ' . number_format($row['cost'], 2); ?></td>
                        <td><span class="client-status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <td>
                            <a class="icon-action" href="subscriptionEditor.php?id=<?php echo (int) $row['id']; ?>"><i class="fa fa-arrow-right"></i></a>
                            <?php if ($row['status'] !== 'cancelled' && oecrm_can($conn, 'subscriptions', 'delete')): ?>
                                <form method="post" action="subscriptionAction.php" style="display:inline" data-confirm="Cancel this subscription?">
                                    <?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="cancel"><input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                    <button class="icon-action danger" title="Cancel"><i class="fa fa-trash"></i></button>
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
<?php include 'footer.php'; ?>
