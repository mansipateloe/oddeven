<?php include 'header.php'; ?>
<?php 
		if(isset($_GET['edit']))
		{
			$id = $_GET['edit'];
			$update = true;
			$getReportQry = "SELECT * from workhoursTbl where id = $id";
			$getReportResult = mysqli_query($conn, $getReportQry);
            $getReportRes = mysqli_fetch_assoc($getReportResult);
            //echo '<pre>'; print_r($getReportRes);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
            $Report_name = $getReportRes['Report_name'];
            $balance = $getReportRes['balance'];
			$id = $getReportRes['id'];
		}

	 ?>	
<style type="text/css">
    td{
        text-align: left;
        font-weight: bold;
    }
    .red{
        background-color: #F08080 !important;
    }
    .warningicon {
        background: transparent; cursor: pointer;
    }
    .warningicon:hover {
        background: transparent;
    }
    .leave{
        background-color: #7E8A9E !important;
    }
</style>
    <div id="page-wrapper">
        <div class="view_report">
        <div class="row">
            <div class="col-lg-12">
            	<div class="dataTablesbox">
                <form role="form" method="POST">
                    <div class="form-group col-lg-3">
                        <label>Select Employee Name</label>
                        <select class="form-control" name="employeeId" required>
                            <option value="">Employee Name</option>
                            <?php 
                                $qryEmpName = "SELECT * FROM `employeesTbl` where status=0 ORDER BY `employeesTbl`.`id` ASC";
                                $resultEmpName = mysqli_query($conn,$qryEmpName);
                                if($resultEmpName->num_rows > 0){
                                    while($rowEmpName = $resultEmpName->fetch_assoc()){
                                        if($_POST['employeeId'] == $rowEmpName['id']){
                                            echo "<option value='".$rowEmpName['id']."' selected>".$rowEmpName['name']."</option>";    
                                        }else{
                                            echo "<option value='".$rowEmpName['id']."'>".$rowEmpName['name']."</option>";    
                                        }
                                    }
                                }
                             ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-2">
                        <label>Select Year</label>
                            <select class="form-control year" name="year" required>
                                <option value="">Year</option>
                                <?php 
                                    $yearsArray = array(
                                                        "2018" => "2018",
                                                        "2019" => "2019",
                                                        "2020" => "2020",
                                                        "2021" => "2021",
                                                        "2022" => "2022",
                                                        "2023" => "2023",
                                                        "2024" => "2024",
                                                        "2025" => "2025",
                                                        "2026" => "2026",
                                                        "2027" => "2027",
                                                        "2028" => "2028",
                                                        "2029" => "2029",
                                                        "2030" => "2030",
                                                    );
                                    foreach ($yearsArray as $key => $value) {
                                        if($_POST['year'] == $key){
                                            echo "<option value=".$key." selected>".$value."</option>";    
                                        }else{
                                            echo "<option value=".$key.">".$value."</option>";    
                                        }
                                    }
                                 ?>
                            </select>
                    </div>
                    <div class="form-group col-lg-2">
                        <label>Select Month</label>
                            <select class="form-control month" name="month" required>
                                <option value="">Month</option>
                                <?php 
                                    $monthsArray = array(
                                                        "1" => "January",
                                                        "2" => "February",
                                                        "3" => "March",
                                                        "4" => "April",
                                                        "5" => "May",
                                                        "6" => "June",
                                                        "7" => "July",
                                                        "8" => "August",
                                                        "9" => "September",
                                                        "10" => "October",
                                                        "11" => "November",
                                                        "12" => "December",
                                                    );
                                    foreach ($monthsArray as $key => $value) {
                                        if($_POST['month'] == $key){
                                            echo "<option value=".$key." selected>".$value."</option>";    
                                        }else{
                                            echo "<option value=".$key.">".$value."</option>";    
                                        }
                                    }
                                 ?>
                                <!-- <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option> -->
                            </select>
                    </div>
                    <div class="form-group col-lg-2">
                        <label>&nbsp;</label>
                        <input class="form-control btn btn-primary viewreport" type="submit" name="viewreport" value="View Report">
                    </div>
                </form>
                </div>
            </div>
        </div>
        </br>
        

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php 
                            $avgHours = "";
                            $employeeName = "";
                            if(isset($_POST['viewreport'])){
                                $employeeId = $_POST['employeeId'];
                                $nameQry = "select * from employeesTbl where id=$employeeId";
                                $resultnameQry = mysqli_query($conn,$nameQry);
                                if($resultnameQry->num_rows > 0){
                                    while($rownameQry = $resultnameQry->fetch_assoc()){
                                        $employeeName = $rownameQry['name'];
                                    }
                                }
                                $month = $_POST['month'];
                                $year = $_POST['year'];
                                $qry = "select * from workhoursTbl where employeeId=$employeeId and month=$month and year=$year and workHours!=''";

                                $result = mysqli_query($conn,$qry);
                                if($result->num_rows > 0){
                                    while($row = $result->fetch_assoc()){
                                        $workHoursArray[] = $row['workHours'];
                                    }
                                    $timestamps = array_map(function($item){
                                        list($h, $m) = explode(':', $item);
                                        return intval($h) * 3600 + intval($m) * 60;
                                    }, $workHoursArray);
                                    $avg = ceil(array_sum($timestamps)/count($timestamps));
                                    $avgHours = gmdate('H:i', $avg);
                                }
                            }
                         ?>
                        Employees Table : <?php echo $employeeName; ?> || Average Hours : <?php echo $avgHours; ?>
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
                                    <div class="table-responsive">
                                        <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                                            <thead>
                                                <tr role="row">
                                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Date</th>
                                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Sign In</th>
                                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Lunch In</th>
                                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">Lunch Out</th>
                                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 148px;">Break IN</th>
                                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">Break Out</th>
                                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 120px;">Sign Out</th>
                                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">Work Hours</th>
                                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">Action</th>
                                               
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php   
                                                    if(isset($_POST['viewreport'])){
                                                        $employeeId = $_POST['employeeId'];
                                                        $month = $_POST['month'];
                                                        $year = $_POST['year'];
                                                        $monthTotaldays = cal_days_in_month(CAL_GREGORIAN,$month,$year);
                                                        for ($day=1; $day <= $monthTotaldays; $day++) { 
                                                            $getDataDateConvert = $day."-".$month."-".$year;
                                                            $getDataDate = date('d-m-Y', strtotime($getDataDateConvert)); 
                                                            $getDataQry = "select * from workhoursTbl where employeeId=$employeeId and workDate='$getDataDate'";
                                                            $getDataResult = mysqli_query($conn,$getDataQry);
                                                            if($getDataResult->num_rows > 0){
                                                                while($getDataRow = $getDataResult->fetch_assoc()){
                                                                    if(strtotime($getDataRow['workHours']) >= strtotime('8:30')){
                                                                        echo "<tr class='gradeA even' role='row'>";
                                                                    }else{
                                                                        echo "<tr class='gradeA even red' role='row'>";
                                                                    }

                                                                    $qryLateWorkhours = "select * from lateWorkhoursTbl where employeeId=$employeeId and month=$month and year=$year and workDate='".$getDataRow['workDate']."'";
                                                                    $resultLateWorkhours = mysqli_query($conn,$qryLateWorkhours);
                                                                    if($resultLateWorkhours->num_rows > 0){
                                                                        $rowLateWorkhours = $resultLateWorkhours->fetch_assoc();
                                                                        echo "<td class='sorting_1 tooltip-demo'>".$getDataRow['workDate']." <p class='fa fa-warning btn-danger warningicon' data-toggle='tooltip' data-placement='right' title='".$rowLateWorkhours['reason']."'></p></td>";
                                                                    }else{
                                                                        echo "<td class='sorting_1'>".$getDataRow['workDate']."</td>";
                                                                    }
                                                                
                                                                    if(empty($getDataRow['signinTime'])){
                                                                        echo "<td>".$getDataRow['signinTime']."</td>";    
                                                                    }else{
                                                                        echo "<td>".date('H:i', strtotime($getDataRow['signinTime']))."</td>";
                                                                    }

                                                                    if(empty($getDataRow['lunchinTime'])){
                                                                        echo "<td>".$getDataRow['lunchinTime']."</td>";    
                                                                    }else{
                                                                        echo "<td>".date('H:i', strtotime($getDataRow['lunchinTime']))."</td>";
                                                                    }

                                                                    if(empty($getDataRow['lunchoutTime'])){
                                                                        echo "<td>".$getDataRow['lunchoutTime']."</td>";    
                                                                    }else{
                                                                        echo "<td>".date('H:i', strtotime($getDataRow['lunchoutTime']))."</td>";
                                                                    }

                                                                    if(empty($getDataRow['breakinTime'])){
                                                                        echo "<td>".$getDataRow['breakinTime']."</td>";    
                                                                    }else{
                                                                        echo "<td>";
                                                                            $breakinTimeValues = json_decode($getDataRow['breakinTime']);
                                                                            if(is_array($breakinTimeValues)){
                                                                                foreach($breakinTimeValues as $breakIns){
                                                                                    echo date('H:i', strtotime($breakIns))."<br>";    
                                                                                }
                                                                            }else{
                                                                                echo date('H:i', strtotime($getDataRow['breakinTime']));
                                                                            }
                                                                        echo "</td>";
                                                                    }

                                                                    if(empty($getDataRow['breakoutTime'])){
                                                                        echo "<td>".$getDataRow['breakoutTime']."</td>";    
                                                                    }else{
                                                                        echo "<td>";
                                                                            $breakoutTimeValues = json_decode($getDataRow['breakoutTime']);
                                                                            if(is_array($breakoutTimeValues)){
                                                                                foreach($breakoutTimeValues as $breakOuts){
                                                                                    echo date('H:i', strtotime($breakOuts))."<br>";    
                                                                                }
                                                                            }else{
                                                                                echo date('H:i', strtotime($getDataRow['breakoutTime']));
                                                                            }
                                                                        echo "</td>";
                                                                    }

                                                                    if(empty($getDataRow['signoutTime'])){
                                                                        echo "<td>".$getDataRow['signoutTime']."</td>";    
                                                                    }else{
                                                                        echo "<td>".date('H:i', strtotime($getDataRow['signoutTime']))."</td>";
                                                                    }

                                                                    if(empty($getDataRow['workHours'])){
                                                                        echo "<td>".$getDataRow['workHours']."</td>";   
                                                                        $countHours[] = ":";
                                                                        
                                                                    }else{
                                                                        echo "<td>".date('H:i', strtotime($getDataRow['workHours']))."</td>";
                                                                        $countHours[] = $getDataRow['workHours'];
                                                                    }
                                                                    echo '<td class="center" align="center">

                                                                    <a href="edit_report.php?edit='.$getDataRow["id"].'" onclick="openModal();" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                                                                  
                                                                    </td>';
                                                                 echo "</tr>";
                                                                // echo '<td class="center" align="center">
                                                                //     <a href="#" onclick="openModal('.$getDataRow["id"].');" style="display: inline-block; width: 28px;">
                                                                //         <i class="fa fa-pencil"></i>
                                                                //     </a>&nbsp;&nbsp;
                                                                // </td>';

                                                                 $i++;
                                                                    // echo "</tr>"; 
                                                                }
                                                            }
                                                            else{
                                                                $dayofweek = date('l', strtotime($getDataDate));
                                                                if($dayofweek == "Saturday" || $dayofweek == "Sunday" ){
                                                                    echo "<tr class='gradeA even' role='row'>";
                                                                    echo "<td>".$getDataDate."</td>"; 
                                                                    echo "<td>".$dayofweek."</td><td></td><td></td><td></td><td></td><td></td><td></td>";
                                                                    echo "</tr>";    
                                                                }else{
                                                                    $checkDate = date('Y-m-d', strtotime($getDataDate));
                                                                    $qryCheckHoliday = "select * from holidayTbl where holidayDate='".$checkDate."'";

                                                                    $resultCheckHoliday = mysqli_query($conn,$qryCheckHoliday);
                                                                    if($resultCheckHoliday->num_rows > 0){
                                                                        $rowCheckHoliday = $resultCheckHoliday->fetch_assoc();
                                                                        echo "<tr class='gradeA even' role='row'>";
                                                                        echo "<td>".$getDataDate."</td>"; 
                                                                        echo "<td>".$rowCheckHoliday['holidayTitle']."</td><td></td><td></td><td></td><td></td><td></td><td></td>";
                                                                        echo "</tr>";
                                                                    }else{
                                                                        echo "<tr class='gradeA even leave' role='row'>";
                                                                        echo "<td>".$getDataDate."</td>"; 
                                                                        echo "<td>! No Data found</td><td></td><td></td><td></td><td></td><td></td><td></td>";
                                                                        echo "</tr>";
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        $seconds = 0;
                                                        foreach ( $countHours as $countHour )
                                                        {
                                                            list( $g, $i) = explode( ':', $countHour );
                                                            $seconds += $g * 3600;
                                                            $seconds += $i * 60;
                                                        }
                                                        $hours    = floor( $seconds / 3600 );
                                                        $seconds -= $hours * 3600;
                                                        $minutes  = floor( $seconds / 60 );
                                                        $getedTotalHours = "{$hours}:{$minutes}";
                                                        echo "<tr><td colspan='6'></td>";
                                                        echo "<td colspan='6'>Total Hours : ".$getedTotalHours."</td>";
                                                        echo "</tr>";
                                                    }else{
                                                        echo "<tr class='gradeA even' role='row'>";
                                                        echo "<td>! No Data found</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                                                        echo "</tr>";
                                                    }                                       
                                                ?>                              
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- ADD MODAL -->
                                        <div class="modal fade" id="addReportmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <form role="form" method="POST">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        <h4 class="modal-title" id="myModalLabel">Edit Report</h4>
                                                    </div>
                                                    <form action="" method="POST">
                                                        <div class="modal-body">
                                                          

                                                            <!-- Next Followup Date -->
                                                            <div class="mb-3">
                                                                <label for="workDate" class="form-label">workDate</label>
                                                                <input type="workDate" class="form-control" id="workDate" name="workDate" required>
                                                            </div>

                                                            <!-- Next Followup Time -->
                                                            <div class="mb-3">
                                                                <label for="signinTime" class="form-label">Sign In</label>
                                                                <input type="time" class="form-control" id="signinTime" name="signinTime" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="lunchinTime" class="form-label">Lunch In</label>
                                                                <input type="time" class="form-control" id="lunchinTime" name="lunchinTime" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="lunchoutTime" class="form-label">Lunch Out</label>
                                                                <input type="time" class="form-control" id="lunchoutTime" name="lunchoutTime" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="breakinTime" class="form-label">Break In</label>
                                                                <input type="time" class="form-control" id="breakinTime" name="breakinTime" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="breakoutTime" class="form-label">Break Out</label>
                                                                <input type="time" class="form-control" id="breakoutTime" name="breakoutTime" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="signoutTime" class="form-label">Sign Out</label>
                                                                <input type="time" class="form-control" id="signoutTime" name="signoutTime" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="workHours" class="form-label">Work Hours</label>
                                                                <input type="time" class="form-control" id="workHours" name="workHours" required>
                                                            </div>
                                                           

                                                            <!-- Add other form fields as needed -->
                                                            <input type="submit" value="Submit" name="saveLeadFollowup" class="btn btn-primary">

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                <!-- END.... -->
                            </div>
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
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Include Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            "responsive": false,
            "paging":   false,
            "ordering": false,
            "info":     false,
            "searching": false
        });
    });

    function openModal() {
        $('#addReportmodal').modal('show');

    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var yearValue = "<?php echo $_POST['year']?>";
        if(yearValue == ""){
            var d = new Date(),
            year = d.getFullYear();
            $('.year option[value='+year +']').attr('selected',true);
        }
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var monthValue = "<?php echo $_POST['month']?>";
        if(monthValue == ""){
            var v = new Date(),
            month = v.getMonth()+1;
            $('.month option[value='+month +']').attr('selected',true);
        }
    });
</script>
<script>
    $('.tooltip-demo').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
</script>

<?php include 'footer.php'; ?>
