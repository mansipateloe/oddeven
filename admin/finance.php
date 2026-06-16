<?php
$active_menu = 'finance';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'finance', 'view');

$companyId = oecrm_current_company_id($conn);
$month = preg_match('/^\d{4}-\d{2}$/', $_GET['month'] ?? '') ? $_GET['month'] : date('Y-m');
$from = $month . '-01';
$to = date('Y-m-t', strtotime($from));
$safeFrom = mysqli_real_escape_string($conn, $from);
$safeTo = mysqli_real_escape_string($conn, $to);
$summary = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) revenue,COALESCE(SUM(paid_amount),0) collected,COALESCE(SUM(total_amount-paid_amount),0) outstanding,COALESCE(SUM(tax_amount),0) gst_collected FROM finance_invoices WHERE company_id=$companyId AND invoice_date BETWEEN '$safeFrom' AND '$safeTo' AND status<>'cancelled'"));
$expenses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount),0) total,COALESCE(SUM(tax_amount),0) gst_paid FROM finance_expenses WHERE company_id=$companyId AND expense_date BETWEEN '$safeFrom' AND '$safeTo'"));
$payroll = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(net_salary),0) total FROM payroll_items pi JOIN payroll_runs pr ON pr.id=pi.payroll_run_id WHERE pr.company_id=$companyId AND CONCAT(pr.period_year,'-',LPAD(pr.period_month,2,'0'))='$month'"));
$net = (float) $summary['revenue'] - (float) $expenses['total'] - (float) $payroll['total'];
$invoices = mysqli_query($conn, 'SELECT f.*,c.display_name FROM finance_invoices f JOIN clients c ON c.id=f.client_id WHERE f.company_id=' . (int) $companyId . ' ORDER BY f.id DESC LIMIT 30');
$expenseRows = mysqli_query($conn, 'SELECT * FROM finance_expenses WHERE company_id=' . (int) $companyId . ' ORDER BY id DESC LIMIT 20');
$flash = $_SESSION['finance_flash'] ?? '';
unset($_SESSION['finance_flash']);
?>
<div id="page-wrapper" class="compact-admin-page finance-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <form method="get" class="finance-month-filter"><input type="month" name="month" value="<?php echo oecrm_h($month); ?>" class="form-control"><button class="btn btn-default">View Period</button></form>
    <div class="resource-summary">
        <div><i class="fa fa-line-chart"></i><span><small>Revenue</small><strong><?php echo number_format($summary['revenue'], 2); ?></strong></span></div>
        <div><i class="fa fa-money"></i><span><small>Expenses + Payroll</small><strong><?php echo number_format($expenses['total'] + $payroll['total'], 2); ?></strong></span></div>
        <div><i class="fa fa-clock-o"></i><span><small>Outstanding</small><strong><?php echo number_format($summary['outstanding'], 2); ?></strong></span></div>
        <div><i class="fa fa-pie-chart"></i><span><small>Net Profit</small><strong><?php echo number_format($net, 2); ?></strong></span></div>
    </div>
    <div class="finance-actions">
        <a href="invoiceEditor.php" class="btn btn-primary"><i class="fa fa-file-text"></i> New Invoice</a>
        <a href="expenseEditor.php" class="btn btn-danger"><i class="fa fa-credit-card"></i> Add Expense</a>
        <a href="gstReport.php?month=<?php echo oecrm_h($month); ?>" class="btn btn-default"><i class="fa fa-percent"></i> GST Report</a>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Recent Invoices</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead><tr><th>Invoice</th><th>Client</th><th>Date</th><th>Total</th><th>Paid</th><th>Outstanding</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($invoices) === 0): ?><tr><td colspan="8" class="empty-cell">No modern invoices yet.</td></tr><?php endif; ?>
                <?php while ($invoice = mysqli_fetch_assoc($invoices)): ?>
                    <tr>
                        <td><a href="invoiceEditor.php?id=<?php echo (int) $invoice['id']; ?>"><?php echo oecrm_h($invoice['invoice_number']); ?></a></td>
                        <td><?php echo oecrm_h($invoice['display_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($invoice['invoice_date'])); ?></td>
                        <td><?php echo number_format($invoice['total_amount'], 2); ?></td>
                        <td><?php echo number_format($invoice['paid_amount'], 2); ?></td>
                        <td><?php echo number_format($invoice['total_amount'] - $invoice['paid_amount'], 2); ?></td>
                        <td><span class="client-status <?php echo $invoice['status']; ?>"><?php echo ucwords(str_replace('_', ' ', $invoice['status'])); ?></span></td>
                        <td>
                            <a class="icon-action" href="invoiceEditor.php?id=<?php echo (int) $invoice['id']; ?>" title="Edit invoice"><i class="fa fa-pencil"></i></a>
                            <?php if ($invoice['status'] !== 'cancelled' && oecrm_can($conn, 'finance', 'delete')): ?>
                                <form method="post" action="financeAction.php" style="display:inline" data-confirm="Cancel this invoice?">
                                    <?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="cancel_invoice"><input type="hidden" name="id" value="<?php echo (int) $invoice['id']; ?>">
                                    <button class="icon-action danger" title="Cancel invoice"><i class="fa fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Recent Expenses</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead><tr><th>Date</th><th>Category</th><th>Vendor</th><th>Description</th><th>Amount</th><th>GST Paid</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($expenseRows) === 0): ?><tr><td colspan="7" class="empty-cell">No expenses found.</td></tr><?php endif; ?>
                <?php while ($expense = mysqli_fetch_assoc($expenseRows)): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                        <td><?php echo oecrm_h($expense['category']); ?></td>
                        <td><?php echo oecrm_h($expense['vendor']); ?></td>
                        <td><?php echo oecrm_h($expense['description']); ?></td>
                        <td><?php echo number_format($expense['amount'], 2); ?></td>
                        <td><?php echo number_format($expense['tax_amount'], 2); ?></td>
                        <td>
                            <?php if (oecrm_can($conn, 'finance', 'delete')): ?>
                                <form method="post" action="financeAction.php" style="display:inline" data-confirm="Delete this expense?">
                                    <?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="delete_expense"><input type="hidden" name="id" value="<?php echo (int) $expense['id']; ?>">
                                    <button class="icon-action danger" title="Delete expense"><i class="fa fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
