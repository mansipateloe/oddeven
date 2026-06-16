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

$id = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? 'save';
$companyId = oecrm_current_company_id($conn);
$actor = (int) $_SESSION['adminId'];

try {
    if ($action === 'cancel') {
        oecrm_require_permission($conn, 'subscriptions', 'delete');
        $old = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM digital_subscriptions WHERE id=' . $id . ' AND company_id=' . $companyId));
        if (!$old) {
            throw new RuntimeException('Subscription not found.');
        }
        $stmt = mysqli_prepare($conn, 'UPDATE digital_subscriptions SET status="cancelled" WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        oecrm_audit($conn, 'subscriptions', 'delete', 'subscription', $id, 'Subscription cancelled', $old, ['status' => 'cancelled']);
        $_SESSION['subscription_flash'] = 'Subscription cancelled.';
        header('Location: subscriptions.php');
        exit;
    }

    oecrm_require_permission($conn, 'subscriptions', $id ? 'edit' : 'create');
    $type = $_POST['asset_type'] ?? 'other';
    $name = trim($_POST['name'] ?? '');
    $provider = trim($_POST['provider'] ?? '');
    $url = trim($_POST['login_url'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['recovery_email'] ?? '');
    $renewal = trim($_POST['renewal_date'] ?? '') ?: null;
    $cost = (float) ($_POST['cost'] ?? 0);
    $currency = strtoupper(substr($_POST['currency_code'] ?? 'INR', 0, 3));
    $notes = trim($_POST['notes'] ?? '');
    $secret = trim($_POST['secret'] ?? '');
    $encrypted = $secret !== '' ? oecrm_encrypt_secret($secret) : null;
    if ($id) {
        $old = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM digital_subscriptions WHERE id=' . $id . ' AND company_id=' . $companyId));
        if (!$old) {
            throw new RuntimeException('Subscription not found.');
        }
        $sql = 'UPDATE digital_subscriptions SET asset_type=?,name=?,provider=?,login_url=?,username=?,recovery_email=?,renewal_date=?,cost=?,currency_code=?,notes=?' . ($encrypted !== null ? ',secret_encrypted=?' : '') . ' WHERE id=? AND company_id=?';
        $stmt = mysqli_prepare($conn, $sql);
        if ($encrypted !== null) {
            mysqli_stmt_bind_param($stmt, 'sssssssdsssii', $type, $name, $provider, $url, $username, $email, $renewal, $cost, $currency, $notes, $encrypted, $id, $companyId);
        } else {
            mysqli_stmt_bind_param($stmt, 'sssssssdssii', $type, $name, $provider, $url, $username, $email, $renewal, $cost, $currency, $notes, $id, $companyId);
        }
    } else {
        $stmt = mysqli_prepare($conn, 'INSERT INTO digital_subscriptions(company_id,asset_type,name,provider,login_url,username,secret_encrypted,recovery_email,renewal_date,cost,currency_code,notes,created_by) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'issssssssdssi', $companyId, $type, $name, $provider, $url, $username, $encrypted, $email, $renewal, $cost, $currency, $notes, $actor);
    }
    mysqli_stmt_execute($stmt);
    if (!$id) {
        $id = mysqli_insert_id($conn);
    }
    mysqli_stmt_close($stmt);
    oecrm_audit($conn, 'subscriptions', $id ? 'save' : 'create', 'subscription', $id, 'Subscription saved');
    $_SESSION['subscription_flash'] = 'Subscription saved.';
    header('Location: subscriptions.php');
    exit;
} catch (Throwable $exception) {
    $_SESSION['subscription_flash'] = $exception->getMessage();
    header('Location: subscriptions.php');
    exit;
}
