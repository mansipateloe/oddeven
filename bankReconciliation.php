<?php
require_once __DIR__ . '/depositCollections.php';

function oecrm_save_current_bank_reconciliation($conn, $companyId, $actor, $input)
{
    $selection = $input['account_id'] ?? '';
    $date = trim((string) ($input['statement_date'] ?? ''));
    $parsed = DateTime::createFromFormat('!Y-m-d', $date);
    $statementValue = trim((string) ($input['statement_balance'] ?? ''));
    if (!is_string($selection) || !preg_match('/^bank:([1-9][0-9]*)$/D', $selection, $match)
        || !$parsed || $parsed->format('Y-m-d') !== $date
        || !preg_match('/^-?[0-9]{1,12}(\.[0-9]{1,2})?$/D', $statementValue)) {
        throw new InvalidArgumentException('Select a current bank, valid date and statement balance.');
    }
    $notes = trim((string) ($input['notes'] ?? ''));
    mysqli_begin_transaction($conn);
    try {
        $account = oecrm_invoice_bank_resolve($conn, $selection);
        $banks = oecrm_collection_bank_accounts($conn);
        $selected = null;
        while ($bank = mysqli_fetch_assoc($banks)) {
            if ((int) $bank['bank_id'] === (int) $match[1]) $selected = $bank;
        }
        mysqli_free_result($banks);
        if (!$selected) throw new InvalidArgumentException('The selected bank is no longer available.');
        $book = round((float) $selected['current_balance'], 2);
        $statement = round((float) $statementValue, 2);
        $diff = round($statement - $book, 2);
        $status = abs($diff) < 0.01 ? 'reconciled' : 'draft';
        $stmt = mysqli_prepare($conn, 'INSERT INTO bank_reconciliation(company_id,account_id,statement_date,statement_balance,book_balance,difference_amount,status,notes,reconciled_by,reconciled_at) VALUES(?,?,?,?,?,?,?,?,?,IF(?="reconciled",NOW(),NULL))');
        mysqli_stmt_bind_param($stmt, 'iisdddssis', $companyId, $account, $date, $statement, $book, $diff, $status, $notes, $actor, $status);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
        return $id;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}
