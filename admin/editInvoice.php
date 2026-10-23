<?php
    include 'header.php';
require_once __DIR__ . '/../invoiceStatus.php';
require_once __DIR__ . '/../invoiceTax.php';
oecrm_invoice_tax_schema($conn);
$invoiceCompanyId = (int) oecrm_current_company_id($conn);
?>
        <div id="page-wrapper">
            <?php oecrm_legacy_invoice_flash(); ?>
            <div class="row">
                <?php
                    if(isset($_GET['edit'])){
                        $edit_id = (int) $_GET['edit'];
                        $edit_qry = "SELECT * FROM invoicetbl where invoice_id = '$edit_id' limit 1";
                        $edit_result = oecrm_invoice_schema_query($conn,$edit_qry);
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
                                                <?php echo oecrm_legacy_invoice_status_badge($edit_invoice); ?>
                                                <?php oecrm_legacy_invoice_cancel_form($conn, $edit_invoice, 'editInvoice.php'); ?>
                                                <?php if ((int) $edit_invoice['status'] === 3): ?>
                                                    <div class="alert alert-warning" style="margin-top:12px;">This invoice is cancelled. Its details are kept for reference.</div>
                                                <?php endif; ?>
                                                <form role="form" method="POST" action="invoices.php">
                                                    <fieldset <?php echo (int) $edit_invoice['status'] === 3 ? 'disabled' : ''; ?>>
                                                    <?php echo oecrm_csrf_field(); ?>
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
                                                                <select name="project_Id" id="project_Id"  required class="form-control">
                                                                    <option value="">Select Project Name</option>
                                                                    <?php
                                                                        $savedProjectId = (int) $edit_invoice['project_Id'];
                                                                        $qryProject = "SELECT id,projectName FROM projectstbl WHERE company_id=$invoiceCompanyId AND (status IN ('pending','inprogress') OR id=$savedProjectId) ORDER BY projectName,id";
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
                                                                                <input type="number" class="form-control" min="0" step="0.01" placeholder="Amount" name="amounts[]" value="<?php echo $details_row['amount'] ?>" required>
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
                                                    <?php oecrm_invoice_tax_fields($edit_invoice); ?>
                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <input type="submit" class="btn btn-primary viewreport" name="updateInvoice" value="Update Invoice">
                                                                <!-- <input class="btn btn-danger cancel_btn" onClick="window.location.reload()" type="reset" value="Cancel">     -->
                                                                <a class="btn btn-danger cancel_btn"  href="addInvoice.php">Back to Invoices</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </fieldset>
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
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 1;
                                                $amounts = 0;
                                                $invoice_qry = "SELECT invoicetbl.*, projectstbl.projectName FROM invoicetbl left join projectstbl on projectstbl.id = invoicetbl.project_Id order by invoicetbl.invoice_id DESC";
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
                                                            echo "<td>" . oecrm_legacy_invoice_status_badge($invoice_row) . "</td>";
                                                            echo '<td class="center" align="center">
                                                            <a href="editInvoice.php?edit='.$invoice_row["invoice_id"].'" title="Edit"><i class="fa fa-pencil"></i></a>
                                                            ';
                                                            oecrm_legacy_invoice_cancel_form($conn, $invoice_row, 'editInvoice.php', true);
                                                            echo '
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
            $("#perticulars_div").append('<div class="perticulars"><div class="col-lg-6"><div class="form-group"><label>Perticular :</label><input type="text" class="form-control" placeholder="Perticular" name="perticulars[]" required></div></div><div class="col-lg-4"><div class="form-group"><label>Amount :</label><input type="number" class="form-control" min="0" step="0.01" placeholder="Amount" name="amounts[]" required></div></div><div class="col-lg-2"><div class="form-group"><label></label><button type="button" class="remove btn btn-sm btn-danger"><i class="fa fa-minus"></i></button></div></div></div>');
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

             ?></select></div></div><div class="col-lg-3"><div class="form-group"><label>Amount :</label><input type="number" class="form-control" min="0" step="0.01" placeholder="Amount" name="amounts[]" required></div></div><div class="col-lg-2"><div class="form-group"><label></label><button type="button" class="remove btn btn-sm btn-danger"><i class="fa fa-minus"></i></button></div></div></div>');
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
<script src="../js/oecrm-invoice-tax.js?v=20261021-2"></script>
<?php include 'footer.php'; ?>
