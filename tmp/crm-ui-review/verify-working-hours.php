<?php
require __DIR__.'/../../dbconnect.php';
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
mysqli_query($conn,'CREATE TEMPORARY TABLE attendance_events (id INT AUTO_INCREMENT PRIMARY KEY, session_id INT,event_type VARCHAR(30),event_time DATETIME)');
$source=file_get_contents(__DIR__.'/../../attendance.php');
$start=strpos($source,'function oecrm_attendance_break_minutes(');
$end=strpos($source,'function oecrm_validate_attendance_timeline(', $start);
eval(substr($source,$start,$end-$start));
mysqli_query($conn,"INSERT INTO attendance_events(session_id,event_type,event_time) VALUES (1,'sign_in','2026-10-23 10:00:00'),(1,'lunch_in','2026-10-23 13:00:00'),(1,'lunch_out','2026-10-23 15:00:00')");
$shift=mysqli_fetch_assoc(mysqli_query($conn,"SELECT required_minutes,break_minutes FROM shifts WHERE code='DAY' AND name='Day Shift' LIMIT 1"));
if(!$shift||(int)$shift['required_minutes']!==510)throw new Exception('Day Shift requirement must be 510 minutes.');
foreach(['2026-10-23 18:30:00'=>390,'2026-10-23 20:30:00'=>510] as $until=>$expected){
 $end=new DateTime($until);$break=oecrm_attendance_break_minutes($conn,1,$end);
 $total=(int)(($end->getTimestamp()-(new DateTime('2026-10-23 10:00:00'))->getTimestamp())/60);
 $effective=max(0,$total-$break);
 if($break!==120||$effective!==$expected)throw new Exception('Break exclusion failed');
 echo json_encode(['sign_out'=>$until,'lunch_minutes'=>$break,'actual_work_minutes'=>$effective,'required_work_minutes'=>(int)$shift['required_minutes'],'completed'=>$effective>=(int)$shift['required_minutes']]),PHP_EOL;
}
mysqli_close($conn);
