<?php include 'header.php'; ?> 

<?php 
    $id = '';
    $account_name = '';
    $balance = '';
    $update = false;

    if (isset($_GET['edit'])) {
        $id = (int) $_GET['edit'];
        $update = true;
        $getAccountQry = "select * from account where account_id = $id";
        $getAccountResult = mysqli_query($conn, $getAccountQry);
        $getAccountRes = mysqli_fetch_assoc($getAccountResult);
        if ($getAccountRes) {
            $account_name = $getAccountRes['account_name'];
            $balance = $getAccountRes['balance'];
            $id = $getAccountRes['account_id'];
        }
    }
?><div id="page-wrapper" class="compact-admin-page">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Add Account
                        </div>
                        <div class="panel-body add_account">
                        	<div class="dataTablesbox2">
                                <form role="form" method="POST">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Account Name :</label>
                                                <input type="text" class="form-control" minlength="3" maxlength="50" name="account_name" placeholder="Account Name" value="<?php echo $account_name; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Initial Balance :</label>
                                                <input type="text" class="form-control" min="0" pattern="[0-9]+" name="balance" placeholder="Balance" value="<?php echo $balance; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                            <?php if($update == false): ?>
                                                <input type="submit" class="btn btn-primary viewreport" name="addAccount" value="Add Account">
                                                <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                            <?php else: ?>
                                                <input type="submit" class="btn btn-primary viewreport" name="updateAccount" value="Update Account">
                                                <!-- <input class="btn btn-danger cancel_btn" type="reset" value="Cancel"> -->
                                            <?php endif ?>	    
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
                        <div class="panel-heading">
                            View All Accounts
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
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Sr No.</th>
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Account Name</th>
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Initial Balance</th>
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 10%; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $i = 1; 
                                        $qryAccount = "SELECT * FROM account";
                                        $resultAccount = mysqli_query($conn,$qryAccount);
                                        if($resultAccount->num_rows > 0){
                                            while($rowAccount = $resultAccount->fetch_assoc()){
                                                echo "<tr class='gradeA even' role='row'>";
                                                    echo "<td>".$i."</td>";
                                                    echo "<td>".$rowAccount['account_name']."</td>";
                                                    echo "<td>".$rowAccount['balance']."</td>";
                                                    /*echo "<td class='center' align='center'><a href='editNotice.php?edit=".$rowNotice['id']."'><i class='fa fa-edit' style='font-size:22px;'></i></a>&nbsp;&nbsp;<a href='deleteNotice.php?delete=".$rowNotice['id']."'><i class='fa fa-trash-o' style='font-size:25px; color:red;'></i></a></td>";*/
                                                   echo '<td class="center" align="center">

                                                   <a href="addaccount.php?edit='.$rowAccount["account_id"].'" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;

                                                   <a href="addaccount.php?delete='.$rowAccount["account_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a>
                                                   
                                                   </td>';
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
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
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
GET['edit']))
		{
			$id = $_GET['edit'];
			$update = true;
			$getAccountQry = "select * from account where account_id = $id";
			$getAccountResult = mysqli_query($conn, $getAccountQry);
            $getAccountRes = mysqli_fetch_assoc($getAccountResult);
            //echo '<pre>'; print_r($getAccountRes);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
            $account_name = $getAccountRes['account_name'];
            $balance = $getAccountRes['balance'];
			$id = $getAccountRes['account_id'];
		}

	 ?>	
        <div id="page-wrapper" class="compact-admin-page">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Add Account
                        </div>
                        <div class="panel-body add_account">
                        	<div class="dataTablesbox2">
                                <form role="form" method="POST">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Account Name :</label>
                                                <input type="text" class="form-control" minlength="3" maxlength="50" name="account_name" placeholder="Account Name" value="<?php echo $account_name; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Initial Balance :</label>
                                                <input type="text" class="form-control" min="0" pattern="[0-9]+" name="balance" placeholder="Balance" value="<?php echo $balance; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                            <?php if($update == false): ?>
                                                <input type="submit" class="btn btn-primary viewreport" name="addAccount" value="Add Account">
                                                <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                            <?php else: ?>
                                                <input type="submit" class="btn btn-primary viewreport" name="updateAccount" value="Update Account">
                                                <!-- <input class="btn btn-danger cancel_btn" type="reset" value="Cancel"> -->
                                            <?php endif ?>	    
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
                        <div class="panel-heading">
                            View All Accounts
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
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Sr No.</th>
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Account Name</th>
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Initial Balance</th>
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 10%; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $i = 1; 
                                        $qryAccount = "SELECT * FROM account";
                                        $resultAccount = mysqli_query($conn,$qryAccount);
                                        if($resultAccount->num_rows > 0){
                                            while($rowAccount = $resultAccount->fetch_assoc()){
                                                echo "<tr class='gradeA even' role='row'>";
                                                    echo "<td>".$i."</td>";
                                                    echo "<td>".$rowAccount['account_name']."</td>";
                                                    echo "<td>".$rowAccount['balance']."</td>";
                                                    /*echo "<td class='center' align='center'><a href='editNotice.php?edit=".$rowNotice['id']."'><i class='fa fa-edit' style='font-size:22px;'></i></a>&nbsp;&nbsp;<a href='deleteNotice.php?delete=".$rowNotice['id']."'><i class='fa fa-trash-o' style='font-size:25px; color:red;'></i></a></td>";*/
                                                   echo '<td class="center" align="center">

                                                   <a href="addaccount.php?edit='.$rowAccount["account_id"].'" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;

                                                   <a href="addaccount.php?delete='.$rowAccount["account_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a>
                                                   
                                                   </td>';
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
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
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
