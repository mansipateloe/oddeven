<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../depositCollections.php';
require_once __DIR__ . '/../invoiceStatus.php';
oecrm_require_admin_login();
if (!oecrm_can($conn, 'finance', 'view') && !oecrm_can($conn, 'finance', 'create')) {
    http_response_code(403);
    exit('You do not have permission to view collections.');
}
$collectionCompanyId = oecrm_current_company_id($conn);
include 'header.php';
$collectionProjects = mysqli_query($conn, 'SELECT id,projectName FROM projectstbl WHERE company_id=' . (int) $collectionCompanyId . ' ORDER BY projectName');
$collectionAccounts = mysqli_query($conn, 'SELECT account_id,account_name FROM account ORDER BY account_name');
$deposit = null;
$requestedInvoice = (int) ($_GET['invoice_id'] ?? 0);
if ($requestedInvoice > 0) {
    $lookup = oecrm_invoice_schema_prepare($conn, 'SELECT i.* FROM invoiceTbl i JOIN projectstbl p ON p.id=i.project_Id WHERE i.invoice_id=? AND p.company_id=?');
    mysqli_stmt_bind_param($lookup, 'ii', $requestedInvoice, $collectionCompanyId);
    mysqli_stmt_execute($lookup);
    $requested = mysqli_fetch_assoc(mysqli_stmt_get_result($lookup));
    mysqli_stmt_close($lookup);
    if ($requested && (int) $requested['status'] === 1) {
        $deposit = ['invoice_id' => $requestedInvoice, 'project_Id' => (int) $requested['project_Id']];
    } else {
        $_SESSION['deposit_flash'] = ['type' => 'warning', 'message' => 'Select a pending invoice belonging to the active company.'];
    }
}
$stmt = oecrm_invoice_schema_prepare($conn, 'SELECT d.*,i.invoice_no,i.invoice_date,i.status AS invoice_status,a.account_name,p.projectName
    FROM deposit d JOIN invoiceTbl i ON i.invoice_id=d.invoice_id
    JOIN projectstbl p ON p.id=i.project_Id LEFT JOIN account a ON a.account_id=d.account_Id
    WHERE p.company_id=? ORDER BY d.deposit_date DESC,d.deposit_id DESC');
mysqli_stmt_bind_param($stmt, 'i', $collectionCompanyId);
mysqli_stmt_execute($stmt);
$collections = mysqli_stmt_get_result($stmt);
$flash = $_SESSION['deposit_flash'] ?? null;
unset($_SESSION['deposit_flash']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?><div class="alert alert-<?php echo oecrm_h($flash['type']); ?>"><?php echo oecrm_h($flash['message']); ?></div><?php endif; ?>
    <?php if (oecrm_can($conn, 'finance', 'create')): ?>
    <div class="panel panel-default">
        <div class="panel-heading">Add Deposit</div>
        <div class="panel-body"><?php include 'depositForm.php'; ?></div>
    </div>
    <?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading">Collections &amp; Deposits</div>
        <div class="panel-body table-responsive">
            <table class="table table-striped table-bordered" id="deposit_table">
                <thead><tr><th>Date</th><th>Invoice No.</th><th>Receiving Bank Account</th><th>Project</th><th>Amount</th><th>Invoice Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php if (!mysqli_num_rows($collections)): ?><tr><td colspan="7" class="text-center">No deposits found.</td></tr><?php endif; ?>
                <?php while ($row = mysqli_fetch_assoc($collections)): ?>
                    <tr>
                        <td><?php echo oecrm_h($row['deposit_date']); ?></td>
                        <td><?php echo oecrm_h($row['invoice_no'] ?: '#' . $row['invoice_id']); ?></td>
                        <td><?php echo oecrm_h($row['account_name'] ?? 'Account unavailable'); ?></td>
                        <td><?php echo oecrm_h($row['projectName']); ?></td>
                        <td class="text-right"><?php echo number_format((float) $row['amount'], 2); ?></td>
                        <td><?php echo oecrm_legacy_invoice_status_badge(['status' => $row['invoice_status']]); ?></td>
                        <td>
                            <?php if (oecrm_can($conn, 'finance', 'edit')): ?><a class="icon-action" href="editDeposit.php?edit=<?php echo (int) $row['deposit_id']; ?>" title="Edit Deposit"><i class="fa fa-pencil"></i></a><?php endif; ?>
                            <a class="icon-action" href="viewInvoice.php?id=<?php echo (int) $row['invoice_id']; ?>" title="View Invoice"><i class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php mysqli_stmt_close($stmt); include 'footer.php'; ?>