<?php include 'header.php'; ?> 
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default deposit_table">
                        <div class="panel-heading">
                            Add Deposit
                        </div>
                        <div class="panel-body add_deposit">
                        	<div class="dataTablesbox2">
                                <form role="form" method="POST">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Date :</label>
                                                <input type="date" class="form-control" name="deposit_date" required value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Project Name :</label>
                                                <select name="project_Id" id="project_Id" class="form-control" onchange="getInvoices(this.value)" required>
                                                    <option value="">Select Project Name</option>
                                                    <?php 
                                                        $qryProject = "SELECT * FROM projectstbl where status IN ('pending','inprogress') order by id desc";
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
                                                                echo "<option value='".$resAccount['account_id']."'>".$resAccount['account_name']."</option>";
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
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="subject" value="">
                                        <input type="hidden" name="amount" value="">
                                    </div>
                                    <div class="row">
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
                            View All Deposits
                        </div>
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
                                <!-- <div class="row">
                                    <div class="col-sm-12">
                                        <div class="search_box_area area2">
                                                <div class="row">
                                                    <div class="form-group col-md-3">
                                                        <select name="project_Id" id="project" class="form-control">
                                                            <option value="all" selected>Select Project Name</option>
                                                            <?php 
                                                                $qryProject = "SELECT * FROM projectstbl";
                                                                $resultProject = mysqli_query($conn,$qryProject);
                                                                if($resultProject->num_rows > 0){
                                                                    while($resProject = $resultProject->fetch_assoc()){
                                                                        echo "<option value='".$resProject['id']."'>".$resProject['projectName']."</option>";
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <select name="account_Id" id="account" class="form-control" required>
                                                            <option value="">Select Account Name</option>
                                                            <?php 
                                                                $qryAccount = "SELECT * FROM account";
                                                                $resultAccount = mysqli_query($conn,$qryAccount);
                                                                if($resultAccount->num_rows > 0){
                                                                    while($resAccount = $resultAccount->fetch_assoc()){
                                                                        echo "<option value='".$resAccount['account_id']."'>".$resAccount['account_name']."</option>";
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <input type="date" name="startdate" id="startdate" class="form-control">
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <input type="date" name="enddate" id="enddate" class="form-control">
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div> -->
                                    <div class="col-sm-12">
                                        <table width="100%" id="deposit_table" class="table table-striped table-bordered">
                                            
                                            <thead>
                                                <tr role="row">
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Invoice No.</th>
                                                    <th>Account Name</th>
                                                    <th>Project Name</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $i = 1;
                                                    $amounts = 0;
                                                    $qryDeposit = "SELECT * FROM deposit order by deposit_id desc";
                                                    $resultDeposit = mysqli_query($conn,$qryDeposit); 
                                                    if($resultDeposit->num_rows > 0){
                                                        while($rowDeposit = $resultDeposit->fetch_assoc()){
                                                            echo "<tr class='gradeA even' role='row'>";
                                                                echo "<td>".$i."</td>";
                                                                echo "<td>".$rowDeposit['deposit_date']."</td>";
                                                                echo "<td>".$rowDeposit['invoice_id']."</td>";
                                                                $qryAccount = "SELECT * FROM account where account_id=".$rowDeposit['account_Id'];
                                                                $resultAccount = mysqli_query($conn,$qryAccount);
                                                                $rowAccount = $resultAccount->fetch_assoc();
                                                                echo "<td>".$rowAccount['account_name']."</td>";
                                                                $qryProject = "SELECT * FROM projectstbl where id=".$rowDeposit['project_Id'];
                                                                $resultProject = mysqli_query($conn,$qryProject);
                                                                $resProject = $resultProject->fetch_assoc();
                                                                echo "<td>".$resProject['projectName']."</td>";
                                                                echo "<td>".$rowDeposit['amount']."</td>";
                                                                echo '<td class="center" align="center">

                                                                <a href="editDeposit.php?edit='.$rowDeposit["deposit_id"].'" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;

                                                                <a href="viewInvoice.php?id='.$rowDeposit["invoice_id"].'" style="display: inline-block;width: 28px;" title="View Invoice"><i class="fa fa-eye"></i></a>

                                                                <a href="deleteDeposit.php?delete='.$rowDeposit["deposit_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a>
                                                               
                                                                </td>';
                                                            echo "</tr>";
                                                            $i++;
                                                            $amounts += $rowDeposit['amount'];
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
        $('#deposit_table').DataTable({
            /*responsive: true,*/
            // order: [[4, 'desc']],
            // columnDefs: [
            // { "orderable": false, "targets": 5 }
            // ]
        });
    });
</script>
<?php include 'footer.php'; ?>
