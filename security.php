<?php
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

function oecrm_apply_security_headers()
{
    if (headers_sent()) {
        return;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function oecrm_destroy_session()
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function oecrm_session_guard()
{
    $now = time();
    $userAgentHash = hash('sha256', (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    if (empty($_SESSION['_oecrm_started_at'])) {
        $_SESSION['_oecrm_started_at'] = $now;
        $_SESSION['_oecrm_last_activity'] = $now;
        $_SESSION['_oecrm_rotated_at'] = $now;
        $_SESSION['_oecrm_user_agent'] = $userAgentHash;
        return true;
    }
    $idleExpired = $now - (int) ($_SESSION['_oecrm_last_activity'] ?? 0) > 1800;
    $absoluteExpired = $now - (int) $_SESSION['_oecrm_started_at'] > 43200;
    $agentChanged = !hash_equals((string) ($_SESSION['_oecrm_user_agent'] ?? ''), $userAgentHash);
    if ($idleExpired || $absoluteExpired || $agentChanged) {
        oecrm_destroy_session();
        return false;
    }
    if ($now - (int) ($_SESSION['_oecrm_rotated_at'] ?? 0) > 900) {
        session_regenerate_id(true);
        $_SESSION['_oecrm_rotated_at'] = $now;
    }
    $_SESSION['_oecrm_last_activity'] = $now;
    return true;
}

function oecrm_navigation_actor_key($portal)
{
    $actorId = $portal === 'admin' ? (int) ($_SESSION['adminId'] ?? 0) : (int) ($_SESSION['employeeId'] ?? 0);
    return $portal . ':' . $actorId;
}

function oecrm_clean_navigation_context($portal)
{
    static $processed = [];
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $processKey = $portal . '|' . $scriptName;
    if (isset($processed[$processKey])) {
        return;
    }
    $processed[$processKey] = true;

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return;
    }
    if (strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest') {
        return;
    }
    $endpoint = basename($scriptName);
    $ajaxEndpoints = ['locationAjax.php', 'getDeposit.php', 'getDateDeposit.php', 'getAccountDeposit.php'];
    if (in_array($endpoint, $ajaxEndpoints, true)) {
        return;
    }

    $contextKey = hash('sha256', $processKey);
    $actorKey = oecrm_navigation_actor_key($portal);
    if (!empty($_GET)) {
        $_SESSION['_oecrm_navigation'][$contextKey] = [
            'actor' => $actorKey,
            'expires' => time() + 120,
            'values' => $_GET,
        ];
        $cleanUrl = strtok((string) ($_SERVER['REQUEST_URI'] ?? $scriptName), '?');
        header('Location: ' . ($cleanUrl ?: $scriptName), true, 302);
        exit;
    }

    $context = $_SESSION['_oecrm_navigation'][$contextKey] ?? null;
    unset($_SESSION['_oecrm_navigation'][$contextKey]);
    if (!is_array($context)
        || ($context['actor'] ?? '') !== $actorKey
        || (int) ($context['expires'] ?? 0) < time()
        || !is_array($context['values'] ?? null)) {
        return;
    }
    $_GET = $context['values'];
}

function oecrm_block_unsafe_get_actions()
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return;
    }
    $endpoint = strtolower(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')));
    $destructiveKeys = ['delete', 'remove', 'purge', 'destroy', 'bank_delete', 'country_delete'];
    $hasDestructiveKey = false;
    foreach ($destructiveKeys as $key) {
        if (array_key_exists($key, $_GET)) {
            $hasDestructiveKey = true;
            break;
        }
    }
    if (strpos($endpoint, 'delete') === 0 || $hasDestructiveKey) {
        http_response_code(405);
        header('Allow: POST');
        exit('This action requires a secure POST request.');
    }
}

function oecrm_initialize_authenticated_session()
{
    $now = time();
    $_SESSION['_oecrm_started_at'] = $now;
    $_SESSION['_oecrm_last_activity'] = $now;
    $_SESSION['_oecrm_rotated_at'] = $now;
    $_SESSION['_oecrm_user_agent'] = hash('sha256', (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    unset($_SESSION['_oecrm_navigation']);
}

function oecrm_login_rate_limited($conn, $portal)
{
    $ip = mysqli_real_escape_string($conn, (string) ($_SERVER['REMOTE_ADDR'] ?? ''));
    $description = mysqli_real_escape_string($conn, ucfirst($portal) . ' portal login failed');
    $row = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) total FROM audit_logs
         WHERE action='failed_login' AND ip_address='$ip' AND description='$description'
         AND created_at>=DATE_SUB(NOW(),INTERVAL 15 MINUTE)"
    ));
    return (int) ($row['total'] ?? 0) >= 10;
}

oecrm_apply_security_headers();

function oecrm_require_admin_login()
{
    if (empty($_SESSION['adminId'])) {
        header('Location: index.php');
        exit;
    }
    if (!oecrm_session_guard()) {
        header('Location: index.php?expired=1');
        exit;
    }
    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
    oecrm_block_unsafe_get_actions();
    oecrm_clean_navigation_context('admin');
}

function oecrm_require_employee_login()
{
    if (empty($_SESSION['employeeId'])) {
        header('Location: index.php');
        exit;
    }
    if (!oecrm_session_guard()) {
        header('Location: index.php?expired=1');
        exit;
    }
    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
    oecrm_block_unsafe_get_actions();
    oecrm_clean_navigation_context('employee');
}

function oecrm_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function oecrm_csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(oecrm_csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function oecrm_verify_csrf($token = null)
{
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    }
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function oecrm_require_csrf()
{
    if (!oecrm_verify_csrf()) {
        http_response_code(403);
        exit('Invalid security token. Please go back, refresh the page, and try again.');
    }
}

function oecrm_int_param($source, $key)
{
    if (!isset($source[$key]) || !preg_match('/^\d+$/', (string)$source[$key])) {
        http_response_code(400);
        exit('Invalid request.');
    }
    return (int)$source[$key];
}

function oecrm_password_hash($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function oecrm_password_verify($password, $hash)
{
    if (!is_string($hash) || $hash === '') {
        return false;
    }
    if (password_get_info($hash)['algo'] !== 0 && password_verify($password, $hash)) {
        return true;
    }
    return hash_equals($hash, md5($password));
}

function oecrm_maybe_upgrade_password($conn, $table, $idColumn, $id, $passwordColumn, $plainPassword, $currentHash)
{
    if (password_get_info((string)$currentHash)['algo'] !== 0) {
        return;
    }
    $newHash = oecrm_password_hash($plainPassword);
    $sql = "UPDATE `$table` SET `$passwordColumn` = ? WHERE `$idColumn` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $newHash, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function oecrm_safe_upload($file, $targetDir, array $allowedExtensions = [])
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Upload failed.');
    }

    $original = (string)$file['name'];
    $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $blocked = ['php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar', 'cgi', 'pl', 'asp', 'aspx', 'jsp', 'sh', 'bat', 'cmd', 'exe', 'dll'];
    if ($extension === '' || in_array($extension, $blocked, true)) {
        throw new RuntimeException('This file type is not allowed.');
    }
    if ($allowedExtensions && !in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('This file type is not allowed.');
    }

    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        throw new RuntimeException('Upload directory is not writable.');
    }

    $safeName = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Could not save uploaded file.');
    }
    return $safeName;
}
?>
