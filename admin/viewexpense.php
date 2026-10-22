<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();
if (!oecrm_can($conn, 'finance', 'view') && !oecrm_legacy_can($conn, 'expense')) {
    http_response_code(403);
    exit('You do not have permission to manage expenses.');
}
require_once __DIR__ . '/../expenseBanks.php';
oecrm_expense_bank_schema($conn);
$canDeleteExpense = oecrm_can($conn, 'finance', 'delete');
$newExpenseCategoryId = (int) ($_SESSION['new_expense_category_id'] ?? 0);
unset($_SESSION['new_expense_category_id'], $_SESSION['new_expense_category_name']);
include 'header.php';
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default expense">
                <div class="panel-heading">Add Expense</div>
                <div class="panel-body">
                    <?php if (!empty($_SESSION['expense_flash'])): ?>
                        <div class="alert alert-<?php echo in_array($_SESSION['expense_flash_type'] ?? '', ['success', 'warning', 'danger'], true) ? $_SESSION['expense_flash_type'] : 'warning'; ?>" role="status"><?php echo oecrm_h($_SESSION['expense_flash']); unset($_SESSION['expense_flash'], $_SESSION['expense_flash_type']); ?></div>
                    <?php endif; ?>
                    <style>
                        .expense-date-field {
                            width: 100%;
                            min-width: 160px;
                            height: 45px;
                            line-height: 45px;
                            padding: 6px 12px;
                            box-sizing: border-box;
                        }
                    </style>
                    <div class="dataTablesbox2">
                        <form role="form" method="POST">
                            <?php echo oecrm_csrf_field(); ?>
                            <?php oecrm_expense_bank_field($conn); ?>
                            <div class="row">
                                <div class="col-lg-6"><div class="form-group"><label>Expense Title :</label><input type="text" class="form-control" placeholder="Expense Title" name="expense_title" minlength="3" maxlength="200" required></div></div>
                                <div class="col-lg-6"><div class="form-group"><label>Date :</label><input type="date" class="form-control expense-date-field" name="expensedate" value="<?php echo date('Y-m-d'); ?>" required></div></div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Expense Category :</label>
                                        <select name="expenseCategory" id="expenseCategory" class="form-control" required>
                                            <option value="">Select Expense Category</option>
                                            <?php
                                            $resultCategory = mysqli_query($conn, 'SELECT category_id, expenseCategory FROM expencecategory ORDER BY expenseCategory');
                                            if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
                                                while ($resCategory = mysqli_fetch_assoc($resultCategory)) {
                                                    $selected = (int) $resCategory['category_id'] === $newExpenseCategoryId ? ' selected' : '';
                                                    echo '<option value="' . (int) $resCategory['category_id'] . '"' . $selected . '>' . oecrm_h($resCategory['expenseCategory']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row"><div class="col-lg-6"><div class="form-group"><label>Amount :</label><input type="number" step="0.01" min="0.01" max="999999999999.99" class="form-control" placeholder="Amount" name="amount" required></div></div></div>
                            <div class="row"><div class="col-lg-6"><div class="form-group"><label>Description :</label><textarea class="form-control" placeholder="Description" rows="5" name="description" maxlength="255" required></textarea></div></div></div>
                            <div class="row"><div class="col-lg-6"><div class="form-group"><input type="submit" class="btn btn-primary viewreport" name="addExpense" value="Add Expense"> <input class="btn btn-danger cancel_btn" type="reset" value="Cancel"></div></div></div>
                        </form>
                        <details style="margin-top:12px;">
                            <summary style="cursor:pointer;">Add a new expense category</summary>
                            <form method="post" action="expenseCategories.php" style="max-width:480px;margin-top:12px;">
                                <?php echo oecrm_csrf_field(); ?>
                                <div class="form-group">
                                    <label for="new-expense-category">Category name</label>
                                    <input type="text" class="form-control" id="new-expense-category" name="category_name" maxlength="255" required>
                                </div>
                                <button type="submit" class="btn btn-default"><i class="fa fa-plus"></i> Add Category</button>
                            </form>
                        </details>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="panel panel-default view_expense">
                <div class="panel-heading">View All Expense</div>
                <div class="panel-body table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="width: 100%;">
                        <thead><tr><th>Date</th><th>Expense Title</th><th>Expense Category</th><th>Amount</th><th>Bank</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php
                            $displayExpense = 'SELECT e.*, c.expenseCategory category_name, b.bank_name FROM expense e LEFT JOIN expencecategory c ON c.category_id = CAST(e.expenseCategory AS UNSIGNED) LEFT JOIN bank_details b ON b.bank_id=e.bank_id ORDER BY e.expensedate DESC, e.expense_id DESC';
                            $result = mysqli_query($conn, $displayExpense);
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<tr>';
                                    echo '<td>' . oecrm_h($row['expensedate']) . '</td>';
                                    echo '<td>' . oecrm_h($row['expense_title'] ?: '-') . '</td>';
                                    echo '<td>' . oecrm_h($row['category_name'] ?: 'Unknown category') . '</td>';
                                    echo '<td>' . oecrm_h($row['amount']) . '</td>';
                                    echo '<td>' . oecrm_h($row['bank_name'] ?? 'Unassigned') . '</td>';
                                    echo '<td><div style="display:flex;align-items:center;gap:8px;"><a class="oecrm-action-btn" href="editExpense.php?edit=' . (int) $row['expense_id'] . '" title="Edit" aria-label="Edit expense"><i class="fa fa-pencil"></i></a>';
                                    if ($canDeleteExpense) {
                                        $confirmation = !empty($row['bank_id'])
                                            ? 'Delete this expense? Its amount will be restored to the linked bank balance.'
                                            : 'Delete this expense? This cannot be undone.';
                                        echo '<form method="post" action="deleteExpense.php" style="display:inline-flex;margin:0;" data-confirm="' . oecrm_h($confirmation) . '">' . oecrm_csrf_field();
                                        echo '<input type="hidden" name="expense_id" value="' . (int) $row['expense_id'] . '">';
                                        echo '<button type="submit" class="btn btn-xs btn-danger" title="Delete expense" aria-label="Delete expense"><i class="fa fa-trash-o"></i></button></form>';
                                    }
                                    echo '</div></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">No expenses found.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
