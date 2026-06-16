<?php include 'header.php'; ?>
<?php 
 session_start();
    // $projectId = $_GET['view'];
    // $qryProject = "select * from projectsTbl where id=".$projectId;
    // $resultProject = mysqli_query($conn,$qryProject);
    // $rowProject = $resultProject->fetch_assoc();
    //echo '<pre>'; print_r($rowProject);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
?>
        <div id="page-wrapper">
            <div class="row">
                </br>
                <div class="col-lg-12">
                	<div class="dataTablesbox2">
                    <!-- <div class="col-lg-8"> -->
                        <form action="" role="form" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Project Name :</label>
                                        <!-- <input class="form-control" value="<?php //echo $noticeTitle; ?>" type="text" name="noticeTitle"> -->
                                        <select class="form-control" name="projectId">
                                            <option value="">Select Project</option>
                                            <?php 
                                                $qryProject = "select * from projectsTbl";
                                                $resultProject = mysqli_query($conn,$qryProject);
                                                while($rowProject = $resultProject->fetch_assoc()){
                                                    echo "<option value='".$rowProject['id']."'>".$rowProject['projectName']."</option>";
                                                }
                                                //echo '<pre>'; print_r($rowProject);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
                                                //$rowProject = $resultProject->fetch_assoc();
                                                //echo "<option value='".$rowProject['id']."' selected>".$rowProject['projectName']."</option>";
                                            ?>
                                            <!-- <input type="hidden" name="projectId" value="<?php// echo $projectId ?>"> -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Select Developer :</label>
                                        <?php 
                                            $empDisplay = "select * from employeesTbl where employeeUname=".$_SESSION["employeeId"];
                                        ?>
                                        <!-- <select multiple size="5" name="developers[]" class="form-control">
                                        <?php 
                                            // $qryEmployee = "SELECT * FROM employeesTbl";
                                            // $resultEmployee = mysqli_query($conn,$qryEmployee);
                                            // if($resultEmployee->num_rows > 0){
                                            //     while($employee = $resultEmployee->fetch_assoc()){
                                            //         echo "<option value='".$employee['id']."'>".$employee['name']."</option>";
                                            //     }
                                            // }
                                         ?>
                                        </select>  -->
                                    </select>
                                        <select class="form-control" name="developerId">
                                            <option value="">Select Developer</option>
                                            <?php 
                                                // $developersId = json_decode($rowProject['developerId']);
                                                // foreach ($developersId as $developer){
                                                //     $qryDeveloper = "select * from employeesTbl where id=".$developer;
                                                //     $resultDeveloper = mysqli_query($conn,$qryDeveloper);
                                                //     if(mysqli_num_rows($resultDeveloper) > 0){
                                                //         while($rowDeveloper = mysqli_fetch_assoc($resultDeveloper)){
                                                //             echo "<option value='".$rowDeveloper['id']."'>".$rowDeveloper['name']."</option>";
                                                //         }
                                                //     }
                                                // }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Task Details :</label>
                                        <textarea class="form-control" rows="6" name="task_details"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                    <label>Date and Time :</label>
                                    <div id="time"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <br>
                                        <input type="submit" class="btn btn-primary" id="addemployeeBtn" name="assignTask" value="Start">
                                        <input class="btn btn-primary" type="reset" value="Cancel">
                                    </div>
                                </div>    
                            </div>
                        </form>
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

    <script>
        document.getElementById("time").innerHTML = formatAMPM();

            function formatAMPM() {
            var d = new Date(),
                minutes = d.getMinutes().toString().length == 1 ? '0'+d.getMinutes() : d.getMinutes(),
                hours = d.getHours().toString().length == 1 ? '0'+d.getHours() : d.getHours(),
                ampm = d.getHours() >= 12 ? 'pm' : 'am',
                months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            return days[d.getDay()]+' '+months[d.getMonth()]+' '+d.getDate()+' '+d.getFullYear()+' '+hours+':'+minutes;
}
    </script>
<?php include 'footer.php'; ?>
