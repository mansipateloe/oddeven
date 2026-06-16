<?php
ob_start();
session_start();
include("config.php");

$lid = $_REQUEST['id'];
$fid = $_REQUEST['fid'];

$d1="delete from cp_lead_followup where fid='".$fid."'";
$qur=mysqli_query($conn,$d1);
if($qur)
{
	$_SESSION['msg']='done';

	$ldate = date('Y-m-d',time());
	mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Delete lead followup details.','".$ldate."','".getHostByName(php_uname('n'))."')");
}
else{
	$_SESSION['msg']='error';
}
header("Location:../index.php?pid=view_lead_details&id=$lid");
?>