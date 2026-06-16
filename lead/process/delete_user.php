<?php
ob_start();
session_start();
include("config.php");

$id = $_REQUEST['id'];

$delete = "delete from cp_users where uid='$id'";
$query = mysqli_query($conn,$delete);
if($query)
{
	$_SESSION['msg'] = "done";
	$ldate = date('Y-m-d',time());
	mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Delete user details.','".$ldate."','".getHostByName(php_uname('n'))."')");
} else {
	$_SESSION['msg'] = "error";
}

header("location:../index.php?pid=users");
?>