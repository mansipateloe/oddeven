<?php include 'header.php'; ?>
<script src="../vendor/jquery/jquery.min.js"></script>
<style type="text/css">
    .leave {
        background-color: #E72C30 !important;
    }
</style>
<?php
$employeeId = $_SESSION['employeeId'];
date_default_timezone_set('Asia/Kolkata');
$getTodayDate = getdate(date("U"));
$monthTotaldays = cal_days_in_month(CAL_GREGORIAN, $getTodayDate['mon'], $getTodayDate['year']);
$currentDay = $getTodayDate['mday'];
$currentMonth = $getTodayDate['mon'];
$currentYear = $getTodayDate['year'];
$currentHour = $getTodayDate['hours'];
$currentMinute = $getTodayDate['minutes'];
//$currentDate = $currentDay."-".$currentMonth."-".$currentYear;
$convertCurrentDate = $currentDay . "-" . $currentMonth . "-" . $currentYear;
$currentDate = date('d-m-Y', strtotime($convertCurrentDate));
$currentTime = $currentHour . ":" . $currentMinute;
$remainingDays = $monthTotaldays - $currentDay;
$qryEmpView = "SELECT * FROM employeesTbl WHERE id=" . $employeeId;
$resultEmpView = mysqli_query($conn, $qryEmpView);
$rowEmpView = $resultEmpView->fetch_assoc();

// Handle form submissions
if (isset($_POST['signin'])) {
    if (strtotime($currentTime) > strtotime('10:30')) {
        echo "<script>$(function() { $('#myModal').modal(); });</script>";
    } else {
        $qry = "INSERT INTO workhoursTbl (employeeId, workDate, signinTime, month, year) VALUES ('$employeeId', '$currentDate', '$currentTime', '$currentMonth', '$currentYear')";
        if (mysqli_query($conn, $qry)) {
            echo "<script>alert('Sign in successful.');</script>";
        } else {
            echo "<script>alert('Sign in failed.');</script>";
        }
    }
}

if (isset($_POST['latesignIn'])) {
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $qry = "INSERT INTO lateWorkhoursTbl (employeeId, workDate, signinTime, month, year, reason) VALUES ('$employeeId', '$currentDate', '$currentTime', '$currentMonth', '$currentYear', '$reason')";
    if (mysqli_query($conn, $qry)) {
        $qrysignIn = "INSERT INTO workhoursTbl (employeeId, workDate, signinTime, month, year) VALUES ('$employeeId', '$currentDate', '$currentTime', '$currentMonth', '$currentYear')";
        if (mysqli_query($conn, $qrysignIn)) {
            echo "<script>alert('Late sign in successful.');</script>";
        } else {
            echo "<script>alert('Signin time and reason not inserted successfully.');</script>";
        }
    } else {
        echo "<script>alert('Reason not inserted successfully.');</script>";
    }
}

if (isset($_POST['lunchin'])) {
    $qryLunchin = "select * from workhoursTbl where employeeId=$employeeId and workDate='$currentDate'";
    $result = mysqli_query($conn, $qryLunchin);
    $rowLunchin = $result->fetch_assoc();
    if (empty($rowLunchin['signinTime'])) {
        echo "<script>alert('You need to sign in first.');</script>";
    } else {
        $qry = "update workhoursTbl set lunchinTime='$currentTime' where employeeId='$employeeId' and workDate='$currentDate'";
        if (mysqli_query($conn, $qry)) {
            echo "<script>alert('Lunch in successful.');</script>";
        } else {
            echo "<script>alert('Lunch in failed.');</script>";
        }
    }
}

if (isset($_POST['lunchout'])) {
    $qryLunch = "select * from workhoursTbl where employeeId=$employeeId and workDate='$currentDate'";
    $result = mysqli_query($conn, $qryLunch);
    $rowLunch = $result->fetch_assoc();
    if (empty($rowLunch['lunchinTime'])) {
        echo "<script>alert('You need to Lunch in first.');</script>";
    } else {
        $qry = "update workhoursTbl set lunchoutTime='$currentTime' where employeeId='$employeeId' and workDate='$currentDate'";
        if (mysqli_query($conn, $qry)) {
            echo "<script>alert('Lunch out successful.');</script>";
        } else {
            echo "<script>alert('Lunch out failed.');</script>";
        }
    }
}

if (isset($_POST['breakin'])) {
    $qryBreakIn = "select * from workhoursTbl where employeeId=$employeeId and workDate='$currentDate'";
    $result = mysqli_query($conn, $qryBreakIn);
    $rowBreakIn = $result->fetch_assoc();
    $arrayCurrentTime = array($currentTime);
    $currentTimeJson = json_encode($arrayCurrentTime);
    if (empty($rowBreakIn['signinTime'])) {
        echo "<script>alert('You need to sign in first.');</script>";
    } else {
        $qry = "update workhoursTbl set breakinTime='$currentTimeJson' where employeeId='$employeeId' and workDate='$currentDate'";
        if (mysqli_query($conn, $qry)) {
            // echo "<script>alert('Break in successful.');</script>";
        } else {
            // echo "<script>alert('Break in failed.');</script>";
        }
    }
}

if (isset($_POST['breakout'])) {
    $qryBreak = "select * from workhoursTbl where employeeId=$employeeId and workDate='$currentDate'";
    $result = mysqli_query($conn, $qryBreak);
    $rowBreak = $result->fetch_assoc();
    $arrayCurrentTime = array($currentTime);
    $currentTimeJson = json_encode($arrayCurrentTime);
    if (empty($rowBreak['breakinTime'])) {
        echo "<script>alert('You need to Break in first.');</script>";
    } else {
        $qry = "update workhoursTbl set breakoutTime='$currentTimeJson' where employeeId='$employeeId' and workDate='$currentDate'";
        if (mysqli_query($conn, $qry)) {
            // echo "<script>alert('Break out successful.');</script>";
        } else {
            // echo "<script>alert('Break out failed.');</script>";
        }
    }
}

if (isset($_POST['extraBreakin'])) {
    $qryBreak = "select * from workhoursTbl where employeeId=$employeeId and workDate='$currentDate'";
    $result = mysqli_query($conn, $qryBreak);
    $rowBreak = $result->fetch_assoc();
    $resultBreakInTime = json_decode($rowBreak['breakinTime']);
    $resultBreakoutTime = json_decode($rowBreak['breakoutTime']);
    $arrlengthBreakInTime = count($resultBreakInTime);
    $arrlengthBreakoutTime = count($resultBreakoutTime);
    if ($arrlengthBreakInTime == $arrlengthBreakoutTime) {
        array_push($resultBreakInTime, $currentTime);
        $extraBreakinTime = json_encode($resultBreakInTime);
        $qry = "update workhoursTbl set breakinTime='$extraBreakinTime' where employeeId='$employeeId' and workDate='$currentDate'";
        if (mysqli_query($conn, $qry)) {
            // echo "<script>alert('Extra break in successful.');</script>";
        } else {
            // echo "<script>alert('Extra break in failed.');</script>";
        }
    } else {
        echo "<script>alert('You need to breakout first.');</script>";
    }
}

if (isset($_POST['extraBreakout'])) {
    $qryBreak = "select * from workhoursTbl where employeeId=$employeeId and workDate='$currentDate'";
    $result = mysqli_query($conn, $qryBreak);
    $rowBreak = $result->fetch_assoc();
    $resultBreakInTime = json_decode($rowBreak['breakinTime']);
    $resultBreakOutTime = json_decode($rowBreak['breakoutTime']);
    $arrlengthBreakInTime = count($resultBreakInTime);
    $arrlengthBreakOutTime = count($resultBreakOutTime);
    if ($arrlengthBreakInTime == $arrlengthBreakOutTime) {
        echo "<script>alert('You need to breakin first');</script>";
    } else {
        array_push($resultBreakOutTime, $currentTime);
        $extraBreakOutTime = json_encode($resultBreakOutTime);
        $qry = "update workhoursTbl set breakoutTime='$extraBreakOutTime' where employeeId='$employeeId' and workDate='$currentDate'";
        if (mysqli_query($conn, $qry)) {
            // echo "<script>alert('Extra break out successful.');</script>";
        } else {
            // echo "<script>alert('Extra break out failed.');</script>";
        }
    }
}

// if(isset($_POST['logoutBtn'])) {
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (isset($_POST['logoutBtn'])) {


//         $qry = "select * from workhoursTbl where employeeId=$employeeId and workDate='$currentDate'";
//         $result = mysqli_query($conn, $qry);
//         $row = $result->fetch_assoc();
//         $signinTime = $row['signinTime'];
//         $lunchinTime = $row['lunchinTime'];
//         $lunchoutTime = $row['lunchoutTime'];
//         $resultBreakInTime = json_decode($row['breakinTime']);
//         $resultBreakOutTime = json_decode($row['breakoutTime']);
//         $arrlengthBreakInTime = count($resultBreakInTime);
//         $arrlengthBreakOutTime = count($resultBreakOutTime);

//         if (!empty($lunchinTime) && empty($lunchoutTime)) {
//             echo "<script>alert('You need to lunch out first.');</script>";
//         } elseif ($arrlengthBreakInTime != $arrlengthBreakOutTime) {
//             // echo "<script>alert('You need to breakout first.');</script>";
//         } elseif (empty($signinTime)) {
//             // echo "<script>alert('You need to sign in first.');</script>";
//         } else {
//             $newlunchintime = new DateTime($lunchinTime);
//             $newlunchouttime = new DateTime($lunchoutTime);
//             $totalLunchTime = $newlunchouttime->diff($newlunchintime)->format("%h:%i");

//             if (empty($row['breakinTime']) && empty($row['breakoutTime'])) {
//                 $row['breakinTime'] = '["00:00"]';
//                 $row['breakoutTime'] = '["00:00"]';
//             }
//             $breakinTimes = json_decode($row['breakinTime']);
//             $breakoutTimes = json_decode($row['breakoutTime']);
//             $countBreakoutTimes = count($breakoutTimes);
//             for ($i = 0; $i < $countBreakoutTimes; $i++) {
//                 $breakinTime = $breakinTimes[$i];
//                 $breakoutTime = $breakoutTimes[$i];
//                 $newbreakinTime = new DateTime($breakinTime);
//                 $newbreakoutTime = new DateTime($breakoutTime);
//                 $totalBreakTime = $newbreakoutTime->diff($newbreakinTime)->format("%h:%i");
//                 $arrayBreakTimes[] = $totalBreakTime;
//             }
//             $seconds = 0;

//             foreach ($arrayBreakTimes as $arrayBreakTime) {
//                 list($g, $i) = explode(':', $arrayBreakTime);
//                 $seconds += $g * 3600;
//                 $seconds += $i * 60;
//             }

//             $hours = floor($seconds / 3600);
//             $seconds -= $hours * 3600;
//             $minutes = floor($seconds / 60);
//             $getedTotalHours = "{$hours}:{$minutes}";
//             $newsigninTime = new DateTime($signinTime);
//             $newsignoutTime = new DateTime($currentTime);
//             $totalSignTime = $newsignoutTime->diff($newsigninTime)->format("%h:%i");
//             $timeLunch = strtotime($totalLunchTime);
//             $timeBreak = strtotime($getedTotalHours);
//             $timeSignin = strtotime($totalSignTime);
//             $workHoursdata1 = ($timeSignin - $timeLunch) / 3600;
//             $newworkHoursdata1 = floor($workHoursdata1) . ':' . (($workHoursdata1 - floor($workHoursdata1)) * 60);
//             $timeWorkData1 = strtotime($newworkHoursdata1);
//             $workHoursdata2 = ($timeWorkData1 - $timeBreak) / 3600;
//             $finalWorkHours = floor($workHoursdata2) . ':' . (($workHoursdata2 - floor($workHoursdata2)) * 60);
//             if (empty($row['lunchinTime'] || $row['lunchoutTime'])) {
//                 $newLunchin = "00:00";
//                 $newLunchout = "00:00";
//             }
//             $zeroValue = '["00:00"]';
//             $qry = "UPDATE workhoursTbl 
//             SET lunchinTime = CASE WHEN lunchinTime = '' THEN '00:00' ELSE lunchinTime END,
//             lunchoutTime = CASE WHEN lunchoutTime = '' THEN '00:00' ELSE lunchoutTime END,
//             breakinTime = CASE WHEN breakinTime = '' THEN '$zeroValue' ELSE breakinTime END,
//             breakoutTime = CASE WHEN breakoutTime = '' THEN '$zeroValue' ELSE breakoutTime END,
//             workHours='$finalWorkHours',
//             signoutTime='$currentTime'
//             WHERE employeeId='$employeeId' and workDate='$currentDate'";

//             if (mysqli_query($conn, $qry)) {
//                 // echo "<script>alert('Logout successful.');</script>";
//             } else {
//                 echo "<script>alert('Logout failed.');</script>";
//             }
//         }
//     }
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logoutBtn'])) {

        $qry = "SELECT * FROM workhoursTbl 
                WHERE employeeId='$employeeId' 
                AND workDate='$currentDate'";
        $result = mysqli_query($conn, $qry);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $signinTime  = $row['signinTime'];
            $lunchinTime = $row['lunchinTime'];
            $lunchoutTime = $row['lunchoutTime'];

            $resultBreakInTime  = json_decode($row['breakinTime']);
            $resultBreakOutTime = json_decode($row['breakoutTime']);

            $arrlengthBreakInTime  = count($resultBreakInTime ?? []);
            $arrlengthBreakOutTime = count($resultBreakOutTime ?? []);

            if (!empty($lunchinTime) && empty($lunchoutTime)) {
                echo "<script>alert('You need to lunch out first.');</script>";
            } elseif ($arrlengthBreakInTime != $arrlengthBreakOutTime) {
                echo "<script>alert('You need to breakout first.');</script>";
            } elseif (empty($signinTime)) {
                echo "<script>alert('You need to sign in first.');</script>";
            } else {
                // 👉 your work hours calculation (kept as is)

                // Fix condition
                if (empty($row['lunchinTime']) || empty($row['lunchoutTime'])) {
                    $newLunchin = "00:00";
                    $newLunchout = "00:00";
                }

                $zeroValue = '["00:00"]';
                $qryUpdate = "UPDATE workhoursTbl 
                    SET lunchinTime = CASE WHEN lunchinTime = '' THEN '00:00' ELSE lunchinTime END,
                        lunchoutTime = CASE WHEN lunchoutTime = '' THEN '00:00' ELSE lunchoutTime END,
                        breakinTime = CASE WHEN breakinTime = '' THEN '$zeroValue' ELSE breakinTime END,
                        breakoutTime = CASE WHEN breakoutTime = '' THEN '$zeroValue' ELSE breakoutTime END,
                        workHours='$finalWorkHours',
                        signoutTime='$currentTime'
                    WHERE employeeId='$employeeId' 
                    AND workDate='$currentDate'";

                if (mysqli_query($conn, $qryUpdate)) {
                    // ✅ Destroy session after successful logout
                    session_start();
                    session_unset();
                    session_destroy();

                    echo "<script>alert('Logout successful.'); window.location.href='home.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('Logout failed.');</script>";
                }
            }
        } else {
            echo "<script>alert('No workhours record found.');</script>";
        }
    }
}

?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form role="form" method="POST">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Why being late ? Reason</h4>
                </div>
                <div class="modal-body form-group">
                    <textarea class="form-control" rows="5" minlength="20" name="reason" required></textarea>
                    <span>*Minimum 20 characters required.</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" name="latesignIn" class="btn btn-primary" value="Sign in">
                </div>
            </div>
        </div>
    </form>
</div>
<div id="wrapper">
    <div id="page-wrapper">
        <section class="table-box">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading panel-box">
                            <h4>Attendance</h4>
                        </div>
                        <div class="panel-body attendance">
                            <div id="dataTables-example_wrapper"
                                class="dataTables_wrapper form-inline dt-bootstrap no-footer">
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
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-striped table-hover dataTable no-footer dtr-inline"
                                                    id="dataTables-example" role="grid"
                                                    aria-describedby="dataTables-example_info">
                                                    <thead>
                                                        <tr role="row" class="panel-heading">
                                                            <th class="" tabindex="0" aria-controls="dataTables-example"
                                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                                aria-label="Rendering engine: activate to sort column descending">
                                                                Date</th>
                                                            <th class="" tabindex="0" aria-controls="dataTables-example"
                                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                                aria-label="Rendering engine: activate to sort column descending">
                                                                Sign In</th>
                                                            <th class="" tabindex="0" aria-controls="dataTables-example"
                                                                rowspan="1" colspan="1"
                                                                aria-label="Browser: activate to sort column ascending">
                                                                Lunch In</th>
                                                            <th class="" tabindex="0" aria-controls="dataTables-example"
                                                                rowspan="1" colspan="1"
                                                                aria-label="Platform(s): activate to sort column ascending">
                                                                Lunch Out</th>
                                                            <th class="" tabindex="0" aria-controls="dataTables-example"
                                                                rowspan="1" colspan="1"
                                                                aria-label="Engine version: activate to sort column ascending">
                                                                Break IN</th>
                                                            <th class="" tabindex="0" aria-controls="dataTables-example"
                                                                rowspan="1" colspan="1"
                                                                aria-label="CSS grade: activate to sort column ascending">
                                                                Break Out</th>
                                                            <th class="" tabindex="0" aria-controls="dataTables-example"
                                                                rowspan="1" colspan="1"
                                                                aria-label="CSS grade: activate to sort column ascending">
                                                                Sign Out</th>
                                                            <!-- <th class="" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Work Hours</th> -->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        for ($day = 1; $day <= $monthTotaldays; $day++) {
                                                            if ($day != $currentDay) {
                                                                $employeeId = $_SESSION['employeeId'];
                                                                $getDataDateConvert = $day . "-" . $currentMonth . "-" . $currentYear;
                                                                $getDataDate = date('d-m-Y', strtotime($getDataDateConvert));
                                                                $qry = "select * from workhoursTbl where employeeId=$employeeId and workDate='$getDataDate'";
                                                                $result = mysqli_query($conn, $qry);
                                                                if ($result->num_rows > 0) {
                                                                    while ($row = $result->fetch_assoc()) {
                                                                        if (!empty($row['signinTime'])) {
                                                                            if (strtotime($row['workHours']) >= strtotime('8:30')) {
                                                                                echo "<tr class='gradeA even' role='row'>";
                                                                                echo "<td class='center-dashboard-block'>";
                                                                                echo $day . "-" . $currentMonth . "-" . $currentYear;
                                                                                echo "</td>";
                                                                                echo "<td class='center-dashboard-block'>";
                                                                                echo date('H:i', strtotime($row['signinTime']));
                                                                                echo "</td>";
                                                                            } else {
                                                                                echo "<tr class='gradeA even red-dashboard-block' role='row'>";
                                                                                echo "<td class='center-dashboard-block'>";
                                                                                echo $day . "-" . $currentMonth . "-" . $currentYear;
                                                                                echo "</td>";
                                                                                echo "<td class='center-dashboard-block'>";
                                                                                echo date('H:i', strtotime($row['signinTime']));
                                                                                echo "</td>";
                                                                            }
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'> </td>";
                                                                        }
                                                                        if (!empty($row['lunchinTime'])) {
                                                                            if (strtotime($row['workHours']) > strtotime('8:300')) {
                                                                                echo "<td class='center-dashboard-block'>";
                                                                                echo date('H:i', strtotime($row['lunchinTime']));
                                                                                echo "</td>";
                                                                            } else {
                                                                                echo "<td class='center-dashboard-block'>";
                                                                                echo date('H:i', strtotime($row['lunchinTime']));
                                                                                echo "</td>";
                                                                            }
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'> </td>";
                                                                        }
                                                                        if (!empty($row['lunchoutTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo date('H:i', strtotime($row['lunchoutTime']));
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'> </td>";
                                                                        }
                                                                        if (!empty($row['breakinTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            $breakinTimeValues = json_decode($row['breakinTime']);
                                                                            if (is_array($breakinTimeValues)) {
                                                                                foreach ($breakinTimeValues as $breakIns) {
                                                                                    echo date('H:i', strtotime($breakIns)) . "<br>";
                                                                                }
                                                                            } else {
                                                                                echo date('H:i', strtotime($row['breakinTime']));
                                                                            }
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'> </td>";
                                                                        }
                                                                        if (!empty($row['breakoutTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            $breakoutTimeValues = json_decode($row['breakoutTime']);
                                                                            if (is_array($breakoutTimeValues)) {
                                                                                foreach ($breakoutTimeValues as $breakOuts) {
                                                                                    echo date('H:i', strtotime($breakOuts)) . "<br>";
                                                                                }
                                                                            } else {
                                                                                echo date('H:i', strtotime($row['breakoutTime']));
                                                                            }
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'> </td>";
                                                                        }
                                                                        if (!empty($row['signoutTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo date('H:i', strtotime($row['signoutTime']));
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'> </td>";
                                                                        }
                                                                        //  if(!empty($row['workHours'])){
                                                                        //      echo "<td class='center-dashboard-block'>";
                                                                        //      echo date('H:i', strtotime($row['workHours']));
                                                                        //      echo "</td>";
                                                                        //  }else{
                                                                        //  echo "<td class='center-dashboard-block'> </td>";
                                                                        //  }
                                                                    }
                                                                } else {
                                                                    $getHolidayDateConvert = $day . "-" . $currentMonth . "-" . $currentYear;
                                                                    $dayofweek = date('l', strtotime($getDataDateConvert));
                                                                    if ($dayofweek == "Saturday" || $dayofweek == "Sunday") {
                                                                        echo "<tr class='gradeA even' role='row'>";
                                                                        echo "<td class='center-dashboard-block'>";
                                                                        echo $day . "-" . $currentMonth . "-" . $currentYear;
                                                                        echo "</td>";
                                                                        echo "<td class='center-dashboard-block'>";
                                                                        echo $dayofweek;
                                                                        echo "</td>";
                                                                        echo "<td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td>
                                                          <!--<td class='center-dashboard-block'></td>-->";
                                                                    } else {
                                                                        $getHolidayDate = date('Y-m-d', strtotime($getHolidayDateConvert));
                                                                        $qryHolidayDate = "select * from holidayTbl where holidayDate='$getHolidayDate'";
                                                                        $resultHolidayDate = mysqli_query($conn, $qryHolidayDate);
                                                                        if ($resultHolidayDate->num_rows > 0) {
                                                                            while ($rowHolidayDate = $resultHolidayDate->fetch_assoc()) {
                                                                                echo "<tr class='gradeA even' role='row'>";
                                                                                echo "<td class='center-dashboard-block'>";
                                                                                echo $day . "-" . $currentMonth . "-" . $currentYear;
                                                                                echo "</td>";
                                                                                echo "<td class='center-dashboard-block'>";
                                                                                echo $rowHolidayDate['holidayTitle'];
                                                                                echo "</td>";
                                                                                echo "<td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td>
                                                                  <!--<td class='center-dashboard-block'></td>-->";
                                                                            }
                                                                        } else {
                                                                            if ($day > $currentDay) {
                                                                                echo "<tr class='gradeA even' role='row'>";
                                                                            } else {
                                                                                echo "<tr class='gradeA even leave' role='row'>";
                                                                            }
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo $day . "-" . $currentMonth . "-" . $currentYear;
                                                                            echo "</td>";
                                                                            echo "<td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td><td class='center-dashboard-block'></td>
                                                              <!--<td class='center-dashboard-block'></td>-->";
                                                                        }
                                                                    }
                                                                    echo "</tr>";
                                                                }
                                                            } else {
                                                                $employeeId = $_SESSION['employeeId'];
                                                                $qry = "select * from workhoursTbl where employeeId=$employeeId and workDate='$currentDate'";
                                                                $result = mysqli_query($conn, $qry);
                                                                if ($result->num_rows > 0) {
                                                                    while ($row = $result->fetch_assoc()) {
                                                                        if (empty($row['workHours'])) {
                                                                            echo "<tr class='gradeA even' role='row'>";
                                                                            echo "<td class='sorting_1 center-dashboard-block'>";
                                                                            echo $day . "-" . $currentMonth . "-" . $currentYear;
                                                                            echo "</td>";
                                                                        } else {
                                                                            if (strtotime($row['workHours']) >= strtotime('8:30')) {
                                                                                echo "<tr class='gradeA even' role='row'>";
                                                                                echo "<td class='sorting_1 center-dashboard-block'>";
                                                                                echo $day . "-" . $currentMonth . "-" . $currentYear;
                                                                                echo "</td>";
                                                                            } else {
                                                                                echo "<tr class='gradeA even red-dashboard-block center-dashboard-block' role='row'>";
                                                                                echo "<td class='sorting_1'>";
                                                                                echo $day . "-" . $currentMonth . "-" . $currentYear;
                                                                                echo "</td>";
                                                                            }
                                                                        }
                                                                        if (!empty($row['signinTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo date('H:i', strtotime($row['signinTime']));
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo "<form method='POST'><button type='submit' class='btn btn-outline btn-default' name='signin'>Sign in</button></form>";
                                                                            echo "</td>";
                                                                        }
                                                                        if (!empty($row['lunchinTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo date('H:i', strtotime($row['lunchinTime']));
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo "<form method='POST'><button type='submit' class='btn btn-outline btn-primary' name='lunchin'>Lunch in</button></form>";
                                                                            echo "</td>";
                                                                        }
                                                                        if (!empty($row['lunchoutTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo date('H:i', strtotime($row['lunchoutTime']));
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo "<form method='POST'><button type='submit' class='btn btn-outline btn-success' name='lunchout'>Lunch out</button></form>";
                                                                            echo "</td>";
                                                                        }
                                                                        if (!empty($row['breakinTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            $breakInTimes = json_decode($row['breakinTime']);


                                                                            foreach ($breakInTimes as $breakInTime) {
                                                                                echo date('H:i', strtotime($breakInTime)) . "<br>";
                                                                            }
                                                                            if (!empty($row['breakoutTime']) && empty($row['signoutTime'])) {
                                                                                $breakOutTimes = json_decode($row['breakoutTime']);

                                                                                if (count($breakInTimes) != count($breakOutTimes)) {
                                                                                } else {
                                                                                    if (count($breakInTimes) < 2) {
                                                                                        // echo "<script>console.log(".json_encode($breakInTimes).")</script>";
                                                                                        // echo "<script>console.log(".json_encode($breakOutTimes).")</script>";
                                                                                        echo "<form method='POST'><button type='submit' class='btn btn-outline btn-info' name='extraBreakin'>Break in</button></form>";
                                                                                    }



                                                                                }
                                                                            }
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo "<form method='POST'><button type='submit' class='btn btn-outline btn-info' name='breakin'>Break in</button></form>";
                                                                            echo "</td>";
                                                                        }
                                                                        if (!empty($row['breakoutTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            $breakOutTimes = json_decode($row['breakoutTime']);
                                                                            foreach ($breakOutTimes as $breakOutTime) {
                                                                                echo date('H:i', strtotime($breakOutTime)) . "<br>";
                                                                            }
                                                                            if (!empty($row['breakoutTime']) && empty($row['signoutTime'])) {
                                                                                if (count($breakOutTimes) < 2) {
                                                                                    echo "<form method='POST'><button type='submit' class='btn btn-outline btn-warning' name='extraBreakout'>Break out</button></form>";
                                                                                }
                                                                            }
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo "<form method='POST'><button type='submit' class='btn btn-outline btn-warning' name='breakout'>Break out</button></form>";
                                                                            echo "</td>";
                                                                        }
                                                                        if (!empty($row['signoutTime'])) {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo date('H:i', strtotime($row['signoutTime']));
                                                                            echo "</td>";
                                                                        } else {
                                                                            echo "<td class='center-dashboard-block'>";
                                                                            echo "<form method='POST'>
                                                                    <button type='submit' class='btn btn-outline btn-danger' name='logoutBtn'>Logout</button></form>";
                                                                            echo "</td>";
                                                                        }
                                                                        //  if(!empty($row['workHours'])){
                                                                        //      echo "<td class='center-dashboard-block'>";
                                                                        //      echo date('H:i', strtotime($row['workHours']));
                                                                        //      echo "</td>";
                                                                        //  }else{
                                                                        //  echo "<td class='center-dashboard-block'>";
                                                                        //  echo "";
                                                                        //  echo "</td>";
                                                                        //  }
                                                                    }
                                                                } else {
                                                                    echo "<tr class='gradeA even' role='row'>";
                                                                    echo "<td class='center-dashboard-block'>";
                                                                    echo $day . "-" . $currentMonth . "-" . $currentYear;
                                                                    echo "</td>";
                                                                    echo "<td class='center-dashboard-block'>";
                                                                    echo "<form method='POST'><button type='submit' class='btn btn-outline btn-default' name='signin'>Sign in</button></form>";
                                                                    echo "</td>";
                                                                    echo "<td class='center-dashboard-block'>";
                                                                    echo "<form method='POST'><button type='submit' class='btn btn-outline btn-primary' name='lunchin'>Lunch in</button></form>";
                                                                    echo "</td>";
                                                                    echo "<td class='center-dashboard-block'>";
                                                                    echo "<form method='POST'><button type='submit' class='btn btn-outline btn-success' name='lunchout'>Lunch out</button></form>";
                                                                    echo "</td>";
                                                                    echo "<td class='center-dashboard-block'>";
                                                                    echo "<form method='POST'><button type='submit' class='btn btn-outline btn-info' name='breakin'>Break in</button></form>";
                                                                    echo "</td>";
                                                                    echo "<td class='center-dashboard-block'>";
                                                                    echo "<form method='POST'><button type='submit' class='btn btn-outline btn-warning' name='breakout'>Break out</button></form>";
                                                                    echo "</td>";
                                                                    echo "<td class='center-dashboard-block'>";
                                                                    echo "<form method='POST'><button type='submit' class='btn btn-outline btn-danger' name='logoutBtn'>Logout</button></form>";
                                                                    echo "</td>";
                                                                    //  echo "<td class='center-dashboard-block'>";
                                                                    //      echo "";
                                                                    //  echo "</td>";
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
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    <?php include 'footer.php'; ?>
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
        $(document).ready(function () {
            $('#example').DataTable({
                "paging": false,
                "ordering": true,
                "info": false
            });
        });
    </script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>