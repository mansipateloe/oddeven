<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

oecrm_require_csrf();

$action = $_POST['action'] ?? '';
$companyId = oecrm_current_company_id($conn);
$actor = (int) ($_SESSION['adminId'] ?? 0);
$expectsJson = (
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
    || ($_POST['response'] ?? '') === 'json'
);

function oecrm_client_action_json(int $statusCode, string $message, array $extra = []): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(['success' => $statusCode < 400, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

function oecrm_next_client_code(mysqli $conn, int $companyId): string
{
    $prefix = 'CL-' . str_pad($companyId, 2, '0', STR_PAD_LEFT) . '-' . date('Ym') . '-';
    $stmt = mysqli_prepare($conn, 'SELECT client_code FROM clients WHERE company_id=? AND client_code LIKE ? ORDER BY id DESC LIMIT 1');
    $like = $prefix . '%';
    mysqli_stmt_bind_param($stmt, 'is', $companyId, $like);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];
    mysqli_stmt_close($stmt);

    $next = 1;
    if (!empty($row['client_code']) && preg_match('/-(\d+)$/', $row['client_code'], $match)) {
        $next = (int) $match[1] + 1;
    }

    return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
}

function oecrm_validate_gst(string $value): bool
{
    return $value === '' || (bool) preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', strtoupper($value));
}

function oecrm_validate_phone(string $value): bool
{
    return $value === '' || (bool) preg_match('/^[0-9]{10}$/', $value);
}

function oecrm_client_contact_exists(mysqli $conn, int $companyId, string $field, string $value, int $excludeId = 0): bool
{
    $sql = $field === 'email'
        ? "SELECT id FROM clients WHERE company_id=? AND LOWER({$field})=LOWER(?) "
        : "SELECT id FROM clients WHERE company_id=? AND {$field}=? ";
    if ($excludeId > 0) {
        $sql .= 'AND id<>? ';
    }
    $sql .= 'LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }
    if ($excludeId > 0) {
        mysqli_stmt_bind_param($stmt, 'isi', $companyId, $value, $excludeId);
    } else {
        mysqli_stmt_bind_param($stmt, 'is', $companyId, $value);
    }
    mysqli_stmt_execute($stmt);
    $exists = (bool) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $exists;
}

try {
    if ($action === 'archive_client') {
        $clientId = (int) ($_POST['client_id'] ?? 0);
        oecrm_require_permission($conn, 'clients', 'delete');

        $stmt = mysqli_prepare($conn, 'SELECT * FROM clients WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'ii', $clientId, $companyId);
        mysqli_stmt_execute($stmt);
        $client = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$client) {
            throw new RuntimeException('Client not found.');
        }

        $stmt = mysqli_prepare($conn, 'SELECT COUNT(*) total FROM projectstbl WHERE client_id=? AND company_id=? AND status NOT IN ("completed","cancel")');
        mysqli_stmt_bind_param($stmt, 'ii', $clientId, $companyId);
        mysqli_stmt_execute($stmt);
        $active = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        $status = !empty($active['total']) ? 'on_hold' : 'inactive';
        $stmt = mysqli_prepare($conn, 'UPDATE clients SET status=? WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'sii', $status, $clientId, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        oecrm_audit($conn, 'clients', 'archive', 'client', $clientId, 'Client archived with dependency check', $client, ['status' => $status, 'active_projects' => $active['total'] ?? 0]);
        $_SESSION['client_flash'] = !empty($active['total']) ? 'Client has active projects and was placed on hold.' : 'Client archived successfully.';

        header('Location: clients.php');
        exit;
    }

    if ($action === 'save_client') {
        $id = (int) ($_POST['id'] ?? 0);
        oecrm_require_permission($conn, 'clients', $id ? 'edit' : 'create');

        $display = trim($_POST['display_name'] ?? '');
        $legal = trim($_POST['legal_name'] ?? '');
        $type = trim($_POST['client_type'] ?? 'company');
        $status = trim($_POST['status'] ?? 'active');

        if ($display === '' || $legal === '') {
            throw new RuntimeException('Client names are required.');
        }

        $fields = ['industry', 'website', 'email', 'phone', 'billing_address', 'city', 'state', 'country', 'tax_id', 'notes'];
        foreach ($fields as $field) {
            $$field = trim($_POST[$field] ?? '');
        }
        $email = strtolower($email);
        $phone = substr(preg_replace('/\D+/', '', $phone), 0, 10);

        $currency = strtoupper(substr(trim($_POST['currency_code'] ?? 'INR'), 0, 3));
        $terms = max(0, (int) ($_POST['payment_terms_days'] ?? 0));

        if ($email === '' && $phone === '') {
            throw new RuntimeException('Please enter at least one contact method: email or phone.');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Please enter a valid email address.');
        }

        if ($phone !== '' && !oecrm_validate_phone($phone)) {
            throw new RuntimeException('Please enter a valid phone number with exactly 10 digits.');
        }

        $excludeId = $id > 0 ? $id : 0;
        if ($email !== '' && oecrm_client_contact_exists($conn, $companyId, 'email', $email, $excludeId)) {
            throw new RuntimeException('This email is already used for another client in this company.');
        }

        if ($phone !== '' && oecrm_client_contact_exists($conn, $companyId, 'phone', $phone, $excludeId)) {
            throw new RuntimeException('This phone number is already used for another client in this company.');
        }

        if (!oecrm_validate_gst(strtoupper($tax_id))) {
            throw new RuntimeException('Please enter a valid 15-character GST / Tax ID.');
        }

        if ($id) {
            $stmt = mysqli_prepare($conn, 'SELECT * FROM clients WHERE id=? AND company_id=?');
            mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
            mysqli_stmt_execute($stmt);
            $old = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if (!$old) {
                throw new RuntimeException('Client not found.');
            }

            $code = $old['client_code'];
            $stmt = mysqli_prepare(
                $conn,
                'UPDATE clients
                 SET client_code=?, legal_name=?, display_name=?, client_type=?, status=?, industry=?, website=?, email=?, phone=?, billing_address=?, city=?, state=?, country=?, tax_id=?, currency_code=?, payment_terms_days=?, notes=?
                 WHERE id=? AND company_id=?'
            );
            mysqli_stmt_bind_param(
                $stmt,
                'sssssssssssssssisii',
                $code,
                $legal,
                $display,
                $type,
                $status,
                $industry,
                $website,
                $email,
                $phone !== '' ? $phone : '',
                $billing_address,
                $city,
                $state,
                $country,
                $tax_id,
                $currency,
                $terms,
                $notes,
                $id,
                $companyId
            );
        } else {
            $old = null;
            $code = oecrm_next_client_code($conn, $companyId);
            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO clients(company_id, client_code, legal_name, display_name, client_type, status, industry, website, email, phone, billing_address, city, state, country, tax_id, currency_code, payment_terms_days, notes, created_by)
                 VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            );
            mysqli_stmt_bind_param(
                $stmt,
                'issssssssssssssisis',
                $companyId,
                $code,
                $legal,
                $display,
                $type,
                $status,
                $industry,
                $website,
                $email,
                $phone !== '' ? $phone : '',
                $billing_address,
                $city,
                $state,
                $country,
                $tax_id,
                $currency,
                $terms,
                $notes,
                $actor
            );
        }

        mysqli_stmt_execute($stmt);
        if (!$id) {
            $id = mysqli_insert_id($conn);
        }
        mysqli_stmt_close($stmt);

        oecrm_audit($conn, 'clients', $old ? 'update' : 'create', 'client', $id, 'Client profile saved', $old, ['client_code' => $code, 'display_name' => $display, 'status' => $status]);

        if ($expectsJson) {
            oecrm_client_action_json(200, 'Client saved successfully.', ['id' => $id, 'client_code' => $code]);
        }

        $_SESSION['client_flash'] = 'Client saved successfully.';
        header('Location: clientProfile.php?id=' . $id);
        exit;
    }

    $clientId = (int) ($_POST['client_id'] ?? 0);
    $stmt = mysqli_prepare($conn, 'SELECT id FROM clients WHERE id=? AND company_id=?');
    mysqli_stmt_bind_param($stmt, 'ii', $clientId, $companyId);
    mysqli_stmt_execute($stmt);
    $check = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$check) {
        throw new RuntimeException('Client not found.');
    }

    if ($action === 'contact') {
        oecrm_require_permission($conn, 'client_contacts', 'create');
        $name = trim($_POST['name'] ?? '');
        $designation = trim($_POST['designation'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = substr(preg_replace('/\D+/', '', trim($_POST['phone'] ?? '')), 0, 10);
        $primary = isset($_POST['is_primary']) ? 1 : 0;

        if ($name === '') {
            throw new RuntimeException('Contact name is required.');
        }
        $email = strtolower($email);
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Please enter a valid contact email address.');
        }
        if (!oecrm_validate_phone($phone)) {
            throw new RuntimeException('Please enter a valid contact phone number with exactly 10 digits.');
        }

        if ($primary) {
            mysqli_query($conn, 'UPDATE client_contacts SET is_primary=0 WHERE client_id=' . (int) $clientId);
        }

        $stmt = mysqli_prepare($conn, 'INSERT INTO client_contacts(client_id,name,designation,email,phone,is_primary) VALUES(?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'issssi', $clientId, $name, $designation, $email, $phone, $primary);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        oecrm_audit($conn, 'client_contacts', 'create', 'client_contact', $id, 'Client contact added', null, ['client_id' => $clientId, 'name' => $name]);
    } elseif ($action === 'contract') {
        oecrm_require_permission($conn, 'client_contracts', 'create');
        $type = trim($_POST['contract_type'] ?? 'service');
        $title = trim($_POST['title'] ?? '');
        $start = $_POST['start_date'] ?: null;
        $end = $_POST['end_date'] ?: null;
        $value = (float) ($_POST['value_amount'] ?? 0);

        if ($title === '') {
            throw new RuntimeException('Contract title is required.');
        }

        $stmt = mysqli_prepare($conn, 'INSERT INTO client_contracts(client_id,contract_type,title,start_date,end_date,value_amount,currency_code,status,created_by) VALUES(?,?,?,?,?,?,"INR","active",?)');
        mysqli_stmt_bind_param($stmt, 'issssdi', $clientId, $type, $title, $start, $end, $value, $actor);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        oecrm_audit($conn, 'client_contracts', 'create', 'client_contract', $id, 'Client contract added', null, ['client_id' => $clientId, 'title' => $title]);
    } elseif ($action === 'communication') {
        oecrm_require_permission($conn, 'client_communications', 'create');
        $type = trim($_POST['communication_type'] ?? 'note');
        $subject = trim($_POST['subject'] ?? '');
        $details = trim($_POST['details'] ?? '');
        $at = str_replace('T', ' ', $_POST['communication_at'] ?? '');

        if ($subject === '' || !strtotime($at)) {
            throw new RuntimeException('Communication subject and date are required.');
        }

        $stmt = mysqli_prepare($conn, 'INSERT INTO client_communications(client_id,communication_type,subject,details,communication_at,created_by) VALUES(?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'issssi', $clientId, $type, $subject, $details, $at, $actor);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        oecrm_audit($conn, 'client_communications', 'create', 'client_communication', $id, 'Client communication recorded', null, ['client_id' => $clientId, 'type' => $type, 'subject' => $subject]);
    } elseif ($action === 'document') {
        oecrm_require_permission($conn, 'client_documents', 'upload');
        $type = trim($_POST['document_type'] ?? 'other');
        $title = trim($_POST['title'] ?? '');

        if ($title === '') {
            throw new RuntimeException('Document title is required.');
        }

        $stored = oecrm_safe_upload($_FILES['document'] ?? null, __DIR__ . '/../storage/client_documents', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);
        $original = $_FILES['document']['name'] ?? '';
        $mime = $_FILES['document']['type'] ?? '';
        $size = (int) ($_FILES['document']['size'] ?? 0);

        $stmt = mysqli_prepare($conn, 'INSERT INTO client_documents(client_id,document_type,title,stored_name,original_name,mime_type,file_size,uploaded_by) VALUES(?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'isssssii', $clientId, $type, $title, $stored, $original, $mime, $size, $actor);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        oecrm_audit($conn, 'client_documents', 'upload', 'client_document', $id, 'Client document uploaded', null, ['client_id' => $clientId, 'type' => $type]);
    } else {
        throw new RuntimeException('Invalid action.');
    }

    $_SESSION['client_flash'] = 'Client information updated.';
    header('Location: clientProfile.php?id=' . $clientId);
    exit;
} catch (Throwable $e) {
    if ($expectsJson) {
        oecrm_client_action_json(422, $e->getMessage());
    }

    $_SESSION['client_error'] = $e->getMessage();
    header('Location: ' . (!empty($clientId) ? 'clientProfile.php?id=' . $clientId : 'clients.php'));
    exit;
}
