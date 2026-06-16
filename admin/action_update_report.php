<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
oecrm_require_admin_login();oecrm_require_csrf();oecrm_require_permission($conn,'attendance_review','correct');$_SESSION['attendance_flash']='Legacy attendance edits are disabled. Submit a correction through Review & Exceptions.';header('Location: attendanceReview.php');exit;
