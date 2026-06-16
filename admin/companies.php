<?php
$active_menu = 'foundation';
$active_submenu = 'companies';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'companies', 'view');

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $legalName = trim($_POST['legal_name'] ?? '');
    $displayName = trim($_POST['display_name'] ?? '');
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $type = $_POST['company_type'] ?? 'other';
    $countryId = (int) ($_POST['country_id'] ?? 0);
    $stateId = (int) ($_POST['state_id'] ?? 0);
    $cityId = (int) ($_POST['city_id'] ?? 0);
    $location = null;
    if ($countryId) {
        $stmt = mysqli_prepare($conn, 'SELECT c.iso2 country_code,s.id state_id,ct.id city_id FROM countries c LEFT JOIN states s ON s.id=? AND s.country_id=c.id LEFT JOIN cities ct ON ct.id=? AND ct.state_id=s.id AND ct.country_id=c.id WHERE c.id=?');
        mysqli_stmt_bind_param($stmt, 'iii', $stateId, $cityId, $countryId);
        mysqli_stmt_execute($stmt);
        $location = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
    }
    $country = strtoupper($location['country_code'] ?? trim($_POST['country_code'] ?? 'IN'));
    $currency = strtoupper(trim($_POST['currency_code'] ?? 'INR'));
    $gstin = trim($_POST['gstin'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    $types = ['india_gst', 'india_non_gst', 'usa', 'other'];

    if ($legalName === '' || $displayName === '' || $code === '' || !in_array($type, $types, true) || ($countryId && (!$location || ($stateId && !$location['state_id']) || ($cityId && !$location['city_id'])))) {
        $error = 'Please complete all required company fields.';
    } elseif ($id) {
        oecrm_require_permission($conn, 'companies', 'edit');
        $stmt = mysqli_prepare($conn, 'SELECT * FROM companies WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
        $stmt = mysqli_prepare($conn, 'UPDATE companies SET code=?, legal_name=?, display_name=?, company_type=?, country_code=?,country_id=?,state_id=?,city_id=?, currency_code=?, gstin=?, email=?, status=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'sssssiiisssii', $code, $legalName, $displayName, $type, $country, $countryId, $stateId, $cityId, $currency, $gstin, $email, $status, $id);
        if (mysqli_stmt_execute($stmt)) {
            oecrm_audit($conn, 'companies', 'update', 'company', $id, 'Company updated', $old, $_POST);
            $message = 'Company updated successfully.';
        } else { $error = 'Company could not be updated. Code must be unique.'; }
        mysqli_stmt_close($stmt);
    } else {
        oecrm_require_permission($conn, 'companies', 'create');
        $stmt = mysqli_prepare($conn, 'INSERT INTO companies (code, legal_name, display_name, company_type, country_code,country_id,state_id,city_id, currency_code, gstin, email, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sssssiiisssi', $code, $legalName, $displayName, $type, $country, $countryId, $stateId, $cityId, $currency, $gstin, $email, $status);
        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($conn);
            oecrm_audit($conn, 'companies', 'create', 'company', $newId, 'Company created', null, $_POST);
            $message = 'Company created successfully.';
        } else { $error = 'Company could not be created. Code must be unique.'; }
        mysqli_stmt_close($stmt);
    }
}

$edit = null;
if (!empty($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, 'SELECT * FROM companies WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
}
$companiesResult = mysqli_query($conn, 'SELECT * FROM companies ORDER BY status DESC, display_name');
$countries = mysqli_query($conn, 'SELECT id,name,iso2 FROM countries WHERE flag=1 ORDER BY name');
?>
<div id="page-wrapper" class="compact-admin-page foundation-page">
  <div class="foundation-grid">
    <div class="panel panel-default">
      <div class="panel-heading"><?php echo $edit ? 'Edit Company' : 'Add Company'; ?></div>
      <div class="panel-body">
        <?php if ($message): ?><div class="alert alert-success"><?php echo oecrm_h($message); ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
        <form method="post">
          <?php echo oecrm_csrf_field(); ?><input type="hidden" name="id" value="<?php echo (int)($edit['id'] ?? 0); ?>">
          <div class="form-group"><label>Company Code *</label><input class="form-control" name="code" maxlength="30" required value="<?php echo oecrm_h($edit['code'] ?? ''); ?>"></div>
          <div class="form-group"><label>Legal Name *</label><input class="form-control" name="legal_name" required value="<?php echo oecrm_h($edit['legal_name'] ?? ''); ?>"></div>
          <div class="form-group"><label>Display Name *</label><input class="form-control" name="display_name" required value="<?php echo oecrm_h($edit['display_name'] ?? ''); ?>"></div>
          <div class="form-row-two">
            <div class="form-group"><label>Company Type</label><select class="form-control" name="company_type"><?php foreach(['india_gst'=>'India GST','india_non_gst'=>'India Non-GST','usa'=>'USA','other'=>'Other'] as $v=>$l): ?><option value="<?php echo $v; ?>" <?php echo (($edit['company_type'] ?? 'india_gst')===$v)?'selected':''; ?>><?php echo $l; ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>GSTIN / Tax ID</label><input class="form-control" name="gstin" value="<?php echo oecrm_h($edit['gstin'] ?? ''); ?>"></div>
          </div>
          <div class="form-row-two"><div class="form-group"><label>Country</label><select class="form-control" id="companyCountry" name="country_id"><option value="">Select Country</option><?php while($countryRow=mysqli_fetch_assoc($countries)):?><option value="<?php echo (int)$countryRow['id'];?>" data-code="<?php echo oecrm_h($countryRow['iso2']);?>" <?php echo (int)($edit['country_id']??0)===(int)$countryRow['id']?'selected':'';?>><?php echo oecrm_h($countryRow['name']);?></option><?php endwhile;?></select><input type="hidden" name="country_code" id="companyCountryCode" value="<?php echo oecrm_h($edit['country_code']??'IN');?>"></div><div class="form-group"><label>State</label><select class="form-control" id="companyState" name="state_id" data-selected="<?php echo (int)($edit['state_id']??0);?>"><option value="">Select State</option></select></div></div>
          <div class="form-row-two"><div class="form-group"><label>City</label><select class="form-control" id="companyCity" name="city_id" data-selected="<?php echo (int)($edit['city_id']??0);?>"><option value="">Select City</option></select></div><div class="form-group"><label>Currency</label><input class="form-control" name="currency_code" maxlength="3" value="<?php echo oecrm_h($edit['currency_code'] ?? 'INR'); ?>"></div></div>
          <div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" value="<?php echo oecrm_h($edit['email'] ?? ''); ?>"></div>
          <label class="foundation-toggle"><input type="checkbox" name="status" <?php echo !isset($edit['status']) || $edit['status'] ? 'checked' : ''; ?>> Active company</label>
          <div class="foundation-actions"><button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> <?php echo $edit ? 'Update' : 'Save'; ?></button><?php if($edit): ?><a class="btn btn-default" href="companies.php">Cancel</a><?php endif; ?></div>
        </form>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">Companies</div>
      <div class="panel-body table-responsive">
        <table class="table foundation-table"><thead><tr><th>Company</th><th>Type</th><th>Currency</th><th>Status</th><th></th></tr></thead><tbody>
        <?php while($row=mysqli_fetch_assoc($companiesResult)): ?><tr><td><strong><?php echo oecrm_h($row['display_name']); ?></strong><small><?php echo oecrm_h($row['code']); ?></small></td><td><?php echo oecrm_h(str_replace('_',' ',ucwords($row['company_type'],'_'))); ?></td><td><?php echo oecrm_h($row['currency_code']); ?></td><td><span class="status-pill <?php echo $row['status']?'active':'inactive'; ?>"><?php echo $row['status']?'Active':'Inactive'; ?></span></td><td><a class="icon-action" href="companies.php?edit=<?php echo (int)$row['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a></td></tr><?php endwhile; ?>
        </tbody></table>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
  var country=document.getElementById('companyCountry'),state=document.getElementById('companyState'),city=document.getElementById('companyCity'),code=document.getElementById('companyCountryCode');
  function load(type,parent,target,selected){target.innerHTML='<option value="">Select '+(type==='states'?'State':'City')+'</option>';if(!parent)return;fetch('locationAjax.php?type='+type+'&parent_id='+parent).then(function(r){return r.json();}).then(function(rows){rows.forEach(function(row){var option=new Option(row.name,row.id);if(String(row.id)===String(selected))option.selected=true;target.add(option);});if(type==='states'&&target.value)load('cities',target.value,city,city.dataset.selected);});}
  country.addEventListener('change',function(){code.value=country.options[country.selectedIndex].dataset.code||'';city.innerHTML='<option value="">Select City</option>';load('states',country.value,state,'');});
  state.addEventListener('change',function(){load('cities',state.value,city,'');});
  if(country.value)load('states',country.value,state,state.dataset.selected);
});
</script>
<?php include 'footer.php'; ?>
