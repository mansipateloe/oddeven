<?php
include 'header.php';
require_once __DIR__ . '/../invoiceStatus.php';
require_once __DIR__ . '/../invoiceTax.php';
oecrm_invoice_tax_schema($conn);
$id = (int) ($_GET['id'] ?? 0);
$result = oecrm_invoice_schema_query($conn, "SELECT i.*,p.projectName FROM invoiceTbl i LEFT JOIN projectstbl p ON p.id=i.project_Id WHERE i.invoice_id=$id LIMIT 1");
$invoice = mysqli_fetch_assoc($result);
?>
<link rel="stylesheet" href="../dist/css/invoice-print.css?v=20261021-1">
<div id="page-wrapper" class="invoice-view-page">
    <?php oecrm_legacy_invoice_flash(); ?>
    <div class="invoice-toolbar">
        <a href="addInvoice.php" class="btn btn-default">Back to Invoices</a>
        <?php if ($invoice): ?><button type="button" class="btn btn-primary" onclick="window.print()">Print / Save PDF</button><?php endif; ?>
    </div>
    <?php if (!$invoice): ?>
        <div class="alert alert-warning">Invoice not found.</div>
    <?php else: ?>
    <article class="invoice-document" aria-label="Invoice">
        <header class="invoice-header">
            <div class="invoice-brand">
                <img class="invoice-logo" src="../admin/img/logo.png" alt="Oddeven Infotech">
                <address>
                    122, Shree Ugati Corporate Park<br>
                    Por - Kudasan Rd, Kudasan,<br>
                    Gujarat 382421<br>
                    Phone: 9081022999
                </address>
            </div>
            <div class="invoice-meta">
                <h1>Invoice</h1>
                <p class="invoice-number"><?php echo oecrm_h($invoice['invoice_no'] ?: '#' . $id); ?></p>
                <p><strong>Invoice ID:</strong> <?php echo $id; ?></p>
                <p><strong>Invoice Date:</strong> <?php echo oecrm_h((new DateTime($invoice['invoice_date']))->format('d-m-Y')); ?></p>
                <p><strong>Status:</strong> <?php echo oecrm_legacy_invoice_status_badge($invoice); ?></p>
            </div>
        </header>
        <section class="invoice-recipient" aria-label="Bill to">
            <h2>Bill To</h2>
            <p><strong><?php echo oecrm_h($invoice['person_name']); ?></strong></p>
            <p><?php echo oecrm_h($invoice['projectName']); ?></p>
        </section>
        <table class="invoice-items">
            <colgroup><col class="invoice-col-index"><col class="invoice-col-description"><col class="invoice-col-bank"><col class="invoice-col-total"></colgroup>
            <thead><tr><th scope="col">#</th><th scope="col">Particulars</th><th scope="col">Bank</th><th scope="col" class="invoice-amount">Total</th></tr></thead>
            <tbody>
            <?php
            $items = mysqli_query($conn, "SELECT d.*,a.account_name,c.country_symbols FROM invoice_details d LEFT JOIN account a ON a.account_id=d.bank_id LEFT JOIN country c ON c.country_id=d.country_id WHERE d.invoice_id=$id");
            $index = 1;
            while ($item = mysqli_fetch_assoc($items)):
            ?>
                <tr>
                    <td><?php echo $index++; ?></td>
                    <td><?php echo nl2br(oecrm_h($item['perticular'])); ?></td>
                    <td><?php echo oecrm_h($item['account_name'] ?? ''); ?></td>
                    <td class="invoice-amount"><?php echo oecrm_h($item['country_symbols'] ?? '') . ' ' . number_format((float) $item['amount'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <div class="invoice-summary">
            <section class="invoice-notes">
                <h2>Notes</h2>
                <p>All accounts are to be paid within 7 days from receipt of invoice. To be paid by cheque or credit card or direct payment online. If account is not paid within 7 days the credits details supplied as confirmation of work undertaken will be charged the agreed quoted fee noted above.</p>
            </section>
            <section class="invoice-totals" aria-label="Invoice totals">
                <?php if (($invoice['tax_mode'] ?? 'none') !== 'none'): ?>
                    <p><span>Subtotal</span><span><?php echo number_format((float) $invoice['subtotal_amount'], 2); ?></span></p>
                    <?php foreach (['cgst', 'sgst', 'igst'] as $taxType): ?>
                        <?php if ((float) ($invoice[$taxType . '_percent'] ?? 0) > 0): ?>
                            <p><span><?php echo strtoupper($taxType); ?> (<?php echo number_format((float) $invoice[$taxType . '_percent'], 2); ?>%)</span><span><?php echo number_format((float) $invoice[$taxType . '_amount'], 2); ?></span></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <p class="invoice-grand-total"><strong>Total</strong><strong><?php echo number_format((float) $invoice['total_amount'], 2); ?></strong></p>
            </section>
        </div>
    </article>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
