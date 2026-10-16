<?php
require_once __DIR__ . '/../invoiceBanks.php';
$invoiceBankOptions = oecrm_invoice_bank_options($conn);
$invoiceCurrencyOptions = '<option value="">Select currency</option>';
$currencies = mysqli_query($conn, 'SELECT country_id,country_symbols,country_code FROM country ORDER BY country_code');
while ($currency = mysqli_fetch_assoc($currencies)) {
    $invoiceCurrencyOptions .= '<option value="' . (int) $currency['country_id'] . '">' . oecrm_h($currency['country_symbols'] . ' ' . $currency['country_code']) . '</option>';
}
function oecrm_invoice_item_markup($bankOptions, $currencyOptions, $first = false)
{
    ?>
    <div class="perticulars invoice-item-row">
        <div class="form-group"><label>Particulars</label><input type="text" class="form-control" placeholder="Describe the service or item" name="perticulars[]" maxlength="255" required></div>
        <div class="form-group"><label>Bank Account</label><select name="bank_id[]" required class="form-control" data-search-placeholder="Search bank name, account ending or IFSC..." data-oecrm-dropdown="viewport"><?php echo $bankOptions; ?></select></div>
        <div class="form-group"><label>Currency</label><select name="country_id[]" required class="form-control" data-search-placeholder="Search currency..." data-oecrm-dropdown="viewport"><?php echo $currencyOptions; ?></select></div>
        <div class="form-group"><label>Amount</label><input type="number" class="form-control" min="0" max="999999999999.99" step="0.01" placeholder="0.00" name="amounts[]" required></div>
        <div class="invoice-item-action"><button type="button" <?php echo $first ? 'id="add_more"' : ''; ?> class="btn btn-sm <?php echo $first ? 'btn-success' : 'btn-danger remove'; ?>" aria-label="<?php echo $first ? 'Add invoice item' : 'Remove invoice item'; ?>"><i class="fa <?php echo $first ? 'fa-plus' : 'fa-minus'; ?>"></i> <?php echo $first ? 'Add item' : 'Remove'; ?></button></div>
    </div>
    <?php
}
?>
<style>
.admin-portal #perticulars_div { margin: 0 !important; }
.invoice-item-row { display: grid; grid-template-columns: minmax(180px,1.5fr) minmax(220px,1.5fr) minmax(130px,.8fr) minmax(130px,.8fr) auto; gap: 14px; align-items: end; padding: 18px; margin-bottom: 16px; border: 1px solid #e4e7ec; border-radius: 10px; background: #fafbfc; }
.invoice-item-row .form-group { min-width: 0; margin: 0; }
.invoice-item-row label { display: block; margin-bottom: 7px; }
.invoice-item-row .form-control, .invoice-item-row .oecrm-select-toggle { height: 40px; }
.invoice-item-row .invoice-item-action .btn { min-height: 40px; white-space: nowrap; }
.invoice-item-row .oecrm-select { width: 100%; min-width: 0; }
@media(max-width:1199px) { .invoice-item-row { grid-template-columns: repeat(2,minmax(0,1fr)); } .invoice-item-action { grid-column: 1/-1; } }
@media(max-width:600px) { .invoice-item-row { grid-template-columns: minmax(0,1fr); padding: 14px; } }
</style>
<div style="margin-bottom:12px;"><button type="button" id="refresh-invoice-banks" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Refresh bank accounts</button> <small id="invoice-bank-feedback" role="status"></small></div>
<div id="perticulars_div">
    <?php oecrm_invoice_item_markup($invoiceBankOptions, $invoiceCurrencyOptions, true); ?>
</div>
<template id="invoice-item-template"><?php oecrm_invoice_item_markup($invoiceBankOptions, $invoiceCurrencyOptions); ?></template>
<script src="../js/oecrm-invoice-items.js?v=20261015-1"></script>