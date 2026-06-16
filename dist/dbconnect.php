<?php 
	$conn = mysqli_connect("localhost", "root", "", "attendance_management");
	if($conn === false){
	    die("ERROR: Could not connect. " . mysqli_connect_error());
	}
 ?>