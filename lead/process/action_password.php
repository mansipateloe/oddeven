<?php
ob_start();
session_start();
include("config.php");
include("../functions.php");

$old_pass = $_REQUEST['old_pass'];
$new_pass = $_REQUEST['new_pass'];
$con_pass = $_REQUEST['con_pass'];

$sel = "select * from cp_login where id='".$_SESSION['admin_login_id']."' and pass='".md5($old_pass)."'";
$qry = mysqli_query($conn,$sel);
$num = mysqli_num_rows($qry);
if($num > 0)
{
	$update = "update cp_login set pass='".md5($new_pass)."', password='".$new_pass."' where id='".$_SESSION['admin_login_id']."'";
	$query = mysqli_query($conn,$update) or die(mysqli_error($conn));
	if($query)
	{
		$_SESSION['msg'] = "done";
		$ldate = date('Y-m-d',time());
		mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Update the profile password details.','".$ldate."','".getHostByName(php_uname('n'))."')");
	} else {
		$_SESSION['msg'] = "error";
	}
} else {
	$_SESSION['msg'] = "wpass";
}
	header("location:../index.php?pid=profile");

?>