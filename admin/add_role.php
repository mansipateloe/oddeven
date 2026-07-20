<?php
ob_start();
$active_menu = 'role';
$active_submenu = 'add_role';
require_once __DIR__ . '/../security.php';
include __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();

function oecrm_role_flash($type, $message)
{
    $_SESSION['role_flash'] = ['type' => $type, 'message' => $message];
}

function oecrm_role_redirect($id = 0)
{
    header('Location: ' . ($id > 0 ? 'add_role.php?id=' . (int) $id : 'all_user_roles.php'));
    exit;
}

function oecrm_role_can_manage($conn, $action)
{
    return oecrm_is_super_admin()
        || oecrm_can($conn, 'roles', $action)
        || oecrm_legacy_can($conn, 'employee')
        || oecrm_legacy_can($conn, 'settings');
}

function oecrm_role_require_manage($conn, $action)
{
    if (!oecrm_role_can_manage($conn, $action)) {
        http_response_code(403);
        exit('You do not have permission to manage user roles.');
    }
}

$companyId = oecrm_current_company_id($conn);
$roleId = (int) ($_GET['id'] ?? $_POST['role_id'] ?? 0);
$isEdit = $roleId > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveRole'])) {
    oecrm_require_csrf();
    $roleId = (int) ($_POST['role_id'] ?? 0);
    $isEdit = $roleId > 0;
    oecrm_role_require_manage($conn, $isEdit ? 'edit' : 'create');

    $name = trim((string) ($_POST['name'] ?? ''));
    $name = preg_replace('/\s+/', ' ', $name);

    if ($name === '') {
        oecrm_role_flash('danger', 'Role name is required.');
        oecrm_role_redirect($roleId);
    }

    if ($isEdit) {
        $roleStmt = mysqli_prepare($conn, 'SELECT id,name,org_id FROM user_type WHERE id=? AND is_deleted=0 AND org_id IN (0,?) LIMIT 1');
        mysqli_stmt_bind_param($roleStmt, 'ii', $roleId, $companyId);
        mysqli_stmt_execute($roleStmt);
        $existingRole = mysqli_fetch_assoc(mysqli_stmt_get_result($roleStmt));
        mysqli_stmt_close($roleStmt);
        if (!$existingRole) {
            oecrm_role_flash('danger', 'Role not found.');
            oecrm_role_redirect();
        }
    }

    $duplicateSql = $isEdit
        ? 'SELECT id FROM user_type WHERE LOWER(name)=LOWER(?) AND is_deleted=0 AND org_id IN (0,?) AND id<>? LIMIT 1'
        : 'SELECT id FROM user_type WHERE LOWER(name)=LOWER(?) AND is_deleted=0 AND org_id IN (0,?) LIMIT 1';
    $duplicateStmt = mysqli_prepare($conn, $duplicateSql);
    if ($isEdit) {
        mysqli_stmt_bind_param($duplicateStmt, 'sii', $name, $companyId, $roleId);
    } else {
        mysqli_stmt_bind_param($duplicateStmt, 'si', $name, $companyId);
    }
    mysqli_stmt_execute($duplicateStmt);
    $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($duplicateStmt));
    mysqli_stmt_close($duplicateStmt);

    if ($duplicate) {
        oecrm_role_flash('warning', 'Role name already exists.');
        oecrm_role_redirect($roleId);
    }

    $actor = (int) ($_SESSION['adminId'] ?? 0);
    $now = date('Y-m-d H:i:s');

    if ($isEdit) {
        $stmt = mysqli_prepare($conn, 'UPDATE user_type SET name=?,updated_by=?,updated_at=? WHERE id=? AND is_deleted=0 AND org_id IN (0,?)');
        mysqli_stmt_bind_param($stmt, 'sisii', $name, $actor, $now, $roleId, $companyId);
        $saved = mysqli_stmt_execute($stmt);
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        if (!$saved || $affected < 0) {
            oecrm_role_flash('danger', 'Role could not be updated.');
            oecrm_role_redirect($roleId);
        }
        oecrm_audit($conn, 'roles', 'edit', 'role', $roleId, 'Role updated', $existingRole, ['name' => $name]);
        oecrm_role_flash('success', 'Role updated successfully.');
    } else {
        $stmt = mysqli_prepare($conn, 'INSERT INTO user_type(name,org_id,is_deleted,created_by,updated_by,created_at,updated_at) VALUES(?, ?, 0, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'siiiss', $name, $companyId, $actor, $actor, $now, $now);
        $saved = mysqli_stmt_execute($stmt);
        $roleId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        if (!$saved || $roleId <= 0) {
            oecrm_role_flash('danger', 'Role could not be created.');
            oecrm_role_redirect();
        }
        oecrm_audit($conn, 'roles', 'create', 'role', $roleId, 'Role created', null, ['name' => $name]);
        oecrm_role_flash('success', 'Role created successfully.');
    }

    oecrm_role_redirect();
}

$role = ['id' => 0, 'name' => ''];
if ($isEdit) {
    oecrm_role_require_manage($conn, 'edit');
    $stmt = mysqli_prepare($conn, 'SELECT id,name FROM user_type WHERE id=? AND is_deleted=0 AND org_id IN (0,?) LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ii', $roleId, $companyId);
    mysqli_stmt_execute($stmt);
    $role = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$role) {
        $_SESSION['role_flash'] = ['type' => 'danger', 'message' => 'Role not found.'];
        header('Location: all_user_roles.php');
        exit;
    }
} else {
    oecrm_role_require_manage($conn, 'create');
}

include 'header.php';
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="panel panel-default">
        <div class="panel-heading panel-box">
            <h4><?php echo $isEdit ? 'Edit Role' : 'Add Role'; ?></h4>
        </div>
        <div class="panel-body manage_project">
            <form id="roleForm" method="post" action="add_role.php">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="role_id" value="<?php echo (int) $role['id']; ?>">
                <div class="form-group col-sm-12">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="<?php echo oecrm_h($role['name']); ?>" placeholder="Name" id="name" name="name" maxlength="100" required>
                </div>
                <div class="form-group col-sm-12">
                    <button type="submit" name="saveRole" value="1" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $isEdit ? 'Update Role' : 'Create Role'; ?></button>
                    <a href="all_user_roles.php" class="btn btn-default cancel_btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>


