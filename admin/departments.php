<?php
$active_menu='foundation'; $active_submenu='departments'; include 'header.php'; require_once __DIR__.'/../foundation.php';
oecrm_require_permission($conn,'departments','view');
$companyId=oecrm_current_company_id($conn); $message=''; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 oecrm_require_csrf(); $id=(int)($_POST['id']??0); $companyId=(int)($_POST['company_id']??$companyId); $name=trim($_POST['name']??''); $code=strtoupper(trim($_POST['code']??'')); $status=isset($_POST['status'])?1:0;
 if($name===''){ $error='Department name is required.'; }
 else{
    $excludeId = $id ?: 0;
    $dup = mysqli_prepare($conn,'SELECT id FROM departments WHERE company_id=? AND (LOWER(name)=LOWER(?) OR (?<>"" AND LOWER(code)=LOWER(?))) AND id<>? LIMIT 1');
    mysqli_stmt_bind_param($dup,'isssi',$companyId,$name,$code,$code,$excludeId);
    mysqli_stmt_execute($dup);
    $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($dup));
    mysqli_stmt_close($dup);
    if($existing){ $error='Department already exists for this company.'; }
    elseif($id){ oecrm_require_permission($conn,'departments','edit'); $stmt=mysqli_prepare($conn,'UPDATE departments SET company_id=?,name=?,code=?,status=? WHERE id=?'); mysqli_stmt_bind_param($stmt,'issii',$companyId,$name,$code,$status,$id); if(mysqli_stmt_execute($stmt)){oecrm_audit($conn,'departments','update','department',$id,'Department updated',null,$_POST);$message='Department updated.';}else{$error='Department could not be updated.';} mysqli_stmt_close($stmt); }
    else{ oecrm_require_permission($conn,'departments','create'); $stmt=mysqli_prepare($conn,'INSERT INTO departments(company_id,name,code,status) VALUES(?,?,?,?)'); mysqli_stmt_bind_param($stmt,'issi',$companyId,$name,$code,$status); if(mysqli_stmt_execute($stmt)){oecrm_audit($conn,'departments','create','department',mysqli_insert_id($conn),'Department created',null,$_POST);$message='Department created.';}else{$error='Department already exists for this company.';} mysqli_stmt_close($stmt); }
 }
}
$edit=null;if(!empty($_GET['edit'])){$id=(int)$_GET['edit'];$stmt=mysqli_prepare($conn,'SELECT * FROM departments WHERE id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);$edit=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);}
$companies=mysqli_query($conn,'SELECT id,display_name FROM companies WHERE status=1 ORDER BY display_name');
$list=mysqli_query($conn,'SELECT d.*,c.display_name company_name,(SELECT COUNT(*) FROM employeestbl e WHERE e.department_id=d.id) employee_count FROM departments d JOIN companies c ON c.id=d.company_id ORDER BY c.display_name,d.name');
?>
<div id="page-wrapper" class="compact-admin-page foundation-page"><div class="foundation-grid">
<div class="panel panel-default"><div class="panel-heading"><?php echo $edit?'Edit Department':'Add Department'; ?></div><div class="panel-body">
<?php if($message):?><div class="alert alert-success"><?php echo oecrm_h($message);?></div><?php endif;?><?php if($error):?><div class="alert alert-danger"><?php echo oecrm_h($error);?></div><?php endif;?>
<form method="post"><?php echo oecrm_csrf_field();?><input type="hidden" name="id" value="<?php echo (int)($edit['id']??0);?>">
<div class="form-group"><label>Company</label><select class="form-control" name="company_id"><?php while($c=mysqli_fetch_assoc($companies)):?><option value="<?php echo (int)$c['id'];?>" <?php echo ((int)($edit['company_id']??$companyId)===(int)$c['id'])?'selected':'';?>><?php echo oecrm_h($c['display_name']);?></option><?php endwhile;?></select></div>
<div class="form-group"><label>Department Name *</label><input class="form-control" name="name" required value="<?php echo oecrm_h($edit['name']??'');?>"></div><div class="form-group"><label>Department Code</label><input class="form-control" name="code" maxlength="30" value="<?php echo oecrm_h($edit['code']??'');?>"></div>
<label class="foundation-toggle"><input type="checkbox" name="status" <?php echo !isset($edit['status'])||$edit['status']?'checked':'';?>> Active department</label><div class="foundation-actions"><button class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $edit?'Update':'Save';?></button><?php if($edit):?><a class="btn btn-default" href="departments.php">Cancel</a><?php endif;?></div></form></div></div>
<div class="panel panel-default"><div class="panel-heading">Departments</div><div class="panel-body table-responsive"><table class="table foundation-table"><thead><tr><th>Department</th><th>Company</th><th>Employees</th><th>Status</th><th></th></tr></thead><tbody><?php while($r=mysqli_fetch_assoc($list)):?><tr><td><strong><?php echo oecrm_h($r['name']);?></strong><small><?php echo oecrm_h($r['code']);?></small></td><td><?php echo oecrm_h($r['company_name']);?></td><td><?php echo (int)$r['employee_count'];?></td><td><span class="status-pill <?php echo $r['status']?'active':'inactive';?>"><?php echo $r['status']?'Active':'Inactive';?></span></td><td><a class="icon-action" href="departments.php?edit=<?php echo (int)$r['id'];?>"><i class="fa fa-pencil"></i></a></td></tr><?php endwhile;?></tbody></table></div></div>
</div></div><?php include 'footer.php';?>
