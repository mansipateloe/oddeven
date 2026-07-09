<?php
$active_menu = 'setting';
$active_submenu = 'add_bank_details';
include 'header.php';

$id = 0;
$update = false;
$bank_name = '';
$flash = $_SESSION['bank_details_flash'] ?? '';
unset($_SESSION['bank_details_flash']);

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $update = true;
    $getAccountQry = "SELECT * FROM bank_details WHERE bank_id = $id";
    $getAccountResult = mysqli_query($conn, $getAccountQry);
    $getAccountRes = mysqli_fetch_assoc($getAccountResult);
    if ($getAccountRes) {
        $bank_name = $getAccountRes['bank_name'];
        $id = (int) $getAccountRes['bank_id'];
    } else {
        $update = false;
        $id = 0;
    }
}
?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4>Manage Bank Details</h4>
            <?php if ($flash): ?>
                <div class="alert alert-info"><?php echo oecrm_h($flash); ?></div>
            <?php endif; ?>
            <div class="dataTablesbox2 dataTablesbox">
                <form role="form" method="POST" action="validation.php">
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
                    <div class="row">
                        <div class="col-lg-3">
                            <label>Name :</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="bank_name" value="<?php echo oecrm_h($bank_name); ?>" required maxlength="50">
                            </div>
                        </div>
                        <div class="col-lg-3" align="right">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <?php if (!$update): ?>
                                    <input type="submit" class="btn btn-primary viewreport" name="addBankDetails" value="Add">
                                    <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                <?php else: ?>
                                    <input type="submit" class="btn btn-primary viewreport" name="updateBankDetails" value="Update">
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
                <div class="panel-body">
                    <div class="table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="bank-details-table" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th style="width: 60%;">Name</th>
                                    <th style="width: 10%; text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $qryNotice = "SELECT * FROM bank_details ORDER BY bank_name";
                                $currencyResult = mysqli_query($conn, $qryNotice);
                                if ($currencyResult && $currencyResult->num_rows > 0) {
                                    while ($rowNotice = $currencyResult->fetch_assoc()) {
                                        echo "<tr class='gradeA even' role='row'>";
                                        echo "<td>" . oecrm_h($rowNotice['bank_name']) . "</td>";
                                        echo '<td class="center" align="center"><a href="addBankDetails.php?edit=' . (int) $rowNotice["bank_id"] . '" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<form method="post" action="validation.php" style="display:inline;">' . oecrm_csrf_field() . '<input type="hidden" name="deleteBankDetails" value="' . (int) $rowNotice["bank_id"] . '"><button type="submit" class="btn btn-link" style="padding:0;border:0;display:inline-block;width:28px;" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o"></i></button></form></td>';
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

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../vendor/metisMenu/metisMenu.min.js"></script>
<script src="../vendor/raphael/raphael.min.js"></script>
<script src="../vendor/morrisjs/morris.min.js"></script>
<script src="../data/morris-data.js"></script>
<script src="../dist/js/sb-admin-2.js"></script>

<?php include 'footer.php'; ?>
