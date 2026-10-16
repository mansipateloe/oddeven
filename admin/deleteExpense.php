<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../expenseBanks.php';

oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}
oecrm_require_csrf();
oecrm_require_permission($conn, 'finance', 'delete');

try {
    $id = oecrm_int_param($_POST, 'expense_id');
    $expense = oecrm_delete_bank_expense($conn, $id);
    try {
        oecrm_audit($conn, 'finance', 'delete', 'expense', $id, 'Expense deleted', $expense, null);
    } catch (Throwable $auditError) {
        error_log('Expense deletion audit failed: ' . $auditError->getMessage());
    }
    $_SESSION['expense_flash'] = !empty($expense['bank_id'])
        ? 'Expense deleted successfully. The bank balance has been restored.'
        : 'Expense deleted successfully.';
    $_SESSION['expense_flash_type'] = 'success';
} catch (InvalidArgumentException $exception) {
    $_SESSION['expense_flash'] = $exception->getMessage();
    $_SESSION['expense_flash_type'] = 'warning';
} catch (Throwable $exception) {
    error_log('Expense deletion failed: ' . $exception->getMessage());
    $_SESSION['expense_flash'] = 'Expense could not be deleted. No bank balance was changed.';
    $_SESSION['expense_flash_type'] = 'danger';
}
header('Location: viewexpense.php');
exit;
