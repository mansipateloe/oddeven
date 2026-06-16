<?php
require_once __DIR__ . '/leave.php';

function oecrm_professional_tax($conn, $gross)
{
    $stmt = mysqli_prepare($conn, 'SELECT professionalTax FROM professionaltaxtbl WHERE ? BETWEEN startingAmount AND endingAmount ORDER BY startingAmount DESC LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'd', $gross);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $tax);
    $found = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $found ? (float) $tax : 0;
}

function oecrm_salary_structure($conn, $employeeId, $periodEnd)
{
    $stmt = mysqli_prepare($conn, 'SELECT * FROM salary_structures WHERE employee_id=? AND status=1 AND effective_from<=? AND (effective_to IS NULL OR effective_to>=?) ORDER BY effective_from DESC,id DESC LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'iss', $employeeId, $periodEnd, $periodEnd);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function oecrm_payroll_employee_calculation($conn, $employee, $year, $month)
{
    $start = sprintf('%04d-%02d-01', $year, $month);
    $end = date('Y-m-t', strtotime($start));
    $structure = oecrm_salary_structure($conn, $employee['id'], $end);
    if (!$structure) return null;

    $calendarDays = (int) date('t', strtotime($start));
    $workingDays = 0;
    $weeklyOffDays = 0;
    $holidayDays = 0;
    $requiredMinutes = 0;
    $cursor = new DateTime($start);
    $last = new DateTime($end);
    while ($cursor <= $last) {
        $date = $cursor->format('Y-m-d');
        $shift = oecrm_employee_shift($conn, $employee['id'], $date);
        if ($shift) {
            if (oecrm_is_holiday($conn, $date, (int) $employee['company_id'])) {
                $holidayDays++;
            } elseif (oecrm_shift_is_weekly_off($conn, $shift['id'], $date)) {
                $weeklyOffDays++;
            } else {
                $workingDays++;
                $requiredMinutes += (int) $shift['required_minutes'];
            }
        }
        $cursor->modify('+1 day');
    }

    $stmt = mysqli_prepare($conn, "SELECT
        COALESCE(SUM(CASE WHEN attendance_status='half_day' THEN .5 WHEN actual_in IS NOT NULL THEN 1 ELSE 0 END),0) present_days,
        COALESCE(SUM(effective_minutes),0) effective,
        COALESCE(SUM(overtime_minutes),0) overtime
        FROM attendance_sessions WHERE employee_id=? AND attendance_date BETWEEN ? AND ?");
    mysqli_stmt_bind_param($stmt, 'iss', $employee['id'], $start, $end);
    mysqli_stmt_execute($stmt);
    $attendance = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "SELECT
        COALESCE(SUM(CASE WHEN p.is_paid=1 THEN r.payable_days-r.unpaid_days ELSE 0 END),0) paid_leave,
        COALESCE(SUM(r.unpaid_days),0) unpaid_leave
        FROM leave_requests r
        JOIN leave_policies p ON p.company_id=r.company_id AND p.leave_type_id=r.leave_type_id
        WHERE r.employee_id=? AND r.status='approved' AND r.start_date<=? AND r.end_date>=?");
    mysqli_stmt_bind_param($stmt, 'iss', $employee['id'], $end, $start);
    mysqli_stmt_execute($stmt);
    $leave = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    $paidLeaveDays = (float) $leave['paid_leave'];
    $unpaidLeaveDays = (float) $leave['unpaid_leave'];
    $presentDays = (float) $attendance['present_days'];
    $payableDays = min($workingDays, $presentDays + $paidLeaveDays);
    $effectiveMinutes = (int) $attendance['effective'];
    $averageRequiredMinutes = $workingDays ? $requiredMinutes / $workingDays : 0;
    $paidLeaveMinutes = (int) round($paidLeaveDays * $averageRequiredMinutes);
    $unpaidLeaveMinutes = (int) round($unpaidLeaveDays * $averageRequiredMinutes);
    $attendanceRequiredMinutes = max(0, $requiredMinutes - $paidLeaveMinutes - $unpaidLeaveMinutes);
    $attendanceDeficitMinutes = max(0, $attendanceRequiredMinutes - $effectiveMinutes);
    $overtimeMinutes = max(0, $effectiveMinutes - $attendanceRequiredMinutes);
    $salaryMode = $structure['salary_mode'] ?? 'fixed';
    $overtimeRate = (float) ($structure['overtime_rate'] ?? 0);
    $overtimeAmount = ($structure['overtime_policy'] ?? 'tracking_only') === 'paid'
        ? round(($overtimeMinutes / 60) * $overtimeRate, 2)
        : 0;

    $attendanceDeduction = 0;
    $leaveDeduction = 0;
    if ($salaryMode === 'hourly') {
        $hourlyRate = (float) $structure['hourly_rate'];
        $gross = round((($effectiveMinutes + $paidLeaveMinutes) / 60) * $hourlyRate, 2);
    } else {
        $gross = (float) $structure['gross_salary'];
        $perDay = $workingDays > 0 ? $gross / $workingDays : 0;
        $perRequiredMinute = $requiredMinutes > 0 ? $gross / $requiredMinutes : 0;
        $attendanceDeduction = round($attendanceDeficitMinutes * $perRequiredMinute, 2);
        $leaveDeduction = round($unpaidLeaveDays * $perDay, 2);
    }

    $tax = !empty($structure['professional_tax_enabled']) ? oecrm_professional_tax($conn, $gross) : 0;
    $retention = 0;
    if ($structure['retention_type'] === 'fixed') $retention = (float) $structure['retention_value'];
    elseif ($structure['retention_type'] === 'percent') $retention = round($gross * ((float) $structure['retention_value'] / 100), 2);
    $totalDeduction = $attendanceDeduction + $leaveDeduction + $tax + $retention;
    $net = max(0, $gross + $overtimeAmount - $totalDeduction);

    return [
        'structure'=>$structure,
        'calendar_days'=>$calendarDays,
        'payable_days'=>$payableDays,
        'present_days'=>$presentDays,
        'paid_leave_days'=>$paidLeaveDays,
        'unpaid_leave_days'=>$unpaidLeaveDays,
        'weekly_off_days'=>$weeklyOffDays,
        'holiday_days'=>$holidayDays,
        'required_minutes'=>$requiredMinutes,
        'effective_minutes'=>$effectiveMinutes,
        'overtime_minutes'=>$overtimeMinutes,
        'gross_salary'=>$gross,
        'attendance_deduction'=>$attendanceDeduction,
        'leave_deduction'=>$leaveDeduction,
        'professional_tax'=>$tax,
        'custom_deduction'=>0,
        'retention_amount'=>$retention,
        'overtime_amount'=>$overtimeAmount,
        'extra_day_amount'=>0,
        'total_deduction'=>$totalDeduction,
        'net_salary'=>$net
    ];
}

function oecrm_generate_payroll($conn, $companyId, $year, $month, $actor)
{
    $start = sprintf('%04d-%02d-01', $year, $month);
    $end = date('Y-m-t', strtotime($start));
    $lockStmt = mysqli_prepare($conn, 'SELECT is_locked FROM attendance_period_locks WHERE company_id=? AND period_year=? AND period_month=?');
    mysqli_stmt_bind_param($lockStmt, 'iii', $companyId, $year, $month);
    mysqli_stmt_execute($lockStmt);
    $lock = mysqli_fetch_assoc(mysqli_stmt_get_result($lockStmt));
    mysqli_stmt_close($lockStmt);
    $attendanceLocked = !empty($lock['is_locked']) ? 1 : 0;

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, 'INSERT INTO payroll_runs(company_id,period_year,period_month,status,attendance_locked,generated_by,generated_at) VALUES(?,?,?,"generated",?,?,NOW()) ON DUPLICATE KEY UPDATE status=IF(status IN ("approved","locked"),status,"generated"),attendance_locked=VALUES(attendance_locked),generated_by=VALUES(generated_by),generated_at=NOW()');
        mysqli_stmt_bind_param($stmt, 'iiiii', $companyId, $year, $month, $attendanceLocked, $actor);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $run = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM payroll_runs WHERE company_id=' . (int) $companyId . ' AND period_year=' . (int) $year . ' AND period_month=' . (int) $month));
        if (in_array($run['status'], ['approved', 'locked'], true)) throw new RuntimeException('Approved or locked payroll cannot be regenerated.');
        $runId = (int) $run['id'];
        mysqli_query($conn, 'DELETE FROM payroll_items WHERE payroll_run_id=' . $runId);
        $employees = mysqli_query($conn, "SELECT e.* FROM employeestbl e LEFT JOIN employee_profiles p ON p.employee_id=e.id WHERE e.company_id=$companyId AND e.joiningDate<='$end' AND (e.status=0 OR (e.status=1 AND p.exit_date BETWEEN '$start' AND '$end')) ORDER BY e.id");
        $totals = ['count'=>0,'gross'=>0,'deduction'=>0,'retention'=>0,'net'=>0];
        $insert = mysqli_prepare($conn, 'INSERT INTO payroll_items(payroll_run_id,employee_id,salary_structure_id,calendar_days,payable_days,present_days,paid_leave_days,unpaid_leave_days,weekly_off_days,holiday_days,required_minutes,effective_minutes,overtime_minutes,gross_salary,attendance_deduction,leave_deduction,professional_tax,custom_deduction,retention_amount,overtime_amount,extra_day_amount,total_deduction,net_salary) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        while ($employee = mysqli_fetch_assoc($employees)) {
            $calculation = oecrm_payroll_employee_calculation($conn, $employee, $year, $month);
            if (!$calculation) continue;
            mysqli_stmt_bind_param($insert, 'iiiiddddddiiidddddddddd', $runId, $employee['id'], $calculation['structure']['id'], $calculation['calendar_days'], $calculation['payable_days'], $calculation['present_days'], $calculation['paid_leave_days'], $calculation['unpaid_leave_days'], $calculation['weekly_off_days'], $calculation['holiday_days'], $calculation['required_minutes'], $calculation['effective_minutes'], $calculation['overtime_minutes'], $calculation['gross_salary'], $calculation['attendance_deduction'], $calculation['leave_deduction'], $calculation['professional_tax'], $calculation['custom_deduction'], $calculation['retention_amount'], $calculation['overtime_amount'], $calculation['extra_day_amount'], $calculation['total_deduction'], $calculation['net_salary']);
            mysqli_stmt_execute($insert);
            $totals['count']++;
            $totals['gross'] += $calculation['gross_salary'];
            $totals['deduction'] += $calculation['total_deduction'];
            $totals['retention'] += $calculation['retention_amount'];
            $totals['net'] += $calculation['net_salary'];
        }
        mysqli_stmt_close($insert);
        $stmt = mysqli_prepare($conn, 'UPDATE payroll_runs SET employee_count=?,gross_total=?,deduction_total=?,retention_total=?,net_total=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'iddddi', $totals['count'], $totals['gross'], $totals['deduction'], $totals['retention'], $totals['net'], $runId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
        return $runId;
    } catch (Throwable $exception) {
        mysqli_rollback($conn);
        throw $exception;
    }
}
