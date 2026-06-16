<?php 
$active_menu= 'setting';
$active_submenu='setting_currency';
include 'header.php'; 
?>

<div id="page-wrapper">

    <div class="row">

        <div class="col-lg-12">
            <h4>Currency</h4>
            <div class="dataTablesbox2 dataTablesbox">

                <form role="form" method="POST">


                    <div class="row">

                        <div class="col-lg-3">

                            <label>Name :</label>

                            <div class="form-group">

                                <input type="text" class="form-control" name="name" required maxlength="20">

                            </div>

                        </div>
                        <div class="col-lg-3">

                            <label>Symbol :</label>

                            <div class="form-group">

                                <input type="text" class="form-control" name="symbol" required maxlength="3">

                            </div>

                        </div>

                        <div class="col-lg-3">

                            <label>Rate :</label>

                            <div class="form-group">

                                <input type="number" class="form-control" name="rate" required>

                            </div>

                        </div>


                        <div class="col-lg-3" align="right">

                            <label>&nbsp;</label>

                            <div class="form-group">

                                <input type="submit" class="btn btn-primary viewreport" name="addCurrency" value="Add Currency">



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

                    Currency Table

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

                                            <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Symbol</th>

                                            <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Rate</th>


                                            <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 10%; text-align:center;">Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $qryNotice = "SELECT * FROM currency_master";

                                        $currencyResult = mysqli_query($conn, $qryNotice);

                                        if ($currencyResult->num_rows > 0) {

                                            while ($rowNotice = $currencyResult->fetch_assoc()) {

                                                echo "<tr class='gradeA even' role='row'>";

                                                echo "<td>" . $rowNotice['name'] . "</td>";

                                                echo "<td>" . $rowNotice['symbol'] . "</td>";

                                                echo "<td>" . $rowNotice['rate'] . "</td>";

                                                echo '<td class="center" align="center"><a href="editCurrency.php?edit=' . $rowNotice["id"] . '" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a href="deleteCurrency.php?delete=' . $rowNotice["id"] . '" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a></td>';

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