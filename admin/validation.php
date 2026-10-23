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



if (isset($_POST['addDeposit']) || isset($_POST['updateDeposit'])) {
    $isEdit = isset($_POST['updateDeposit']);
    oecrm_require_permission($conn, 'finance', $isEdit ? 'edit' : 'create');
    require_once __DIR__ . '/../depositCollections.php';
    $depositId = $isEdit ? (int) ($_POST['id'] ?? 0) : 0;
    try {
        if ($isEdit && $depositId <= 0) throw new InvalidArgumentException('Invalid deposit selected.');
        $saved = oecrm_save_collection($conn, oecrm_current_company_id($conn), $_POST, $depositId);
        try {
        oecrm_audit($conn, 'finance', $isEdit ? 'edit' : 'create', 'deposit', $saved['id'], 'Invoice collection saved', $saved['old'], ['invoice_no' => $saved['invoice_no'], 'amount' => $saved['amount'], 'account_id' => (int) $_POST['account_Id']]);
        } catch (Throwable $auditError) {
            error_log('Collection audit failed: ' . $auditError->getMessage());
        }
        $_SESSION['deposit_flash'] = ['type' => 'success', 'message' => 'Deposit saved for invoice ' . $saved['invoice_no'] . '. Bank balance updated.'];
        $redirect = 'addDeposit.php';
    } catch (Throwable $e) {
        error_log('Collection save failed: ' . $e->getMessage());
        $_SESSION['deposit_flash'] = ['type' => 'danger', 'message' => $e instanceof InvalidArgumentException ? $e->getMessage() : 'Deposit could not be saved. No balance was changed.'];
        $redirect = $isEdit && $depositId > 0 ? 'editDeposit.php?edit=' . $depositId : 'addDeposit.php';
    }
    header('Location:' . $redirect);
    exit;
}
?>

<?php

if (isset($_POST['addExpense']) || isset($_POST['updateExpense'])) {
    $isEdit = isset($_POST['updateExpense']);
    if (!oecrm_can($conn, 'finance', $isEdit ? 'edit' : 'create') && !oecrm_legacy_can($conn, 'expense')) {
        http_response_code(403);
        exit('You do not have permission to manage expenses.');
    }
    require_once __DIR__ . '/../expenseBanks.php';
    oecrm_expense_bank_schema($conn);
    $id = $isEdit ? (int) ($_POST['id'] ?? $_GET['edit'] ?? 0) : 0;
    $redirect = $isEdit && $id > 0 ? 'editExpense.php?edit=' . $id : 'viewexpense.php';
    try {
        if ($isEdit && $id <= 0) {
            throw new InvalidArgumentException('Invalid expense selected.');
        }
        oecrm_save_bank_expense(
            $conn,
            $id,
            (int) ($_POST['expenseCategory'] ?? $_POST['expenceCategory'] ?? $_POST['expencecategory'] ?? 0),
            trim((string) ($_POST['expensedate'] ?? '')),
            trim((string) ($_POST['amount'] ?? '')),
            trim((string) ($_POST['description'] ?? '')),
            (int) ($_POST['bank_id'] ?? 0)
        );
        $_SESSION['expense_flash'] = 'Expense saved. The selected bank balance has been updated.';
        $redirect = 'viewexpense.php';
    } catch (InvalidArgumentException $e) {
        $_SESSION['expense_flash'] = $e->getMessage();
    } catch (Throwable $e) {
        error_log('Bank expense save failed: ' . $e->getMessage());
        $_SESSION['expense_flash'] = 'Expense could not be saved. No bank balance was changed.';
    }
    header('Location:' . $redirect);
    exit;
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

if (isset($_POST['addLeadSource']) || isset($_POST['updateLeadSource'])) {
    oecrm_require_csrf();
    $editing = isset($_POST['updateLeadSource']);
    $sourceId = oecrm_int_param($_POST, 'leadSourceId');
    $message = '';
    $type = 'error';
    if (
        !(!$editing && oecrm_can($conn, 'clients', 'create'))
        && !oecrm_can($conn, 'clients', 'edit')
        && !oecrm_legacy_can($conn, 'add_lead')
        && !oecrm_legacy_can($conn, 'view_lead')
    ) {
        $message = 'You do not have permission to ' . ($editing ? 'edit' : 'add') . ' lead sources.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $sourceExists = true;
        if ($editing) {
            $lookup = mysqli_prepare($conn, 'SELECT id FROM lead_source_tbl WHERE id=? LIMIT 1');
            mysqli_stmt_bind_param($lookup, 'i', $sourceId);
            mysqli_stmt_execute($lookup);
            $sourceExists = (bool) mysqli_fetch_assoc(mysqli_stmt_get_result($lookup));
            mysqli_stmt_close($lookup);
        }
        if (!$sourceExists) {
            $message = 'Lead source not found.';
        } elseif ($name === '') {
            $message = 'Lead source name is required.';
        } else {
            $excludedId = $editing ? $sourceId : 0;
            $check = mysqli_prepare($conn, 'SELECT id FROM lead_source_tbl WHERE LOWER(TRIM(name))=LOWER(?) AND id<>? LIMIT 1');
            mysqli_stmt_bind_param($check, 'si', $name, $excludedId);
            mysqli_stmt_execute($check);
            $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
            mysqli_stmt_close($check);
            if ($exists) {
                $message = 'Lead source already exists.';
            } else {
                $insert = mysqli_prepare($conn, $editing ? 'UPDATE lead_source_tbl SET name=? WHERE id=?' : 'INSERT INTO lead_source_tbl (name) VALUES (?)');
                if ($editing) {
                    mysqli_stmt_bind_param($insert, 'si', $name, $sourceId);
                } else {
                    mysqli_stmt_bind_param($insert, 's', $name);
                }
                $saved = mysqli_stmt_execute($insert);
                mysqli_stmt_close($insert);
                $message = $saved ? 'Lead source ' . ($editing ? 'updated' : 'added') . ' successfully.' : 'Lead source could not be saved.';
                $type = $saved ? 'success' : 'error';
            }
        }
    }
    $_SESSION['lead_source_flash'] = $message;
    $_SESSION['lead_source_flash_type'] = $type;
    header('Location:manageLeadSource.php');
    exit;
}

if (isset($_POST['addFollowupType'])) {
    oecrm_require_csrf();
    if (
        !oecrm_can($conn, 'clients', 'create')
        && !oecrm_can($conn, 'clients', 'edit')
        && !oecrm_legacy_can($conn, 'add_lead')
        && !oecrm_legacy_can($conn, 'view_lead')
    ) {
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





if (!function_exists('oecrm_lead_redirect_error')) {
    function oecrm_lead_redirect_error($message)
    {
        $_SESSION['lead_flash'] = $message;
        $_SESSION['lead_flash_type'] = 'danger';
        $_SESSION['lead_form'] = [
            'leadDate' => $_POST['leadDate'] ?? '',
            'executiveName' => $_POST['executiveName'] ?? '',
            'nick_name' => $_POST['nick_name'] ?? '',
            'company' => $_POST['company'] ?? '',
            'cperson' => $_POST['cperson'] ?? '',
            'mobileno1' => $_POST['mobileno1'] ?? '',
            'mobileno2' => $_POST['mobileno2'] ?? '',
            'emailid' => $_POST['emailid'] ?? '',
            'emailid2' => $_POST['emailid2'] ?? '',
            'city' => $_POST['city'] ?? '',
            'lead_source' => $_POST['lead_source'] ?? '',
            'leadType' => $_POST['leadType'] ?? 'medium',
            'address' => $_POST['address'] ?? '',
            'followupType' => $_POST['followupType'] ?? 'Call',
            'nextFollowupDate' => $_POST['nextFollowupDate'] ?? '',
            'nextFollowupTime' => $_POST['nextFollowupTime'] ?? '',
            'status' => $_POST['status'] ?? '',
            'remarks' => $_POST['remarks'] ?? '',
        ];
        header('Location:add_lead.php');
        exit;
    }
}

if (isset($_POST['leadSave'])) {
    oecrm_require_csrf();
    oecrm_require_permission($conn, 'clients', 'create');
    $companyId = oecrm_current_company_id($conn);
    $actor = (int) $_SESSION['adminId'];

    $leadDate = trim((string) ($_POST['leadDate'] ?? ''));
    $executiveName = trim((string) ($_POST['executiveName'] ?? ''));
    $company = trim((string) ($_POST['company'] ?? ''));
    $cperson = trim((string) ($_POST['cperson'] ?? ''));
    $mobileno1 = substr(preg_replace('/\D+/', '', (string) ($_POST['mobileno1'] ?? '')), 0, 10);
    $mobileno2 = substr(preg_replace('/\D+/', '', (string) ($_POST['mobileno2'] ?? '')), 0, 10);
    $emailid = trim((string) ($_POST['emailid'] ?? ''));
    $emailid2 = trim((string) ($_POST['emailid2'] ?? ''));
    $city = trim((string) ($_POST['city'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));
    $leadType = $_POST['leadType'] ?? 'medium';
    $nick_name = trim((string) ($_POST['nick_name'] ?? ''));
    $status = trim((string) ($_POST['status'] ?? ''));
    $leadSource = (int) ($_POST['lead_source'] ?? 0);
    $followupType = $_POST['followupType'] ?? 'Call';
    $remarks = trim((string) ($_POST['remarks'] ?? ''));
    $nextFollowupDate = !empty($_POST['nextFollowupDate']) ? $_POST['nextFollowupDate'] : null;
    $nextFollowupTime = !empty($_POST['nextFollowupTime']) ? $_POST['nextFollowupTime'] : null;

    $allowedStatuses = ['pending', 'inprogress', 'completed', 'closed'];
    if ($status !== '' && !in_array($status, $allowedStatuses, true)) {
        oecrm_lead_redirect_error('Please select a valid status.');
    }
    if ($leadDate === '' || !strtotime($leadDate)) {
        oecrm_lead_redirect_error('Lead date is required.');
    }
    if ($executiveName === '') {
        oecrm_lead_redirect_error('Client name is required.');
    }
    if ($company === '') {
        oecrm_lead_redirect_error('Company name is required.');
    }
    if ($cperson === '') {
        oecrm_lead_redirect_error('Contact person is required.');
    }
    if ($mobileno1 === '' && $emailid === '') {
        oecrm_lead_redirect_error('Please enter Mobile No1 or Company Email ID.');
    }
    if ($leadSource <= 0) {
        oecrm_lead_redirect_error('Lead source is required.');
    }
    if ($status === '') {
        oecrm_lead_redirect_error('Status is required.');
    }
    if ($mobileno1 !== '' && strlen($mobileno1) !== 10) {
        oecrm_lead_redirect_error('Mobile No1 must contain exactly 10 digits when entered.');
    }
    if ($mobileno2 !== '' && strlen($mobileno2) !== 10) {
        oecrm_lead_redirect_error('Mobile No2 must contain exactly 10 digits when entered.');
    }
    if ($emailid !== '' && !filter_var($emailid, FILTER_VALIDATE_EMAIL)) {
        oecrm_lead_redirect_error('Please enter a valid company email address.');
    }
    if ($emailid2 !== '' && !filter_var($emailid2, FILTER_VALIDATE_EMAIL)) {
        oecrm_lead_redirect_error('Please enter a valid personal email address.');
    }
    if ($nextFollowupDate && $nextFollowupDate < date('Y-m-d')) {
        oecrm_lead_redirect_error('Next follow-up date cannot be in the past.');
    }

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, 'INSERT INTO leads(company_id,lead_date,executive_name,company_name,contact_person,mobile_no1,mobile_no2,email,personal_email,city,address,assign_to,is_active,created_at,created_by,lead_source,nick_name,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,NULL,1,NOW(),?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'issssssssssiiss', $companyId, $leadDate, $executiveName, $company, $cperson, $mobileno1, $mobileno2, $emailid, $emailid2, $city, $address, $actor, $leadSource, $nick_name, $status);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        if ($remarks !== '') {
            $stmt = mysqli_prepare($conn, 'INSERT INTO lead_followup(lead_id,lead_type,followup_type,remarks,next_followup_date,next_followup_time,created_at,created_by,status) VALUES(?,?,?,?,?,?,NOW(),?,"pending")');
            mysqli_stmt_bind_param($stmt, 'isssssi', $id, $leadType, $followupType, $remarks, $nextFollowupDate, $nextFollowupTime, $actor);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        mysqli_commit($conn);
        oecrm_audit($conn, 'clients', 'create', 'lead', $id, 'Lead created', null, ['company_name' => $company, 'status' => $status]);
        $_SESSION['lead_flash'] = 'Lead created successfully.';
        unset($_SESSION['lead_form'], $_SESSION['lead_flash_type']);
    } catch (Throwable $exception) {
        mysqli_rollback($conn);
        oecrm_lead_redirect_error($exception->getMessage());
    }
    header('Location:leads.php');
    exit;
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
    $_SESSION['bank_details_flash'] = [
        'type' => 'warning',
        'message' => 'Please use the delete button on the bank details page.',
    ];
    header('Location:addBankDetails.php');
    exit;
}
if (isset($_POST['addCountry'])) {
    oecrm_require_csrf();
    $country_code = trim((string) ($_POST['country_code'] ?? ''));
    $country_symbols = trim((string) ($_POST['country_symbols'] ?? ''));

    if ($country_code === '' || $country_symbols === '') {
        $_SESSION['country_flash'] = ['type' => 'danger', 'message' => 'Country code and symbol are required.'];
        header('Location:addcountry.php');
        exit;
    }

    $duplicateStmt = mysqli_prepare($conn, 'SELECT country_id FROM country WHERE LOWER(TRIM(country_code))=LOWER(?) OR TRIM(country_symbols)=? LIMIT 1');
    mysqli_stmt_bind_param($duplicateStmt, 'ss', $country_code, $country_symbols);
    mysqli_stmt_execute($duplicateStmt);
    $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($duplicateStmt));
    mysqli_stmt_close($duplicateStmt);

    if ($duplicate) {
        $_SESSION['country_flash'] = ['type' => 'warning', 'message' => 'Country code or symbol already exists.'];
        header('Location:addcountry.php');
        exit;
    }

    $stmt = mysqli_prepare($conn, 'INSERT INTO country(country_code, country_symbols) VALUES (?, ?)');
    mysqli_stmt_bind_param($stmt, 'ss', $country_code, $country_symbols);
    $saved = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION['country_flash'] = $saved
        ? ['type' => 'success', 'message' => 'Country details added successfully.']
        : ['type' => 'danger', 'message' => 'Country details could not be added.'];
    header('Location:addcountry.php');
    exit;
}



if (isset($_POST['updateCountry'])) {
    oecrm_require_csrf();
    $country_code = trim((string) ($_POST['country_code'] ?? ''));
    $country_symbols = trim((string) ($_POST['country_symbols'] ?? ''));
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0 || $country_code === '' || $country_symbols === '') {
        $_SESSION['country_flash'] = ['type' => 'danger', 'message' => 'Country code and symbol are required.'];
        header('Location:addcountry.php' . ($id > 0 ? '?edit=' . $id : ''));
        exit;
    }

    $duplicateStmt = mysqli_prepare($conn, 'SELECT country_id FROM country WHERE (LOWER(TRIM(country_code))=LOWER(?) OR TRIM(country_symbols)=?) AND country_id<>? LIMIT 1');
    mysqli_stmt_bind_param($duplicateStmt, 'ssi', $country_code, $country_symbols, $id);
    mysqli_stmt_execute($duplicateStmt);
    $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($duplicateStmt));
    mysqli_stmt_close($duplicateStmt);

    if ($duplicate) {
        $_SESSION['country_flash'] = ['type' => 'warning', 'message' => 'Country code or symbol already exists.'];
        header('Location:addcountry.php?edit=' . $id);
        exit;
    }

    $stmt = mysqli_prepare($conn, 'UPDATE country SET country_code=?, country_symbols=? WHERE country_id=?');
    mysqli_stmt_bind_param($stmt, 'ssi', $country_code, $country_symbols, $id);
    $saved = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION['country_flash'] = $saved
        ? ['type' => 'success', 'message' => 'Country details updated successfully.']
        : ['type' => 'danger', 'message' => 'Country details could not be updated.'];
    header('Location:addcountry.php');
    exit;
}



if (isset($_POST['deleteCountry'])) {
    oecrm_require_csrf();
    $id = (int) ($_POST['deleteCountry'] ?? 0);

    if ($id <= 0) {
        $_SESSION['country_flash'] = ['type' => 'danger', 'message' => 'Invalid country details selected.'];
        header('Location:addcountry.php');
        exit;
    }

    $stmt = mysqli_prepare($conn, 'DELETE FROM country WHERE country_id=?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $deleted = mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION['country_flash'] = ($deleted && $affected > 0)
        ? ['type' => 'success', 'message' => 'Country details deleted successfully.']
        : ['type' => 'warning', 'message' => 'Country details not found or already deleted.'];
    header('Location:addcountry.php');
    exit;
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









