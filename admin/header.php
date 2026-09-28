<?php include 'dbconnect.php'; ?>
<?php require_once __DIR__ . '/../security.php'; ?>
<?php require_once __DIR__ . '/../foundation.php'; ?>
<?php oecrm_require_admin_login(); ?>
<?php include 'validation.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Oddeven Infotech Pvt. Ltd. - Admin Panel</title>

  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
  <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
  <link href="../vendor/morrisjs/morris.css" rel="stylesheet">
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
  <link href="../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../vendor/custom/customAdmin.css?v=20260627-1">
  <!-- <style>
        .nav-second-level {
            display: none; /* Hide all submenus by default */
        }

        .active .nav-second-level {
            display: block; /* Show the submenu when the parent is active */
        }
    </style> -->
  <!-- <link href="../vendor/custom/custom.css" rel="stylesheet">
 -->
</head>

<body class="admin-portal" data-current-page="<?php echo oecrm_h(basename($_SERVER['PHP_SELF'] ?? 'dashboard.php'));?>">
  <?php
  function check_is_access_new($mname,$utype="")
  {
    global $conn;
    return oecrm_legacy_can($conn, $mname, (int) $utype) ? 1 : 0;
  }
  ?>
  <?php
  $adminId = $_SESSION['adminId'];
  if(!isset($_SESSION["adminName"]))
  {
    if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]==1)
    {
      $qryadminView = "SELECT * FROM admins WHERE id=" . $adminId;
      $resultadminView = mysqli_query($conn, $qryadminView);
      $rowadminView = $resultadminView->fetch_assoc();
      $_SESSION['adminName']=$rowadminView['uname'];
    }else
    {
      $qryadminView = "SELECT * FROM employeestbl WHERE id=" . $adminId;
      $resultadminView = mysqli_query($conn, $qryadminView);
      $rowadminView = $resultadminView->fetch_assoc();
      $_SESSION['adminName']=$rowadminView['name'];
    }
    
    
  }
  
  ?>
  <?php
  $adminCurrentPage = basename($_SERVER['PHP_SELF'] ?? 'dashboard.php');
  $adminPageTitles = [
    'dashboard.php'=>'Dashboard','clients.php'=>'Clients','clientProfile.php'=>'Client Profile',
    'projectWorkspace.php'=>'Projects','projectEditor.php'=>'Project Editor','projectBoard.php'=>'Project Board',
    'viewTask.php'=>'Tasks','taskEditor.php'=>'Task Editor','timesheets.php'=>'Timesheets',
    'manageEmployee.php'=>'Employees','addEmployee.php'=>'Add Employee','editEmployee.php'=>'Edit Employee',
    'employeeProfile.php'=>'Employee Profile','employeeContinuations.php'=>'Employee Continuations','attendanceReview.php'=>'Attendance Review',
    'manageLeave.php'=>'Leave Management','payrollManagement.php'=>'Payroll','salary.php'=>'Salary Reports',
    'assets.php'=>'Assets','resources.php'=>'Resources','finance.php'=>'Finance',
    'noticeCenter.php'=>'Notices','birthdayCenter.php'=>'Birthdays','workforceReports.php'=>'Workforce Reports',
    'role_permissions.php'=>'Role Permissions','audit_logs.php'=>'Audit Logs',
  ];
  $adminPageTitle = $adminPageTitles[$adminCurrentPage] ?? ucwords(str_replace(['.php','_'],['',' '],$adminCurrentPage));
  $birthdayCompanyId = oecrm_current_company_id($conn);
  require_once __DIR__ . '/../birthdays.php';
  oecrm_auto_send_birthday_wishes($conn, $birthdayCompanyId);
  $birthdayTodayCountRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM employeestbl WHERE company_id=" . (int) $birthdayCompanyId . " AND status=0 AND birthdate REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' AND DATE_FORMAT(STR_TO_DATE(birthdate,'%Y-%m-%d'),'%m-%d')=DATE_FORMAT(CURDATE(),'%m-%d')"));
  $birthdayTodayCount = (int) ($birthdayTodayCountRow['total'] ?? 0);
  $adminCompanyRow = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT display_name FROM companies WHERE id=' . (int) $birthdayCompanyId . ' LIMIT 1'));
  $adminCompanyName = $adminCompanyRow['display_name'] ?? 'Active Company';
  if (!function_exists('admin_sidebar_is_active')) {
    function admin_sidebar_is_active($pages, $currentPage)
    {
      return in_array($currentPage, (array) $pages, true);
    }
  }
  if (!function_exists('admin_sidebar_link')) {
    function admin_sidebar_link($href, $label, $icon, $currentPage, $pages = null)
    {
      $matchPages = $pages ?: [$href];
      $active = admin_sidebar_is_active($matchPages, $currentPage) ? ' class="active"' : '';
      echo '<li><a href="' . oecrm_h($href) . '"' . $active . '><i class="fa ' . oecrm_h($icon) . ' fa-fw"></i><span>' . oecrm_h($label) . '</span></a></li>';
    }
  }
  if (!function_exists('admin_sidebar_parent')) {
    function admin_sidebar_parent($label, $icon, $items, $currentPage)
    {
      $allowedItems = [];
      $isActive = false;
      foreach ($items as $item) {
        if (empty($item['show'])) {
          continue;
        }
        $item['pages'] = $item['pages'] ?? [$item['href']];
        if (admin_sidebar_is_active($item['pages'], $currentPage)) {
          $isActive = true;
        }
        $allowedItems[] = $item;
      }
      if (!$allowedItems) {
        return;
      }
      $parentClass = 'oecrm-sidebar-parent' . ($isActive ? ' active open' : '');
      $style = $isActive ? ' style="display:block"' : '';
      echo '<li class="' . $parentClass . '">';
      echo '<a href="#" aria-expanded="' . ($isActive ? 'true' : 'false') . '"><i class="fa ' . oecrm_h($icon) . ' fa-fw"></i><span>' . oecrm_h($label) . '</span><span class="fa arrow"></span></a>';
      echo '<ul class="nav nav-second-level"' . $style . '>';
      foreach ($allowedItems as $item) {
        admin_sidebar_link($item['href'], $item['label'], $item['icon'], $currentPage, $item['pages']);
      }
      echo '</ul></li>';
    }
  }
  ?>
  <div id="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

      <div class="navbar-default sidebar" role="navigation">
        <div class="headerlogo" style="
    display: flex;
    align-content: center;
    justify-content: center;
    align-items: center;">
    <a class="navbar-brand" href="index.php"><img class="img-responsive" src="../admin/img/logo.png" alt=""></a></div>

        <div class="sidebar-nav">
          <ul class="nav erp-sidebar" id="side-menu">
            <li class="sidebar-user-menu">
              <button type="button" class="sidebar-user-toggle">
                <span><i><img class="img-circle" src="../admin/img/user-image.png" alt=""></i><?php echo "Hi, ".$_SESSION['adminName']; ?></span><span class="fa arrow"></span>
              </button>
              <ul class="nav nav-second-level"><li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li></ul>
            </li>

            <?php if (oecrm_is_super_admin()) { $companyOptions=mysqli_query($conn,"SELECT id,display_name FROM companies WHERE status=1 ORDER BY display_name");$currentCompanyId=oecrm_current_company_id($conn); ?>
            <li class="erp-company-context"><form method="post" action="switch_company.php"><?php echo oecrm_csrf_field();?><label><i class="fa fa-building"></i> Active Company</label><select name="company_id" onchange="this.form.submit()" title="Active company"><?php while($companyOption=mysqli_fetch_assoc($companyOptions)):?><option value="<?php echo (int)$companyOption['id'];?>" <?php echo $currentCompanyId===(int)$companyOption['id']?'selected':'';?>><?php echo oecrm_h($companyOption['display_name']);?></option><?php endwhile;?></select></form></li>
            <?php } ?>

            <?php
            admin_sidebar_link('dashboard.php', 'Dashboard', 'fa-tachometer', $adminCurrentPage, ['dashboard.php']);

            echo '<li class="erp-nav-label">Business</li>';
            admin_sidebar_parent('CRM & Sales', 'fa-handshake-o', [
              ['href'=>'clients.php','label'=>'Clients','icon'=>'fa-building-o','show'=>oecrm_can($conn,'clients','view'),'pages'=>['clients.php','clientProfile.php']],
              ['href'=>'add_lead.php','label'=>'Add Lead','icon'=>'fa-user-plus','show'=>check_is_access_new('add_lead')==1,'pages'=>['add_lead.php']],
              ['href'=>'leads.php','label'=>'Lead Pipeline','icon'=>'fa-address-book-o','show'=>check_is_access_new('view_lead')==1,'pages'=>['leads.php','action_update_lead.php']],
              ['href'=>'followup.php','label'=>'Follow-ups','icon'=>'fa-calendar-check-o','show'=>check_is_access_new('view_lead')==1,'pages'=>['followup.php','action_update_followup.php']],
              ['href'=>'manageLeadSource.php','label'=>'Lead Sources','icon'=>'fa-code-fork','show'=>check_is_access_new('view_lead')==1,'pages'=>['manageLeadSource.php']],
              ['href'=>'manageFollowupType.php','label'=>'Follow-up Types','icon'=>'fa-tags','show'=>check_is_access_new('view_lead')==1,'pages'=>['manageFollowupType.php']],
            ], $adminCurrentPage);

            admin_sidebar_parent('Projects & Tasks', 'fa-briefcase', [
              ['href'=>'projectWorkspace.php','label'=>'Projects','icon'=>'fa-folder-open','show'=>check_is_access_new('project')==1||oecrm_can($conn,'projects','view'),'pages'=>['projectWorkspace.php','projectEditor.php','projectBoard.php']],
              ['href'=>'projectEditor.php','label'=>'New Project','icon'=>'fa-plus-square','show'=>check_is_access_new('project')==1||oecrm_can($conn,'projects','create'),'pages'=>['projectEditor.php']],
              ['href'=>'viewTask.php','label'=>'Tasks','icon'=>'fa-tasks','show'=>check_is_access_new('lead')==1||oecrm_can($conn,'tasks','view'),'pages'=>['viewTask.php','taskEditor.php']],
              ['href'=>'projectKanban.php','label'=>'Task Board','icon'=>'fa-columns','show'=>oecrm_can($conn,'project_sprints','view')||oecrm_can($conn,'tasks','view'),'pages'=>['projectKanban.php']],
              ['href'=>'timesheets.php','label'=>'Timesheets','icon'=>'fa-clock-o','show'=>oecrm_can($conn,'timesheets','view'),'pages'=>['timesheets.php']],
            ], $adminCurrentPage);

            echo '<li class="erp-nav-label">People & HR</li>';
            admin_sidebar_parent('Employees', 'fa-users', [
              ['href'=>'manageEmployee.php','label'=>'Employee Directory','icon'=>'fa-address-card-o','show'=>check_is_access_new('employee')==1||oecrm_can($conn,'employees','view'),'pages'=>['manageEmployee.php','employeeProfile.php','editEmployee.php']],
              ['href'=>'addEmployee.php','label'=>'Add Employee','icon'=>'fa-user-plus','show'=>check_is_access_new('employee')==1||oecrm_can($conn,'employees','create'),'pages'=>['addEmployee.php']],
              ['href'=>'employeeContinuations.php','label'=>'Job Renewal / Continuation','icon'=>'fa-refresh','show'=>check_is_access_new('employee')==1||oecrm_can($conn,'employees','view'),'pages'=>['employeeContinuations.php']],
              ['href'=>'departments.php','label'=>'Departments','icon'=>'fa-sitemap','show'=>oecrm_can($conn,'departments','view')||check_is_access_new('employee')==1,'pages'=>['departments.php']],
              ['href'=>'manageDesignation.php','label'=>'Designations','icon'=>'fa-id-badge','show'=>check_is_access_new('employee')==1||oecrm_can($conn,'designations','view'),'pages'=>['manageDesignation.php']],
              ['href'=>'employeeExits.php','label'=>'Employee Exit & F&F','icon'=>'fa-sign-out','show'=>oecrm_can($conn,'employee_exit','view'),'pages'=>['employeeExits.php']],
            ], $adminCurrentPage);

            admin_sidebar_parent('Attendance & Leave', 'fa-clock-o', [
              ['href'=>'attendanceReview.php','label'=>'Attendance Review','icon'=>'fa-check-square-o','show'=>oecrm_can($conn,'attendance_review','view'),'pages'=>['attendanceReview.php']],
              ['href'=>'manageLeave.php','label'=>'Leave Requests','icon'=>'fa-calendar-minus-o','show'=>check_is_access_new('leave')==1||oecrm_can($conn,'leave_requests','view'),'pages'=>['manageLeave.php']],
              ['href'=>'manageLeaveType.php','label'=>'Leave Types','icon'=>'fa-calendar-plus-o','show'=>check_is_access_new('settings')==1||oecrm_can($conn,'leave_types','view'),'pages'=>['manageLeaveType.php']],
              ['href'=>'leavePolicies.php','label'=>'Leave & Comp-Off Rules','icon'=>'fa-sliders','show'=>check_is_access_new('leave')==1||oecrm_can($conn,'leave_policies','view'),'pages'=>['leavePolicies.php']],
              ['href'=>'manageHoliday.php','label'=>'Holidays','icon'=>'fa-calendar','show'=>check_is_access_new('settings')==1||oecrm_can($conn,'holidays','view'),'pages'=>['manageHoliday.php']],
              ['href'=>'shifts.php','label'=>'Shifts','icon'=>'fa-clock-o','show'=>oecrm_can($conn,'shifts','view'),'pages'=>['shifts.php']],
              ['href'=>'activeShiftAssignments.php','label'=>'Shift Assignments','icon'=>'fa-random','show'=>oecrm_can($conn,'shift_assignments','view'),'pages'=>['activeShiftAssignments.php']],
            ], $adminCurrentPage);

            admin_sidebar_parent('HR Documents', 'fa-file-text-o', [
              ['href'=>'hrLetterTemplates.php','label'=>'Letter Templates','icon'=>'fa-files-o','show'=>oecrm_can($conn,'hr_letter_templates','view'),'pages'=>['hrLetterTemplates.php']],
              ['href'=>'hrLetters.php','label'=>'Employee Letters','icon'=>'fa-envelope-open-o','show'=>oecrm_can($conn,'hr_letters','view'),'pages'=>['hrLetters.php']],
            ], $adminCurrentPage);

            echo '<li class="erp-nav-label">Finance & Resources</li>';
            admin_sidebar_parent('Finance & Accounting', 'fa-line-chart', [
              ['href'=>'finance.php','label'=>'Finance Overview','icon'=>'fa-dashboard','show'=>oecrm_can($conn,'finance','view'),'pages'=>['finance.php']],
              ['href'=>'addInvoice.php','label'=>'Invoices','icon'=>'fa-file-text-o','show'=>check_is_access_new('lead')==1||oecrm_can($conn,'finance','view'),'pages'=>['addInvoice.php','invoiceEditor.php']],
              ['href'=>'addDeposit.php','label'=>'Collections & Deposits','icon'=>'fa-arrow-circle-down','show'=>check_is_access_new('deposit')==1,'pages'=>['addDeposit.php']],
              ['href'=>'viewexpense.php','label'=>'Expenses','icon'=>'fa-credit-card','show'=>check_is_access_new('expense')==1||oecrm_can($conn,'finance','view'),'pages'=>['viewexpense.php','expenseEditor.php']],
              ['href'=>'projectExpense.php','label'=>'Project Expenses','icon'=>'fa-pie-chart','show'=>check_is_access_new('project_expense')==1,'pages'=>['projectExpense.php']],
              ['href'=>'payrollManagement.php','label'=>'Payroll','icon'=>'fa-calculator','show'=>oecrm_can($conn,'payroll','view'),'pages'=>['payrollManagement.php','salary.php']],
              ['href'=>'salaryPolicies.php','label'=>'Salary Policies','icon'=>'fa-list-alt','show'=>oecrm_can($conn,'payroll','view'),'pages'=>['salaryPolicies.php']],
              ['href'=>'manageCurrency.php','label'=>'Currency','icon'=>'fa-exchange','show'=>true,'pages'=>['manageCurrency.php']],
              ['href'=>'addBankDetails.php','label'=>'Bank Details','icon'=>'fa-bank','show'=>true,'pages'=>['addBankDetails.php','addaccount.php']],
              ['href'=>'manageProfessionalTax.php','label'=>'Professional Tax','icon'=>'fa-percent','show'=>true,'pages'=>['manageProfessionalTax.php']],
              ['href'=>'gstReport.php','label'=>'GST Reports','icon'=>'fa-percent','show'=>oecrm_can($conn,'gst','view'),'pages'=>['gstReport.php']],
              ['href'=>'bankReconciliation.php','label'=>'Cash Flow & Reconciliation','icon'=>'fa-balance-scale','show'=>oecrm_can($conn,'bank_reconciliation','view'),'pages'=>['bankReconciliation.php']],
            ], $adminCurrentPage);

            admin_sidebar_parent('Assets & Resources', 'fa-cubes', [
              ['href'=>'assets.php','label'=>'Asset Management','icon'=>'fa-laptop','show'=>oecrm_can($conn,'assets','view'),'pages'=>['assets.php','assetEditor.php']],
              ['href'=>'resources.php','label'=>'Resource Planning','icon'=>'fa-users','show'=>oecrm_can($conn,'resources','view'),'pages'=>['resources.php','resourceAllocation.php']],
              ['href'=>'accessManagement.php','label'=>'Access Management','icon'=>'fa-key','show'=>oecrm_can($conn,'access_management','view'),'pages'=>['accessManagement.php','accessAccount.php']],
              ['href'=>'subscriptions.php','label'=>'Subscriptions & Vault','icon'=>'fa-refresh','show'=>oecrm_can($conn,'subscriptions','view'),'pages'=>['subscriptions.php']],
              ['href'=>'viewDomainHosting.php','label'=>'Domain Register','icon'=>'fa-server','show'=>check_is_access_new('domain_hosting')==1,'pages'=>['viewDomainHosting.php','expiring_hostings.php']],
            ], $adminCurrentPage);

            admin_sidebar_parent('Notices & Policies', 'fa-bullhorn', [
              ['href'=>'noticeCenter.php','label'=>'Notices','icon'=>'fa-bell-o','show'=>oecrm_can($conn,'notices','view'),'pages'=>['noticeCenter.php']],
              ['href'=>'birthdayCenter.php','label'=>'Birthday Wishes','icon'=>'fa-birthday-cake','show'=>oecrm_can($conn,'notices','view')||oecrm_is_super_admin(),'pages'=>['birthdayCenter.php']],
            ], $adminCurrentPage);

            admin_sidebar_parent('Reports', 'fa-bar-chart', [
              ['href'=>'erpAnalytics.php','label'=>'Management Dashboard','icon'=>'fa-dashboard','show'=>oecrm_can($conn,'dashboards','view'),'pages'=>['erpAnalytics.php']],
              ['href'=>'workforceReports.php','label'=>'Workforce Reports','icon'=>'fa-line-chart','show'=>oecrm_can($conn,'dashboards','view'),'pages'=>['workforceReports.php']],
            ], $adminCurrentPage);

            echo '<li class="erp-nav-label">System</li>';
            admin_sidebar_parent('Settings', 'fa-cogs', [
              ['href'=>'companies.php','label'=>'Companies','icon'=>'fa-building','show'=>oecrm_can($conn,'companies','view'),'pages'=>['companies.php']],
              ['href'=>'all_user_roles.php','label'=>'Roles','icon'=>'fa-user-secret','show'=>oecrm_can($conn,'roles','view')||check_is_access_new('employee')==1,'pages'=>['all_user_roles.php','add_user_roles.php']],
              ['href'=>'role_permissions.php','label'=>'Permissions','icon'=>'fa-key','show'=>oecrm_can($conn,'roles','manage_permissions'),'pages'=>['role_permissions.php']],
              ['href'=>'audit_logs.php','label'=>'Activity Logs','icon'=>'fa-shield','show'=>oecrm_can($conn,'activity_audit','view')||oecrm_can($conn,'audit','view'),'pages'=>['audit_logs.php']],
              ['href'=>'manageEmployeeLoginLog.php','label'=>'Employee Login Logs','icon'=>'fa-sign-in','show'=>check_is_access_new('settings')==1,'pages'=>['manageEmployeeLoginLog.php']],
              ['href'=>'addcountry.php','label'=>'Countries / States / Cities','icon'=>'fa-flag','show'=>check_is_access_new('settings')==1,'pages'=>['addcountry.php']],
              ['href'=>'databaseMaintenance.php','label'=>'Database Backups','icon'=>'fa-database','show'=>oecrm_can($conn,'backups','view'),'pages'=>['databaseMaintenance.php']],
            ], $adminCurrentPage);
            ?>
          </ul>        </div>
        <!-- /.sidebar-collapse -->
      </div>
      
      <!-- /.navbar-static-side -->
    </nav>
    <header class="admin-topbar">
      <div class="admin-topbar-left">
        <button type="button" class="admin-menu-toggle" aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>
        <div><span>Admin / <?php echo oecrm_h($adminPageTitle);?></span><h1><?php echo oecrm_h($adminPageTitle);?></h1></div>
      </div>
      <div class="admin-topbar-actions">
        <span class="admin-company-pill" title="Active company"><i class="fa fa-building-o"></i><?php echo oecrm_h($adminCompanyName);?></span>
        <span class="admin-topbar-date"><i class="fa fa-calendar-o"></i><?php echo date('D, d M Y');?></span>
        <a class="admin-topbar-icon birthday-topbar-link" href="birthdayCenter.php" title="<?php echo $birthdayTodayCount; ?> birthday<?php echo $birthdayTodayCount===1?'':'s'; ?> today"><i class="fa fa-birthday-cake"></i><?php if($birthdayTodayCount):?><span class="topbar-notification-badge"><?php echo $birthdayTodayCount;?></span><?php endif;?></a>
        <a class="admin-topbar-icon" href="noticeCenter.php" title="Notices"><i class="fa fa-bell-o"></i></a>
        <div class="admin-profile-menu">
          <button type="button" class="admin-topbar-profile" aria-haspopup="true" aria-expanded="false" data-profile-toggle="admin">
            <img src="../admin/img/user-image.png" alt="">
            <span><?php echo oecrm_h($_SESSION['adminName']);?><small><?php echo oecrm_is_super_admin()?'Super Admin':'Administrator';?></small></span>
            <i class="fa fa-angle-down"></i>
          </button>
          <div class="admin-profile-dropdown">
            <a href="dashboard.php"><i class="fa fa-tachometer"></i> Dashboard</a>
            <a href="noticeCenter.php"><i class="fa fa-bell-o"></i> Notices</a>
            <?php if(oecrm_can($conn,'activity_audit','view')||oecrm_can($conn,'audit','view')):?><a href="audit_logs.php"><i class="fa fa-shield"></i> Activity Logs</a><?php endif;?>
            <a class="danger" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
          </div>
        </div>
      </div>
    </header>
















