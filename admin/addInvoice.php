<?php include 'header.php'; ?> 
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default deposit_table">
                        <div class="panel-heading">
                            Add Invoice
                        </div>
                        <div class="panel-body add_deposit">
                        	<div>
                                <form role="form" method="POST" action="invoices.php">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Invoice No :</label>
                                                <input type="text" class="form-control" placeholder="Invoice No." name="invoice_no" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Date :</label>
                                                <input type="date" class="form-control" name="invoice_date" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Project Name :</label>
                                                <select name="project_Id" id="project_Id" required onchange="getPerson(this.value)" class="form-control">
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
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Person Name :</label>
                                                <input type="text" class="form-control" name="person_name" id="person_name" value="" required minlength="3" maxlength="200">
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row" id="perticulars_div">
                                        <div class="perticulars">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Perticular :</label>
                                                    <input type="text" class="form-control" placeholder="Perticular" name="perticulars[]" required>
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
                                                                while($resProject = $resultProject->fetch_assoc()){
                                                                    echo "<option value='".$resProject['account_id']."'>".$resProject['account_name']."</option>";
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
                                                                while($resProject = $resultProject->fetch_assoc()){
                                                                    echo "<option value='".$resProject['country_id']."'>".$resProject['country_symbols'].' '.$resProject['country_code']."</option>";
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Amount :</label>
                                                    <input type="number" class="form-control" min="0" placeholder="Amount" name="amounts[]" required>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label></label>
                                                    <button type="button" id="add_more" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-primary viewreport" name="addInvoice" value="Add Invoice">
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
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            //  -- where invoiceTbl.status = 1 
                                                $i = 1;
                                                $amounts = 0;
                                                $invoice_qry = "SELECT invoiceTbl.*, projectsTbl.projectName FROM invoiceTbl left join projectsTbl on projectsTbl.id = invoiceTbl.project_Id 
                                                   
                                                    order by invoiceTbl.invoice_id DESC";
                                                $invoice_result = mysqli_query($conn,$invoice_qry); 
                                                if($invoice_result->num_rows > 0){
                                                    while($invoice_row = $invoice_result->fetch_assoc()){
                                                        $disabled = '';
                                                        if($invoice_row['status'] == 2){
                                                            $status = "Completed";
                                                        }else if($invoice_row['status'] == 1){
                                                            $status = "Pending";
                                                        }
                                                        echo "<tr class='gradeA even' role='row'>";
                                                            echo "<td>".$i."</td>";
                                                            echo "<td>".$invoice_row['invoice_date']."</td>";
                                                            echo "<td>".$invoice_row['invoice_id']."</td>";
                                                            echo "<td>".$invoice_row['invoice_no']."</td>";
                                                            echo "<td>".$invoice_row['person_name']."</td>";
                                                            echo "<td>".$invoice_row['projectName']."</td>";
                                                            echo "<td>".$invoice_row['total_amount']."</td>";
                                                            echo "<td>".$status."</td>";

                                                            echo '<td class="center" align="center">';
                                                            if ($invoice_row['status'] == 2) {
                                                                echo '<a type="button" class="disabled" style="pointer-events: none;"><i class="fa fa-money"></i></a>';
                                                            } else {
                                                                echo '<a type="button" data-toggle="modal" data-target="#myModal' . $invoice_row['invoice_id'] . '"><i class="fa fa-money"></i></a>';
                                                            }
                                                            echo '&nbsp;&nbsp;

                                                            <a href="editInvoice.php?edit='.$invoice_row["invoice_id"].'" style="display: inline-block;width: 28px;" title="Edit"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                                                            <a href="viewInvoice.php?id='.$invoice_row["invoice_id"].'" style="display: inline-block;width: 28px;" title="View"><i class="fa fa-eye"></i></a>
                                                            <a href="deleteInvoice.php?delete='.$invoice_row["invoice_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;" title="Delete"><i class="fa fa-trash-o"></i></a>
                                                            </td>';
                                                        echo "</tr>";

                                                        echo '<div id="myModal'.$invoice_row['invoice_id'].'" class="modal fade" role="dialog">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                            <h4 class="modal-title">Add Deposit</h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form role="form" method="POST">
                                                                                <div class="row">
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Invoice Date :</label>
                                                                                            <input type="date" class="form-control" name="deposit_date" required value='.$invoice_row['invoice_date'].' readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Invoice Id :</label>
                                                                                            <input type="text" class="form-control" name="invoice_id" required value='.$invoice_row['invoice_id'].' readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Invoice No :</label>
                                                                                            <input type="text" class="form-control" name="invoice_no" required value='.$invoice_row['invoice_no'].' readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Amount :</label>
                                                                                            <input type="text" class="form-control" name="amount" required value='.$invoice_row['total_amount'].' readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Project Name :</label>
                                                                                            <select name="projects_Id" id="projects_Id" class="form-control" onchange="getInvoices(this.value)" disabled>';
                                                                                                // <option value="">Select Project Name</option>';
                                                                                                
                                                                                                    $qryProject = "SELECT * FROM projectsTbl
                                                                                                    where status = 'inprogress'  order by id desc";
                                                                                                    $resultProject = mysqli_query($conn,$qryProject);
                                                                                                    if($resultProject->num_rows > 0){
                                                                                                        while($resProject = $resultProject->fetch_assoc()){
                                                                                                            echo "<option value='".$resProject['id']."'>".$invoice_row['projectName']."</option>";
                                                                                                        }
                                                                                                    }
                                                                                               
                                                                                     echo  '</select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Select Date :</label>
                                                                                            <input type="date" class="form-control" name="deposit_date" required value='.date('Y-m-d').'>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Account Name :</label>
                                                                                            <select name="account_Id" id="account_Id" class="form-control" required>
                                                                                                <option value="">Select Account Name</option>';
                                                                                                
                                                                                                    $qryAccount = "SELECT * FROM account";
                                                                                                    $resultAccount = mysqli_query($conn,$qryAccount);
                                                                                                    if($resultAccount->num_rows > 0){
                                                                                                        while($resAccount = $resultAccount->fetch_assoc()){
                                                                                                            echo "<option value='".$resAccount['account_id']."'>".$resAccount['account_name']."</option>";
                                                                                                        }
                                                                                                    }
                                                                                                
                                                                                            echo '</select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="hidden" name="project_Id" value='.$invoice_row['project_Id'].'>
                                                                                    <input type="hidden" name="subject" value="">
                                                                                    <input type="hidden" name="amount" value='.$invoice_row['total_amount'].'>
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Tax Amount :</label>
                                                                                            <input type="text" class="form-control" name="tax_amount" required >
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Approx Amount :</label>
                                                                                            <input type="text" class="form-control" name="approx_amount" required  >
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <label>Remark :</label>
                                                                                            <textarea type="text" class="form-control" placeholder="Remark" rows="5" name="description"></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-lg-6">
                                                                                        <div class="form-group">
                                                                                            <input type="submit" class="btn btn-primary viewreport" name="addDeposit" value="Add Deposit">
                                                                                            <input class="btn btn-danger cancel_btn" class="close" data-dismiss="modal" type="reset" value="Cancel">    
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';

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

<?php

?>
<script type="text/javascript">

    $(document).ready(function(){
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