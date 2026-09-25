<?php include 'header.php'; ?>
<?php
$id = (int) ($_GET['edit'] ?? 0);
$stmt = mysqli_prepare($conn, 'SELECT * FROM expense WHERE expense_id=? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$rowView = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$rowView) {
    $_SESSION['expense_flash'] = 'Expense not found.';
    header('Location:viewexpense.php');
    exit;
}
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading panel-box"><h4>Edit Expense</h4></div>
                <div class="panel-body">
                    <?php if (!empty($_SESSION['expense_flash'])): ?>
                        <div class="alert alert-warning"><?php echo oecrm_h($_SESSION['expense_flash']); unset($_SESSION['expense_flash']); ?></div>
                    <?php endif; ?>
                    <div class="dataTablesbox2">
                        <form role="form" method="POST">
                            <?php echo oecrm_csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
                            <div class="row">
                                <div class="col-lg-6"><div class="form-group"><label>Date :</label><input type="date" class="form-control" name="expensedate" value="<?php echo oecrm_h($rowView['expensedate']); ?>" required></div></div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Expense Category :</label>
                                        <select name="expenseCategory" id="expenseCategory" class="form-control" required>
                                            <option value="">Select Expense Category</option>
                                            <?php
                                            $resultCategory = mysqli_query($conn, 'SELECT category_id, expenseCategory FROM expencecategory ORDER BY expenseCategory');
                                            if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
                                                while ($resCategory = mysqli_fetch_assoc($resultCategory)) {
                                                    $selected = ((int) $resCategory['category_id'] === (int) $rowView['expenseCategory']) ? ' selected' : '';
                                                    echo '<option value="' . (int) $resCategory['category_id'] . '"' . $selected . '>' . oecrm_h($resCategory['expenseCategory']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row"><div class="col-lg-6"><div class="form-group"><label>Amount :</label><input type="number" step="0.01" min="0" class="form-control" value="<?php echo oecrm_h($rowView['amount']); ?>" placeholder="Amount" name="amount" required></div></div></div>
                            <div class="row"><div class="col-lg-6"><div class="form-group"><label>Description :</label><textarea class="form-control" placeholder="Description" rows="5" name="description" required><?php echo oecrm_h($rowView['description']); ?></textarea></div></div></div>
                            <div class="row"><div class="col-lg-6"><div class="form-group"><input type="submit" class="btn btn-primary viewreport" name="updateExpense" value="Update Expense"> <a class="btn btn-danger cancel_btn" href="viewexpense.php">Cancel</a></div></div></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
