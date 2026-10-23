<?php
require __DIR__.'/../../dbconnect.php';
$r=mysqli_query($conn,"SELECT id,name,start_time,end_time,crosses_midnight,required_minutes,break_minutes,status FROM shifts WHERE code='NIGHT'");
$shift=mysqli_fetch_assoc($r);
if (!$shift || $shift['start_time']!=='18:30:00' || $shift['end_time']!=='02:30:00' || (int)$shift['crosses_midnight']!==1 || (int)$shift['required_minutes']!==480 || (int)$shift['break_minutes']!==0) throw new Exception('Night shift verification failed');
echo json_encode($shift),PHP_EOL;
