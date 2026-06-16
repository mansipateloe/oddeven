<?php
ob_start();
session_start();
include("config.php");

$lid = $_REQUEST['id'];

$d1="delete from cp_lead where lid='".$lid."'";
$qur=mysqli_query($conn,$d1);
if($qur)
{
	$_SESSION['msg']='done';
	$d3="delete from cp_lead_file where lid='".$lid."'";
	$qur3=mysqli_query($conn,$d3);
	mysqli_query($conn,"delete from cp_lead_followup where lid='".$lid."'");
	
	$ldate = date('Y-m-d',time());
		mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Delete lead details.','".$ldate."','".getHostByName(php_uname('n'))."')");
}
else{
	$_SESSION['msg']='error';
}
header("Location:../index.php?pid=view_all_leads");
?>