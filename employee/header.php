<?php require_once __DIR__ . '/../security.php';
oecrm_require_employee_login();
include 'dbconnect.php';
include 'validation.php';

$currentEmployeePage = basename($_SERVER['PHP_SELF'] ?? 'home.php');
$employeePageTitles = [
   'home.php' => 'Dashboard',
   'dashboard.php' => 'Attendance',
   'viewProject.php' => 'My Projects',
   'addTask.php' => 'Add Task',
   'editTask.php' => 'Edit Task',
   'viewTask.php' => 'My Tasks',
   'timesheets.php' => 'Timesheets',
   'notices.php' => 'Notices',
   'holidays.php' => 'Holidays',
   'leave_index.php' => 'Leaves',
   'userinfo.php' => 'My Profile',
];
$employeePageTitle = $employeePageTitles[$currentEmployeePage] ?? 'Employee Portal';
$projectPages = ['viewProject.php', 'viewTask.php', 'timesheets.php'];

function employee_nav_class($pages, $currentPage)
{
   return in_array($currentPage, (array) $pages, true) ? ' class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      <title>Welcome</title>
      <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
      <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
      <link href="../vendor/morrisjs/morris.css" rel="stylesheet">
      <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
      <link href="../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
      <link href="../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
      <link href="../vendor/custom/custom.css?v=20260612-8" rel="stylesheet">
      <link href="../vendor/custom/fontawesome.all.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   </head>
   <body class="employee-portal" data-current-page="<?php echo htmlspecialchars($currentEmployeePage, ENT_QUOTES, 'UTF-8'); ?>">
      <div id="wrapper">
      <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
         <div class="navbar-default sidebar border" role="navigation">
            <div class="headerlogo"><a class="navbar-brand" href="index.php">
               <img class="img-responsive" src="../images/logo.png" alt=""></a>
            </div>
            <div class="sidebar-nav navbar-collapse">
               <ul class="nav" id="side-menu">
               <?php
                  $employeeId = (int) $_SESSION['employeeId'];
                  $qryEmpView = "SELECT * FROM employeesTbl WHERE id=".$employeeId;
                  $resultEmpView = mysqli_query($conn,$qryEmpView);
                  $rowEmpView = $resultEmpView ? $resultEmpView->fetch_assoc() : null;
               ?>
               <li>
                  <div class="dropdown">
                     <button class="btn btn-primary" type="button">
                        <img class="img-circle" src="../images/user-image.png" alt="">
                        <span>Hi, <?php echo htmlspecialchars($rowEmpView['name'] ?? 'Employee', ENT_QUOTES, 'UTF-8'); ?></span>
                     </button>
                  </div>
               </li>
               <li class="erp-nav-label">My Workspace</li>
               <li><a href="home.php"<?php echo employee_nav_class('home.php', $currentEmployeePage); ?>><i class="fa fa-tachometer fa-fw"></i> Dashboard</a></li>
               <li><a href="dashboard.php"<?php echo employee_nav_class('dashboard.php', $currentEmployeePage); ?>><i class="fa fa-calendar-check-o fa-fw" aria-hidden="true"></i>Attendance</a></li>
               <li<?php echo in_array($currentEmployeePage, $projectPages, true) ? ' class="active open"' : ''; ?>>
                  <a href="#"><i class="fa fa-briefcase fa-fw" aria-hidden="true"></i>Projects<span class="fa arrow"></span></a>
                  <ul class="nav nav-second-level"<?php echo in_array($currentEmployeePage, $projectPages, true) ? ' style="display:block"' : ''; ?>>
                     <li><a href="viewProject.php"<?php echo employee_nav_class('viewProject.php', $currentEmployeePage); ?>><i class="fa fa-folder-open fa-fw" aria-hidden="true"></i>View all Projects</a></li>
                     <li><a href="viewTask.php"<?php echo employee_nav_class('viewTask.php', $currentEmployeePage); ?>><i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i>View all Task</a></li>
                     <li><a href="timesheets.php"<?php echo employee_nav_class('timesheets.php', $currentEmployeePage); ?>><i class="fa fa-clock-o fa-fw" aria-hidden="true"></i>Timesheets</a></li>
                     
                  </ul>
               </li>
               <li class="erp-nav-label">Company & HR</li>
               <li><a href="notices.php"<?php echo employee_nav_class('notices.php', $currentEmployeePage); ?>><i class="fa fa-bullhorn fa-fw" aria-hidden="true"></i>Notices</a></li>
               <li><a href="holidays.php"<?php echo employee_nav_class('holidays.php', $currentEmployeePage); ?>><i class="fa fa-calendar fa-fw" aria-hidden="true"></i>Holidays</a></li>
               <li><a href="leave_index.php"<?php echo employee_nav_class('leave_index.php', $currentEmployeePage); ?>><i class="fa fa-calendar-minus-o fa-fw" aria-hidden="true"></i>Leaves</a></li>
               </ul>
            </div>
         </div>
      </nav>
      <header class="employee-topbar">
         <div class="employee-topbar-left">
            <button type="button" class="employee-menu-toggle" aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>
            <div>
               <span class="employee-breadcrumb">Employee / <?php echo htmlspecialchars($employeePageTitle, ENT_QUOTES, 'UTF-8'); ?></span>
               <h1><?php echo htmlspecialchars($employeePageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
            </div>
         </div>
         <div class="employee-topbar-actions">
            <span class="employee-today"><i class="fa fa-calendar-o"></i><?php echo date('D, d M Y'); ?></span>
            <a class="employee-icon-link" href="notices.php" title="Notices"><i class="fa fa-bell-o"></i></a>
            <a class="employee-profile-link" href="userinfo.php" title="User Profile">
               <img src="../images/user-image.png" alt="">
               <span><?php echo htmlspecialchars($rowEmpView['name'] ?? 'Employee', ENT_QUOTES, 'UTF-8'); ?><small>Employee</small></span>
            </a>
            <a class="employee-icon-link employee-logout-link" href="logout.php" title="Logout" aria-label="Logout"><i class="fa fa-sign-out"></i></a>
         </div>
      </header>

