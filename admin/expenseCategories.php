<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
if (!oecrm_can($conn, 'finance', 'create') && !oecrm_legacy_can($conn, 'expense')) {
    http_response_code(403);
    exit('You do not have permission to add expense categories.');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}
oecrm_require_csrf();

$returnTo = ($_POST['return_to'] ?? '') === 'expenseEditor.php' ? 'expenseEditor.php' : 'viewexpense.php';
$name = trim((string) ($_POST['category_name'] ?? ''));
if ($name === '' || strlen($name) > 255) {
    $_SESSION['expense_flash'] = 'Enter an expense category name up to 255 characters.';
    $_SESSION['expense_flash_type'] = 'warning';
    header('Location: ' . $returnTo);
    exit;
}

try {
    $stmt = mysqli_prepare($conn, 'SELECT category_id FROM expencecategory WHERE expenseCategory=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $name);
    mysqli_stmt_execute($stmt);
    $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if ($existing) {
        $_SESSION['new_expense_category_id'] = (int) $existing['category_id'];
        $_SESSION['new_expense_category_name'] = $name;
        $_SESSION['expense_flash'] = 'That expense category already exists and has been selected.';
        $_SESSION['expense_flash_type'] = 'warning';
    } else {
        $stmt = mysqli_prepare($conn, 'INSERT INTO expencecategory (expenseCategory) VALUES (?)');
        mysqli_stmt_bind_param($stmt, 's', $name);
        mysqli_stmt_execute($stmt);
        $_SESSION['new_expense_category_id'] = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        $_SESSION['new_expense_category_name'] = $name;
        $_SESSION['expense_flash'] = 'Expense category added. It is selected in the expense form.';
        $_SESSION['expense_flash_type'] = 'success';
    }
} catch (Throwable $exception) {
    error_log('Expense category creation failed: ' . $exception->getMessage());
    $_SESSION['expense_flash'] = 'Expense category could not be added. Please try again.';
    $_SESSION['expense_flash_type'] = 'danger';
}

header('Location: ' . $returnTo);
exit;
