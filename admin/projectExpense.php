<?php include 'header.php'; ?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default expense">
                    <div class="panel-heading">Add Project Expense</div>
                    <div class="panel-body">
                        <div class="dataTablesbox2">
                            <?php 
                                if(isset($_GET['edit'])){
                                    $id = $_GET['edit'];
                                    $get_edit_row = "SELECT * FROM project_expenses WHERE id=".$id." limit 1";
                                    $get_edit_result = mysqli_query($conn,$get_edit_row);
                                    if($get_edit_result->num_rows > 0){
                                        $edit_row = mysqli_fetch_assoc($get_edit_result);
                                        ?>
                                        <form role="form" method="POST" action="process/projectexpense.php">
                                            <input type="hidden" name="id" value="<?php echo $_GET['edit'] ?>">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Date :</label>
                                                        <input type="date" class="form-control" name="expensedate" value="<?php echo $edit_row['expensedate']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Project Name :</label>
                                                        <select name="project_Id" id="project_Id" class="form-control" required>
                                                            <option value="">Select Project Name</option>
                                                            <?php 
                                                                $qryProject = "SELECT * FROM projectsTbl where status = 'inprogress' order by id desc";
                                                                $resultProject = mysqli_query($conn,$qryProject);
                                                                if($resultProject->num_rows > 0){
                                                                    while($resProject = $resultProject->fetch_assoc()){
                                                                        if($resProject['id'] == $edit_row['project_Id']){
                                                                            echo "<option value='".$resProject['id']."' selected>".$resProject['projectName']."</option>";
                                                                        }else{
                                                                            echo "<option value='".$resProject['id']."'>".$resProject['projectName']."</option>";
                                                                        }
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
                                                        <label>Expense Title:</label>
                                                        <input type="text" class="form-control" placeholder="Expense Title" name="expense_title" value="<?php echo $edit_row['expense_title']; ?>" minlength="3" maxlength="200" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Amount :</label>
                                                        <input type="text" class="form-control" min="0" value="<?php echo $edit_row['amount']; ?>" pattern="[0-9]+" placeholder="Amount" name="amount" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Account Name :</label>
                                                        <select name="account_Id" id="account_Id" class="form-control" required>
                                                            <option value="">Select Account Name</option>
                                                            <?php 
                                                                $qryAccount = "SELECT * FROM account";
                                                                $resultAccount = mysqli_query($conn,$qryAccount);
                                                                if($resultAccount->num_rows > 0){
                                                                    while($resAccount = $resultAccount->fetch_assoc()){
                                                                        if($resAccount['account_id'] == $edit_row['account_Id']){
                                                                            echo "<option value='".$resAccount['account_id']."' selected>".$resAccount['account_name']."</option>";
                                                                        }else{
                                                                            echo "<option value='".$resAccount['account_id']."'>".$resAccount['account_name']."</option>";
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Remark :</label>
                                                        <textarea type="text" class="form-control" placeholder="Remark" rows="5" name="remark"><?php echo $edit_row['remark']; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="submit" class="btn btn-primary viewreport" name="update_project_expense" value="Update Expense">
                                                        <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">    
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <?php    
                                    }
                                }else{
                                    ?>
                                    <form role="form" method="POST" action="process/projectexpense.php">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Date :</label>
                                                    <input type="date" class="form-control" name="expensedate" value="<?php echo date('Y-m-d'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Project Name :</label>
                                                    <select name="project_Id" id="project_Id" class="form-control" required>
                                                        <option value="">Select Project Name</option>
                                                        <?php 
                                                            $qryProject = "SELECT * FROM projectsTbl where status = 'inprogress' order by id desc";
                                                            $resultProject = mysqli_query($conn,$qryProject);
                                                            if($resultProject->num_rows > 0){
                                                                while($resProject = $resultProject->fetch_assoc()){
                                                                    echo "<option value='".$resProject['id']."'>".$resProject['projectName']."</option>";
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
                                                    <label>Expense Title:</label>
                                                    <input type="text" class="form-control" placeholder="Expense Title" name="expense_title" minlength="3" maxlength="200" required>
                                                </div>
                                            </div>
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
                                                    <label>Account Name :</label>
                                                    <select name="account_Id" id="account_Id" class="form-control" required>
                                                        <option value="">Select Account Name</option>
                                                        <?php 
                                                            $qryAccount = "SELECT * FROM account";
                                                            $resultAccount = mysqli_query($conn,$qryAccount);
                                                            if($resultAccount->num_rows > 0){
                                                                while($resAccount = $resultAccount->fetch_assoc()){
                                                                    echo "<option value='".$resAccount['account_id']."'>".$resAccount['account_name']."</option>";
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Remark :</label>
                                                    <textarea type="text" class="form-control" placeholder="Remark" rows="5" name="remark"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="submit" class="btn btn-primary viewreport" name="add_project_expense" value="Add Expense">
                                                    <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">    
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                }
                             ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel panel-default view_expense">
                    <div class="panel-heading">
                       View All Project Expense
                    </div>
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
                                    <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Project</th>
                                                <th>Expense</th>
                                                <th>Account</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $displayExpense = "select * from project_expenses";
                                                $result = mysqli_query($conn,$displayExpense);
                                                if($result->num_rows > 0){
                                                    while($row = $result->fetch_assoc()){
                                                    echo "<tr class='gradeA even' role='row'>";
                                                        echo "<td>".$row['expensedate']."</td>";
                                                        $get_proj_qry = "select * from projectsTbl where id=".$row['project_Id'];
                                                        $get_proj_result = mysqli_query($conn,$get_proj_qry);
                                                        $get_proj_row = $get_proj_result->fetch_assoc();
                                                        echo "<td>".$get_proj_row['projectName']."</td>";
                                                        echo "<td>".$row['expense_title']."</td>";
                                                        $get_acc_qry = "select * from account where account_id=".$row['account_Id'];
                                                        $get_acc_result = mysqli_query($conn,$get_acc_qry);
                                                        $get_acc_row = $get_acc_result->fetch_assoc();
                                                        echo "<td>".$get_acc_row['account_name']."</td>";
                                                        echo "<td>".$row['amount']."</td>";
                                                        echo '<td><a href="projectExpense.php?edit='.$row["id"].'"><i class="fa fa-pencil" style="font-size:22px;"></i></a>&nbsp;&nbsp;
                                                        <a href="process/projectexpense.php?delete='.$row["id"].'" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></a></td>';
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
                </div>
            </div>
        </div>
    </div>
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
