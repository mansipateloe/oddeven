<?php
$active_menu = 'role';
$active_submenu = 'view_role';
include 'header.php';
function oecrm_role_can_manage($conn, $action)
{
    return oecrm_is_super_admin()
        || oecrm_can($conn, 'roles', $action)
        || oecrm_legacy_can($conn, 'employee')
        || oecrm_legacy_can($conn, 'settings');
}
if (!oecrm_role_can_manage($conn, 'view')) {
    http_response_code(403);
    exit('You do not have permission to manage user roles.');
}
$companyId = oecrm_current_company_id($conn);
$roleFlash = $_SESSION['role_flash'] ?? null;
$roleError = $_SESSION['role_error'] ?? '';
unset($_SESSION['role_flash'], $_SESSION['role_error']);
$stmt = mysqli_prepare($conn, 'SELECT id,name,org_id FROM user_type WHERE is_deleted=0 AND org_id IN (0,?) ORDER BY name');
mysqli_stmt_bind_param($stmt, 'i', $companyId);
mysqli_stmt_execute($stmt);
$roles = mysqli_stmt_get_result($stmt);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if (!empty($roleFlash['message'])): ?>
        <div class="alert alert-<?php echo oecrm_h($roleFlash['type']); ?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo oecrm_h($roleFlash['message']); ?>
        </div>
    <?php endif; ?>
    <?php if ($roleError): ?>
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo oecrm_h($roleError); ?>
        </div>
    <?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading panel-box" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <h4>User Roles</h4>
            <?php if (oecrm_role_can_manage($conn, 'create')): ?>
                <a href="add_role.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Role</a>
            <?php endif; ?>
        </div>
        <div class="panel-body manage_project">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="role_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="32">#</th>
                            <th>Name</th>
                            <th class="text-center" width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                        <?php if ($roles && mysqli_num_rows($roles) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($roles)): ?>
                                <tr>
                                    <td class="text-center"><?php echo ++$counter; ?></td>
                                    <td><?php echo oecrm_h($row['name']); ?></td>
                                    <td class="text-center">
                                        <?php if (oecrm_role_can_manage($conn, 'manage_permissions')): ?>
                                            <a class="oecrm-action-btn" href="role_permissions.php?role_id=<?php echo (int) $row['id']; ?>" title="Permissions"><i class="fa fa-key"></i></a>
                                        <?php endif; ?>
                                        <?php if (oecrm_role_can_manage($conn, 'edit')): ?>
                                            <a class="oecrm-action-btn" href="add_role.php?id=<?php echo (int) $row['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                        <?php endif; ?>
                                        <?php if (oecrm_role_can_manage($conn, 'delete')): ?>
                                            <form method="post" action="action_delete_role.php" style="display:inline;">
                                                <?php echo oecrm_csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                                <button type="submit" class="btn btn-link oecrm-action-btn oecrm-action-danger" data-confirm="Are you sure you want to delete this role?" title="Delete"><i class="fa fa-trash-o"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center">No roles found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php mysqli_stmt_close($stmt); include 'footer.php'; ?>


