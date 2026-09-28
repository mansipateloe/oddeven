<?php require_once __DIR__ . '/../security.php';
oecrm_require_employee_login();
include 'dbconnect.php';
include 'validation.php';
require_once __DIR__ . '/../birthdays.php';

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
function employee_sidebar_is_active($pages, $currentPage)
{
   return in_array($currentPage, (array) $pages, true);
}
function employee_sidebar_link($href, $label, $icon, $currentPage, $pages = null)
{
   $matchPages = $pages ?: [$href];
   $active = employee_sidebar_is_active($matchPages, $currentPage) ? ' class="active"' : '';
   echo '<li><a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"' . $active . '><i class="fa ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . ' fa-fw"></i><span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span></a></li>';
}
function employee_sidebar_parent($label, $icon, $items, $currentPage)
{
   $isActive = false;
   foreach ($items as $item) {
      if (employee_sidebar_is_active($item['pages'] ?? [$item['href']], $currentPage)) {
         $isActive = true;
         break;
      }
   }
   $class = 'oecrm-sidebar-parent' . ($isActive ? ' active open' : '');
   $style = $isActive ? ' style="display:block"' : '';
   echo '<li class="' . $class . '">';
   echo '<a href="#" aria-expanded="' . ($isActive ? 'true' : 'false') . '"><i class="fa ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . ' fa-fw"></i><span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span><span class="fa arrow"></span></a>';
   echo '<ul class="nav nav-second-level"' . $style . '>';
   foreach ($items as $item) {
      employee_sidebar_link($item['href'], $item['label'], $item['icon'], $currentPage, $item['pages'] ?? null);
   }
   echo '</ul></li>';
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
      <link href="../vendor/custom/custom.css?v=20260627-1" rel="stylesheet">
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
                  $qryEmpView = "SELECT * FROM employeestbl WHERE id=".$employeeId;
                  $resultEmpView = mysqli_query($conn,$qryEmpView);
                  $rowEmpView = $resultEmpView ? $resultEmpView->fetch_assoc() : null;
                  if (!empty($rowEmpView['company_id'])) {
                     oecrm_auto_send_birthday_wishes($conn, (int) $rowEmpView['company_id']);
                  }
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
               <?php employee_sidebar_link('home.php', 'Dashboard', 'fa-tachometer', $currentEmployeePage, ['home.php']); ?>
               <?php employee_sidebar_link('dashboard.php', 'Attendance', 'fa-calendar-check-o', $currentEmployeePage, ['dashboard.php']); ?>
               <?php
                  employee_sidebar_parent('Projects', 'fa-briefcase', [
                     ['href' => 'viewProject.php', 'label' => 'My Projects', 'icon' => 'fa-folder-open', 'pages' => ['viewProject.php', 'manageProject.php', 'editProject.php']],
                     ['href' => 'viewTask.php', 'label' => 'My Tasks', 'icon' => 'fa-check-square-o', 'pages' => ['viewTask.php', 'addTask.php', 'editTask.php']],
                     ['href' => 'timesheets.php', 'label' => 'Timesheets', 'icon' => 'fa-clock-o', 'pages' => ['timesheets.php']],
                  ], $currentEmployeePage);
               ?>
               <li class="erp-nav-label">Company & HR</li>
               <?php employee_sidebar_link('notices.php', 'Notices', 'fa-bullhorn', $currentEmployeePage, ['notices.php', 'dashboardNotices.php']); ?>
               <?php employee_sidebar_link('holidays.php', 'Holidays', 'fa-calendar', $currentEmployeePage, ['holidays.php']); ?>
               <?php employee_sidebar_link('leave_index.php', 'Leaves', 'fa-calendar-minus-o', $currentEmployeePage, ['leave_index.php']); ?>
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
            <div class="employee-profile-menu">
               <button type="button" class="employee-profile-link" aria-haspopup="true" aria-expanded="false" data-profile-toggle="employee">
                  <img src="../images/user-image.png" alt="">
                  <span><?php echo htmlspecialchars($rowEmpView['name'] ?? 'Employee', ENT_QUOTES, 'UTF-8'); ?><small>Employee</small></span>
                  <i class="fa fa-angle-down"></i>
               </button>
               <div class="employee-profile-dropdown">
                  <a href="userinfo.php"><i class="fa fa-user"></i> My Profile</a>
                  <a href="notices.php"><i class="fa fa-bell-o"></i> Notices</a>
                  <a href="leave_index.php"><i class="fa fa-calendar-minus-o"></i> Leaves</a>
                  <a class="danger" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
               </div>
            </div>
         </div>
      </header>






