<?php
include '../dbconnect.php';
require_once __DIR__ . '/../../security.php';
oecrm_require_admin_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['delete'])) {
    oecrm_require_csrf();
}

if (isset($_POST['add_project_expense'])) {
    $expensedate = $_POST['expensedate'] ?? '';
    $project_Id = (int)($_POST['project_Id'] ?? 0);
    $expense_title = $_POST['expense_title'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $remark = $_POST['remark'] ?? '';
    $account_Id = (int)($_POST['account_Id'] ?? 0);

    $stmt = mysqli_prepare($conn, "INSERT INTO project_expenses (expensedate, project_Id, expense_title, amount, account_Id, remark) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sissis', $expensedate, $project_Id, $expense_title, $amount, $account_Id, $remark);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location:../projectExpense.php');
    exit;
} elseif (isset($_POST['update_project_expense'])) {
    $id = oecrm_int_param($_POST, 'id');
    $expensedate = $_POST['expensedate'] ?? '';
    $project_Id = (int)($_POST['project_Id'] ?? 0);
    $expense_title = $_POST['expense_title'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $remark = $_POST['remark'] ?? '';
    $account_Id = (int)($_POST['account_Id'] ?? 0);

    $stmt = mysqli_prepare($conn, "UPDATE project_expenses SET expensedate=?, project_Id=?, expense_title=?, amount=?, account_Id=?, remark=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sissisi', $expensedate, $project_Id, $expense_title, $amount, $account_Id, $remark, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location:../projectExpense.php?edit=' . $id);
    exit;
} elseif (isset($_GET['delete'])) {
    $id = oecrm_int_param($_GET, 'delete');
    $stmt = mysqli_prepare($conn, "DELETE FROM project_expenses WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location:../projectExpense.php');
    exit;
}
?>
