<?php 
    // ini_set('memory_limit', '-1');
    include 'dbconnect.php';
    $projectId = $_GET['projectId'];
    $developerId = $_GET['developerId'];
    $startdate = $_GET['startdate'];
    $enddate = $_GET['enddate'];
    $tabledata = [];
    $i = 0;
    
    if($projectId=="" && $developerId=="" && $startdate=="" && $enddate==""){
        $qry = "SELECT * FROM taskhoursTbl";
        $result = mysqli_query($conn,$qry);
        // $data = mysqli_fetch_all($result,MYSQLI_ASSOC);
        // foreach ($data as $datarow) {
        while($datarow = $result->fetch_assoc()) {
            
            $tabledata[$i]['date'] = $datarow['startTaskDate'];
            $tabledata[$i]['totalHours'] = "-";
            $timeTotalData = [];
            
            if($datarow['projectId']){
                $qryProject = "SELECT * FROM projectsTbl WHERE id=".$datarow['projectId'];
                $resultProject = mysqli_query($conn,$qryProject);
                $rowProject = $resultProject->fetch_assoc();
                if($rowProject){
                    $tabledata[$i]['projectName'] = $rowProject['projectName'];
                }else{
                    $tabledata[$i]['projectName'] = '';    
                }
            }elseif($datarow['taskId']){
                $qryTask = "SELECT * FROM taskTbl WHERE id=".$datarow['taskId'];
                $resultTask = mysqli_query($conn,$qryTask);
                $rowTask = $resultTask->fetch_assoc();
                if($rowTask){
                    $qryProject = "SELECT * FROM projectsTbl WHERE id=".$rowTask['projectId'];
                    $resultProject = mysqli_query($conn,$qryProject);
                    $rowProject = $resultProject->fetch_assoc();
                    if($rowProject){
                        $tabledata[$i]['projectName'] = $rowProject['projectName'];
                    }else{
                        $tabledata[$i]['projectName'] = '';    
                    }
                }else{
                    $tabledata[$i]['projectName'] = '';
                }
            }else{
                $tabledata[$i]['projectName'] = '';
                $getedTotalHours = "-";
                // $tabledata[$i]['totalHours'] = $getedTotalHours;
            }

            if($datarow['developerId']){
                $qryDeveloper = "SELECT * FROM employeesTbl WHERE id=".$datarow['developerId'];
                $resultDeveloper = mysqli_query($conn,$qryDeveloper);
                $rowDeveloper = $resultDeveloper->fetch_assoc();
                if($rowDeveloper){
                    $tabledata[$i]['developerName'] = $rowDeveloper['name'];
                }else{
                    $tabledata[$i]['developerName'] = '';    
                }
            }else{
                $tabledata[$i]['developerName'] = '';
            }

            $tabledata[$i]['hours'] = $datarow['totalTime'];

            $tabledata[$i]['dailyWorkHours'] = "-";

            if($datarow['totalTime']){
                $tabledata[$i]['status'] = 'Completed';
            }else{
                $tabledata[$i]['status'] = 'Pending';
            }
            $i++;
        }
    }
    echo json_encode($tabledata);
?>
