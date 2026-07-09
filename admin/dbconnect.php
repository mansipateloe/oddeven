<?php 
    $conn = mysqli_connect("localhost", "oddev213", "QSVtIhci4d989", "oddev213");
    if($conn === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    
    $today_date = date('Y-m-d'); //get today date
 ?>