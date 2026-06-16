<?php
ob_start();
session_start();
include("config.php");
include("../functions.php");

$exname = $_SESSION['admin_id'];

$ftype = $_REQUEST['ftype'];
$fdate = date('Y-m-d',strtotime($_REQUEST['fdate']));
$ftime = $_REQUEST['ftime'];
$nfdate = date('Y-m-d',strtotime($_REQUEST['nfdate']));
$nftime = $_REQUEST['nftime'];
$remakrs = addslashes($_REQUEST['remarks']);

$lid = $_REQUEST['lid'];

if(isset($_REQUEST['fid']))
{
	
			$fid = $_REQUEST['fid'];
			
			$update="update cp_lead_followup  set exname='".$exname."', ftype='".$ftype."', fdate='".$fdate."', ftime='".$ftime."', remarks='".$remakrs."', nfdate='".$nfdate."', nftime='".$nftime."' where fid='".$fid."'";
			
			 $query = mysqli_query($conn,$update);
			 if($query)
			 {
			  	   $_SESSION['msg'] = "done";
				   
				   $ldate = date('Y-m-d',time());
					mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Update lead followup details.','".$ldate."','".getHostByName(php_uname('n'))."')");
			 } else {
				$_SESSION['msg'] = "error";
			 }
} else {
	
	$insert = "insert into cp_lead_followup (fid,lid,exname,ftype,fdate,ftime,remarks,nfdate,nftime) values(NULL,'".$lid."','".$exname."','".$ftype."','".$fdate."','".$ftime."','".$remakrs."','".$nfdate."','".$nftime."')";
	$query = mysqli_query($conn,$insert);
	if($query)
	{
		$_SESSION['msg'] = "done";
		
		$ldate = date('Y-m-d',time());
		mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Add lead followup details.','".$ldate."','".getHostByName(php_uname('n'))."')");
		
	} else {
		$_SESSION['msg'] = "error";
	}

}
header("location:../index.php?pid=view_lead_details&id=$lid");
?>