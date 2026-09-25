<?php
$active_menu = "setting";
$active_submenu = "add_bank_details";


include "header.php";

if (isset($_GET["edit"])) {
    $id = $_GET["edit"];
    $update = true;
    $getAccountQry = "select * from bank_details where bank_id = $id";
    $getAccountResult = mysqli_query($conn, $getAccountQry);
    $getAccountRes = mysqli_fetch_assoc($getAccountResult);
    //echo '<pre>'; print_r($getAccountRes);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    $bank_name = $getAccountRes["bank_name"];
    $id = $getAccountRes["bank_id"];
}
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if (!empty($flash['message'])): ?>
        <div class="alert alert-<?php echo oecrm_h($flash['type']); ?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo oecrm_h($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <h4>Manage Bank Details</h4>
            <div class="dataTablesbox2 dataTablesbox">
                <form role="form" method="POST" action="addBankDetails.php" novalidate>
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="action" value="save_bank_details">
                    <input type="hidden" name="id" value="<?php echo (int) $bank['bank_id']; ?>">
                    <div class="row">
                        <div class="col-lg-3">
                            <label>Name <span class="text-danger">*</span></label>
                            <div class="form-group">

                                <input type="text" class="form-control" name="bank_name" value="<?php if (
                                    isset($_GET["edit"])
                                ) {
                                    echo $bank_name;
                                } ?>" required maxlength="20">

                            </div>
                        </div>
                        <div class="col-lg-3" align="right">
                            <label>&nbsp;</label>
                            <div class="form-group">

                                
                                <?php if ($update == false): ?>
                                    <input type="submit" class="btn btn-primary viewreport" name="addBankDetails" value="Add">
                                    <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                <?php else: ?>
                                    <input type="submit" class="btn btn-primary viewreport" name="updateBankDetails" value="Update">
                                    <!-- <input class="btn btn-danger cancel_btn" type="reset" value="Cancel"> -->
                                <?php endif; ?>  


                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Bank Table</div>
                <div class="panel-body table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bankRows && mysqli_num_rows($bankRows) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($bankRows)): ?>
                                    <tr>
                                        <td><?php echo oecrm_h($row['bank_name']); ?></td>
                                        <td class="center text-center">
                                            <?php if (oecrm_bank_details_can_manage($conn, 'edit')): ?>
                                                <a class="oecrm-action-btn" href="addBankDetails.php?edit=<?php echo (int) $row['bank_id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <?php endif; ?>
                                            <?php if (oecrm_bank_details_can_manage($conn, 'delete')): ?>
                                                <form method="post" action="addBankDetails.php" style="display:inline;">
                                                    <?php echo oecrm_csrf_field(); ?>
                                                    <input type="hidden" name="action" value="delete_bank_details">
                                                    <input type="hidden" name="id" value="<?php echo (int) $row['bank_id']; ?>">
                                                    <button type="submit" class="btn btn-link oecrm-action-btn oecrm-action-danger" data-confirm="Are you sure you want to delete this bank record?" title="Delete"><i class="fa fa-trash-o"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center">No bank records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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

                                            <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 60%;">Name</th>

                                        
                                        
                                            <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 10%; text-align:center;">Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        $qryNotice =
                                            "SELECT * FROM bank_details";

                                        $currencyResult = mysqli_query(
                                            $conn,
                                            $qryNotice
                                        );

                                        if ($currencyResult->num_rows > 0) {
                                            while (
                                                $rowNotice = $currencyResult->fetch_assoc()
                                            ) {
                                                echo "<tr class='gradeA even' role='row'>";

                                                echo "<td>" .
                                                    $rowNotice["bank_name"] .
                                                    "</td>";

                                                echo '<td class="center" align="center"><a href="addBankDetails.php?edit=' .
                                                    $rowNotice["bank_id"] .
                                                    '" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                                                    
                                                    <a href="addBankDetails.php?bank_delete=' .
                                                    $rowNotice["bank_id"] .
                                                    '" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a>
                                                    
                                                    </td>';

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

<?php include "footer.php"; ?>
