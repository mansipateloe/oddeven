<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../bankReconciliation.php';
oecrm_require_admin_login();
oecrm_require_csrf();
oecrm_require_permission($conn, 'bank_reconciliation', 'create');
try {
    $id = oecrm_save_current_bank_reconciliation($conn, oecrm_current_company_id($conn), (int) $_SESSION['adminId'], $_POST);
    oecrm_audit($conn, 'bank_reconciliation', 'create', 'reconciliation', $id, 'Bank reconciliation created');
} catch (Throwable $e) {
    error_log('Bank reconciliation failed: ' . $e->getMessage());
    $_SESSION['reconciliation_error'] = $e instanceof InvalidArgumentException ? $e->getMessage() : 'Reconciliation could not be saved. Please try again.';
}
header('Location: bankReconciliation.php');
exit;
