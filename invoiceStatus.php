<?php
// Existing invoiceTbl.status values: 1 = pending, 2 = paid, 3 = cancelled.
// Reuse the existing integer column: deployment needs no SQL migration.
function oecrm_legacy_invoice_status_badge($invoice)
{
    $status = (int) ($invoice['status'] ?? 1);
    $labels = [1 => ['warning', 'Pending'], 2 => ['success', 'Paid'], 3 => ['danger', 'Cancelled']];
    [$style, $label] = $labels[$status] ?? ['default', 'Unknown'];
    return '<span class="label label-' . $style . '">' . $label . '</span>';
}

function oecrm_legacy_invoice_cancel_form($conn, $invoice, $returnPage = 'addInvoice.php')
{
    if ((int) ($invoice['status'] ?? 1) !== 1 || !oecrm_can($conn, 'finance', 'edit')) {
        return;
    }
    $message = 'Cancel invoice ' . ($invoice['invoice_no'] ?: '#' . $invoice['invoice_id']) . '? It will remain in the listing as Cancelled and cannot receive payments.';
    echo '<form method="post" action="invoices.php" style="display:inline-block;margin:0;" data-confirm="' . oecrm_h($message) . '">';
    echo oecrm_csrf_field();
    echo '<input type="hidden" name="action" value="cancel_invoice">';
    echo '<input type="hidden" name="id" value="' . (int) $invoice['invoice_id'] . '">';
    echo '<input type="hidden" name="return_page" value="' . oecrm_h($returnPage) . '">';
    echo '<button type="submit" class="btn btn-danger btn-xs invoice-cancel-btn" title="Cancel Invoice"><i class="fa fa-ban"></i> Cancel Invoice</button>';
    echo '</form>';
}

function oecrm_legacy_invoice_flash()
{
    $flash = $_SESSION['invoice_flash'] ?? null;
    unset($_SESSION['invoice_flash']);
    if (!empty($flash['message'])) {
        echo '<div class="alert alert-' . oecrm_h($flash['type']) . '" role="alert">' . oecrm_h($flash['message']) . '</div>';
    }
}

// Call inside a transaction; cancellation and payment serialize on the invoice.
function oecrm_legacy_invoice_lock($conn, $id)
{
    $stmt = mysqli_prepare($conn, 'SELECT * FROM invoiceTbl WHERE invoice_id=? FOR UPDATE');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $invoice = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$invoice) {
        throw new InvalidArgumentException('Invoice not found.');
    }
    return $invoice;
}

function oecrm_legacy_invoice_payment_guard($conn, $id, $newPayment = true)
{
    $invoice = oecrm_legacy_invoice_lock($conn, $id);
    if ((int) $invoice['status'] === 3) {
        throw new InvalidArgumentException('Cancelled invoices cannot receive payments.');
    }
    if ($newPayment && (int) $invoice['status'] !== 1) {
        throw new InvalidArgumentException('This invoice has already been paid or is not pending.');
    }
    return $invoice;
}

function oecrm_cancel_legacy_invoice($conn, $id)
{
    if ($id <= 0) {
        throw new InvalidArgumentException('Please select a valid invoice.');
    }
    mysqli_begin_transaction($conn);
    try {
        $invoice = oecrm_legacy_invoice_lock($conn, $id);
        if ((int) $invoice['status'] === 3) {
            mysqli_commit($conn);
            return null; // Repeated submissions are harmless.
        }
        if ((int) $invoice['status'] !== 1) {
            throw new InvalidArgumentException('Paid invoices cannot be cancelled. Please resolve the recorded payment first.');
        }
        $stmt = mysqli_prepare($conn, 'SELECT deposit_id FROM deposit WHERE invoice_id=? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $deposit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if ($deposit) {
            throw new InvalidArgumentException('This invoice has a recorded deposit and cannot be cancelled.');
        }
        $stmt = mysqli_prepare($conn, 'UPDATE invoiceTbl SET status=3 WHERE invoice_id=? AND status=1');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException(mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
        return $invoice;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}