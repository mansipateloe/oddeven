<?php
include 'dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();
oecrm_require_csrf();
oecrm_require_permission($conn,'clients','edit');

if (isset($_POST['leadDate'])) {
    $id = oecrm_int_param($_POST, 'lead_id');
    $leaddate = $_POST['leadDate'] ?? '';
    $executive = $_POST['executiveName'] ?? '';
    $companyname = $_POST['company'] ?? '';
    $contact = $_POST['cperson'] ?? '';
    $email = $_POST['emailid'] ?? '';
    $mobileone = substr(preg_replace('/\D+/', '', $_POST['mobileno1'] ?? ''), 0, 10);
    $mobiletwo = substr(preg_replace('/\D+/', '', $_POST['mobileno2'] ?? ''), 0, 10);
    $emailtwo = $_POST['emailid2'] ?? '';
    $nick_name = $_POST['nick_name'] ?? '';
    $status = $_POST['status'] ?? '';
    $lead_source = $_POST['lead_source'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';

    $companyId=oecrm_current_company_id($conn);
    $stmt = mysqli_prepare($conn, "UPDATE leads SET lead_date=?, executive_name=?, company_name=?, contact_person=?, email=?, nick_name=?, lead_source=?, status=?, address=?, city=?, mobile_no1=?, mobile_no2=?, personal_email=? WHERE lead_id=? AND company_id=? AND is_active=1");
    if (strlen($mobileone) !== 10 || strlen($mobiletwo) !== 10) {
        http_response_code(422);
        exit('Phone numbers must contain exactly 10 digits.');
    }
    mysqli_stmt_bind_param($stmt, 'sssssssssssssii', $leaddate, $executive, $companyname, $contact, $email, $nick_name, $lead_source, $status, $address, $city, $mobileone, $mobiletwo, $emailtwo, $id,$companyId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    oecrm_audit($conn,'clients','update','lead',$id,'Lead updated',null,['company_name'=>$companyname,'status'=>$status]);
    header('Location:lead_edit.php?edit=' . $id);
    exit;
}

http_response_code(400);
echo 'Invalid request.';
?>
