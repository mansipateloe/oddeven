<?php
ob_start();
require_once("config.php");
require_once __DIR__ . '/../../security.php';
include("../functions.php");

if (empty($_SESSION['admin_id'])) {
    header("location:../login.php");
    exit;
}

$fname = $_REQUEST['fname'] ?? '';
$lname = $_REQUEST['lname'] ?? '';
$empcode = $_REQUEST['empcode'] ?? '';
$phoneno = $_REQUEST['phoneno'] ?? '';
$emailid = $_REQUEST['emailid'] ?? '';
$doj = !empty($_REQUEST['doj']) ? date('Y-m-d', strtotime($_REQUEST['doj'])) : null;
$address = $_REQUEST['address'] ?? '';
$plainPassword = $_REQUEST['password'] ?? '';
$pass = $plainPassword !== '' ? oecrm_password_hash($plainPassword) : '';
$utype = $_REQUEST['utype'] ?? '';
$scoordinator = $_REQUEST['scoordinator'] ?? '';

try {
    $uploadedPhoto = oecrm_safe_upload($_FILES['photo'] ?? null, __DIR__ . '/../img', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
} catch (RuntimeException $e) {
    $_SESSION['msg'] = "error";
    header("location:../index.php?pid=users");
    exit;
}

if (isset($_REQUEST['id'])) {
    $id = oecrm_int_param($_REQUEST, 'id');
    $oldimg = $_REQUEST['oldphoto'] ?? '';
    $nimg = $uploadedPhoto !== '' ? $uploadedPhoto : $oldimg;

    if ($pass !== '') {
        $stmt = mysqli_prepare($conn, "UPDATE cp_users SET fname=?, lname=?, utype=?, empcode=?, emailid=?, phoneno=?, pass=?, password='', photo=?, doj=?, address=?, scoordinator=? WHERE uid=?");
        mysqli_stmt_bind_param($stmt, 'sssssssssssi', $fname, $lname, $utype, $empcode, $emailid, $phoneno, $pass, $nimg, $doj, $address, $scoordinator, $id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE cp_users SET fname=?, lname=?, utype=?, empcode=?, emailid=?, phoneno=?, photo=?, doj=?, address=?, scoordinator=? WHERE uid=?");
        mysqli_stmt_bind_param($stmt, 'ssssssssssi', $fname, $lname, $utype, $empcode, $emailid, $phoneno, $nimg, $doj, $address, $scoordinator, $id);
    }
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['msg'] = $ok ? "done" : "error";
    header("location:../index.php?pid=users&id=" . $id);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT uid FROM cp_users WHERE emailid = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $emailid);
mysqli_stmt_execute($stmt);
$existing = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

if (mysqli_num_rows($existing) > 0) {
    $_SESSION['msg'] = "user_avail";
    header("location:../index.php?pid=users");
    exit;
}

$stmt = mysqli_prepare($conn, "INSERT INTO cp_users (uid, fname, lname, utype, empcode, emailid, phoneno, pass, password, photo, status, doj, address, scoordinator) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, '', ?, '0', ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sssssssssss', $fname, $lname, $utype, $empcode, $emailid, $phoneno, $pass, $uploadedPhoto, $doj, $address, $scoordinator);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
$_SESSION['msg'] = $ok ? "done" : "error";
header("location:../index.php?pid=users");
exit;
?>
