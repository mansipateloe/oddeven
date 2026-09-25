<?php
ob_start();
$active_menu = 'finance';
$active_submenu = 'add_bank_details';
require_once __DIR__ . '/../security.php';
include __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();

function oecrm_bank_details_flash($type, $message)
{
    $_SESSION['bank_details_flash'] = ['type' => $type, 'message' => $message];
}

function oecrm_bank_details_redirect($editId = 0)
{
    header('Location: addBankDetails.php' . ($editId > 0 ? '?edit=' . (int) $editId : ''));
    exit;
}

function oecrm_bank_details_can_manage($conn, $action = 'view')
{
    return oecrm_is_super_admin()
        || oecrm_can($conn, 'finance', $action)
        || oecrm_can($conn, 'bank_reconciliation', $action)
        || oecrm_can($conn, 'settings', $action)
        || oecrm_legacy_can($conn, 'settings')
        || oecrm_legacy_can($conn, 'invocie')
        || oecrm_legacy_can($conn, 'deposit')
        || oecrm_legacy_can($conn, 'expense');
}

function oecrm_bank_details_require($conn, $action = 'view')
{
    if (!oecrm_bank_details_can_manage($conn, $action)) {
        http_response_code(403);
        exit('You do not have permission to manage bank details.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save_bank_details') {
        $id = (int) ($_POST['id'] ?? 0);
        $isEdit = $id > 0;
        oecrm_bank_details_require($conn, $isEdit ? 'edit' : 'create');

        $bankName = preg_replace('/\s+/', ' ', trim((string) ($_POST['bank_name'] ?? '')));
        if ($bankName === '') {
            oecrm_bank_details_flash('danger', 'Bank name is required.');
            oecrm_bank_details_redirect($id);
        }

        if (strlen($bankName) > 255) {
            oecrm_bank_details_flash('danger', 'Bank name must be 255 characters or less.');
            oecrm_bank_details_redirect($id);
        }

        if ($isEdit) {
            $oldStmt = mysqli_prepare($conn, 'SELECT bank_id, bank_name FROM bank_details WHERE bank_id=? LIMIT 1');
            mysqli_stmt_bind_param($oldStmt, 'i', $id);
            mysqli_stmt_execute($oldStmt);
            $old = mysqli_fetch_assoc(mysqli_stmt_get_result($oldStmt));
            mysqli_stmt_close($oldStmt);
            if (!$old) {
                oecrm_bank_details_flash('warning', 'Bank record not found.');
                oecrm_bank_details_redirect();
            }
        }

        $duplicateSql = $isEdit
            ? 'SELECT bank_id FROM bank_details WHERE LOWER(bank_name)=LOWER(?) AND bank_id<>? LIMIT 1'
            : 'SELECT bank_id FROM bank_details WHERE LOWER(bank_name)=LOWER(?) LIMIT 1';
        $duplicateStmt = mysqli_prepare($conn, $duplicateSql);
        if ($isEdit) {
            mysqli_stmt_bind_param($duplicateStmt, 'si', $bankName, $id);
        } else {
            mysqli_stmt_bind_param($duplicateStmt, 's', $bankName);
        }
        mysqli_stmt_execute($duplicateStmt);
        $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($duplicateStmt));
        mysqli_stmt_close($duplicateStmt);

        if ($duplicate) {
            oecrm_bank_details_flash('warning', 'This bank name already exists.');
            oecrm_bank_details_redirect($id);
        }

        if ($isEdit) {
            $stmt = mysqli_prepare($conn, 'UPDATE bank_details SET bank_name=? WHERE bank_id=?');
            mysqli_stmt_bind_param($stmt, 'si', $bankName, $id);
            $saved = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if (!$saved) {
                oecrm_bank_details_flash('danger', 'Bank record could not be updated.');
                oecrm_bank_details_redirect($id);
            }
            oecrm_audit($conn, 'finance', 'edit', 'bank_details', $id, 'Bank details updated', $old, ['bank_name' => $bankName]);
            oecrm_bank_details_flash('success', 'Bank record updated successfully.');
            oecrm_bank_details_redirect();
        }

        $stmt = mysqli_prepare($conn, 'INSERT INTO bank_details(bank_name) VALUES(?)');
        mysqli_stmt_bind_param($stmt, 's', $bankName);
        $saved = mysqli_stmt_execute($stmt);
        $newId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        if (!$saved) {
            oecrm_bank_details_flash('danger', 'Bank record could not be saved.');
            oecrm_bank_details_redirect();
        }

        oecrm_audit($conn, 'finance', 'create', 'bank_details', $newId, 'Bank details created', null, ['bank_name' => $bankName]);
        oecrm_bank_details_flash('success', 'Bank record added successfully.');
        oecrm_bank_details_redirect();
    }

    if ($action === 'delete_bank_details') {
        oecrm_bank_details_require($conn, 'delete');
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            oecrm_bank_details_flash('danger', 'Invalid bank record selected.');
            oecrm_bank_details_redirect();
        }

        $oldStmt = mysqli_prepare($conn, 'SELECT bank_id, bank_name FROM bank_details WHERE bank_id=? LIMIT 1');
        mysqli_stmt_bind_param($oldStmt, 'i', $id);
        mysqli_stmt_execute($oldStmt);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($oldStmt));
        mysqli_stmt_close($oldStmt);

        if (!$old) {
            oecrm_bank_details_flash('warning', 'Bank record not found.');
            oecrm_bank_details_redirect();
        }

        $deleteStmt = mysqli_prepare($conn, 'DELETE FROM bank_details WHERE bank_id=?');
        mysqli_stmt_bind_param($deleteStmt, 'i', $id);
        $deleted = mysqli_stmt_execute($deleteStmt);
        $affected = mysqli_stmt_affected_rows($deleteStmt);
        mysqli_stmt_close($deleteStmt);

        if (!$deleted || $affected < 1) {
            oecrm_bank_details_flash('danger', 'Bank record could not be deleted.');
            oecrm_bank_details_redirect();
        }

        oecrm_audit($conn, 'finance', 'delete', 'bank_details', $id, 'Bank details deleted', $old, null);
        oecrm_bank_details_flash('success', 'Bank record deleted successfully.');
        oecrm_bank_details_redirect();
    }
}

oecrm_bank_details_require($conn, 'view');
$editId = (int) ($_GET['edit'] ?? 0);
$bank = ['bank_id' => 0, 'bank_name' => ''];
if ($editId > 0) {
    oecrm_bank_details_require($conn, 'edit');
    $editStmt = mysqli_prepare($conn, 'SELECT bank_id, bank_name FROM bank_details WHERE bank_id=? LIMIT 1');
    mysqli_stmt_bind_param($editStmt, 'i', $editId);
    mysqli_stmt_execute($editStmt);
    $bank = mysqli_fetch_assoc(mysqli_stmt_get_result($editStmt));
    mysqli_stmt_close($editStmt);
    if (!$bank) {
        oecrm_bank_details_flash('warning', 'Bank record not found.');
        oecrm_bank_details_redirect();
    }
}

$flash = $_SESSION['bank_details_flash'] ?? null;
unset($_SESSION['bank_details_flash']);
include 'header.php';
$bankRows = mysqli_query($conn, 'SELECT bank_id, bank_name FROM bank_details ORDER BY bank_name');
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if (!empty($flash['message'])): ?>
        <div class="alert alert-<?php echo oecrm_h($flash['type']); ?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo oecrm_h($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <h4>Manage Bank Details</h4>
            <?php if ($flash): ?>
                <div class="alert alert-info"><?php echo oecrm_h($flash); ?></div>
            <?php endif; ?>
            <div class="dataTablesbox2 dataTablesbox">
                <form role="form" method="POST" action="addBankDetails.php" novalidate>
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="action" value="save_bank_details">
                    <input type="hidden" name="id" value="<?php echo (int) $bank['bank_id']; ?>">
                    <div class="row">
                        <div class="col-lg-3">
                            <label>Name <span class="text-danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="bank_name" value="<?php echo oecrm_h($bank['bank_name']); ?>" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-lg-3" align="right">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary viewreport"><i class="fa fa-save"></i> <?php echo $editId > 0 ? 'Update' : 'Add'; ?></button>
                                <?php if ($editId > 0): ?>
                                    <a class="btn btn-default cancel_btn" href="addBankDetails.php">Cancel</a>
                                <?php else: ?>
                                    <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Bank Table</div>
                <div class="panel-body table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bankRows && mysqli_num_rows($bankRows) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($bankRows)): ?>
                                    <tr>
                                        <td><?php echo oecrm_h($row['bank_name']); ?></td>
                                        <td class="center text-center">
                                            <?php if (oecrm_bank_details_can_manage($conn, 'edit')): ?>
                                                <a class="oecrm-action-btn" href="addBankDetails.php?edit=<?php echo (int) $row['bank_id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <?php endif; ?>
                                            <?php if (oecrm_bank_details_can_manage($conn, 'delete')): ?>
                                                <form method="post" action="addBankDetails.php" style="display:inline;">
                                                    <?php echo oecrm_csrf_field(); ?>
                                                    <input type="hidden" name="action" value="delete_bank_details">
                                                    <input type="hidden" name="id" value="<?php echo (int) $row['bank_id']; ?>">
                                                    <button type="submit" class="btn btn-link oecrm-action-btn oecrm-action-danger" data-confirm="Are you sure you want to delete this bank record?" title="Delete"><i class="fa fa-trash-o"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center">No bank records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../vendor/metisMenu/metisMenu.min.js"></script>
<script src="../vendor/raphael/raphael.min.js"></script>
<script src="../vendor/morrisjs/morris.min.js"></script>
<script src="../data/morris-data.js"></script>
<script src="../dist/js/sb-admin-2.js"></script>

<?php include 'footer.php'; ?>
