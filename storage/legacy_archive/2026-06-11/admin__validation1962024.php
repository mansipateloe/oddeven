<?php error_reporting(E_ALL);
ini_set('display_errors', '1');

?>

<?php

$employeeCode  = "";
$name = "";
$employeeUname = "";
$employeeUpass = "";
$mobile1 = "";
$mobile2 = "";
$companyEmail = "";
$skypeUname = "";
$address = "";
$joiningDate = "";
$personalEmail = "";
$salary = "";
$bankName = "";
$bankIFSCno = "";
$bankAcHolderName = "";
$bankAcNo = "";

$employeeCodeError  = "";
$nameError = "";
$mobile1Error = "";
$mobile2Error = "";
$salaryError = "";

$errors = 0;

$holidayDate = "";
$holidayTitle = "";
$holidayNotice = "";

$designation = "";
$designationError = "";

if (isset($_POST['addemployee'])) {
    // echo 'hii';
    // exit;
    //$name = $_POST['name'];
    $employeeCode = mysqli_real_escape_string($conn, $_POST['employeeCode']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    //$designation = $_POST['designation'];
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    //$employeeUname = $_POST['employeeUname'];
    $employeeUname = mysqli_real_escape_string($conn, $_POST['employeeUname']);
    $employeeUpass = md5($_POST['employeeUpass']);
    //$mobile1 = $_POST['mobile1'];
    $mobile1 = mysqli_real_escape_string($conn, $_POST['mobile1']);
    //$mobile2 = $_POST['mobile2'];
    $mobile2 = mysqli_real_escape_string($conn, $_POST['mobile2']);
    //$companyEmail = $_POST['companyEmail'];
    $companyEmail = mysqli_real_escape_string($conn, $_POST['companyEmail']);
    $personalEmail = mysqli_real_escape_string($conn, $_POST['personalEmail']);
    //$skypeUname = $_POST['skypeUname'];
    $skypeUname = mysqli_real_escape_string($conn, $_POST['skypeUname']);
    //$address = $_POST['address'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $joiningDate = $_POST['joiningDate'];
    $salary = $_POST['salary'];
    $bankName = mysqli_real_escape_string($conn, $_POST['bankName']);
    $bankIFSCno = mysqli_real_escape_string($conn, $_POST['bankIFSCno']);
    $bankAcHolderName = mysqli_real_escape_string($conn, $_POST['bankAcHolderName']);
    $bankAcNo = mysqli_real_escape_string($conn, $_POST['bankAcNo']);
    $birthdate = $_POST['birthdate'];
    $status = 1;


    if (empty($_POST["employeeCode"])) {
        $employeeCodeError = "Employee code required.";
        $errors = 1;
    }
    if (empty($_POST["name"])) {
        $nameError = "Name required.";
        $errors = 1;
    }
    if (empty($_POST["designation"])) {
        $designationError = "Designation required.";
        $errors = 1;
    }
    if (!empty($_POST['salary'])) {
        $salary = test_input($_POST['salary']);
        if (!preg_match("/^[0-9]{1,10}$/", $salary)) {
            $salaryError = "Salary must be numeric and maximum 10 digit allowed.";
            $errors = 1;
        }
    }

    /*$mobile1 = test_input($_POST["mobile1"]);
        if(!preg_match("/^\d{10,14}$/", $mobile1)){    /^[1-9][0-9]{0,15}$/
            $mobile1Error = "Number must be 10 digit";
            $errors = 1;
        }

        $mobile2 = test_input($_POST["mobile2"]);
        if(!preg_match("/^\d{10,14}$/", $mobile2)){
            $mobile2Error = "Number must be 10 digit";
            $errors = 1;
        }*/

    if ($errors != 1) {
        $qry = "INSERT INTO employeesTbl (employeeCode, designation, name, birthdate, employeeUname, employeeUpass, companyEmail, personalEmail, mobile1, mobile2, skypeUname, joiningDate, salary, bankName, bankIFSCno, bankAcHolderName, bankAcNo, address, status) VALUES ('$employeeCode', '$designation', '$name', '$birthdate', '$employeeUname', '$employeeUpass', '$companyEmail', '$personalEmail', '$mobile1', '$mobile2', '$skypeUname', '$joiningDate', '$salary', '$bankName', '$bankIFSCno', '$bankAcHolderName', '$bankAcNo', '$address', '$status')";
       // exit;
        //echo '<pre>'; print_r($qry);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
        if (mysqli_query($conn, $qry)) {
            echo "Data inserted succesfully.";
            /*header('Location:manageEmployee.php');*/
        } else {
            echo "Data not inserted succesfully.";
        }
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<?php
if (isset($_POST['addHost'])) {
    //echo '<pre>'; print_r($_POST);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    $clientname = $_POST['clientname'];
    $service = $_POST['service'];
    $domainname = $_POST['domainname'];
    $startdate = $_POST['startdate'];
    $enddate = $_POST['enddate'];
    $amount = $_POST['amount'];

    $domainQry = "INSERT INTO `domainhostingtbl`(`clientname`, `service`, `domainname`, `startdate`, `enddate`, `amount`) VALUES ('$clientname','$service','$domainname','$startdate','$enddate','$amount')";
    $domainResult = mysqli_query($conn, $domainQry);
    if ($domainResult) {
        header('Location:viewDomainHosting.php');
    }
}
?>

<?php
if (isset($_POST['updateHost'])) {
    //echo '<pre>'; print_r($_POST);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    $id = $_GET['edit'];
    $clientname = $_POST['clientname'];
    $service = $_POST['service'];
    $domainname = $_POST['domainname'];
    $startdate = $_POST['startdate'];
    $enddate = $_POST['enddate'];
    $amount = $_POST['amount'];

    $editDomain = "UPDATE `domainhostingtbl` SET `clientname`='$clientname',`service`='$service',`domainname`='$domainname',`startdate`='$startdate',`enddate`='$enddate',`amount`='$amount' WHERE domain_id=" . $id;
    $domainResult = mysqli_query($conn, $editDomain);
    if ($domainResult) {
        header('Location:viewDomainHosting.php');
    }
}
?>

<?php
if (isset($_POST['addHoliday'])) {
    $holidayDate = $_POST['holidayDate'];
    //$holidayTitle = $_POST['holidayTitle'];
    $holidayTitle = mysqli_real_escape_string($conn, $_POST['holidayTitle']);
    //$holidayNotice = $_POST['holidayNotice'];
    /*$holidayNotice = mysqli_real_escape_string($conn,$_POST['holidayNotice']);*/

    /*$qry = "INSERT INTO holidaytbl (holidayDate, holidayTitle, holidayNotice) VALUES ('$holidayDate', '$holidayTitle', '$holidayNotice')";*/
    $qry = "INSERT INTO holidaytbl (holidayDate, holidayTitle) VALUES ('$holidayDate', '$holidayTitle')";
    if (mysqli_query($conn, $qry)) {
        /*echo "Data inserted succesfully.";*/
        header('Location:manageHoliday.php');
    } else {
        echo "Data not inserted succesfully.";
    }
}
?>

<?php
if (isset($_POST['addDesignation'])) {
    //$designation = $_POST['designation'];
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);

    $qry = "INSERT INTO designation (designation) VALUES ('$designation')";
    if (mysqli_query($conn, $qry)) {
        /*echo "Data inserted succesfully.";*/
        header('Location:manageDesignation.php');
    } else {
        echo "Data not inserted succesfully.";
    }
}
?>

<?php
if (isset($_POST['addProject'])) {
    $developers = $_POST['developers'];
    $serializedDevelopers = json_encode($developers);
    $projectName = mysqli_real_escape_string($conn, $_POST['projectName']);
    $description = $_POST['description'];

    $startdate = $_POST['startdate'];
    $enddate = $_POST['enddate'];
    $amount = $_POST['amount'];
    $expence = $_POST['expence'];
    $customerName = $_POST['customerName'];
    $platform = $_POST['platform'];
    $nickName = $_POST['nickName'];
    $projectType = $_POST['projectType'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $location = $_POST['location'];
    $country = $_POST['country'];
    $reference = $_POST['reference'];
    $status = $_POST['status'];

    $attachment = "";
    if (isset($_FILES['productimage']) && $_FILES['productimage']['error'] == "0") {
        $attachment = $_FILES['productimage']['name'];
        $target_path = "attachment/";
        $target_filepath = $target_path . $attachment;
        $img  = move_uploaded_file($_FILES['productimage']['tmp_name'], $target_filepath);
    }

    $insertProject = "insert into projectsTbl (projectName, developerId, description, attachment, startdate, enddate, amount, expence, customerName,nickName,projectType,platform, phone, email, location, country, reference, status) values ('$projectName', '$serializedDevelopers', '$description', '$attachment', '$startdate', '$enddate', '$amount', '$expence', '$customerName','$nickName','$projectType','$platform', '$phone', '$email', '$location', '$country', '$reference','$status')";

    $projectResult = mysqli_query($conn, $insertProject) or die(mysqli_error($conn));
    if ($projectResult) {
        // header('Location:viewProject.php');
    }
}
?>



 <?php
    if (isset($_POST['updateProject'])) {
        $id = $_GET['edit'];
        $projectName = mysqli_real_escape_string($conn, $_POST['projectName']);
        $developers = json_encode($_POST['developers']);
        $description = $_POST['description'];
        $attachment = $_POST['productimage'];
        $startdate = $_POST['startdate'];
        $enddate = $_POST['enddate'];
        $amount = $_POST['amount'];
        $expence = $_POST['expence'];
        $customerName = $_POST['customerName'];
        $platform = $_POST['platform'];
        $nickName = $_POST['nickName'];
        $projectType = $_POST['projectType'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $location = $_POST['location'];
        $country = $_POST['country'];
        $reference = $_POST['reference'];
        $status = $_POST['status'];

        $editProject = "UPDATE projectsTbl SET projectName='$projectName', developerId='$developers', description='$description', attachment='$attachment',startdate='$startdate', enddate='$enddate', amount='$amount', expence='$expence', customerName='$customerName',platform='$platform',nickName='$nickName',projectType='$projectType', phone='$phone', email='$email', location='$location', country='$country', reference='$reference',status='$status' WHERE id=" . $id;
        if (mysqli_query($conn, $editProject)) {
            echo "Data updated succesfully.";
            header('Location:viewProject.php');
        } else {
            echo "Data not inserted succesfully.";
        }
    }
    ?>
<?php
if (isset($_POST['assignTask'])) {
    echo '<pre>';
    print_r($_POST);
    die((__FILE__) . '-->' . (__FUNCTION__) . '--Line(' . (__LINE__) . ')');
    $projectId = $_POST['projectId'];
    $developerId = "4";
    $task_details = mysqli_real_escape_string($conn, $_POST['task_details']);
    $status = 1;

    $qryTask = "INSERT INTO taskTbl (projectId, developerId, task_details, status) VALUES ('$projectId', '$developerId', '$task_details', '$status')";
    if (mysqli_query($conn, $qryTask)) {
        header('Location:addTask.php');
    }
}
?>
<?php
if (isset($_POST['updatetaskhourstbl'])) {
    //echo '<pre>'; print_r($_POST);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    $id = $_GET['edit'];
    $rowId = $_POST['rowid'];
    $adminResponse = mysqli_real_escape_string($conn, $_POST['adminResponse']);
    $status = $_POST['status'];

    $rowUpdateQry = "update taskhourstbl set adminResponse='$adminResponse' where id=" . $rowId;
    if (mysqli_query($conn, $rowUpdateQry)) {
        $rowStatusQry = "update taskTbl set status='$status' where id=" . $id;
        if (mysqli_query($conn, $rowStatusQry)) {
            /*echo "Status inserted succesfully with file upload.";*/
            header('Location:editTask.php?edit=' . $id);
        }
        /*echo "Data inserted succesfully with file upload.";*/
        header('Location:editTask.php?edit=' . $id);
    } else {
        echo "Data not inserted succesfully.";
    }
}
?>

<?php
if (isset($_POST['updateTask'])) {
    $id = $_GET['edit'];
    $task_details = mysqli_real_escape_string($conn, $_POST['task_details']);

    $qryeditTask = "update taskhourstbl set worklog='$task_details' where id=" . $id;
    if (mysqli_query($conn, $qryeditTask)) {
        echo "Data inserted succesfully.";
        header('Location:editTask.php?edit=' . $id);
    } else {
        echo "Data not inserted succesfully.";
    }
}
?>

<?php
$account_id = 0;
$account_name = "";
$balance = "";
$update = false;

if (isset($_POST['addAccount'])) {
    $account_name = $_POST['account_name'];
    $balance = $_POST['balance'];
    $qryAccount = "INSERT INTO account (account_name, balance) VALUES ('$account_name', '$balance')";
    if (mysqli_query($conn, $qryAccount)) {
        header('Location:addaccount.php');
    }
    //echo '<pre>'; print_r($qryAccount);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
}

if (isset($_POST['updateAccount'])) {
    $id = $_GET['edit'];
    $account_name = $_POST['account_name'];
    $balance = $_POST['balance'];
    $editAccountQry = "update account set account_name='$account_name', balance='$balance' where account_id=" . $id;
    if (mysqli_query($conn, $editAccountQry)) {
        header('Location:addaccount.php');
    }
    //echo '<pre>'; print_r($editAccountQry);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $deleteQry = "delete from account where account_id=$id";
    $deleteResult = mysqli_query($conn, $deleteQry);
    header("location: addaccount.php");
}
?>

<?php
$deposit_date = date('d-m-Y');

if (isset($_POST['addDeposit'])) {
    $deposit_date = $_POST['deposit_date'];
    $account_Id = $_POST['account_Id'];
    $project_Id = $_POST['project_Id'];
    $description = $_POST['description'];
    $invoice_id = $_POST['invoice_id'];
    $subject = $_POST['subject'];
    $amount = $_POST['amount'];

    $project_sql = "select * from invoicetbl where invoice_id = $invoice_id limit 1";

    $project_result = mysqli_query($conn, $project_sql);
    if (mysqli_num_rows($project_result) > 0) {
        $project_row = mysqli_fetch_assoc($project_result);
        $amount = $project_row['total_amount'];
    }

   $qryDeposit = "INSERT INTO deposit (deposit_date, account_Id, project_Id, subject, description, amount, invoice_id) VALUES ('$deposit_date', '$account_Id', '$project_Id', '$subject', '$description', '$amount', '$invoice_id')";
   
    if (mysqli_query($conn, $qryDeposit)) {

        $account_sql = "select * from account where account_id = $account_Id limit 1";

        $account_result = mysqli_query($conn, $account_sql);
        if (mysqli_num_rows($account_result) > 0) {
            $account_row = mysqli_fetch_assoc($account_result);
            $balance = $account_row['balance'];
        }
        $final_balance = $balance + $amount ; 
        $editAccount = "update account set balance='$final_balance' where account_id=" . $account_Id;
        mysqli_query($conn, $editAccount);

        header('Location:addDeposit.php');
    }
}

if (isset($_POST['updateDeposit'])) {
    $id = $_POST['id'];
    $deposit_date = $_POST['deposit_date'];
    $account_Id = $_POST['account_Id'];
    $project_Id = $_POST['project_Id'];
    $description = $_POST['description'];
    $invoice_id = $_POST['invoice_id'];
    $subject = $_POST['subject'];
    $amount = $_POST['amount'];

    $project_sql = "select * from invoicetbl where invoice_id = $invoice_id limit 1";

    $project_result = mysqli_query($conn, $project_sql);
    if (mysqli_num_rows($project_result) > 0) {
        $project_row = mysqli_fetch_assoc($project_result);
        $amount = $project_row['total_amount'];
    }

    $editQryDeposit = "update deposit set deposit_date='$deposit_date', account_Id='$account_Id', project_Id='$project_Id', subject='$subject', description='$description', amount='$amount', invoice_id='$invoice_id' where deposit_id=" . $id;
    if (mysqli_query($conn, $editQryDeposit)) {
        header('Location:addDeposit.php');
    }
}
?>
<?php
if (isset($_POST['addExpense'])) {
    $expensedate = $_POST['expensedate'];
    $expenseCategory = $_POST['expenseCategory'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $expenseQry = "insert into expense (expenseCategory, expensedate, amount, description) values ('$expenseCategory', '$expensedate', '$amount', '$description')";
    $expenseResult = mysqli_query($conn, $expenseQry);
    if ($expenseResult) {
        header('location:viewexpense.php');
    }
}
?>

<?php
if (isset($_POST['updateExpense'])) {
    $id = $_GET['edit'];
    $expensedate = $_POST['expensedate'];
    $expencecategory = $_POST['expencecategory'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $editExpense = "UPDATE `expense` SET `expenseCategory`='$expencecategory',`expensedate`='$expensedate',`amount`='$amount',`description`='$description' WHERE expense_id=" . $id;
    $expenseResult = mysqli_query($conn, $editExpense);
    if ($expenseResult) {
        header('location:viewexpense.php');
    }
    //echo '<pre>'; print_r($_POST);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
}

?>



<?php
if (isset($_POST['updateEmployee'])) {
    $id = $_GET['edit'];
    //echo '<pre>'; print_r($_POST);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    $employeeCode = mysqli_real_escape_string($conn, $_POST['employeeCode']);
    //$designation = $_POST['designation'];
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    //$name = $_POST['name'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    //$employeeUname = $_POST['employeeUname'];
    $employeeUname = mysqli_real_escape_string($conn, $_POST['employeeUname']);
    $employeeUpass = $_POST['employeeUpass'];
    $oldEmployeeUpass = $_POST['oldEmployeeUpass'];
    if ($oldEmployeeUpass == $employeeUpass) {
        $employeePassword = $oldEmployeeUpass;
    } else {
        $employeePassword = md5($employeeUpass);
    }
    //$emailUname = $_POST['emailUname'];
    $companyEmail = mysqli_real_escape_string($conn, $_POST['companyEmail']);
    $personalEmail = mysqli_real_escape_string($conn, $_POST['personalEmail']);
    //$mobile1 = $_POST['mobile1'];
    $mobile1 = mysqli_real_escape_string($conn, $_POST['mobile1']);
    //$mobile2 = $_POST['mobile2'];
    $mobile2 = mysqli_real_escape_string($conn, $_POST['mobile2']);
    //$skypeUname = $_POST['skypeUname'];
    $skypeUname = mysqli_real_escape_string($conn, $_POST['skypeUname']);
    $joiningDate = $_POST['joiningDate'];
    //$address = $_POST['address'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $bankName = mysqli_real_escape_string($conn, $_POST['bankName']);
    $bankIFSCno = mysqli_real_escape_string($conn, $_POST['bankIFSCno']);
    $bankAcHolderName = mysqli_real_escape_string($conn, $_POST['bankAcHolderName']);
    $bankAcNo = mysqli_real_escape_string($conn, $_POST['bankAcNo']);
    $birthdate = $_POST['birthdate'];

     $qry = "UPDATE employeesTbl SET employeeCode='$employeeCode', designation='$designation', name='$name', birthdate='$birthdate',   employeeUname='$employeeUname', employeeUpass='$employeePassword', companyEmail='$companyEmail', personalEmail='$personalEmail', mobile1='$mobile1', mobile2='$mobile2', skypeUname='$skypeUname', joiningDate='$joiningDate', salary='$salary', bankName='$bankName', bankIFSCno='$bankIFSCno', bankAcHolderName='$bankAcHolderName', bankAcNo='$bankAcNo', address='$address' WHERE id=" . $id;

    if (mysqli_query($conn, $qry)) {
        echo "Data updated succesfully.";
        header('Location:manageEmployee.php');
    } else {
        echo "Data not inserted succesfully.";
    }
}
?>
<?php
if (isset($_POST['updateHoliday'])) {
    $id = $_GET['editHoliday'];
    $holidayDate = $_POST['holidayDate'];
    //$holidayTitle = $_POST['holidayTitle'];
    $holidayTitle = mysqli_real_escape_string($conn, $_POST['holidayTitle']);
    //$holidayNotice = $_POST['holidayNotice'];
    /*$holidayNotice = mysqli_real_escape_string($conn,$_POST['holidayNotice']);*/

    /*$qry = "UPDATE holidaytbl SET holidayDate='$holidayDate', holidayTitle='$holidayTitle', holidayNotice='$holidayNotice' WHERE id=".$id;*/
    $qry = "UPDATE holidaytbl SET holidayDate='$holidayDate', holidayTitle='$holidayTitle' WHERE id=" . $id;
    if (mysqli_query($conn, $qry)) {
        echo "Holiday updated succesfully.";
        header('Location:manageHoliday.php');
    } else {
        echo "Data not inserted succesfully.";
    }
}
?>
<?php
if (isset($_POST['addNotice'])) {
    //$notice = $_POST['notice'];
    $notice = mysqli_real_escape_string($conn, $_POST['notice']);
    /*$startingDate = $_POST['startingDate'];
        $endingDate = $_POST['endingDate'];*/
    $startingDate = date('d-m-Y', strtotime($_POST['startingDate']));
    $endingDate = date('d-m-Y', strtotime($_POST['endingDate']));
    /*$noticeStatus = $_POST['noticeStatus'];*/
    $noticeStatus = 1;

    $qry = "INSERT INTO noticetbl (notice, startingDate, endingDate, noticeStatus) VALUES ('$notice', '$startingDate', '$endingDate', '$noticeStatus')";
    /*echo $qry; die();*/
    if (mysqli_query($conn, $qry)) {
        echo "Data inserted succesfully.";
        header('Location:manageNotice.php');
    } else {
        echo "Data not inserted succesfully.";
    }
}
?>
<?php
if (isset($_POST['updateNotice'])) {
    //echo '<pre>'; print_r($_POST);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    $id = $_GET['edit'];
    //$notice = $_POST['notice'];
    $notice = mysqli_real_escape_string($conn, $_POST['notice']);
    $startingDate = date('d-m-Y', strtotime($_POST['startingDate']));
    $endingDate = date('d-m-Y', strtotime($_POST['endingDate']));
    $noticeStatus = $_POST['noticeStatus'];

    $qry = "UPDATE noticetbl SET notice='$notice', startingDate='$startingDate', endingDate='$endingDate', noticeStatus='$noticeStatus' WHERE id=" . $id;
    if (mysqli_query($conn, $qry)) {
        echo "Data updated succesfully.";
        header('Location:manageNotice.php');
    } else {
        echo "Data not updated succesfully.";
    }
}
?>

<?php
if (isset($_POST['addCurrency'])) {
    //$notice = $_POST['notice'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $symbol = mysqli_real_escape_string($conn, $_POST['symbol']);
    $rate = mysqli_real_escape_string($conn, $_POST['rate']);


    $qry = "INSERT INTO `currency_master`(`name`, `symbol`, `rate`) VALUES ('$name','$symbol','$rate')";
    /*echo $qry; die();*/
    if (mysqli_query($conn, $qry)) {
        echo "Data inserted succesfully.";
        header('Location:manageCurrency.php');
    } else {
        echo "Data not inserted succesfully.";
    }
}

if (isset($_POST['updateCurrency'])) {
    //$notice = $_POST['notice'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $symbol = mysqli_real_escape_string($conn, $_POST['symbol']);
    $rate = mysqli_real_escape_string($conn, $_POST['rate']);
    $rate = mysqli_real_escape_string($conn, $_POST['rate']);
    $id = mysqli_real_escape_string($conn, $_POST['id']);


    $qry = "UPDATE `currency_master` SET name='$name', symbol='$symbol', rate='$rate' where id=$id";
    /*echo $qry; die();*/
    if (mysqli_query($conn, $qry)) {
        echo "Data updated succesfully.";
        header('Location:manageCurrency.php');
    } else {
        echo "Data not updated succesfully.";
    }
}

if (isset($_POST['addLeaveType'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $qry = "INSERT INTO `leavetypetbl`(`id`, `name`) VALUES (NULL,'$name')";
    if (mysqli_query($conn, $qry)) {
        echo "Data Inserted succesfully.";
        header('Location:manageLeaveType.php');
    } else {
        echo "Data not inserted succesfully.";
    }
}
if (isset($_POST['addLeadSource'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $qry = "INSERT INTO `followup_type_tbl`(`id`, `name`) VALUES (NULL,'$name')";
    if (mysqli_query($conn, $qry)) {
        echo "Data Inserted succesfully.";
        header('Location:manageLeadSource.php');
    } else {
        echo "Data not inserted succesfully.";
    }
}

if (isset($_POST['addFollowupType'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $qry = "INSERT INTO `followup_type_tbl`(`id`, `name`) VALUES (NULL,'$name')";
    if (mysqli_query($conn, $qry)) {
        echo "Data Inserted succesfully.";
        header('Location:manageFollowupType.php');
    } else {
        echo "Data not inserted succesfully.";
    }
}

if (isset($_POST['LeaveSave'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    mysqli_query($conn, "UPDATE leave_master SET is_approved='$status',remarks='$remarks' where id=$id");
    header('Location:manageLeave.php');
}


if (isset($_POST['leadSave'])) {
    $leadDate = $_POST['leadDate'];
    $executiveName = $_POST['executiveName'];
    $company = $_POST['company'];
    $cperson = $_POST['cperson'];
    $mobileno1 = $_POST['mobileno1'];
    $mobileno2 = $_POST['mobileno2'];
    $emailid = $_POST['emailid'];
    $emailid2 = $_POST['emailid2'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $leadType = $_POST['leadType'];
    $nick_name=$_POST['nick_name'];
    $status=$_POST['status'];
    $lead_source=$_POST['lead_source'];
    $followupType = $_POST['followupType'];
    $remarks = $_POST['remarks'];
    $nextFollowupDate = $_POST['nextFollowupDate'];
    $nextFollowupTime = $_POST['nextFollowupTime'];

    mysqli_query($conn, "INSERT INTO `leads`(`lead_date`, `executive_name`, `company_name`, `contact_person`, `mobile_no1`, `mobile_no2`, `email`, `personal_email`, 
    `city`, `address`, `assign_to`, `is_active`, `created_at`, `created_by`, `lead_source`, `nick_name`, `status`) 
    VALUES ('$leadDate','$executiveName','$company','$cperson','$mobileno1','$mobileno2','$emailid','$emailid2',
    '$city','$address',NULL,'1',CURRENT_TIMESTAMP,'0','$lead_source','$nick_name','$status')");
    $leadId = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(lead_id) as `id` from leads"));
    $id = $leadId['id'];
    if($remarks!="")
    {
        mysqli_query($conn, "INSERT INTO `lead_followup`
        (`lead_id`, `lead_type`, `followup_type`, `remarks`, `next_followup_date`, `next_followup_time`, `created_at`, `created_by`) 
        VALUES ('$id','$leadType','$followupType','$remarks','$nextFollowupDate','$nextFollowupTime',CURRENT_TIMESTAMP,'0')");
    }
    
    header('Location:add_lead.php');
}

if (isset($_POST['saveLeadFollowup'])) {
    $leadType = $_POST['lead_type'];
    $followupType = $_POST['followupType'];
    $remarks = $_POST['remarks'];
    $nextFollowupDate = $_POST['next_followup_date'];
    $nextFollowupTime = $_POST['next_followup_time'];
    $lead_id = $_POST['lead_id'];
    $status = $_POST['status'];
    mysqli_query($conn, "INSERT INTO `lead_followup`
    (`lead_id`, `lead_type`, `followup_type`, `remarks`, `next_followup_date`, `next_followup_time`, `created_at`, `created_by`, `status`) 
    VALUES ('$lead_id','$leadType','$followupType','$remarks','$nextFollowupDate','$nextFollowupTime',CURRENT_TIMESTAMP,'0','$status')");
    header("Location:lead_details.php?leadId=$lead_id");
}
//add new for report...
if (isset($_POST['ReportSave'])) {
    $workDate = $_POST['workDate'];
    $signinTime = $_POST['signinTime'];
    $lunchinTime = $_POST['lunchinTime'];
    $lunchoutTime = $_POST['lunchoutTime'];
    $breakinTime = $_POST['breakinTime'];
    $breakoutTime = $_POST['breakoutTime'];
    $signoutTime = $_POST['signoutTime'];
    $workHours = $_POST['workHours'];
   

    mysqli_query($conn, "INSERT INTO `workhoursTbl`(`workDate`, `signinTime`, `lunchinTime`, `lunchoutTime`, `lunchoutTime`, `breakinTime`, `breakoutTime`, `signoutTime`, 
    `workHours`) 
    VALUES ('$workDate','$signinTime','$lunchinTime','$lunchoutTime','$lunchoutTime','$breakinTime','$breakoutTime','$signoutTime',
    '$workHours')");
    $Id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(id) as `id` from workhoursTbl"));
    // $id = $leadId['id'];
    // mysqli_query($conn, "INSERT INTO `lead_followup`
    // (`lead_id`, `lead_type`, `followup_type`, `remarks`, `next_followup_date`, `next_followup_time`, `created_at`, `created_by`) 
    // VALUES ('$id','$leadType','$followupType','$remarks','$nextFollowupDate','$nextFollowupTime',CURRENT_TIMESTAMP,'0')");
    // header('Location:add_lead.php');
}

?>