<?php require_once __DIR__ . '/../security.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
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

    $mobile1 = substr(preg_replace('/\D+/', '', mysqli_real_escape_string($conn, $_POST['mobile1'])), 0, 10);

    //$mobile2 = $_POST['mobile2'];

    $mobile2 = substr(preg_replace('/\D+/', '', mysqli_real_escape_string($conn, $_POST['mobile2'])), 0, 10);

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

    if (strlen($mobile1) !== 10) {
        $mobile1Error = "Number must be 10 digit";
        $errors = 1;
    }

    if (strlen($mobile2) !== 10) {
        $mobile2Error = "Number must be 10 digit";
        $errors = 1;
    }





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

        $qry = "INSERT INTO employeestbl (employeeCode, designation, name, birthdate, employeeUname, employeeUpass, companyEmail, personalEmail, mobile1, mobile2, skypeUname, joiningDate, salary, bankName, bankIFSCno, bankAcHolderName, bankAcNo, address, status) VALUES ('$employeeCode', '$designation', '$name', '$birthdate', '$employeeUname', '$employeeUpass', '$companyEmail', '$personalEmail', '$mobile1', '$mobile2', '$skypeUname', '$joiningDate', '$salary', '$bankName', '$bankIFSCno', '$bankAcHolderName', '$bankAcNo', '$address', '$status')";

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
    oecrm_require_csrf();
    if (!oecrm_can($conn,'employees','create') && !oecrm_can($conn,'employees','edit')) {
        $_SESSION['designation_flash'] = 'You do not have permission to add designations.';
        header('Location:manageDesignation.php');
        exit;
    }
    $designation = trim($_POST['designation'] ?? '');
    if ($designation === '') {
        $_SESSION['designation_flash'] = 'Designation is required.';
        header('Location:manageDesignation.php');
        exit;
    }
    $check = mysqli_prepare($conn,'SELECT id FROM designation WHERE LOWER(designation)=LOWER(?) LIMIT 1');
    mysqli_stmt_bind_param($check,'s',$designation);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);
    if ($exists) {
        $_SESSION['designation_flash'] = 'Designation already exists.';
        header('Location:manageDesignation.php');
        exit;
    }
    $stmt = mysqli_prepare($conn,'INSERT INTO designation (designation) VALUES (?)');
    mysqli_stmt_bind_param($stmt,'s',$designation);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location:manageDesignation.php');
    exit;

}

?>



<?php

if (isset($_POST['addProject'])) {

    $developers = $_POST['developers'] ?? $_POST['team'] ?? [];
    if (!is_array($developers)) {
        $developers = [$developers];
    }

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

    $phone = substr(preg_replace('/\D+/', '', $_POST['phone']), 0, 10);

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



    $insertProject = "insert into projectstbl (projectName, developerId, description, attachment, startdate, enddate, amount, expence, customerName,nickName,projectType,platform, phone, email, location, country, reference, status) values ('$projectName', '$serializedDevelopers', '$description', '$attachment', '$startdate', '$enddate', '$amount', '$expence', '$customerName','$nickName','$projectType','$platform', '$phone', '$email', '$location', '$country', '$reference','$status')";



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

        $developersInput = $_POST['developers'] ?? $_POST['team'] ?? [];
        if (!is_array($developersInput)) {
            $developersInput = [$developersInput];
        }
        $developers = json_encode($developersInput);

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

        $phone = substr(preg_replace('/\D+/', '', $_POST['phone']), 0, 10);

        $email = $_POST['email'];

        $location = $_POST['location'];

        $country = $_POST['country'];

        $reference = $_POST['reference'];

        $status = $_POST['status'];

        if (strlen($phone) !== 10) {
            echo "Phone number must be exactly 10 digits.";
            exit;
        }



        $editProject = "UPDATE projectstbl SET projectName='$projectName', developerId='$developers', description='$description', attachment='$attachment',startdate='$startdate', enddate='$enddate', amount='$amount', expence='$expence', customerName='$customerName',platform='$platform',nickName='$nickName',projectType='$projectType', phone='$phone', email='$email', location='$location', country='$country', reference='$reference',status='$status' WHERE id=" . $id;

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

    $mobile1 = substr(preg_replace('/\D+/', '', mysqli_real_escape_string($conn, $_POST['mobile1'])), 0, 10);

    //$mobile2 = $_POST['mobile2'];

    $mobile2 = substr(preg_replace('/\D+/', '', mysqli_real_escape_string($conn, $_POST['mobile2'])), 0, 10);

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

    if (strlen($mobile1) !== 10) {
        $mobile1Error = "Number must be 10 digit";
        $errors = 1;
    }
    if (strlen($mobile2) !== 10) {
        $mobile2Error = "Number must be 10 digit";
        $errors = 1;
    }



     $qry = "UPDATE employeestbl SET employeeCode='$employeeCode', designation='$designation', name='$name', birthdate='$birthdate',   employeeUname='$employeeUname', employeeUpass='$employeePassword', companyEmail='$companyEmail', personalEmail='$personalEmail', mobile1='$mobile1', mobile2='$mobile2', skypeUname='$skypeUname', joiningDate='$joiningDate', salary='$salary', bankName='$bankName', bankIFSCno='$bankIFSCno', bankAcHolderName='$bankAcHolderName', bankAcNo='$bankAcNo', address='$address' WHERE id=" . $id;



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

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS currency_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    symbol VARCHAR(20) NOT NULL,
    rate DECIMAL(14,6) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

if (isset($_POST['addCurrency'])) {
    oecrm_require_csrf();
    $name = trim($_POST['name'] ?? '');
    $symbol = trim($_POST['symbol'] ?? '');
    $rate = (float) ($_POST['rate'] ?? 0);
    if ($name === '' || $symbol === '' || $rate <= 0) {
        $_SESSION['currency_flash'] = 'Please fill all currency fields.';
        header('Location:manageCurrency.php');
        exit;
    }
    $duplicate = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM currency_master WHERE LOWER(name)=LOWER('" . mysqli_real_escape_string($conn, $name) . "') OR UPPER(symbol)=UPPER('" . mysqli_real_escape_string($conn, $symbol) . "') LIMIT 1"));
    if ($duplicate) {
        $_SESSION['currency_flash'] = 'Currency already exists.';
        header('Location:manageCurrency.php');
        exit;
    }
    $nameEsc = mysqli_real_escape_string($conn, $name);
    $symbolEsc = mysqli_real_escape_string($conn, $symbol);
    $qry = "INSERT INTO currency_master(name, symbol, rate) VALUES ('$nameEsc','$symbolEsc',$rate)";
    if (mysqli_query($conn, $qry)) {
        $_SESSION['currency_flash'] = 'Currency added successfully.';
    } else {
        $_SESSION['currency_flash'] = 'Currency could not be added.';
    }
    header('Location:manageCurrency.php');
    exit;
}

if (isset($_POST['updateCurrency'])) {
    oecrm_require_csrf();
    $name = trim($_POST['name'] ?? '');
    $symbol = trim($_POST['symbol'] ?? '');
    $rate = (float) ($_POST['rate'] ?? 0);
    $id = (int) ($_POST['id'] ?? 0);
    if (!$id || $name === '' || $symbol === '' || $rate <= 0) {
        $_SESSION['currency_flash'] = 'Please fill all currency fields.';
        header('Location:manageCurrency.php');
        exit;
    }
    $nameEsc = mysqli_real_escape_string($conn, $name);
    $symbolEsc = mysqli_real_escape_string($conn, $symbol);
    $duplicate = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM currency_master WHERE id <> $id AND (LOWER(name)=LOWER('$nameEsc') OR UPPER(symbol)=UPPER('$symbolEsc')) LIMIT 1"));
    if ($duplicate) {
        $_SESSION['currency_flash'] = 'Currency already exists.';
        header('Location:manageCurrency.php');
        exit;
    }
    $qry = "UPDATE currency_master SET name='$nameEsc', symbol='$symbolEsc', rate=$rate WHERE id=$id";
    if (mysqli_query($conn, $qry)) {
        $_SESSION['currency_flash'] = 'Currency updated successfully.';
    } else {
        $_SESSION['currency_flash'] = 'Currency could not be updated.';
    }
    header('Location:manageCurrency.php');
    exit;
}



if (isset($_POST['addLeaveType'])) {
    oecrm_require_csrf();
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $_SESSION['leave_type_flash'] = 'Leave type is required.';
        header('Location:manageLeaveType.php');
        exit;
    }

    $check = mysqli_prepare($conn, 'SELECT id FROM leavetypetbl WHERE LOWER(name)=LOWER(?) LIMIT 1');
    mysqli_stmt_bind_param($check, 's', $name);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);
    if ($exists) {
        $_SESSION['leave_type_flash'] = 'Leave type already exists.';
        header('Location:manageLeaveType.php');
        exit;
    }

    $stmt = mysqli_prepare($conn, 'INSERT INTO leavetypetbl(name) VALUES (?)');
    mysqli_stmt_bind_param($stmt, 's', $name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['leave_type_flash'] = 'Leave type added successfully.';
    header('Location:manageLeaveType.php');
    exit;

}

if (isset($_POST['addLeadSource'])) {

    oecrm_require_csrf();
    // Allow users who can view leads via legacy access to add lead sources.
    if (!oecrm_can($conn,'clients','create') && !oecrm_can($conn,'clients','edit') && check_is_access_new('view_lead')!==1) {
        $_SESSION['lead_source_flash'] = 'You do not have permission to add lead sources.';
        header('Location:manageLeadSource.php');
        exit;
    }
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $_SESSION['lead_source_flash'] = 'Lead source name is required.';
        header('Location:manageLeadSource.php');
        exit;
    }

    $name = mysqli_real_escape_string($conn, $name);

    $check = mysqli_prepare($conn, 'SELECT id FROM lead_source_tbl WHERE LOWER(name)=LOWER(?) LIMIT 1');
    mysqli_stmt_bind_param($check, 's', $name);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);
    if ($exists) {
        $_SESSION['lead_source_flash'] = 'Lead source already exists.';
        header('Location:manageLeadSource.php');
        exit;
    }
    $qry = "INSERT INTO `lead_source_tbl`(`id`, `name`) VALUES (NULL,'$name')";

    if (mysqli_query($conn, $qry)) {
        header('Location:manageLeadSource.php');
        exit;

    } else {
        $_SESSION['lead_source_flash'] = 'Lead source could not be saved.';
        header('Location:manageLeadSource.php');
        exit;

    }

}



if (isset($_POST['addFollowupType'])) {
    oecrm_require_csrf();
    // Allow users who can view leads via legacy access to add follow-up types.
    if (!oecrm_can($conn,'clients','create') && !oecrm_can($conn,'clients','edit') && check_is_access_new('view_lead')!==1) {
        $_SESSION['followup_type_flash'] = 'You do not have permission to add follow-up types.';
        header('Location:manageFollowupType.php');
        exit;
    }
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $_SESSION['followup_type_flash'] = 'Follow-up type is required.';
        header('Location:manageFollowupType.php');
        exit;
    }
    $check = mysqli_prepare($conn, 'SELECT id FROM followup_type_tbl WHERE name=? LIMIT 1');
    mysqli_stmt_bind_param($check, 's', $name);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);
    if ($exists) {
        $_SESSION['followup_type_flash'] = 'Follow-up type already exists.';
        header('Location:manageFollowupType.php');
        exit;
    }

    $name = mysqli_real_escape_string($conn, $name);

    $qry = "INSERT INTO `followup_type_tbl`(`id`, `name`) VALUES (NULL,'$name')";

    if (mysqli_query($conn, $qry)) {
        header('Location:manageFollowupType.php');
        exit;

    } else {
        $_SESSION['followup_type_flash'] = 'Follow-up type could not be saved.';
        header('Location:manageFollowupType.php');
        exit;

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

    $id = (int)($_POST['id'] ?? 0);

    $status = (int)($_POST['status'] ?? 0);

    $remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');

    mysqli_query($conn, "UPDATE leave_master SET is_approved='$status',remarks='$remarks' where id=$id");

    header('Location:manageLeave.php');
    exit;

}





if (isset($_POST['leadSave'])) {
    oecrm_require_permission($conn,'clients','create');
    $companyId=oecrm_current_company_id($conn);$actor=(int)$_SESSION['adminId'];
    $leadDate=$_POST['leadDate']??'';$executiveName=trim($_POST['executiveName']??'');$company=trim($_POST['company']??'');$cperson=trim($_POST['cperson']??'');$mobileno1=trim($_POST['mobileno1']??'');$mobileno2=trim($_POST['mobileno2']??'');$emailid=trim($_POST['emailid']??'');$emailid2=trim($_POST['emailid2']??'');$city=trim($_POST['city']??'');$address=trim($_POST['address']??'');$leadType=$_POST['leadType']??'medium';$nick_name=trim($_POST['nick_name']??'');$status=$_POST['status']??'pending';$leadSource=(int)($_POST['lead_source']??0);$followupType=$_POST['followupType']??'Call';$remarks=trim($_POST['remarks']??'');$nextFollowupDate=$_POST['nextFollowupDate']?:null;$nextFollowupTime=$_POST['nextFollowupTime']?:null;
    $mobileno1 = substr(preg_replace('/\D+/', '', $mobileno1), 0, 10);
    $mobileno2 = substr(preg_replace('/\D+/', '', $mobileno2), 0, 10);
    if ($executiveName === '' || $company === '' || $cperson === '' || $leadSource <= 0 || !strtotime($leadDate)) { $_SESSION['lead_flash'] = 'Lead date, client name, company, contact person and lead source are required.'; header('Location:add_lead.php'); exit; }
    if (strlen($mobileno1) !== 10 || strlen($mobileno2) !== 10) { $_SESSION['lead_flash'] = 'Both mobile numbers must contain exactly 10 digits.'; header('Location:add_lead.php'); exit; }
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
    if($nextFollowupDate && $nextFollowupDate < date('Y-m-d')){$_SESSION['lead_flash']='Next follow-up date cannot be in the past.';header("Location:lead_details.php?leadId=$lead_id");exit;}
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
    oecrm_require_csrf();
    $bank_name = trim($_POST['bank_name'] ?? '');
    if ($bank_name === '') {
        $_SESSION['bank_details_flash'] = 'Bank name is required.';
        header('Location:addBankDetails.php');
        exit;
    }
    $bank_nameEsc = mysqli_real_escape_string($conn, $bank_name);
    $qry = "INSERT INTO bank_details(bank_name) VALUES ('$bank_nameEsc')";
    if (mysqli_query($conn, $qry)) {
        $_SESSION['bank_details_flash'] = 'Bank added successfully.';
    } else {
        $_SESSION['bank_details_flash'] = 'Bank could not be added.';
    }
    header('Location:addBankDetails.php');
    exit;
}



if (isset($_POST['updateBankDetails'])) {
    oecrm_require_csrf();
    $bank_name = trim($_POST['bank_name'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);
    if (!$id || $bank_name === '') {
        $_SESSION['bank_details_flash'] = 'Bank name is required.';
        header('Location:addBankDetails.php');
        exit;
    }
    $bank_nameEsc = mysqli_real_escape_string($conn, $bank_name);
    $qry = "UPDATE bank_details SET bank_name='$bank_nameEsc' where bank_id=$id";
    if (mysqli_query($conn, $qry)) {
        $_SESSION['bank_details_flash'] = 'Bank updated successfully.';
    } else {
        $_SESSION['bank_details_flash'] = 'Bank could not be updated.';
    }
    header('Location:addBankDetails.php');
    exit;
}



if (isset($_POST['deleteBankDetails'])) {
    oecrm_require_csrf();
    oecrm_require_permission($conn, 'finance', 'delete');
    $id = (int)($_POST['deleteBankDetails'] ?? 0);
    $row = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM bank_details WHERE bank_id=' . (int) $id));
    if (!$row) {
        http_response_code(404);
        exit('Bank record not found.');
    }
    $deleteQry = "delete from bank_details where bank_id=$id";
    mysqli_query($conn, $deleteQry);
    $_SESSION['bank_details_flash'] = 'Bank deleted successfully.';
    header("location: addBankDetails.php");
    exit;
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
    oecrm_require_csrf();
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $_SESSION['role_flash'] = 'Role name is required.';
        header('Location:all_user_roles.php');
        exit;
    }
    $id = (int)($_POST['id'] ?? 0);
    $check = mysqli_prepare($conn, 'SELECT id FROM user_type WHERE LOWER(name)=LOWER(?) AND is_deleted=0 AND id<>? LIMIT 1');
    mysqli_stmt_bind_param($check, 'si', $name, $id);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);
    if ($exists) {
        $_SESSION['role_flash'] = 'Role already exists.';
        header('Location:all_user_roles.php');
        exit;
    }
    if ($id > 0) {
        $stmt = mysqli_prepare($conn, 'UPDATE user_type SET name=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'si', $name, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['role_flash'] = 'Role updated successfully.';
    } else {
        $stmt = mysqli_prepare($conn, 'INSERT INTO user_type(name) VALUES (?)');
        mysqli_stmt_bind_param($stmt, 's', $name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['role_flash'] = 'Role added successfully.';
    }
    header('Location:all_user_roles.php');
    exit;
}



if (isset($_POST['roleAccessSave'])) {
    $roleId=(int)($_POST['utype']??0);
    header('Location:role_permissions.php'.($roleId?'?role_id='.$roleId:''));
    exit;

}







