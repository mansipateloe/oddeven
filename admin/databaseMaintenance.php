<?php $active_menu = "setting";
include "header.php";
require_once __DIR__ . "/../foundation.php";
oecrm_require_permission($conn, "backups", "view");
$rows = mysqli_query($conn, "SELECT * FROM database_backups ORDER BY id DESC");
?><div id="page-wrapper" class="compact-admin-page"><div class="panel panel-default"><div class="panel-heading foundation-heading"><span>Database Backup Management</span><?php if (
    oecrm_can($conn, "backups", "create")
): ?><form method="post" action="databaseSnapshotAction.php"><?php echo oecrm_csrf_field(); ?><button class="btn btn-primary btn-sm"><i class="fa fa-database"></i> Create Backup</button></form><?php endif; ?></div><div class="panel-body table-responsive"><table class="table foundation-table"><thead><tr><th>Backup File</th><th>Created</th><th>Size</th><th>Status</th></tr></thead><tbody><?php
if (
    mysqli_num_rows($rows) === 0
): ?><tr><td colspan="4" class="empty-cell">No backups created.</td></tr><?php endif;
while ($r = mysqli_fetch_assoc($rows)): ?><tr><td><?php echo oecrm_h(
    $r["file_name"]
); ?></td><td><?php echo date(
    "d M Y H:i",
    strtotime($r["created_at"])
); ?></td><td><?php echo number_format(
    $r["file_size"] / 1024,
    1
); ?> KB</td><td><?php echo ucfirst($r["status"]); ?></td></tr><?php endwhile;
?></tbody></table></div></div></div><?php include "footer.php"; ?>
