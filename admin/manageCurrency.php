<?php
ob_start();
$active_menu = 'setting';
$active_submenu = 'setting_currency';
require_once __DIR__ . '/../security.php';
include __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();

function oecrm_currency_flash($type, $message)
{
    $_SESSION['currency_flash'] = ['type' => $type, 'message' => $message];
}

function oecrm_currency_redirect($editId = 0)
{
    header('Location: manageCurrency.php' . ($editId > 0 ? '?edit=' . (int) $editId : ''));
    exit;
}

function oecrm_currency_can_manage($conn, $action = 'view')
{
    return oecrm_is_super_admin()
        || oecrm_can($conn, 'finance', $action)
        || oecrm_can($conn, 'settings', $action)
        || oecrm_legacy_can($conn, 'settings')
        || oecrm_legacy_can($conn, 'invocie')
        || oecrm_legacy_can($conn, 'expense');
}

function oecrm_currency_require_manage($conn, $action = 'view')
{
    if (!oecrm_currency_can_manage($conn, $action)) {
        http_response_code(403);
        exit('You do not have permission to manage currencies.');
    }
}

function oecrm_currency_normalize_name($name)
{
    return preg_replace('/\s+/', ' ', trim((string) $name));
}

function oecrm_currency_normalize_symbol($symbol)
{
    return trim((string) $symbol);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    oecrm_require_csrf();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save_currency') {
        $id = (int) ($_POST['id'] ?? 0);
        $isEdit = $id > 0;
        oecrm_currency_require_manage($conn, $isEdit ? 'edit' : 'create');

        $name = oecrm_currency_normalize_name($_POST['name'] ?? '');
        $symbol = oecrm_currency_normalize_symbol($_POST['symbol'] ?? '');
        $rate = trim((string) ($_POST['rate'] ?? ''));

        if ($name === '' || $symbol === '' || $rate === '') {
            oecrm_currency_flash('danger', 'Currency name, symbol and rate are required.');
            oecrm_currency_redirect($id);
        }

        if (strlen($name) > 100) {
            oecrm_currency_flash('danger', 'Currency name must be 100 characters or less.');
            oecrm_currency_redirect($id);
        }

        if (strlen($symbol) > 20) {
            oecrm_currency_flash('danger', 'Currency symbol/code must be 20 characters or less.');
            oecrm_currency_redirect($id);
        }

        if (!is_numeric($rate) || (float) $rate <= 0) {
            oecrm_currency_flash('danger', 'Currency rate must be a positive number.');
            oecrm_currency_redirect($id);
        }
        $rateValue = (float) $rate;

        if ($isEdit) {
            $existingStmt = mysqli_prepare($conn, 'SELECT id,name,symbol,rate FROM currency_master WHERE id=? LIMIT 1');
            mysqli_stmt_bind_param($existingStmt, 'i', $id);
            mysqli_stmt_execute($existingStmt);
            $existingCurrency = mysqli_fetch_assoc(mysqli_stmt_get_result($existingStmt));
            mysqli_stmt_close($existingStmt);
            if (!$existingCurrency) {
                oecrm_currency_flash('warning', 'Currency not found.');
                oecrm_currency_redirect();
            }
        }

        $duplicateSql = $isEdit
            ? 'SELECT id FROM currency_master WHERE (LOWER(name)=LOWER(?) OR LOWER(symbol)=LOWER(?)) AND id<>? LIMIT 1'
            : 'SELECT id FROM currency_master WHERE LOWER(name)=LOWER(?) OR LOWER(symbol)=LOWER(?) LIMIT 1';
        $duplicateStmt = mysqli_prepare($conn, $duplicateSql);
        if ($isEdit) {
            mysqli_stmt_bind_param($duplicateStmt, 'ssi', $name, $symbol, $id);
        } else {
            mysqli_stmt_bind_param($duplicateStmt, 'ss', $name, $symbol);
        }
        mysqli_stmt_execute($duplicateStmt);
        $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($duplicateStmt));
        mysqli_stmt_close($duplicateStmt);

        if ($duplicate) {
            oecrm_currency_flash('warning', 'Currency name or symbol already exists.');
            oecrm_currency_redirect($id);
        }

        if ($isEdit) {
            $stmt = mysqli_prepare($conn, 'UPDATE currency_master SET name=?,symbol=?,rate=? WHERE id=?');
            mysqli_stmt_bind_param($stmt, 'ssdi', $name, $symbol, $rateValue, $id);
            $saved = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if (!$saved) {
                oecrm_currency_flash('danger', 'Currency could not be updated.');
                oecrm_currency_redirect($id);
            }
            oecrm_audit($conn, 'finance', 'edit', 'currency', $id, 'Currency updated', $existingCurrency, ['name' => $name, 'symbol' => $symbol, 'rate' => $rateValue]);
            oecrm_currency_flash('success', 'Currency updated successfully.');
            oecrm_currency_redirect();
        }

        $stmt = mysqli_prepare($conn, 'INSERT INTO currency_master(name,symbol,rate) VALUES(?,?,?)');
        mysqli_stmt_bind_param($stmt, 'ssd', $name, $symbol, $rateValue);
        $saved = mysqli_stmt_execute($stmt);
        $newId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        if (!$saved) {
            oecrm_currency_flash('danger', 'Currency could not be saved.');
            oecrm_currency_redirect();
        }
        oecrm_audit($conn, 'finance', 'create', 'currency', $newId, 'Currency created', null, ['name' => $name, 'symbol' => $symbol, 'rate' => $rateValue]);
        oecrm_currency_flash('success', 'Currency added successfully.');
        oecrm_currency_redirect();
    }

    if ($action === 'delete_currency') {
        oecrm_currency_require_manage($conn, 'delete');
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            oecrm_currency_flash('danger', 'Invalid currency selected.');
            oecrm_currency_redirect();
        }

        $currencyStmt = mysqli_prepare($conn, 'SELECT id,name,symbol,rate FROM currency_master WHERE id=? LIMIT 1');
        mysqli_stmt_bind_param($currencyStmt, 'i', $id);
        mysqli_stmt_execute($currencyStmt);
        $currency = mysqli_fetch_assoc(mysqli_stmt_get_result($currencyStmt));
        mysqli_stmt_close($currencyStmt);

        if (!$currency) {
            oecrm_currency_flash('warning', 'Currency not found.');
            oecrm_currency_redirect();
        }

        $deleteStmt = mysqli_prepare($conn, 'DELETE FROM currency_master WHERE id=?');
        mysqli_stmt_bind_param($deleteStmt, 'i', $id);
        $deleted = mysqli_stmt_execute($deleteStmt);
        $affected = mysqli_stmt_affected_rows($deleteStmt);
        mysqli_stmt_close($deleteStmt);

        if (!$deleted || $affected < 1) {
            oecrm_currency_flash('danger', 'Currency could not be deleted because it is already used.');
            oecrm_currency_redirect();
        }

        oecrm_audit($conn, 'finance', 'delete', 'currency', $id, 'Currency deleted', $currency, null);
        oecrm_currency_flash('success', 'Currency deleted successfully.');
        oecrm_currency_redirect();
    }
}

oecrm_currency_require_manage($conn, 'view');
$editId = (int) ($_GET['edit'] ?? 0);
$editCurrency = null;
if ($editId > 0) {
    oecrm_currency_require_manage($conn, 'edit');
    $editStmt = mysqli_prepare($conn, 'SELECT id,name,symbol,rate FROM currency_master WHERE id=? LIMIT 1');
    mysqli_stmt_bind_param($editStmt, 'i', $editId);
    mysqli_stmt_execute($editStmt);
    $editCurrency = mysqli_fetch_assoc(mysqli_stmt_get_result($editStmt));
    mysqli_stmt_close($editStmt);
    if (!$editCurrency) {
        oecrm_currency_flash('warning', 'Currency not found.');
        oecrm_currency_redirect();
    }
}

$currencyFlash = $_SESSION['currency_flash'] ?? null;
unset($_SESSION['currency_flash']);
include 'header.php';
$currencyResult = mysqli_query($conn, 'SELECT id,name,symbol,rate FROM currency_master ORDER BY name');
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if (!empty($currencyFlash['message'])): ?>
        <div class="alert alert-<?php echo oecrm_h($currencyFlash['type']); ?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo oecrm_h($currencyFlash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <h4><?php echo $editCurrency ? 'Edit Currency' : 'Currency'; ?></h4>
            <div class="dataTablesbox2 dataTablesbox">
                <form role="form" method="POST" action="manageCurrency.php" novalidate>
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="action" value="save_currency">
                    <input type="hidden" name="id" value="<?php echo (int) ($editCurrency['id'] ?? 0); ?>">
                    <div class="row">
                        <div class="col-lg-3">
                            <label>Name <span class="text-danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" required maxlength="100" value="<?php echo oecrm_h($editCurrency['name'] ?? ''); ?>" placeholder="Currency name">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label>Symbol / Code <span class="text-danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="symbol" required maxlength="20" value="<?php echo oecrm_h($editCurrency['symbol'] ?? ''); ?>" placeholder="₹ / INR / $">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label>Rate <span class="text-danger">*</span></label>
                            <div class="form-group">
                                <input type="number" step="0.000001" min="0.000001" class="form-control" name="rate" required value="<?php echo oecrm_h($editCurrency['rate'] ?? '1.000000'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-3" align="right">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary viewreport" name="saveCurrency" value="1"><i class="fa fa-save"></i> <?php echo $editCurrency ? 'Update Currency' : 'Add Currency'; ?></button>
                                <?php if ($editCurrency): ?>
                                    <a href="manageCurrency.php" class="btn btn-default cancel_btn">Cancel</a>
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
                <div class="panel-heading">Currency Table</div>
                <div class="panel-body table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Symbol / Code</th>
                                <th>Rate</th>
                                <th style="text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($currencyResult && mysqli_num_rows($currencyResult) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($currencyResult)): ?>
                                    <tr>
                                        <td><?php echo oecrm_h($row['name']); ?></td>
                                        <td><?php echo oecrm_h($row['symbol']); ?></td>
                                        <td><?php echo oecrm_h(number_format((float) $row['rate'], 6, '.', '')); ?></td>
                                        <td class="center" align="center">
                                            <?php if (oecrm_currency_can_manage($conn, 'edit')): ?>
                                                <a class="oecrm-action-btn" href="manageCurrency.php?edit=<?php echo (int) $row['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <?php endif; ?>
                                            <?php if (oecrm_currency_can_manage($conn, 'delete')): ?>
                                                <form method="post" action="manageCurrency.php" style="display:inline;">
                                                    <?php echo oecrm_csrf_field(); ?>
                                                    <input type="hidden" name="action" value="delete_currency">
                                                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                                    <button type="submit" class="btn btn-link oecrm-action-btn oecrm-action-danger" data-confirm="Are you sure you want to delete this currency?" title="Delete"><i class="fa fa-trash-o"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">No currencies found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
