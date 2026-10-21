<?php
$editingDeposit = !empty($deposit['deposit_id']);
$initialInvoices = !empty($deposit['project_Id']) ? oecrm_collection_invoice_options($conn, $collectionCompanyId, (int) $deposit['project_Id'], (int) ($deposit['deposit_id'] ?? 0)) : [];
?>
<form method="post" class="collection-form" id="collection-form">
    <?php echo oecrm_csrf_field(); ?>
    <input type="hidden" name="id" value="<?php echo (int) ($deposit['deposit_id'] ?? 0); ?>">
    <div class="row">
        <div class="col-md-6"><div class="form-group"><label for="collection-date">Deposit Date</label>
            <input type="date" id="collection-date" name="deposit_date" class="form-control" value="<?php echo oecrm_h($deposit['deposit_date'] ?? date('Y-m-d')); ?>" required>
        </div></div>
        <div class="col-md-6"><div class="form-group"><label for="collection-project">Project</label>
            <select id="collection-project" name="project_Id" class="form-control" required>
                <option value="">Select Project</option>
                <?php while ($project = mysqli_fetch_assoc($collectionProjects)): ?>
                    <option value="<?php echo (int) $project['id']; ?>" <?php echo (int) ($deposit['project_Id'] ?? 0) === (int) $project['id'] ? 'selected' : ''; ?>><?php echo oecrm_h($project['projectName']); ?></option>
                <?php endwhile; ?>
            </select>
        </div></div>
    </div>
    <div class="row">
        <div class="col-md-6"><div class="form-group"><label for="collection-invoice">Invoice</label>
            <select id="collection-invoice" name="invoice_id" class="form-control" required><option value="">Select Project First</option></select>
        </div></div>
        <div class="col-md-6"><div class="form-group"><label for="collection-account">Receiving Bank Account</label>
            <select id="collection-account" name="account_Id" class="form-control" required>
                <option value="">Select Receiving Account</option>
                <?php while ($account = mysqli_fetch_assoc($collectionAccounts)): ?>
                    <option data-balance="<?php echo oecrm_h($account['current_balance']); ?>" data-account-id="<?php echo (int) $account['account_id']; ?>" value="bank:<?php echo (int) $account['bank_id']; ?>" <?php echo !empty($account['account_id']) && (int) ($deposit['account_Id'] ?? 0) === (int) $account['account_id'] ? 'selected' : ''; ?>><?php echo oecrm_h($account['account_name']) . ' - Balance: ' . number_format((float) $account['current_balance'], 2); ?></option>
                <?php endwhile; ?>
            </select>
<p class="help-block" id="collection-bank-balance" aria-live="polite"></p>
            <p class="help-block" id="collection-bank-hint">Select the bank account that receives this payment.</p>
        </div></div>
    </div>
    <div class="row">
        <div class="col-md-4"><div class="form-group"><label for="collection-client">Invoice Client</label><input id="collection-client" class="form-control" readonly></div></div>
        <div class="col-md-4"><div class="form-group"><label for="collection-invoice-date">Invoice Date</label><input id="collection-invoice-date" type="date" class="form-control" readonly></div></div>
        <div class="col-md-4"><div class="form-group"><label for="collection-amount">Deposit Amount (Invoice Total)</label><input id="collection-amount" name="amount" class="form-control" readonly required><p class="help-block">Includes invoice GST, where applicable.</p></div></div>
    </div>
    <p id="collection-message" class="help-block" role="status"></p>
    <div class="form-group"><label for="collection-remark">Remark</label><textarea id="collection-remark" name="description" class="form-control" rows="3"><?php echo oecrm_h($deposit['description'] ?? ''); ?></textarea></div>
    <button class="btn btn-primary" type="submit" name="<?php echo $editingDeposit ? 'updateDeposit' : 'addDeposit'; ?>" value="1" id="collection-save"><?php echo $editingDeposit ? 'Update Deposit' : 'Add Deposit'; ?></button>
    <a class="btn btn-default" href="addDeposit.php">Cancel</a>
</form>
<script type="application/json" id="collection-initial"><?php echo json_encode(['invoices' => $initialInvoices, 'invoice_id' => (int) ($deposit['invoice_id'] ?? 0), 'original_account_id' => (int) ($deposit['account_Id'] ?? 0), 'original_amount' => (string) ($deposit['amount'] ?? '0')], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<script src="../js/oecrm-collections.js?v=20261021-2"></script>