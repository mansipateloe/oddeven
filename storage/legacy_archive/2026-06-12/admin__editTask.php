<?php include 'header.php'; ?>



<?php 

    $id = $_GET['edit'];

    $qryView = "SELECT * FROM taskhoursTbl WHERE id=".$id;

    $resultView = mysqli_query($conn,$qryView);

    $rowView = mysqli_fetch_assoc($resultView);

?>  

        <div id="page-wrapper">

            <div class="row">

             <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading panel-box">
                        <h4>Edit Task</h4>
                    </div>
                    <div class="panel-body edit_task">

                     	<div class="dataTablesbox2">

                            <!-- <div class="col-lg-8"> -->

                                <form action="" role="form" method="POST" enctype="multipart/form-data">

                                    <div class="row">

                                        <div class="col-lg-6">

                                            <div class="form-group">

                                                <label>Project Name :</label>

                                                <select class="form-control" name="projectId" disabled>

                                                    <option value=""> Select Project</option>

                                                    <?php 

                                                        $qryProject = "select * from projectsTbl";

                                                        $resultProject = mysqli_query($conn,$qryProject);

                                                        if(mysqli_num_rows($resultProject) > 0){

                                                            while($rowProject = mysqli_fetch_assoc($resultProject)){

                                                                if($rowProject['id']==$rowView['projectId']){

                                                                    echo "<option selected value='".$rowProject['id']."'>".$rowProject['projectName']."</option>";

                                                                }else{

                                                                    echo "<option value='".$rowProject['id']."'>".$rowProject['projectName']."</option>";

                                                                }

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

                                                <label>Developer :</label>

                                                <select class="form-control" name="developerId" disabled>

                                                    <option value="">Select Developer</option>

                                                    <?php 

                                                        $qryDev = "SELECT * FROM employeesTbl WHERE id=".$rowView['developerId'];

                                                        $resultDev = mysqli_query($conn,$qryDev);

                                                        $rowDev = mysqli_fetch_assoc($resultDev);

                                                        if($rowDev){

                                                            echo "<option selected value='".$rowDev['id']."'>".$rowDev['name']."</option>";

                                                        }else{

                                                            echo "<option selected>--</option>";

                                                        }

                                                    ?>

                                                </select>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-lg-12">

                                            <div class="form-group">

                                                <label>Task Details</label>

                                                <textarea class="form-control" rows="6" name="task_details"><?php echo $rowView['worklog'] ?></textarea>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-lg-12">

                                            <div class="form-group">

                                                <br>

                                                <input type="submit" class="btn btn-primary viewreport" name="updateTask" value="Update Task">

                                                <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">

                                            </div>

                                        </div>

                                        <!-- <div class="col-lg-3">

                                            <div class="form-group">

                                                <br>

                                                <input class="btn btn-danger" type="reset" value="Cancel">

                                            </div>

                                        </div> -->

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

            </div>

            <!-- <div class="col-lg-12">

                <div class="addtaskbox">

                    <div class="headding">

                        <div class="datebox">Wensday, 15th Aug</div>

                        <div class="totalhours">00:00:00</div>

                    </div>

                    <div class="content">

                        <div class="whatsapp">

                            <input class="" maxlength="500" name="desc" placeholder="What's up?" type="text">

                        </div>

                        <div class="tracker-project">

                        <a class="icon-plus tracker-project-no-name">

                            <span>

                              Project

                            </span>

                        </a>

                        </div>

                        <div class="tracker-tag">

                            <span class="icon-tag"></span>

                        </div>

                          

                        <div class="tracker-container-right">

                          <div class="tracker-dollar-container" tabindex="0"> <a class="icon-dollar"> </a> </div>

                          <div class="tracker-time-entry-container">

                            <single-date-picker>

                              <div class="tracker-datePicker">

                                <div class="tracker-datePicker-end timepicker">

                                  <input class="timepicker-hour" tabindex="0" value="02:20">

                                </div>

                                - 

                                <div class="tracker-datePicker-end timepicker">

                                  <input class="timepicker-hour" value="02:20">

                                </div>

                                <span class="icon-calendar" tabindex="0"> </span> 

                              </div>

                            </single-date-picker>

                            <button class="tracker-button-cool" type="button"> add </button>

                          </div>

                          <div class="tracker-time-entry-switch"> 

                            <button class="icon-automatic" title="Timer Mode"> </button>

                            <button class="icon-manual icon-manual-hover" title="Manual Mode"> </button>

                          </div>

                        </div>



                    </div>

                </div>

            </div> -->

            

            <!-- <div class="col-lg-12">

                <div class="addtaskbox">

                    <div class="headding">

                        <div class="datebox">Wensday, 15th Aug</div>

                        <div class="totalhours">00:00:00</div>

                    </div>

                    <div class="content">

                        <div class="whatsapp">

                            <input class="" maxlength="500" name="desc" placeholder="What's up?" type="text">

                        </div>

                        <div class="tracker-project">

                        <a class="icon-plus tracker-project-no-name">

                            <span>

                              Project

                            </span>

                        </a>

                        </div>

                        <div class="tracker-tag">

                            <span class="icon-tag"></span>

                        </div>

                          

                        <div class="tracker-container-right">

                          <div class="tracker-dollar-container" tabindex="0"> <a class="icon-dollar"> </a> </div>

                          <div class="tracker-time-entry-container">

                            <single-date-picker>

                              <div class="tracker-datePicker">

                                <div class="tracker-datePicker-end timepicker">

                                  <input class="timepicker-hour" tabindex="0" value="02:20">

                                </div>

                                - 

                                <div class="tracker-datePicker-end timepicker">

                                  <input class="timepicker-hour" value="02:20">

                                </div>

                                <span class="icon-calendar" tabindex="0"> </span> 

                              </div>

                            </single-date-picker>

                            <button class="tracker-button-cool" type="button"> add </button>

                          </div>

                          <div class="tracker-time-entry-switch"> 

                            <button class="icon-automatic" title="Timer Mode"> </button>

                            <button class="icon-manual icon-manual-hover" title="Manual Mode"> </button>

                          </div>

                        </div>



                    </div>

                </div>

            </div> -->

            <!-- <div class="row">

                <div class="col-lg-12">

                    <div class="col-lg-12">

                        <div class="panel panel-default">

                            <div class="panel-heading">

                                Task Worktable

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

                                    <div class="row">

                                    <div class="col-sm-12">

                                    <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">

                                    <thead>

                                        <tr role="row">

                                            <th class="" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="descending" aria-label="Rendering engine: activate to sort column descending" style="width: 70px;">Date</th>

                                            <th class="" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="descending" aria-label="Rendering engine: activate to sort column descending" style="width: 60px;">Start Time</th>

                                            <th class="" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column descending" style="width: 60px;">End Time</th>

                                            <th class="" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column descending" style="width: 60px;">Total Time</th>

                                            <th class="" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column descending" style="width: 300px;">Worklog Details</th>

                                            <th class="" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column descending" style="width: 300px;">Admin Response</th>

                                        </tr>

                                    </thead>

                                    <tbody>



                                        <?php 

                                            $taskId = $_GET['edit'];

                                            $developerId = $rowView['developerId'];

                                            $qry = "select * from taskhoursTbl where taskId='$taskId' and developerId='$developerId' order by id desc";

                                            $result = mysqli_query($conn,$qry);

                                            if($result->num_rows > 0){

                                                while($row = $result->fetch_assoc()){

                                                echo "<tr class='gradeA even' role='row'>";

                                                    /*echo "<td>".$row['id']."</td>";*/

                                                    echo "<td>".$row['startTaskDate']."</td>";

                                                    echo "<td>".$row['startTaskTime']."</td>";

                                                    echo "<td>".$row['endTaskTime']."</td>";

                                                    echo "<td>".$row['totalTime']."</td>";

                                                    echo "<td>".$row['worklog']."</td>";

                                                    if(empty($row['adminResponse'])){

                                                        echo "<td>";

                                                        echo "<form method='POST'>";

                                                        echo "<input type='hidden' name='rowid' value='".$row['id']."' >";

                                                            echo "<textarea class='form-control' name='adminResponse' rows='4' cols='50' required>";

                                                            echo "</textarea>";

                                                            echo "<hr>";

                                                                echo "<select class='form-control' name='status'>";

                                                                if($rowView['status'] == "pending"){

                                                                    echo"<option value='pending' selected>Pending</option>";

                                                                    echo"<option value='inprogress'>In Progress</option>";

                                                                    echo"<option value='completed'>Completed</option>";

                                                                }

                                                                if($rowView['status'] == "completed"){

                                                                    echo"<option value='completed' selected>Completed</option>";

                                                                    echo"<option value='inprogress'>In Progress</option>";

                                                                    echo"<option value='pending'>Pending</option>";

                                                                }

                                                                if($rowView['status'] == "inprogress"){

                                                                    echo"<option value='inprogress' selected>In Progress</option>";

                                                                    echo"<option value='pending'>Pending</option>";

                                                                    echo"<option value='completed'>Completed</option>";

                                                                }

                                                                echo"</select>";

                                                                echo "&nbsp;&nbsp;";

                                                                echo "<input type='submit' class='btn btn-primary' name='updateTaskHoursTbl' value='Add Response'>";

                                                        echo "</form>";

                                                        echo "</td>";

                                                    }else{

                                                        echo "<td>".$row['adminResponse']."</td>";

                                                    }

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

                        </div>

                    </div>

                </div>

            </div> -->

        </div>

    </div>

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

            responsive: true

        });

    });

    /*$(document).ready(function() {

        $('#example').DataTable( {

            "paging":   false,

            "ordering": true,

            "info":     false

        });

    });*/

    /*var table = $('#example').DataTable( {

        keys: true

    } );

     

    table.keys.disable();*/

    </script>

<?php include 'footer.php'; ?>