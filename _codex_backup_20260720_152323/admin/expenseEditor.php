<<<<<<< HEAD
﻿<?php
=======
<?php
>>>>>>> 16f952b06473752f063dd7dad31e1ed1d31da926
$active_menu = 'finance';
include 'header.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_permission($conn, 'finance', 'create');

$expenseCategories = [];
<<<<<<< HEAD
$categoryResult = mysqli_query($conn, 'SELECT expensecategory FROM expencecategory ORDER BY expensecategory');
if ($categoryResult) {
    while ($category = mysqli_fetch_assoc($categoryResult)) {
        $name = trim((string) ($category['expensecategory'] ?? ''));
        if ($name !== '') {
            $expenseCategories[] = $name;
=======
foreach (['expenceCategory', 'expenseCategory', 'expense_categories', 'expensecategory'] as $tableName) {
    $check = @mysqli_query($conn, "SELECT * FROM $tableName");
    if ($check && mysqli_num_rows($check) > 0) {
        while ($row = mysqli_fetch_assoc($check)) {
            $expenseCategories[] = [
                'id' => (int)($row['category_id'] ?? $row['id'] ?? 0),
                'name' => $row['expenseCategory'] ?? $row['name'] ?? $row['category_name'] ?? '',
            ];
        }
        if ($expenseCategories) {
            break;
>>>>>>> 16f952b06473752f063dd7dad31e1ed1d31da926
        }
    }
}
if (!$expenseCategories) {
<<<<<<< HEAD
    $expenseCategories = ['Salary', 'Rent', 'Hosting', 'AWS', 'Marketing', 'Software Subscriptions', 'Internet', 'Electricity', 'Miscellaneous'];
}
?>
<div id="page-wrapper" class="compact-admin-page resource-form-page">
    <div class="foundation-titlebar"><a href="finance.php"><i class="fa fa-arrow-left"></i> Finance</a><h2>Add Expense</h2></div>
=======
    foreach (['Salary', 'Rent', 'Hosting', 'AWS', 'Marketing', 'Software Subscriptions', 'Internet', 'Electricity', 'Miscellaneous'] as $index => $label) {
        $expenseCategories[] = ['id' => $index + 1, 'name' => $label];
    }
}
?>
<div id="page-wrapper" class="compact-admin-page resource-form-page">
    <div class="foundation-titlebar">
        <a href="finance.php"><i class="fa fa-arrow-left"></i> Finance</a>
        <h2>Add Expense</h2>
    </div>
>>>>>>> 16f952b06473752f063dd7dad31e1ed1d31da926
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="post" action="financeAction.php" class="resource-form">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="action" value="expense">
<<<<<<< HEAD
                <div class="form-group"><label>Date</label><input class="form-control" type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required></div>
                <div class="form-group">
                    <label>Category <span class="text-danger">*</span></label>
                    <select class="form-control" name="category" required>
                        <option value="">Select category</option>
                        <?php foreach ($expenseCategories as $category): ?>
                            <option value="<?php echo oecrm_h($category); ?>"><?php echo oecrm_h($category); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Vendor</label><input class="form-control" name="vendor"></div>
                <div class="form-group"><label>Amount</label><input class="form-control" type="number" step=".01" name="amount" required></div>
                <div class="form-group"><label>GST Paid</label><input class="form-control" type="number" step=".01" name="tax_amount" value="0"></div>
                <div class="form-group"><label>Currency</label><input class="form-control" name="currency_code" value="INR"></div>
                <div class="form-group resource-notes"><label>Description</label><textarea class="form-control" name="description" required></textarea></div>
                <div class="resource-actions"><button class="btn btn-primary"><i class="fa fa-save"></i> Save Expense</button></div>
=======
                <div class="form-group">
                    <label>Date</label>
                    <input class="form-control" type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" name="category" required>
                        <?php foreach ($expenseCategories as $category): ?>
                            <option value="<?php echo oecrm_h($category['name']); ?>"><?php echo oecrm_h($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Vendor</label>
                    <input class="form-control" name="vendor">
                </div>
                <div class="form-group">
                    <label>Amount</label>
                    <input class="form-control" type="number" step=".01" name="amount" required>
                </div>
                <div class="form-group">
                    <label>GST Paid</label>
                    <input class="form-control" type="number" step=".01" name="tax_amount" value="0">
                </div>
                <div class="form-group">
                    <label>Currency</label>
                    <input class="form-control" name="currency_code" value="INR">
                </div>
                <div class="form-group resource-notes">
                    <label>Description</label>
                    <textarea class="form-control" name="description" required></textarea>
                </div>
                <div class="resource-actions">
                    <button class="btn btn-primary"><i class="fa fa-save"></i> Save Expense</button>
                </div>
>>>>>>> 16f952b06473752f063dd7dad31e1ed1d31da926
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
