<?php
$active_menu = 'attendance';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'attendance', 'view');
$companyId = oecrm_current_company_id($conn);
$editId = (int) ($_GET['edit'] ?? 0);
$edit = $editId ? mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM holidaytbl WHERE id=' . $editId . ' AND company_id IN (0,' . $companyId . ')')) : null;
$rows = mysqli_query($conn, 'SELECT h.*,c.display_name company_name FROM holidaytbl h LEFT JOIN companies c ON c.id=h.company_id WHERE h.company_id IN (0,' . $companyId . ') ORDER BY h.holidayDate');
$flash = $_SESSION['holiday_flash'] ?? '';
unset($_SESSION['holiday_flash']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if($flash): ?><div class="alert alert-info"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="panel panel-default"><div class="panel-heading"><?php echo $edit?'Edit':'Add'; ?> Holiday</div><div class="panel-body"><form method="post" action="holidayAction.php" class="form-inline"><?php echo oecrm_csrf_field(); ?><input type="hidden" name="id" value="<?php echo (int)($edit['id']??0); ?>"><div class="form-group"><label>Date</label> <input class="form-control" type="date" name="holiday_date" required value="<?php echo oecrm_h($edit['holidayDate']??''); ?>"></div> <div class="form-group"><label>Title</label> <input class="form-control" name="title" required value="<?php echo oecrm_h($edit['holidayTitle']??''); ?>"></div> <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button></form></div></div>
    <div class="panel panel-default"><div class="panel-heading">Holiday Calendar</div><div class="panel-body table-responsive"><table class="table foundation-table"><thead><tr><th>Date</th><th>Holiday</th><th>Scope</th><th>Actions</th></tr></thead><tbody><?php while($row=mysqli_fetch_assoc($rows)): ?><tr><td><?php echo date('d M Y',strtotime($row['holidayDate'])); ?></td><td><?php echo oecrm_h($row['holidayTitle']); ?></td><td><?php echo $row['company_id']?oecrm_h($row['company_name']):'All Companies'; ?></td><td><?php if((int)$row['company_id']===$companyId): ?><a class="icon-action" href="manageHoliday.php?edit=<?php echo (int)$row['id']; ?>"><i class="fa fa-pencil"></i></a><form method="post" action="holidayAction.php" style="display:inline" data-confirm="Archive this holiday?"><?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>"><button class="icon-action danger"><i class="fa fa-trash"></i></button></form><?php endif; ?></td></tr><?php endwhile; ?></tbody></table></div></div>
</div>
<?php include 'footer.php'; ?>
