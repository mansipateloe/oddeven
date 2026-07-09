<?php include 'header.php'; ?>
<?php
$expenseCategoryMap = [];
foreach (['expenceCategory', 'expenseCategory', 'expense_categories', 'expensecategory'] as $tableName) {
    $check = @mysqli_query($conn, "SELECT * FROM $tableName");
    if ($check && mysqli_num_rows($check) > 0) {
        while ($row = mysqli_fetch_assoc($check)) {
            $expenseCategoryMap[(string)($row['category_id'] ?? $row['id'] ?? '')] = $row['expenseCategory'] ?? $row['name'] ?? $row['category_name'] ?? '';
        }
        if ($expenseCategoryMap) break;
    }
}
?>
       <div id="page-wrapper">
            <div class="row">
                                <div class="col-lg-12">
                    <div class="panel panel-default expense">
                        <div class="panel-heading">Add Expense</div>
                        <div class="panel-body">
                            <div class="dataTablesbox2">
                                <form role="form" method="POST">
                                <input type="hidden" name="id" value="<?php //echo $id; ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Date :</label>
                                                <input type="date" class="form-control" name="expensedate" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Account Name :</label>
                                                <select name="expenseCategory" id="expenseCategory" class="form-control" required>
                                                    <option value="">Select Expense Category</option>
                                                    <?php 
                                                        $qryCategory = "SELECT * FROM expenceCategory";
                                                        $resultCategory = mysqli_query($conn,$qryCategory);
                                                        if($resultCategory->num_rows > 0){
                                                            while($resCategory = $resultCategory->fetch_assoc()){
                                                                echo "<option value='".$resCategory['category_id']."'>".$resCategory['expenseCategory']."</option>";
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
                                                <input type="text" class="form-control" min="0" pattern="[0-9]+" placeholder="Amount" name="amount" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Description :</label>
                                                <textarea type="text" class="form-control" placeholder="Description" rows="5" name="description" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-primary viewreport" name="addExpense" value="Add Expense">
                                                <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">    
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        </div>
                    </div>

                <div class="col-lg-12">
                    <div class="panel panel-default view_expense">
                        <div class="panel-heading">
                           View All Expense
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div id="dataTables-example_filter" class="dataTables_filter">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-sm-12">
                                <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <!-- <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Project Name</th> -->
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">Date</th>
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Expense Category</th>
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Amount</th>
                                        <!-- <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 148px;">Start Date</th>
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">End Date</th> -->
                                        <th tabindex="0" rowspan="1" colspan="1"  style="width: 110px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $displayExpense = "select * from expense";
                                        $result = mysqli_query($conn,$displayExpense);
                                        if($result->num_rows > 0){
                                            while($row = $result->fetch_assoc()){
                                            echo "<tr class='gradeA even' role='row'>";
                                                echo "<td>".$row['expensedate']."</td>";
                                                $categoryValue = (string)$row['expenseCategory'];
                                                $categoryName = $expenseCategoryMap[$categoryValue] ?? $categoryValue;
                                                echo "<td>".$categoryName."</td>";
                                                echo "<td>".$row['amount']."</td>";
                                                echo '<td><a href="editExpense.php?edit='.$row["expense_id"].'"><i class="fa fa-pencil" style="font-size:22px;"></i></a>&nbsp;&nbsp;
                                                <a href="deleteExpense.php?delete='.$row["expense_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></a></td>';
                                            echo "</tr>";
                                            }
                                        }
                                    ?>                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                
                <!-- /.col-lg-12 -->
            </div>
        </div>
    <!-- /#wrapper -->

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../data/morris-data.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTables-example').DataTable({
                /*responsive: true,*/
                order: [[0, 'desc']],
                // columnDefs: [
                // { "orderable": false, "targets": 5 }
                // ]
            });
        });
    </script>
<?php include 'footer.php'; ?>
