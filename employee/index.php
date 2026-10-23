<?php
if (ob_get_level() === 0) {
    ob_start();
}
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../foundation.php';

if (isset($_SESSION['employeeId'])) {
    header("Location:home.php");
    exit;
}

$loginError = "";
$check = null;

if (isset($_POST['loginButton'])) {
    oecrm_require_csrf();

    $username = trim($_POST['username'] ?? '');
    $plainPassword = $_POST['password'] ?? '';

    if (oecrm_login_rate_limited($conn, 'employee')) {
        http_response_code(429);
        $loginError = "<div class='form-group'><div class='alert alert-danger'><p class='alert-link'>Too many login attempts. Please try again after 15 minutes.</p></div></div>";
    } elseif ($username === '' || $plainPassword === '') {
        $loginError = "<div class='form-group'><div class='alert alert-danger alert-dismissable'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>x</button>
                            <p class='alert-link'>Invalid login credentials.</p>
                        </div></div>";
    } elseif (strlen($plainPassword) > 13) {
        $loginError = "<div class='form-group'><div class='alert alert-danger alert-dismissable'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>x</button>
                            <p class='alert-link'>Password cannot exceed 13 characters.</p>
                        </div></div>";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM employeestbl WHERE employeeUname = ? AND status = 0 LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $check = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }

    if ($check && oecrm_password_verify($plainPassword, $check['employeeUpass'])) {
        session_regenerate_id(true);
        oecrm_initialize_authenticated_session();
        $_SESSION['employeeId'] = (int) $check['id'];
        $_SESSION['alert_displayed'] = "false";
        oecrm_maybe_upgrade_password($conn, 'employeestbl', 'id', (int) $check['id'], 'employeeUpass', $plainPassword, $check['employeeUpass']);

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipAddress = trim($forwarded[0]);
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $browser_type = $_SERVER['HTTP_SEC_CH_UA'] ?? '';
        $device_type = $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '';
        $mobile_browser = str_replace("?", "", $_SERVER['HTTP_SEC_CH_UA_MOBILE'] ?? '');
        $date = date('Y-m-d H:i:s');

        $stmt = mysqli_prepare($conn, "INSERT INTO login_details(user_id, ip_address, login_datetime, browser_details, browser_type, device_type, mobile_browser) VALUES(?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'issssss', $check['id'], $ipAddress, $date, $userAgent, $browser_type, $device_type, $mobile_browser);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        oecrm_auth_audit($conn, 'employee', (int) $check['id'], (int) $check['company_id'], 'login', 'Employee login successful', (int) $check['id'], ['username' => $username]);

        header("Location:home.php");
        exit;
    } elseif (isset($_POST['loginButton'])) {
        oecrm_auth_audit($conn, 'system', 0, 0, 'failed_login', 'Employee portal login failed', null, ['username' => $username]);
        $loginError = "<div class='form-group'><div class='alert alert-danger alert-dismissable'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>x</button>
                            <p class='alert-link'>Invalid login credentials.</p>
                        </div></div>";
    }
}
$loginPortal = 'employee';
require __DIR__ . '/../loginView.php';
