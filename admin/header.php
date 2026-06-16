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
  <link rel="stylesheet" type="text/css" href="../vendor/custom/customAdmin.css?v=20260613-1">
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
    'employeeProfile.php'=>'Employee Profile','attendanceReview.php'=>'Attendance Review',
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
            <li class="sidebar-user-menu"><a href="#"><span><i><img class="img-circle" src="../admin/img/user-image.png" alt=""></i><?php echo "Hi, ".$_SESSION['adminName']; ?></span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li></ul></li>

            <?php if (oecrm_is_super_admin()) { $companyOptions=mysqli_query($conn,"SELECT id,display_name FROM companies WHERE status=1 ORDER BY display_name");$currentCompanyId=oecrm_current_company_id($conn); ?>
            <li class="erp-company-context"><form method="post" action="switch_company.php"><?php echo oecrm_csrf_field();?><label><i class="fa fa-building"></i> Active Company</label><select name="company_id" onchange="this.form.submit()" title="Active company"><?php while($companyOption=mysqli_fetch_assoc($companyOptions)):?><option value="<?php echo (int)$companyOption['id'];?>" <?php echo $currentCompanyId===(int)$companyOption['id']?'selected':'';?>><?php echo oecrm_h($companyOption['display_name']);?></option><?php endwhile;?></select></form></li>
            <?php } ?>

            <li><a href="dashboard.php" class="<?php echo (isset($active_menu)&&$active_menu==='dashboard')?'active':'';?>"><i class="fa fa-tachometer fa-fw"></i><span>Dashboard</span></a></li>

            <li class="erp-nav-label">Operations</li>
            <?php if(check_is_access_new('lead')==1):?>
            <li><a href="#" class="<?php echo (isset($active_menu)&&$active_menu==='lead')?'active':'';?>"><i class="fa fa-handshake-o fa-fw"></i><span>CRM & Sales</span><span class="fa arrow"></span></a><ul class="nav nav-second-level">
              <?php if(oecrm_can($conn,'clients','view')):?><li><a href="clients.php"><i class="fa fa-building-o fa-fw"></i> Client Directory</a></li><?php endif;?>
              <?php if(check_is_access_new('add_lead')==1):?><li><a href="add_lead.php"><i class="fa fa-user-plus fa-fw"></i> Add Lead</a></li><?php endif;?>
              <?php if(check_is_access_new('view_lead')==1):?><li><a href="leads.php"><i class="fa fa-address-book-o fa-fw"></i> Lead Pipeline</a></li><li><a href="followup.php"><i class="fa fa-calendar-check-o fa-fw"></i> Follow-ups</a></li><li><a href="manageLeadSource.php"><i class="fa fa-code-fork fa-fw"></i> Lead Sources</a></li><li><a href="manageFollowupType.php"><i class="fa fa-tags fa-fw"></i> Follow-up Types</a></li><?php endif;?>
            </ul></li>
            <?php endif;?>

            <?php if(check_is_access_new('project')==1||check_is_access_new('lead')==1):?>
            <li><a href="#"><i class="fa fa-briefcase fa-fw"></i><span>Projects & Tasks</span><span class="fa arrow"></span></a><ul class="nav nav-second-level">
              <?php if(check_is_access_new('project')==1||oecrm_can($conn,'projects','view')):?><li><a href="projectWorkspace.php"><i class="fa fa-folder-open fa-fw"></i> Project Portfolio</a></li><li><a href="projectEditor.php"><i class="fa fa-plus-square fa-fw"></i> New Project</a></li><?php endif;?>
              <?php if(check_is_access_new('lead')==1):?><li><a href="viewTask.php"><i class="fa fa-tasks fa-fw"></i> Task Management</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'timesheets','view')):?><li><a href="timesheets.php"><i class="fa fa-clock-o fa-fw"></i> Timesheets</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'project_sprints','view')):?><li><a href="projectKanban.php"><i class="fa fa-columns fa-fw"></i> Kanban & Sprints</a></li><?php endif;?>
            </ul></li>
            <?php endif;?>

            <li class="erp-nav-label">People & HR</li>
            <?php if(check_is_access_new('employee')==1||oecrm_can($conn,'employees','view')):?>
            <li><a href="#" class="<?php echo (isset($active_menu)&&$active_menu==='employee')?'active':'';?>"><i class="fa fa-users fa-fw"></i><span>Employee Management</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a href="manageEmployee.php"><i class="fa fa-address-card-o fa-fw"></i> Employee Directory</a></li><li><a href="addEmployee.php"><i class="fa fa-user-plus fa-fw"></i> Add Employee</a></li><li><a href="manageDesignation.php"><i class="fa fa-id-badge fa-fw"></i> Designations</a></li><li><a href="departments.php"><i class="fa fa-sitemap fa-fw"></i> Departments</a></li></ul></li>
            <?php endif;?>

            <li><a href="#" class="<?php echo in_array($active_menu??'',['attendance','employee_leave'],true)?'active':'';?>"><i class="fa fa-clock-o fa-fw"></i><span>Attendance & Leave</span><span class="fa arrow"></span></a><ul class="nav nav-second-level">
              <?php if(oecrm_can($conn,'shifts','view')):?><li><a href="shifts.php"><i class="fa fa-clock-o fa-fw"></i> Shift Master</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'shift_assignments','view')):?><li><a href="activeShiftAssignments.php"><i class="fa fa-random fa-fw"></i> Shift Assignments</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'attendance_review','view')):?><li><a href="attendanceReview.php"><i class="fa fa-check-square-o fa-fw"></i> Review & Exceptions</a></li><?php endif;?>
              <?php if(check_is_access_new('leave')==1||oecrm_can($conn,'leave_requests','view')):?><li><a href="manageLeave.php"><i class="fa fa-calendar-minus-o fa-fw"></i> Leave Approvals</a></li><li><a href="leavePolicies.php"><i class="fa fa-sliders fa-fw"></i> Leave & Comp-Off Rules</a></li><?php endif;?>
            </ul></li>

            <?php if(oecrm_can($conn,'hr_letters','view')||oecrm_can($conn,'hr_letter_templates','view')):?><li><a href="#" class="<?php echo (isset($active_menu)&&$active_menu==='hr_letters')?'active':'';?>"><i class="fa fa-file-text-o fa-fw"></i><span>HR Documents</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><?php if(oecrm_can($conn,'hr_letters','view')):?><li><a href="hrLetters.php"><i class="fa fa-envelope-open-o fa-fw"></i> Letters & History</a></li><?php endif;?><?php if(oecrm_can($conn,'hr_letter_templates','view')):?><li><a href="hrLetterTemplates.php"><i class="fa fa-files-o fa-fw"></i> Letter Templates</a></li><?php endif;?></ul></li><?php endif;?>
            <?php if(oecrm_can($conn,'notices','view')):?><li><a href="noticeCenter.php" class="<?php echo (isset($active_menu)&&$active_menu==='notices')?'active':'';?>"><i class="fa fa-bullhorn fa-fw"></i><span>Notices & Policies</span></a></li><?php endif;?>
            <?php if(oecrm_can($conn,'employee_exit','view')):?><li><a href="employeeExits.php" class="<?php echo ($active_menu??'')==='employee_exit'?'active':'';?>"><i class="fa fa-sign-out fa-fw"></i><span>Employee Exit & F&amp;F</span></a></li><?php endif;?>

            <?php if(oecrm_can($conn,'payroll','view')):?><li><a href="#" class="<?php echo (isset($active_menu)&&$active_menu==='payroll')?'active':'';?>"><i class="fa fa-money fa-fw"></i><span>Payroll</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a href="payrollManagement.php"><i class="fa fa-calculator fa-fw"></i> Payroll Runs & Reports</a></li><li><a href="salaryPolicies.php"><i class="fa fa-list-alt fa-fw"></i> Salary Policies</a></li></ul></li><?php endif;?>

            <li class="erp-nav-label">Finance & Resources</li>
            <?php if(oecrm_can($conn,'resources','view')):?><li><a href="#" class="<?php echo ($active_menu??'')==='resources'?'active':'';?>"><i class="fa fa-random fa-fw"></i><span>Resource Management</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a href="resources.php"><i class="fa fa-users fa-fw"></i> Allocations & Bench</a></li><li><a href="resourceAllocation.php"><i class="fa fa-user-plus fa-fw"></i> New Allocation</a></li></ul></li><?php endif;?>
            <?php if(oecrm_can($conn,'assets','view')):?><li><a href="#" class="<?php echo ($active_menu??'')==='assets'?'active':'';?>"><i class="fa fa-laptop fa-fw"></i><span>Asset Management</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a href="assets.php"><i class="fa fa-list fa-fw"></i> Asset Register</a></li><li><a href="assetEditor.php"><i class="fa fa-plus-circle fa-fw"></i> Add Asset</a></li></ul></li><?php endif;?>
            <?php if(oecrm_can($conn,'access_management','view')):?><li><a href="#" class="<?php echo ($active_menu??'')==='access_management'?'active':'';?>"><i class="fa fa-key fa-fw"></i><span>Access Management</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a href="accessManagement.php"><i class="fa fa-shield fa-fw"></i> Access Register</a></li><li><a href="accessAccount.php"><i class="fa fa-plus-circle fa-fw"></i> Add Account</a></li></ul></li><?php endif;?>
            <li><a href="#" class="<?php echo ($active_menu??'')==='finance'?'active':'';?>"><i class="fa fa-line-chart fa-fw"></i><span>Finance & Accounting</span><span class="fa arrow"></span></a><ul class="nav nav-second-level">
              <?php if(oecrm_can($conn,'finance','view')):?><li><a href="finance.php"><i class="fa fa-dashboard fa-fw"></i> Finance Overview</a></li><li><a href="invoiceEditor.php"><i class="fa fa-file-text fa-fw"></i> New Invoice</a></li><li><a href="expenseEditor.php"><i class="fa fa-credit-card fa-fw"></i> Add Expense</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'gst','view')):?><li><a href="gstReport.php"><i class="fa fa-percent fa-fw"></i> GST Reports</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'bank_reconciliation','view')):?><li><a href="bankReconciliation.php"><i class="fa fa-balance-scale fa-fw"></i> Cash Flow & Reconciliation</a></li><?php endif;?>
              <?php if(check_is_access_new('invocie')==1):?><li><a href="addaccount.php"><i class="fa fa-university fa-fw"></i> Bank Accounts</a></li><?php endif;?>
              <?php if(check_is_access_new('lead')==1):?><li><a href="addInvoice.php"><i class="fa fa-file-text-o fa-fw"></i> Invoices</a></li><?php endif;?>
              <?php if(check_is_access_new('deposit')==1):?><li><a href="addDeposit.php"><i class="fa fa-arrow-circle-down fa-fw"></i> Collections & Deposits</a></li><?php endif;?>
              <?php if(check_is_access_new('expense')==1):?><li><a href="viewexpense.php"><i class="fa fa-credit-card fa-fw"></i> Expenses</a></li><?php endif;?>
              <?php if(check_is_access_new('project_expense')==1):?><li><a href="projectExpense.php"><i class="fa fa-pie-chart fa-fw"></i> Project Expenses</a></li><?php endif;?>
              <li><a href="manageCurrency.php"><i class="fa fa-exchange fa-fw"></i> Currencies</a></li><li><a href="manageProfessionalTax.php"><i class="fa fa-percent fa-fw"></i> Professional Tax</a></li><li><a href="addBankDetails.php"><i class="fa fa-bank fa-fw"></i> Bank Masters</a></li>
            </ul></li>

            <?php if(check_is_access_new('domain_hosting')==1||oecrm_can($conn,'subscriptions','view')):?><li><a href="#" class="<?php echo ($active_menu??'')==='digital_assets'?'active':'';?>"><i class="fa fa-cloud fa-fw"></i><span>Digital Assets</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><?php if(oecrm_can($conn,'subscriptions','view')):?><li><a href="subscriptions.php"><i class="fa fa-refresh fa-fw"></i> Subscriptions & Vault</a></li><?php endif;?><li><a href="viewDomainHosting.php"><i class="fa fa-server fa-fw"></i> Legacy Domain Register</a></li><li><a href="expiring_hostings.php"><i class="fa fa-hourglass-half fa-fw"></i> Renewals & Expiry</a></li></ul></li><?php endif;?>

            <?php if(oecrm_can($conn,'dashboards','view')):?><li><a href="#" class="<?php echo ($active_menu??'')==='analytics'?'active':'';?>"><i class="fa fa-bar-chart fa-fw"></i><span>Analytics</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a href="erpAnalytics.php"><i class="fa fa-dashboard fa-fw"></i> Management Dashboard</a></li><li><a href="workforceReports.php"><i class="fa fa-line-chart fa-fw"></i> Workforce & Costing</a></li></ul></li><?php endif;?>

            <li class="erp-nav-label">System</li>
            <li><a href="#" class="<?php echo in_array($active_menu??'',['foundation','activity_audit','role','setting'],true)?'active':'';?>"><i class="fa fa-cogs fa-fw"></i><span>Administration</span><span class="fa arrow"></span></a><ul class="nav nav-second-level">
              <?php if(oecrm_can($conn,'companies','view')):?><li><a href="companies.php"><i class="fa fa-building fa-fw"></i> Companies</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'roles','view')||check_is_access_new('employee')==1):?><li><a href="all_user_roles.php"><i class="fa fa-user-secret fa-fw"></i> Roles</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'roles','manage_permissions')):?><li><a href="role_permissions.php"><i class="fa fa-key fa-fw"></i> Permissions</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'activity_audit','view')||oecrm_can($conn,'audit','view')):?><li><a href="audit_logs.php"><i class="fa fa-shield fa-fw"></i> Activity & Audit</a></li><?php endif;?>
              <?php if(oecrm_can($conn,'backups','view')):?><li><a href="databaseMaintenance.php"><i class="fa fa-database fa-fw"></i> Database Backups</a></li><?php endif;?>
              <?php if(check_is_access_new('settings')==1):?><li><a href="manageHoliday.php"><i class="fa fa-calendar fa-fw"></i> Holiday Calendar</a></li><li><a href="manageLeaveType.php"><i class="fa fa-calendar-plus-o fa-fw"></i> Leave Types</a></li><li><a href="addcountry.php"><i class="fa fa-flag fa-fw"></i> Countries</a></li><li><a href="manageEmployeeLoginLog.php"><i class="fa fa-sign-in fa-fw"></i> Legacy Login Log</a></li><?php endif;?>
            </ul></li>
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
        <span class="admin-topbar-date"><i class="fa fa-calendar-o"></i><?php echo date('D, d M Y');?></span>
        <a class="admin-topbar-icon birthday-topbar-link" href="birthdayCenter.php" title="<?php echo $birthdayTodayCount; ?> birthday<?php echo $birthdayTodayCount===1?'':'s'; ?> today"><i class="fa fa-birthday-cake"></i><?php if($birthdayTodayCount):?><span class="topbar-notification-badge"><?php echo $birthdayTodayCount;?></span><?php endif;?></a>
        <a class="admin-topbar-icon" href="noticeCenter.php" title="Notices"><i class="fa fa-bell-o"></i></a>
        <a class="admin-topbar-profile" href="logout.php"><img src="../admin/img/user-image.png" alt=""><span><?php echo oecrm_h($_SESSION['adminName']);?><small><?php echo oecrm_is_super_admin()?'Super Admin':'Administrator';?></small></span></a>
      </div>
    </header>










