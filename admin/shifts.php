<?php
$active_menu='attendance';$active_submenu='shifts';include 'header.php';require_once __DIR__.'/../foundation.php';oecrm_require_permission($conn,'shifts','view');
$message=$_SESSION['shift_flash']??'';unset($_SESSION['shift_flash']);$edit=null;
if(!empty($_GET['edit'])){$id=(int)$_GET['edit'];$stmt=mysqli_prepare($conn,'SELECT * FROM shifts WHERE id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);$edit=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);}
$companyId=oecrm_current_company_id($conn);$companies=mysqli_query($conn,'SELECT id,display_name FROM companies WHERE status=1 ORDER BY display_name');
$list=mysqli_query($conn,'SELECT s.*,c.display_name,(SELECT COUNT(*) FROM employee_shift_assignments a WHERE a.shift_id=s.id AND a.status=1 AND (a.effective_to IS NULL OR a.effective_to>=CURDATE())) employee_count FROM shifts s JOIN companies c ON c.id=s.company_id ORDER BY s.status DESC,s.name');
$rules=[];if($edit){$r=mysqli_query($conn,'SELECT weekday,week_of_month FROM shift_weekly_off_rules WHERE shift_id='.(int)$edit['id'].' AND is_off=1');while($x=mysqli_fetch_assoc($r)){$rules[$x['weekday'].'_'.$x['week_of_month']]=true;}}
?>
<div id="page-wrapper" class="compact-admin-page shift-page">
  <div class="panel panel-default">
    <div class="panel-heading"><?php echo $edit?'Edit Shift':'Create Shift';?></div>
    <div class="panel-body">
      <?php if($message):?><div class="alert alert-success"><?php echo oecrm_h($message);?></div><?php endif;?>
      <form method="post" action="shiftAction.php">
        <?php echo oecrm_csrf_field();?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?php echo (int)($edit['id']??0);?>">
        <div class="shift-form-grid">
          <div class="form-group"><label>Company</label><select class="form-control" name="company_id" required><?php while($c=mysqli_fetch_assoc($companies)):?><option value="<?php echo (int)$c['id'];?>" <?php echo (int)($edit['company_id']??$companyId)===(int)$c['id']?'selected':'';?>><?php echo oecrm_h($c['display_name']);?></option><?php endwhile;?></select></div>
          <div class="form-group"><label>Shift Type</label><select class="form-control" name="shift_type"><?php foreach(['general','night','us','flexible','custom'] as $v):?><option value="<?php echo $v;?>" <?php echo ($edit['shift_type']??'general')===$v?'selected':'';?>><?php echo ucwords($v);?></option><?php endforeach;?></select></div>
          <div class="form-group"><label>Shift Name *</label><input class="form-control" name="name" required value="<?php echo oecrm_h($edit['name']??'');?>"></div>
          <div class="form-group"><label>Code *</label><input class="form-control" name="code" required maxlength="30" value="<?php echo oecrm_h($edit['code']??'');?>"></div>
          <div class="form-group"><label>Start Time *</label><input type="time" class="form-control" name="start_time" required value="<?php echo oecrm_h(substr($edit['start_time']??'10:00',0,5));?>"></div>
          <div class="form-group"><label>End Time *</label><input type="time" class="form-control" name="end_time" required value="<?php echo oecrm_h(substr($edit['end_time']??'19:30',0,5));?>"></div>
          <div class="form-group"><label>Required Work Minutes</label><input type="number" min="1" class="form-control" name="required_minutes" value="<?php echo (int)($edit['required_minutes']??510);?>"></div>
          <div class="form-group"><label>Allowed Break Minutes</label><input type="number" min="0" class="form-control" name="break_minutes" value="<?php echo (int)($edit['break_minutes']??60);?>"></div>
          <div class="form-group"><label>Sign-In Grace Minutes</label><input type="number" min="0" class="form-control" name="grace_in_minutes" value="<?php echo (int)($edit['grace_in_minutes']??0);?>"></div>
          <div class="form-group"><label>Sign-Out Grace Minutes</label><input type="number" min="0" class="form-control" name="grace_out_minutes" value="<?php echo (int)($edit['grace_out_minutes']??0);?>"></div>
        </div>
        <div class="weekly-off-box"><h4>Weekly Off Rules</h4><div class="weekly-off-grid">
          <label><input type="checkbox" name="weekly_off[]" value="0_0" <?php echo isset($rules['0_0'])?'checked':'';?>> Every Sunday</label>
          <label><input type="checkbox" name="weekly_off[]" value="6_1" <?php echo isset($rules['6_1'])?'checked':'';?>> 1st Saturday</label>
          <label><input type="checkbox" name="weekly_off[]" value="6_2" <?php echo isset($rules['6_2'])?'checked':'';?>> 2nd Saturday</label>
          <label><input type="checkbox" name="weekly_off[]" value="6_3" <?php echo isset($rules['6_3'])?'checked':'';?>> 3rd Saturday</label>
          <label><input type="checkbox" name="weekly_off[]" value="6_4" <?php echo isset($rules['6_4'])?'checked':'';?>> 4th Saturday</label>
          <label><input type="checkbox" name="weekly_off[]" value="6_5" <?php echo isset($rules['6_5'])?'checked':'';?>> 5th Saturday</label>
        </div></div>
        <label class="foundation-toggle"><input type="checkbox" name="status" <?php echo !isset($edit['status'])||$edit['status']?'checked':'';?>> Active shift</label>
        <div class="foundation-actions"><button class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $edit?'Update Shift':'Create Shift';?></button><?php if($edit):?><a class="btn btn-default" href="shifts.php">Cancel</a><?php endif;?></div>
      </form>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">Shift Directory</div>
    <div class="panel-body">
      <div class="form-group" style="max-width:320px"><input type="text" class="form-control" id="shiftSearch" placeholder="Search shifts..."></div>
      <div class="shift-card-grid" id="shiftCardGrid">
        <?php while($s=mysqli_fetch_assoc($list)):?>
          <article class="shift-card" data-shift-card data-search="<?php echo oecrm_h(strtolower($s['name'].' '.$s['code'].' '.ucwords($s['shift_type']).' '.($s['display_name']??'')));?>">
            <div class="shift-card-top"><div><strong><?php echo oecrm_h($s['name']);?></strong><small><?php echo oecrm_h($s['code'].' · '.ucwords($s['shift_type']));?></small></div><span class="status-pill <?php echo $s['status']?'active':'inactive';?>"><?php echo $s['status']?'Active':'Inactive';?></span></div>
            <div class="shift-time"><i class="fa fa-clock-o"></i><span><?php echo oecrm_h(substr($s['start_time'],0,5).' - '.substr($s['end_time'],0,5));?></span><?php if($s['crosses_midnight']):?><em>Next day</em><?php endif;?></div>
            <div class="shift-metrics"><span><b><?php echo (int)$s['required_minutes'];?></b> work min</span><span><b><?php echo (int)$s['grace_in_minutes'];?></b> grace min</span><span><b><?php echo (int)$s['employee_count'];?></b> employees</span></div>
            <div class="shift-card-actions">
              <a href="shifts.php?edit=<?php echo (int)$s['id'];?>"><i class="fa fa-pencil"></i> Edit</a>
              <a href="shiftAssignments.php?shift_id=<?php echo (int)$s['id'];?>"><i class="fa fa-users"></i> Assign</a>
              <form method="post" action="shiftAction.php" style="display:inline;">
                <?php echo oecrm_csrf_field();?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$s['id'];?>"><button type="submit" class="btn btn-link" style="padding:0;border:0;" data-confirm="Delete this shift?"><i class="fa fa-trash"></i> Delete</button>
              </form>
            </div>
          </article>
        <?php endwhile;?>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('shiftSearch')?.addEventListener('input',function(){var term=this.value.toLowerCase();document.querySelectorAll('[data-shift-card]').forEach(function(card){card.style.display=card.dataset.search.indexOf(term)>=0?'':'none';});});
</script>
<?php include 'footer.php'; ?>
