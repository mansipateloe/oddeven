<?php
$active_menu='notices';
include 'header.php';
require_once __DIR__.'/../notices.php';
oecrm_require_permission($conn,'notices','view');
oecrm_notice_sync_statuses($conn);
$companyId=oecrm_current_company_id($conn);
$editId=(int)($_GET['edit']??0);
$edit=$editId?mysqli_fetch_assoc(mysqli_query($conn,'SELECT * FROM notices WHERE id='.$editId.' AND company_id='.$companyId)):null;
$selectedDepartments=[];$selectedEmployees=[];
if($edit){$targets=mysqli_query($conn,'SELECT department_id,employee_id FROM notice_targets WHERE notice_id='.$editId);while($target=mysqli_fetch_assoc($targets)){if($target['department_id'])$selectedDepartments[]=(int)$target['department_id'];if($target['employee_id'])$selectedEmployees[]=(int)$target['employee_id'];}}
$types=mysqli_query($conn,'SELECT type_key,name FROM notice_types WHERE company_id='.$companyId.' AND status=1 ORDER BY name');
$categories=mysqli_query($conn,'SELECT id,name FROM notice_categories WHERE company_id='.$companyId.' AND status=1 ORDER BY name');
$departments=mysqli_query($conn,'SELECT id,name FROM departments WHERE company_id='.$companyId.' AND status=1 ORDER BY name');
$employees=mysqli_query($conn,'SELECT id,employeeCode,name,department_id FROM employeestbl WHERE company_id='.$companyId.' AND status=0 ORDER BY name');
$rows=mysqli_query($conn,"SELECT n.*,nc.name category_name,(SELECT COUNT(*) FROM notice_receipts r WHERE r.notice_id=n.id AND r.read_at IS NOT NULL) read_count,(SELECT COUNT(*) FROM notice_receipts r WHERE r.notice_id=n.id AND r.acknowledged_at IS NOT NULL) ack_count FROM notices n LEFT JOIN notice_categories nc ON nc.id=n.category_id WHERE n.company_id=$companyId ORDER BY n.id DESC");
$flash=$_SESSION['notice_flash']??'';$error=$_SESSION['notice_error']??'';unset($_SESSION['notice_flash'],$_SESSION['notice_error']);
?>
<div id="page-wrapper" class="compact-admin-page notice-admin-page"><?php if($flash):?><div class="alert alert-success"><?php echo oecrm_h($flash);?></div><?php endif;?><?php if($error):?><div class="alert alert-danger"><?php echo oecrm_h($error);?></div><?php endif;?>
<div class="panel panel-default"><div class="panel-heading foundation-heading"><span><?php echo $edit?'Edit':'Create';?> Notice</span><a href="noticeMasters.php" class="btn btn-default btn-sm"><i class="fa fa-cog"></i> Types & Categories</a></div><div class="panel-body"><style>
.notice-form .is-invalid {
    border-color: #d9534f !important;
    box-shadow: 0 0 0 0.2rem rgba(217, 83, 79, 0.15) !important;
}
.notice-form .field-error {
    display: block;
    color: #d9534f;
    font-size: 12px;
    margin-top: 6px;
    line-height: 1.3;
}
</style><form id="noticeForm" method="post" action="noticeAction.php" enctype="multipart/form-data" class="notice-form" novalidate><?php echo oecrm_csrf_field();?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?php echo (int)($edit['id']??0);?>">
<div class="notice-form-grid four"><div class="form-group"><label>Type</label><select class="form-control" name="notice_type"><?php while($type=mysqli_fetch_assoc($types)):?><option value="<?php echo oecrm_h($type['type_key']);?>" <?php echo ($edit['notice_type']??'company')===$type['type_key']?'selected':'';?>><?php echo oecrm_h($type['name']);?></option><?php endwhile;?></select></div><div class="form-group"><label>Category</label><select class="form-control" name="category_id"><option value="0">General</option><?php while($category=mysqli_fetch_assoc($categories)):?><option value="<?php echo (int)$category['id'];?>" <?php echo (int)($edit['category_id']??0)===(int)$category['id']?'selected':'';?>><?php echo oecrm_h($category['name']);?></option><?php endwhile;?></select></div><div class="form-group"><label>Priority</label><select class="form-control" name="priority"><?php foreach(['low','normal','high','urgent'] as $value):?><option value="<?php echo $value;?>" <?php echo ($edit['priority']??'normal')===$value?'selected':'';?>><?php echo ucfirst($value);?></option><?php endforeach;?></select></div><div class="form-group"><label>Audience</label><select class="form-control" id="noticeAudience" name="audience_type"><?php foreach(['all'=>'All Employees','department'=>'Departments','employee'=>'Selected Employees'] as $key=>$label):?><option value="<?php echo $key;?>" <?php echo ($edit['audience_type']??'all')===$key?'selected':'';?>><?php echo $label;?></option><?php endforeach;?></select></div></div>
<div class="form-group"><label>Title</label><input class="form-control" name="title" value="<?php echo oecrm_h($edit['title']??'');?>"></div><div class="form-group"><label>Content</label><textarea class="form-control notice-editor" name="body_html"><?php echo oecrm_h($edit['body_html']??'');?></textarea></div>
<div class="notice-form-grid"><div class="form-group"><label>Publish At</label><input type="datetime-local" class="form-control" name="publish_at" value="<?php echo oecrm_h(!empty($edit['publish_at'])?date('Y-m-d\TH:i',strtotime($edit['publish_at'])):date('Y-m-d\TH:i'));?>"></div><div class="form-group"><label>Expires At</label><input type="datetime-local" class="form-control" name="expires_at" value="<?php echo oecrm_h(!empty($edit['expires_at'])?date('Y-m-d\TH:i',strtotime($edit['expires_at'])):'');?>"></div><div class="form-group"><label>Attachment</label><input type="file" class="form-control" name="attachment"></div></div>
<div id="departmentTargets" class="notice-targets"><label>Departments</label><div><?php while($department=mysqli_fetch_assoc($departments)):?><label><input type="checkbox" name="department_ids[]" value="<?php echo (int)$department['id'];?>" <?php echo in_array((int)$department['id'],$selectedDepartments,true)?'checked':'';?>> <?php echo oecrm_h($department['name']);?></label><?php endwhile;?></div></div>
<div id="employeeTargets" class="notice-targets"><label>Employees</label><div><?php while($employee=mysqli_fetch_assoc($employees)):?><label data-department="<?php echo (int)$employee['department_id'];?>"><input type="checkbox" name="employee_ids[]" value="<?php echo (int)$employee['id'];?>" <?php echo in_array((int)$employee['id'],$selectedEmployees,true)?'checked':'';?>> <?php echo oecrm_h($employee['employeeCode'].' - '.$employee['name']);?></label><?php endwhile;?></div></div>
<div class="notice-options"><label><input type="checkbox" name="show_popup" <?php echo !empty($edit['show_popup'])?'checked':'';?>> Dashboard popup</label><label><input type="checkbox" name="acknowledgement_required" <?php echo !empty($edit['acknowledgement_required'])?'checked':'';?>> Require acknowledgement</label></div><div class="foundation-actions"><button class="btn btn-default" name="save_mode" value="draft">Save Draft</button><button class="btn btn-primary" name="save_mode" value="publish">Publish / Schedule</button></div></form></div></div>
<div class="panel panel-default"><div class="panel-heading">Notice Delivery</div><div class="panel-body table-responsive"><table class="table foundation-table"><thead><tr><th>Notice</th><th>Category</th><th>Audience</th><th>Status</th><th>Delivery</th><th>Actions</th></tr></thead><tbody><?php while($notice=mysqli_fetch_assoc($rows)):?><tr><td><strong><?php echo oecrm_h($notice['title']);?></strong><small><?php echo oecrm_h($notice['notice_type'].' | '.$notice['priority']);?></small></td><td><?php echo oecrm_h($notice['category_name']?:'General');?></td><td><?php echo ucfirst($notice['audience_type']);?></td><td><?php echo ucfirst($notice['status']);?></td><td><?php echo (int)$notice['read_count'];?> read / <?php echo (int)$notice['ack_count'];?> acknowledged</td><td><a class="icon-action" href="noticeCenter.php?edit=<?php echo (int)$notice['id'];?>"><i class="fa fa-pencil"></i></a><a class="icon-action" href="noticeReport.php?id=<?php echo (int)$notice['id'];?>"><i class="fa fa-bar-chart"></i></a><?php if(!in_array($notice['status'],['cancelled','expired'],true)):?><form method="post" action="noticeAction.php" style="display:inline" data-confirm="Cancel this notice?"><?php echo oecrm_csrf_field();?><input type="hidden" name="action" value="cancel"><input type="hidden" name="id" value="<?php echo (int)$notice['id'];?>"><button class="icon-action danger"><i class="fa fa-ban"></i></button></form><?php endif;?></td></tr><?php endwhile;?></tbody></table></div></div>
</div>
<script>
(function () {
    var audience = document.getElementById('noticeAudience');
    var departments = document.getElementById('departmentTargets');
    var employees = document.getElementById('employeeTargets');
    var form = document.getElementById('noticeForm');
    var requiredFields = [
        form && form.querySelector('input[name="title"]'),
        form && form.querySelector('textarea[name="body_html"]'),
        form && form.querySelector('input[name="publish_at"]')
    ].filter(Boolean);

    function showError(field, message) {
        field.classList.add('is-invalid');
        var group = field.closest('.form-group');
        if (!group) return;
        var error = group.querySelector('.field-error');
        if (!error) {
            error = document.createElement('div');
            error.className = 'field-error';
            group.appendChild(error);
        }
        error.textContent = message;
    }

    function clearError(field) {
        field.classList.remove('is-invalid');
        var group = field.closest('.form-group');
        if (!group) return;
        var error = group.querySelector('.field-error');
        if (error) error.remove();
    }

    requiredFields.forEach(function (field) {
        field.addEventListener('input', function () {
            if (field.value && field.value.trim() !== '') clearError(field);
        });
        field.addEventListener('change', function () {
            if (field.value && field.value.trim() !== '') clearError(field);
        });
    });

    if (form) {
        form.addEventListener('submit', function (event) {
            var valid = true;
            requiredFields.forEach(function (field) {
                if (!field.value || field.value.trim() === '') {
                    valid = false;
                    showError(field, 'This field is required.');
                } else {
                    clearError(field);
                }
            });

            if (!valid) {
                event.preventDefault();
                var firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    function sync() {
        if (departments) {
            departments.style.display = audience.value === 'department' ? 'block' : 'none';
        }
        if (employees) {
            employees.style.display = audience.value === 'employee' ? 'block' : 'none';
        }
    }

    if (audience) {
        audience.addEventListener('change', sync);
        sync();
    }
})();
</script>
<?php include 'footer.php'; ?>
