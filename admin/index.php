<?php
if (ob_get_level() === 0) {
    ob_start();
}
require_once __DIR__ . '/../security.php';
include 'dbconnect.php';
require_once __DIR__ . '/../foundation.php';
if (isset($_SESSION['adminId'])) {
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
            $_SESSION['adminId'] = (int)$admin['id'];
            $_SESSION['adminName'] = $admin['uname'];
            $_SESSION['is_admin'] = '1';
            oecrm_maybe_upgrade_password($conn, 'admins', 'id', (int)$admin['id'], 'password', $plainPassword, $admin['password']);
            oecrm_auth_audit($conn, 'admin', (int)$admin['id'], (int)($admin['company_id'] ?? 0), 'login', 'Admin login successful', null, ['username'=>$uname]);
            header("Location:dashboard.php");
            exit;
        }

        $stmt = mysqli_prepare($conn, "SELECT * FROM employeesTbl WHERE employeeUname = ? AND status = 0 AND is_admin_access = 1 AND access_role > 0 LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $uname);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && oecrm_password_verify($plainPassword, $user['employeeUpass'])) {
            session_regenerate_id(true);
            oecrm_initialize_authenticated_session();
            $_SESSION['adminId'] = (int)$user['id'];
            $_SESSION['adminName'] = $user['name'];
            $_SESSION['admin_access_role'] = $user['access_role'];
            $_SESSION['is_admin'] = '0';
            oecrm_maybe_upgrade_password($conn, 'employeesTbl', 'id', (int)$user['id'], 'employeeUpass', $plainPassword, $user['employeeUpass']);
            oecrm_auth_audit($conn, 'admin', (int)$user['id'], (int)$user['company_id'], 'login', 'Employee admin-access login successful', (int)$user['id'], ['username'=>$uname]);
            header("Location:dashboard.php");
            exit;
        }

        oecrm_auth_audit($conn, 'system', 0, 0, 'failed_login', 'Admin portal login failed', null, ['username'=>$uname]);
        $loginError = "<div class='form-group'><div class='alert alert-danger alert-dismissable'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>x</button>
                            <p class='alert-link'>Invalid login credentials.</p>
                        </div></div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Oddeven Infotech Pvt. Ltd.</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/custom/customAdmin.css?v=20260627-3" rel="stylesheet">
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    

    <div class="container">
        <div class="row">
            <div class="col-md-5 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <img class="img-responsive logo_img" src="../admin/img/logo.png" alt="logo">
                        <p class="text_heading">Enter your Username and Password to access admin panel.</p>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="POST">
                            <?php echo oecrm_csrf_field(); ?>
                            <fieldset>
                                <?php echo $loginError; ?>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input class="form-control" placeholder="Enter your username" name="uname" type="text" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <div class="oecrm-password-field">
                                        <span class="oecrm-password-prefix" aria-hidden="true"><i class="fa fa-lock"></i></span>
                                        <input class="form-control" placeholder="Enter your password" name="password" type="password" maxlength="13" autocomplete="current-password" required>
                                        <button type="button" class="btn btn-default oecrm-password-toggle" data-target="admin-login-password" aria-label="Show password"><i class="fa fa-eye"></i></button>
                                    </div>
                                </div>
                                <input type="submit" class="btn btn-lg btn-success btn-block" value="Login" name="submit">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>
    <script>
      (function(){
        var input = document.querySelector('input[name="password"]');
        var toggle = document.querySelector('.oecrm-password-toggle');
        if (input) input.id = 'admin-login-password';
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


