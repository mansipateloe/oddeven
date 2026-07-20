<?php include 'header.php'; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default expense">
                <div class="panel-heading">Add Expense</div>
                <div class="panel-body">
                    <?php if (!empty($_SESSION['expense_flash'])): ?>
                        <div class="alert alert-warning"><?php echo oecrm_h($_SESSION['expense_flash']); unset($_SESSION['expense_flash']); ?></div>
                    <?php endif; ?>
                    <div class="dataTablesbox2">
                        <form role="form" method="POST">
                            <?php echo oecrm_csrf_field(); ?>
                            <div class="row">
                                <div class="col-lg-6"><div class="form-group"><label>Date :</label><input type="date" class="form-control" name="expensedate" value="<?php echo date('Y-m-d'); ?>" required></div></div>
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
                            <div class="row"><div class="col-lg-6"><div class="form-group"><label>Amount :</label><input type="number" step="0.01" min="0" class="form-control" placeholder="Amount" name="amount" required></div></div></div>
                            <div class="row"><div class="col-lg-6"><div class="form-group"><label>Description :</label><textarea class="form-control" placeholder="Description" rows="5" name="description" required></textarea></div></div></div>
                            <div class="row"><div class="col-lg-6"><div class="form-group"><input type="submit" class="btn btn-primary viewreport" name="addExpense" value="Add Expense"> <input class="btn btn-danger cancel_btn" type="reset" value="Cancel"></div></div></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="panel panel-default view_expense">
                <div class="panel-heading">View All Expense</div>
                <div class="panel-body table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="width: 100%;">
                        <thead><tr><th>Date</th><th>Expense Category</th><th>Amount</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php
                            $displayExpense = 'SELECT e.*, c.expenseCategory category_name FROM expense e LEFT JOIN expencecategory c ON c.category_id = CAST(e.expenseCategory AS UNSIGNED) ORDER BY e.expensedate DESC, e.expense_id DESC';
                            $result = mysqli_query($conn, $displayExpense);
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<tr>';
                                    echo '<td>' . oecrm_h($row['expensedate']) . '</td>';
                                    echo '<td>' . oecrm_h($row['category_name'] ?: 'Unknown category') . '</td>';
                                    echo '<td>' . oecrm_h($row['amount']) . '</td>';
                                    echo '<td><a class="oecrm-action-btn" href="editExpense.php?edit=' . (int) $row['expense_id'] . '" title="Edit"><i class="fa fa-pencil"></i></a></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center">No expenses found.</td></tr>';
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
