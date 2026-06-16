<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../birthdays.php';

$employeeId = (int) $_SESSION['employeeId'];
$employeeCompany = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT company_id FROM employeestbl WHERE id=' . $employeeId));
oecrm_auto_send_birthday_wishes($conn, (int) ($employeeCompany['company_id'] ?? 0));
$today = date('Y-m-d');

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

$totalProjects = max(1, (int) ($projectStats['total'] ?? 0));
$completion = round(((int) ($projectStats['completed'] ?? 0) / $totalProjects) * 100);
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="employee-welcome-card">
        <div>
            <span class="employee-welcome-label">Welcome back</span>
            <h2><?php echo oecrm_h($rowEmpView['name'] ?? 'Employee'); ?> 👋</h2>
            <p>Track your projects, tasks, attendance and leave requests from one workspace.</p>
        </div>
        <div class="employee-welcome-actions">
            <a class="btn btn-default" href="viewTask.php"><i class="fa fa-check-square-o"></i> My Tasks</a>
            <a class="btn btn-primary" href="dashboard.php"><i class="fa fa-clock-o"></i> Attendance</a>
        </div>
    </div>
    <?php include 'dashboardNotices.php'; ?>
    <div class="employee-stat-grid">
        <a href="viewProject.php" class="employee-stat-card stat-purple"><i class="fa fa-briefcase"></i><span><small>Assigned Projects</small><strong><?php echo (int) ($projectStats['total'] ?? 0); ?></strong><em><?php echo (int) ($projectStats['active'] ?? 0); ?> currently active</em></span></a>
        <a href="viewTask.php" class="employee-stat-card stat-blue"><i class="fa fa-tasks"></i><span><small>Open Tasks</small><strong><?php echo (int) ($taskStats['open_tasks'] ?? 0); ?></strong><em><?php echo (int) ($taskStats['completed'] ?? 0); ?> completed</em></span></a>
        <a href="leave_index.php" class="employee-stat-card stat-orange"><i class="fa fa-calendar-minus-o"></i><span><small>Pending Leaves</small><strong><?php echo (int) ($leaveStats['pending'] ?? 0); ?></strong><em><?php echo (int) ($leaveStats['total'] ?? 0); ?> total requests</em></span></a>
        <a href="dashboard.php" class="employee-stat-card stat-green"><i class="fa fa-clock-o"></i><span><small>Today Attendance</small><strong class="attendance-state"><?php echo oecrm_h(ucwords(str_replace('_', ' ', $attendance['attendance_status'] ?? 'Not marked'))); ?></strong><em><?php echo oecrm_h($today); ?></em></span></a>
    </div>
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
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
