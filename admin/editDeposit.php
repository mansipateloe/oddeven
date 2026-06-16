<?php include 'header.php'; ?> 
<?php
    if(isset($_GET['edit']))
    {
        $id = $_GET['edit'];
        $getDepositQry = "select * from deposit where deposit_id = $id";
        $getDepositResult = mysqli_query($conn, $getDepositQry);
        $getDepositRes = mysqli_fetch_assoc($getDepositResult);
        $accountId = $getDepositRes['account_Id'];
        $projectId = $getDepositRes['project_Id'];
    }
?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading panel-box">
                            <h4>Edit Deposit</h4>
                        </div>
                        <div class="panel-body edit_task">
                        	<div class="dataTablesbox2">
                                <form role="form" method="POST">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Date :</label>
                                                <input type="date" class="form-control" name="deposit_date" value="<?php echo $getDepositRes['deposit_date']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Project Name :</label>
                                                <select name="project_Id" id="project_Id" onchange="getInvoices(this.value)" class="form-control" required>
                                                    <option value="">Select Project Name</option>
                                                    <?php 
                                                        $qryProject = "SELECT * FROM projectsTbl order by id desc";
                                                        $resultProject = mysqli_query($conn,$qryProject);
                                                        if($resultProject->num_rows > 0){
                                                            while($resProject = $resultProject->fetch_assoc()){
                                                                $project_id = $resProject['id'];
                                                                if($projectId == $project_id){
                                                                    echo "<option value='".$resProject['id']."'selected>".$resProject['projectName']."</option>";
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
                                                <label>Account Name :</label>
                                                <select name="account_Id" id="account_Id" class="form-control" required>
                                                    <option value="">Select Account Name</option>
                                                    <?php 
                                                        $qryAccount = "SELECT * FROM account";
                                                        $resultAccount = mysqli_query($conn,$qryAccount);
                                                        if($resultAccount->num_rows > 0){
                                                            while($resAccount = $resultAccount->fetch_assoc()){
                                                                $account_id = $resAccount['account_id'];
                                                                if($account_id == $accountId){
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
                                                <label>Invoice :</label>
                                                <select name="invoice_id" id="invoice_id" class="form-control" required>
                                                    <option value="" selected disabled>Select Invoice</option>
                                                    <?php 
                                                        if($getDepositRes['invoice_id']){
                                                            echo '<option value="'.$getDepositRes['invoice_id'].'" selected>'.$getDepositRes['invoice_id'].'</option>';
                                                        }
                                                     ?>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="subject" value="">
                                        <input type="hidden" name="amount" value="">
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Description :</label>
                                                <textarea type="text" class="form-control" placeholder="Description" rows="5" name="description" required><?php echo $getDepositRes['description']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-primary viewreport" name="updateDeposit" value="Update Deposit">
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
    <script type="text/javascript">
        function getInvoices(proj_id){
            $('#invoice_id').empty();
            $.ajax({
                type:'post',
                url:'getInvoices.php',
                data: {
                    proj_id:proj_id,
                },
                dataType: 'JSON',
                success: function (response){
                    $.each( response.data, function( key, value ) {
                        $("<option />", {val: value.invoice_id,text: value.invoice_id}).appendTo('#invoice_id');
                    });
                }
            });
        }
    </script>
<?php include 'footer.php'; ?>
