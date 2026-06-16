<?php
$active_menu='foundation'; $active_submenu='permissions'; include 'header.php'; require_once __DIR__.'/../foundation.php';
oecrm_require_permission($conn,'roles','manage_permissions');
$companyId=oecrm_current_company_id($conn);$roleId=(int)($_GET['role_id']??$_POST['role_id']??0); $message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 oecrm_require_csrf();
 if($roleId<1){http_response_code(400);exit('Invalid role.');}
 $roleStmt=mysqli_prepare($conn,'SELECT id FROM user_type WHERE id=? AND is_deleted=0 AND org_id IN (0,?)');
 mysqli_stmt_bind_param($roleStmt,'ii',$roleId,$companyId);mysqli_stmt_execute($roleStmt);$validRole=mysqli_fetch_assoc(mysqli_stmt_get_result($roleStmt));mysqli_stmt_close($roleStmt);
 if(!$validRole){http_response_code(404);exit('Role not found.');}
 $selectedPermissionIds=array_fill_keys(array_map('intval',$_POST['permissions']??[]),true);
 mysqli_begin_transaction($conn);
 try{
  $stmt=mysqli_prepare($conn,'DELETE FROM role_permissions WHERE role_id=? AND company_id=?');mysqli_stmt_bind_param($stmt,'ii',$roleId,$companyId);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
  $permissionRows=mysqli_query($conn,'SELECT id FROM permissions ORDER BY id');
  $insert=mysqli_prepare($conn,'INSERT INTO role_permissions(role_id,permission_id,company_id,allowed) VALUES(?,?,?,?)');
  while($permissionRow=mysqli_fetch_assoc($permissionRows)){$permissionId=(int)$permissionRow['id'];$allowed=isset($selectedPermissionIds[$permissionId])?1:0;mysqli_stmt_bind_param($insert,'iiii',$roleId,$permissionId,$companyId,$allowed);mysqli_stmt_execute($insert);}
  mysqli_stmt_close($insert);mysqli_commit($conn);oecrm_audit($conn,'roles','manage_permissions','role',$roleId,'Role permissions updated',null,['permissions'=>$_POST['permissions']??[]]);$message='Permissions updated successfully.';
 }catch(Throwable $e){mysqli_rollback($conn);throw $e;}
}
$roles=mysqli_query($conn,'SELECT id,name FROM user_type WHERE is_deleted=0 AND org_id IN (0,'.(int)$companyId.') ORDER BY name');
$role=null;if($roleId){$stmt=mysqli_prepare($conn,'SELECT id,name FROM user_type WHERE id=? AND is_deleted=0 AND org_id IN (0,?)');mysqli_stmt_bind_param($stmt,'ii',$roleId,$companyId);mysqli_stmt_execute($stmt);$role=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);}
$selected=[];if($roleId){$stmt=mysqli_prepare($conn,'SELECT permission_id,allowed FROM role_permissions WHERE role_id=? AND company_id IN (0,?) ORDER BY company_id ASC');mysqli_stmt_bind_param($stmt,'ii',$roleId,$companyId);mysqli_stmt_execute($stmt);$result=mysqli_stmt_get_result($stmt);while($r=mysqli_fetch_assoc($result)){$selected[(int)$r['permission_id']]=(bool)$r['allowed'];}mysqli_stmt_close($stmt);}
$permissions=mysqli_query($conn,'SELECT * FROM permissions ORDER BY module_key,action_key');$grouped=[];while($p=mysqli_fetch_assoc($permissions)){$grouped[$p['module_key']][]=$p;}
?>
<div id="page-wrapper" class="compact-admin-page foundation-page"><div class="panel panel-default"><div class="panel-heading foundation-heading"><span>Role Permissions</span><small>Company-scoped action access</small></div><div class="panel-body">
<?php if($message):?><div class="alert alert-success"><?php echo oecrm_h($message);?></div><?php endif;?>
<form method="get" class="permission-role-picker"><label>Select Role</label><select class="form-control" name="role_id" onchange="this.form.submit()"><option value="">Choose role</option><?php while($r=mysqli_fetch_assoc($roles)):?><option value="<?php echo (int)$r['id'];?>" <?php echo $roleId===(int)$r['id']?'selected':'';?>><?php echo oecrm_h($r['name']);?></option><?php endwhile;?></select></form>
<?php if($role):?><form method="post"><?php echo oecrm_csrf_field();?><input type="hidden" name="role_id" value="<?php echo $roleId;?>"><div class="permission-toolbar"><strong><?php echo oecrm_h($role['name']);?></strong><div><button type="button" class="btn btn-default btn-sm" onclick="setPermissions(true)">Select All</button><button type="button" class="btn btn-default btn-sm" onclick="setPermissions(false)">Clear All</button></div></div><div class="permission-grid">
<?php foreach($grouped as $module=>$items):?><section class="permission-module"><h4><?php echo oecrm_h(ucwords(str_replace('_',' ',$module)));?></h4><div class="permission-options"><?php foreach($items as $p):?><label><input type="checkbox" name="permissions[]" value="<?php echo (int)$p['id'];?>" <?php echo !empty($selected[(int)$p['id']])?'checked':'';?>><span><?php echo oecrm_h(ucwords(str_replace('_',' ',$p['action_key'])));?></span></label><?php endforeach;?></div></section><?php endforeach;?></div><div class="foundation-actions permission-save"><button class="btn btn-primary"><i class="fa fa-save"></i> Save Permissions</button></div></form><?php else:?><div class="empty-cell">Select a role to manage its permissions.</div><?php endif;?></div></div></div>
<script>function setPermissions(value){document.querySelectorAll('input[name="permissions[]"]').forEach(function(item){item.checked=value;});}</script><?php include 'footer.php';?>
