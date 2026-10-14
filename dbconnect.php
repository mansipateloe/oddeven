<?php
$conn = mysqli_connect("localhost", "oddev213", "QSVtIhci4d989", "oddev213");
//$conn = mysqli_connect("localhost", "root", "Admin@1234", "oddnew");


if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
$today_date = date('Y-m-d');