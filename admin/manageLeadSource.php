<?php 

include 'header.php';
 ?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="row">
        <div class="col-lg-12">
            <div class="dataTablesbox">
                <form role="form" method="POST">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Lead Source</label>
                            <input class="form-control" value="" required type="text" name="name">
                        </div>

                    </div>
                    <div class="col-lg-2">
                        <br>
                        <div class="form-group" align="right">
                            <input class="btn btn-primary" type="submit" name="addLeadSource" value="Add Lead Source" style="margin-top: 7px;">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <br>
                        <div class="form-group" align="left">
                            <input class="btn btn-danger cancel_btn" type="reset" value="Cancel" style="margin-top: 7px;">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="manage_designation">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Lead Source Table
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
                                                <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">No.</th>
                                                <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Title</th>
                                                <!-- <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">Notice</th> -->
                                                <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $qry = "SELECT * FROM lead_source_tbl";
                                            $result = mysqli_query($conn, $qry);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr class='gradeA even' role='row'>";
                                                    echo "<td class='sorting_1'>" . $row['id'] . "</td>";
                                                    echo "<td>" . $row['name'] . "</td>";
                                                    /*echo "<td class='center' align='center'>&nbsp;&nbsp;<a href='deleteDesignation.php?deleteDesignation=".$row['id']."'><i class='fa fa-trash-o' style='font-size:25px; color:red;'></i></a></td>";*/
                                                    echo '<td class="center" align="center"><a href="deleteLeadSource.php?deleteLeadSource=' . $row["id"] . '" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></a></td>';
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td>";
                                                echo "Nothing to display";
                                                echo "</td></tr>";
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
            <!-- /.col-lg-12 -->
        </div>
    </div>
</div>



</div>
<!-- /#wrapper -->

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
        $('#dataTables-example').DataTable({
            responsive: false,
            "paging": false,
            "ordering": false,
            "info": false,
            "searching": false
        });
    });
</script>

<?php include 'footer.php'; ?>