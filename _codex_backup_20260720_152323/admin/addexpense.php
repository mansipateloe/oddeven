<?php include 'header.php'; ?>
<div id="page-wrapper">
    <div class="row">
        <br>
        <div class="col-lg-12">
            <div class="dataTablesbox2">
                <?php if (!empty($_SESSION['expense_flash'])): ?>
                    <div class="alert alert-warning"><?php echo oecrm_h($_SESSION['expense_flash']); unset($_SESSION['expense_flash']); ?></div>
                <?php endif; ?>
                <form role="form" method="POST">
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Date :</label>
                                <input type="date" class="form-control" name="expensedate" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
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
                                            echo '<option value="' . (int) $resCategory['category_id'] . '">' . oecrm_h($resCategory['expenseCategory']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Amount :</label>
                                <input type="number" step="0.01" min="0" class="form-control" placeholder="Amount" name="amount" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Description :</label>
                                <textarea class="form-control" placeholder="Description" rows="5" name="description" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary viewreport" name="addExpense" value="Add Expense">
                                <input class="btn btn-danger" type="reset" value="Cancel">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
