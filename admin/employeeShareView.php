<?php
$active_menu = 'notices';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'notices', 'view');
$companyId = oecrm_current_company_id($conn);
$noticeId = (int) ($_GET['id'] ?? 0);
$submission = null;

if ($noticeId > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT n.id,n.title,n.body_html,n.notice_type,n.status,n.created_at,n.published_at,
                e.name employee_name,e.employeeCode,e.designation
         FROM notices n
         LEFT JOIN employeestbl e ON e.id=n.created_by
         WHERE n.id=? AND n.company_id=? AND n.notice_type='employee_share'
         LIMIT 1"
    );
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $noticeId, $companyId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $submission = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
    }
}

$statusLabel = $submission ? ucfirst(str_replace('_', ' ', $submission['status'])) : '';
$statusClass = 'label-default';
if ($submission && $submission['status'] === 'pending_review') { $statusClass = 'label-warning'; }
if ($submission && $submission['status'] === 'published') { $statusClass = 'label-success'; }
if ($submission && $submission['status'] === 'rejected') { $statusClass = 'label-danger'; }

$bodyText = '';
if ($submission) {
    $bodyText = preg_replace('/<\/(?:p|div)>/i', "\n\n", $submission['body_html']);
    $bodyText = html_entity_decode(strip_tags($bodyText), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
?>
<div id="page-wrapper" class="compact-admin-page employee-share-detail-page">
    <div class="foundation-titlebar"><span></span><h2>Employee Update Details</h2></div>
    <p><a href="employeeShareApprovals.php" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to approvals</a></p>

    <?php if (!$submission): ?>
        <div class="alert alert-warning">This employee update was not found or is not available for your company.</div>
    <?php else: ?>
        <div class="panel panel-default employee-share-detail-panel">
            <div class="panel-heading">
                <span><?php echo oecrm_h($submission['title']); ?></span>
                <span class="label <?php echo $statusClass; ?>"><?php echo oecrm_h($statusLabel); ?></span>
            </div>
            <div class="panel-body">
                <dl class="employee-share-detail-meta">
                    <div><dt>Employee</dt><dd><?php echo oecrm_h($submission['employee_name'] ?: 'Unknown'); ?></dd></div>
                    <div><dt>Employee Code</dt><dd><?php echo oecrm_h($submission['employeeCode'] ?: '-'); ?></dd></div>
                    <div><dt>Designation</dt><dd><?php echo oecrm_h($submission['designation'] ?: '-'); ?></dd></div>
                    <div><dt>Type</dt><dd><?php echo oecrm_h(ucwords(str_replace('_', ' ', $submission['notice_type']))); ?></dd></div>
                    <div><dt>Submitted</dt><dd><?php echo oecrm_h($submission['created_at'] ? date('d M Y, g:i a', strtotime($submission['created_at'])) : '-'); ?></dd></div>
                    <?php if ($submission['published_at']): ?>
                        <div><dt>Published</dt><dd><?php echo oecrm_h(date('d M Y, g:i a', strtotime($submission['published_at']))); ?></dd></div>
                    <?php endif; ?>
                </dl>
                <section class="employee-share-detail-content">
                    <h3>Update</h3>
                    <div><?php echo nl2br(oecrm_h(trim($bodyText))); ?></div>
                </section>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
