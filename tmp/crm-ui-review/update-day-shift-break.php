<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
ini_set('session.save_path',__DIR__);
require __DIR__.'/../../dbconnect.php';
require __DIR__.'/../../foundation.php';
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($conn);
try{
 $rows=mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM shifts WHERE code='DAY' AND name='Day Shift' FOR UPDATE"),MYSQLI_ASSOC);
 if(count($rows)!==1)throw new RuntimeException('Expected exactly one Day Shift.');
 $old=$rows[0];$id=(int)$old['id'];
 $stmt=mysqli_prepare($conn,'UPDATE shifts SET required_minutes=510,break_minutes=0 WHERE id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
 $saved=mysqli_fetch_assoc(mysqli_query($conn,'SELECT id,name,start_time,end_time,required_minutes,break_minutes FROM shifts WHERE id='.$id));
 if((int)$saved['required_minutes']!==510||(int)$saved['break_minutes']!==0)throw new RuntimeException('Verification failed.');
 $_SESSION['company_id']=(int)$old['company_id'];
 oecrm_audit($conn,'shifts','update','shift',$id,'Day Shift: 8 hours 30 minutes required work, zero allowed break minutes',$old,$saved);
 mysqli_commit($conn);echo json_encode($saved),PHP_EOL;
}catch(Throwable $e){mysqli_rollback($conn);throw $e;}
