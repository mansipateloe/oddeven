<?php
include 'header.php';
require_once __DIR__ . '/../notices.php';

$employeeId = (int) $_SESSION['employeeId'];
$notices = oecrm_employee_notices($conn, $employeeId, 100);
$flash = $_SESSION['employee_notice_flash'] ?? '';
unset($_SESSION['employee_notice_flash']);
?>
<div id="page-wrapper" class="employee-notice-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
    <div class="employee-notice-heading">
        <div><h3>Notice Center</h3><p>Company announcements, policies and HR updates</p></div>
    </div>
    <div class="employee-notice-list" id="employeeNoticeList">
        <?php if (mysqli_num_rows($notices) === 0): ?><div class="employee-notice-empty"><i class="fa fa-bell-o"></i><h4>No active notices</h4></div><?php endif; ?>
        <?php $noticeIndex = 0; while ($n = mysqli_fetch_assoc($notices)): $noticeIndex++; ?>
            <article class="employee-notice-card priority-<?php echo htmlspecialchars($n['priority']); ?> <?php echo !$n['read_at'] ? 'unread' : ''; ?> <?php echo $noticeIndex > 5 ? 'notice-hidden' : ''; ?>">
                <div class="employee-notice-meta">
                    <span><?php echo htmlspecialchars(ucfirst($n['notice_type'])); ?></span>
                    <span><?php echo date('d M Y h:i A', strtotime($n['publish_at'])); ?></span>
                    <span class="notice-priority <?php echo $n['priority']; ?>"><?php echo ucfirst($n['priority']); ?></span>
                </div>
                <h4><?php echo htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <div class="employee-notice-body"><?php echo $n['body_html']; ?></div>
                <div class="employee-notice-actions">
                    <form method="post" action="noticeAction.php">
                        <?php echo oecrm_csrf_field(); ?>
                        <input type="hidden" name="notice_id" value="<?php echo (int) $n['id']; ?>">
                        <button name="action" value="read" class="btn btn-default btn-sm"><i class="fa fa-check"></i> <?php echo $n['read_at'] ? 'Read' : 'Mark as Read'; ?></button>
                        <?php if ($n['acknowledgement_required'] && !$n['acknowledged_at']): ?>
                            <button name="action" value="acknowledge" class="btn btn-primary btn-sm"><i class="fa fa-check-circle"></i> Acknowledge</button>
                        <?php elseif ($n['acknowledged_at']): ?>
                            <span class="acknowledged"><i class="fa fa-check-circle"></i> Acknowledged</span>
                        <?php endif; ?>
                    </form>
                    <?php if ($n['attachment_stored_name']): ?><a class="btn btn-default btn-sm" href="noticeAttachment.php?id=<?php echo (int) $n['id']; ?>"><i class="fa fa-paperclip"></i> Attachment</a><?php endif; ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
    <?php if ($noticeIndex > 5): ?><div class="employee-load-more-wrap"><button type="button" class="btn btn-primary" id="noticeLoadMore"><i class="fa fa-chevron-down"></i> Load More</button></div><?php endif; ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var button = document.getElementById('noticeLoadMore');
    if (!button) return;
    button.addEventListener('click', function () {
        var hidden = Array.prototype.slice.call(document.querySelectorAll('.employee-notice-card.notice-hidden')).slice(0, 5);
        hidden.forEach(function (card) { card.classList.remove('notice-hidden'); });
        if (!document.querySelector('.employee-notice-card.notice-hidden')) button.style.display = 'none';
    });
});
</script>
<?php include 'footer.php'; ?>
