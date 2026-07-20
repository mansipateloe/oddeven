<?php
include 'header.php';

$id = 0;
$account_name = '';
$balance = '';
$update = false;

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $update = true;
    $getAccountQry = "SELECT * FROM account WHERE account_id = $id";
    $getAccountResult = mysqli_query($conn, $getAccountQry);
    $getAccountRes = mysqli_fetch_assoc($getAccountResult);
    if ($getAccountRes) {
        $account_name = $getAccountRes['account_name'];
        $balance = $getAccountRes['balance'];
        $id = $getAccountRes['account_id'];
    }
}
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Add Account</div>
                <div class="panel-body add_account">
                    <div class="dataTablesbox2">
                        <form role="form" method="POST">
                            <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Account Name :</label>
                                        <input type="text" class="form-control" minlength="3" maxlength="50" name="account_name" placeholder="Account Name" value="<?php echo oecrm_h($account_name); ?>" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Initial Balance :</label>
                                        <input type="text" class="form-control" min="0" pattern="[0-9]+" name="balance" placeholder="Balance" value="<?php echo oecrm_h($balance); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <?php if (!$update): ?>
                                            <input type="submit" class="btn btn-primary viewreport" name="addAccount" value="Add Account">
                                            <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                        <?php else: ?>
                                            <input type="submit" class="btn btn-primary viewreport" name="updateAccount" value="Update Account">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">View All Accounts</div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th style="width: 10%;">Sr No.</th>
                                    <th style="width: 10%;">Account Name</th>
                                    <th style="width: 10%;">Initial Balance</th>
                                    <th style="width: 10%; text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $qryAccount = "SELECT * FROM account ORDER BY account_id DESC";
                                $resultAccount = mysqli_query($conn, $qryAccount);
                                if ($resultAccount && $resultAccount->num_rows > 0) {
                                    while ($rowAccount = $resultAccount->fetch_assoc()) {
                                        echo "<tr class='gradeA even' role='row'>";
                                        echo "<td>" . $i . "</td>";
                                        echo "<td>" . oecrm_h($rowAccount['account_name']) . "</td>";
                                        echo "<td>" . oecrm_h($rowAccount['balance']) . "</td>";
                                        echo '<td class="center" align="center"><a href="addaccount.php?edit=' . (int) $rowAccount["account_id"] . '" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a href="addaccount.php?delete=' . (int) $rowAccount["account_id"] . '" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a></td>';
                                        echo "</tr>";
                                        $i++;
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
