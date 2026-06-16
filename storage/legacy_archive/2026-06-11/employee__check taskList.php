<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      <title>Project List</title>
      <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
      <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
      <link href="../vendor/morrisjs/morris.css" rel="stylesheet">
      <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
      <link href="../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
      <link href="../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
   </head>
   <body>
      <?php
         session_start();
         if(!isset($_SESSION['employeeId'])) {
           header('Location:index.php'); die();
         }
         ?>
      <?php include 'dbconnect.php'; ?>
      <?php 
         $employeeId = $_SESSION['employeeId'];
         $qryEmpView = "SELECT * FROM employeesTbl WHERE id=".$employeeId;
         $resultEmpView = mysqli_query($conn,$qryEmpView);
         $rowEmpView = $resultEmpView->fetch_assoc();
         ?>
      <div id="wrapper">
         <!-- Navigation -->
         <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
               <a class="navbar-brand" href="dashboard.php">Oddeven Infotech Pvt. Ltd.</a>
            </div>
            <!-- /.navbar-header -->
            <ul class="nav navbar-top-links navbar-right">
               <li>
                  <span>
                  <?php echo "Hi, ".$rowEmpView['name']; ?>
                  </span>
               </li>
               <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                  <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-user">
                     <li><a href="userinfo.php"><i class="fa fa-user fa-fw"></i> User Profile</a>
                     </li>
                     <li class="divider"></li>
                     <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                     </li>
                  </ul>
                  <!-- /.dropdown-user -->
               </li>
               <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
            <div class="navbar-default sidebar" role="navigation">
               <div class="sidebar-nav navbar-collapse">
                  <ul class="nav" id="side-menu">
                     <li>
                        <a href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                     </li>
                     <li>
                        <a href="userinfo.php"><i class="fa fa-dashboard fa-fw"></i> Update Profile</a>
                     </li>
                     <li>
                        <a href="holidays.php"><i class="fa fa-bookmark fa-fw"></i> Holiday list</a>
                     </li>
                     <li>
                        <a href="taskList.php"><i class="fa fa-tasks fa-fw"></i> Task list</a>
                     </li>
                  </ul>
               </div>
               <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
         </nav>
         <div id="page-wrapper">
            <div class="row">
               <div class="col-lg-12">
                  <h1 class="page-header">Welcome, <?php echo $rowEmpView['name']; ?> </h1>
               </div>
               <!-- /.col-lg-12 -->
            </div>
            <div class="row">
               <div class="col-lg-12">
                  <div class="panel panel-default">
                     <div class="panel-heading">
                        Projects
                     </div>
                     <!-- /.panel-heading -->
                     <div class="panel-body">
                        <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                           <div class="row">
                              <div class="col-sm-6">
                                 <div class="dataTables_length" id="dataTables-example_length">
                                 </div>
                              </div>
                              <div class="col-sm-6">
                                 <div id="dataTables-example_filter" class="dataTables_filter">
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-sm-12">
                                 <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                                    <thead>
                                       <tr role="row">
                                          <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Project Name</th>
                                          <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Details</th>
                                          <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">Assign Date</th>
                                          <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">Expected Date</th>
                                          <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">Status</th>
                                          <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">Action</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       <?php 
                                          $employeeId = $_SESSION['employeeId'];
                                          $qryTaskView = "SELECT * FROM taskTbl WHERE developerId=".$employeeId;
                                          $resultTaskView = mysqli_query($conn,$qryTaskView);
                                          if($resultTaskView->num_rows > 0){
                                              while($rowTaskView = $resultTaskView->fetch_assoc()){
                                              echo "<tr class='gradeA even' role='row'>";
                                                  $qryProView = "SELECT * FROM projectsTbl WHERE id=".$rowTaskView['projectId'];
                                                  $resultProView = mysqli_query($conn,$qryProView);
                                                  $rowProView = $resultProView->fetch_assoc();
                                                  echo "<td class='sorting_1'>".$rowProView['projectName']."</td>";
                                                  $fullTaskDetails = $rowTaskView['task_details'];
                                                  $smallTaskDetails = substr($fullTaskDetails, 0, 100);
                                                  echo "<td>".$smallTaskDetails."</td>";
                                                  echo "<td>".$rowTaskView['assignDate']."</td>";
                                                  echo "<td>".$rowTaskView['expectedDate']."</td>";
                                                  echo "<td>".$rowTaskView['status']."</td>";
                                                  echo "<td class='center' align='center'><a href='updateProject.php?updateProject=".$rowTaskView['id']."'><i class='fa fa-edit' style='font-size:22px;'></i></a>&nbsp;&nbsp;</td>";
                                              echo "</tr>";
                                              }
                                          }
                                          ?>                                  
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                     <!-- /.panel-body -->
                  </div>
                  <!-- /.panel -->
               </div>
               <!-- /.col-lg-12 -->
            </div>
         </div>
         <!-- /#page-wrapper -->
      </div>
      <!-- /#wrapper -->
      <script src="../vendor/jquery/jquery.min.js"></script>
      <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
      <script src="../vendor/metisMenu/metisMenu.min.js"></script>
      <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
      <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
      <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
      <script src="../vendor/raphael/raphael.min.js"></script>
      <script src="../vendor/morrisjs/morris.min.js"></script>
      <script src="../data/morris-data.js"></script>
      <script src="../dist/js/sb-admin-2.js"></script>
      <script>
         $(document).ready(function() {
             $('#dataTables-example').DataTable({
                 responsive: true
             });
         });
         /*$(document).ready(function() {
             $('#example').DataTable( {
                 "paging":   false,
                 "ordering": true,
                 "info":     false
             });
         });*/
         
         /*$("tr").click(function(){
            window.location = "dashboard.php";
          });*/
         /*var table = $('#example').DataTable( {
             keys: true
         } );
          
         table.keys.disable();*/
      </script>
   </body>
</html>