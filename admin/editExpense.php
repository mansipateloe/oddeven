<?php include 'header.php'; ?> 

<?php 
    $id = $_GET['edit'];
    $qryView = "SELECT * FROM expense WHERE expense_id=".$id;
    $resultView = mysqli_query($conn,$qryView);
    $rowView = mysqli_fetch_assoc($resultView);
?>  
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading panel-box">
                            <h4>Edit Expense</h4>
                        </div>
                        <div class="panel-body">
                        	<div class="dataTablesbox2">
                                <form role="form" method="POST">
                                <input type="hidden" name="id" value="<?php //echo $id; ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Date :</label>
                                                <input type="date" class="form-control" name="expensedate" value="<?php echo $rowView['expensedate']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Account Name :</label>
                                                <select name="expenceCategory" id="expenceCategory" class="form-control" required>
                                                    <option value="">Select Expense Category</option>
                                                    <?php 
                                                        $qryCategory = "SELECT * FROM expenceCategory";
                                                        $resultCategory = mysqli_query($conn,$qryCategory);
                                                        if($resultCategory->num_rows > 0){
                                                            while($resCategory = $resultCategory->fetch_assoc()){
                                                                if($resCategory['category_id'] == $rowView['expenseCategory']){
                                                                    echo "<option selected value='".$resCategory['category_id']."'>".$resCategory['expenseCategory']."</option>";
                                                                }else{
                                                                    echo "<option value='".$resCategory['category_id']."'>".$resCategory['expenseCategory']."</option>";
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
                                                <label>Amount :</label>
                                                <input type="text" class="form-control" min="0" pattern="[0-9]+" value="<?php echo $rowView['amount']; ?>" placeholder="Amount" name="amount" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Description :</label>
                                                <textarea type="text" class="form-control" placeholder="Description" rows="5" name="description" required><?php echo $rowView['description']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-primary viewreport" name="updateExpense" value="Add Expense">
                                                <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">    
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
<script src="../vendor/raphael/raphael.min.js"></script>
<script src="../vendor/morrisjs/morris.min.js"></script>
<script src="../data/morris-data.js"></script>
<script src="../dist/js/sb-admin-2.js"></script>

<?php include 'footer.php'; ?>


