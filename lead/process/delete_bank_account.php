<?php
ob_start();
session_start();
include("config.php");

$bid = $_REQUEST['id'];

$d1="delete from cp_bank where bid='".$bid."'";
$qur=mysqli_query($conn,$d1);
if($qur)
{
	$_SESSION['msg']='done';
	
	$ldate = date('Y-m-d',time());
	mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Delete bank account details.','".$ldate."','".getHostByName(php_uname('n'))."')");
}
else{
	$_SESSION['msg']='error';
}
header("Location:../index.php?pid=bank_account");
?>