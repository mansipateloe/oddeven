<?php
$active_menu = 'lead';
$active_submenu = 'view_lead';
include 'header.php';
$id = oecrm_int_param($_GET, 'edit');
$companyId = oecrm_current_company_id($conn);
oecrm_require_permission($conn, 'clients', 'edit');
$stmt = mysqli_prepare($conn, 'SELECT * FROM leads WHERE lead_id=? AND company_id=? AND is_active=1');
mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
mysqli_stmt_execute($stmt);
$lead = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$lead) {
    http_response_code(404);
    exit('Lead not found.');
}
$sources = mysqli_query($conn, 'SELECT id,name FROM lead_source_tbl ORDER BY name');
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="lead-page-header">
        <div><h2>Edit Lead</h2><small>Update contact, source and pipeline details</small></div>
        <a href="leads.php" class="btn btn-default"><i class="fa fa-arrow-left"></i> Lead Pipeline</a>
    </div>
    <div class="panel panel-default lead-form-card">
        <div class="panel-heading"><strong>Lead Information</strong><span class="lead-status"><?php echo oecrm_h($lead['status']); ?></span></div>
        <div class="panel-body">
            <form action="action_update_lead.php" method="post">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="lead_id" value="<?php echo (int)$id; ?>">
                <div class="lead-form-grid">
                    <div class="form-group"><label>Lead Date *</label><input type="date" class="form-control" name="leadDate" value="<?php echo oecrm_h($lead['lead_date']); ?>" required></div>
                    <div class="form-group"><label>Client Name *</label><input class="form-control" name="executiveName" value="<?php echo oecrm_h($lead['executive_name']); ?>" required></div>
                    <div class="form-group"><label>Nick Name</label><input class="form-control" name="nick_name" value="<?php echo oecrm_h($lead['nick_name']); ?>"></div>
                    <div class="form-group"><label>Company Name *</label><input class="form-control" name="company" value="<?php echo oecrm_h($lead['company_name']); ?>" required></div>
                    <div class="form-group"><label>Contact Person *</label><input class="form-control" name="cperson" value="<?php echo oecrm_h($lead['contact_person']); ?>" required></div>
                    <div class="form-group"><label>Lead Source *</label><select class="form-control" name="lead_source" required><option value="">Select source</option><?php while($source=mysqli_fetch_assoc($sources)):?><option value="<?php echo (int)$source['id'];?>" <?php echo (int)$lead['lead_source']===(int)$source['id']?'selected':'';?>><?php echo oecrm_h($source['name']);?></option><?php endwhile;?></select></div>
                    <div class="form-group"><label>Mobile No. 1</label><input class="form-control only-digits" name="mobileno1" value="<?php echo oecrm_h($lead['mobile_no1']); ?>" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" autocomplete="off"></div>
                    <div class="form-group"><label>Mobile No. 2</label><input class="form-control only-digits" name="mobileno2" value="<?php echo oecrm_h($lead['mobile_no2']); ?>" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" autocomplete="off"></div>
                    <div class="form-group"><label>Status *</label><select class="form-control" name="status" required><?php foreach(['pending'=>'Pending','inprogress'=>'In Progress','complete'=>'Complete','close'=>'Closed'] as $value=>$label):?><option value="<?php echo $value;?>" <?php echo $lead['status']===$value?'selected':'';?>><?php echo $label;?></option><?php endforeach;?></select></div>
                    <div class="form-group"><label>Company Email</label><input type="email" class="form-control" name="emailid" value="<?php echo oecrm_h($lead['email']); ?>"></div>
                    <div class="form-group"><label>Personal Email</label><input type="email" class="form-control" name="emailid2" value="<?php echo oecrm_h($lead['personal_email']); ?>"></div>
                    <div class="form-group"><label>City</label><input class="form-control" name="city" value="<?php echo oecrm_h($lead['city']); ?>"></div>
                    <div class="form-group span-3"><label>Address</label><textarea class="form-control" name="address" rows="3"><?php echo oecrm_h($lead['address']); ?></textarea></div>
                    <div class="lead-form-actions"><button class="btn btn-primary" name="leadSave"><i class="fa fa-save"></i> Update Lead</button><a class="btn btn-default" href="leads.php">Cancel</a></div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
