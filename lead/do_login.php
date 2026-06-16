<?php
@ob_start();
require_once("process/config.php");
require_once __DIR__ . '/../security.php';

if (isset($_POST['do_login'])) {
    $uname = trim($_POST['login_username'] ?? '');
    $pass = $_POST['login_password'] ?? '';

    if ($uname === '' || $pass === '') {
        $_SESSION['msg'] = "up_black";
        header("location:../login.php");
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM cp_login WHERE email = BINARY ? AND status != '1' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $uname);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    $fetch = mysqli_fetch_assoc($query);
    mysqli_stmt_close($stmt);

    if ($fetch && oecrm_password_verify($pass, $fetch['pass'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $uname;
        $_SESSION['admin_login_id'] = $fetch['id'];
        $_SESSION['utype'] = 1;
        oecrm_maybe_upgrade_password($conn, 'cp_login', 'id', (int)$fetch['id'], 'pass', $pass, $fetch['pass']);

        $ldate = date('Y-m-d');
        $details = 'Admin Login in the system.';
        $cip = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt = mysqli_prepare($conn, "INSERT INTO cp_log (lid, exname, details, ldate, cip) VALUES (NULL, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $uname, $details, $ldate, $cip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "success";
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM cp_users WHERE emailid = BINARY ? AND status != '1' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $uname);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    $fetch = mysqli_fetch_assoc($query);
    mysqli_stmt_close($stmt);

    if ($fetch && oecrm_password_verify($pass, $fetch['pass'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $uname;
        $_SESSION['admin_login_id'] = $fetch['uid'];
        $_SESSION['utype'] = $fetch['utype'];
        oecrm_maybe_upgrade_password($conn, 'cp_users', 'uid', (int)$fetch['uid'], 'pass', $pass, $fetch['pass']);

        $ldate = date('Y-m-d');
        $details = 'User Login in the system.';
        $cip = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt = mysqli_prepare($conn, "INSERT INTO cp_log (lid, exname, details, ldate, cip) VALUES (NULL, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $uname, $details, $ldate, $cip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "success";
        exit;
    }

    echo "fail";
}
?>
