<?php include 'dbconnect.php'; ?>
<?php
// $details = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * from leads where lead_id=$id"));
// print_r( $_POST);

$employeeId = $_POST['employeeId'];


// ... collect other fields
if(isset( $_POST['workDate'])){
  
    $workDate = $_POST['workDate'];
    $signinTime = $_POST['signinTime'];
    $lunchinTime = $_POST['lunchinTime'];
    $lunchoutTime = $_POST['lunchoutTime'];
    $breakinTime = $_POST['breakinTime'];

    $breakoutTime = $_POST['breakoutTime'];
    $signoutTime = $_POST['signoutTime'];
    // $workHours = $_POST['workHours'];


    
   $row=$_POST;
   
   
   $resultBreakInTime = ($_POST['breakinTime']);
   $resultBreakOutTime = ($_POST['breakoutTime']);
   $arrlengthBreakInTime = count($resultBreakInTime);
   $arrlengthBreakOutTime = count($resultBreakOutTime);


   $newlunchintime = new DateTime($lunchinTime);
   $newlunchouttime = new DateTime($lunchoutTime);
   $totalLunchTime = $newlunchouttime->diff($newlunchintime)->format("%h:%i");


//    if(!isset($row['breakinTime']) && !isset($row['breakoutTime']))
//    {
//            $row['breakinTime'] = '["00:00"]';
//            $row['breakoutTime'] = '["00:00"]';
//     }
    // print_r($row);echo "<br>";
       $breakinTimes = ($row['breakinTime']);
       $breakoutTimes = ($row['breakoutTime']);
       $countBreakoutTimes = count($breakoutTimes);
       for($i=0; $i < $countBreakoutTimes; $i++){
           $breakinTime = $breakinTimes[$i];
           $breakoutTime = $breakoutTimes[$i];
           $newbreakinTime = new DateTime($breakinTime);
           $newbreakoutTime = new DateTime($breakoutTime);
           $totalBreakTime = $newbreakoutTime->diff($newbreakinTime)->format("%h:%i");
           $arrayBreakTimes[] = $totalBreakTime;
       }
       $seconds = 0;


       foreach ( $arrayBreakTimes as $arrayBreakTime )
       {
           list( $g, $i) = explode( ':', $arrayBreakTime );
           $seconds += $g * 3600;
           $seconds += $i * 60;
       }
   
       $hours    = floor( $seconds / 3600 );
       $seconds -= $hours * 3600;
       $minutes  = floor( $seconds / 60 );
       $getedTotalHours = "{$hours}:{$minutes}";
       $newsigninTime = new DateTime($signinTime);
       $newsignoutTime = new DateTime($signoutTime);
       $totalSignTime = $newsignoutTime->diff($newsigninTime)->format("%h:%i");
       $timeLunch = strtotime($totalLunchTime);
       $timeBreak = strtotime($getedTotalHours);
       $timeSignin = strtotime($totalSignTime);
       $workHoursdata1 = ($timeSignin - $timeLunch)/3600;
       $newworkHoursdata1 = floor($workHoursdata1) . ':' . ( ($workHoursdata1-floor($workHoursdata1)) * 60 );
       $timeWorkData1 = strtotime($newworkHoursdata1);
       $workHoursdata2 = ($timeWorkData1 - $timeBreak)/3600;
       $finalWorkHours = floor($workHoursdata2) . ':' . ( ($workHoursdata2-floor($workHoursdata2)) * 60 );



    $id = $_POST['id'];
    
//
// f (isset($_POST['ReportSave'])) {
//     $workDate = $_POST['workDate'];
//     $signinTime = $_POST['signinTime'];
//     $lunchinTime = $_POST['lunchinTime'];
//     $lunchoutTime = $_POST['lunchoutTime'];
//     $breakinTime = $_POST['breakinTime'];
//     $breakoutTime = $_POST['breakoutTime'];
//     $signoutTime = $_POST['signoutTime'];
//     $workHours = $_POST['workHours'];
   
//

// Calculate workHours

$breakinTime=json_encode($_POST['breakinTime']);
$breakoutTime=json_encode($_POST['breakoutTime']);
$workHours=$finalWorkHours;

// Construct the update query
$updateQuery =  "UPDATE workhoursTbl SET ";
$updateQuery .= "workDate = '$workDate', ";
$updateQuery .= "signinTime = '$signinTime',";
$updateQuery .= "lunchinTime = '$lunchinTime', ";
$updateQuery .= "lunchoutTime = '$lunchoutTime',";
$updateQuery .= "breakinTime = '$breakinTime',";

$updateQuery .= "breakoutTime = '$breakoutTime', ";
$updateQuery .= "signoutTime = '$signoutTime', ";
$updateQuery .= "workHours = '$workHours'";
// $updateQuery .= "workHours = ";
// $updateQuery .= "TIMEDIFF('$signoutTime', '$signinTime') - ";
// $updateQuery .= "IFNULL(TIMEDIFF('$lunchoutTime', '$lunchinTime'), '00:00') - ";
// $updateQuery .= "(SELECT IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(breakoutTime, breakinTime)))), '00:00') FROM (SELECT * FROM workHoursTbl WHERE id = $id) AS subquery)";
// ... add other fields
$updateQuery .= " WHERE id = $id";
// Execute the update query
// print_r($updateQuery);
    if (mysqli_query($conn, $updateQuery)) {
        echo  "Update successful!";
        // header('Location:viewReport.php');
                header("Location: viewReport.php?employeeId=$employeeId");

        exit(); 
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>


