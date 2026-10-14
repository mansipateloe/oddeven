<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
oecrm_require_csrf();

$leadId = oecrm_int_param($_POST, 'lead_id');
$companyId = oecrm_current_company_id($conn);
oecrm_require_permission($conn, 'clients', 'create');

$stmt = mysqli_prepare($conn, 'SELECT * FROM leads WHERE lead_id=? AND company_id=? AND is_active=1');
mysqli_stmt_bind_param($stmt, 'ii', $leadId, $companyId);
mysqli_stmt_execute($stmt);
$lead = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$lead) {
    http_response_code(404);
    exit('Lead not found.');
}

$display = trim((string)($lead['company_name'] ?? '')) ?: trim((string)($lead['executive_name'] ?? ''));
$legal = $display;
$email = strtolower(trim((string)($lead['email'] ?? '')));
$phone = preg_replace('/\D+/', '', (string)($lead['mobile_no1'] ?? ''));
$phone = substr($phone, 0, 10);
$phone2 = preg_replace('/\D+/', '', (string)($lead['mobile_no2'] ?? ''));
$country = trim((string)($lead['country'] ?? ''));
$city = trim((string)($lead['city'] ?? ''));
$state = trim((string)($lead['state'] ?? ''));
$address = trim((string)($lead['address'] ?? ''));
$notes = 'Converted from lead #' . $leadId . '. Source: ' . ($lead['lead_source'] ?? '');

if ($display === '') {
    $display = 'Lead ' . $leadId;
    $legal = $display;
}

$duplicate = null;
if ($email !== '') {
    $duplicateStmt = mysqli_prepare($conn, 'SELECT id FROM clients WHERE company_id=? AND LOWER(email)=LOWER(?) LIMIT 1');
    mysqli_stmt_bind_param($duplicateStmt, 'is', $companyId, $email);
    mysqli_stmt_execute($duplicateStmt);
    $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($duplicateStmt));
    mysqli_stmt_close($duplicateStmt);
}
if (!$duplicate && $phone !== '') {
    $duplicateStmt = mysqli_prepare($conn, 'SELECT id FROM clients WHERE company_id=? AND phone=? LIMIT 1');
    mysqli_stmt_bind_param($duplicateStmt, 'is', $companyId, $phone);
    mysqli_stmt_execute($duplicateStmt);
    $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($duplicateStmt));
    mysqli_stmt_close($duplicateStmt);
}

if ($duplicate) {
    $_SESSION['lead_flash'] = 'A client with this email or phone already exists.';
    header('Location: lead_details.php?leadId=' . (int) $leadId);
    exit;
}

$codePrefix = 'CL-' . str_pad($companyId, 2, '0', STR_PAD_LEFT) . '-' . date('Ym') . '-';
$last = mysqli_fetch_assoc(mysqli_query($conn, "SELECT client_code FROM clients WHERE company_id=" . (int) $companyId . " AND client_code LIKE '" . mysqli_real_escape_string($conn, $codePrefix) . "%' ORDER BY id DESC LIMIT 1"));
$sequence = 1;
if (!empty($last['client_code']) && preg_match('/(\d+)$/', $last['client_code'], $match)) {
    $sequence = (int) $match[1] + 1;
}
$clientCode = $codePrefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);

$clientType = 'company';
$status = 'active';
$industry = '';
$website = '';
$taxId = '';
$currency = 'INR';
$paymentTerms = 0;
$phoneValue = $phone;
$actor = (int) ($_SESSION['adminId'] ?? 0);

mysqli_begin_transaction($conn);
try {
    $stmt = mysqli_prepare($conn, 'INSERT INTO clients(company_id,client_code,legal_name,display_name,client_type,status,industry,website,email,phone,billing_address,city,state,country,tax_id,currency_code,payment_terms_days,notes,created_by) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    mysqli_stmt_bind_param($stmt, 'issssssssssssssisis', $companyId, $clientCode, $legal, $display, $clientType, $status, $industry, $website, $email, $phoneValue, $address, $city, $state, $country, $taxId, $currency, $paymentTerms, $notes, $actor);
    $inserted = mysqli_stmt_execute($stmt);
    $clientId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if (!$inserted || $clientId <= 0) {
        throw new RuntimeException('Client could not be created.');
    }

    $updateStmt = mysqli_prepare($conn, 'UPDATE leads SET is_active=0,status="closed" WHERE lead_id=? AND company_id=? AND is_active=1');
    mysqli_stmt_bind_param($updateStmt, 'ii', $leadId, $companyId);
    $updated = mysqli_stmt_execute($updateStmt);
    $affected = mysqli_stmt_affected_rows($updateStmt);
    mysqli_stmt_close($updateStmt);

    if (!$updated || $affected < 1) {
        throw new RuntimeException('Lead could not be marked as converted.');
    }

    oecrm_audit($conn, 'clients', 'create', 'client', $clientId, 'Lead converted to client', $lead, ['lead_id' => $leadId, 'client_code' => $clientCode]);
    mysqli_commit($conn);
} catch (Throwable $exception) {
    mysqli_rollback($conn);
    $_SESSION['lead_flash'] = $exception->getMessage();
    header('Location: lead_details.php?leadId=' . (int) $leadId);
    exit;
}

$_SESSION['client_flash'] = 'Lead converted to client successfully.';
$_SESSION['lead_flash'] = 'Lead converted to client successfully.';
header('Location: clientProfile.php?id=' . $clientId);
exit;
