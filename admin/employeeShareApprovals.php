<?php
$active_menu = 'notices';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'notices', 'view');
$companyId = oecrm_current_company_id($conn);
$flash = $_SESSION['employee_share_flash'] ?? '';
$error = $_SESSION['employee_share_error'] ?? '';
unset($_SESSION['employee_share_flash'], $_SESSION['employee_share_error']);

$statusFilter = $_GET['status'] ?? 'all';
$allowedStatusFilters = ['all', 'pending_review', 'published', 'rejected'];
if (!in_array($statusFilter, $allowedStatusFilters, true)) {
    $statusFilter = 'all';
}

$searchTerm = trim((string) ($_GET['search'] ?? ''));
$searchSql = '';
if ($searchTerm !== '') {
    $searchTermEscaped = strtolower(mysqli_real_escape_string($conn, $searchTerm));
    $searchSql = " AND (LOWER(COALESCE(e.name, '')) LIKE '%{$searchTermEscaped}%' OR LOWER(COALESCE(n.title, '')) LIKE '%{$searchTermEscaped}%')";
}

$statusSql = '';
if ($statusFilter !== 'all') {
    $statusSql = " AND n.status = '" . mysqli_real_escape_string($conn, $statusFilter) . "'";
}

$submissions = mysqli_query(
    $conn,
    "SELECT n.id, n.title, n.notice_type, n.status, n.created_at, e.name employee_name, e.employeeCode, e.designation
     FROM notices n
     LEFT JOIN employeestbl e ON e.id=n.created_by
     WHERE n.company_id=$companyId AND n.notice_type='employee_share'{$statusSql}{$searchSql}
     ORDER BY CASE WHEN n.status='pending_review' THEN 0 ELSE 1 END, n.id DESC"
);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>

    <div class="foundation-titlebar"><span></span><h2>Employee Share Approvals</h2></div>

    <form method="get" class="form-inline" style="margin-bottom: 15px;">
        <div class="form-group" style="margin-right: 10px;">
            <label for="statusFilter" style="margin-right: 6px;">Status</label>
            <select id="statusFilter" name="status" class="form-control">
                <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All</option>
                <option value="pending_review" <?php echo $statusFilter === 'pending_review' ? 'selected' : ''; ?>>Pending</option>
                <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>Approved</option>
                <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Declined</option>
            </select>
        </div>

        <div class="form-group" style="margin-right: 10px;">
            <label for="searchFilter" style="margin-right: 6px;">Search</label>
            <input type="text" id="searchFilter" name="search" class="form-control" value="<?php echo oecrm_h($searchTerm); ?>" placeholder="Employee or title">
        </div>

        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if ($statusFilter !== 'all' || $searchTerm !== ''): ?>
            <a href="employeeShareApprovals.php" class="btn btn-default" style="margin-left: 8px;">Clear</a>
        <?php endif; ?>
    </form>

    <div class="panel panel-default">
        <div class="panel-heading">Pending and recent employee updates</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table employee-share-approval-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="employee-share-submitted-column">Submitted</th>
                        <th class="employee-share-action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($submissions) === 0): ?>
                        <tr><td colspan="6">No employee share submissions found.</td></tr>
                    <?php endif; ?>
                    <?php while ($row = mysqli_fetch_assoc($submissions)): ?>
                        <tr class="employee-share-clickable-row" data-href="employeeShareView.php?id=<?php echo (int) $row['id']; ?>" tabindex="0" role="link" aria-label="View update details for <?php echo oecrm_h($row['title']); ?>">
                            <td><strong><?php echo oecrm_h($row['employee_name'] ?: 'Unknown'); ?></strong><small style="display:block"><?php echo oecrm_h($row['employeeCode'] ?: '-'); ?></small></td>
                            <td><?php echo oecrm_h($row['title']); ?></td>
                            <td><?php echo oecrm_h(ucwords(str_replace('_', ' ', $row['notice_type']))); ?></td>
                            <td>
                                <?php
                                $statusClass = 'label-default';
                                if ($row['status'] === 'pending_review') { $statusClass = 'label-warning'; }
                                if ($row['status'] === 'published') { $statusClass = 'label-success'; }
                                if ($row['status'] === 'rejected') { $statusClass = 'label-danger'; }
                                ?>
                                <span class="label <?php echo $statusClass; ?>"><?php echo oecrm_h(ucfirst(str_replace('_', ' ', $row['status']))); ?></span>
                            </td>
                            <td class="employee-share-submitted-column"><?php echo oecrm_h($row['created_at'] ? date('d M Y', strtotime($row['created_at'])) : '-'); ?></td>
                            <td class="employee-share-action-column">
                                <div class="employee-share-admin-actions">
                                <?php if ($row['status'] === 'pending_review'): ?>
                                    <form method="post" action="employeeShareApprovalAction.php">
                                        <?php echo oecrm_csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-success btn-xs" onclick="return confirm('Approve this employee update?');">Approve</button>
                                    </form>
                                    <form method="post" action="employeeShareApprovalAction.php">
                                        <?php echo oecrm_csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-xs" onclick="return confirm('Decline this employee update?');">Decline</button>
                                    </form>
                                <?php elseif ($row['status'] === 'published'): ?>
                                    <span class="text-success"><strong>Approved</strong></span>
                                <?php elseif ($row['status'] === 'rejected'): ?>
                                    <span class="text-danger"><strong>Declined</strong></span>
                                <?php else: ?>
                                    <span class="text-muted">Done</span>
                                <?php endif; ?>
                                <form method="post" action="employeeShareApprovalAction.php">
                                    <?php echo oecrm_csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                    <button type="submit" name="action" value="delete" class="btn btn-xs employee-share-delete-button" onclick="return confirm('Delete this employee update? This cannot be undone.');"><i class="fa fa-trash-o"></i> Delete</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('.employee-share-clickable-row').forEach(function (row) {
    function openDetails(event) {
        if (event.type === 'keydown') {
            if (event.key !== 'Enter') { return; }
            event.preventDefault();
        } else if (event.target.closest('a, button, form, input, select, textarea, label')) {
            return;
        }
        window.location.assign(row.dataset.href);
    }

    row.addEventListener('click', openDetails);
    row.addEventListener('keydown', openDetails);
});
</script>
<?php include 'footer.php'; ?>
