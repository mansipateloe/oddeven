<?php
// Bank Details is the selection source; saved invoice_details keeps its legacy account ID.
function oecrm_invoice_bank_options($conn)
{
    $result = mysqli_query($conn, 'SELECT bank_id,bank_name,account_number,ifsc_code FROM bank_details ORDER BY bank_name,bank_id');
    $html = '<option value="">Select bank account</option>';
    while ($bank = mysqli_fetch_assoc($result)) {
        $label = trim($bank['bank_name']);
        if ($bank['account_number'] !== '') $label .= ' - A/c ending ' . substr($bank['account_number'], -4);
        if ($bank['ifsc_code'] !== '') $label .= ' - ' . $bank['ifsc_code'];
        $html .= '<option value="bank:' . (int) $bank['bank_id'] . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    return $html;
}

// Call inside the invoice transaction: concurrent saves cannot create duplicate mappings.
function oecrm_invoice_bank_resolve($conn, $selection)
{
    if (!is_string($selection) || !preg_match('/^bank:([1-9][0-9]*)$/D', $selection, $match)) {
        throw new InvalidArgumentException('Please select a current bank account from Bank Details.');
    }
    $bankId = (int) $match[1];
    $stmt = mysqli_prepare($conn, 'SELECT bank_id,bank_name FROM bank_details WHERE bank_id=? FOR UPDATE');
    mysqli_stmt_bind_param($stmt, 'i', $bankId); mysqli_stmt_execute($stmt);
    $bank = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    if (!$bank) throw new InvalidArgumentException('The selected bank was deleted. Reload and choose an available bank.');
    $key = 'invoice_bank_account_' . $bankId;
    $stmt = mysqli_prepare($conn, 'SELECT setting_value FROM app_settings WHERE company_id=0 AND setting_key=?');
    mysqli_stmt_bind_param($stmt, 's', $key); mysqli_stmt_execute($stmt);
    $mapping = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    if ($mapping) {
        $accountId = (int) $mapping['setting_value'];
        $stmt = mysqli_prepare($conn, 'SELECT account_id FROM account WHERE account_id=?');
        mysqli_stmt_bind_param($stmt, 'i', $accountId); mysqli_stmt_execute($stmt);
        $account = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
        if ($account) return $accountId;
        throw new InvalidArgumentException('The selected bank ledger account is unavailable. Please contact your administrator.');
    }
    // Do not copy existing balances or rewrite old invoices when creating a collection ledger.
    $stmt = mysqli_prepare($conn, "INSERT INTO account(account_name,balance) VALUES(?,'0')");
    mysqli_stmt_bind_param($stmt, 's', $bank['bank_name']); mysqli_stmt_execute($stmt);
    $accountId = (int) mysqli_insert_id($conn); mysqli_stmt_close($stmt);
    $value = (string) $accountId;
    $stmt = mysqli_prepare($conn, 'INSERT INTO app_settings(company_id,setting_key,setting_value,is_encrypted) VALUES(0,?,?,0)');
    mysqli_stmt_bind_param($stmt, 'ss', $key, $value); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    return $accountId;
}