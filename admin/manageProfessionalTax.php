<?php include 'header.php'; ?>
<?php 
    if(isset($_POST['addProfessionalTax'])){
        $startingAmount = mysqli_real_escape_string($conn,$_POST['startingAmount']);
        $endingAmount = mysqli_real_escape_string($conn,$_POST['endingAmount']);
        $professionalTax = mysqli_real_escape_string($conn,$_POST['professionalTax']);
        $qryAddProTax = "INSERT INTO professionalTaxTbl (startingAmount, endingAmount, professionalTax) VALUES ('$startingAmount', '$endingAmount', '$professionalTax')";
        if (mysqli_query($conn,$qryAddProTax)){
            echo "Data inserted succesfully.";
            echo "<meta http-equiv='refresh' content='0'>";
        }else{
            echo "Data not inserted succesfully.";
        }
    }
 ?>
 <!-- /*            header('Location:manageProfessionalTax.php');*/ -->
		<div id="page-wrapper">
            <div class="panel panel-default managetax">
                <div class="panel-heading">Add New Professional Tax</div>
                <div class="panel-body">
                    <div class="search_box_area">
            			<form method="POST">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group col-lg-2">
                                        <label>*From</label>
                                        <input type="number" class="form-control" name="startingAmount" placeholder="Starting Amount" required>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label>*To </label>
                                        <input type="number" class="form-control" name="endingAmount" placeholder="Ending Amount" required>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label>*Tax</label>
                                        <input type="number" class="form-control" name="professionalTax" placeholder="Tax" required>
                                    </div>
                                    <div class="form-group col-lg-1">
                                        <br>
                                            <input type="submit" class="btn btn-primary profeesionaltax_btn" name="addProfessionalTax" value="Add">
                                    </div>
                                    <div class="form-group col-lg-1">
                                        <br>
                                            <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                    </div>
                                </div>
                			</div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Professional Tax Table
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
                                                <th class="sorting_asc center-bold" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">From</th>
                                                <th class="sorting_asc center-bold" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">To</th>
                                                <th class="sorting_asc center-bold" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Tax Amount</th>
                                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $qryProTax = "SELECT * FROM professionalTaxTbl";
                                                $resultProTax = mysqli_query($conn,$qryProTax);
                                                if($resultProTax->num_rows > 0){
                                                    while($rowProTax = $resultProTax->fetch_assoc()){
                                                        echo "<tr class='gradeA even' role='row'>";
                                                            echo "<td class='center-bold'>".$rowProTax['startingAmount']."</td>";
                                                            echo "<td class='center-bold'>".$rowProTax['endingAmount']."</td>";
                                                            echo "<td class='center-bold'>".$rowProTax['professionalTax']."</td>";
                                                            echo '<td class="center" align="center"><a href="deleteProfessionalTax.php?delete='.$rowProTax["id"].'" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></a></td>';
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