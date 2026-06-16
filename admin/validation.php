<?php require_once __DIR__ . '/../security.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
    $legacyProjectActions = ['addProject', 'updateProject'];
    foreach ($legacyProjectActions as $legacyAction) {
        if (isset($_POST[$legacyAction])) {
            $_SESSION['project_error'] = 'This legacy project action is disabled. Please use Project Management.';
            header('Location: projectEditor.php');
            exit;
        }
    }
    $legacyTaskActions = ['assignTask', 'updatetaskhourstbl', 'updateTask'];
    foreach ($legacyTaskActions as $legacyAction) {
        if (isset($_POST[$legacyAction])) {
            $_SESSION['task_error'] = 'This legacy task action is disabled. Please use Task Management.';
            header('Location: viewTask.php');
            exit;
        }
    }
}
error_reporting(E_ALL);
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

    $employeeUpass = oecrm_password_hash($_POST['employeeUpass']);

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



    /*$qry = "INSERT INTO holidayTbl (holidayDate, holidayTitle, holidayNotice) VALUES ('$holidayDate', '$holidayTitle', '$holidayNotice')";*/

    $qry = "INSERT INTO holidayTbl (holidayDate, holidayTitle) VALUES ('$holidayDate', '$holidayTitle')";

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

    try {
        $attachment = oecrm_safe_upload($_FILES['productimage'] ?? null, __DIR__ . '/attachment');
    } catch (RuntimeException $e) {
        die($e->getMessage());
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



    $rowUpdateQry = "SELECT 0";

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



    $qryeditTask = "SELECT 0";

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

    $tax_amount = $_POST['tax_amount'];

    $approx_amount = $_POST['approx_amount'];



    $project_sql = "select * from invoicetbl where invoice_id = $invoice_id limit 1";



    $project_result = mysqli_query($conn, $project_sql);

    if (mysqli_num_rows($project_result) > 0) {

        $project_row = mysqli_fetch_assoc($project_result);

        $amount = $project_row['total_amount'];

    }



   $qryDeposit = "INSERT INTO deposit (deposit_date, account_Id, project_Id, subject, description, amount, invoice_id,tax_amount, approx_amount) VALUES ('$deposit_date', '$account_Id', '$project_Id', '$subject', '$description', '$amount', '$invoice_id', '$tax_amount', '$approx_amount')";

   

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



        $editAccount = "update invoiceTbl set status='2' where invoice_id=" . $invoice_id;

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

        $employeePassword = oecrm_password_hash($employeeUpass);

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



    /*$qry = "UPDATE holidayTbl SET holidayDate='$holidayDate', holidayTitle='$holidayTitle', holidayNotice='$holidayNotice' WHERE id=".$id;*/

    $qry = "UPDATE holidayTbl SET holidayDate='$holidayDate', holidayTitle='$holidayTitle' WHERE id=" . $id;

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




if (isset($_POST['DashboardLeaveSave'])) {
    $id = (int)$_POST['id'];
    $status = (int)$_POST['status'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');
    mysqli_query($conn, "UPDATE leave_master SET is_approved='$status', remarks='$remarks' WHERE id=$id");
    header('Location:dashboard.php');
    exit;
}
if (isset($_POST['LeaveSave'])) {

    $id = $_POST['id'];

    $status = $_POST['status'];

    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    mysqli_query($conn, "UPDATE leave_master SET is_approved='$status',remarks='$remarks' where id=$id");

    header('Location:manageLeave.php');

}





if (isset($_POST['leadSave'])) {
    oecrm_require_permission($conn,'clients','create');
    $companyId=oecrm_current_company_id($conn);$actor=(int)$_SESSION['adminId'];
    $leadDate=$_POST['leadDate']??'';$executiveName=trim($_POST['executiveName']??'');$company=trim($_POST['company']??'');$cperson=trim($_POST['cperson']??'');$mobileno1=trim($_POST['mobileno1']??'');$mobileno2=trim($_POST['mobileno2']??'');$emailid=trim($_POST['emailid']??'');$emailid2=trim($_POST['emailid2']??'');$city=trim($_POST['city']??'');$address=trim($_POST['address']??'');$leadType=$_POST['leadType']??'medium';$nick_name=trim($_POST['nick_name']??'');$status=$_POST['status']??'pending';$leadSource=(int)($_POST['lead_source']??0);$followupType=$_POST['followupType']??'Call';$remarks=trim($_POST['remarks']??'');$nextFollowupDate=$_POST['nextFollowupDate']?:null;$nextFollowupTime=$_POST['nextFollowupTime']?:null;
    if($executiveName===''||!strtotime($leadDate)){$_SESSION['lead_flash']='Lead date and client name are required.';header('Location:add_lead.php');exit;}
    mysqli_begin_transaction($conn);
    try{
        $stmt=mysqli_prepare($conn,'INSERT INTO leads(company_id,lead_date,executive_name,company_name,contact_person,mobile_no1,mobile_no2,email,personal_email,city,address,assign_to,is_active,created_at,created_by,lead_source,nick_name,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,NULL,1,NOW(),?,?,?,?)');
        mysqli_stmt_bind_param($stmt,'issssssssssiiss',$companyId,$leadDate,$executiveName,$company,$cperson,$mobileno1,$mobileno2,$emailid,$emailid2,$city,$address,$actor,$leadSource,$nick_name,$status);
        mysqli_stmt_execute($stmt);$id=mysqli_insert_id($conn);mysqli_stmt_close($stmt);
        if($remarks!==''){$stmt=mysqli_prepare($conn,'INSERT INTO lead_followup(lead_id,lead_type,followup_type,remarks,next_followup_date,next_followup_time,created_at,created_by,status) VALUES(?,?,?,?,?,?,NOW(),?,"pending")');mysqli_stmt_bind_param($stmt,'isssssi',$id,$leadType,$followupType,$remarks,$nextFollowupDate,$nextFollowupTime,$actor);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);}
        mysqli_commit($conn);oecrm_audit($conn,'clients','create','lead',$id,'Lead created',null,['company_name'=>$company,'status'=>$status]);$_SESSION['lead_flash']='Lead created successfully.';
    }catch(Throwable $exception){mysqli_rollback($conn);$_SESSION['lead_flash']=$exception->getMessage();}
    header('Location:leads.php');exit;

}



if (isset($_POST['saveLeadFollowup'])) {
    oecrm_require_permission($conn,'client_communications','create');
    $companyId=oecrm_current_company_id($conn);$actor=(int)$_SESSION['adminId'];$leadType=$_POST['lead_type']??'';$followupType=$_POST['followupType']??'';$remarks=trim($_POST['remarks']??'');$nextFollowupDate=$_POST['next_followup_date']?:null;$nextFollowupTime=$_POST['next_followup_time']?:null;$lead_id=(int)($_POST['lead_id']??0);$status=$_POST['status']??'pending';
    $stmt=mysqli_prepare($conn,'SELECT lead_id FROM leads WHERE lead_id=? AND company_id=? AND is_active=1');mysqli_stmt_bind_param($stmt,'ii',$lead_id,$companyId);mysqli_stmt_execute($stmt);$valid=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);if(!$valid){http_response_code(404);exit('Lead not found.');}
    $stmt=mysqli_prepare($conn,'INSERT INTO lead_followup(lead_id,lead_type,followup_type,remarks,next_followup_date,next_followup_time,created_at,created_by,status) VALUES(?,?,?,?,?,?,NOW(),?,?)');mysqli_stmt_bind_param($stmt,'isssssis',$lead_id,$leadType,$followupType,$remarks,$nextFollowupDate,$nextFollowupTime,$actor,$status);mysqli_stmt_execute($stmt);$followupId=mysqli_insert_id($conn);mysqli_stmt_close($stmt);oecrm_audit($conn,'client_communications','create','lead_followup',$followupId,'Lead follow-up created',null,['lead_id'=>$lead_id,'status'=>$status]);
    header("Location:lead_details.php?leadId=$lead_id");exit;

}

//add new for report...

if (isset($_POST['ReportSave'])) {
    oecrm_require_permission($conn,'attendance_review','correct');
    $_SESSION['attendance_flash']='Legacy manual attendance entry is disabled. Use Review & Exceptions.';
    header('Location:attendanceReview.php');exit;
}





if (isset($_POST['addBankDetails'])) {

    //$notice = $_POST['notice'];

    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);



    $qry = "INSERT INTO `bank_details`(`bank_name`) VALUES ('$bank_name' )";

    // echo $qry; die();

    if (mysqli_query($conn, $qry)) {

        echo "Data inserted succesfully.";

        header('Location:addBankDetails.php');

    } else {

        echo "Data not inserted succesfully.";

    }

}



if (isset($_POST['updateBankDetails'])) {

    //$notice = $_POST['notice'];

    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);

    $id = mysqli_real_escape_string($conn, $_POST['id']);



    $qry = "UPDATE `bank_details` SET bank_name='$bank_name' where bank_id=$id";

    /*echo $qry; die();*/

    if (mysqli_query($conn, $qry)) {

        echo "Data updated succesfully.";

        header('Location:addBankDetails.php');

    } else {

        echo "Data not updated succesfully.";

    }

}



if (isset($_GET['bank_delete'])) {

    $id = $_GET['bank_delete'];

    $deleteQry = "delete from bank_details where bank_id=$id";

    $deleteResult = mysqli_query($conn, $deleteQry);

    header("location: addBankDetails.php");

}



if (isset($_POST['addCountry'])) {

    //$notice = $_POST['notice'];

    $country_code = mysqli_real_escape_string($conn, $_POST['country_code']);

    $country_symbols = mysqli_real_escape_string($conn, $_POST['country_symbols']);



    $qry = "INSERT INTO `country`(`country_code`, `country_symbols`) VALUES ('$country_code','$country_symbols')";

    // echo $qry; die();

    if (mysqli_query($conn, $qry)) {

        echo "Data inserted succesfully.";

        header('Location:addcountry.php');

    } else {

        echo "Data not inserted succesfully.";

    }

}



if (isset($_POST['updateCountry'])) {

    //$notice = $_POST['notice'];

    $country_code = mysqli_real_escape_string($conn, $_POST['country_code']);

    $country_symbols = mysqli_real_escape_string($conn, $_POST['country_symbols']);

    $id = mysqli_real_escape_string($conn, $_POST['id']);



    $qry = "UPDATE `country` SET country_code='$country_code', country_symbols='$country_symbols' where country_id=$id";

    /*echo $qry; die();*/

    if (mysqli_query($conn, $qry)) {

        echo "Data updated succesfully.";

        header('Location:addcountry.php');

    } else {

        echo "Data not updated succesfully.";

    }

}



if (isset($_GET['country_delete'])) {

    $id = $_GET['country_delete'];

    $deleteQry = "delete from country where country_id=$id";

    $deleteResult = mysqli_query($conn, $deleteQry);

    header("location: addcountry.php");

}



if (isset($_POST['roleSave'])) {

    $name = $_POST['name'];

    

    if(isset($_REQUEST['id']))

    {

        $id=$_REQUEST['id'];

        mysqli_query($conn, "UPDATE `user_type`  SET `name`= '$name' WHERE id='$id'");

    }else

    {

        mysqli_query($conn, "INSERT INTO `user_type`(`name`) VALUES ('$name')");

    }   

    

    header('Location:all_user_roles.php');

}



if (isset($_POST['roleAccessSave'])) {
    $roleId=(int)($_POST['utype']??0);
    header('Location:role_permissions.php'.($roleId?'?role_id='.$roleId:''));
    exit;

}







