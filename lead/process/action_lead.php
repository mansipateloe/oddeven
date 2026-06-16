<?php
ob_start();
session_start();
include("config.php");
include("../functions.php");
include('../assets/jquery.filer/src/class.fileuploader.php');

$srno = $_REQUEST['srno'];
$empcode = $_REQUEST['empcode'];
$ldate = date('Y-m-d',time());
$exname = $_SESSION['admin_id'];
$moniby = get_moniby($_SESSION['admin_id']);

$company = $_REQUEST['company'];
$cperson = $_REQUEST['cperson'];
$mobileno1 = $_REQUEST['mobileno1'];
$mobileno2 = $_REQUEST['mobileno2'];

$emailid = $_REQUEST['emailid'];
$city = $_REQUEST['city'];
$address = addslashes($_REQUEST['address']);
$pname = $_REQUEST['pname'];
$dpv = $_REQUEST['dpv'];
$ltype = $_REQUEST['ltype'];


if(isset($_REQUEST['id']))
{
			$lid = $_REQUEST['id'];
			if(isset($_REQUEST['ltype']))
			{
	     		$update="update cp_lead  set company='".$company."', cperson='".$cperson."', mobile1='".$mobileno1."', mobile2='".$mobileno2."', emailid='".$emailid."', city='".$city."', address='".$address."', pname='".$pname."', dpv='".$dpv."', ltype='".$ltype."' where lid='".$lid."'";
			} else {
				$update="update cp_lead  set company='".$company."', cperson='".$cperson."', mobile1='".$mobileno1."', mobile2='".$mobileno2."', emailid='".$emailid."', city='".$city."', address='".$address."', pname='".$pname."', dpv='".$dpv."' where lid='".$lid."'";
			}
			 $query = mysqli_query($conn,$update);
			 if($query)
			 {
			  	   $_SESSION['msg'] = "done";
				    
					// initialize FileUploader
					$FileUploader = new FileUploader('lfile', array(
						'uploadDir' => '../img/',
						'title' => 'name'
					));
					// call to upload the files
					$data = $FileUploader->upload();
					// get the fileList
					$fileList = $FileUploader->getFileList();
					// show
					foreach($fileList as $val)
					{
						$aname = $val['name'];
						mysqli_query($conn,"insert into cp_lead_file (id,lid,lfile) values(NULL,'".$lid."','".$aname."')");
					}
					
					$ldate = date('Y-m-d',time());
					mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Update lead details.','".$ldate."','".getHostByName(php_uname('n'))."')");
			 } else {
				$_SESSION['msg'] = "error";
			 }
			 header("location:../index.php?pid=add_lead&id=$lid");
} else {
	
	$ftype = $_REQUEST['ftype'];
	$remarks = addslashes($_REQUEST['remarks']);
	$fdate = date('Y-m-d',time());
	$ftime = date('h:i A',time());
	$nfdate = date('Y-m-d',strtotime($_REQUEST['nfdate']));
	$nftime = $_REQUEST['nftime'];
	
	$insert = "insert into cp_lead (lid,srno,empcode,ldate,exname,moniby,company,cperson,mobile1,mobile2,emailid,city,address,pname,dpv,ltype) values(NULL,'".$srno."','".$empcode."','".$ldate."','".$exname."','".$moniby."','".$company."','".$cperson."','".$mobileno1."','".$mobileno2."','".$emailid."','".$city."','".$address."','".$pname."','".$dpv."','".$ltype."')";
	$query = mysqli_query($conn,$insert);
	if($query)
	{
		$_SESSION['msg'] = "done";
		$lid = mysqli_insert_id($conn);
		
		// initialize FileUploader
	    $FileUploader = new FileUploader('lfile', array(
	        'uploadDir' => '../img/',
	        'title' => 'name'
	    ));
		// call to upload the files
	    $data = $FileUploader->upload();
		// get the fileList
		$fileList = $FileUploader->getFileList();
		// show
		foreach($fileList as $val)
		{
			$aname = $val['name'];
			mysqli_query($conn,"insert into cp_lead_file (id,lid,lfile) values(NULL,'".$lid."','".$aname."')");
		}
		$ldate = date('Y-m-d',time());
		mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Add lead  details.','".$ldate."','".getHostByName(php_uname('n'))."')");
		
		mysqli_query($conn,"insert into cp_lead_followup (fid,lid,exname,ftype,fdate,ftime,remarks,nfdate,nftime) values(NULL,'".$lid."','".$exname."','".$ftype."','".$fdate."','".$ftime."','".$remarks."','".$nfdate."','".$nftime."')");
		
	} else {
		$_SESSION['msg'] = "error";
	}
	header("location:../index.php?pid=add_lead");
}

?>