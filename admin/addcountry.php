<?php 
$active_menu= 'setting';
$active_submenu='add_country';
include 'header.php'; 

if(isset($_GET['edit']))
{
    $id = $_GET['edit'];
    $update = true;
    $getAccountQry = "select * from country where country_id = $id";
    $getAccountResult = mysqli_query($conn, $getAccountQry);
    $getAccountRes = mysqli_fetch_assoc($getAccountResult);
    //echo '<pre>'; print_r($getAccountRes);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    $country_symbols = $getAccountRes['country_symbols'];
    $country_code = $getAccountRes['country_code'];
    $id = $getAccountRes['country_id'];
}

?>


<div id="page-wrapper">

    <div class="row">

        <div class="col-lg-12">
            <h4>Manage Country Details</h4>
            <div class="dataTablesbox2 dataTablesbox">

                <form role="form" method="POST">

                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="row">

                        <div class="col-lg-3">

                            <label>Code :</label>

                            <div class="form-group">

                                <input type="text" class="form-control" name="country_code" value="<?php if(isset($_GET['edit'])){ echo $country_code; } ?>" required maxlength="20">

                            </div>

                        </div>
                        <div class="col-lg-3">

                            <label>Symbols :</label>

                            <div class="form-group">

                                <input type="text" class="form-control" name="country_symbols" required maxlength="3" value="<?php if(isset($_GET['edit'])){ echo $country_symbols; } ?>" >

                            </div>

                        </div>

                       
                        <div class="col-lg-3" align="right">

                            <label>&nbsp;</label>

                            <div class="form-group">

                                
                                <?php if($update == false): ?>
                                    <input type="submit" class="btn btn-primary viewreport" name="addCountry" value="Add">
                                    <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                <?php else: ?>
                                    <input type="submit" class="btn btn-primary viewreport" name="updateCountry" value="Update">
                                    <!-- <input class="btn btn-danger cancel_btn" type="reset" value="Cancel"> -->
                                <?php endif ?>  


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

                <div class="panel-heading">

                    Country Table Details

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

                                            <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 60%;">Code</th>

                                            <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Symbol</th>

                                        
                                            <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 10%; text-align:center;">Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $qryNotice = "SELECT * FROM country";

                                        $currencyResult = mysqli_query($conn, $qryNotice);

                                        if ($currencyResult->num_rows > 0) {

                                            while ($rowNotice = $currencyResult->fetch_assoc()) {

                                                echo "<tr class='gradeA even' role='row'>";

                                                echo "<td>" . $rowNotice['country_code'] . "</td>";

                                                echo "<td>" . $rowNotice['country_symbols'] . "</td>";

                                                echo '<td class="center" align="center"><a href="addcountry.php?edit=' . $rowNotice["country_id"] . '" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a href="addcountry.php?country_delete=' . $rowNotice["country_id"] . '" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a></td>';

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