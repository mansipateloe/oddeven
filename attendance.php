<?php
require_once __DIR__.'/foundation.php';

function oecrm_legacy_attendance_sync($conn,$employeeId,$date,$column,$value)
{
 $allowed=['signinTime','lunchinTime','lunchoutTime','breakinTime','breakoutTime','signoutTime','workHours'];if(!in_array($column,$allowed,true))return false;$employeeId=(int)$employeeId;$safeDate=mysqli_real_escape_string($conn,$date);$existing=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM workhourstbl WHERE employeeId=$employeeId AND workDate='$safeDate' LIMIT 1"));$safeValue=mysqli_real_escape_string($conn,$value);
 if($existing)mysqli_query($conn,"UPDATE workhourstbl SET `$column`='$safeValue' WHERE id=".(int)$existing['id']);
 else{$month=date('m',strtotime($date));$year=date('Y',strtotime($date));mysqli_query($conn,"INSERT INTO workhourstbl(employeeId,workDate,month,year,signinTime,lunchinTime,lunchoutTime,breakinTime,breakoutTime,signoutTime,workHours) VALUES($employeeId,'$safeDate','$month','$year','','','','','','','')");$id=mysqli_insert_id($conn);mysqli_query($conn,"UPDATE workhourstbl SET `$column`='$safeValue' WHERE id=".(int)$id);}
 return true;
}
function oecrm_is_holiday($conn,$date,$companyId=0)
{
 $stmt=mysqli_prepare($conn,'SELECT 1 FROM holidaytbl WHERE holidayDate=? AND company_id IN (0,?) ORDER BY company_id DESC LIMIT 1');mysqli_stmt_bind_param($stmt,'si',$date,$companyId);mysqli_stmt_execute($stmt);$yes=(bool)mysqli_fetch_row(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);return $yes;
}
function oecrm_employee_shift($conn,$employeeId,$date)
{
 $stmt=mysqli_prepare($conn,'SELECT s.* FROM employee_shift_assignments a JOIN shifts s ON s.id=a.shift_id WHERE a.employee_id=? AND a.status=1 AND a.effective_from<=? AND (a.effective_to IS NULL OR a.effective_to>=?) AND s.status=1 ORDER BY a.effective_from DESC,a.id DESC LIMIT 1');
 mysqli_stmt_bind_param($stmt,'iss',$employeeId,$date,$date);mysqli_stmt_execute($stmt);$shift=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);return $shift;
}
function oecrm_shift_schedule($shift,$date)
{
 $start=new DateTime($date.' '.$shift['start_time']);$end=new DateTime($date.' '.$shift['end_time']);if((int)$shift['crosses_midnight']||$end<=$start)$end->modify('+1 day');return [$start,$end];
}
function oecrm_shift_is_weekly_off($conn,$shiftId,$date)
{
 $dt=new DateTime($date);$weekday=(int)$dt->format('w');$week=(int)ceil(((int)$dt->format('j'))/7);$stmt=mysqli_prepare($conn,'SELECT 1 FROM shift_weekly_off_rules WHERE shift_id=? AND weekday=? AND is_off=1 AND week_of_month IN (0,?) LIMIT 1');mysqli_stmt_bind_param($stmt,'iii',$shiftId,$weekday,$week);mysqli_stmt_execute($stmt);$off=(bool)mysqli_fetch_row(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);return $off;
}
function oecrm_attendance_context($conn,$employeeId,DateTime $now)
{
 $today=$now->format('Y-m-d');$shift=oecrm_employee_shift($conn,$employeeId,$today);
 if(!$shift){return null;}
 list($start,$end)=oecrm_shift_schedule($shift,$today);
 if((int)$shift['crosses_midnight']&&$now<$start){$previous=(clone $now)->modify('-1 day')->format('Y-m-d');$previousShift=oecrm_employee_shift($conn,$employeeId,$previous);if($previousShift){list($ps,$pe)=oecrm_shift_schedule($previousShift,$previous);$latest=(clone $pe)->modify('+'.(int)$previousShift['latest_checkout_minutes'].' minutes');if($now<=$latest){$today=$previous;$shift=$previousShift;$start=$ps;$end=$pe;}}}
 return ['date'=>$today,'shift'=>$shift,'start'=>$start,'end'=>$end,'weekly_off'=>oecrm_shift_is_weekly_off($conn,$shift['id'],$today),'holiday'=>oecrm_is_holiday($conn,$today,(int)$shift['company_id'])];
}
function oecrm_attendance_event($conn,$sessionId,$employeeId,$type,DateTime $now,$notes='')
{
 $ip=$_SERVER['REMOTE_ADDR']??'';$agent=substr($_SERVER['HTTP_USER_AGENT']??'',0,500);$time=$now->format('Y-m-d H:i:s');$stmt=mysqli_prepare($conn,'INSERT INTO attendance_events(session_id,employee_id,event_type,event_time,ip_address,user_agent,notes) VALUES(?,?,?,?,?,?,?)');mysqli_stmt_bind_param($stmt,'iisssss',$sessionId,$employeeId,$type,$time,$ip,$agent,$notes);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
}
function oecrm_attendance_break_minutes($conn,$sessionId,DateTime $until)
{
 $stmt=mysqli_prepare($conn,"SELECT event_type,event_time FROM attendance_events WHERE session_id=? AND event_type IN ('lunch_in','lunch_out','break_in','break_out') ORDER BY event_time,id");mysqli_stmt_bind_param($stmt,'i',$sessionId);mysqli_stmt_execute($stmt);$r=mysqli_stmt_get_result($stmt);$open=[];$minutes=0;while($e=mysqli_fetch_assoc($r)){$time=new DateTime($e['event_time']);$group=strpos($e['event_type'],'lunch_')===0?'lunch':'break';if(substr($e['event_type'],-3)==='_in'&&!isset($open[$group]))$open[$group]=$time;elseif(substr($e['event_type'],-4)==='_out'&&isset($open[$group])){$minutes+=(int)floor(($time->getTimestamp()-$open[$group]->getTimestamp())/60);unset($open[$group]);}}foreach($open as $time)$minutes+=(int)floor(($until->getTimestamp()-$time->getTimestamp())/60);mysqli_stmt_close($stmt);return max(0,$minutes);
}
function oecrm_validate_attendance_timeline($events)
{
 $state='not_started';$lunchOpen=false;$breakOpen=false;$signInCount=0;$signOutCount=0;
 foreach($events as $event){$type=$event['event_type'];
  if($type==='sign_in'){if($state!=='not_started'||++$signInCount>1)throw new RuntimeException('Sign In must be the first attendance event.');$state='working';}
  elseif($type==='lunch_in'){if($state!=='working'||$lunchOpen||$breakOpen)throw new RuntimeException('Lunch In requires an active working session.');$lunchOpen=true;$state='lunch';}
  elseif($type==='lunch_out'){if(!$lunchOpen||$state!=='lunch')throw new RuntimeException('Lunch Out requires an open lunch.');$lunchOpen=false;$state='working';}
  elseif($type==='break_in'){if($state!=='working'||$lunchOpen||$breakOpen)throw new RuntimeException('Break In requires an active working session.');$breakOpen=true;$state='break';}
  elseif($type==='break_out'){if(!$breakOpen||$state!=='break')throw new RuntimeException('Break Out requires an open break.');$breakOpen=false;$state='working';}
  elseif($type==='sign_out'){if($state!=='working'||$lunchOpen||$breakOpen||++$signOutCount>1)throw new RuntimeException('Close lunch or break before Sign Out.');$state='signed_out';}
  elseif($type!=='manual_adjustment')throw new RuntimeException('Unsupported attendance event.');
 }
 if($signInCount!==1)throw new RuntimeException('Attendance timeline requires Sign In.');
 return ['state'=>$state,'lunch_open'=>$lunchOpen,'break_open'=>$breakOpen,'signed_out'=>$signOutCount===1];
}
function oecrm_recalculate_attendance_session($conn,$sessionId,$reviewStatus='corrected',$reviewedBy=0,$notes='')
{
 $session=mysqli_fetch_assoc(mysqli_query($conn,'SELECT s.*,sh.required_minutes FROM attendance_sessions s JOIN shifts sh ON sh.id=s.shift_id WHERE s.id='.(int)$sessionId));
 if(!$session)throw new RuntimeException('Attendance session not found.');
 $result=mysqli_query($conn,'SELECT event_type,event_time FROM attendance_events WHERE session_id='.(int)$sessionId.' ORDER BY event_time,id');
 $events=[];while($result&&($event=mysqli_fetch_assoc($result)))$events[]=$event;
 $timeline=oecrm_validate_attendance_timeline($events);$actualIn=null;$actualOut=null;$open=[];$breakMinutes=0;
 foreach($events as $event){$time=new DateTime($event['event_time']);$type=$event['event_type'];if($type==='sign_in')$actualIn=$time;elseif($type==='sign_out')$actualOut=$time;elseif(in_array($type,['lunch_in','break_in'],true))$open[strpos($type,'lunch_')===0?'lunch':'break']=$time;elseif(in_array($type,['lunch_out','break_out'],true)){$group=strpos($type,'lunch_')===0?'lunch':'break';if(isset($open[$group])){$breakMinutes+=max(0,(int)floor(($time->getTimestamp()-$open[$group]->getTimestamp())/60));unset($open[$group]);}}}
 $total=$actualIn&&$actualOut?max(0,(int)floor(($actualOut->getTimestamp()-$actualIn->getTimestamp())/60)):0;$effective=max(0,$total-$breakMinutes);$required=(int)$session['required_minutes'];$overtime=max(0,$effective-$required);$scheduledStart=new DateTime($session['scheduled_start']);$scheduledEnd=new DateTime($session['scheduled_end']);$late=$actualIn?max(0,(int)floor(($actualIn->getTimestamp()-$scheduledStart->getTimestamp())/60)):0;$early=$actualOut?max(0,(int)floor(($scheduledEnd->getTimestamp()-$actualOut->getTimestamp())/60)):0;
 if(!$actualOut)$status=$actualIn?'incomplete':'not_started';elseif(in_array($session['attendance_status'],['weekly_off','holiday'],true))$status=$session['attendance_status'];else $status=$effective>=($required/2)?($late>0?'late':'present'):'half_day';
 $inSql=$actualIn?$actualIn->format('Y-m-d H:i:s'):null;$outSql=$actualOut?$actualOut->format('Y-m-d H:i:s'):null;
 $stmt=mysqli_prepare($conn,'UPDATE attendance_sessions SET actual_in=?,actual_out=?,total_minutes=?,break_minutes=?,effective_minutes=?,late_minutes=?,early_exit_minutes=?,overtime_minutes=?,attendance_status=?,review_status=?,reviewed_by=?,reviewed_at=NOW(),review_notes=? WHERE id=?');
 mysqli_stmt_bind_param($stmt,'ssiiiiiissisi',$inSql,$outSql,$total,$breakMinutes,$effective,$late,$early,$overtime,$status,$reviewStatus,$reviewedBy,$notes,$sessionId);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
 return ['actual_in'=>$inSql,'actual_out'=>$outSql,'total_minutes'=>$total,'break_minutes'=>$breakMinutes,'effective_minutes'=>$effective,'late_minutes'=>$late,'early_exit_minutes'=>$early,'overtime_minutes'=>$overtime,'attendance_status'=>$status,'timeline'=>$timeline];
}
function oecrm_handle_attendance_post($conn,$employeeId)
{
 if($_SERVER['REQUEST_METHOD']!=='POST')return false;$map=['signin'=>'sign_in','latesignIn'=>'sign_in','lunchin'=>'lunch_in','lunchout'=>'lunch_out','breakin'=>'break_in','extraBreakin'=>'break_in','breakout'=>'break_out','extraBreakout'=>'break_out','logoutBtn'=>'sign_out'];$type=null;foreach($map as $key=>$event){if(isset($_POST[$key])){$type=$event;break;}}if(!$type)return false;
 oecrm_require_csrf();$now=new DateTime('now',new DateTimeZone('Asia/Kolkata'));$ctx=oecrm_attendance_context($conn,$employeeId,$now);if(!$ctx){$_SESSION['attendance_flash']='No active shift is assigned.';header('Location: dashboard.php');exit;}$shift=$ctx['shift'];$date=$ctx['date'];$reason=trim($_POST['reason']??'');
 $stmt=mysqli_prepare($conn,'SELECT * FROM attendance_sessions WHERE employee_id=? AND attendance_date=?');mysqli_stmt_bind_param($stmt,'is',$employeeId,$date);mysqli_stmt_execute($stmt);$session=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);
 if($type==='sign_in'){
  if($session&&$session['actual_in']){$_SESSION['attendance_flash']='You are already signed in.';header('Location: dashboard.php');exit;}$lateAfter=(clone $ctx['start'])->modify('+'.(int)$shift['grace_in_minutes'].' minutes');$late=max(0,(int)floor(($now->getTimestamp()-$lateAfter->getTimestamp())/60));if($late>0&&$reason===''&&!$ctx['weekly_off']&&!$ctx['holiday']){$_SESSION['attendance_requires_reason']=true;return false;}$status=$ctx['holiday']?'holiday':($ctx['weekly_off']?'weekly_off':($late>0?'late':'present'));$start=$ctx['start']->format('Y-m-d H:i:s');$end=$ctx['end']->format('Y-m-d H:i:s');$actual=$now->format('Y-m-d H:i:s');$ip=$_SERVER['REMOTE_ADDR']??'';$agent=substr($_SERVER['HTTP_USER_AGENT']??'',0,500);$stmt=mysqli_prepare($conn,'INSERT INTO attendance_sessions(employee_id,shift_id,attendance_date,scheduled_start,scheduled_end,actual_in,late_minutes,attendance_status,late_reason,ip_address,user_agent) VALUES(?,?,?,?,?,?,?,?,?,?,?)');mysqli_stmt_bind_param($stmt,'iissssissss',$employeeId,$shift['id'],$date,$start,$end,$actual,$late,$status,$reason,$ip,$agent);mysqli_stmt_execute($stmt);$sessionId=mysqli_insert_id($conn);mysqli_stmt_close($stmt);oecrm_attendance_event($conn,$sessionId,$employeeId,'sign_in',$now,$reason);$_SESSION['attendance_flash']=$ctx['holiday']?'Holiday work sign-in recorded.':($ctx['weekly_off']?'Weekly-off work sign-in recorded.':($late>0?'Late sign-in recorded.':'Sign-in successful.'));
 }else{
  if(!$session||!$session['actual_in']){$_SESSION['attendance_flash']='You need to sign in first.';header('Location: dashboard.php');exit;}$sessionId=(int)$session['id'];$last=mysqli_query($conn,"SELECT event_type FROM attendance_events WHERE session_id=$sessionId ORDER BY event_time DESC,id DESC LIMIT 1");$lastType=($last&&($lr=mysqli_fetch_assoc($last)))?$lr['event_type']:'';
  if($type==='lunch_in'){if(in_array($lastType,['lunch_in','break_in'],true)){$_SESSION['attendance_flash']='Finish the current break first.';}else{oecrm_attendance_event($conn,$sessionId,$employeeId,'lunch_in',$now);$_SESSION['attendance_flash']='Lunch started.';}}
  elseif($type==='lunch_out'){if($lastType!=='lunch_in'){$_SESSION['attendance_flash']='Start lunch first.';}else{oecrm_attendance_event($conn,$sessionId,$employeeId,'lunch_out',$now);$_SESSION['attendance_flash']='Lunch ended.';}}
  elseif($type==='break_in'){if(in_array($lastType,['break_in','lunch_in'],true)){$_SESSION['attendance_flash']='Finish the current break first.';}else{oecrm_attendance_event($conn,$sessionId,$employeeId,'break_in',$now);$_SESSION['attendance_flash']='Break started.';}}
  elseif($type==='break_out'){if($lastType!=='break_in'){$_SESSION['attendance_flash']='Start a break first.';}else{oecrm_attendance_event($conn,$sessionId,$employeeId,'break_out',$now);$_SESSION['attendance_flash']='Break ended.';}}
  elseif($type==='sign_out'){if(in_array($lastType,['break_in','lunch_in'],true)){$_SESSION['attendance_flash']='End your current break before signing out.';header('Location: dashboard.php');exit;}$break=oecrm_attendance_break_minutes($conn,$sessionId,$now);$total=max(0,(int)floor(($now->getTimestamp()-(new DateTime($session['actual_in']))->getTimestamp())/60));$effective=max(0,$total-$break);$required=(int)$shift['required_minutes'];$overtime=max(0,$effective-$required);$early=max(0,(int)floor(($ctx['end']->getTimestamp()-$now->getTimestamp())/60));$status=$effective>=($required/2)?($session['late_minutes']>0?'late':'present'):'half_day';$out=$now->format('Y-m-d H:i:s');$stmt=mysqli_prepare($conn,'UPDATE attendance_sessions SET actual_out=?,total_minutes=?,break_minutes=?,effective_minutes=?,early_exit_minutes=?,overtime_minutes=?,attendance_status=? WHERE id=?');mysqli_stmt_bind_param($stmt,'siiiiisi',$out,$total,$break,$effective,$early,$overtime,$status,$sessionId);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);oecrm_attendance_event($conn,$sessionId,$employeeId,'sign_out',$now);$_SESSION['attendance_flash']='Sign-out successful. Effective hours: '.sprintf('%02d:%02d',intdiv($effective,60),$effective%60);}
 }
 oecrm_audit($conn,'attendance',$type,'attendance_session',$sessionId??0,'Attendance event recorded',null,['employee_id'=>$employeeId,'date'=>$date,'event'=>$type]);header('Location: dashboard.php');exit;
}
