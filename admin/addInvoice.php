<?php include 'header.php';
require_once __DIR__ . '/../invoiceStatus.php';
require_once __DIR__ . '/../invoiceNumbering.php';
oecrm_invoice_numbering_schema($conn);
$invoiceCompanyId = oecrm_current_company_id($conn);
$invoiceSequence = oecrm_invoice_sequence_preview($conn, $invoiceCompanyId);
?>
        <div id="page-wrapper">
            <?php oecrm_legacy_invoice_flash(); ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default deposit_table">
                        <div class="panel-heading">
                            Add Invoice
                        </div>
                        <div class="panel-body add_deposit">
                            <style>
                                .add-invoice-date-field {
                                    width: 100%;
                                    min-width: 160px;
                                    height: 45px;
                                    line-height: 45px;
                                    padding: 6px 12px;
                                    box-sizing: border-box;
                                }
                            </style>
                        	<div>
                                <form role="form" method="POST" action="invoices.php">
                                    <?php echo oecrm_csrf_field(); ?>
                                    <input type="hidden" name="invoice_company_id" value="<?php echo (int) $invoiceCompanyId; ?>">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Invoice No :</label>
                                                <input type="text" class="form-control" placeholder="Invoice No." name="invoice_no" value="<?php echo oecrm_h($invoiceSequence['next_invoice_number']); ?>" readonly required>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Date :</label>
                                                <input type="date" class="form-control add-invoice-date-field" name="invoice_date" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Project Name :</label>
                                                <select name="project_Id" id="project_Id" required  class="form-control">
                                                    <option value="">Select Project Name</option>
                                                    <?php
                                                        $qryProject = "SELECT * FROM projectstbl where company_id=$invoiceCompanyId AND status IN ('pending','inprogress') order by id desc";
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
                                    <?php include __DIR__ . '/invoiceLineItems.php'; ?>
                                    <?php oecrm_invoice_tax_fields(); ?>
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
                                            //  -- where invoicetbl.status = 1
                                                $i = 1;
                                                $amounts = 0;
                                                $invoice_qry = "SELECT invoicetbl.*, projectstbl.projectName FROM invoicetbl LEFT JOIN projectstbl ON projectstbl.id = invoicetbl.project_Id

                                                    WHERE COALESCE(invoicetbl.company_id,projectstbl.company_id)=$invoiceCompanyId ORDER BY invoicetbl.invoice_id DESC";
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

                                                            echo '<td class="center" align="center">';
                                                            if ((int) $invoice_row['status'] !== 1) {
                                                                echo '<a type="button" class="disabled" style="pointer-events: none;"><i class="fa fa-money"></i></a>';
                                                            } else {
                                                                echo '<a href="addDeposit.php?invoice_id=' . (int) $invoice_row['invoice_id'] . '" title="Collect Invoice"><i class="fa fa-money"></i></a>';
                                                            }
                                                            echo '&nbsp;&nbsp;';

                                                            if ((int) $invoice_row['status'] === 2) {
                                                                echo '<span class="text-muted" style="display: inline-block;width: 28px;" title="Paid invoices cannot be edited" aria-label="Paid invoices cannot be edited"><i class="fa fa-pencil"></i></span>';
                                                            } else {
                                                                echo '<a href="editInvoice.php?edit=' . (int) $invoice_row['invoice_id'] . '" style="display: inline-block;width: 28px;" title="Edit"><i class="fa fa-pencil"></i></a>';
                                                            }
                                                            echo '&nbsp;&nbsp;<a href="viewInvoice.php?id=' . (int) $invoice_row['invoice_id'] . '" style="display: inline-block;width: 28px;" title="View"><i class="fa fa-eye"></i></a>';
                                                            oecrm_legacy_invoice_cancel_form($conn, $invoice_row);
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

<?php

?>

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
