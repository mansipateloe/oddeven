<?php
require_once __DIR__.'/attendance.php';
require_once __DIR__.'/employee_continuations.php';

// $start = $_POST['start_date'] ?? '';
// $end   = $_POST['end_date'] ?? '';

// if ($start === '' || $end === '') {
//     throw new RuntimeException('Start date and end date are required.');
// }

// if ($end < $start) {
//     throw new RuntimeException('End date cannot be before start date.');
// }

function oecrm_leave_policy($conn,$companyId,$leaveTypeId){$stmt=mysqli_prepare($conn,'SELECT p.*,s.comp_off_expiry_days,s.minimum_comp_off_minutes,s.sandwich_enabled company_sandwich FROM leave_policies p JOIN leave_settings s ON s.company_id=p.company_id WHERE p.company_id=? AND p.leave_type_id=? AND p.status=1');mysqli_stmt_bind_param($stmt,'ii',$companyId,$leaveTypeId);mysqli_stmt_execute($stmt);$r=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);return $r;}
function oecrm_employee_leave_period($conn,$employeeId,$date)
{
    $target=DateTimeImmutable::createFromFormat('!Y-m-d',$date);
    $dateErrors=DateTimeImmutable::getLastErrors();
    if(!$target||($dateErrors&&($dateErrors['warning_count']||$dateErrors['error_count'])))throw new DomainException('A valid leave date is required.');
    $stmt=mysqli_prepare($conn,'SELECT e.company_id,e.joiningDate,p.exit_date FROM employeestbl e LEFT JOIN employee_profiles p ON p.employee_id=e.id WHERE e.id=?');
    mysqli_stmt_bind_param($stmt,'i',$employeeId);
    mysqli_stmt_execute($stmt);
    $employee=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if(!$employee)throw new DomainException('Employee not found.');
    $joining=DateTimeImmutable::createFromFormat('!Y-m-d',(string)$employee['joiningDate']);
    if(!$joining||$target<$joining)throw new DomainException('A valid joining date is required before leave can be requested.');
    if(!empty($employee['exit_date'])&&$date>$employee['exit_date'])throw new DomainException('Leave date is after the employee exit date.');

    $historyStmt=mysqli_prepare($conn,'SELECT previous_to,renewed_from,renewed_to FROM employee_continuation_history WHERE employee_id=? ORDER BY term_no,id');
    mysqli_stmt_bind_param($historyStmt,'i',$employeeId);
    mysqli_stmt_execute($historyStmt);
    $history=mysqli_stmt_get_result($historyStmt);
    $terms=[];
    while($term=mysqli_fetch_assoc($history))$terms[]=$term;
    mysqli_stmt_close($historyStmt);

    $periodStart=null;
    $periodEnd=null;
    $fullPeriodStart=null;
    $contractEnd=null;
    foreach($terms as $term){
        if($date>=$term['renewed_from']&&$date<=$term['renewed_to']){
            $periodStart=new DateTimeImmutable($term['renewed_from']);
            $fullPeriodStart=$periodStart;
            $periodEnd=new DateTimeImmutable($term['renewed_to']);
            $contractEnd=$term['renewed_to'];
            break;
        }
    }
    if(!$periodStart&&$terms){
        $first=$terms[0];
        $initialEnd=$first['previous_to']?:((new DateTimeImmutable($first['renewed_from']))->modify('-1 day')->format('Y-m-d'));
        if($date>=$joining->format('Y-m-d')&&$date<=$initialEnd){
            $periodStart=$joining;
            $fullPeriodStart=$periodStart;
            $periodEnd=new DateTimeImmutable($initialEnd);
            $contractEnd=$initialEnd;
        }else{
            $latest=$terms[count($terms)-1];
            if($date>$latest['renewed_to'])throw new DomainException('Leave date is after the current contract end date.');
            throw new DomainException('Leave date is outside the recorded contract periods.');
        }
    }

    if(!$periodStart){
        $stmt=mysqli_prepare($conn,'SELECT start_date,end_date FROM employee_contracts WHERE employee_id=? AND contract_type="employment" AND status="active" AND (start_date IS NULL OR start_date<=?) AND (end_date IS NULL OR end_date>=?) ORDER BY start_date DESC,id DESC LIMIT 1');
        mysqli_stmt_bind_param($stmt,'iss',$employeeId,$date,$date);
        mysqli_stmt_execute($stmt);
        $contract=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if(!$contract)throw new DomainException('No active employment contract covers this leave date.');
        $periodStart=$joining;
        while($periodStart->modify('+1 year')<=$target)$periodStart=$periodStart->modify('+1 year');
        while($periodStart>$target)$periodStart=$periodStart->modify('-1 year');
        $fullPeriodStart=$periodStart;
        $periodEnd=$periodStart->modify('+1 year')->modify('-1 day');
        if(!empty($contract['start_date'])&&$contract['start_date']>$periodStart->format('Y-m-d'))$periodStart=new DateTimeImmutable($contract['start_date']);
        if(!empty($contract['end_date'])){
            $contractEnd=$contract['end_date'];
            if($contract['end_date']<$periodEnd->format('Y-m-d'))$periodEnd=new DateTimeImmutable($contract['end_date']);
        }
    }
    if(!empty($employee['exit_date'])&&$employee['exit_date']<$periodEnd->format('Y-m-d')){
        $periodEnd=new DateTimeImmutable($employee['exit_date']);
        $contractEnd=$contractEnd?min($contractEnd,$employee['exit_date']):$employee['exit_date'];
    }
    if($date<$periodStart->format('Y-m-d')||$date>$periodEnd->format('Y-m-d'))throw new DomainException('Leave date is outside the active contract period.');
    $fullPeriodEnd=$fullPeriodStart->modify('+1 year')->modify('-1 day');
    $fullPeriodDays=$fullPeriodStart->diff($fullPeriodEnd)->days+1;
    $contractPeriodDays=$periodStart->diff($periodEnd)->days+1;
    return [
        'start_date'=>$periodStart->format('Y-m-d'),
        'end_date'=>$periodEnd->format('Y-m-d'),
        'contract_end'=>$contractEnd,
        'portion'=>min(1,$contractPeriodDays/max(1,$fullPeriodDays)),
        'company_id'=>(int)$employee['company_id']
    ];
}
function oecrm_paid_leave_balance($conn,$employeeId,$date)
{
    $period=oecrm_employee_leave_period($conn,$employeeId,$date);
    $stmt=mysqli_prepare($conn,'SELECT paid_leave_per_year FROM leave_settings WHERE company_id=?');
    mysqli_stmt_bind_param($stmt,'i',$period['company_id']);
    mysqli_stmt_execute($stmt);
    $settings=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if(!$settings)throw new DomainException('Paid leave settings are not configured for this employee.');
    $stmt=mysqli_prepare($conn,'SELECT r.status,COALESCE(SUM(GREATEST(0,r.payable_days-r.unpaid_days)),0) days FROM leave_requests r JOIN leave_policies p ON p.company_id=r.company_id AND p.leave_type_id=r.leave_type_id WHERE r.employee_id=? AND r.company_id=? AND p.is_paid=1 AND r.status IN ("approved","pending") AND r.start_date BETWEEN ? AND ? GROUP BY r.status');
    mysqli_stmt_bind_param($stmt,'iiss',$employeeId,$period['company_id'],$period['start_date'],$period['end_date']);
    mysqli_stmt_execute($stmt);
    $rows=mysqli_stmt_get_result($stmt);
    $used=0.0;
    $pending=0.0;
    while($row=mysqli_fetch_assoc($rows)){
        if($row['status']==='approved')$used=(float)$row['days'];
        else $pending=(float)$row['days'];
    }
    mysqli_stmt_close($stmt);
    $entitlement=(float)$settings['paid_leave_per_year']*$period['portion'];
    return [
        'entitlement'=>$entitlement,
        'used'=>$used,
        'pending'=>$pending,
        'available'=>max(0,$entitlement-$used-$pending),
        'period_start'=>$period['start_date'],
        'period_end'=>$period['end_date'],
        'period'=>$period
    ];
}
function oecrm_leave_calculate($conn,$employeeId,$leaveTypeId,$start,$end,$halfDay=false)
{
    $employee=mysqli_fetch_assoc(mysqli_query($conn,'SELECT company_id FROM employeestbl WHERE id='.(int)$employeeId));
    if(!$employee)throw new DomainException('Employee not found.');
    $policy=oecrm_leave_policy($conn,$employee['company_id'],$leaveTypeId);
    if(!$policy)throw new DomainException('Leave policy not configured.');
    $from=new DateTime($start);
    $to=new DateTime($end);
    if($to<$from)throw new DomainException('End date must be after start date.');
    $days=[];
    $cursor=clone $from;
    $firstWorking=null;
    $lastWorking=null;
    while($cursor<=$to){
        $date=$cursor->format('Y-m-d');
        $shift=oecrm_employee_shift($conn,$employeeId,$date);
        $isOff=oecrm_is_holiday($conn,$date,(int)$employee['company_id'])||($shift&&oecrm_shift_is_weekly_off($conn,$shift['id'],$date));
        $days[$date]=$isOff?'off':'working';
        if(!$isOff){
            $firstWorking=$firstWorking??$date;
            $lastWorking=$date;
        }
        $cursor->modify('+1 day');
    }
    $working=$halfDay
        ? (($days[$start]??'off')==='working'?0.5:0)
        : count(array_filter($days,static function($kind){return $kind==='working';}));
    $sandwich=0;
    if(!$halfDay&&!empty($policy['sandwich_enabled'])&&!empty($policy['company_sandwich'])&&$firstWorking!==null){
        foreach($days as $date=>$kind)if($kind==='off'&&$date>$firstWorking&&$date<$lastWorking)$sandwich++;
    }
    $requested=$working;
    return [
        'requested_days'=>$requested,
        'sandwich_days'=>$sandwich,
        'payable_days'=>$requested+$sandwich,
        'policy'=>$policy
    ];
}
function oecrm_leave_balance($conn,$employeeId,$leaveTypeId,$year){$stmt=mysqli_prepare($conn,'SELECT *,(opening_balance+credited+adjusted-used-pending) available FROM employee_leave_balances WHERE employee_id=? AND leave_type_id=? AND balance_year=?');mysqli_stmt_bind_param($stmt,'iii',$employeeId,$leaveTypeId,$year);mysqli_stmt_execute($stmt);$r=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);return $r;}
function oecrm_generate_comp_off_credits($conn,$companyId,$year,$month,$actor=0){$settings=mysqli_fetch_assoc(mysqli_query($conn,'SELECT * FROM leave_settings WHERE company_id='.(int)$companyId));if(!$settings)return 0;$start=sprintf('%04d-%02d-01',$year,$month);$end=date('Y-m-t',strtotime($start));$stmt=mysqli_prepare($conn,'SELECT s.* FROM attendance_sessions s JOIN employeestbl e ON e.id=s.employee_id WHERE e.company_id=? AND s.attendance_date BETWEEN ? AND ? AND s.actual_out IS NOT NULL AND s.effective_minutes>=?');mysqli_stmt_bind_param($stmt,'issi',$companyId,$start,$end,$settings['minimum_comp_off_minutes']);mysqli_stmt_execute($stmt);$rows=mysqli_stmt_get_result($stmt);$count=0;while($r=mysqli_fetch_assoc($rows)){$shift=oecrm_employee_shift($conn,$r['employee_id'],$r['attendance_date']);$weekly=$shift?oecrm_shift_is_weekly_off($conn,$shift['id'],$r['attendance_date']):false;$holiday=oecrm_is_holiday($conn,$r['attendance_date'],$companyId);if(!$weekly&&!$holiday)continue;$source=$holiday?'holiday_work':'weekly_off_work';$existsStmt=mysqli_prepare($conn,'SELECT id FROM comp_off_ledger WHERE employee_id=? AND source_type=? AND source_date=? AND status<>"cancelled"');mysqli_stmt_bind_param($existsStmt,'iss',$r['employee_id'],$source,$r['attendance_date']);mysqli_stmt_execute($existsStmt);$exists=mysqli_fetch_assoc(mysqli_stmt_get_result($existsStmt));mysqli_stmt_close($existsStmt);if($exists)continue;$credit=$r['effective_minutes']>=((int)$shift['required_minutes'])?1.0:0.5;$expiry=date('Y-m-d',strtotime($r['attendance_date'].' +'.(int)$settings['comp_off_expiry_days'].' days'));$notes=ucwords(str_replace('_',' ',$source)).' attendance credit';$ins=mysqli_prepare($conn,'INSERT INTO comp_off_ledger(employee_id,source_type,source_date,credit_days,expiry_date,reference_type,reference_id,notes,created_by) VALUES(?,?,?,?,?,"attendance_session",?,?,?)');mysqli_stmt_bind_param($ins,'issdsisi',$r['employee_id'],$source,$r['attendance_date'],$credit,$expiry,$r['id'],$notes,$actor);mysqli_stmt_execute($ins);mysqli_stmt_close($ins);$count++;}mysqli_stmt_close($stmt);return $count;}
