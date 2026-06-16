<?php
ob_start();
session_start();
session_unset();
session_destroy();
session_start();
$_SESSION['msg'] = "logout";
header("location:../login.php");
?>