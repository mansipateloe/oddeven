<?php
require_once __DIR__ . '/invoiceTax.php';
require_once __DIR__ . '/invoiceBanks.php';
require_once __DIR__ . '/expenseBanks.php';

// Bank Details is the current bank list; account holds historical ledger balances.
function oecrm_collection_bank_accounts($conn)
{
    return mysqli_query($conn, 'SELECT b.bank_id,b.bank_name AS account_name,m.setting_value AS account_id,b.current_balance
        FROM (' . oecrm_expense_bank_list_sql() . ') b LEFT JOIN app_settings m ON m.company_id=0
        AND CAST(m.setting_key AS BINARY)=CAST(CONCAT(\'invoice_bank_account_\',b.bank_id) AS BINARY)
        ORDER BY b.bank_name,b.bank_id');
}

function oecrm_collection_deposit($conn, $companyId, $id, $lock = false)
{
    $stmt = oecrm_invoice_schema_prepare($conn, 'SELECT d.* FROM deposit d
        JOIN invoiceTbl i ON i.invoice_id=d.invoice_id JOIN projectstbl p ON p.id=i.project_Id
        WHERE d.deposit_id=? AND p.company_id=?' . ($lock ? ' FOR UPDATE' : ''));
    mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$row) throw new InvalidArgumentException('Deposit not found for the active company.');
    return $row;
}

function oecrm_collection_invoice_meta($conn, $invoice)
{
    $stmt = mysqli_prepare($conn, 'SELECT DISTINCT CONCAT(\'bank:\',b.bank_id) AS account_id,b.bank_name AS account_name FROM invoice_details d
        JOIN app_settings m ON m.company_id=0 AND CAST(m.setting_value AS UNSIGNED)=d.bank_id
        JOIN bank_details b ON CAST(m.setting_key AS BINARY)=CAST(CONCAT(\'invoice_bank_account_\',b.bank_id) AS BINARY)
        WHERE d.invoice_id=? ORDER BY b.bank_name');
    mysqli_stmt_bind_param($stmt, 'i', $invoice['invoice_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $banks = [];
    while ($bank = mysqli_fetch_assoc($result)) $banks[] = $bank;
    mysqli_stmt_close($stmt);
    return [
        'invoice_id' => (int) $invoice['invoice_id'],
        'invoice_no' => $invoice['invoice_no'] ?: '#' . $invoice['invoice_id'],
        'invoice_date' => $invoice['invoice_date'],
        'person_name' => $invoice['person_name'],
        'total_amount' => number_format((float) $invoice['total_amount'], 2, '.', ''),
        'tax_amount' => number_format((float) ($invoice['cgst_amount'] ?? 0) + (float) ($invoice['sgst_amount'] ?? 0) + (float) ($invoice['igst_amount'] ?? 0), 2, '.', ''),
        'banks' => $banks,
        'suggested_account_id' => count($banks) === 1 ? $banks[0]['account_id'] : 0,
    ];
}

function oecrm_collection_invoice_options($conn, $companyId, $projectId, $depositId = 0)
{
    $current = $depositId > 0 ? oecrm_collection_deposit($conn, $companyId, $depositId) : null;
    $currentInvoice = $current ? (int) $current['invoice_id'] : 0;
    $stmt = oecrm_invoice_schema_prepare($conn, 'SELECT i.* FROM invoiceTbl i JOIN projectstbl p ON p.id=i.project_Id
        WHERE p.company_id=? AND i.project_Id=? AND (i.status=1 OR i.invoice_id=?)
        AND i.status<>3 AND NOT EXISTS (SELECT 1 FROM deposit d WHERE d.invoice_id=i.invoice_id AND d.deposit_id<>?)
        ORDER BY i.invoice_date DESC,i.invoice_id DESC');
    mysqli_stmt_bind_param($stmt, 'iiii', $companyId, $projectId, $currentInvoice, $depositId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $invoices = [];
    while ($row = mysqli_fetch_assoc($result)) $invoices[] = oecrm_collection_invoice_meta($conn, $row);
    mysqli_stmt_close($stmt);
    return $invoices;
}

// Full-invoice collections. The database invoice total is authoritative.
function oecrm_save_collection($conn, $companyId, $input, $id = 0)
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $date = trim((string) ($input['deposit_date'] ?? ''));
    $parsed = DateTime::createFromFormat('!Y-m-d', $date);
    $invoiceId = (int) ($input['invoice_id'] ?? 0);
    $bankSelection = $input['account_Id'] ?? '';
    $validBank = is_string($bankSelection) && preg_match('/^bank:([1-9][0-9]*)$/D', $bankSelection);
    $projectId = (int) ($input['project_Id'] ?? 0);
    if (!$parsed || $parsed->format('Y-m-d') !== $date || $invoiceId <= 0 || !$validBank || $projectId <= 0) {
        throw new InvalidArgumentException('Select a valid date, project, invoice and receiving bank account.');
    }
    $description = trim((string) ($input['description'] ?? ''));
    mysqli_begin_transaction($conn);
    try {
        $snapshot = $id > 0 ? oecrm_collection_deposit($conn, $companyId, $id) : null;
        $invoiceIds = array_unique([$invoiceId, $snapshot ? (int) $snapshot['invoice_id'] : $invoiceId]);
        sort($invoiceIds, SORT_NUMERIC);
        $invoices = [];
        foreach ($invoiceIds as $lockId) {
            $stmt = oecrm_invoice_schema_prepare($conn, 'SELECT i.* FROM invoiceTbl i
                JOIN projectstbl p ON p.id=i.project_Id WHERE i.invoice_id=? AND p.company_id=? FOR UPDATE');
            mysqli_stmt_bind_param($stmt, 'ii', $lockId, $companyId);
            mysqli_stmt_execute($stmt);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
            if (!$row) throw new InvalidArgumentException('Invoice not found for the active company.');
            $invoices[$lockId] = $row;
        }
        $old = $id > 0 ? oecrm_collection_deposit($conn, $companyId, $id, true) : null;
        if ($old && (int) $old['invoice_id'] !== (int) $snapshot['invoice_id']) {
            throw new InvalidArgumentException('This deposit was changed. Reload before saving.');
        }
        $invoice = $invoices[$invoiceId];
        if ((int) $invoice['project_Id'] !== $projectId) throw new InvalidArgumentException('The invoice does not belong to the selected project.');
        if ((int) $invoice['status'] === 3) throw new InvalidArgumentException('Cancelled invoices cannot receive deposits.');
        if ((!$old || (int) $old['invoice_id'] !== $invoiceId) && (int) $invoice['status'] !== 1) {
            throw new InvalidArgumentException('This invoice is already paid or is not pending.');
        }
        $stmt = mysqli_prepare($conn, 'SELECT deposit_id FROM deposit WHERE invoice_id=? AND deposit_id<>? LIMIT 1 FOR UPDATE');
        mysqli_stmt_bind_param($stmt, 'ii', $invoiceId, $id);
        mysqli_stmt_execute($stmt);
        $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if ($duplicate) throw new InvalidArgumentException('This invoice already has a recorded deposit.');
        $amount = number_format((float) $invoice['total_amount'], 2, '.', '');
        if (!is_numeric($invoice['total_amount']) || (float) $amount <= 0 || (float) $amount > 999999999999.99) {
            throw new InvalidArgumentException('The invoice must have a valid positive total.');
        }
        if ($old && (!is_numeric($old['amount']) || (float) $old['amount'] < 0)) {
            throw new InvalidArgumentException('The existing deposit amount is invalid and cannot be adjusted automatically.');
        }
        // Resolve only a current Bank Details entry; retain legacy ledger IDs in deposits.
        $accountId = oecrm_invoice_bank_resolve($conn, $bankSelection);
        $accountIds = array_unique([$accountId, $old ? (int) $old['account_Id'] : $accountId]);
        sort($accountIds, SORT_NUMERIC);
        foreach ($accountIds as $bankId) {
            $stmt = mysqli_prepare($conn, 'SELECT account_id,balance FROM account WHERE account_id=? FOR UPDATE');
            mysqli_stmt_bind_param($stmt, 'i', $bankId);
            mysqli_stmt_execute($stmt);
            $account = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
            if (!$account || !is_numeric($account['balance'])) throw new InvalidArgumentException('A selected bank account is missing or has an invalid balance.');
        }
        $tax = number_format((float) ($invoice['cgst_amount'] ?? 0) + (float) ($invoice['sgst_amount'] ?? 0) + (float) ($invoice['igst_amount'] ?? 0), 2, '.', '');
        $subject = (string) ($invoice['invoice_no'] ?: '#' . $invoiceId);
        if ($old) {
            $stmt = mysqli_prepare($conn, 'UPDATE deposit SET deposit_date=?,account_Id=?,project_Id=?,invoice_id=?,subject=?,description=?,amount=?,tax_amount=?,approx_amount=? WHERE deposit_id=?');
            mysqli_stmt_bind_param($stmt, 'siiisssssi', $date, $accountId, $projectId, $invoiceId, $subject, $description, $amount, $tax, $amount, $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO deposit(deposit_date,account_Id,project_Id,invoice_id,subject,description,amount,tax_amount,approx_amount) VALUES(?,?,?,?,?,?,?,?,?)');
            mysqli_stmt_bind_param($stmt, 'siiisssss', $date, $accountId, $projectId, $invoiceId, $subject, $description, $amount, $tax, $amount);
        }
        mysqli_stmt_execute($stmt);
        if (!$id) $id = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        if ($old) {
            $stmt = mysqli_prepare($conn, 'UPDATE account SET balance=CAST(balance AS DECIMAL(20,2))-CAST(? AS DECIMAL(20,2)) WHERE account_id=?');
            mysqli_stmt_bind_param($stmt, 'si', $old['amount'], $old['account_Id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        $stmt = mysqli_prepare($conn, 'UPDATE account SET balance=CAST(balance AS DECIMAL(20,2))+CAST(? AS DECIMAL(20,2)) WHERE account_id=?');
        mysqli_stmt_bind_param($stmt, 'si', $amount, $accountId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $stmt = oecrm_invoice_schema_prepare($conn, 'UPDATE invoiceTbl SET status=2 WHERE invoice_id=?');
        mysqli_stmt_bind_param($stmt, 'i', $invoiceId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($old && (int) $old['invoice_id'] !== $invoiceId) {
            $stmt = oecrm_invoice_schema_prepare($conn, 'UPDATE invoiceTbl SET status=1 WHERE invoice_id=? AND status<>3
                AND NOT EXISTS (SELECT 1 FROM deposit WHERE invoice_id=?)');
            mysqli_stmt_bind_param($stmt, 'ii', $old['invoice_id'], $old['invoice_id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        mysqli_commit($conn);
        return ['id' => $id, 'invoice_no' => $subject, 'amount' => $amount, 'old' => $old];
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}