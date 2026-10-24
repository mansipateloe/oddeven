<?php
if (ob_get_level() === 0) {
    ob_start();
}
require_once __DIR__ . '/../security.php';
if (!empty($_SESSION['employeeId'])) {
    oecrm_unauthorized_user();
}
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../foundation.php';

if (isset($_SESSION['adminId'])) {
    oecrm_require_admin_login();
    header("Location:dashboard.php");
    exit;
}

$loginError = "";

if (isset($_POST['submit'])) {
    oecrm_require_csrf();

    $uname = trim($_POST['uname'] ?? '');
    $plainPassword = $_POST['password'] ?? '';

    if (oecrm_login_rate_limited($conn, 'admin')) {
        http_response_code(429);
        $loginError = "<div class='form-group'><div class='alert alert-danger'><p class='alert-link'>Too many login attempts. Please try again after 15 minutes.</p></div></div>";
    } elseif ($uname === '' || $plainPassword === '') {
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
        $stmt = mysqli_prepare($conn, "SELECT * FROM admins WHERE uname = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $uname);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($admin && oecrm_password_verify($plainPassword, $admin['password'])) {
            session_regenerate_id(true);
            oecrm_initialize_authenticated_session();
            $_SESSION['adminId'] = (int) $admin['id'];
            $_SESSION['adminName'] = $admin['uname'];
            $_SESSION['is_admin'] = '1';
            oecrm_maybe_upgrade_password($conn, 'admins', 'id', (int) $admin['id'], 'password', $plainPassword, $admin['password']);
            oecrm_auth_audit($conn, 'admin', (int) $admin['id'], (int) ($admin['company_id'] ?? 0), 'login', 'Admin login successful', null, ['username' => $uname]);
            header("Location:dashboard.php");
            exit;
        }

        $stmt = mysqli_prepare($conn, "SELECT * FROM employeestbl WHERE employeeUname = ? AND status = 0 AND is_admin_access = 1 AND access_role > 0 LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $uname);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && oecrm_password_verify($plainPassword, $user['employeeUpass'])) {
            session_regenerate_id(true);
            oecrm_initialize_authenticated_session();
            $_SESSION['adminId'] = (int) $user['id'];
            $_SESSION['adminName'] = $user['name'];
            $_SESSION['admin_access_role'] = $user['access_role'];
            $_SESSION['is_admin'] = '0';
            oecrm_maybe_upgrade_password($conn, 'employeestbl', 'id', (int) $user['id'], 'employeeUpass', $plainPassword, $user['employeeUpass']);
            oecrm_auth_audit($conn, 'admin', (int) $user['id'], (int) $user['company_id'], 'login', 'Employee admin-access login successful', (int) $user['id'], ['username' => $uname]);
            header("Location:dashboard.php");
            exit;
        }

        oecrm_auth_audit($conn, 'system', 0, 0, 'failed_login', 'Admin portal login failed', null, ['username' => $uname]);
        $loginError = "<div class='form-group'><div class='alert alert-danger alert-dismissable'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>x</button>
                            <p class='alert-link'>Invalid login credentials.</p>
                        </div></div>";
    }
}
$loginPortal = 'admin';
require __DIR__ . '/../loginView.php';
