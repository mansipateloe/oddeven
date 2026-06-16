<?php include 'header.php'; ?>
<?php 
    $id = $_GET['view'];
    $qryView = "SELECT * FROM projectsTbl WHERE id=".$id;
    $resultView = mysqli_query($conn,$qryView);
    $rowView = $resultView->fetch_assoc();
    $developerIds = json_decode($rowView['developerId']);
    foreach($developerIds as $developerId){
        $qryEmployee = "SELECT * FROM employeesTbl where id=".$developerId;
        $resultEmployee = mysqli_query($conn,$qryEmployee);
        $employee = $resultEmployee->fetch_assoc();
    }
?>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg .tg-0lax{text-align:left;vertical-align:top}
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    View Project Detail
                    <a href="editProject.php?edit=<?php echo $rowView['id']; ?>" class="btn btn-primary">Edit Project</a>
                   </div>
                <div>
                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                        <div class="row">
                        <div class="col-sm-12">           
                            <table class="tg table table-bordered">
                                <tbody>
                                  <tr>
                                    <th class="tg-0lax">Project Name</th>
                                    <td class="tg-0lax" colspan="3"><?php echo  $rowView['projectName']; ?></td>
                                  </tr>
                                  <tr>
                                    <th class="tg-0lax">Client Name</td>
                                    <td class="tg-0lax"><?php echo  $rowView['customerName']; ?></td>
                                    <th class="tg-0lax">Nick Name</td>
                                    <td class="tg-0lax"><?php echo  $rowView['nickName']; ?></td>
                                    <!-- <th class="tg-0lax">Status</td>
                                    <td class="tg-0lax"><?php echo  $rowView['status']; ?></td> -->
                                  </tr>
                                  <tr>
                                    <th class="tg-0lax">Email</td>
                                    <td class="tg-0lax"><?php echo  $rowView['email']; ?></td>
                                    <th class="tg-0lax">Contact</td>
                                    <td class="tg-0lax"><?php echo  $rowView['phone']; ?></td>
                                  </tr>
                                  <tr>
                                    <th class="tg-0lax">Platform</th>
                                    <td class="tg-0lax">
                                        <?php 
                                            $projectplatformId = $rowView['platform'];
                                            $qryPlatformType = "SELECT name FROM projectplatform WHERE id = $projectplatformId";
                                            $resultPlatformType = mysqli_query($conn, $qryPlatformType);
                                            $rowPlatformType = $resultPlatformType->fetch_assoc();
                                            echo $rowPlatformType['name'];
                                            // print_r($rowView);
                                        ?>
                                    </td>
                                   
                                    <th class="tg-0lax">ProjectType</th>
                                    <td class="tg-0lax">
                                        <?php 
                                            $projectTypeId = $rowView['projectType'];
                                            $qryProjectType = "SELECT name FROM projecttype WHERE id = $projectTypeId";
                                            $resultProjectType = mysqli_query($conn, $qryProjectType);
                                            $rowProjectType = $resultProjectType->fetch_assoc();
                                            echo $rowProjectType['name'];
                                        ?>
                                    </td>
                                    <!-- <th class="tg-0lax">ProjectType</td>
                                    <td class="tg-0lax"><?php echo  $rowView['projectType']; ?></td> -->
                                  </tr>
                                  <tr>
                                    <th class="tg-0lax">Start Date</td>
                                    <td class="tg-0lax"><?php echo  $rowView['startdate']; ?></td>
                                    <th class="tg-0lax">End Date</td>
                                    <td class="tg-0lax"><?php echo  $rowView['enddate']; ?></td>
                                  </tr>
                                  
                                  <tr>
                                    <th class="tg-0lax">Technology</td>
                                    <td class="tg-0lax"></td>
                                    <th class="tg-0lax">Assign To</td>
                                    <td class="tg-0lax"><?php echo  $employee['name']; ?></td>
                                  </tr>
                                  <tr>
                                    <th class="tg-0lax">Status</td>
                                    <td class="tg-0lax"><?php echo  $rowView['status']; ?></td>
                                    <th class="tg-0lax">Total hours</td>
                                    <td class="tg-0lax" colspan="3"></td>
                                    
                                  </tr>
                                  </tbody>
                            </table>
                            <br>
                            <div class="item list">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#itemlist">Account</a></li>
                                    <li><a data-toggle="tab" href="#additem">Work</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="itemlist" class="tab-pane fade in active">
                                        <table class="table table-bordered account_table">
                                        <thead>
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Account Name</th>
                                                <th>Invoice</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $i = 1;
                                                $toAmount = 0;
                                                $projectAmount = 0;
                                                $serviceFee = 0;
                                                $projectIncome = 0;

                                                $all_data = array();

                                                $deposits_qry = "select deposit.*, deposit_date as per_date from deposit where project_Id=".$rowView['id'];
                                                $deposits_result = mysqli_query($conn, $deposits_qry);
                                                if($deposits_result->num_rows > 0){
                                                    while($deposit_row = $deposits_result->fetch_assoc()){
                                                        $deposit_row['type'] = "Deposit";
                                                        $all_data[] = $deposit_row; 
                                                    }
                                                }

                                                $expenses_qry = "select project_expenses.*, expensedate as per_date from project_expenses where project_Id=".$rowView['id'];
                                                $expenses_result = mysqli_query($conn, $expenses_qry);
                                                if($expenses_result->num_rows > 0){
                                                    while($expense_row = $expenses_result->fetch_assoc()){
                                                        $expense_row['type'] = "Expense";
                                                        $all_data[] = $expense_row; 
                                                    }
                                                }

                                                array_multisort(array_map('strtotime',array_column($all_data,'per_date')),SORT_DESC, $all_data);

                                                $total_deposit = 0;
                                                $total_expense = 0;

                                                if(count($all_data) > 0){
                                                    foreach ($all_data as $detail_data) {
                                                        echo "<tr class='gradeA even' role='row'>";
                                                        echo "<td>".$i."</td>";
                                                        $new_per_date = new DateTime($detail_data['per_date']);
                                                        echo "<td>".$new_per_date->format('d-m-Y')."</td>";
                                                        echo "<td>".$detail_data['type']."</td>";

                                                        $qryAccount = "SELECT * FROM account where account_id=".$detail_data['account_Id'];
                                                        $resultAccount = mysqli_query($conn,$qryAccount);
                                                        $rowAccount = $resultAccount->fetch_assoc();
                                                        echo "<td>".$rowAccount['account_name']."</td>";
                                                        
                                                        if($detail_data['type'] == "Deposit"){
                                                            $total_deposit += $detail_data['amount'];
                                                            echo '<td><a target="_blank" href="viewInvoice.php?id='.$detail_data["invoice_id"].'" style="display: inline-block;width: 28px;" title="View">'.$detail_data["invoice_id"].'</a></td>';
                                                        }elseif($detail_data['type'] == "Expense"){
                                                            $total_expense += $detail_data['amount'];
                                                            echo '<td>'.$detail_data["expense_title"].'</td>';
                                                        }
                                                        
                                                        
                                                        echo '<td>'.$detail_data['amount'] .'</td>';
                                                        echo "</tr>"; 
                                                        $i++;
                                                    }
                                                }
                                            ?>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Project Budget : </td>
                                                        <td><?php echo $rowView['amount'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Total Deposit : </td>
                                                        <td><?php echo $total_deposit ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Pending Payment : </td>
                                                        <td><?php if(!isset($rowView['amount']) || (isset($rowView['amount']) && $rowView['amount']=="")){$rowView['amount']=0;}  echo $rowView['amount'] - $total_deposit ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Total Expense : </td>
                                                        <td><?php echo $total_expense ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Project Income : </td>
                                                        <td><?php echo $rowView['amount'] - $total_expense ?></td>
                                                    </tr>
                                        </tbody>
                                        </table>
                                    </div>
                                    <div id="additem" class="tab-pane fade">
                                        <table class="table table-bordered work_table">
                                            <thead>
                                                <tr>
                                                    <th>Sr No.</th>
                                                    <th>Work Date</th>
                                                    <th>Assign To</th>
                                                    <th>Detail</th>
                                                    <th>Work Hour</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>    
                                            <tbody>
                                                <?php
                                                   
                                                // Array
                                                //     (
                                                //         [id] =&gt; 845
                                                //         [taskId] =&gt; 0
                                                //         [projectId] =&gt; 73
                                                //         [developerId] =&gt; 4
                                                //         [startTaskDate] =&gt; 2019-10-15
                                                //         [startTaskTime] =&gt; 09:47
                                                //         [endTaskTime] =&gt; 11:13
                                                //         [worklog] =&gt; testig by lipsa
                                                //         [totalTime] =&gt; 1:26
                                                //         [adminResponse] =&gt; 
                                                //     )
                                                //     /opt/lampp/htdocs/attendance/admin/detailproject.php--&gt;--Line--&gt;189

                                                    $i = 1;
                                                    $workDetails = "select * from taskhoursTbl where projectId=".$rowView['id'];
                                                    $workResult = mysqli_query($conn, $workDetails);
                                                    if($workResult->num_rows > 0){
                                                        while($rowWork = $workResult->fetch_assoc()){
                                                            if(!empty($rowWork['totalTime'])){
                                                                echo "<tr class='gradeA even' role='row'>";
                                                                echo "<td>".$i."</td>";
                                                                $workdate = $rowWork['startTaskDate'];
                                                                $endTime = date('d-m-Y', strtotime($workdate));
                                                                echo "<td>".$endTime."</td>";
                                                                $qryDev = "select * from employeesTbl where id=".$rowWork['developerId'];
                                                                $resultDev = mysqli_query($conn,$qryDev);
                                                                $rowresultDev = mysqli_fetch_assoc($resultDev);
                                                                echo "<td>".$rowresultDev['name']."</td>";
                                                                echo "<td>".$rowWork['worklog']."</td>";
                                                                echo "<td>".$rowWork['totalTime']."</td>";
                                                                echo "<td>"."Completed"."</td>";
                                                                echo '<td>
                                                                    <a href="#"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;
                                                                    <a href="editTask.php?edit='.$rowWork["id"].'"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                                                                    <a href="deleteProjectWork.php?delete='.$rowWork["id"].'" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o"></i></a>
                                                                </td>';
                                                                echo "</tr>";
                                                                $i++;
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>



                        </div>
                        </div>
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
        </div>
    </div>
</div>

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
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTables-example').DataTable({
                responsive: true,
                "paging":   false,
                "ordering": false,
                "info":     false,
                "searching": false
                /*"pageLength": 50*/
            });
        });
    </script>
    <script type="text/javascript">
        function checkprojectName(){
            var project=document.getElementById( "projectName" ).value;
            if(project){
                $.ajax({
                    type:'post',
                    url:'checkAvailibility.php',
                    data: {
                        projectName:project,
                    },
                    success: function (response){
                        /*$("#loaderIcon").hide();*/
                        $('#projectName_status').html(response);
                        if(response=="Projectname Available"){
                            $('#projectName_status').css("color", "green");
                            return true;
                        }else{
                            $('#projectName_status').css("color", "red");
                            return false;
                        }
                    }
                });
            }else{
                $('#projectName_status').html("");
                return false;
            }
        }
        function checkall(){
            var projectNamehtml=document.getElementById("projectName_status").innerHTML;

            if(projectNamehtml=="Projectname Available"){
                return true;
            }else{
                return false;
            }
        }
    </script>

<?php include 'footer.php'; ?>