<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect("localhost", "oddev213", "QSVtIhci4d989", "oddev213");
    if($conn === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
 ?>