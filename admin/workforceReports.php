<?php
$active_menu = 'analytics';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'dashboards', 'view');
$companyId = oecrm_current_company_id($conn);
$month = preg_match('/^\d{4}-\d{2}$/', $_GET['month'] ?? '') ? $_GET['month'] : date('Y-m');
$start = $month . '-01';
$end = date('Y-m-t', strtotime($start));
$startSafe = mysqli_real_escape_string($conn, $start);
$endSafe = mysqli_real_escape_string($conn, $end);

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

$projects = mysqli_query($conn, "SELECT p.id,p.projectName,p.status,CAST(p.amount AS DECIMAL(14,2)) revenue,
    COALESCE(te.labour_cost,0) labour_cost,COALESCE(pe.expense_cost,0) expense_cost,
    COALESCE(te.labour_cost,0)+COALESCE(pe.expense_cost,0) total_cost,
    CAST(p.amount AS DECIMAL(14,2))-COALESCE(te.labour_cost,0)-COALESCE(pe.expense_cost,0) profit
    FROM projectstbl p
    LEFT JOIN (
        SELECT t.project_id,SUM(t.hours*(COALESCE(ss.gross_salary,CAST(e.salary AS DECIMAL(14,2)),0)/176)) labour_cost
        FROM timesheet_entries t
        JOIN employeestbl e ON e.id=t.employee_id
        LEFT JOIN salary_structures ss ON ss.id=(SELECT s2.id FROM salary_structures s2 WHERE s2.employee_id=e.id AND s2.status=1 AND s2.effective_from<=t.work_date ORDER BY s2.effective_from DESC,s2.id DESC LIMIT 1)
        WHERE t.work_date BETWEEN '$startSafe' AND '$endSafe' AND t.status='approved'
        GROUP BY t.project_id
    ) te ON te.project_id=p.id
    LEFT JOIN (
        SELECT project_Id project_id,SUM(CAST(amount AS DECIMAL(14,2))) expense_cost FROM project_expenses
        WHERE expensedate BETWEEN '$startSafe' AND '$endSafe'
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
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="foundation-titlebar"><span></span><h2>Workforce & Project Analytics</h2></div>
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="get" class="form-inline"><label>Month</label> <input type="month" class="form-control" name="month" value="<?php echo oecrm_h($month); ?>"> <button class="btn btn-primary"><i class="fa fa-filter"></i> Apply</button></form>
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
            <table class="table foundation-table"><thead><tr><th>Project</th><th>Status</th><th>Revenue/Budget</th><th>Labour Cost</th><th>Expenses</th><th>Total Cost</th><th>Profit</th></tr></thead><tbody>
            <?php while ($row=mysqli_fetch_assoc($projects)): ?><tr><td><?php echo oecrm_h($row['projectName']); ?></td><td><?php echo oecrm_h(ucfirst($row['status'])); ?></td><td><?php echo number_format($row['revenue'],2); ?></td><td><?php echo number_format($row['labour_cost'],2); ?></td><td><?php echo number_format($row['expense_cost'],2); ?></td><td><?php echo number_format($row['total_cost'],2); ?></td><td class="<?php echo $row['profit']<0?'text-danger':'text-success'; ?>"><strong><?php echo number_format($row['profit'],2); ?></strong></td></tr><?php endwhile; ?>
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
