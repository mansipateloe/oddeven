<?php
$conn = mysqli_connect("localhost", "root", "", "oddeven");
//$conn = mysqli_connect("localhost", "root", "", "oddeven");


if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
$today_date = date('Y-m-d');