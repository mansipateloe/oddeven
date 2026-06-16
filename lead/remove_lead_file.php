<?php
ob_start();
session_start();
include("process/config.php");


	$eid = $_REQUEST['rid'];
	$sele="select * from cp_lead_file where id='".$eid."'";
	$que=mysqli_query($conn,$sele);
	$fet=mysqli_fetch_array($que);
	unlink('img/'.$fet['lfile']);
	
	mysqli_query($conn,"delete from cp_lead_file where id='".$eid."'");
?>