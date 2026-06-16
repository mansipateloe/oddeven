<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();oecrm_require_permission($conn,'attendance_review','correct');$_SESSION['attendance_flash']='Use Review & Exceptions to correct attendance records.';header('Location: attendanceReview.php');exit;
