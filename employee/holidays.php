<?php include 'header.php'; ?>
        <div id="page-wrapper">
            <section class="table-box page-detail">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading panel-box">
                            <h4>Holiday list 2024</h4>
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
                                <section class="table-section">
                                <div class="row">
                                <div class="col-sm-12">
                         <!-- <table class="table table-striped table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                                <thead>
                                    <tr role="row" class="panel-heading">
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Holiday Date</th>
                                        <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Title</th>
                                        <!-- <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">Notice</th> -->
                                        <!-- <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        /*$qry = "SELECT * FROM holidayTbl";*/
                                        $qry = "SELECT * FROM `holidaytbl` ORDER BY `holidaytbl`.`holidayDate` ASC ";
                                        $result = mysqli_query($conn,$qry);
                                        if($result->num_rows > 0){
                                            while($row = $result->fetch_assoc()){
                                            echo "<tr class='gradeA even' role='row'>";
                                                /*echo "<td class='sorting_1'>".$row['holidayDate']."</td>";*/
                                                echo "<td>".date('d-m-Y', strtotime($row['holidayDate']))."</td>";
                                                echo "<td>".$row['holidayTitle']."</td>";
                                                /*echo "<td>".$row['holidayNotice']."</td>";*/
                                                //echo "<td class='center' align='center'><a href='editNotice.php?editNotice=".$row['id']."'><i class='fa fa-edit' style='font-size:22px;'></i></a>&nbsp;&nbsp;<a href='deleteNotice.php?deleteNotice=".$row['id']."'><i class='fa fa-trash-o' style='font-size:25px; color:red;'></i></a></td>";
                                            echo "</tr>";
                                            }
                                        }
                                    ?>                                  
                                </tbody>
                     </table> -->

                     <!-- add by me -->
                     <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">

                         <thead>

                            <tr role="row">

                                <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Holiday Date</th>

                                <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Holiday Title</th>

                            </tr>

                          </thead>

                            <tbody>

                                <?php 

                                    /*$qry = "SELECT * FROM holidayTbl";*/

                                    $qry = "SELECT * FROM `holidayTbl` ORDER BY `holidayTbl`.`holidayDate` ASC ";

                                    $result = mysqli_query($conn,$qry);

                                    if($result->num_rows > 0){

                                        while($row = $result->fetch_assoc()){

                                        echo "<tr class='gradeA even' role='row'>";

                                            /*echo "<td class='sorting_1'>".$row['holidayDate']."</td>";*/

                                            echo "<td>".date('d-m-Y', strtotime($row['holidayDate']))."</td>";

                                            echo "<td>".$row['holidayTitle']."</td>";

                                        echo "</tr>";

                                        }

                                    }

                                ?>                                  

                            </tbody>

                    </table>

                     <!-- end -->
                        </div>
                    </div>
                </section>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
        </section>
            
        </div>
        <!-- /#page-wrapper -->

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
    /*$(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });*/
    $(document).ready(function() {
        $('#example').DataTable( {
            "paging":   false,
            "ordering": false,
            "info":     false,
            sort:false
        });
    });
    /*var table = $('#example').DataTable( {
        keys: true
    } );
     
    table.keys.disable();*/
    </script>
<?php include 'footer.php'; ?>
