<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
oecrm_require_csrf();
$companyId = oecrm_current_company_id($conn);
$actor = (int)$_SESSION['adminId'];
$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);

if ($action === 'invoice') {
    oecrm_require_permission($conn, 'finance', $id ? 'edit' : 'create');
    $number = trim($_POST['invoice_number']);
    $client = (int)$_POST['client_id'];
    $company = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT company_type,currency_code FROM companies WHERE id='.$companyId));
    $isGstCompany = ($company['company_type'] ?? '') === 'india_gst';
    $type = $isGstCompany ? 'gst' : 'international';
    $date = $_POST['invoice_date'];
    $due = $_POST['due_date'];
    $currency = $company['currency_code'] ?? ($isGstCompany ? 'INR' : 'USD');
    $descriptions = $_POST['item_description'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $rates = $_POST['rate'] ?? [];
    $taxPercents = $_POST['tax_percent'] ?? [];
    $lineItems = [];
    $subtotal = 0;
    $tax = 0;
    foreach ($descriptions as $index => $description) {
        $description = trim($description);
        $quantity = max(0, (float)($quantities[$index] ?? 0));
        $rate = max(0, (float)($rates[$index] ?? 0));
        $taxPercent = $isGstCompany ? 18.0 : 0.0;
        if ($description === '' || $quantity <= 0) continue;
        $amount = $quantity * $rate;
        $subtotal += $amount;
        $tax += $amount * $taxPercent / 100;
        $lineItems[] = [$description,$quantity,$rate,$taxPercent,$amount];
    }
    if (!$lineItems) throw new RuntimeException('At least one invoice item is required.');
    $total = $subtotal + $tax;
    $status = $_POST['status'];
    $notes = trim($_POST['notes']);
    $frequency = $_POST['recurring_frequency'] ?? 'none';
    $next = $frequency === 'none' ? null : date('Y-m-d', strtotime($date.' +'.($frequency === 'monthly' ? '1 month' : ($frequency === 'quarterly' ? '3 months' : '1 year'))));
    if ($id) {
        $s = mysqli_prepare($conn, 'UPDATE finance_invoices SET client_id=?,invoice_number=?,invoice_type=?,invoice_date=?,due_date=?,currency_code=?,subtotal=?,tax_amount=?,total_amount=?,status=?,recurring_frequency=?,next_invoice_date=?,notes=? WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($s, 'isssssdddssssii', $client,$number,$type,$date,$due,$currency,$subtotal,$tax,$total,$status,$frequency,$next,$notes,$id,$companyId);
    } else {
        $s = mysqli_prepare($conn, 'INSERT INTO finance_invoices(company_id,client_id,invoice_number,invoice_type,invoice_date,due_date,currency_code,subtotal,tax_amount,total_amount,status,recurring_frequency,next_invoice_date,notes,created_by) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($s, 'iisssssdddssssi', $companyId,$client,$number,$type,$date,$due,$currency,$subtotal,$tax,$total,$status,$frequency,$next,$notes,$actor);
    }
    mysqli_stmt_execute($s);
    if (!$id) $id = mysqli_insert_id($conn);
    mysqli_query($conn, 'DELETE FROM finance_invoice_items WHERE invoice_id='.$id);
    $itemStmt = mysqli_prepare($conn, 'INSERT INTO finance_invoice_items(invoice_id,description,quantity,rate,tax_percent,amount) VALUES(?,?,?,?,?,?)');
    foreach ($lineItems as $line) {
        mysqli_stmt_bind_param($itemStmt, 'isdddd', $id,$line[0],$line[1],$line[2],$line[3],$line[4]);
        mysqli_stmt_execute($itemStmt);
    }
    mysqli_stmt_close($itemStmt);
} elseif ($action === 'payment') {
    oecrm_require_permission($conn, 'finance', 'edit');
    $date = $_POST['payment_date'];
    $amount = (float)$_POST['amount'];
    $ref = trim($_POST['reference']);
    $gateway = $_POST['gateway'];
    $s = mysqli_prepare($conn, 'INSERT INTO finance_payments(company_id,invoice_id,payment_date,amount,transaction_reference,gateway,created_by) VALUES(?,?,?,?,?,?,?)');
    mysqli_stmt_bind_param($s, 'iisdssi', $companyId,$id,$date,$amount,$ref,$gateway,$actor);
    mysqli_stmt_execute($s);
    mysqli_query($conn, "UPDATE finance_invoices SET paid_amount=LEAST(total_amount,paid_amount+$amount),status=CASE WHEN paid_amount+$amount>=total_amount THEN 'paid' ELSE 'partially_paid' END WHERE id=$id AND company_id=$companyId");
} elseif ($action === 'expense') {
    oecrm_require_permission($conn, 'finance', 'create');
    $date = $_POST['expense_date'];
    $category = $_POST['category'];
    $vendor = trim($_POST['vendor']);
    $description = trim($_POST['description']);
    $amount = (float)$_POST['amount'];
    $tax = (float)$_POST['tax_amount'];
    $currency = strtoupper(substr($_POST['currency_code'], 0, 3));
    $s = mysqli_prepare($conn, 'INSERT INTO finance_expenses(company_id,category,expense_date,vendor,description,amount,tax_amount,currency_code,created_by) VALUES(?,?,?,?,?,?,?,?,?)');
    mysqli_stmt_bind_param($s, 'issssddsi', $companyId,$category,$date,$vendor,$description,$amount,$tax,$currency,$actor);
    mysqli_stmt_execute($s);
} elseif ($action === 'reminder') {
    oecrm_require_permission($conn, 'finance', 'edit');
    $r = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT f.invoice_number,f.due_date,c.email FROM finance_invoices f JOIN clients c ON c.id=f.client_id WHERE f.id='.$id.' AND f.company_id='.$companyId));
    if ($r && $r['email']) {
        $subject = 'Payment reminder for invoice '.$r['invoice_number'];
        $message = 'This is a payment reminder. The invoice due date is '.$r['due_date'].'.';
        $s = mysqli_prepare($conn, 'INSERT INTO invoice_reminders(company_id,invoice_id,recipient_email,subject,message,created_by) VALUES(?,?,?,?,?,?)');
        mysqli_stmt_bind_param($s, 'iisssi', $companyId,$id,$r['email'],$subject,$message,$actor);
        mysqli_stmt_execute($s);
    }
} elseif ($action === 'cancel_invoice') {
    oecrm_require_permission($conn, 'finance', 'delete');
    $old = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM finance_invoices WHERE id=' . $id . ' AND company_id=' . $companyId));
    if (!$old) {
        http_response_code(404);
        exit('Invoice not found.');
    }
    mysqli_query($conn, 'UPDATE finance_invoices SET status="cancelled" WHERE id=' . $id . ' AND company_id=' . $companyId);
} elseif ($action === 'delete_expense') {
    oecrm_require_permission($conn, 'finance', 'delete');
    $old = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM finance_expenses WHERE id=' . $id . ' AND company_id=' . $companyId));
    if (!$old) {
        http_response_code(404);
        exit('Expense not found.');
    }
    mysqli_query($conn, 'DELETE FROM finance_expenses WHERE id=' . $id . ' AND company_id=' . $companyId);
} else {
    http_response_code(400);
    exit('Invalid finance action.');
}

oecrm_audit($conn, 'finance', $action, 'finance_record', $id, 'Finance record updated');
$_SESSION['finance_flash'] = 'Finance record saved.';
header('Location: finance.php');
