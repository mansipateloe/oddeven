<?php
ob_start();
session_start();
include("config.php");
include("../functions.php");

$fname = $_REQUEST['fname'];
$lname = $_REQUEST['lname'];
$department = $_REQUEST['department'];
$design = $_REQUEST['design'];
$phoneno = $_REQUEST['phoneno'];
$emailid = $_REQUEST['emailid'];
$doj = date('Y-m-d',strtotime($_REQUEST['doj']));
$address = addslashes($_REQUEST['address']);

$region = $_REQUEST['region'];



$img = $_FILES['photo']['name'];
$i = strrpos($img,".");
$l = strlen($img) - $i;
$ext = substr($img,$i+1,$l);
$r = explode(".",$img);
if($ext != ""){ $name = '_'.time().'.'.$ext; } else { $name = ""; }
$target = "../img/".$name;

	$oldimg = $_REQUEST['oldphoto'];
	$nimg = "";
	if($img == ""){ $nimg = $oldimg; } else { $nimg = $name; }
	$update = "update cp_users set fname='".$fname."', lname='".$lname."', department='".$department."', designation='".$design."', photo='".$nimg."', phoneno='".$phoneno."', doj='".$doj."', address='".$address."', region='".$region."'  where uid='".$_SESSION['admin_login_id']."'";
	$query = mysqli_query($conn,$update) or die(mysqli_error($conn));
	if($query)
	{
		$_SESSION['msg'] = "done";
		if($ext != "")
		{
			chmod("../img/",0777);
			move_uploaded_file($_FILES['photo']['tmp_name'], $target);
			chmod("../img/",0555);
		}
		$ldate = date('Y-m-d',time());
		mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Update the profile details.','".$ldate."','".getHostByName(php_uname('n'))."')");
	} else {
		$_SESSION['msg'] = "error";
	}
	header("location:../index.php?pid=profile1");

?>