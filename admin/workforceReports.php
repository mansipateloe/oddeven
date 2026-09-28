<?php
$active_menu = 'analytics';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../projectDeadlineHelpers.php';

oecrm_require_permission($conn, 'dashboards', 'view');
$companyId = oecrm_current_company_id($conn);
oecrm_project_deadline_ensure_schema($conn);
$month = preg_match('/^\d{4}-\d{2}$/', $_GET['month'] ?? '') ? $_GET['month'] : date('Y-m');
$year = preg_match('/^\d{4}$/', (string) ($_GET['year'] ?? '')) ? (int) $_GET['year'] : (int) date('Y');
$start = $month . '-01';
$end = date('Y-m-t', strtotime($start));
$startSafe = mysqli_real_escape_string($conn, $start);
$endSafe = mysqli_real_escape_string($conn, $end);
$yearStartSafe = mysqli_real_escape_string($conn, $year . '-01-01');
$yearEndSafe = mysqli_real_escape_string($conn, $year . '-12-31');

$salaryRateExpression = "CASE
        WHEN COALESCE(ss.salary_mode,'fixed')='hourly' AND COALESCE(ss.hourly_rate,0)>0 THEN ss.hourly_rate
        ELSE COALESCE(NULLIF(ss.gross_salary,0),CAST(NULLIF(REPLACE(e.salary,',',''),'') AS DECIMAL(14,2)),0)/176
    END";
$salaryMonthlyExpression = "CASE
        WHEN COALESCE(ss.salary_mode,'fixed')='hourly' AND COALESCE(ss.hourly_rate,0)>0 THEN ss.hourly_rate*176
        ELSE COALESCE(NULLIF(ss.gross_salary,0),CAST(NULLIF(REPLACE(e.salary,',',''),'') AS DECIMAL(14,2)),0)
    END";
$salaryStructureJoin = "LEFT JOIN salary_structures ss ON ss.id=(
        SELECT s2.id FROM salary_structures s2
        WHERE s2.employee_id=e.id
          AND s2.status=1
          AND s2.effective_from<=t.work_date
          AND (s2.effective_to IS NULL OR s2.effective_to>=t.work_date)
        ORDER BY s2.effective_from DESC,s2.id DESC
        LIMIT 1
    )";

$workforce = mysqli_query($conn, "SELECT e.id,e.employeeCode,e.name,e.company_id,
    COALESCE(a.attendance_minutes,0) attendance_minutes,
    COALESCE(a.required_minutes,0) required_minutes,
    GREATEST(COALESCE(a.attendance_minutes,0)-COALESCE(a.required_minutes,0),0) extra_minutes,
    GREATEST(COALESCE(a.required_minutes,0)-COALESCE(a.attendance_minutes,0),0) short_minutes,
    COALESCE(t.timesheet_hours,0) timesheet_hours,
    GREATEST(COALESCE(a.attendance_minutes,0)/60-COALESCE(t.timesheet_hours,0),0) unallocated_hours,
    CASE WHEN COALESCE(a.attendance_minutes,0)>0 THEN LEAST(100,ROUND(COALESCE(t.timesheet_hours,0)*60*100/a.attendance_minutes,2)) ELSE 0 END utilization
    FROM employeestbl e
    LEFT JOIN (
        SELECT s.employee_id,SUM(s.effective_minutes) attendance_minutes,SUM(COALESCE(sh.required_minutes,0)) required_minutes
        FROM attendance_sessions s
        LEFT JOIN employee_shift_assignments sa ON sa.employee_id=s.employee_id AND sa.status=1 AND sa.effective_from<=s.attendance_date AND (sa.effective_to IS NULL OR sa.effective_to>=s.attendance_date)
        LEFT JOIN shifts sh ON sh.id=sa.shift_id
        WHERE s.attendance_date BETWEEN '$startSafe' AND '$endSafe'
        GROUP BY s.employee_id
    ) a ON a.employee_id=e.id
    LEFT JOIN (
        SELECT employee_id,SUM(hours) timesheet_hours FROM timesheet_entries
        WHERE work_date BETWEEN '$startSafe' AND '$endSafe' AND status='approved'
        GROUP BY employee_id
    ) t ON t.employee_id=e.id
    WHERE e.company_id=$companyId
    ORDER BY e.status,e.name");

$projects = mysqli_query($conn, "SELECT p.id,p.projectName,p.status,c.display_name client_name,
    COALESCE(NULLIF(p.original_deadline,'0000-00-00'),NULLIF(p.enddate,'0000-00-00')) original_deadline,
    COALESCE(NULLIF(p.current_deadline,'0000-00-00'),NULLIF(p.enddate,'0000-00-00')) current_deadline,
    COALESCE(p.deadline_extended_count,0) deadline_extended_count,
    CAST(p.amount AS DECIMAL(14,2)) base_revenue,
    COALESCE(CAST(p.extra_scope_value AS DECIMAL(14,2)),0) extra_scope_value,
    CAST(p.amount AS DECIMAL(14,2))+COALESCE(CAST(p.extra_scope_value AS DECIMAL(14,2)),0) revenue,
    COALESCE(te.labour_cost,0) labour_cost,COALESCE(pe.expense_cost,0) expense_cost,
    COALESCE(te.labour_cost,0)+COALESCE(pe.expense_cost,0) total_cost,
    CAST(p.amount AS DECIMAL(14,2))+COALESCE(CAST(p.extra_scope_value AS DECIMAL(14,2)),0)-COALESCE(te.labour_cost,0)-COALESCE(pe.expense_cost,0) profit,
    (SELECT COUNT(*) FROM tasktbl task WHERE CAST(task.projectId AS UNSIGNED)=p.id AND task.company_id=p.company_id AND NULLIF(task.expectedDate,'0000-00-00') > COALESCE(NULLIF(p.current_deadline,'0000-00-00'),NULLIF(p.enddate,'0000-00-00'))) risk_tasks
    FROM projectstbl p
    LEFT JOIN clients c ON c.id=p.client_id
    LEFT JOIN (
        SELECT t.project_id,SUM(t.hours*(COALESCE(ss.gross_salary,CAST(e.salary AS DECIMAL(14,2)),0)/176)) labour_cost
        FROM timesheet_entries t
        JOIN employeestbl e ON e.id=t.employee_id
        LEFT JOIN salary_structures ss ON ss.id=(SELECT s2.id FROM salary_structures s2 WHERE s2.employee_id=e.id AND s2.status=1 AND s2.effective_from<=t.work_date ORDER BY s2.effective_from DESC,s2.id DESC LIMIT 1)
        WHERE t.status='approved'
        GROUP BY t.project_id
    ) te ON te.project_id=p.id
    LEFT JOIN (
        SELECT project_Id project_id,SUM(CAST(amount AS DECIMAL(14,2))) expense_cost FROM project_expenses
        GROUP BY project_Id
    ) pe ON pe.project_id=p.id
    WHERE p.company_id=$companyId
    ORDER BY profit,p.projectName");

$intercompany = mysqli_query($conn, "SELECT home.display_name home_company,project_company.display_name project_company,
    e.employeeCode,e.name,p.projectName,SUM(t.hours) hours,
    SUM(t.hours*(COALESCE(ss.gross_salary,CAST(e.salary AS DECIMAL(14,2)),0)/176)) allocated_cost
    FROM timesheet_entries t
    JOIN employeestbl e ON e.id=t.employee_id
    JOIN companies home ON home.id=e.company_id
    JOIN projectstbl p ON p.id=t.project_id
    JOIN companies project_company ON project_company.id=p.company_id
    LEFT JOIN salary_structures ss ON ss.id=(SELECT s2.id FROM salary_structures s2 WHERE s2.employee_id=e.id AND s2.status=1 AND s2.effective_from<=t.work_date ORDER BY s2.effective_from DESC,s2.id DESC LIMIT 1)
    WHERE t.work_date BETWEEN '$startSafe' AND '$endSafe' AND t.status='approved'
      AND e.company_id<>p.company_id AND (e.company_id=$companyId OR p.company_id=$companyId)
    GROUP BY e.id,p.id
    ORDER BY project_company.display_name,p.projectName,e.name");

$yearlyEmployeeTotals = mysqli_query($conn, "SELECT e.id,e.employeeCode,e.name,e.designation,home.display_name employee_company,
    COUNT(DISTINCT p.id) project_count,
    COUNT(DISTINCT t.work_date) work_days,
    SUM(t.hours) total_hours,
    SUM(t.hours*($salaryRateExpression)) salary_cost,
    CASE WHEN SUM(t.hours)>0 THEN SUM(t.hours*($salaryRateExpression))/SUM(t.hours) ELSE 0 END hourly_cost,
    CASE WHEN SUM(t.hours)>0 THEN SUM(t.hours*($salaryMonthlyExpression))/SUM(t.hours) ELSE 0 END monthly_salary_basis,
    MIN(t.work_date) first_work_date,
    MAX(t.work_date) last_work_date
    FROM timesheet_entries t
    JOIN employeestbl e ON e.id=t.employee_id
    LEFT JOIN companies home ON home.id=e.company_id
    JOIN projectstbl p ON p.id=t.project_id
    $salaryStructureJoin
    WHERE t.work_date BETWEEN '$yearStartSafe' AND '$yearEndSafe'
      AND t.status='approved'
      AND e.company_id=$companyId
    GROUP BY e.id
    ORDER BY e.name");

$yearlyEmployeeProjects = mysqli_query($conn, "SELECT e.id employee_id,e.employeeCode,e.name,e.designation,home.display_name employee_company,
    p.id project_id,p.projectName,project_company.display_name project_company,COALESCE(c.display_name,p.customerName,'-') client_name,
    COUNT(DISTINCT t.work_date) work_days,
    SUM(t.hours) total_hours,
    SUM(t.hours*($salaryRateExpression)) salary_cost,
    CASE WHEN SUM(t.hours)>0 THEN SUM(t.hours*($salaryRateExpression))/SUM(t.hours) ELSE 0 END hourly_cost,
    CASE WHEN SUM(t.hours)>0 THEN SUM(t.hours*($salaryMonthlyExpression))/SUM(t.hours) ELSE 0 END monthly_salary_basis,
    MIN(t.work_date) first_work_date,
    MAX(t.work_date) last_work_date
    FROM timesheet_entries t
    JOIN employeestbl e ON e.id=t.employee_id
    LEFT JOIN companies home ON home.id=e.company_id
    JOIN projectstbl p ON p.id=t.project_id
    LEFT JOIN companies project_company ON project_company.id=p.company_id
    LEFT JOIN clients c ON c.id=p.client_id
    $salaryStructureJoin
    WHERE t.work_date BETWEEN '$yearStartSafe' AND '$yearEndSafe'
      AND t.status='approved'
      AND e.company_id=$companyId
    GROUP BY e.id,p.id
    ORDER BY e.name,p.projectName");
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="foundation-titlebar"><span></span><h2>Workforce & Project Analytics</h2></div>
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="get" class="form-inline">
                <label>Month</label>
                <input type="month" class="form-control" name="month" value="<?php echo oecrm_h($month); ?>">
                <label style="margin-left:12px">Year</label>
                <input type="number" class="form-control" name="year" min="2000" max="2100" value="<?php echo (int) $year; ?>">
                <button class="btn btn-primary"><i class="fa fa-filter"></i> Apply</button>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Yearly Employee Project Salary Report - <?php echo (int) $year; ?></div>
        <div class="panel-body table-responsive">
            <h4>Employee Yearly Summary</h4>
            <table class="table foundation-table">
                <thead><tr><th>Employee</th><th>Designation</th><th>Projects</th><th>Work Days</th><th>Total Hours</th><th>Monthly Salary Basis</th><th>Yearly Salary Basis</th><th>Avg Hourly Cost</th><th>Allocated Salary Cost</th><th>Period</th></tr></thead>
                <tbody>
                <?php if (!$yearlyEmployeeTotals || mysqli_num_rows($yearlyEmployeeTotals) === 0): ?><tr><td colspan="10">No approved timesheets found for this year.</td></tr><?php endif; ?>
                <?php if ($yearlyEmployeeTotals): while ($row=mysqli_fetch_assoc($yearlyEmployeeTotals)): ?>
                    <tr>
                        <td><strong><?php echo oecrm_h($row['employeeCode'] . ' - ' . $row['name']); ?></strong><small style="display:block"><?php echo oecrm_h($row['employee_company'] ?: '-'); ?></small></td>
                        <td><?php echo oecrm_h($row['designation'] ?: '-'); ?></td>
                        <td><?php echo (int) $row['project_count']; ?></td>
                        <td><?php echo (int) $row['work_days']; ?></td>
                        <td><?php echo number_format((float) $row['total_hours'], 2); ?> h</td>
                        <td><?php echo number_format((float) $row['monthly_salary_basis'], 2); ?></td>
                        <td><?php echo number_format((float) $row['monthly_salary_basis'] * 12, 2); ?></td>
                        <td><?php echo number_format((float) $row['hourly_cost'], 2); ?></td>
                        <td><strong><?php echo number_format((float) $row['salary_cost'], 2); ?></strong></td>
                        <td><?php echo oecrm_h($row['first_work_date'] . ' to ' . $row['last_work_date']); ?></td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
            <h4 style="margin-top:24px">Project-wise Employee Salary Cost</h4>
            <table class="table foundation-table">
                <thead><tr><th>Employee</th><th>Project</th><th>Client</th><th>Project Company</th><th>Work Days</th><th>Hours</th><th>Monthly Salary Basis</th><th>Avg Hourly Cost</th><th>Project Salary Cost</th><th>Period</th></tr></thead>
                <tbody>
                <?php if (!$yearlyEmployeeProjects || mysqli_num_rows($yearlyEmployeeProjects) === 0): ?><tr><td colspan="10">No project-wise salary data found for this year.</td></tr><?php endif; ?>
                <?php if ($yearlyEmployeeProjects): while ($row=mysqli_fetch_assoc($yearlyEmployeeProjects)): ?>
                    <tr>
                        <td><strong><?php echo oecrm_h($row['employeeCode'] . ' - ' . $row['name']); ?></strong><small style="display:block"><?php echo oecrm_h($row['designation'] ?: '-'); ?></small></td>
                        <td><?php echo oecrm_h($row['projectName']); ?></td>
                        <td><?php echo oecrm_h($row['client_name'] ?: '-'); ?></td>
                        <td><?php echo oecrm_h($row['project_company'] ?: '-'); ?></td>
                        <td><?php echo (int) $row['work_days']; ?></td>
                        <td><?php echo number_format((float) $row['total_hours'], 2); ?> h</td>
                        <td><?php echo number_format((float) $row['monthly_salary_basis'], 2); ?></td>
                        <td><?php echo number_format((float) $row['hourly_cost'], 2); ?></td>
                        <td><strong><?php echo number_format((float) $row['salary_cost'], 2); ?></strong></td>
                        <td><?php echo oecrm_h($row['first_work_date'] . ' to ' . $row['last_work_date']); ?></td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
            <p class="text-muted">Calculation uses approved timesheets only. Fixed salary hourly cost = monthly salary / 176 hours. Hourly salary mode uses configured hourly rate.</p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Attendance, Extra Hours & Utilization</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table"><thead><tr><th>Employee</th><th>Attendance</th><th>Required</th><th>Extra</th><th>Short</th><th>Timesheet</th><th>Unallocated</th><th>Utilization</th></tr></thead><tbody>
            <?php while ($row=mysqli_fetch_assoc($workforce)): ?><tr><td><strong><?php echo oecrm_h($row['name']); ?></strong><small style="display:block"><?php echo oecrm_h($row['employeeCode']); ?></small></td><td><?php echo number_format($row['attendance_minutes']/60,2); ?> h</td><td><?php echo number_format($row['required_minutes']/60,2); ?> h</td><td><?php echo number_format($row['extra_minutes']/60,2); ?> h</td><td><?php echo number_format($row['short_minutes']/60,2); ?> h</td><td><?php echo number_format($row['timesheet_hours'],2); ?> h</td><td><?php echo number_format($row['unallocated_hours'],2); ?> h</td><td><?php echo number_format($row['utilization'],2); ?>%</td></tr><?php endwhile; ?>
            </tbody></table>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Project Costing & Profitability</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table"><thead><tr><th>Project</th><th>Client</th><th>Deadline</th><th>Status</th><th>Revenue</th><th>Extra Scope</th><th>Labour Cost</th><th>Expenses</th><th>Total Cost</th><th>Profit/Loss</th><th>%</th></tr></thead><tbody>
            <?php while ($row=mysqli_fetch_assoc($projects)):
                $profitPercent = (float) $row['revenue'] > 0 ? ((float) $row['profit'] * 100 / (float) $row['revenue']) : 0;
                $profitStatus = (float) $row['profit'] < 0 ? 'Loss' : ((float) $row['profit'] > 0 ? 'Profit' : 'Break-even');
            ?><tr>
                <td><?php echo oecrm_h($row['projectName']); ?></td>
                <td><?php echo oecrm_h($row['client_name'] ?: '-'); ?></td>
                <td><?php echo oecrm_h(oecrm_project_deadline_format($row['current_deadline'])); ?><?php if ((int)$row['deadline_extended_count'] > 0): ?><small style="display:block"><?php echo (int)$row['deadline_extended_count']; ?> extension(s)</small><?php endif; ?><?php if ((int)$row['risk_tasks'] > 0): ?><small class="text-warning" style="display:block"><?php echo (int)$row['risk_tasks']; ?> risk task(s)</small><?php endif; ?></td>
                <td><?php echo oecrm_h(ucfirst($row['status'])); ?></td>
                <td><?php echo number_format($row['revenue'],2); ?><small style="display:block">Base: <?php echo number_format($row['base_revenue'],2); ?></small></td>
                <td><?php echo number_format($row['extra_scope_value'],2); ?></td>
                <td><?php echo number_format($row['labour_cost'],2); ?></td>
                <td><?php echo number_format($row['expense_cost'],2); ?></td>
                <td><?php echo number_format($row['total_cost'],2); ?></td>
                <td class="<?php echo $row['profit']<0?'text-danger':'text-success'; ?>"><strong><?php echo number_format($row['profit'],2); ?></strong><small style="display:block"><?php echo $profitStatus; ?></small></td>
                <td><?php echo number_format($profitPercent,2); ?>%</td>
            </tr><?php endwhile; ?>
            </tbody></table>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Inter-company Shared Employee Costing</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table"><thead><tr><th>Employee</th><th>Main Company</th><th>Project Company</th><th>Project</th><th>Hours</th><th>Allocated Cost</th></tr></thead><tbody>
            <?php if (mysqli_num_rows($intercompany)===0): ?><tr><td colspan="6">No inter-company approved timesheets for this month.</td></tr><?php endif; ?>
            <?php while ($row=mysqli_fetch_assoc($intercompany)): ?><tr><td><?php echo oecrm_h($row['employeeCode'].' - '.$row['name']); ?></td><td><?php echo oecrm_h($row['home_company']); ?></td><td><?php echo oecrm_h($row['project_company']); ?></td><td><?php echo oecrm_h($row['projectName']); ?></td><td><?php echo number_format($row['hours'],2); ?></td><td><?php echo number_format($row['allocated_cost'],2); ?></td></tr><?php endwhile; ?>
            </tbody></table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
