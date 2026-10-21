<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../depositCollections.php';
oecrm_require_admin_login();
oecrm_require_permission($conn, 'finance', 'edit');
$collectionCompanyId = oecrm_current_company_id($conn);
try {
    $deposit = oecrm_collection_deposit($conn, $collectionCompanyId, (int) ($_GET['edit'] ?? 0));
} catch (InvalidArgumentException $e) {
    $_SESSION['deposit_flash'] = ['type' => 'danger', 'message' => $e->getMessage()];
    header('Location:addDeposit.php');
    exit;
}
include 'header.php';
$collectionProjects = mysqli_query($conn, 'SELECT id,projectName FROM projectstbl WHERE company_id=' . (int) $collectionCompanyId . ' ORDER BY projectName');
$collectionAccounts = oecrm_collection_bank_accounts($conn);
$flash = $_SESSION['deposit_flash'] ?? null;
unset($_SESSION['deposit_flash']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?><div class="alert alert-<?php echo oecrm_h($flash['type']); ?>"><?php echo oecrm_h($flash['message']); ?></div><?php endif; ?>
    <div class="panel panel-default"><div class="panel-heading">Edit Deposit</div>
        <div class="panel-body"><?php include 'depositForm.php'; ?></div>
    </div>
</div>
<?php include 'footer.php'; ?>