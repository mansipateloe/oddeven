<?php
include 'dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
oecrm_require_csrf();
oecrm_require_permission($conn, 'clients', 'edit');

function oecrm_lead_redirect_with_error(int $id, string $message): void
{
    $_SESSION['lead_flash'] = $message;
    header('Location:lead_edit.php?edit=' . max(0, $id));
    exit;
}

if (!isset($_POST['leadDate'])) {
    http_response_code(400);
    echo 'Invalid request.';
    exit;
}

$id = oecrm_int_param($_POST, 'lead_id');
$companyId = oecrm_current_company_id($conn);

$leaddate = trim((string) ($_POST['leadDate'] ?? ''));
$executive = trim((string) ($_POST['executiveName'] ?? ''));
$companyname = trim((string) ($_POST['company'] ?? ''));
$contact = trim((string) ($_POST['cperson'] ?? ''));
$email = trim((string) ($_POST['emailid'] ?? ''));
$mobileone = substr(preg_replace('/\D+/', '', (string) ($_POST['mobileno1'] ?? '')), 0, 10);
$mobiletwo = substr(preg_replace('/\D+/', '', (string) ($_POST['mobileno2'] ?? '')), 0, 10);
$emailtwo = trim((string) ($_POST['emailid2'] ?? ''));
$nick_name = trim((string) ($_POST['nick_name'] ?? ''));
$status = trim((string) ($_POST['status'] ?? ''));
$lead_source = (int) ($_POST['lead_source'] ?? 0);
$address = trim((string) ($_POST['address'] ?? ''));
$city = trim((string) ($_POST['city'] ?? ''));

$allowedStatuses = ['pending', 'inprogress', 'completed', 'closed'];
if ($companyname === '' || $contact === '' || !in_array($status, $allowedStatuses, true)) {
    oecrm_lead_redirect_with_error($id, 'Company Name, Contact Person and Status are required.');
}

if ($leaddate === '' || !strtotime($leaddate)) {
    $leaddate = date('Y-m-d');
}
if ($executive === '') {
    $executive = $companyname ?: $contact;
}
if ($mobileone !== '' && strlen($mobileone) !== 10) {
    oecrm_lead_redirect_with_error($id, 'Mobile No1 must contain exactly 10 digits when entered.');
}
if ($mobiletwo !== '' && strlen($mobiletwo) !== 10) {
    oecrm_lead_redirect_with_error($id, 'Mobile No2 must contain exactly 10 digits when entered.');
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    oecrm_lead_redirect_with_error($id, 'Please enter a valid company email address.');
}
if ($emailtwo !== '' && !filter_var($emailtwo, FILTER_VALIDATE_EMAIL)) {
    oecrm_lead_redirect_with_error($id, 'Please enter a valid personal email address.');
}

$stmt = mysqli_prepare(
    $conn,
    'UPDATE leads SET lead_date=?, executive_name=?, company_name=?, contact_person=?, email=?, nick_name=?, lead_source=?, status=?, address=?, city=?, mobile_no1=?, mobile_no2=?, personal_email=? WHERE lead_id=? AND company_id=? AND is_active=1'
);
if (!$stmt) {
    oecrm_lead_redirect_with_error($id, 'Lead update could not be prepared.');
}
mysqli_stmt_bind_param($stmt, 'ssssssissssssii', $leaddate, $executive, $companyname, $contact, $email, $nick_name, $lead_source, $status, $address, $city, $mobileone, $mobiletwo, $emailtwo, $id, $companyId);
$updated = mysqli_stmt_execute($stmt);
$affected = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

if (!$updated || $affected < 0) {
    oecrm_lead_redirect_with_error($id, 'Lead could not be updated.');
}

oecrm_audit($conn, 'clients', 'update', 'lead', $id, 'Lead updated', null, ['company_name' => $companyname, 'status' => $status]);
$_SESSION['lead_flash'] = 'Lead updated successfully.';
header('Location:lead_edit.php?edit=' . $id);
exit;