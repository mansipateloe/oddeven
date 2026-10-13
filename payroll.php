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
    $shiftsByDate = [];
    $fallbackShiftStmt = mysqli_prepare($conn, 'SELECT s.* FROM employee_shift_assignments a JOIN shifts s ON s.id=a.shift_id WHERE a.employee_id=? AND a.status=1 AND s.status=1 ORDER BY a.effective_from DESC,a.id DESC LIMIT 1');
    mysqli_stmt_bind_param($fallbackShiftStmt, 'i', $employee['id']);
    mysqli_stmt_execute($fallbackShiftStmt);
    $fallbackShift = mysqli_fetch_assoc(mysqli_stmt_get_result($fallbackShiftStmt));
    mysqli_stmt_close($fallbackShiftStmt);
    $employmentStart = max($start, (string) $employee['joiningDate']);
    $employmentEnd = min(
        $end,
        !empty($employee['payroll_exit_date'])
            ? date('Y-m-d', strtotime($employee['payroll_exit_date']))
            : $end
    );
    $cursor = new DateTime($employmentStart);
    $last = new DateTime($employmentEnd);
    while ($cursor <= $last) {
        $date = $cursor->format('Y-m-d');
        $shift = oecrm_employee_shift($conn, $employee['id'], $date) ?: $fallbackShift;
        if ($shift) {
            $shiftsByDate[$date] = $shift;
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

    $stmt = mysqli_prepare($conn, 'SELECT a.*,s.required_minutes FROM attendance_sessions a JOIN shifts s ON s.id=a.shift_id WHERE a.employee_id=? AND a.attendance_date BETWEEN ? AND ?');
    mysqli_stmt_bind_param($stmt, 'iss', $employee['id'], $employmentStart, $employmentEnd);
    mysqli_stmt_execute($stmt);
    $attendanceRows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $presentDays = 0.0;
    $effectiveMinutes = 0;
    $extraDayUnits = 0.0;
    $extraDayMinutes = 0;
    $workedDates = [];
    while ($row = mysqli_fetch_assoc($attendanceRows)) {
        if (empty($row['actual_in'])) {
            continue;
        }
        $date = $row['attendance_date'];
        $isHoliday = oecrm_is_holiday($conn, $date, (int) $employee['company_id']);
        $isWeeklyOff = oecrm_shift_is_weekly_off($conn, (int) $row['shift_id'], $date);
        if ($isHoliday || $isWeeklyOff) {
            if (
                !empty($row['actual_out']) &&
                in_array($row['review_status'], ['approved', 'corrected'], true) &&
                (int) $row['effective_minutes'] > 0
            ) {
                $requiredForDay = max(1, (int) $row['required_minutes']);
                $creditedMinutes = min((int) $row['effective_minutes'], $requiredForDay);
                $extraDayUnits += $creditedMinutes / $requiredForDay;
                $extraDayMinutes += $creditedMinutes;
                $workedDates[$date] = true;
            }
            continue;
        }
        $presentDays += $row['attendance_status'] === 'half_day' ? 0.5 : 1;
        $effectiveMinutes += (int) $row['effective_minutes'];
        if (
            !empty($row['actual_out']) &&
            in_array($row['review_status'], ['approved', 'corrected'], true)
        ) {
            $workedDates[$date] = true;
        }
    }

    $stmt = mysqli_prepare($conn, "SELECT r.* FROM leave_requests r WHERE r.employee_id=? AND r.status='approved' AND r.start_date<=? AND r.end_date>=?");
    mysqli_stmt_bind_param($stmt, 'iss', $employee['id'], $end, $start);
    mysqli_stmt_execute($stmt);
    $leaveRows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $paidLeaveDays = 0.0;
    $unpaidLeaveDays = 0.0;
    $workedLeaveDates = [];
    while ($leave = mysqli_fetch_assoc($leaveRows)) {
        $remainingLeaveDays = max(0, (float) $leave['payable_days']);
        $remainingSandwichDays = max(0, (float) $leave['sandwich_days']);
        $unpaidRatio = $remainingLeaveDays > 0
            ? min(1, (float) $leave['unpaid_days'] / $remainingLeaveDays)
            : 0;
        $firstWorkingDate = null;
        $lastWorkingDate = null;
        $leaveDate = new DateTime($leave['start_date']);
        $requestEnd = new DateTime($leave['end_date']);
        while ($leaveDate <= $requestEnd) {
            $date = $leaveDate->format('Y-m-d');
            $shift = $shiftsByDate[$date] ?? oecrm_employee_shift($conn, $employee['id'], $date) ?? $fallbackShift;
            $isOff = oecrm_is_holiday($conn, $date, (int) $employee['company_id']) ||
                ($shift && oecrm_shift_is_weekly_off($conn, (int) $shift['id'], $date));
            if (!$isOff) {
                $firstWorkingDate = $firstWorkingDate ?? $date;
                $lastWorkingDate = $date;
            }
            $leaveDate->modify('+1 day');
        }
        $leaveDate = new DateTime($leave['start_date']);
        while ($leaveDate <= $requestEnd && $remainingLeaveDays > 0) {
            $date = $leaveDate->format('Y-m-d');
            $shift = $shiftsByDate[$date] ?? oecrm_employee_shift($conn, $employee['id'], $date) ?? $fallbackShift;
            $isOff = oecrm_is_holiday($conn, $date, (int) $employee['company_id']) ||
                ($shift && oecrm_shift_is_weekly_off($conn, (int) $shift['id'], $date));
            $isSandwichDate = $isOff &&
                $remainingSandwichDays > 0 &&
                $firstWorkingDate !== null &&
                $date > $firstWorkingDate &&
                $date < $lastWorkingDate;
            if (!$isOff || $isSandwichDate) {
                $leaveUnits = min(1, $remainingLeaveDays);
                if (!empty($workedDates[$date])) {
                    $workedLeaveDates[$date] = true;
                } elseif ($date >= $start && $date <= $end) {
                    $unpaidLeaveDays += $leaveUnits * $unpaidRatio;
                    $paidLeaveDays += $leaveUnits * (1 - $unpaidRatio);
                }
                $remainingLeaveDays -= $leaveUnits;
                if ($isSandwichDate) {
                    $remainingSandwichDays -= min(1, $remainingSandwichDays);
                }
            }
            $leaveDate->modify('+1 day');
        }
    }
    $payableDays = min($workingDays, $presentDays + $paidLeaveDays);
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
        $extraDayAmount = round(($extraDayMinutes / 60) * (float) $structure['hourly_rate'], 2);
    } else {
        $gross = (float) $structure['gross_salary'];
        $perDay = $workingDays > 0 ? $gross / $workingDays : 0;
        $perRequiredMinute = $requiredMinutes > 0 ? $gross / $requiredMinutes : 0;
        $attendanceDeduction = round($attendanceDeficitMinutes * $perRequiredMinute, 2);
        $leaveDeduction = round($unpaidLeaveDays * $perDay, 2);
        $extraDayAmount = round($extraDayUnits * $perDay, 2);
    }

    $tax = !empty($structure['professional_tax_enabled']) ? oecrm_professional_tax($conn, $gross) : 0;
    $retention = 0;
    if ($structure['retention_type'] === 'fixed') $retention = (float) $structure['retention_value'];
    elseif ($structure['retention_type'] === 'percent') $retention = round($gross * ((float) $structure['retention_value'] / 100), 2);
    $totalDeduction = $attendanceDeduction + $leaveDeduction + $tax + $retention;
    $net = max(0, $gross + $overtimeAmount + $extraDayAmount - $totalDeduction);
    $notes = $workedLeaveDates
        ? 'Worked on approved leave date(s): ' . implode(', ', array_keys($workedLeaveDates))
        : null;

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
        'extra_day_amount'=>$extraDayAmount,
        'total_deduction'=>$totalDeduction,
        'net_salary'=>$net,
        'notes'=>$notes
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
        $employees = mysqli_query($conn, "SELECT e.*,p.exit_date AS payroll_exit_date FROM employeestbl e LEFT JOIN employee_profiles p ON p.employee_id=e.id WHERE e.company_id=$companyId AND e.joiningDate<='$end' AND (e.status=0 OR (e.status=1 AND p.exit_date BETWEEN '$start' AND '$end')) ORDER BY e.id");
        $totals = ['count'=>0,'gross'=>0,'deduction'=>0,'retention'=>0,'net'=>0];
        $insert = mysqli_prepare($conn, 'INSERT INTO payroll_items(payroll_run_id,employee_id,salary_structure_id,calendar_days,payable_days,present_days,paid_leave_days,unpaid_leave_days,weekly_off_days,holiday_days,required_minutes,effective_minutes,overtime_minutes,gross_salary,attendance_deduction,leave_deduction,professional_tax,custom_deduction,retention_amount,overtime_amount,extra_day_amount,total_deduction,net_salary,notes) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        while ($employee = mysqli_fetch_assoc($employees)) {
            $calculation = oecrm_payroll_employee_calculation($conn, $employee, $year, $month);
            if (!$calculation) continue;
            mysqli_stmt_bind_param($insert, 'iiiiddddddiiidddddddddds', $runId, $employee['id'], $calculation['structure']['id'], $calculation['calendar_days'], $calculation['payable_days'], $calculation['present_days'], $calculation['paid_leave_days'], $calculation['unpaid_leave_days'], $calculation['weekly_off_days'], $calculation['holiday_days'], $calculation['required_minutes'], $calculation['effective_minutes'], $calculation['overtime_minutes'], $calculation['gross_salary'], $calculation['attendance_deduction'], $calculation['leave_deduction'], $calculation['professional_tax'], $calculation['custom_deduction'], $calculation['retention_amount'], $calculation['overtime_amount'], $calculation['extra_day_amount'], $calculation['total_deduction'], $calculation['net_salary'], $calculation['notes']);
            mysqli_stmt_execute($insert);
            $totals['count']++;
            $totals['gross'] += $calculation['gross_salary'] + $calculation['overtime_amount'] + $calculation['extra_day_amount'];
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
