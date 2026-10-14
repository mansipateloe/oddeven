<?php
require_once __DIR__ . '/../dbconnect.php';
$queries = [
 'SELECT id,name,company_id,status,start_time,end_time,required_minutes,grace_in_minutes,latest_checkout_minutes,crosses_midnight FROM shifts WHERE status=1 ORDER BY company_id,id',
 'SHOW COLUMNS FROM attendance_events',
 'SELECT attendance_status,review_status,COUNT(*) count FROM attendance_sessions WHERE employee_id=52 GROUP BY attendance_status,review_status',
 'SELECT id,employee_id,attendance_date,shift_id,scheduled_start,scheduled_end,actual_in,actual_out,total_minutes,break_minutes,effective_minutes,attendance_status,review_status FROM attendance_sessions WHERE employee_id=52 ORDER BY attendance_date LIMIT 5',
 'SELECT sh.id,sh.name,COUNT(DISTINCT a.employee_id) employees,MIN(a.effective_from) earliest_assignment,MAX(a.effective_from) latest_assignment FROM shifts sh JOIN employee_shift_assignments a ON a.shift_id=sh.id AND a.status=1 WHERE sh.company_id=1 GROUP BY sh.id,sh.name ORDER BY sh.id',
 'SELECT employeeCode,designation,department_id,joiningDate FROM employeestbl WHERE company_id=1 AND status=0 ORDER BY employeeCode',
 'SELECT employee_id,COUNT(*) sessions,MIN(attendance_date) first_day,MAX(attendance_date) last_day FROM attendance_sessions WHERE attendance_date BETWEEN "2026-01-01" AND "2026-10-13" GROUP BY employee_id ORDER BY employee_id'
];
foreach ($queries as $sql) {
 echo '## ' . $sql . PHP_EOL;
 $result=mysqli_query($conn,$sql);
 if (!$result) { echo 'ERROR: '.mysqli_error($conn).PHP_EOL; continue; }
 while ($row=mysqli_fetch_assoc($result)) echo json_encode($row,JSON_UNESCAPED_SLASHES).PHP_EOL;
}
