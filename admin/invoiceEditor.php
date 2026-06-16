<?php
$active_menu = 'finance';
include 'header.php';
require_once __DIR__.'/../foundation.php';
$id = (int)($_GET['id'] ?? 0);
oecrm_require_permission($conn, 'finance', $id ? 'edit' : 'create');
$companyId = oecrm_current_company_id($conn);
$company = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM companies WHERE id='.$companyId));
$isGstCompany = ($company['company_type'] ?? '') === 'india_gst';
$defaultInvoiceType = $isGstCompany ? 'gst' : 'international';
$defaultCurrency = $company['currency_code'] ?? ($isGstCompany ? 'INR' : 'USD');
$prefixResult = mysqli_fetch_assoc(mysqli_query($conn, "SELECT setting_value FROM app_settings WHERE company_id=$companyId AND setting_key='invoice_prefix' LIMIT 1"));
$invoicePrefix = $prefixResult['setting_value'] ?? ($isGstCompany ? 'INV' : 'INT');
$clients = mysqli_query($conn, 'SELECT id,display_name FROM clients WHERE company_id='.$companyId.' AND status IN ("active","prospect") ORDER BY display_name');
$invoice = [
    'client_id'=>'','invoice_number'=>$invoicePrefix.'-'.date('Ymd-His'),'invoice_type'=>$defaultInvoiceType,
    'invoice_date'=>date('Y-m-d'),'due_date'=>date('Y-m-d',strtotime('+15 days')),
    'currency_code'=>$defaultCurrency,'status'=>'draft','recurring_frequency'=>'none','notes'=>''
];
$items = [];
if ($id) {
    $invoice = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM finance_invoices WHERE id='.$id.' AND company_id='.$companyId));
    if (!$invoice) exit('Invoice not found.');
    $itemResult = mysqli_query($conn, 'SELECT * FROM finance_invoice_items WHERE invoice_id='.$id.' ORDER BY id');
    while ($item = mysqli_fetch_assoc($itemResult)) $items[] = $item;
}
if (!$items) $items[] = ['description'=>'Professional services','quantity'=>1,'rate'=>0,'tax_percent'=>$isGstCompany?18:0];
?>
<div id="page-wrapper" class="compact-admin-page resource-form-page">
  <div class="foundation-titlebar">
    <a href="finance.php"><i class="fa fa-arrow-left"></i> Finance</a>
    <h2><?php echo $id ? 'Invoice '.oecrm_h($invoice['invoice_number']) : 'New Invoice'; ?></h2>
  </div>
  <div class="panel panel-default">
    <div class="panel-body">
      <form method="post" action="financeAction.php" id="invoice-form">
        <?php echo oecrm_csrf_field(); ?>
        <input type="hidden" name="action" value="invoice">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="resource-form">
          <div class="form-group"><label>Invoice Number</label><input class="form-control" name="invoice_number" value="<?php echo oecrm_h($invoice['invoice_number']); ?>" required></div>
          <div class="form-group"><label>Client</label><select class="form-control" name="client_id" required><option value="">Select client</option><?php while($c=mysqli_fetch_assoc($clients)): ?><option value="<?php echo $c['id']; ?>" <?php echo $invoice['client_id']==$c['id']?'selected':''; ?>><?php echo oecrm_h($c['display_name']); ?></option><?php endwhile; ?></select></div>
          <div class="form-group"><label>Invoice Type</label><input class="form-control" value="<?php echo $isGstCompany?'GST Invoice (18%)':'International Invoice'; ?>" readonly><input type="hidden" name="invoice_type" value="<?php echo $defaultInvoiceType; ?>"></div>
          <div class="form-group"><label>Invoice Date</label><input class="form-control" type="date" name="invoice_date" value="<?php echo $invoice['invoice_date']; ?>" required></div>
          <div class="form-group"><label>Due Date</label><input class="form-control" type="date" name="due_date" value="<?php echo $invoice['due_date']; ?>" required></div>
          <div class="form-group"><label>Currency</label><input class="form-control" maxlength="3" name="currency_code" value="<?php echo oecrm_h($invoice['currency_code']); ?>" readonly></div>
          <div class="form-group"><label>Status</label><select class="form-control" name="status"><?php foreach(['draft','sent','viewed','partially_paid','paid','overdue','cancelled'] as $v): ?><option value="<?php echo $v; ?>" <?php echo $invoice['status']===$v?'selected':''; ?>><?php echo ucwords(str_replace('_',' ',$v)); ?></option><?php endforeach; ?></select></div>
          <div class="form-group"><label>Recurring</label><select class="form-control" name="recurring_frequency"><?php foreach(['none','monthly','quarterly','yearly'] as $v): ?><option value="<?php echo $v; ?>" <?php echo ($invoice['recurring_frequency']??'none')===$v?'selected':''; ?>><?php echo ucfirst($v); ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="invoice-items-heading"><h4>Invoice Items</h4><button type="button" class="btn btn-default btn-sm" id="add-item"><i class="fa fa-plus"></i> Add Item</button></div>
        <div class="table-responsive"><table class="table foundation-table invoice-items"><thead><tr><th>Description</th><th>Quantity</th><th>Rate</th><th>Tax %</th><th>Line Total</th><th></th></tr></thead><tbody id="invoice-items-body">
        <?php foreach($items as $item): ?><tr>
          <td><input class="form-control" name="item_description[]" value="<?php echo oecrm_h($item['description']); ?>" required></td>
          <td><input class="form-control item-quantity" type="number" min=".01" step=".01" name="quantity[]" value="<?php echo oecrm_h($item['quantity']); ?>" required></td>
          <td><input class="form-control item-rate" type="number" min="0" step=".01" name="rate[]" value="<?php echo oecrm_h($item['rate']); ?>" required></td>
          <td><input class="form-control item-tax" type="number" name="tax_percent[]" value="<?php echo $isGstCompany?'18':'0'; ?>" readonly></td>
          <td class="line-total">0.00</td><td><button type="button" class="btn btn-danger btn-xs remove-item"><i class="fa fa-trash"></i></button></td>
        </tr><?php endforeach; ?>
        </tbody></table></div>
        <div class="invoice-total-box"><span>Subtotal <strong id="invoice-subtotal">0.00</strong></span><span>Tax <strong id="invoice-tax">0.00</strong></span><span>Total <strong id="invoice-total">0.00</strong></span></div>
        <div class="form-group"><label>Notes</label><textarea class="form-control" name="notes"><?php echo oecrm_h($invoice['notes']); ?></textarea></div>
        <div class="resource-actions"><button class="btn btn-primary"><i class="fa fa-save"></i> Save Invoice</button><?php if($id): ?><a class="btn btn-default" target="_blank" href="invoiceView.php?id=<?php echo $id; ?>"><i class="fa fa-file-pdf-o"></i> PDF / Print</a><?php endif; ?><a class="btn btn-default" href="finance.php">Cancel</a></div>
      </form>
    </div>
  </div>
</div>
<script>
(function(){
  var body=document.getElementById('invoice-items-body');
  function calculate(){
    var subtotal=0,tax=0;
    body.querySelectorAll('tr').forEach(function(row){
      var q=parseFloat(row.querySelector('.item-quantity').value)||0;
      var rate=parseFloat(row.querySelector('.item-rate').value)||0;
      var percent=parseFloat(row.querySelector('.item-tax').value)||0;
      var line=q*rate,lineTax=line*percent/100;
      subtotal+=line;tax+=lineTax;row.querySelector('.line-total').textContent=(line+lineTax).toFixed(2);
    });
    document.getElementById('invoice-subtotal').textContent=subtotal.toFixed(2);
    document.getElementById('invoice-tax').textContent=tax.toFixed(2);
    document.getElementById('invoice-total').textContent=(subtotal+tax).toFixed(2);
  }
  body.addEventListener('input',calculate);
  body.addEventListener('click',function(e){var b=e.target.closest('.remove-item');if(b&&body.rows.length>1){b.closest('tr').remove();calculate();}});
  document.getElementById('add-item').addEventListener('click',function(){
    body.insertAdjacentHTML('beforeend','<tr><td><input class="form-control" name="item_description[]" required></td><td><input class="form-control item-quantity" type="number" min=".01" step=".01" name="quantity[]" value="1" required></td><td><input class="form-control item-rate" type="number" min="0" step=".01" name="rate[]" value="0" required></td><td><input class="form-control item-tax" type="number" name="tax_percent[]" value="<?php echo $isGstCompany?'18':'0'; ?>" readonly></td><td class="line-total">0.00</td><td><button type="button" class="btn btn-danger btn-xs remove-item"><i class="fa fa-trash"></i></button></td></tr>');
  });
  calculate();
})();
</script>
<?php include 'footer.php'; ?>
