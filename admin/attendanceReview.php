<?php
$active_menu = 'attendance';
$active_submenu = 'attendance_review';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../attendance.php';
oecrm_require_permission($conn, 'attendance_review', 'view');

$companyId = oecrm_current_company_id($conn);
$yearRangeRow = mysqli_fetch_assoc(mysqli_query(
    $conn,
    'SELECT LEAST(
        COALESCE((SELECT MIN(YEAR(s.attendance_date)) FROM attendance_sessions s JOIN employeestbl e ON e.id=s.employee_id WHERE e.company_id=' . (int) $companyId . '), YEAR(CURDATE())),
        COALESCE((SELECT MIN(YEAR(STR_TO_DATE(joiningDate,"%Y-%m-%d"))) FROM employeestbl WHERE company_id=' . (int) $companyId . ' AND joiningDate REGEXP "^[0-9]{4}-[0-9]{2}-[0-9]{2}$"), YEAR(CURDATE()))
    ) earliest_year'
));
$earliestYear = max(2000, (int) ($yearRangeRow['earliest_year'] ?? date('Y')));
$latestYear = (int) date('Y');
$year = min($latestYear, max($earliestYear, (int) ($_GET['year'] ?? $latestYear)));
$month = min(12, max(1, (int) ($_GET['month'] ?? date('n'))));
$employeeId = max(0, (int) ($_GET['employee_id'] ?? 0));
$periodStart = sprintf('%04d-%02d-01', $year, $month);
$periodEnd = date('Y-m-t', strtotime($periodStart));
$today = date('Y-m-d');
$hasAttendanceFilter = isset($_GET['month']) || isset($_GET['year']) || isset($_GET['employee_id']);
$message = $_SESSION['attendance_review_flash'] ?? '';
$error = $_SESSION['attendance_review_error'] ?? '';
unset($_SESSION['attendance_review_flash'], $_SESSION['attendance_review_error']);

$lockStmt = mysqli_prepare($conn, 'SELECT * FROM attendance_period_locks WHERE company_id=? AND period_year=? AND period_month=?');
mysqli_stmt_bind_param($lockStmt, 'iii', $companyId, $year, $month);
mysqli_stmt_execute($lockStmt);
$periodLock = mysqli_fetch_assoc(mysqli_stmt_get_result($lockStmt));
mysqli_stmt_close($lockStmt);
$isLocked = !empty($periodLock['is_locked']);

$employeeStmt = mysqli_prepare($conn, 'SELECT id,employeeCode,name,designation,joiningDate FROM employeestbl WHERE company_id=? AND status=0 ORDER BY name');
mysqli_stmt_bind_param($employeeStmt, 'i', $companyId);
mysqli_stmt_execute($employeeStmt);
$employeeResult = mysqli_stmt_get_result($employeeStmt);
$employeeOptions = [];
while ($employee = mysqli_fetch_assoc($employeeResult)) {
    $employeeOptions[] = $employee;
}
mysqli_stmt_close($employeeStmt);

$selectedEmployee = null;
foreach ($employeeOptions as $employee) {
    if ((int) $employee['id'] === $employeeId) {
        $selectedEmployee = $employee;
        break;
    }
}
if ($employeeId && !$selectedEmployee) {
    $employeeId = 0;
    $error = 'Active employee not found for the selected company.';
}

$calendarRows = [];
$stats = ['present' => 0, 'short' => 0, 'missing' => 0, 'off' => 0];
if ($selectedEmployee) {
    $sessionStmt = mysqli_prepare(
        $conn,
        'SELECT s.*,sh.name shift_name,sh.required_minutes
         FROM attendance_sessions s
         JOIN shifts sh ON sh.id=s.shift_id
         WHERE s.employee_id=? AND s.attendance_date BETWEEN ? AND ?'
    );
    mysqli_stmt_bind_param($sessionStmt, 'iss', $employeeId, $periodStart, $periodEnd);
    mysqli_stmt_execute($sessionStmt);
    $sessionResult = mysqli_stmt_get_result($sessionStmt);
    $sessionsByDate = [];
    $sessionIds = [];
    while ($session = mysqli_fetch_assoc($sessionResult)) {
        $sessionsByDate[$session['attendance_date']] = $session;
        $sessionIds[] = (int) $session['id'];
    }
    mysqli_stmt_close($sessionStmt);

    $eventsByDate = [];
    if ($sessionIds) {
        $eventResult = mysqli_query(
            $conn,
            'SELECT s.attendance_date,e.event_type,e.event_time
             FROM attendance_events e
             JOIN attendance_sessions s ON s.id=e.session_id
             WHERE e.session_id IN (' . implode(',', $sessionIds) . ')
             ORDER BY e.event_time,e.id'
        );
        while ($event = mysqli_fetch_assoc($eventResult)) {
            $eventsByDate[$event['attendance_date']][$event['event_type']][] = $event['event_time'];
        }
    }

    $cursor = new DateTime($periodStart);
    $lastDate = new DateTime($periodEnd);
    while ($cursor <= $lastDate) {
        $date = $cursor->format('Y-m-d');
        $session = $sessionsByDate[$date] ?? null;
        $shift = oecrm_employee_shift($conn, $employeeId, $date);
        $joined = empty($selectedEmployee['joiningDate']) || $selectedEmployee['joiningDate'] <= $date;
        $weeklyOff = $shift ? oecrm_shift_is_weekly_off($conn, (int) $shift['id'], $date) : false;
        $holiday = oecrm_is_holiday($conn, $date, $companyId);
        $workedOnOffDay = $session && ($weeklyOff || $holiday);

        if (!$joined) {
            $statusKey = 'not-joined';
            $statusLabel = 'Not Joined';
        } elseif (!$shift) {
            $statusKey = 'no-shift';
            $statusLabel = 'No Shift';
        } elseif ($workedOnOffDay) {
            $statusKey = 'worked-off';
            $statusLabel = $holiday ? 'Worked on Holiday' : 'Worked on Weekly Off';
            $stats['present']++;
        } elseif ($holiday) {
            $statusKey = 'holiday';
            $statusLabel = 'Holiday';
            $stats['off']++;
        } elseif ($weeklyOff) {
            $statusKey = 'weekly-off';
            $statusLabel = 'Weekly Off';
            $stats['off']++;
        } elseif ($session) {
            $statusKey = str_replace('_', '-', $session['attendance_status']);
            $statusLabel = ucwords(str_replace('_', ' ', $session['attendance_status']));
            if ($session['actual_out'] && (int) $session['effective_minutes'] >= (int) $session['required_minutes']) {
                $stats['present']++;
            } else {
                $stats['short']++;
            }
        } elseif ($date < $today) {
            $statusKey = 'absent';
            $statusLabel = 'Absent';
            $stats['missing']++;
        } elseif ($date === $today) {
            $statusKey = 'not-started';
            $statusLabel = 'Not Started';
        } else {
            $statusKey = 'upcoming';
            $statusLabel = 'Upcoming';
        }

        $calendarRows[] = [
            'date' => $date,
            'session' => $session,
            'shift' => $shift,
            'events' => $eventsByDate[$date] ?? [],
            'status_key' => $statusKey,
            'status_label' => $statusLabel,
        ];
        $cursor->modify('+1 day');
    }
}

function attendance_event_times($events, $type)
{
    if (empty($events[$type])) {
        return '<span class="attendance-empty">—</span>';
    }
    $times = array_map(static function ($time) {
        return date('h:i A', strtotime($time));
    }, $events[$type]);
    return implode('<br>', array_map('oecrm_h', $times));
}

function attendance_minutes($minutes)
{
    $minutes = max(0, (int) $minutes);
    return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
}
?>
<div id="page-wrapper" class="compact-admin-page attendance-review-page">
    <?php if ($message): ?><div class="alert alert-success"><?php echo oecrm_h($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>

    <div class="attendance-review-toolbar">
        <div>
            <?php if ($selectedEmployee): ?>
                <a class="attendance-back-link" href="attendanceReview.php?month=<?php echo $month; ?>&year=<?php echo $year; ?>"><i class="fa fa-arrow-left"></i> All Active Employees</a>
                <h2><?php echo oecrm_h($selectedEmployee['name']); ?></h2>
                <p><?php echo oecrm_h($selectedEmployee['employeeCode']); ?> · <?php echo oecrm_h($selectedEmployee['designation'] ?: 'Employee'); ?></p>
            <?php else: ?>
                <h2>Active Employees</h2>
                <p>Select an employee to view complete monthly attendance.</p>
            <?php endif; ?>
        </div>
        <form method="get" class="attendance-period-filter">
            <?php if ($selectedEmployee): ?><input type="hidden" name="employee_id" value="<?php echo $employeeId; ?>"><?php endif; ?>
            <select class="form-control" name="month" aria-label="Month">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php echo $month === $m ? 'selected' : ''; ?>><?php echo date('F', mktime(0, 0, 0, $m, 1)); ?></option>
                <?php endfor; ?>
            </select>
            <select class="form-control" name="year" aria-label="Year">
                <?php for ($y = $latestYear; $y >= $earliestYear; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $year === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <button class="btn btn-primary"><i class="fa fa-calendar"></i> View</button>
            <a class="btn btn-default" href="attendanceReview.php"><i class="fa fa-times"></i> Remove Filter</a>
        </form>
    </div>

    <?php if (!$selectedEmployee): ?>
        <div class="panel panel-default attendance-employee-panel">
            <div class="panel-heading">All Active Employees <span><?php echo count($employeeOptions); ?></span></div>
            <div class="panel-body table-responsive">
                <table class="table no-datatable attendance-employee-table">
                    <thead><tr><th>Employee</th><th>Designation</th><th>Current Shift</th><th>Required</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php if (!$employeeOptions): ?><tr><td colspan="5" class="empty-cell">No active employees found.</td></tr><?php endif; ?>
                    <?php foreach ($employeeOptions as $employee): $shift = oecrm_employee_shift($conn, (int) $employee['id'], $today); ?>
                        <tr>
                            <td><strong><?php echo oecrm_h($employee['name']); ?></strong><small><?php echo oecrm_h($employee['employeeCode']); ?></small></td>
                            <td><?php echo oecrm_h($employee['designation'] ?: '—'); ?></td>
                            <td><?php echo $shift ? oecrm_h($shift['name']) : '<span class="attendance-empty">Not assigned</span>'; ?></td>
                            <td><?php echo $shift ? attendance_minutes($shift['required_minutes']) . ' hrs' : '—'; ?></td>
                            <td><a class="btn btn-primary btn-xs" href="attendanceReview.php?employee_id=<?php echo (int) $employee['id']; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>" title="View monthly attendance"><i class="fa fa-eye"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="attendance-review-actions">
            <div class="attendance-period-state <?php echo $isLocked ? 'locked' : 'open'; ?>">
                <i class="fa <?php echo $isLocked ? 'fa-lock' : 'fa-unlock'; ?>"></i>
                <span><small><?php echo date('F Y', strtotime($periodStart)); ?></small><strong><?php echo $isLocked ? 'Period Locked' : 'Period Open'; ?></strong></span>
            </div>
            <?php if (!$isLocked && oecrm_can($conn, 'attendance_review', 'correct')): ?>
                <button class="btn btn-primary" data-toggle="modal" data-target="#manualEventModal"><i class="fa fa-plus"></i> Add Event</button>
            <?php endif; ?>
            <?php if (oecrm_can($conn, 'attendance_review', $isLocked ? 'unlock' : 'lock')): ?>
                <button class="btn <?php echo $isLocked ? 'btn-warning' : 'btn-danger'; ?>" data-toggle="modal" data-target="#periodLockModal"><i class="fa <?php echo $isLocked ? 'fa-unlock' : 'fa-lock'; ?>"></i> <?php echo $isLocked ? 'Unlock' : 'Lock'; ?> Month</button>
            <?php endif; ?>
        </div>

        <div class="review-stats attendance-month-stats">
            <div><i class="fa fa-check-circle"></i><span><strong><?php echo $stats['present']; ?></strong><small>Completed / Worked</small></span></div>
            <div><i class="fa fa-clock-o"></i><span><strong><?php echo $stats['short']; ?></strong><small>Incomplete / Short</small></span></div>
            <div><i class="fa fa-times-circle"></i><span><strong><?php echo $stats['missing']; ?></strong><small>Absent</small></span></div>
            <div><i class="fa fa-calendar"></i><span><strong><?php echo $stats['off']; ?></strong><small>Holiday / Weekly Off</small></span></div>
        </div>

        <div class="panel panel-default attendance-calendar-panel">
            <div class="panel-heading"><?php echo date('F Y', strtotime($periodStart)); ?> Attendance</div>
            <div class="panel-body table-responsive">
                <table class="table no-datatable attendance-calendar-table">
                    <thead><tr><th>Date</th><th>Shift</th><th>Sign In</th><th>Lunch In</th><th>Lunch Out</th><th>Break In</th><th>Break Out</th><th>Sign Out</th><th>Effective</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($calendarRows as $row): $session = $row['session']; ?>
                        <tr class="attendance-day-<?php echo oecrm_h($row['status_key']); ?>">
                            <td><strong><?php echo date('d M Y', strtotime($row['date'])); ?></strong><small><?php echo date('l', strtotime($row['date'])); ?></small></td>
                            <td><?php echo $row['shift'] ? oecrm_h($row['shift']['name']) : '—'; ?></td>
                            <td><?php echo attendance_event_times($row['events'], 'sign_in'); ?></td>
                            <td><?php echo attendance_event_times($row['events'], 'lunch_in'); ?></td>
                            <td><?php echo attendance_event_times($row['events'], 'lunch_out'); ?></td>
                            <td><?php echo attendance_event_times($row['events'], 'break_in'); ?></td>
                            <td><?php echo attendance_event_times($row['events'], 'break_out'); ?></td>
                            <td><?php echo attendance_event_times($row['events'], 'sign_out'); ?></td>
                            <td><?php echo $session ? attendance_minutes($session['effective_minutes']) : '—'; ?></td>
                            <td><span class="attendance-day-badge <?php echo oecrm_h($row['status_key']); ?>"><?php echo oecrm_h($row['status_label']); ?></span></td>
                            <td>
                                <?php if ($session && !$isLocked && oecrm_can($conn, 'attendance_review', 'correct')): ?>
                                    <button class="icon-action correction-btn"
                                            data-id="<?php echo (int) $session['id']; ?>"
                                            data-in="<?php echo $session['actual_in'] ? date('Y-m-d\TH:i', strtotime($session['actual_in'])) : ''; ?>"
                                            data-out="<?php echo $session['actual_out'] ? date('Y-m-d\TH:i', strtotime($session['actual_out'])) : ''; ?>"
                                            data-break="<?php echo (int) $session['break_minutes']; ?>"
                                            title="Correct attendance"><i class="fa fa-pencil"></i></button>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($selectedEmployee): ?>
<div class="modal fade" id="manualEventModal">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="attendanceReviewAction.php">
            <?php echo oecrm_csrf_field(); ?>
            <input type="hidden" name="action" value="manual_event">
            <input type="hidden" name="employee_id" value="<?php echo $employeeId; ?>">
            <input type="hidden" name="return_query" value="<?php echo oecrm_h(http_build_query(['month' => $month, 'year' => $year, 'employee_id' => $employeeId])); ?>">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4>Add Attendance Event</h4></div>
            <div class="modal-body">
                <div class="alert alert-info"><strong><?php echo oecrm_h($selectedEmployee['name']); ?></strong><br>Use this only when the employee forgot an attendance action.</div>
                <div class="form-group"><label>Event</label><select class="form-control" name="event_type" required><option value="">Select event</option><option value="sign_in">Sign In</option><option value="lunch_in">Lunch In</option><option value="lunch_out">Lunch Out</option><option value="break_in">Break In</option><option value="break_out">Break Out</option><option value="sign_out">Sign Out</option></select></div>
                <div class="form-group"><label>Event Date & Time</label><input type="datetime-local" class="form-control" name="event_time" value="<?php echo date('Y-m-d\TH:i'); ?>" required></div>
                <div class="form-group"><label>Reason</label><textarea class="form-control" name="reason" minlength="5" required placeholder="Example: Employee forgot to mark Sign In"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button class="btn btn-primary"><i class="fa fa-save"></i> Add Event</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="correctionModal">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="attendanceReviewAction.php">
            <?php echo oecrm_csrf_field(); ?>
            <input type="hidden" name="action" value="correct">
            <input type="hidden" name="session_id" id="correctionSessionId">
            <input type="hidden" name="return_query" value="<?php echo oecrm_h(http_build_query(['month' => $month, 'year' => $year, 'employee_id' => $employeeId])); ?>">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4>Correct Attendance</h4></div>
            <div class="modal-body">
                <div class="form-group"><label>Sign In</label><input type="datetime-local" class="form-control" name="actual_in" id="correctionIn" required></div>
                <div class="form-group"><label>Sign Out</label><input type="datetime-local" class="form-control" name="actual_out" id="correctionOut"></div>
                <div class="form-group"><label>Break Minutes</label><input type="number" min="0" class="form-control" name="break_minutes" id="correctionBreak"></div>
                <div class="form-group"><label>Correction Reason *</label><textarea class="form-control" name="reason" required minlength="10"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button class="btn btn-primary">Save Correction</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="periodLockModal">
    <div class="modal-dialog modal-sm">
        <form class="modal-content" method="post" action="attendanceReviewAction.php">
            <?php echo oecrm_csrf_field(); ?>
            <input type="hidden" name="action" value="<?php echo $isLocked ? 'unlock' : 'lock'; ?>">
            <input type="hidden" name="year" value="<?php echo $year; ?>">
            <input type="hidden" name="month" value="<?php echo $month; ?>">
            <input type="hidden" name="return_query" value="<?php echo oecrm_h(http_build_query(['month' => $month, 'year' => $year, 'employee_id' => $employeeId])); ?>">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4><?php echo $isLocked ? 'Unlock' : 'Lock'; ?> Month</h4></div>
            <div class="modal-body"><div class="form-group"><label>Reason *</label><textarea class="form-control" name="reason" required></textarea></div></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button class="btn <?php echo $isLocked ? 'btn-warning' : 'btn-danger'; ?>"><?php echo $isLocked ? 'Unlock' : 'Lock'; ?></button></div>
        </form>
    </div>
</div>
<script>
document.querySelectorAll('.correction-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        document.getElementById('correctionSessionId').value = this.dataset.id;
        document.getElementById('correctionIn').value = this.dataset.in;
        document.getElementById('correctionOut').value = this.dataset.out;
        document.getElementById('correctionBreak').value = this.dataset.break;
        $('#correctionModal').modal('show');
    });
});
</script>
<?php endif; ?>
<?php if ($hasAttendanceFilter): ?>
<script>
if (window.history && window.history.replaceState) {
    window.history.replaceState({}, document.title, 'attendanceReview.php');
}
</script>
<?php endif; ?>
<?php include 'footer.php'; ?>
