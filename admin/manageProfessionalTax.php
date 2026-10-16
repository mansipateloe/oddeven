<?php
ob_start();
$active_menu = 'finance';
$active_submenu = 'professional_tax';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();

function oecrm_professional_tax_flash($type, $message)
{
    $_SESSION['professional_tax_flash'] = ['type' => $type, 'message' => $message];
}

function oecrm_professional_tax_redirect($editId = 0)
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && in_array($_POST['action'] ?? '', ['add_professional_tax', 'update_professional_tax'], true)) {
        if (($_SESSION['professional_tax_flash']['type'] ?? '') === 'success') {
            unset($_SESSION['professional_tax_form']);
        } else {
            $_SESSION['professional_tax_form'] = ['id' => (int) $editId, 'company_id' => oecrm_current_company_id($GLOBALS['conn'])];
            foreach (['startingAmount', 'endingAmount', 'professionalTax'] as $field) {
                $_SESSION['professional_tax_form'][$field] = is_scalar($_POST[$field] ?? null) ? (string) $_POST[$field] : '';
            }
        }
    }
    header('Location: manageProfessionalTax.php' . ($editId > 0 ? '?edit=' . (int) $editId : ''));
    exit;
}

function oecrm_professional_tax_can_manage($conn, $action = 'view')
{
    return oecrm_is_super_admin()
        || oecrm_can($conn, 'payroll', $action)
        || oecrm_can($conn, 'finance', $action)
        || oecrm_can($conn, 'salary_structures', $action)
        || oecrm_legacy_can($conn, 'settings')
        || oecrm_legacy_can($conn, 'employee')
        || oecrm_legacy_can($conn, 'invocie');
}

function oecrm_professional_tax_require($conn, $action = 'view')
{
    if (!oecrm_professional_tax_can_manage($conn, $action)) {
        http_response_code(403);
        exit('You do not have permission to manage professional tax.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'add_professional_tax' || $action === 'update_professional_tax') {
        try {
        $isEdit = $action === 'update_professional_tax';
        oecrm_professional_tax_require($conn, $isEdit ? 'edit' : 'create');
        $id = $isEdit ? (int) ($_POST['id'] ?? 0) : 0;
        if ($isEdit && $id <= 0) {
            oecrm_professional_tax_flash('danger', 'Invalid Professional Tax record selected.');
            oecrm_professional_tax_redirect();
        }
        $old = null;
        if ($isEdit) {
            $stmt = mysqli_prepare($conn, 'SELECT id,startingAmount,endingAmount,professionalTax FROM professionaltaxtbl WHERE id=? LIMIT 1');
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $old = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
            if (!$old) {
                oecrm_professional_tax_flash('warning', 'Professional Tax record not found.');
                oecrm_professional_tax_redirect();
            }
        }
        $values = [];
        foreach (['startingAmount', 'endingAmount', 'professionalTax'] as $key) {
            $raw = trim((string) ($_POST[$key] ?? ''));
            $value = preg_match('/^[0-9]+$/D', $raw) ? (float) $raw : -1;
            if ($value < 0 || $value > 2147483647) {
                oecrm_professional_tax_flash('danger', 'From, To and Tax must be non-negative whole amounts within the supported range.');
                oecrm_professional_tax_redirect($id);
            }
            $values[$key] = (int) $value;
        }
        $startingAmount = $values['startingAmount'];
        $endingAmount = $values['endingAmount'];
        $professionalTax = $values['professionalTax'];
        if ($endingAmount < $startingAmount) {
            oecrm_professional_tax_flash('warning', 'To amount must be greater than or equal to From amount.');
            oecrm_professional_tax_redirect($id);
        }
        // An edited slab must be excluded from its own overlap check.
        $overlapStmt = mysqli_prepare($conn, 'SELECT id FROM professionaltaxtbl WHERE startingAmount<=? AND endingAmount>=? AND id<>? LIMIT 1');
        mysqli_stmt_bind_param($overlapStmt, 'iii', $endingAmount, $startingAmount, $id);
        mysqli_stmt_execute($overlapStmt);
        $overlap = mysqli_fetch_assoc(mysqli_stmt_get_result($overlapStmt));
        mysqli_stmt_close($overlapStmt);
        if ($overlap) {
            oecrm_professional_tax_flash('warning', 'This salary range overlaps an existing professional tax record.');
            oecrm_professional_tax_redirect($id);
        }
        if ($isEdit) {
            $stmt = mysqli_prepare($conn, 'UPDATE professionaltaxtbl SET startingAmount=?,endingAmount=?,professionalTax=? WHERE id=?');
            mysqli_stmt_bind_param($stmt, 'iiii', $startingAmount, $endingAmount, $professionalTax, $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO professionaltaxtbl(startingAmount,endingAmount,professionalTax) VALUES(?,?,?)');
            mysqli_stmt_bind_param($stmt, 'iii', $startingAmount, $endingAmount, $professionalTax);
        }
        $saved = mysqli_stmt_execute($stmt);
        if (!$isEdit) $id = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        if (!$saved) {
            oecrm_professional_tax_flash('danger', 'Professional Tax could not be saved.');
            oecrm_professional_tax_redirect($isEdit ? $id : 0);
        }
        try {
        oecrm_audit($conn, 'payroll', $isEdit ? 'edit' : 'create', 'professional_tax', $id, $isEdit ? 'Professional tax slab updated' : 'Professional tax slab created', $old, $values);
        } catch (Throwable $auditError) { error_log('Professional tax audit failed: ' . $auditError->getMessage()); }
        oecrm_professional_tax_flash('success', $isEdit ? 'Professional Tax record updated successfully.' : 'Professional Tax record added successfully.');
        oecrm_professional_tax_redirect();
        } catch (Throwable $saveError) {
            error_log('Professional tax save failed: ' . $saveError->getMessage());
            oecrm_professional_tax_flash('danger', 'Professional Tax could not be saved. Your entered values have been kept.');
            oecrm_professional_tax_redirect(!empty($isEdit) ? (int) ($_POST['id'] ?? 0) : 0);
        }
    }
    if ($action === 'delete_professional_tax') {
        oecrm_professional_tax_require($conn, 'edit');
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            oecrm_professional_tax_flash('danger', 'Invalid Professional Tax record selected.');
            oecrm_professional_tax_redirect();
        }

        $oldStmt = mysqli_prepare($conn, 'SELECT * FROM professionaltaxtbl WHERE id=? LIMIT 1');
        mysqli_stmt_bind_param($oldStmt, 'i', $id);
        mysqli_stmt_execute($oldStmt);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($oldStmt));
        mysqli_stmt_close($oldStmt);

        if (!$old) {
            oecrm_professional_tax_flash('warning', 'Professional Tax record not found.');
            oecrm_professional_tax_redirect();
        }

        $deleteStmt = mysqli_prepare($conn, 'DELETE FROM professionaltaxtbl WHERE id=?');
        mysqli_stmt_bind_param($deleteStmt, 'i', $id);
        $deleted = mysqli_stmt_execute($deleteStmt);
        $affected = mysqli_stmt_affected_rows($deleteStmt);
        mysqli_stmt_close($deleteStmt);

        if (!$deleted || $affected < 1) {
            oecrm_professional_tax_flash('danger', 'Professional Tax record could not be deleted.');
            oecrm_professional_tax_redirect();
        }

        oecrm_audit($conn, 'payroll', 'delete', 'professional_tax', $id, 'Professional tax slab deleted', $old, null);
        oecrm_professional_tax_flash('success', 'Professional Tax record deleted successfully.');
        oecrm_professional_tax_redirect();
    }
}

oecrm_professional_tax_require($conn, 'view');
$editId = (int) ($_GET['edit'] ?? 0);
$taxRecord = ['startingAmount' => '', 'endingAmount' => '', 'professionalTax' => ''];
if ($editId > 0) {
    oecrm_professional_tax_require($conn, 'edit');
    $editStmt = mysqli_prepare($conn, 'SELECT id,startingAmount,endingAmount,professionalTax FROM professionaltaxtbl WHERE id=? LIMIT 1');
    mysqli_stmt_bind_param($editStmt, 'i', $editId);
    mysqli_stmt_execute($editStmt);
    $taxRecord = mysqli_fetch_assoc(mysqli_stmt_get_result($editStmt));
    mysqli_stmt_close($editStmt);
    if (!$taxRecord) {
        oecrm_professional_tax_flash('warning', 'Professional Tax record not found.');
        oecrm_professional_tax_redirect();
    }
}
$taxDraft = $_SESSION['professional_tax_form'] ?? [];
if ((int) ($taxDraft['id'] ?? -1) === $editId && (int) ($taxDraft['company_id'] ?? 0) === oecrm_current_company_id($conn)) {
    foreach (['startingAmount', 'endingAmount', 'professionalTax'] as $field) {
        $taxRecord[$field] = $taxDraft[$field] ?? $taxRecord[$field];
    }
}
unset($_SESSION['professional_tax_form']);
$professionalTaxFlash = $_SESSION['professional_tax_flash'] ?? null;
unset($_SESSION['professional_tax_flash']);
include 'header.php';
$resultProTax = mysqli_query($conn, 'SELECT id, startingAmount, endingAmount, professionalTax FROM professionaltaxtbl ORDER BY startingAmount, endingAmount');
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if (!empty($professionalTaxFlash['message'])): ?>
        <div class="alert alert-<?php echo oecrm_h($professionalTaxFlash['type']); ?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo oecrm_h($professionalTaxFlash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="panel panel-default managetax">
        <div class="panel-heading"><?php echo $editId > 0 ? 'Edit Professional Tax' : 'Add New Professional Tax'; ?></div>
        <div class="panel-body">
            <div class="search_box_area">
                <form method="POST" action="manageProfessionalTax.php">
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="action" value="<?php echo $editId > 0 ? 'update_professional_tax' : 'add_professional_tax'; ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $editId; ?>">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group col-lg-2">
                                <label>From <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" name="startingAmount" value="<?php echo oecrm_h($taxRecord['startingAmount']); ?>" max="2147483647" step="1" placeholder="Starting Amount" required>
                            </div>
                            <div class="form-group col-lg-2">
                                <label>To <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" name="endingAmount" value="<?php echo oecrm_h($taxRecord['endingAmount']); ?>" max="2147483647" step="1" placeholder="Ending Amount" required>
                            </div>
                            <div class="form-group col-lg-2">
                                <label>Tax <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" name="professionalTax" value="<?php echo oecrm_h($taxRecord['professionalTax']); ?>" max="2147483647" step="1" placeholder="Tax" required>
                            </div>
                            <div class="form-group col-lg-1">
                                <br>
                                <button type="submit" class="btn btn-primary profeesionaltax_btn"><i class="fa <?php echo $editId > 0 ? 'fa-save' : 'fa-plus'; ?>"></i> <?php echo $editId > 0 ? 'Update' : 'Add'; ?></button>
                            </div>
                            <div class="form-group col-lg-1">
                                <br>
                                <?php if ($editId > 0): ?><a class="btn btn-default cancel_btn" href="manageProfessionalTax.php">Cancel</a><?php else: ?><input class="btn btn-danger cancel_btn" type="reset" value="Cancel"><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Professional Tax Table</div>
                        <div class="panel-body table-responsive">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="center-bold">From</th>
                                        <th class="center-bold">To</th>
                                        <th class="center-bold">Tax Amount</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($resultProTax && mysqli_num_rows($resultProTax) > 0): ?>
                                        <?php while ($rowProTax = mysqli_fetch_assoc($resultProTax)): ?>
                                            <tr>
                                                <td class="center-bold"><?php echo oecrm_h($rowProTax['startingAmount']); ?></td>
                                                <td class="center-bold"><?php echo oecrm_h($rowProTax['endingAmount']); ?></td>
                                                <td class="center-bold"><?php echo oecrm_h($rowProTax['professionalTax']); ?></td>
                                                <td class="center text-center">
                                                    <?php if (oecrm_professional_tax_can_manage($conn, 'edit')): ?>
                                                        <a class="oecrm-action-btn" href="manageProfessionalTax.php?edit=<?php echo (int) $rowProTax['id']; ?>" title="Edit Professional Tax" aria-label="Edit Professional Tax"><i class="fa fa-pencil"></i></a>
                                                    <?php endif; ?>
                                                    <form method="post" action="manageProfessionalTax.php" style="display:inline;">
                                                        <?php echo oecrm_csrf_field(); ?>
                                                        <input type="hidden" name="action" value="delete_professional_tax">
                                                        <input type="hidden" name="id" value="<?php echo (int) $rowProTax['id']; ?>">
                                                        <button type="submit" class="btn btn-link oecrm-action-btn oecrm-action-danger" data-confirm="Are you sure you want to delete this Professional Tax record?" title="Delete"><i class="fa fa-trash-o"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center">No professional tax records found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
