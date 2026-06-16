<?php include 'header.php'; ?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <?php
                    require_once __DIR__ . '/../birthdays.php';
                    function admin_dashboard_count($conn, $sql) {
                        $result = mysqli_query($conn, $sql);
                        if (!$result) {
                            return 0;
                        }
                        $row = mysqli_fetch_assoc($result);
                        return (int)($row['total'] ?? 0);
                    }

                    $totalProjects = admin_dashboard_count($conn, "SELECT COUNT(*) AS total FROM projectsTbl");
                    $pendingProjects = admin_dashboard_count($conn, "SELECT COUNT(*) AS total FROM projectsTbl WHERE status = 'pending'");
                    $inprogressProjects = admin_dashboard_count($conn, "SELECT COUNT(*) AS total FROM projectsTbl WHERE status = 'inprogress'");
                    $completedProjects = admin_dashboard_count($conn, "SELECT COUNT(*) AS total FROM projectsTbl WHERE status = 'completed'");
                    $cancelProjects = admin_dashboard_count($conn, "SELECT COUNT(*) AS total FROM projectsTbl WHERE status = 'cancel'");
                    $holidayCount = admin_dashboard_count($conn, "SELECT COUNT(*) AS total FROM holidayTbl");
                    $leaveCount = admin_dashboard_count($conn, "SELECT COUNT(*) AS total FROM leave_master");
                    $dashboardBirthdays = oecrm_employee_birthdays($conn, oecrm_current_company_id($conn), 30);
                    $todayBirthdays = array_values(array_filter($dashboardBirthdays, static function ($employee) { return (int) $employee['days_until'] === 0; }));
                ?>
                <?php if ($todayBirthdays): ?>
                    <div class="birthday-admin-alert">
                        <div><i class="fa fa-birthday-cake"></i><span><strong><?php echo count($todayBirthdays); ?> employee birthday<?php echo count($todayBirthdays)===1?'':'s'; ?> today</strong><small><?php echo oecrm_h(implode(', ', array_column($todayBirthdays, 'name'))); ?></small></span></div>
                        <a class="btn btn-primary btn-sm" href="birthdayCenter.php"><i class="fa fa-gift"></i> Send Birthday Wish</a>
                    </div>
                <?php endif; ?>
                <div class="topboxes analytics-summary">
                    <div class="row analytics-summary-row">
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="viewProject.php" class="analytics-card analytics-card-primary">
                                <span class="analytics-icon"><i class="fa fa-folder-open-o"></i></span>
                                <span class="analytics-trend trend-up">+22% <i class="fa fa-angle-up"></i></span>
                                <span class="analytics-value"><?php echo $totalProjects; ?></span>
                                <span class="analytics-label">Total Projects</span>
                                <span class="analytics-pill">All Time</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="viewProject.php" class="analytics-card analytics-card-warning">
                                <span class="analytics-icon"><i class="fa fa-clock-o"></i></span>
                                <span class="analytics-trend trend-up">+12% <i class="fa fa-angle-up"></i></span>
                                <span class="analytics-value"><?php echo $pendingProjects; ?></span>
                                <span class="analytics-label">Pending Projects</span>
                                <span class="analytics-pill">Open Queue</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="viewProject.php" class="analytics-card analytics-card-info">
                                <span class="analytics-icon"><i class="fa fa-refresh"></i></span>
                                <span class="analytics-trend trend-up">+38% <i class="fa fa-angle-up"></i></span>
                                <span class="analytics-value"><?php echo $inprogressProjects; ?></span>
                                <span class="analytics-label">Total Inprogress</span>
                                <span class="analytics-pill">Active Work</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="viewProject.php" class="analytics-card analytics-card-success">
                                <span class="analytics-icon"><i class="fa fa-check"></i></span>
                                <span class="analytics-trend trend-up">+16% <i class="fa fa-angle-up"></i></span>
                                <span class="analytics-value"><?php echo $completedProjects; ?></span>
                                <span class="analytics-label">Total Completed</span>
                                <span class="analytics-pill">Delivered</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="viewProject.php" class="analytics-card analytics-card-danger">
                                <span class="analytics-icon"><i class="fa fa-ban"></i></span>
                                <span class="analytics-trend trend-down">-18% <i class="fa fa-angle-down"></i></span>
                                <span class="analytics-value"><?php echo $cancelProjects; ?></span>
                                <span class="analytics-label">Total Cancel</span>
                                <span class="analytics-pill">Stopped</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="manageHoliday.php" class="analytics-card analytics-card-primary">
                                <span class="analytics-icon"><i class="fa fa-calendar"></i></span>
                                <span class="analytics-trend trend-up">+10% <i class="fa fa-angle-up"></i></span>
                                <span class="analytics-value"><?php echo $holidayCount; ?></span>
                                <span class="analytics-label">Holidays!</span>
                                <span class="analytics-pill">This Year</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="manageLeave.php" class="analytics-card analytics-card-warning">
                                <span class="analytics-icon"><i class="fa fa-user-times"></i></span>
                                <span class="analytics-trend trend-up">+8% <i class="fa fa-angle-up"></i></span>
                                <span class="analytics-value"><?php echo $leaveCount; ?></span>
                                <span class="analytics-label">Leaves!</span>
                                <span class="analytics-pill">Requests</span>
                            </a>
                        </div>
                    </div>
                </div>        <?php
            $balancePeriod = $_GET['balance_period'] ?? 'month';
            $allowedBalancePeriods = ['month', 'quarter', 'six_month', 'year', 'all'];
            if (!in_array($balancePeriod, $allowedBalancePeriods, true)) {
                $balancePeriod = 'month';
            }

            $today = new DateTime();
            $periodStart = null;
            $periodEnd = $today->format('Y-m-d');
            $periodLabel = 'This Month';

            if ($balancePeriod === 'month') {
                $periodStart = $today->format('Y-m-01');
                $periodLabel = 'This Month';
            } elseif ($balancePeriod === 'quarter') {
                $quarterStartMonth = (int)(floor(((int)$today->format('n') - 1) / 3) * 3) + 1;
                $periodStart = $today->format('Y') . '-' . str_pad((string)$quarterStartMonth, 2, '0', STR_PAD_LEFT) . '-01';
                $periodLabel = 'This Quarter';
            } elseif ($balancePeriod === 'six_month') {
                $periodStart = (new DateTime('-6 months'))->format('Y-m-d');
                $periodLabel = 'Last 6 Month';
            } elseif ($balancePeriod === 'year') {
                $periodStart = $today->format('Y-01-01');
                $periodLabel = 'This Year';
            } else {
                $periodStart = null;
                $periodLabel = 'All Time';
            }

            function oecrm_money($amount) {
                return 'Rs.' . number_format((float)$amount, 0);
            }

            function oecrm_account_sum($conn, $table, $amountColumn, $accountColumn, $dateColumn, $accountId, $periodStart, $periodEnd) {
                if ($periodStart === null) {
                    $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM($amountColumn), 0) AS total FROM $table WHERE $accountColumn = ?");
                    mysqli_stmt_bind_param($stmt, 'i', $accountId);
                } else {
                    $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM($amountColumn), 0) AS total FROM $table WHERE $accountColumn = ? AND DATE($dateColumn) BETWEEN ? AND ?");
                    mysqli_stmt_bind_param($stmt, 'iss', $accountId, $periodStart, $periodEnd);
                }
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);
                return (float)($row['total'] ?? 0);
            }

            $periodLinks = [
                'month' => 'Month Wise',
                'quarter' => 'Quarterly',
                'six_month' => 'Six Month',
                'year' => 'Yearly',
                'all' => 'All Time',
            ];

            $accounts = [];
            $totalCurrentBalance = 0;
            $totalPeriodIncome = 0;
            $totalPeriodExpense = 0;
            $qryAccount = "SELECT * FROM account ORDER BY account_name ASC";
            $resultAccount = mysqli_query($conn, $qryAccount);
            if ($resultAccount && $resultAccount->num_rows > 0) {
                while ($rowAccount = $resultAccount->fetch_assoc()) {
                    $accountId = (int)$rowAccount['account_id'];
                    $currentBalance = (float)$rowAccount['balance'];
                    $periodIncome = oecrm_account_sum($conn, 'deposit', 'amount', 'account_Id', 'deposit_date', $accountId, $periodStart, $periodEnd);
                    $periodExpense = oecrm_account_sum($conn, 'project_expenses', 'amount', 'account_Id', 'expensedate', $accountId, $periodStart, $periodEnd);
                    $netMovement = $periodIncome - $periodExpense;
                    $totalCurrentBalance += $currentBalance;
                    $totalPeriodIncome += $periodIncome;
                    $totalPeriodExpense += $periodExpense;
                    $accounts[] = [
                        'name' => $rowAccount['account_name'],
                        'balance' => $currentBalance,
                        'income' => $periodIncome,
                        'expense' => $periodExpense,
                        'net' => $netMovement,
                    ];
                }
            }
            $totalNetMovement = $totalPeriodIncome - $totalPeriodExpense;
        ?>
        <div class="bank_details finance-balance-section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default finance-balance-card">
                        <div class="panel-heading finance-balance-heading">
                            <div>
                                <span>Account Balance</span>
                                <small><?php echo htmlspecialchars($periodLabel); ?> bank account overview</small>
                            </div>
                            <div class="finance-period-tabs">
                                <?php foreach ($periodLinks as $periodKey => $periodText) { ?>
                                    <a class="<?php echo $balancePeriod === $periodKey ? 'active' : ''; ?>" href="dashboard.php?balance_period=<?php echo $periodKey; ?>"><?php echo $periodText; ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="finance-summary-grid">
                                <div class="finance-summary-tile tile-primary">
                                    <span class="finance-tile-icon"><i class="fa fa-bank"></i></span>
                                    <span class="finance-tile-label">Current Balance</span>
                                    <strong><?php echo oecrm_money($totalCurrentBalance); ?></strong>
                                </div>
                                <div class="finance-summary-tile tile-success">
                                    <span class="finance-tile-icon"><i class="fa fa-arrow-down"></i></span>
                                    <span class="finance-tile-label">Period Income</span>
                                    <strong><?php echo oecrm_money($totalPeriodIncome); ?></strong>
                                </div>
                                <div class="finance-summary-tile tile-danger">
                                    <span class="finance-tile-icon"><i class="fa fa-arrow-up"></i></span>
                                    <span class="finance-tile-label">Period Expense</span>
                                    <strong><?php echo oecrm_money($totalPeriodExpense); ?></strong>
                                </div>
                                <div class="finance-summary-tile <?php echo $totalNetMovement >= 0 ? 'tile-info' : 'tile-danger'; ?>">
                                    <span class="finance-tile-icon"><i class="fa fa-line-chart"></i></span>
                                    <span class="finance-tile-label">Net Movement</span>
                                    <strong><?php echo oecrm_money($totalNetMovement); ?></strong>
                                </div>
                            </div>
                            <div class="table-responsive finance-account-list">
                                <table class="table finance-account-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Bank Account</th>
                                            <th>Current Balance</th>
                                            <th>Income</th>
                                            <th>Expense</th>
                                            <th>Net</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($accounts as $account) {
                                            $initial = strtoupper(substr(trim($account['name'] ?: 'B'), 0, 1));
                                            $netClass = $account['net'] >= 0 ? 'status-active' : 'status-inactive';
                                            $netText = $account['net'] >= 0 ? 'Positive' : 'Outflow';
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="finance-account-name">
                                                        <span class="finance-account-avatar"><?php echo htmlspecialchars($initial); ?></span>
                                                        <span><strong><?php echo htmlspecialchars($account['name']); ?></strong><small><?php echo htmlspecialchars($periodLabel); ?></small></span>
                                                    </div>
                                                </td>
                                                <td><strong><?php echo oecrm_money($account['balance']); ?></strong></td>
                                                <td class="finance-income"><?php echo oecrm_money($account['income']); ?></td>
                                                <td class="finance-expense"><?php echo oecrm_money($account['expense']); ?></td>
                                                <td class="<?php echo $account['net'] >= 0 ? 'finance-income' : 'finance-expense'; ?>"><?php echo oecrm_money($account['net']); ?></td>
                                                <td><span class="status-pill <?php echo $netClass; ?>"><?php echo $netText; ?></span></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        <br>
        <!-- leave -->
        <?php
            $pendingLeaveCount = admin_dashboard_count($conn, "SELECT COUNT(*) AS total FROM leave_master WHERE is_approved = 0");
        ?>
        <div class="bank_details leave-approval-section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default leave-approval-card">
                        <div class="panel-heading leave-approval-heading">
                            <div>
                                <span>Employees not approve Leaves</span>
                                <small><?php echo $pendingLeaveCount; ?> pending request<?php echo $pendingLeaveCount == 1 ? '' : 's'; ?> waiting for admin review</small>
                            </div>
                            <a href="manageLeave.php" class="leave-view-all">View All</a>
                        </div>
                        <div class="panel-body">
                            <div class="leave-request-list">
                                <?php
                                    $leaveQuery = mysqli_query($conn, "SELECT lm.*, e.employeeUname, e.name, e.companyEmail FROM leave_master lm LEFT JOIN employeesTbl e ON e.id = lm.emp_id WHERE lm.is_approved = 0 ORDER BY lm.created_at DESC LIMIT 8");
                                    if ($leaveQuery && mysqli_num_rows($leaveQuery) > 0) {
                                        while ($leaveRow = mysqli_fetch_assoc($leaveQuery)) {
                                            $employeeName = !empty($leaveRow['name']) ? $leaveRow['name'] : $leaveRow['employeeUname'];
                                            $employeeUser = $leaveRow['employeeUname'] ?? '';
                                            $employeeEmail = $leaveRow['companyEmail'] ?? '-';
                                            $initial = strtoupper(substr(trim($employeeName ?: 'U'), 0, 1));
                                            $subject = $leaveRow['subject'] ?? '-';
                                            $leaveType = $leaveRow['type'] ?? '-';
                                            $dateRange = ($leaveRow['start_date'] ?? '-') . ' - ' . ($leaveRow['end_date'] ?? '-');
                                            $remarks = !empty($leaveRow['remarks']) ? $leaveRow['remarks'] : '';
                                ?>
                                    <div class="leave-request-item">
                                        <div class="leave-user-block">
                                            <span class="leave-avatar"><?php echo htmlspecialchars($initial); ?></span>
                                            <span>
                                                <strong><?php echo htmlspecialchars($employeeName); ?></strong>
                                                <small>@<?php echo htmlspecialchars($employeeUser); ?> · <?php echo htmlspecialchars($employeeEmail); ?></small>
                                            </span>
                                        </div>
                                        <div class="leave-detail-block">
                                            <span class="leave-meta-label">Leave Detail</span>
                                            <strong><?php echo htmlspecialchars($subject); ?></strong>
                                            <small><?php echo htmlspecialchars($leaveType); ?></small>
                                        </div>
                                        <div class="leave-date-block">
                                            <span class="leave-meta-label">Dates</span>
                                            <strong><?php echo htmlspecialchars($dateRange); ?></strong>
                                            <small>Requested <?php echo htmlspecialchars(date('d M Y', strtotime($leaveRow['created_at']))); ?></small>
                                        </div>
                                        <div class="leave-status-block">
                                            <span class="status-pill status-pending">Pending</span>
                                            <?php if ($remarks !== '') { ?><small><?php echo htmlspecialchars($remarks); ?></small><?php } ?>
                                        </div>
                                        <div class="leave-action-block">
                                            <form method="POST" class="leave-action-form">
                                                <?php echo oecrm_csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo (int)$leaveRow['id']; ?>">
                                                <input type="hidden" name="status" value="1">
                                                <input type="hidden" name="remarks" value="Approved from dashboard">
                                                <button type="submit" name="DashboardLeaveSave" class="btn leave-approve-btn"><i class="fa fa-check"></i> Approve</button>
                                            </form>
                                            <form method="POST" class="leave-action-form">
                                                <?php echo oecrm_csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo (int)$leaveRow['id']; ?>">
                                                <input type="hidden" name="status" value="2">
                                                <input type="hidden" name="remarks" value="Rejected from dashboard">
                                                <button type="submit" name="DashboardLeaveSave" class="btn leave-reject-btn"><i class="fa fa-times"></i> Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php
                                        }
                                    } else {
                                ?>
                                    <div class="leave-empty-state">
                                        <span><i class="fa fa-check-circle"></i></span>
                                        <strong>No pending leaves</strong>
                                        <small>All employee leave requests are reviewed.</small>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end -->
        <br>
        <div class="bank_details material-list-section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default material-list-card">
                        <div class="panel-heading">Expiring Domain/Hosting</div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table width="100%" class="table material-user-table" id="expiring_tbl" role="grid" style="width: 100%;">
                                    <thead>
                                        <tr role="row">
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Dates</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $first_date = date('Y-m-d', strtotime('today'));
                                            $last_date = date('Y-m-d', strtotime('today + 30 days'));
                                            $displayhosting = "select * from domainHostingTbl WHERE DATE(enddate) >= DATE('$first_date') AND DATE(enddate) <= DATE('$last_date') order by enddate";
                                            $result = mysqli_query($conn, $displayhosting);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $clientName = $row['clientname'] ?? '-';
                                                    $service = $row['service'] ?? '-';
                                                    $domainName = $row['domainname'] ?? '-';
                                                    $amount = $row['amount'] ?? '-';
                                                    $dateRange = ($row['startdate'] ?? '-') . ' - ' . ($row['enddate'] ?? '-');
                                                    $initial = strtoupper(substr(trim($clientName ?: 'D'), 0, 1));
                                                    $daysLeft = ceil((strtotime($row['enddate']) - strtotime(date('Y-m-d'))) / 86400);
                                                    $statusClass = $daysLeft <= 7 ? 'status-inactive' : 'status-pending';
                                                    $statusText = $daysLeft <= 7 ? 'Urgent' : 'Pending';
                                                    echo '<tr>';
                                                    echo '<td><div class="material-user-cell"><span class="material-avatar avatar-green">' . htmlspecialchars($initial) . '</span><span><strong>' . htmlspecialchars($clientName) . '</strong><small>' . htmlspecialchars($domainName) . '</small></span></div></td>';
                                                    echo '<td class="material-muted">' . htmlspecialchars($domainName) . '</td>';
                                                    echo '<td><span class="material-role role-admin"><i class="fa fa-server"></i>' . htmlspecialchars($service) . '</span><small class="material-subtext">Amount: ' . htmlspecialchars($amount) . '</small></td>';
                                                    echo '<td class="material-muted">' . htmlspecialchars($dateRange) . '</td>';
                                                    echo '<td><span class="status-pill ' . $statusClass . '">' . $statusText . '</span></td>';
                                                    echo '<td><a href="editDomainHosting.php?edit=' . $row["domain_id"] . '"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a href="deleteDomainHosting.php?delete=' . $row["domain_id"] . '" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o"></i></a></td>';
                                                    echo '</tr>';
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../data/morris-data.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>
    <script>
        $(document).ready(function() {
            $('#expiring_tbl').DataTable({
                "pageLength": 5
            });
            $('#leave_tbl').DataTable({
                "pageLength": 5
            });
        });
    </script>
    <?php
function getApprovalStatus($status)
{
    switch ($status) {
        case 0:
            return 'pending';
            break;
        case 1:
            return 'Approved';
            break;
        case 2:
            return 'Disapproved';
            break;

        default:
            # code...
            break;
    }
}
function getApprovalStatusColor($status)
{
    switch ($status) {
        case 0:
            return '';
            break;
        case 1:
            return 'style="background:lightblue;"';
            break;
        case 2:
            return 'style="background:#ffafa6;"';
            break;

        default:
            # code...
            break;
    }
}
?>
<?php include 'footer.php'; ?>






