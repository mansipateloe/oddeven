<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();
oecrm_require_permission($conn, 'employees', 'create');
  if(isset($_POST['employeeUname'])){
    $name = mysqli_real_escape_string($conn,$_POST['employeeUname']);
    $stmt=mysqli_prepare($conn,'SELECT id FROM employeestbl WHERE employeeUname=? LIMIT 1');
    mysqli_stmt_bind_param($stmt,'s',$name);
    mysqli_stmt_execute($stmt);
    $query=mysqli_stmt_get_result($stmt);
    if(mysqli_num_rows($query)>0){
      echo "User Name Already Exist";
    }else{
      echo "Username Available";
    }
    mysqli_stmt_close($stmt);
  exit();
  }

  if(isset($_POST['companyEmail'])){
    //$emailId=$_POST['companyEmail'];
    $emailId = mysqli_real_escape_string($conn,$_POST['companyEmail']);

    $checkdata=" SELECT * FROM employeestbl WHERE companyEmail='$emailId' ";

    $query=mysqli_query($conn,$checkdata);

    if(mysqli_num_rows($query)>0){
      echo "Email Already Exist";
    }else{
      echo "Email Available";
    }
    exit();
  }

  if(isset($_POST['projectName'])){
    $projectName = mysqli_real_escape_string($conn,$_POST['projectName']);
    $checkdata=" SELECT * FROM projectstbl WHERE projectName='$projectName'";

    $query=mysqli_query($conn,$checkdata);

    if(mysqli_num_rows($query)>0){
      echo "Projectname already exist";
    }else{
      echo "Projectname Available";
    }
    exit();
  }  

  if(isset($_POST['updateProjectName'])){
    $projectName = mysqli_real_escape_string($conn,$_POST['updateProjectName']);
    $projectId = $_POST['projectId'];

    $checkdata=" SELECT * FROM projectstbl WHERE projectName='$projectName' AND id!='$projectId'";

    $query=mysqli_query($conn,$checkdata);

    if(mysqli_num_rows($query)>0){
      echo "Projectname already exist";
    }else{
      echo "Projectname Available";
    }
    exit();
  }  
?>
