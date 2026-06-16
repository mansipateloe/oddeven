<?php 
    $conn = mysqli_connect("localhost", "root", "", "oecrm");
    if($conn === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    
    $today_date = date('Y-m-d'); //get today date
 ?>