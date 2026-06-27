<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../attendance.php';

oecrm_require_employee_login();
$employeeId = (int) $_SESSION['employeeId'];
oecrm_handle_attendance_post($conn, $employeeId);

$now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
$todayDate = $now->format('Y-m-d');
$context = oecrm_attendance_context($conn, $employeeId, $now);
$attendanceDate = $context ? $context['date'] : $todayDate;
$today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM attendance_sessions WHERE employee_id=$employeeId AND attendance_date='" . mysqli_real_escape_string($conn, $attendanceDate) . "' LIMIT 1"));
$lastEvent = $today ? mysqli_fetch_assoc(mysqli_query($conn, 'SELECT event_type,event_time FROM attendance_events WHERE session_id=' . (int) $today['id'] . ' ORDER BY event_time DESC,id DESC LIMIT 1')) : null;

$month = preg_match('/^\d{4}-\d{2}$/', $_GET['month'] ?? '') ? $_GET['month'] : $now->format('Y-m');
$start = $month . '-01';
$end = date('Y-m-t', strtotime($start));

$sessions = [];
$stmt = mysqli_prepare($conn, 'SELECT s.*,sh.name shift_name,sh.required_minutes FROM attendance_sessions s LEFT JOIN shifts sh ON sh.id=s.shift_id WHERE s.employee_id=? AND s.attendance_date BETWEEN ? AND ?');
mysqli_stmt_bind_param($stmt, 'iss', $employeeId, $start, $end);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row=mysqli_fetch_assoc($result)) $sessions[$row['attendance_date']]=$row;
mysqli_stmt_close($stmt);

$events = [];
if ($sessions) {
    $sessionIds = implode(',', array_map(function($row){ return (int)$row['id']; }, $sessions));
    $eventResult = mysqli_query($conn, "SELECT session_id,event_type,event_time FROM attendance_events WHERE session_id IN ($sessionIds) ORDER BY event_time,id");
    while ($event=mysqli_fetch_assoc($eventResult)) $events[(int)$event['session_id']][$event['event_type']][]=$event['event_time'];
}

$calendarRows = [];
$cursor = new DateTime($start);
$last = new DateTime($end);
while ($cursor <= $last) {
    $date = $cursor->format('Y-m-d');
    $session = $sessions[$date] ?? null;
    $shift = oecrm_employee_shift($conn, $employeeId, $date);
    $sessionEvents = $session ? ($events[(int)$session['id']] ?? []) : [];
    $weeklyOff = $shift ? oecrm_shift_is_weekly_off($conn, $shift['id'], $date) : in_array((int)$cursor->format('w'), [0,6], true);
    $holiday = $shift ? oecrm_is_holiday($conn, $date, (int)$shift['company_id']) : false;
    $status = $session['attendance_status'] ?? ($holiday ? 'holiday' : ($weeklyOff ? 'weekly_off' : ($date < $todayDate ? 'absent' : ($date > $todayDate ? 'upcoming' : 'not_started'))));
    $breakIns = $sessionEvents['break_in'] ?? [];
    $breakOuts = $sessionEvents['break_out'] ?? [];
    $calendarRows[]=[
        'date'=>$date,'session'=>$session,'shift'=>$shift,'status'=>$status,
        'sign_in'=>$session['actual_in']??'',
        'lunch_in'=>$sessionEvents['lunch_in'][0]??'',
        'lunch_out'=>$sessionEvents['lunch_out'][0]??'',
        'break_ins'=>$breakIns,'break_outs'=>$breakOuts,
        'sign_out'=>$session['actual_out']??''
    ];
    $cursor->modify('+1 day');
}

function attendance_time($value)
{
    if (!$value || $value === '00:00') return '00:00';
    return date('H:i', strtotime($value));
}

function attendance_times(array $values)
{
    if (!$values) return '';
    return implode('<br>', array_map('attendance_time', $values));
}

$flash = $_SESSION['attendance_flash'] ?? '';
$requiresReason = !empty($_SESSION['attendance_requires_reason']);
unset($_SESSION['attendance_flash'], $_SESSION['attendance_requires_reason']);
$monthOptions = [];
$monthCursor = new DateTime($now->format('Y-m-01'));
$monthCursor->modify('-12 months');
for ($i = 0; $i < 25; $i++) {
    $value = $monthCursor->format('Y-m');
    $monthOptions[$value] = $monthCursor->format('F Y');
    $monthCursor->modify('+1 month');
}
include 'header.php';
?>
<style>
.classic-attendance{border-top:4px solid #2f71aa}.classic-attendance .panel-heading{background:#fff;color:#27659b;font-size:20px;font-weight:700;padding:15px}
.classic-attendance-table{margin:0;font-weight:600}.classic-attendance-table thead th{background:#2f71aa!important;color:#fff;text-align:center;padding:14px!important;border-color:#d7e0e8!important}
.classic-attendance-table tbody td{text-align:center;vertical-align:middle!important;padding:12px 8px!important}
.classic-attendance-table tbody tr.absent-day td{background:#e85d63!important;color:#fff}
.classic-attendance-table tbody tr.incomplete-day td{background:#f8c8ca!important;color:#7d2529}
.classic-attendance-table tbody tr.worked-off-day td{background:#d9f2e6!important;color:#236445}
.classic-attendance-table tbody tr.off-day td{background:#f2f5f8!important;color:#667085}
.classic-attendance-table tbody tr.upcoming-day td{background:#fff!important;color:#98a2b3}
.classic-attendance-table .btn{border-radius:0;padding:7px 14px}.attendance-month-filter{float:right;margin-top:-5px}
@media(max-width:767px){.classic-attendance-table{min-width:900px}.attendance-month-filter{float:none;margin:10px 0 0}}
</style>
<div id="page-wrapper">
    <?php if($flash): ?><div class="alert alert-info"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="panel panel-default classic-attendance">
        <div class="panel-heading">
            Attendance
            <form method="get" class="form-inline attendance-month-filter">
                <select class="form-control input-sm" name="month" data-oecrm-native="1">
                    <?php foreach ($monthOptions as $value => $label): ?><option value="<?php echo oecrm_h($value); ?>" <?php echo $month === $value ? 'selected' : ''; ?>><?php echo oecrm_h($label); ?></option><?php endforeach; ?>
                </select>
                <button class="btn btn-primary btn-sm">View</button>
            </form>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped classic-attendance-table no-datatable">
                <thead><tr><th>Date</th><th>Sign In</th><th>Lunch In</th><th>Lunch Out</th><th>Break In</th><th>Break Out</th><th>Sign Out</th></tr></thead>
                <tbody>
                <?php foreach($calendarRows as $row):
                    $isToday=$row['date']===$attendanceDate;
                    $session=$row['session'];
                    $required=(int)($row['shift']['required_minutes']??0);
                    $offDay=in_array($row['status'],['weekly_off','holiday'],true);
                    $workedOffDay=$offDay&&$session&&!empty($session['actual_in']);
                    $absentDay=!$offDay&&!$session&&$row['date']<$todayDate;
                    $incompleteDay=!$offDay&&$session&&!empty($session['actual_out'])&&$required>0&&(int)$session['effective_minutes']<$required;
                    $upcomingDay=$row['date']>$todayDate;
                    $rowClass=$workedOffDay?'worked-off-day':($absentDay?'absent-day':($incompleteDay?'incomplete-day':($offDay?'off-day':($upcomingDay?'upcoming-day':''))));
                ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td><?php echo date('j-n-Y',strtotime($row['date'])); ?></td>
                        <?php if($offDay&&!$session): ?>
                            <td><?php echo $row['status']==='holiday'?'Holiday':date('l',strtotime($row['date'])); ?></td><td></td><td></td><td></td><td></td><td></td>
                        <?php else: ?>
                            <td>
                                <?php if($row['sign_in']): echo attendance_time($row['sign_in']); elseif($isToday&&$context): ?>
                                    <form method="post"><?php echo oecrm_csrf_field(); ?><button class="btn btn-success btn-sm" name="signin">Login</button></form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['lunch_in']): echo attendance_time($row['lunch_in']); elseif($isToday&&$session&&!$session['actual_out']&&!in_array($lastEvent['event_type']??'',['lunch_in','break_in'],true)): ?>
                                    <form method="post"><?php echo oecrm_csrf_field(); ?><button class="btn btn-info btn-sm" name="lunchin">Lunch In</button></form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['lunch_out']): echo attendance_time($row['lunch_out']); elseif($isToday&&($lastEvent['event_type']??'')==='lunch_in'): ?>
                                    <form method="post"><?php echo oecrm_csrf_field(); ?><button class="btn btn-success btn-sm" name="lunchout">Lunch Out</button></form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['break_ins']): ?><div><?php echo attendance_times($row['break_ins']); ?></div><?php endif; ?>
                                <?php if($isToday&&$session&&!$session['actual_out']&&!in_array($lastEvent['event_type']??'',['lunch_in','break_in'],true)): ?>
                                    <form method="post"><?php echo oecrm_csrf_field(); ?><button class="btn btn-info btn-sm" name="breakin">Break In</button></form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['break_outs']): ?><div><?php echo attendance_times($row['break_outs']); ?></div><?php endif; ?>
                                <?php if($isToday&&($lastEvent['event_type']??'')==='break_in'): ?>
                                    <form method="post"><?php echo oecrm_csrf_field(); ?><button class="btn btn-warning btn-sm" name="breakout">Break Out</button></form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['sign_out']): echo attendance_time($row['sign_out']); elseif($isToday&&$session&&!in_array($lastEvent['event_type']??'',['lunch_in','break_in'],true)): ?>
                                    <form method="post" data-confirm="Logout for today?"><?php echo oecrm_csrf_field(); ?><button class="btn btn-danger btn-sm" name="logoutBtn">Logout</button></form>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if($requiresReason): ?>
<div class="modal fade" id="lateReasonModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post"><div class="modal-header"><h4>Late Sign-in Reason</h4></div><div class="modal-body"><?php echo oecrm_csrf_field(); ?><textarea class="form-control" name="reason" minlength="10" required></textarea></div><div class="modal-footer"><button class="btn btn-primary" name="latesignIn">Continue Sign In</button></div></form></div></div></div>
<script>document.addEventListener('DOMContentLoaded',function(){jQuery('#lateReasonModal').modal({backdrop:'static',keyboard:false});});</script>
<?php endif; ?>
<?php include 'footer.php'; ?>
