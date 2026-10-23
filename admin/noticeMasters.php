<?php
$active_menu = 'notices';
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();
oecrm_require_permission($conn, 'notices', 'view');
$companyId = oecrm_current_company_id($conn);

function oecrm_notice_master_delete($conn, $companyId, $kind, $id)
{
    $tables = ['type' => 'notice_types', 'category' => 'notice_categories'];
    if (!isset($tables[$kind]) || $id <= 0) throw new InvalidArgumentException('Select a valid notice type or category.');
    $table = $tables[$kind];
    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, "SELECT * FROM $table WHERE id=? AND company_id=? AND status=1 FOR UPDATE");
        mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
        mysqli_stmt_execute($stmt);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if (!$old) throw new InvalidArgumentException('Notice type or category not found or already deleted.');
        // Retain references and names for notices already created.
        $stmt = mysqli_prepare($conn, "UPDATE $table SET status=0 WHERE id=? AND company_id=?");
        mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
        if (!mysqli_stmt_execute($stmt)) throw new RuntimeException(mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
        return $old;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
    oecrm_require_permission($conn, 'notices', 'edit');
    $kind = (string) ($_POST['kind'] ?? '');
    $action = (string) ($_POST['action'] ?? 'add');
    $tables = ['type' => 'notice_types', 'category' => 'notice_categories'];
    try {
        if (!isset($tables[$kind])) throw new InvalidArgumentException('Invalid notice master selected.');
        $table = $tables[$kind];
        $label = $kind === 'type' ? 'Notice Type' : 'Notice Category';
        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $old = oecrm_notice_master_delete($conn, $companyId, $kind, $id);
            oecrm_audit($conn, 'notices', 'delete', $table, $id, $label . ' deleted', $old, ['status' => 0]);
            $_SESSION['notice_type_flash'] = $label . ' deleted. Existing notices are preserved.';
        } elseif ($action === 'add') {
            $name = trim((string) ($_POST['name'] ?? ''));
            if ($name === '' || strlen($name) > 100) throw new InvalidArgumentException('Enter a name of up to 100 characters.');
            if ($kind === 'type') {
                $key = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '_', $name), '_'));
                if ($key === '' || strlen($key) > 40) throw new InvalidArgumentException('The notice type must create a valid key of up to 40 characters.');
                $stmt = mysqli_prepare($conn, 'SELECT id,status FROM notice_types WHERE company_id=? AND (LOWER(name)=LOWER(?) OR type_key=?) LIMIT 1');
                mysqli_stmt_bind_param($stmt, 'iss', $companyId, $name, $key);
            } else {
                $stmt = mysqli_prepare($conn, 'SELECT id,status FROM notice_categories WHERE company_id=? AND LOWER(name)=LOWER(?) LIMIT 1');
                mysqli_stmt_bind_param($stmt, 'is', $companyId, $name);
            }
            mysqli_stmt_execute($stmt);
            $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
            if ($exists && (int) $exists['status'] === 1) throw new InvalidArgumentException($label . ' already exists.');
            if ($exists) {
                // Re-adding an intentionally removed master restores its identity.
                $id = (int) $exists['id'];
                $stmt = mysqli_prepare($conn, "UPDATE $table SET status=1 WHERE id=? AND company_id=?");
                mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $_SESSION['notice_type_flash'] = $label . ' restored successfully.';
            } else {
                if ($kind === 'type') {
                    $stmt = mysqli_prepare($conn, 'INSERT INTO notice_types(company_id,type_key,name) VALUES(?,?,?)');
                    mysqli_stmt_bind_param($stmt, 'iss', $companyId, $key, $name);
                } else {
                    $stmt = mysqli_prepare($conn, 'INSERT INTO notice_categories(company_id,name) VALUES(?,?)');
                    mysqli_stmt_bind_param($stmt, 'is', $companyId, $name);
                }
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $_SESSION['notice_type_flash'] = $label . ' added successfully.';
            }
        } else {
            throw new InvalidArgumentException('Invalid notice master action.');
        }
    } catch (Throwable $e) {
        error_log('Notice master action failed: ' . $e->getMessage());
        $_SESSION['notice_type_flash'] = $e instanceof InvalidArgumentException ? $e->getMessage() : 'The notice type or category could not be saved. Please try again.';
    }
    header('Location:noticeMasters.php');
    exit;
}
$flash = $_SESSION['notice_type_flash'] ?? '';
unset($_SESSION['notice_type_flash']);
$types = mysqli_query($conn, 'SELECT * FROM notice_types WHERE company_id=' . (int) $companyId . ' AND status=1 ORDER BY name');
$categories = mysqli_query($conn, 'SELECT * FROM notice_categories WHERE company_id=' . (int) $companyId . ' AND status=1 ORDER BY name');
$canEdit = oecrm_can($conn, 'notices', 'edit');
include 'header.php';
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="foundation-titlebar"><h2>Notice Types &amp; Categories</h2>
    <a href="manageNotice.php"><i class="fa fa-arrow-left"></i> Notices</a></div>
    <?php if ($flash): ?><div class="alert alert-info" role="status"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="row">
        <?php foreach (['type' => ['Notice Types', $types], 'category' => ['Notice Categories', $categories]] as $kind => $section): ?>
        <div class="col-md-6"><div class="panel panel-default">
            <div class="panel-heading"><?php echo $section[0]; ?></div>
            <div class="panel-body table-responsive">
                <?php if ($canEdit): ?>
                <form method="post" class="form-inline">
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="kind" value="<?php echo $kind; ?>">
                    <input class="form-control" name="name" maxlength="100" placeholder="<?php echo $kind === 'type' ? 'Type name' : 'Category name'; ?>" aria-label="<?php echo $kind === 'type' ? 'Notice type name' : 'Notice category name'; ?>" required>
                    <button class="btn btn-primary">Add</button>
                </form><hr>
                <?php endif; ?>
                <table class="table">
                    <thead><tr><th>Name</th><?php if ($kind === 'type'): ?><th>Key</th><?php endif; ?><th>Action</th></tr></thead>
                    <tbody>
                    <?php if (!mysqli_num_rows($section[1])): ?><tr><td colspan="<?php echo $kind === 'type' ? 3 : 2; ?>">No <?php echo strtolower($section[0]); ?> found.</td></tr><?php endif; ?>
                    <?php while ($row = mysqli_fetch_assoc($section[1])): ?>
                        <tr>
                            <td><?php echo oecrm_h($row['name']); ?></td>
                            <?php if ($kind === 'type'): ?><td><code><?php echo oecrm_h($row['type_key']); ?></code></td><?php endif; ?>
                            <td>
                                <?php if ($canEdit): ?>
                                <form method="post" style="display:inline;margin:0;" data-confirm="<?php echo oecrm_h('Delete ' . ($kind === 'type' ? 'notice type' : 'notice category') . ' "' . $row['name'] . '"? Existing notices will be preserved.'); ?>">
                                    <?php echo oecrm_csrf_field(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="kind" value="<?php echo $kind; ?>">
                                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                    <button type="submit" class="icon-action danger" title="Delete" aria-label="<?php echo oecrm_h('Delete ' . $row['name']); ?>"><i class="fa fa-trash-o"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div></div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'footer.php'; ?>