<?php
require_once __DIR__ . '/../security.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
}

if (isset($_POST['updateEmployee'])) {
    $employeeId = (int)$_SESSION['employeeId'];
    $name = $_POST['name'] ?? '';
    $employeeUpass = $_POST['employeeUpass'] ?? '';
    $oldEmployeeUpass = $_POST['oldEmployeeUpass'] ?? '';
    $personalEmail = $_POST['personalEmail'] ?? '';
    $mobile1 = $_POST['mobile1'] ?? '';
    $mobile2 = $_POST['mobile2'] ?? '';
    $skypeUname = $_POST['skypeUname'] ?? '';
    $bankName = $_POST['bankName'] ?? '';
    $bankIFSCno = $_POST['bankIFSCno'] ?? '';
    $bankAcHolderName = $_POST['bankAcHolderName'] ?? '';
    $bankAcNo = $_POST['bankAcNo'] ?? '';
    $address = $_POST['address'] ?? '';

    if ($employeeUpass === $oldEmployeeUpass) {
        $stmt = mysqli_prepare($conn, "UPDATE employeesTbl SET name=?, mobile1=?, personalEmail=?, mobile2=?, skypeUname=?, bankName=?, bankIFSCno=?, bankAcHolderName=?, bankAcNo=?, address=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssssssssssi', $name, $mobile1, $personalEmail, $mobile2, $skypeUname, $bankName, $bankIFSCno, $bankAcHolderName, $bankAcNo, $address, $employeeId);
    } else {
        $newEmployeeUpass = oecrm_password_hash($employeeUpass);
        $stmt = mysqli_prepare($conn, "UPDATE employeesTbl SET name=?, employeeUpass=?, personalEmail=?, mobile1=?, mobile2=?, skypeUname=?, bankName=?, bankIFSCno=?, bankAcHolderName=?, bankAcNo=?, address=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'sssssssssssi', $name, $newEmployeeUpass, $personalEmail, $mobile1, $mobile2, $skypeUname, $bankName, $bankIFSCno, $bankAcHolderName, $bankAcNo, $address, $employeeId);
    }
    if (mysqli_stmt_execute($stmt)) {
        header('Location:userinfo.php');
        exit;
    }
}

?>
