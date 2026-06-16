<?php
$active_menu = 'employee_login_log';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$employeeId = max(0, (int) ($_GET['employee_id'] ?? 0));
$module = trim($_GET['module'] ?? '');
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to = $_GET['to'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-30 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) $to = date('Y-m-d');

$companyId = oecrm_current_company_id($conn);
$companyFilter = oecrm_is_super_admin() ? '' : ' AND e.company_id=' . (int) $companyId;
$employeeFilter = $employeeId ? ' AND e.id=' . $employeeId : '';
$fromSql = mysqli_real_escape_string($conn, $from . ' 00:00:00');
$toSql = mysqli_real_escape_string($conn, $to . ' 23:59:59');
$activities = [];

$pushActivity = static function (&$activities, $row, $moduleKey, $activity, $details, $date, $ip = '') {
    $activities[] = [
        'date' => $date,
        'employee_id' => (int) $row['employee_id'],
        'employee' => $row['employee_name'],
        'employee_code' => $row['employeeCode'],
        'company' => $row['company_name'] ?: '-',
        'module' => $moduleKey,
        'activity' => $activity,
        'details' => $details,
        'ip' => $ip,
    ];
};

if ($module === '' || $module === 'authentication') {
    $rows = mysqli_query($conn, "SELECT l.user_id employee_id,e.name employee_name,e.employeeCode,c.display_name company_name,l.login_datetime activity_date,l.ip_address
        FROM login_details l JOIN employeestbl e ON e.id=l.user_id LEFT JOIN companies c ON c.id=e.company_id
        WHERE l.login_datetime BETWEEN '$fromSql' AND '$toSql'$companyFilter$employeeFilter");
    while ($row = mysqli_fetch_assoc($rows)) {
        $pushActivity($activities, $row, 'authentication', 'Login', 'Employee portal login', $row['activity_date'], $row['ip_address']);
    }
    $rows = mysqli_query($conn, "SELECT a.employee_id,e.name employee_name,e.employeeCode,c.display_name company_name,a.created_at activity_date,a.ip_address,a.action,a.description
        FROM audit_logs a JOIN employeestbl e ON e.id=a.employee_id LEFT JOIN companies c ON c.id=e.company_id
        WHERE a.module_key='authentication' AND a.action IN ('logout','failed_login') AND a.created_at BETWEEN '$fromSql' AND '$toSql'$companyFilter$employeeFilter");
    while ($row = mysqli_fetch_assoc($rows)) {
        $pushActivity($activities, $row, 'authentication', ucwords(str_replace('_', ' ', $row['action'])), $row['description'] ?: 'Authentication activity', $row['activity_date'], $row['ip_address']);
    }
}

if ($module === '' || $module === 'attendance') {
    $rows = mysqli_query($conn, "SELECT ev.employee_id,e.name employee_name,e.employeeCode,c.display_name company_name,ev.event_type,ev.event_time activity_date,ev.ip_address,ev.notes
        FROM attendance_events ev JOIN employeestbl e ON e.id=ev.employee_id LEFT JOIN companies c ON c.id=e.company_id
        WHERE ev.event_time BETWEEN '$fromSql' AND '$toSql'$companyFilter$employeeFilter");
    while ($row = mysqli_fetch_assoc($rows)) {
        $label = ucwords(str_replace('_', ' ', $row['event_type']));
        $pushActivity($activities, $row, 'attendance', $label, $row['notes'] ?: $label . ' recorded', $row['activity_date'], $row['ip_address']);
    }
}

if ($module === '' || $module === 'projects') {
    $rows = mysqli_query($conn, "SELECT pt.employee_id,e.name employee_name,e.employeeCode,c.display_name company_name,p.projectName,pt.role_name,pt.allocation_percent,pt.created_at activity_date
        FROM project_team_members pt JOIN employeestbl e ON e.id=pt.employee_id JOIN projectstbl p ON p.id=pt.project_id LEFT JOIN companies c ON c.id=e.company_id
        WHERE pt.created_at BETWEEN '$fromSql' AND '$toSql'$companyFilter$employeeFilter");
    while ($row = mysqli_fetch_assoc($rows)) {
        $details = $row['projectName'] . ' | ' . ($row['role_name'] ?: 'Team Member') . ' | ' . number_format((float) $row['allocation_percent'], 0) . '% allocation';
        $pushActivity($activities, $row, 'projects', 'Project Assigned', $details, $row['activity_date']);
    }
}

if ($module === '' || $module === 'tasks') {
    $rows = mysqli_query($conn, "SELECT ta.employee_id,e.name employee_name,e.employeeCode,c.display_name company_name,t.taskTitle,p.projectName,ta.is_primary,ta.assigned_at activity_date
        FROM task_assignees ta JOIN employeestbl e ON e.id=ta.employee_id JOIN tasktbl t ON t.id=ta.task_id
        LEFT JOIN projectstbl p ON p.id=CAST(t.projectId AS UNSIGNED) LEFT JOIN companies c ON c.id=e.company_id
        WHERE ta.assigned_at BETWEEN '$fromSql' AND '$toSql'$companyFilter$employeeFilter");
    while ($row = mysqli_fetch_assoc($rows)) {
        $details = $row['taskTitle'] . ($row['projectName'] ? ' | ' . $row['projectName'] : '') . ((int) $row['is_primary'] ? ' | Primary assignee' : ' | Reviewer / member');
        $pushActivity($activities, $row, 'tasks', 'Task Assigned', $details, $row['activity_date']);
    }
    $rows = mysqli_query($conn, "SELECT a.employee_id,e.name employee_name,e.employeeCode,c.display_name company_name,a.action,a.description,a.created_at activity_date,a.ip_address,a.old_values,a.new_values
        FROM audit_logs a JOIN employeestbl e ON e.id=a.employee_id LEFT JOIN companies c ON c.id=e.company_id
        WHERE a.module_key='tasks' AND a.created_at BETWEEN '$fromSql' AND '$toSql'$companyFilter$employeeFilter");
    while ($row = mysqli_fetch_assoc($rows)) {
        $details = $row['description'] ?: 'Task activity';
        $newValues = json_decode((string) $row['new_values'], true);
        if (!empty($newValues['status'])) $details .= ' | Status: ' . ucwords(str_replace('_', ' ', $newValues['status']));
        $pushActivity($activities, $row, 'tasks', ucwords(str_replace('_', ' ', $row['action'])), $details, $row['activity_date'], $row['ip_address']);
    }
}

if ($module === '' || $module === 'timesheets') {
    $rows = mysqli_query($conn, "SELECT a.employee_id,e.name employee_name,e.employeeCode,c.display_name company_name,a.action,a.description,a.created_at activity_date,a.ip_address,a.new_values
        FROM audit_logs a JOIN employeestbl e ON e.id=a.employee_id LEFT JOIN companies c ON c.id=e.company_id
        WHERE a.module_key='timesheets' AND a.created_at BETWEEN '$fromSql' AND '$toSql'$companyFilter$employeeFilter");
    while ($row = mysqli_fetch_assoc($rows)) {
        $details = $row['description'] ?: 'Timesheet activity';
        $newValues = json_decode((string) $row['new_values'], true);
        if (isset($newValues['hours'])) $details .= ' | ' . number_format((float) $newValues['hours'], 2) . ' hours';
        $pushActivity($activities, $row, 'timesheets', ucwords(str_replace('_', ' ', $row['action'])), $details, $row['activity_date'], $row['ip_address']);
    }
}

if ($module === '' || $module === 'notices') {
    $rows = mysqli_query($conn, "SELECT r.employee_id,e.name employee_name,e.employeeCode,c.display_name company_name,n.title,r.first_seen_at,r.read_at,r.acknowledged_at,r.acknowledgement_ip
        FROM notice_receipts r JOIN employeestbl e ON e.id=r.employee_id JOIN notices n ON n.id=r.notice_id LEFT JOIN companies c ON c.id=e.company_id
        WHERE COALESCE(r.acknowledged_at,r.read_at,r.first_seen_at) BETWEEN '$fromSql' AND '$toSql'$companyFilter$employeeFilter");
    while ($row = mysqli_fetch_assoc($rows)) {
        if ($row['first_seen_at'] && $row['first_seen_at'] >= $fromSql && $row['first_seen_at'] <= $toSql) {
            $pushActivity($activities, $row, 'notices', 'Notice Viewed', $row['title'], $row['first_seen_at']);
        }
        if ($row['read_at'] && $row['read_at'] >= $fromSql && $row['read_at'] <= $toSql) {
            $pushActivity($activities, $row, 'notices', 'Notice Read', $row['title'], $row['read_at']);
        }
        if ($row['acknowledged_at'] && $row['acknowledged_at'] >= $fromSql && $row['acknowledged_at'] <= $toSql) {
            $pushActivity($activities, $row, 'notices', 'Notice Acknowledged', $row['title'], $row['acknowledged_at'], $row['acknowledgement_ip']);
        }
    }
}

usort($activities, static function ($left, $right) {
    return strcmp($right['date'], $left['date']);
});

$employeeOptionsWhere = oecrm_is_super_admin() ? '1=1' : 'e.company_id=' . (int) $companyId;
$employeeOptions = mysqli_query($conn, "SELECT e.id,e.employeeCode,e.name,c.display_name company_name FROM employeestbl e LEFT JOIN companies c ON c.id=e.company_id WHERE $employeeOptionsWhere ORDER BY c.display_name,e.name");
$modules = [
    'authentication' => 'Login / Logout',
    'attendance' => 'Attendance',
    'projects' => 'Projects',
    'tasks' => 'Tasks',
    'timesheets' => 'Timesheets',
    'notices' => 'Notice Read',
];
?>

<div id="page-wrapper" class="compact-admin-page">
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading">
            <span>Employee Module Activity Logs</span>
            <small>Login, attendance, projects, tasks, timesheets and notice activity</small>
        </div>
        <div class="panel-body">
            <form method="get" class="activity-filters">
                <select class="form-control" name="employee_id" data-placeholder="Filter employee">
                    <option value="0">All employees</option>
                    <?php while ($employee = mysqli_fetch_assoc($employeeOptions)): ?>
                        <option value="<?php echo (int) $employee['id']; ?>" <?php echo $employeeId === (int) $employee['id'] ? 'selected' : ''; ?>><?php echo oecrm_h(($employee['company_name'] ? $employee['company_name'] . ' - ' : '') . $employee['employeeCode'] . ' - ' . $employee['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <select class="form-control" name="module">
                    <option value="">All modules</option>
                    <?php foreach ($modules as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo $module === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
                <input class="form-control" type="date" name="from" value="<?php echo oecrm_h($from); ?>">
                <input class="form-control" type="date" name="to" value="<?php echo oecrm_h($to); ?>">
                <button class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                <a class="btn btn-default" href="manageEmployeeLoginLog.php">Reset</a>
            </form>
            <div class="table-responsive">
                <table id="employeeActivityLogTable" class="table foundation-table">
                    <thead><tr><th>Date & Time</th><th>Employee</th><th>Company</th><th>Module</th><th>Activity</th><th>Details</th><th>IP</th></tr></thead>
                    <tbody>
                    <?php if (!$activities): ?><tr><td colspan="7" class="empty-cell">No employee activity found for selected filters.</td></tr><?php endif; ?>
                    <?php foreach ($activities as $row): ?>
                        <tr>
                            <td data-order="<?php echo oecrm_h($row['date']); ?>"><?php echo date('d M Y', strtotime($row['date'])); ?><small><?php echo date('h:i:s A', strtotime($row['date'])); ?></small></td>
                            <td><strong><?php echo oecrm_h($row['employee']); ?></strong><small><?php echo oecrm_h($row['employee_code']); ?></small></td>
                            <td><?php echo oecrm_h($row['company']); ?></td>
                            <td><span class="client-status active"><?php echo oecrm_h($modules[$row['module']] ?? ucwords($row['module'])); ?></span></td>
                            <td><?php echo oecrm_h($row['activity']); ?></td>
                            <td><?php echo oecrm_h($row['details']); ?></td>
                            <td><?php echo oecrm_h($row['ip'] ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
