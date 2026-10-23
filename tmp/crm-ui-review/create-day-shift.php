<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__.'/../../dbconnect.php';
require __DIR__.'/../../foundation.php';
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
$company=mysqli_fetch_assoc(mysqli_query($conn,'SELECT id,display_name FROM companies WHERE status=1 ORDER BY id LIMIT 1'));
if (!$company) throw new RuntimeException('No active company found.');
$companyId=(int)$company['id'];
mysqli_begin_transaction($conn);
try {
 $check=mysqli_prepare($conn,"SELECT id FROM shifts WHERE company_id=? AND (code='DAY' OR LOWER(name)='day shift') LIMIT 1 FOR UPDATE");
 mysqli_stmt_bind_param($check,'i',$companyId);mysqli_stmt_execute($check);
 $existing=mysqli_fetch_assoc(mysqli_stmt_get_result($check));mysqli_stmt_close($check);
 if ($existing) throw new RuntimeException('Day Shift already exists; no duplicate created.');
 $stmt=mysqli_prepare($conn,"INSERT INTO shifts(company_id,name,code,shift_type,start_time,end_time,crosses_midnight,required_minutes,break_minutes,grace_in_minutes,grace_out_minutes,status) VALUES(?,'Day Shift','DAY','general','10:00:00','19:30:00',0,510,60,0,0,1)");
 mysqli_stmt_bind_param($stmt,'i',$companyId);mysqli_stmt_execute($stmt);$shiftId=(int)mysqli_insert_id($conn);mysqli_stmt_close($stmt);
 $stmt=mysqli_prepare($conn,'INSERT INTO shift_weekly_off_rules(shift_id,weekday,week_of_month,is_off) VALUES(?,?,?,1)');
 foreach([[0,0],[6,1],[6,3],[6,5]] as [$weekday,$week]) { mysqli_stmt_bind_param($stmt,'iii',$shiftId,$weekday,$week);mysqli_stmt_execute($stmt); }
 mysqli_stmt_close($stmt);
 // Verify the exact saved rules before committing.
 $rules=mysqli_fetch_all(mysqli_query($conn,'SELECT weekday,week_of_month FROM shift_weekly_off_rules WHERE shift_id='.$shiftId.' AND is_off=1 ORDER BY weekday,week_of_month'),MYSQLI_NUM);
 if (array_map(fn($r)=>array_map('intval',$r),$rules)!==[[0,0],[6,1],[6,3],[6,5]]) throw new RuntimeException('Weekly off verification failed.');
 $_SESSION['company_id']=$companyId;
 oecrm_audit($conn,'shifts','create','shift',$shiftId,'Created Day Shift with Sunday and 1st, 3rd, 5th Saturday off',null,['name'=>'Day Shift','code'=>'DAY','start_time'=>'10:00','end_time'=>'19:30','weekly_off'=>['0_0','6_1','6_3','6_5']]);
 mysqli_commit($conn);
 echo json_encode(['created'=>true,'shift_id'=>$shiftId,'company'=>$company['display_name'],'name'=>'Day Shift','time'=>'10:00-19:30','weekly_off'=>$rules]),PHP_EOL;
} catch (Throwable $e) { mysqli_rollback($conn);throw $e; }
foreach(['2026-10-03'=>true,'2026-10-10'=>false,'2026-10-17'=>true,'2026-10-24'=>false,'2026-10-31'=>true,'2026-10-04'=>true,'2026-10-05'=>false] as $date=>$expected) {
 $day=new DateTime($date);$weekday=(int)$day->format('w');$week=(int)ceil((int)$day->format('j')/7);
 $row=mysqli_fetch_row(mysqli_query($conn,"SELECT 1 FROM shift_weekly_off_rules WHERE shift_id=$shiftId AND weekday=$weekday AND is_off=1 AND week_of_month IN (0,$week) LIMIT 1"));
 if ((bool)$row!==$expected) throw new RuntimeException('Date verification failed: '.$date);
 echo 'PASS: '.$date.' '.($expected?'Off':'Working'),PHP_EOL;
}
