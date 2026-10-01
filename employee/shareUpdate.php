<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$employeeId = (int) ($_SESSION['employeeId'] ?? 0);
$companyId = (int) (mysqli_fetch_assoc(mysqli_query($conn, 'SELECT company_id FROM employeestbl WHERE id=' . $employeeId))['company_id'] ?? 0);
$flash = $_SESSION['employee_share_flash'] ?? '';
$errors = $_SESSION['employee_share_error'] ?? '';
unset($_SESSION['employee_share_flash'], $_SESSION['employee_share_error']);

$myPosts = mysqli_query(
    $conn,
    "SELECT id,title,body_html,notice_type,status,publish_at
     FROM notices
     WHERE company_id=$companyId AND created_by=$employeeId AND notice_type='employee_share'
     ORDER BY id DESC"
);
?>
<div id="page-wrapper" class="compact-admin-page employee-share-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="alert alert-danger"><?php echo oecrm_h($errors); ?></div><?php endif; ?>

    <div class="panel panel-default">
        <div class="panel-heading foundation-heading"><span>Share an Update</span><small>Post an announcement, poll idea or skill-share update for the team</small></div>
        <div class="panel-body">
            <form method="post" action="shareUpdateAction.php" class="form-horizontal">
                <?php echo oecrm_csrf_field(); ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Type</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="share_type" required>
                            <option value="announcement">Announcement</option>
                            <option value="poll">Poll / Question</option>
                            <option value="skill_share">Skill Share</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Title</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="title" maxlength="180" placeholder="Example: Project delivered successfully" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Details</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="body_html" rows="6" placeholder="Share what happened, what was achieved, or what you want the team to know..." required></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Submit for Approval</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">My Submitted Updates</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($myPosts) === 0): ?>
                        <tr><td colspan="4">No updates submitted yet.</td></tr>
                    <?php endif; ?>
                    <?php while ($post = mysqli_fetch_assoc($myPosts)): ?>
                        <tr>
                            <td><?php echo oecrm_h($post['title']); ?></td>
                            <td><?php echo oecrm_h(ucwords(str_replace('_', ' ', $post['notice_type']))); ?></td>
                            <td>
                                <?php
                                $statusClass = 'label-default';
                                if ($post['status'] === 'pending_review') { $statusClass = 'label-warning'; }
                                if ($post['status'] === 'published') { $statusClass = 'label-success'; }
                                if ($post['status'] === 'rejected') { $statusClass = 'label-danger'; }
                                ?>
                                <span class="label <?php echo $statusClass; ?>"><?php echo oecrm_h(ucfirst(str_replace('_', ' ', $post['status']))); ?></span>
                            </td>
                            <td><?php echo oecrm_h($post['publish_at'] ?: '-'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
