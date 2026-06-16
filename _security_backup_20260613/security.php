<?php
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function oecrm_require_admin_login()
{
    if (empty($_SESSION['adminId'])) {
        header('Location: index.php');
        exit;
    }
}

function oecrm_require_employee_login()
{
    if (empty($_SESSION['employeeId'])) {
        header('Location: index.php');
        exit;
    }
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
