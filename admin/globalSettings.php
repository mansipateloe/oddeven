<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../invoiceNumbering.php';
oecrm_require_admin_login();
$canEdit = oecrm_is_super_admin() || oecrm_can($conn, 'settings', 'edit');
if (!$canEdit && !oecrm_can($conn, 'settings', 'view')) {
    http_response_code(403);
    exit('You do not have permission to view Global Settings.');
}
$companyId = oecrm_current_company_id($conn);
oecrm_invoice_numbering_schema($conn);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
    if (!$canEdit) {
        http_response_code(403);
        exit('You do not have permission to update Global Settings.');
    }
    try {
        if ((int) ($_POST['invoice_company_id'] ?? 0) !== $companyId) {
            throw new InvalidArgumentException('The active company changed. Reload this page before saving.');
        }
        $value = trim((string) ($_POST['last_invoice_number'] ?? ''));
        oecrm_invoice_sequence_save($conn, $companyId, $value);
        oecrm_audit($conn, 'settings', 'edit', 'company_invoice_sequences', $companyId, 'Invoice numbering updated', null, ['last_invoice_number' => $value]);
        $_SESSION['global_settings_flash'] = 'Invoice numbering saved for this company.';
        header('Location:globalSettings.php');
        exit;
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    } catch (Throwable $e) {
        error_log('Global invoice settings save failed: ' . $e->getMessage());
        $error = 'Settings could not be saved. Please try again.';
    }
}
$sequence = oecrm_invoice_sequence_preview($conn, $companyId);
$company = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT display_name FROM companies WHERE id=' . (int) $companyId));
$flash = $_SESSION['global_settings_flash'] ?? '';
unset($_SESSION['global_settings_flash']);
$active_menu = 'setting';
$active_submenu = 'global_settings';
include 'header.php';
?>
<div id="page-wrapper" class="compact-admin-page">
    <h3>Global Settings</h3>
    <div class="panel panel-default">
        <div class="panel-heading">Invoice Numbering — <?php echo oecrm_h($company['display_name']); ?></div>
        <div class="panel-body">
            <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
            <p>These settings apply to the active company shown in the menu. Switch the active company to configure another company.</p>
            <form method="post">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="invoice_company_id" value="<?php echo (int) $companyId; ?>">
                <div class="row"><div class="col-md-6">
                    <div class="form-group">
                        <label for="last_invoice_number">Last Used Invoice Number</label>
                        <input id="last_invoice_number" class="form-control" name="last_invoice_number" maxlength="50" value="<?php echo oecrm_h($_POST['last_invoice_number'] ?? $sequence['last_invoice_number']); ?>" placeholder="odd001" required <?php echo $canEdit ? '' : 'readonly'; ?>>
                        <p class="help-block">Include the prefix and digits. For example, enter odd001 to start the next invoice at odd002. Enter odd000 to start at odd001.</p>
                    </div>
                    <p><strong>Next Invoice Number:</strong> <?php echo oecrm_h($sequence['next_invoice_number']); ?></p>
                    <?php if ($canEdit): ?><button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save Settings</button><?php endif; ?>
                </div></div>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>