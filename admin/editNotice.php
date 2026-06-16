<?php include 'header.php'; ?>

<?php 

    $id = $_GET['edit'];

    $qryView = "SELECT * FROM noticeTbl WHERE id=".$id;

    $resultView = mysqli_query($conn,$qryView);

    $rowView = $resultView->fetch_assoc();

?> 

        <div id="page-wrapper">

            <div class="row">

                <div class="col-lg-12">

                <div class="dataTablesbox2 dataTablesbox">

                    <form role="form" method="POST">

                        <div class="row">

                            <div class="col-lg-9">

                                <div class="form-group">

                                    <label>Notice :</label>

                                    <textarea class="form-control" name="notice" rows="4" required><?php echo $rowView['notice']; ?></textarea>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-lg-2">

                                <label>From :</label>

                                <div class="form-group">

                                    <?php $startingDate = date('Y-m-d', strtotime($rowView['startingDate'])); ?>

                                    <input type="date" class="form-control" name="startingDate" value="<?php echo $startingDate; ?>" required>

                                </div>

                            </div>

                            <div class="col-lg-2">

                                <label>To :</label>

                                <div class="form-group">

                                    <?php $endingDate = date('Y-m-d', strtotime($rowView['endingDate'])); ?>

                                    <input type="date" class="form-control" name="endingDate" value="<?php echo $endingDate ?>" required>

                                </div>  

                            </div>

                            <div class="col-lg-2">

                                <div class="form-group">

                                    <label>Status</label>

                                    <select class="form-control" name="noticeStatus" required>

                                        <option value="">---</option>

                                        <?php if($rowView['noticeStatus'] == 0):?>

                                            <option value="1">Enabled</option>

                                            <option value="0" selected>Disabled</option>

                                        <?php endif; ?>

                                        <?php if($rowView['noticeStatus'] == 1): ?>

                                            <option value="1" selected>Enabled</option>

                                            <option value="0">Disabled</option>

                                        <?php endif; ?>

                                    </select>

                                </div>

                            </div>

                            <div class="col-lg-3" align="right">

                                <label>&nbsp;</label>

                                <div class="form-group">

                                        <input type="submit" class="btn btn-primary viewreport" name="updateNotice" value="Update Notice">

                                        <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">

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

                            Notice Table

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

                                        <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 60%;">Notice</th>

                                        <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">From</th>

                                        <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">To</th>

                                        <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Status</th>

                                        <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 10%;">Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php 

                                        $qryNotice = "SELECT * FROM noticeTbl";

                                        $resultNotice = mysqli_query($conn,$qryNotice);

                                        if($resultNotice->num_rows > 0){

                                            while($rowNotice = $resultNotice->fetch_assoc()){

                                                echo "<tr class='gradeA even' role='row'>";

                                                    echo "<td>".$rowNotice['notice']."</td>";

                                                    echo "<td>".$rowNotice['startingDate']."</td>";

                                                    echo "<td>".$rowNotice['endingDate']."</td>";

                                                    if($rowNotice['noticeStatus'] == 0){

                                                        echo "<td>Disabled</td>";

                                                    }elseif($rowNotice['noticeStatus'] == 1){

                                                        echo "<td>Enabled</td>";

                                                    }

                                                    /*echo "<td class='center' align='center'><a href='editNotice.php?edit=".$rowNotice['id']."'><i class='fa fa-edit' style='font-size:22px;'></i></a>&nbsp;&nbsp;<a href='deleteNotice.php?delete=".$rowNotice['id']."'><i class='fa fa-trash-o' style='font-size:25px; color:red;'></i></a></td>";*/

                                                    echo '<td class="center" align="center"><a href="editNotice.php?edit='.$rowNotice["id"].'"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a href="deleteNotice.php?delete='.$rowNotice["id"].'" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o"></i></a></td>';

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

