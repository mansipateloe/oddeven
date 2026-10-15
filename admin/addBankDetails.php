<?php
ob_start();
$active_menu = "setting";
$active_submenu = "add_bank_details";

require_once __DIR__ . "/../dbconnect.php";
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../foundation.php";

if (isset($_GET["bank_delete"])) {
    $_SESSION["bank_details_flash"] = [
        "type" => "warning",
        "message" => "Please use the delete button on this page to delete bank details securely.",
    ];
    header("Location: addBankDetails.php");
    exit;
}

oecrm_require_admin_login();

function oecrm_bank_details_flash($type, $message)
{
    $_SESSION["bank_details_flash"] = ["type" => $type, "message" => $message];
}

function oecrm_bank_details_redirect($editId = 0)
{
    header("Location: addBankDetails.php" . ($editId > 0 ? "?edit=" . (int) $editId : ""));
    exit;
}

function oecrm_bank_details_can_manage($conn, $action = "view")
{
    return oecrm_is_super_admin()
        || oecrm_can($conn, "finance", $action)
        || oecrm_can($conn, "bank_reconciliation", $action)
        || oecrm_can($conn, "settings", $action)
        || oecrm_legacy_can($conn, "settings")
        || oecrm_legacy_can($conn, "invocie")
        || oecrm_legacy_can($conn, "deposit")
        || oecrm_legacy_can($conn, "expense");
}

function oecrm_bank_details_require($conn, $action = "view")
{
    if (!oecrm_bank_details_can_manage($conn, $action)) {
        http_response_code(403);
        exit("You do not have permission to manage bank details.");
    }
}

oecrm_bank_details_require($conn, "view");
require_once __DIR__ . "/../expenseBanks.php";
oecrm_expense_bank_schema($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    oecrm_require_csrf();
    $action = (string) ($_POST["action"] ?? "");

    if ($action === "save_bank_details") {
        $id = (int) ($_POST["id"] ?? 0);
        $isEdit = $id > 0;
        oecrm_bank_details_require($conn, $isEdit ? "edit" : "create");

        $bankName = preg_replace("/\s+/", " ", trim((string) ($_POST["bank_name"] ?? "")));
        if ($bankName === "") {
            oecrm_bank_details_flash("danger", "Bank name is required.");
            oecrm_bank_details_redirect($id);
        }
        if (strlen($bankName) > 255) {
            oecrm_bank_details_flash("danger", "Bank name must be 255 characters or less.");
            oecrm_bank_details_redirect($id);
        }

        $accountNumber = trim((string) ($_POST["account_number"] ?? ""));
        $ifscCode = strtoupper(trim((string) ($_POST["ifsc_code"] ?? "")));
        $openingBalance = trim((string) ($_POST["opening_balance"] ?? "0"));
        if ($openingBalance === "") {
            $openingBalance = "0";
        }
        if ($accountNumber !== "" && !preg_match('/^[0-9]{1,50}$/D', $accountNumber)) {
            oecrm_bank_details_flash("danger", "Account number must contain only digits (maximum 50).");
            oecrm_bank_details_redirect($id);
        }
        if ($ifscCode !== "" && !preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/D', $ifscCode)) {
            oecrm_bank_details_flash("danger", "Enter a valid 11-character IFSC code, for example SBIN0001234.");
            oecrm_bank_details_redirect($id);
        }
        if (!preg_match('/^-?[0-9]{1,12}(\.[0-9]{1,2})?$/D', $openingBalance)) {
            oecrm_bank_details_flash("danger", "Opening balance must be a valid amount with up to 12 whole digits and 2 decimal places.");
            oecrm_bank_details_redirect($id);
        }
        if (!$isEdit && (float) $openingBalance < 0) {
            oecrm_bank_details_flash("danger", "Opening balance cannot be negative when adding a bank.");
            oecrm_bank_details_redirect();
        }
        $bankDetails = [
            "bank_name" => $bankName,
            "account_number" => $accountNumber,
            "ifsc_code" => $ifscCode,
            "opening_balance" => $openingBalance,
        ];

        $old = null;
        if ($isEdit) {
            $oldStmt = mysqli_prepare($conn, "SELECT bank_id,bank_name,account_number,ifsc_code,opening_balance FROM bank_details WHERE bank_id=? LIMIT 1");
            mysqli_stmt_bind_param($oldStmt, "i", $id);
            mysqli_stmt_execute($oldStmt);
            $old = mysqli_fetch_assoc(mysqli_stmt_get_result($oldStmt));
            mysqli_stmt_close($oldStmt);
            if (!$old) {
                oecrm_bank_details_flash("warning", "Bank record not found.");
                oecrm_bank_details_redirect();
            }
        }

        $duplicateSql = $isEdit
            ? "SELECT bank_id FROM bank_details WHERE LOWER(TRIM(bank_name))=LOWER(?) AND bank_id<>? LIMIT 1"
            : "SELECT bank_id FROM bank_details WHERE LOWER(TRIM(bank_name))=LOWER(?) LIMIT 1";
        $duplicateStmt = mysqli_prepare($conn, $duplicateSql);
        if ($isEdit) {
            mysqli_stmt_bind_param($duplicateStmt, "si", $bankName, $id);
        } else {
            mysqli_stmt_bind_param($duplicateStmt, "s", $bankName);
        }
        mysqli_stmt_execute($duplicateStmt);
        $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($duplicateStmt));
        mysqli_stmt_close($duplicateStmt);

        if ($duplicate) {
            oecrm_bank_details_flash("warning", "This bank name already exists.");
            oecrm_bank_details_redirect($id);
        }

        if ($isEdit) {
            $stmt = mysqli_prepare($conn, "UPDATE bank_details SET bank_name=?,account_number=?,ifsc_code=?,opening_balance=? WHERE bank_id=?");
            mysqli_stmt_bind_param($stmt, "ssssi", $bankName, $accountNumber, $ifscCode, $openingBalance, $id);
            $saved = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if (!$saved) {
                oecrm_bank_details_flash("danger", "Bank record could not be updated.");
                oecrm_bank_details_redirect($id);
            }
            oecrm_audit($conn, "finance", "edit", "bank_details", $id, "Bank details updated", $old, $bankDetails);
            oecrm_bank_details_flash("success", "Bank record updated successfully.");
            oecrm_bank_details_redirect();
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO bank_details(bank_name,account_number,ifsc_code,opening_balance) VALUES(?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssss", $bankName, $accountNumber, $ifscCode, $openingBalance);
        $saved = mysqli_stmt_execute($stmt);
        $newId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        if (!$saved) {
            oecrm_bank_details_flash("danger", "Bank record could not be saved.");
            oecrm_bank_details_redirect();
        }

        oecrm_audit($conn, "finance", "create", "bank_details", $newId, "Bank details created", null, $bankDetails);
        oecrm_bank_details_flash("success", "Bank record added successfully.");
        oecrm_bank_details_redirect();
    }

    if ($action === "delete_bank_details") {
        oecrm_bank_details_require($conn, "delete");
        $id = (int) ($_POST["id"] ?? 0);
        if ($id <= 0) {
            oecrm_bank_details_flash("danger", "Invalid bank record selected.");
            oecrm_bank_details_redirect();
        }

        $oldStmt = mysqli_prepare($conn, "SELECT bank_id,bank_name,account_number,ifsc_code,opening_balance FROM bank_details WHERE bank_id=? LIMIT 1");
        mysqli_stmt_bind_param($oldStmt, "i", $id);
        mysqli_stmt_execute($oldStmt);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($oldStmt));
        mysqli_stmt_close($oldStmt);
        if (!$old) {
            oecrm_bank_details_flash("warning", "Bank record not found.");
            oecrm_bank_details_redirect();
        }

        $deleteStmt = mysqli_prepare($conn, "DELETE FROM bank_details WHERE bank_id=? AND NOT EXISTS (SELECT 1 FROM expense WHERE expense.bank_id=bank_details.bank_id)");
        mysqli_stmt_bind_param($deleteStmt, "i", $id);
        $deleted = mysqli_stmt_execute($deleteStmt);
        $affected = mysqli_stmt_affected_rows($deleteStmt);
        mysqli_stmt_close($deleteStmt);

        if (!$deleted || $affected < 1) {
            oecrm_bank_details_flash("danger", "Bank could not be deleted. Banks linked to expenses must be kept for balance history.");
            oecrm_bank_details_redirect();
        }

        oecrm_audit($conn, "finance", "delete", "bank_details", $id, "Bank details deleted", $old, null);
        oecrm_bank_details_flash("success", "Bank record deleted successfully.");
        oecrm_bank_details_redirect();
    }
}

oecrm_bank_details_require($conn, "view");
$editId = (int) ($_GET["edit"] ?? 0);
$bank = ["bank_id" => 0, "bank_name" => "", "account_number" => "", "ifsc_code" => "", "opening_balance" => "0.00"];
if ($editId > 0) {
    oecrm_bank_details_require($conn, "edit");
    $editStmt = mysqli_prepare($conn, "SELECT bank_id,bank_name,account_number,ifsc_code,opening_balance FROM bank_details WHERE bank_id=? LIMIT 1");
    mysqli_stmt_bind_param($editStmt, "i", $editId);
    mysqli_stmt_execute($editStmt);
    $bank = mysqli_fetch_assoc(mysqli_stmt_get_result($editStmt));
    mysqli_stmt_close($editStmt);
    if (!$bank) {
        oecrm_bank_details_flash("warning", "Bank record not found.");
        oecrm_bank_details_redirect();
    }
}

$flash = $_SESSION["bank_details_flash"] ?? null;
unset($_SESSION["bank_details_flash"]);

include "header.php";
$bankRows = mysqli_query($conn, oecrm_expense_bank_list_sql());
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if (!empty($flash["message"])): ?>
        <div class="alert alert-<?php echo oecrm_h($flash["type"]); ?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo oecrm_h($flash["message"]); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <h4>Manage Bank Details</h4>
            <div class="dataTablesbox2 dataTablesbox">
                <form role="form" method="POST" action="addBankDetails.php">
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="action" value="save_bank_details">
                    <input type="hidden" name="id" value="<?php echo (int) $bank["bank_id"]; ?>">
                    <div class="row">
                        <div class="col-lg-3">
                            <label>Name <span class="text-danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="bank_name" value="<?php echo oecrm_h($bank["bank_name"]); ?>" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label for="account_number">Bank Account Number</label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="account_number" name="account_number" value="<?php echo oecrm_h($bank["account_number"]); ?>" maxlength="50" inputmode="numeric" pattern="[0-9]{1,50}" title="Enter up to 50 digits">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label for="ifsc_code">IFSC Code</label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?php echo oecrm_h($bank["ifsc_code"]); ?>" maxlength="11" pattern="[A-Za-z]{4}0[A-Za-z0-9]{6}" title="Enter a valid 11-character IFSC code" placeholder="SBIN0001234" style="text-transform:uppercase;">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label for="opening_balance">Opening Balance</label>
                            <div class="form-group">
                                <input type="number" class="form-control" id="opening_balance" name="opening_balance" value="<?php echo oecrm_h($bank["opening_balance"]); ?>" step="0.01" min="<?php echo $editId > 0 ? '-999999999999.99' : '0'; ?>" max="999999999999.99" required>
                            </div>
                        </div>
                        <div class="col-lg-12" align="right">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary viewreport"><i class="fa fa-save"></i> <?php echo $editId > 0 ? "Update" : "Add"; ?></button>
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
                                <th>Account Number</th>
                                <th>IFSC Code</th>
                                <th class="text-right">Opening Balance</th>
                                <th class="text-right">Current Balance</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bankRows && mysqli_num_rows($bankRows) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($bankRows)): ?>
                                    <tr>
                                        <td><?php echo oecrm_h($row["bank_name"]); ?></td>
                                        <td><?php echo oecrm_h($row["account_number"]); ?></td>
                                        <td><?php echo oecrm_h($row["ifsc_code"]); ?></td>
                                        <td class="text-right"><?php echo number_format((float) $row["opening_balance"], 2); ?></td>
                                        <td class="text-right"><?php echo number_format((float) $row["current_balance"], 2); ?></td>
                                        <td class="center text-center">
                                            <?php if (oecrm_bank_details_can_manage($conn, "edit")): ?>
                                                <a class="oecrm-action-btn" href="addBankDetails.php?edit=<?php echo (int) $row["bank_id"]; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <?php endif; ?>
                                            <?php if (oecrm_bank_details_can_manage($conn, "delete")): ?>
                                                <form method="post" action="addBankDetails.php" style="display:inline;" data-confirm="Are you sure you want to delete this bank record?">
                                                    <?php echo oecrm_csrf_field(); ?>
                                                    <input type="hidden" name="action" value="delete_bank_details">
                                                    <input type="hidden" name="id" value="<?php echo (int) $row["bank_id"]; ?>">
                                                    <button type="submit" class="btn btn-link oecrm-action-btn oecrm-action-danger" title="Delete"><i class="fa fa-trash-o"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No bank records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
