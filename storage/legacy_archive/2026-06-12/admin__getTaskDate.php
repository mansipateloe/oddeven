<?php 
    include 'dbconnect.php';
    $projectId = $_GET['projectId'];
    $developerId = $_GET['developerId'];
    $startdateId = $_GET['startdateId'];
    $enddateId = $_GET['enddateId'];
    $i = 1;
    $amounts = 0;
    if($projectId != '' && $developerId == ''){
        $dateReportQry = "select * from taskTbl where created_at BETWEEN '$startdateId' and '$enddateId' and projectId = $projectId ORDER BY created_at ASC";
        $getdeposit = mysqli_query($conn, $dateReportQry);
        $getdepositRes = mysqli_num_rows($getdeposit);
        if($getdepositRes == 0){
                echo '0';
        }else{
            echo ' <thead>
            <tr role="row">
            <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Date</th>
            <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Project Name</th>
            <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Developer</th>
            <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">hours</th>
            <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">Status</th>
            </tr>
        </thead>
        <tbody>';
            while($getReportRes = mysqli_fetch_assoc($getdeposit)){
                //print_r($getReportRes); die();
                echo "<tr class='gradeA even' role='row'>";
                    $workdate = $getReportRes['created_at'];
                    $endTime = date('d-m-Y', strtotime($workdate));
                    echo "<td>".$endTime."</td>";
                    $projectQry = "select * from projectsTbl where id=".$getReportRes['projectId'];
                    $resultproject = mysqli_query($conn,$projectQry);
                    $rowproject = $resultproject->fetch_assoc();
                // print_r($rowproject); die();
                    echo "<td>".$rowproject['projectName']."</td>";
                    $qryEmp = "SELECT * FROM employeesTbl where id=".$getReportRes['developerId'];
                    $resultEmp = mysqli_query($conn,$qryEmp);
                    $rowEmp = $resultEmp->fetch_assoc();
                    //print_r($rowEmp); die();
                    echo "<td>".$rowEmp['name']."</td>";
                    if(empty($getReportRes['totalHour'])){
                        echo "<td>" .$getReportRes['totalHour']."</td>";
                        $countHours[] = ":";
                    }else{
                        echo "<td>".date('H:i', strtotime($getReportRes['totalHour']))."</td>";
                        $countHours[] = $getReportRes['totalHour'];
                        echo "<td>"."Completed"."</td>";
                    }
                    // echo '<td class="center" align="center">
                    // <a href="editDeposit.php?edit='.$getReportRes["deposit_id"].'" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                    // <a href="deleteDeposit.php?delete='.$getReportRes["deposit_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a>
                    // </td>';
                echo "</tr>";
            }
            $seconds = 0;
            foreach ($countHours as $countHour){
                list( $g, $i) = explode( ':', $countHour );
                $seconds += $g * 3600;
                $seconds += $i * 60;
            }
            $hours    = floor( $seconds / 3600 );
            $seconds -= $hours * 3600;
            $minutes  = floor( $seconds / 60 );
            $getedTotalHours = "{$hours}:{$minutes}";
            echo "<tr><td colspan='3'></td>";
            echo "<td colspan='6'>Total Hours : ".$getedTotalHours."</td>";
            echo "</tr>";
        }  
    }

    else if($developerId){
        //echo '<pre>'; print_r("dsfd");die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
        $dateReportQry = "select * from taskTbl where created_at BETWEEN '$startdateId' and '$enddateId' and developerId = $developerId ORDER BY created_at ASC";
        $getdeposit = mysqli_query($conn, $dateReportQry);
        $getdepositRes = mysqli_num_rows($getdeposit);
        if($getdepositRes == 0){
                echo '0';
        }else{
            echo ' <thead>
            <tr role="row">
            <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Date</th>
            <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Project Name</th>
            <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Developer</th>
            <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">hours</th>
            <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">Status</th>
            <th tabindex="0" rowspan="1" colspan="1"  style="width: 110px;">Action</th>
            </tr>
        </thead>
        <tbody>';
            while($getReportRes = mysqli_fetch_assoc($getdeposit)){
                //print_r($getReportRes); die();
                echo "<tr class='gradeA even' role='row'>";
                    $workdate = $getReportRes['created_at'];
                    $endTime = date('d-m-Y', strtotime($workdate));
                    echo "<td>".$endTime."</td>";
                    $projectQry = "select * from projectsTbl where id=".$getReportRes['projectId'];
                    $resultproject = mysqli_query($conn,$projectQry);
                    $rowproject = $resultproject->fetch_assoc();
                // print_r($rowproject); die();
                    echo "<td>".$rowproject['projectName']."</td>";
                    $qryEmp = "SELECT * FROM employeesTbl where id=".$getReportRes['developerId'];
                    $resultEmp = mysqli_query($conn,$qryEmp);
                    $rowEmp = $resultEmp->fetch_assoc();
                    //print_r($rowEmp); die();
                    echo "<td>".$rowEmp['name']."</td>";
                    if(empty($getReportRes['totalHour'])){
                        echo "<td>" .$getReportRes['totalHour']."</td>";
                        $countHours[] = ":";
                    }else{
                        echo "<td>".date('H:i', strtotime($getReportRes['totalHour']))."</td>";
                        $countHours[] = $getReportRes['totalHour'];
                        echo "<td>"."Completed"."</td>";
                    }
                    // echo '<td class="center" align="center">
                    // <a href="editDeposit.php?edit='.$getReportRes["deposit_id"].'" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                    // <a href="deleteDeposit.php?delete='.$getReportRes["deposit_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a>
                    // </td>';
                echo "</tr>";
            }
            $seconds = 0;
            foreach ($countHours as $countHour){
                list( $g, $i) = explode( ':', $countHour );
                $seconds += $g * 3600;
                $seconds += $i * 60;
            }
            $hours    = floor( $seconds / 3600 );
            $seconds -= $hours * 3600;
            $minutes  = floor( $seconds / 60 );
            $getedTotalHours = "{$hours}:{$minutes}";
            echo "<tr><td colspan='3'></td>";
            echo "<td colspan='6'>Total Hours : ".$getedTotalHours."</td>";
            echo "</tr>";
        } 
    }
?>
