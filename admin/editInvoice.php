<?php
    include 'header.php';
?> 
        <div id="page-wrapper">
            <div class="row">
                <?php
                    if(isset($_GET['edit'])){
                        $edit_id = $_GET['edit'];
                        $edit_qry = "SELECT * FROM invoiceTbl where invoice_id = '$edit_id' limit 1";
                        $edit_result = mysqli_query($conn,$edit_qry);
                        if($edit_result->num_rows > 0){
                            $edit_invoice = $edit_result->fetch_assoc();
                            ?>
                                <div class="col-lg-12">
                                    <div class="panel panel-default deposit_table">
                                        <div class="panel-heading">
                                            Edit Invoice
                                        </div>
                                        <div class="panel-body add_deposit">
                                        	<div class="dataTablesbox2">
                                                <form role="form" method="POST" action="invoices.php">
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label>Invoice No :</label>
                                                                <input type="text" class="form-control" value="<?php echo $edit_invoice['invoice_no'] ?>" placeholder="Invoice No." name="invoice_no" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label>Date :</label>
                                                                <input type="hidden" name="id" value="<?php echo $edit_invoice['invoice_id'] ?>">
                                                                <input type="date" class="form-control" name="invoice_date" value="<?php echo $edit_invoice['invoice_date'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label>Project Name :</label>
                                                                <select name="project_Id" id="project_Id" onchange="getPerson(this.value)" required class="form-control">
                                                                    <option value="">Select Project Name</option>
                                                                    <?php 
                                                                        $qryProject = "SELECT * FROM projectsTbl where status = 'inprogress'";
                                                                        $resultProject = mysqli_query($conn,$qryProject);
                                                                        if($resultProject->num_rows > 0){
                                                                            while($resProject = $resultProject->fetch_assoc()){
                                                                                if($resProject['id'] == $edit_invoice['project_Id']){
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
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label>Person Name :</label>
                                                                <input type="text" class="form-control" id="person_name" name="person_name" value="<?php echo $edit_invoice['person_name'] ?>" required minlength="3" maxlength="200">
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="row" id="perticulars_div">
                                                        <?php 
                                                            $details_qry = "SELECT * FROM invoice_details where invoice_id = '$edit_id'";
                                                            $details_result = mysqli_query($conn,$details_qry);
                                                            if($details_result->num_rows > 0){
                                                                $dt = 1;
                                                                while($details_row = $details_result->fetch_assoc()){
                                                                    ?>
                                                                    <div class="perticulars">
                                                                        <div class="col-lg-3">
                                                                            <div class="form-group">
                                                                                <label>Perticular :</label>
                                                                                <input type="text" class="form-control" placeholder="Perticular" name="perticulars[]" value="<?php echo $details_row['perticular'] ?>" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <div class="form-group">
                                                                                <label>Bank Account :</label>
                                                                                <select name="bank_id[]" id="bank_id" required class="form-control">
                                                                                    <option value="">Select Bank</option>
                                                                                    <?php 
                                                                                        $qryProject = "SELECT * FROM account order by account_id desc";
                                                                                        $resultProject = mysqli_query($conn,$qryProject);
                                                                                        if($resultProject->num_rows > 0){
                                                                                            while($resProject = $resultProject->fetch_assoc())
                                                                                            {
                                                                                                $selected_opt="";
                                                                                                if(isset($details_row['bank_id']) && $details_row['bank_id']==$resProject['account_id']){$selected_opt="selected";}
                                                                                                echo "<option ".$selected_opt." value='".$resProject['account_id']."'>".$resProject['account_name']."</option>";
                                                                                            }
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-2">
                                                                            <div class="form-group">
                                                                                <label>Currency code :</label>
                                                                                <select name="country_id[]" id="country_id" required  class="form-control">
                                                                                    <option value="">Select Code</option>
                                                                                    <?php 
                                                                                        $qryProject = "SELECT * FROM country order by country_id desc";
                                                                                        $resultProject = mysqli_query($conn,$qryProject);
                                                                                        if($resultProject->num_rows > 0){
                                                                                            while($resProject = $resultProject->fetch_assoc())
                                                                                            {
                                                                                                $selected_opt="";
                                                                                                if(isset($details_row['country_id']) && $details_row['country_id']==$resProject['country_id']){$selected_opt="selected";}
                                                                                                echo "<option ".$selected_opt." value='".$resProject['country_id']."'>".$resProject['country_symbols'].' '.$resProject['country_code']."</option>";
                                                                                            }
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <div class="form-group">
                                                                                <label>Amount :</label>
                                                                                <input type="number" class="form-control" min="0" placeholder="Amount" name="amounts[]" value="<?php echo $details_row['amount'] ?>" required>
                                                                            </div>
                                                                        </div>
                                                                        <?php 
                                                                            if($dt == "1"){
                                                                                ?>
                                                                                <div class="col-lg-2">
                                                                                    <div class="form-group">
                                                                                        <label></label>
                                                                                        <button type="button" id="add_more" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                                <?php
                                                                            }else{
                                                                                ?>
                                                                                <div class="col-lg-2">
                                                                                    <div class="form-group">
                                                                                        <label></label>
                                                                                        <button type="button" class="remove btn btn-sm btn-danger"><i class="fa fa-minus"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                                <?php
                                                                            }
                                                                         ?>
                                                                    </div>
                                                                    <?php
                                                                    $dt++;
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <input type="submit" class="btn btn-primary viewreport" name="updateInvoice" value="Update Invoice">
                                                                <!-- <input class="btn btn-danger cancel_btn" onClick="window.location.reload()" type="reset" value="Cancel">     -->
                                                                <a class="btn btn-danger cancel_btn"  href="addInvoice.php">Cancel</a>    
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            
                        }
                    }
                ?>
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            View All Invoice
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table width="100%" id="invoice_tbl" class="table table-striped table-bordered">
                                        <thead>
                                            <tr role="row">
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Invoice ID.</th>
                                                <th>Invoice No.</th>
                                                <th>Person Name</th>
                                                <th>Project Name</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 1;
                                                $amounts = 0;
                                                $invoice_qry = "SELECT invoiceTbl.*, projectsTbl.projectName FROM invoiceTbl left join projectsTbl on projectsTbl.id = invoiceTbl.project_Id order by invoiceTbl.invoice_id DESC";
                                                $invoice_result = mysqli_query($conn,$invoice_qry); 
                                                if($invoice_result->num_rows > 0){
                                                    while($invoice_row = $invoice_result->fetch_assoc()){
                                                        echo "<tr class='gradeA even' role='row'>";
                                                            echo "<td>".$i."</td>";
                                                            echo "<td>".$invoice_row['invoice_date']."</td>";
                                                            echo "<td>".$invoice_row['invoice_id']."</td>";
                                                            echo "<td>".$invoice_row['invoice_no']."</td>";
                                                            echo "<td>".$invoice_row['person_name']."</td>";
                                                            echo "<td>".$invoice_row['projectName']."</td>";
                                                            echo "<td>".$invoice_row['total_amount']."</td>";
                                                            echo '<td class="center" align="center">
                                                            <a href="editInvoice.php?edit='.$invoice_row["invoice_id"].'" style="display: inline-block;width: 28px;" title="Edit"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                                                            <a href="deleteInvoice.php?delete='.$invoice_row["invoice_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;" title="Delete"><i class="fa fa-trash-o"></i></a>
                                                            </td>';
                                                        echo "</tr>";
                                                        $i++;
                                                        $amounts += $invoice_row['total_amount'];
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
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../vendor/metisMenu/metisMenu.min.js"></script>
<script src="../vendor/raphael/raphael.min.js"></script>
<script src="../vendor/morrisjs/morris.min.js"></script>
<script src="../data/morris-data.js"></script>
<script src="../dist/js/sb-admin-2.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        /*
        $("#add_more").click(function(){
            $("#perticulars_div").append('<div class="perticulars"><div class="col-lg-6"><div class="form-group"><label>Perticular :</label><input type="text" class="form-control" placeholder="Perticular" name="perticulars[]" required></div></div><div class="col-lg-4"><div class="form-group"><label>Amount :</label><input type="number" class="form-control" min="0" placeholder="Amount" name="amounts[]" required></div></div><div class="col-lg-2"><div class="form-group"><label></label><button type="button" class="remove btn btn-sm btn-danger"><i class="fa fa-minus"></i></button></div></div></div>');
        });
        */
        $("#add_more").click(function(){
            $("#perticulars_div").append('<div class="perticulars"><div class="col-lg-3"><div class="form-group"><label>Perticular :</label><input type="text" class="form-control" placeholder="Perticular" name="perticulars[]" required></div></div><div class="col-lg-2"><div class="form-group"><label>Bank Account :</label><select name="bank_id[]" id="bank_id" required  class="form-control"><option value="">Select Bank</option> <?php $qryProject = "SELECT * FROM account ORDER BY account_id DESC";$resultProject = mysqli_query($conn, $qryProject);$bankOptions = '';if ($resultProject->num_rows > 0) {while ($resProject = $resultProject->fetch_assoc()) { echo '<option value="' . $resProject['account_id'] . '">' . $resProject['account_name'] . '</option>';} } ?></select></div></div><div class="col-lg-2"><div class="form-group"><label>Currency code :</label><select name="country_id[]" id="country_id" required  class="form-control"><option value="">Select Code</option><?php 
                 $qryProject = "SELECT * FROM country order by country_id desc";
                                                        $resultProject = mysqli_query($conn,$qryProject);
                                                        if($resultProject->num_rows > 0){
                                                            while($resProject = $resultProject->fetch_assoc()){
                                                               
                                                                echo '<option value="' . $resProject['country_id'] . '">' . $resProject['country_symbols'].''. $resProject['country_code'].'</option>';
                                                            }
                                                        }

             ?></select></div></div><div class="col-lg-3"><div class="form-group"><label>Amount :</label><input type="number" class="form-control" min="0" placeholder="Amount" name="amounts[]" required></div></div><div class="col-lg-2"><div class="form-group"><label></label><button type="button" class="remove btn btn-sm btn-danger"><i class="fa fa-minus"></i></button></div></div></div>');
        });
        $("body").on("click",".remove",function(){ 
            $(this).parents(".perticulars").remove();
        });
    });
</script>
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
        $('#invoice_tbl').DataTable();
    });
</script>
<script type="text/javascript">
    function getPerson(proj_id){
        $('#person_name').val("");
        $.ajax({
            type:'post',
            url:'getPerson.php',
            data: {
                proj_id:proj_id,
            },
            dataType: 'JSON',
            success: function (response){
                $('#person_name').val(response.data.customerName);
            }
        });
    }
</script>
<?php include 'footer.php'; ?>