<?php include 'header.php'; ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            View All Domain/Hosting
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
                                        <!-- <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Project Name</th> -->
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 110px;">Sr No.</th>
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Client Name</th>
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 150px;">Service</th>
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Domain Name</th>
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 148px;">Start Date</th>
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">End Date</th>
                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 110px;">Amount</th>
                                        <th tabindex="0" rowspan="1" colspan="1"  style="width: 110px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $i = 1;
                                        $displayhosting = "select * from domainHostingTbl";
                                        $result = mysqli_query($conn,$displayhosting);
                                        if($result->num_rows > 0){
                                            while($row = $result->fetch_assoc()){
                                            echo "<tr class='gradeA even' role='row'>";
                                                echo "<td>".$i."</td>";
                                                echo "<td>".$row['clientname']."</td>";
                                                echo "<td>".$row['service']."</td>";
                                                echo "<td>".$row['domainname']."</td>";
                                                echo "<td>".$row['startdate']."</td>";
                                                echo "<td>".$row['enddate']."</td>";
                                                echo "<td>".$row['amount']."</td>";
                                                echo '<td><a href="editDomainHosting.php?edit='.$row["domain_id"].'"><i class="fa fa-pencil" style="font-size:22px;"></i></a>&nbsp;&nbsp;
                                                <a href="deleteDomainHosting.php?delete='.$row["domain_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></a></td>';
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
                <!-- /.col-lg-12 -->
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
                /*responsive: true,*/
                order: [[4, 'desc']],
                columnDefs: [
                { "orderable": false, "targets": 5 }
                ]
            });
        });
    </script>
<?php include 'footer.php'; ?>
