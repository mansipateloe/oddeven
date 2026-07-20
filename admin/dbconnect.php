<?php 
<<<<<<< HEAD
    $conn = mysqli_connect("localhost", "stagi408", "W84PBjTBY18Ap", "stagi408");
=======
    $conn = mysqli_connect("localhost", "oddev213", "QSVtIhci4d989", "oddev213");
>>>>>>> 16f952b06473752f063dd7dad31e1ed1d31da926
    if($conn === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    
    $today_date = date('Y-m-d'); //get today date
 ?>