<?php
$active_menu = 'finance';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'finance', 'create');

$expenseCategories = [];
$newExpenseCategory = trim((string) ($_SESSION['new_expense_category_name'] ?? ''));
unset($_SESSION['new_expense_category_name']);
$expenseFlash = $_SESSION['expense_flash'] ?? '';
$expenseFlashType = $_SESSION['expense_flash_type'] ?? 'warning';
unset($_SESSION['expense_flash'], $_SESSION['expense_flash_type']);
$categoryResult = mysqli_query($conn, 'SELECT expenseCategory FROM expencecategory ORDER BY expenseCategory');
if ($categoryResult) {
    while ($category = mysqli_fetch_assoc($categoryResult)) {
        $name = trim((string) ($category['expenseCategory'] ?? ''));
        if ($name !== '') {
            $expenseCategories[] = $name;
        }
    }
}
if (!$expenseCategories) {
    $expenseCategories = ['Salary', 'Rent', 'Hosting', 'AWS', 'Marketing', 'Software Subscriptions', 'Internet', 'Electricity', 'Miscellaneous'];
}
?>
<div id="page-wrapper" class="compact-admin-page resource-form-page">
    <div class="foundation-titlebar"><a href="finance.php"><i class="fa fa-arrow-left"></i> Finance</a><h2>Add Expense</h2></div>
    <?php if ($expenseFlash): ?><div class="alert alert-<?php echo in_array($expenseFlashType, ['success', 'warning', 'danger'], true) ? oecrm_h($expenseFlashType) : 'warning'; ?>" role="status"><?php echo oecrm_h($expenseFlash); ?></div><?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="post" action="financeAction.php" class="resource-form">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="action" value="expense">
                <div class="form-group"><label>Date</label><input class="form-control" type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required></div>
                <div class="form-group">
                    <label>Category <span class="text-danger">*</span></label>
                    <select class="form-control" name="category" required>
                        <option value="">Select category</option>
                        <?php foreach ($expenseCategories as $category): ?>
                            <option value="<?php echo oecrm_h($category); ?>" <?php echo $category === $newExpenseCategory ? 'selected' : ''; ?>><?php echo oecrm_h($category); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Vendor</label><input class="form-control" name="vendor"></div>
                <div class="form-group"><label>Amount</label><input class="form-control" type="number" step=".01" name="amount" required></div>
                <div class="form-group"><label>GST Paid</label><input class="form-control" type="number" step=".01" name="tax_amount" value="0"></div>
                <div class="form-group"><label>Currency</label><input class="form-control" name="currency_code" value="INR"></div>
                <div class="form-group resource-notes"><label>Description</label><textarea class="form-control" name="description" required></textarea></div>
                <div class="resource-actions"><button class="btn btn-primary"><i class="fa fa-save"></i> Save Expense</button></div>
            </form>
            <details style="margin-top:8px;">
                <summary style="cursor:pointer;">Add a new expense category</summary>
                <form method="post" action="expenseCategories.php" style="max-width:480px;margin-top:12px;">
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="return_to" value="expenseEditor.php">
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
<?php include 'footer.php'; ?>
