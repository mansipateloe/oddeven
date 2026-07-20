<?php
if (ob_get_level() === 0) {
    ob_start();
}
require_once __DIR__ . '/../security.php';
include 'dbconnect.php';
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
        $stmt = mysqli_prepare($conn, "SELECT * FROM employeesTbl WHERE employeeUname = ? AND status = 0 LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $check = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }

    if ($check && oecrm_password_verify($plainPassword, $check['employeeUpass'])) {
        session_regenerate_id(true);
        oecrm_initialize_authenticated_session();
        $_SESSION['employeeId'] = (int)$check['id'];
        $_SESSION['alert_displayed'] = "false";
        oecrm_maybe_upgrade_password($conn, 'employeesTbl', 'id', (int)$check['id'], 'employeeUpass', $plainPassword, $check['employeeUpass']);

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
        oecrm_auth_audit($conn, 'employee', (int)$check['id'], (int)$check['company_id'], 'login', 'Employee login successful', (int)$check['id'], ['username'=>$username]);

<<<<<<< HEAD
            if (oecrm_login_rate_limited($conn, 'employee')) {
                http_response_code(429);
                $loginError = "<div class='form-group'><div class='alert alert-danger'><p class='alert-link'>Too many login attempts. Please try again after 15 minutes.</p></div></div>";
                $check = null;
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
                $_SESSION['employeeId'] = (int)$check['id'];
                $_SESSION['alert_displayed'] = "false";
                oecrm_maybe_upgrade_password($conn, 'employeestbl', 'id', (int)$check['id'], 'employeeUpass', $plainPassword, $check['employeeUpass']);

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
                oecrm_auth_audit($conn, 'employee', (int)$check['id'], (int)$check['company_id'], 'login', 'Employee login successful', (int)$check['id'], ['username'=>$username]);

                header("Location:home.php");
                exit;
            } else {
                oecrm_auth_audit($conn, 'system', 0, 0, 'failed_login', 'Employee portal login failed', null, ['username'=>$username]);
                $loginError = "<div class='form-group'><div class='alert alert-danger alert-dismissable'>
                                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>x</button>
                                    <p class='alert-link'>Invalid login credentials.</p>
                                </div></div>";
            }
        }
    
=======
        header("Location:home.php");
        exit;
    } elseif (isset($_POST['loginButton'])) {
        oecrm_auth_audit($conn, 'system', 0, 0, 'failed_login', 'Employee portal login failed', null, ['username'=>$username]);
        $loginError = "<div class='form-group'><div class='alert alert-danger alert-dismissable'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>x</button>
                            <p class='alert-link'>Invalid login credentials.</p>
                        </div></div>";
    }
}
>>>>>>> origin/local-work
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Oddeven Infotech Pvt. Ltd. || Login</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/custom/custom.css?v=20260627-3" rel="stylesheet">
</head>
<body style="background: linear-gradient(#edfaff,#bbe8fc);">
    
    <div class="wrapper">
<section class="login-form">
    <div class="container">
        <div class="row">
                <div class="login-box panel panel-default">
                    <div class="panel-heading">
                        <img class="img-responsive logo_img" src="../images/logo.png" alt="logo">
                        <p class="text_heading">Enter your Username and Password to access admin panel.</p>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="POST">
                            <?php echo oecrm_csrf_field(); ?>
                            <fieldset>
                                <?php echo $loginError; ?>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input class="form-control" placeholder="Enter your username" name="username" type="text" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <div class="oecrm-password-field">
                                        <span class="oecrm-password-prefix" aria-hidden="true"><i class="fa fa-lock"></i></span>
                                        <input class="form-control" placeholder="Enter your password" name="password" type="password" maxlength="13" autocomplete="current-password" required>
                                        <button type="button" class="btn btn-default oecrm-password-toggle" aria-label="Show password"><i class="fa fa-eye"></i></button>
                                    </div>
                                </div>
                                <div class="form-group text-left">
                                    <input id="checkbox11" type="checkbox">
                                    <label for="checkbox11">Remember Me</label>
                                </div>
                                <input type="submit" class="btn btn-lg btn-primary btn-block" name="loginButton" value="Login">
                            </fieldset>
                        </form>
                    </div>
                </div>
        </div>
        </div>
    </section>
</div>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>
    <script>
      (function(){
        var input = document.querySelector('input[name="password"]');
        var toggle = document.querySelector('.oecrm-password-toggle');
        if (input) input.id = 'employee-login-password';
        if (toggle && input) {
          toggle.addEventListener('click', function () {
            var visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            toggle.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
            toggle.querySelector('i').className = visible ? 'fa fa-eye' : 'fa fa-eye-slash';
          });
        }
      })();
    </script>
</body>
</html>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>


