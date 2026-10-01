<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../birthdays.php';

$employeeId = (int) $_SESSION['employeeId'];
$employeeCompany = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT company_id FROM employeestbl WHERE id=' . $employeeId));
oecrm_auto_send_birthday_wishes($conn, (int) ($employeeCompany['company_id'] ?? 0));
$today = date('Y-m-d');
$nextWeek = date('Y-m-d', strtotime('+7 days'));
$shareFeedFlash = $_SESSION['employee_share_feed_flash'] ?? '';
$shareFeedError = $_SESSION['employee_share_feed_error'] ?? '';
unset($_SESSION['employee_share_feed_flash'], $_SESSION['employee_share_feed_error']);

$projectStats = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT
    COUNT(DISTINCT p.id) total,
    COUNT(DISTINCT CASE WHEN p.status="pending" THEN p.id END) pending,
    COUNT(DISTINCT CASE WHEN p.status="inprogress" THEN p.id END) active,
    COUNT(DISTINCT CASE WHEN p.status="completed" THEN p.id END) completed
    FROM projectstbl p
    LEFT JOIN project_team_members tm ON tm.project_id=p.id AND tm.employee_id=' . $employeeId . ' AND tm.left_at IS NULL
    LEFT JOIN tasktbl assigned_task ON CAST(assigned_task.projectId AS UNSIGNED)=p.id
    LEFT JOIN task_assignees assigned_to ON assigned_to.task_id=assigned_task.id AND assigned_to.employee_id=' . $employeeId . '
    WHERE tm.employee_id=' . $employeeId . ' OR assigned_to.employee_id=' . $employeeId));
$taskStats = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(DISTINCT t.id) total,SUM(t.status IN ("open","in_progress","in_review","to_be_tested","staging_server","production","on_hold","pending","inprogress")) open_tasks,SUM(t.status IN ("closed","completed","2")) completed FROM tasktbl t JOIN task_assignees ta ON ta.task_id=t.id WHERE ta.employee_id=' . $employeeId));
$leaveStats = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total,SUM(status="pending") pending FROM leave_requests WHERE employee_id=' . $employeeId));
$attendance = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM attendance_sessions WHERE employee_id=$employeeId AND attendance_date='$today' LIMIT 1"));
$notices = mysqli_query($conn, 'SELECT DISTINCT n.id,n.title,n.priority,n.publish_at
    FROM notices n
    LEFT JOIN notice_targets nt ON nt.notice_id=n.id
    LEFT JOIN employeestbl e ON e.id=' . $employeeId . '
    WHERE n.status="published" AND n.publish_at<=NOW() AND (n.expires_at IS NULL OR n.expires_at>=NOW())
    AND (n.audience_type="all" OR nt.employee_id=' . $employeeId . ' OR (nt.department_id IS NOT NULL AND nt.department_id=e.department_id))
    ORDER BY FIELD(n.priority,"urgent","high","normal","low"),n.publish_at DESC LIMIT 5');
$sharePostsResult = mysqli_query(
    $conn,
    "SELECT n.id,n.title,n.body_html,n.publish_at,e.name employee_name,e.designation,
        (SELECT COUNT(*) FROM employee_share_likes l WHERE l.notice_id=n.id) likes_count,
        (SELECT COUNT(*) FROM employee_share_comments c WHERE c.notice_id=n.id) comments_count,
        EXISTS(SELECT 1 FROM employee_share_likes mine WHERE mine.notice_id=n.id AND mine.employee_id=$employeeId) liked_by_me
     FROM notices n
     JOIN employeestbl e ON e.id=n.created_by
     WHERE n.company_id=" . (int) ($employeeCompany['company_id'] ?? 0) . "
       AND n.notice_type='employee_share' AND n.status='published' AND n.publish_at<=NOW()
       AND (n.expires_at IS NULL OR n.expires_at>=NOW())
     ORDER BY COALESCE(n.published_at,n.publish_at) DESC,n.id DESC
     LIMIT 10"
);
$sharePosts = [];
if ($sharePostsResult) {
    while ($sharePost = mysqli_fetch_assoc($sharePostsResult)) {
        $sharePosts[] = $sharePost;
    }
}

$commentsByPost = [];
$sharePostIds = array_map(static function ($post) {
    return (int) $post['id'];
}, $sharePosts);
if ($sharePostIds) {
    $sharePostIdList = implode(',', $sharePostIds);
    $feedComments = mysqli_query(
        $conn,
        "SELECT c.notice_id,c.comment_text,c.created_at,e.name employee_name
         FROM employee_share_comments c
         JOIN employeestbl e ON e.id=c.employee_id
         WHERE c.notice_id IN ($sharePostIdList)
         ORDER BY c.id DESC"
    );
    if ($feedComments) {
        while ($comment = mysqli_fetch_assoc($feedComments)) {
            $postId = (int) $comment['notice_id'];
            if (count($commentsByPost[$postId] ?? []) < 5) {
                $commentsByPost[$postId][] = $comment;
            }
        }
    }
    foreach ($commentsByPost as &$postComments) {
        $postComments = array_reverse($postComments);
    }
    unset($postComments);
}
$upcomingHolidays = mysqli_query($conn, 'SELECT holidayDate,holidayTitle FROM holidaytbl WHERE company_id=' . (int) ($employeeCompany['company_id'] ?? 0) . ' AND holidayDate BETWEEN "' . mysqli_real_escape_string($conn, $today) . '" AND "' . mysqli_real_escape_string($conn, $nextWeek) . '" ORDER BY holidayDate');

$totalProjects = max(1, (int) ($projectStats['total'] ?? 0));
$completion = round(((int) ($projectStats['completed'] ?? 0) / $totalProjects) * 100);
?>
<div id="page-wrapper" class="compact-admin-page preadmin-dashboard-page employee-main-dashboard">
    <div class="employee-welcome-card">
        <div>
            <span class="employee-welcome-label">Welcome back</span>
            <h2><?php echo oecrm_h($rowEmpView['name'] ?? 'Employee'); ?> 👋</h2>
            <p>Track your projects, tasks, attendance and leave requests from one workspace.</p>
        </div>
        <div class="employee-welcome-actions">
            <a class="btn btn-default" href="viewTask.php"><i class="fa fa-check-square-o"></i> My Tasks</a>
            <a class="btn btn-primary" href="dashboard.php"><i class="fa fa-clock-o"></i> Attendance</a>
            <a class="btn btn-success" href="shareUpdate.php"><i class="fa fa-paper-plane"></i> Share Update</a>
        </div>
    </div>
    <?php include 'dashboardNotices.php'; ?>
    <div class="employee-stat-grid">
        <a href="viewProject.php" class="employee-stat-card stat-purple"><i class="fa fa-briefcase"></i><span><small>Assigned Projects</small><strong><?php echo (int) ($projectStats['total'] ?? 0); ?></strong><em><?php echo (int) ($projectStats['active'] ?? 0); ?> currently active</em></span></a>
        <a href="viewTask.php" class="employee-stat-card stat-blue"><i class="fa fa-tasks"></i><span><small>Open Tasks</small><strong><?php echo (int) ($taskStats['open_tasks'] ?? 0); ?></strong><em><?php echo (int) ($taskStats['completed'] ?? 0); ?> completed</em></span></a>
        <a href="leave_index.php" class="employee-stat-card stat-orange"><i class="fa fa-calendar-minus-o"></i><span><small>Pending Leaves</small><strong><?php echo (int) ($leaveStats['pending'] ?? 0); ?></strong><em><?php echo (int) ($leaveStats['total'] ?? 0); ?> total requests</em></span></a>
        <a href="dashboard.php" class="employee-stat-card stat-green"><i class="fa fa-clock-o"></i><span><small>Today Attendance</small><strong class="attendance-state"><?php echo oecrm_h(ucwords(str_replace('_', ' ', $attendance['attendance_status'] ?? 'Not marked'))); ?></strong><em><?php echo oecrm_h($today); ?></em></span></a>
    </div>
    <section class="panel panel-default employee-share-feed" id="employee-share-feed">
        <div class="panel-heading"><span><i class="fa fa-comments-o"></i> Team Updates</span><a href="shareUpdate.php">Share an update</a></div>
        <div class="panel-body">
            <?php if ($shareFeedFlash): ?><div class="alert alert-success"><?php echo oecrm_h($shareFeedFlash); ?></div><?php endif; ?>
            <?php if ($shareFeedError): ?><div class="alert alert-danger"><?php echo oecrm_h($shareFeedError); ?></div><?php endif; ?>
            <?php if (!$sharePosts): ?>
                <div class="employee-empty-state"><i class="fa fa-comments-o"></i><p>No approved team updates yet.</p></div>
            <?php endif; ?>
            <?php foreach ($sharePosts as $sharePost): ?>
                <?php $postId = (int) $sharePost['id']; ?>
                <article class="employee-share-post">
                    <div class="employee-share-post-author">
                        <span class="employee-share-avatar"><i class="fa fa-user"></i></span>
                        <span class="employee-share-author-info">
                            <strong><?php echo oecrm_h($sharePost['employee_name']); ?></strong>
                            <small><span><?php echo oecrm_h($sharePost['designation'] ?: 'Employee'); ?></span><span aria-hidden="true"> · </span><time datetime="<?php echo oecrm_h(date('c', strtotime($sharePost['publish_at']))); ?>"><?php echo oecrm_h(date('d M Y, g:i a', strtotime($sharePost['publish_at']))); ?></time></small>
                        </span>
                    </div>
                    <h4><?php echo oecrm_h($sharePost['title']); ?></h4>
                    <div class="employee-share-post-content"><?php echo nl2br(oecrm_h(strip_tags($sharePost['body_html']))); ?></div>
                    <div class="employee-share-post-actions">
                        <form method="post" action="shareUpdateEngagementAction.php">
                            <?php echo oecrm_csrf_field(); ?>
                            <input type="hidden" name="notice_id" value="<?php echo $postId; ?>">
                            <button type="submit" name="action" value="<?php echo $sharePost['liked_by_me'] ? 'unlike' : 'like'; ?>" class="btn btn-default btn-sm"><i class="fa fa-thumbs-up"></i> <?php echo $sharePost['liked_by_me'] ? 'Liked' : 'Like'; ?> · <?php echo (int) $sharePost['likes_count']; ?></button>
                        </form>
                        <span><i class="fa fa-comment-o"></i> <?php echo (int) $sharePost['comments_count']; ?> comments</span>
                    </div>
                    <?php if (!empty($commentsByPost[$postId])): ?>
                        <div class="employee-share-comments">
                            <?php foreach ($commentsByPost[$postId] as $comment): ?>
                                <div class="employee-share-comment">
                                    <strong><?php echo oecrm_h($comment['employee_name']); ?></strong>
                                    <span><?php echo nl2br(oecrm_h($comment['comment_text'])); ?></span>
                                    <small><?php echo oecrm_h(date('d M Y, g:i a', strtotime($comment['created_at']))); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="shareUpdateEngagementAction.php" class="employee-share-comment-form">
                        <?php echo oecrm_csrf_field(); ?>
                        <input type="hidden" name="notice_id" value="<?php echo $postId; ?>">
                        <input type="hidden" name="action" value="comment">
                        <input type="text" name="comment" class="form-control" maxlength="500" placeholder="Write a comment..." required>
                        <button type="submit" class="btn btn-primary btn-sm" aria-label="Post comment"><i class="fa fa-paper-plane"></i></button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <div class="row employee-dashboard-grid">
        <div class="col-lg-7">
            <div class="panel panel-default">
                <div class="panel-heading"><span><i class="fa fa-line-chart"></i> Work Overview</span><a href="viewProject.php">View projects</a></div>
                <div class="panel-body">
                    <div class="employee-progress-summary">
                        <div class="employee-progress-ring" style="--progress:<?php echo $completion; ?>%"><span><?php echo $completion; ?>%</span></div>
                        <div>
                            <h3>Project completion</h3>
                            <p><?php echo (int) ($projectStats['completed'] ?? 0); ?> of <?php echo (int) ($projectStats['total'] ?? 0); ?> assigned projects completed.</p>
                            <div class="employee-work-legend">
                                <span><i class="pending"></i><?php echo (int) ($projectStats['pending'] ?? 0); ?> Pending</span>
                                <span><i class="active"></i><?php echo (int) ($projectStats['active'] ?? 0); ?> In progress</span>
                                <span><i class="done"></i><?php echo (int) ($projectStats['completed'] ?? 0); ?> Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="panel panel-default">
                <div class="panel-heading"><span><i class="fa fa-bullhorn"></i> Latest Notices</span><a href="notices.php">View all</a></div>
                <div class="panel-body">
                    <?php if (mysqli_num_rows($notices) === 0): ?>
                        <div class="employee-empty-state"><i class="fa fa-bell-slash-o"></i><p>No current notices.</p></div>
                    <?php endif; ?>
                    <?php while ($notice = mysqli_fetch_assoc($notices)): ?>
                        <div class="employee-notice-row priority-<?php echo oecrm_h($notice['priority']); ?>">
                            <i class="fa fa-bullhorn"></i>
                            <span><strong><?php echo oecrm_h($notice['title']); ?></strong><small><?php echo oecrm_h(ucfirst($notice['priority']) . ' · ' . date('d M Y', strtotime($notice['publish_at']))); ?></small></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><span><i class="fa fa-calendar"></i> Next 7 Days Holidays</span><a href="holidays.php">View calendar</a></div>
                <div class="panel-body">
                    <?php if (!$upcomingHolidays || mysqli_num_rows($upcomingHolidays) === 0): ?>
                        <div class="employee-empty-state"><i class="fa fa-calendar-o"></i><p>No holiday in the next 7 days.</p></div>
                    <?php endif; ?>
                    <?php while ($holiday = mysqli_fetch_assoc($upcomingHolidays)): ?>
                        <div class="employee-notice-row priority-normal">
                            <i class="fa fa-calendar-check-o"></i>
                            <span><strong><?php echo oecrm_h($holiday['holidayTitle']); ?></strong><small><?php echo date('D, d M Y', strtotime($holiday['holidayDate'])); ?></small></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

