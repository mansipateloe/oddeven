<?php
    require_once __DIR__ . '/dbconnect.php';
    require_once __DIR__ . '/../security.php';
    require_once __DIR__ . '/../foundation.php';
    oecrm_require_admin_login();
    oecrm_require_permission($conn, 'finance', 'view');
    $projectId = isset($_GET['projectId']) && $_GET['projectId'] !== '' ? oecrm_int_param($_GET, 'projectId') : 0;
    $accountId = isset($_GET['accountId']) && $_GET['accountId'] !== '' ? oecrm_int_param($_GET, 'accountId') : 0;
    $startdateId = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['startdateId'] ?? '') ? $_GET['startdateId'] : '';
    $enddateId = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['enddateId'] ?? '') ? $_GET['enddateId'] : '';
    if ($startdateId === '' || $enddateId === '') { http_response_code(400); exit('Invalid date range.'); }
    //echo '<pre>'; print_r($accountId);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    $i = 1;
    $amounts = 0;
    if($projectId != '' && $accountId == ''){
        $dateReportQry = "select * from deposit where deposit_date BETWEEN '$startdateId' and '$enddateId' and project_Id = $projectId ORDER BY deposit_date ASC";
        $getdeposit = mysqli_query($conn, $dateReportQry);
        $getdepositRes = mysqli_num_rows($getdeposit);
        if($getdepositRes == 0){
                echo '0';
        }else{
             echo ' <thead>
             <tr role="row">
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Sr No.</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Date</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Account Name</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Project Name</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Amount</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 10%; text-align:center;">Action</th>
             </tr>
         </thead>
         <tbody>';
            while($getReportRes = mysqli_fetch_assoc($getdeposit)){
                echo "<tr class='gradeA even' role='row'>";
                    echo "<td>".$i."</td>";
                    echo "<td>".$getReportRes['deposit_date']."</td>";
                    $qryAccount = "SELECT * FROM account where account_id=".$getReportRes['account_Id'];
                    $resultAccount = mysqli_query($conn,$qryAccount);
                    $rowAccount = $resultAccount->fetch_assoc();
                    echo "<td>".$rowAccount['account_name']."</td>";
                    $qryProject = "SELECT * FROM projectsTbl where id=".$getReportRes['project_Id'];
                    $resultProject = mysqli_query($conn,$qryProject);
                    $resProject = $resultProject->fetch_assoc();
                    echo "<td>".$resProject['projectName']."</td>";
                    echo "<td>".$getReportRes['amount']."</td>";
                    echo '<td class="center" align="center">

                    <a href="editDeposit.php?edit='.$getReportRes["deposit_id"].'" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;

                    <a href="deleteDeposit.php?delete='.$getReportRes["deposit_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a>
                   
                    </td>';
                echo "</tr>";
                $i++;
                $amounts += $getReportRes['amount'];
            }
        }  
    }

    else if($accountId){
        //echo '<pre>'; print_r("dsfd");die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
        $dateReportQry = "select * from deposit where deposit_date BETWEEN '$startdateId' and '$enddateId' and account_Id = $accountId ORDER BY deposit_date ASC";
        $getdeposit = mysqli_query($conn, $dateReportQry);
        $getdepositRes = mysqli_num_rows($getdeposit);
        if($getdepositRes == 0){
                echo '0';
        }else{
             echo ' <thead>
             <tr role="row">
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Sr No.</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Date</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Account Name</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Project Name</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10%;">Amount</th>
                 <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 10%; text-align:center;">Action</th>
             </tr>
         </thead>
         <tbody>';
            while($getReportRes = mysqli_fetch_assoc($getdeposit)){
                echo "<tr class='gradeA even' role='row'>";
                    echo "<td>".$i."</td>";
                    echo "<td>".$getReportRes['deposit_date']."</td>";
                    $qryAccount = "SELECT * FROM account where account_id=".$getReportRes['account_Id'];
                    $resultAccount = mysqli_query($conn,$qryAccount);
                    $rowAccount = $resultAccount->fetch_assoc();
                    echo "<td>".$rowAccount['account_name']."</td>";
                    $qryProject = "SELECT * FROM projectsTbl where id=".$getReportRes['project_Id'];
                    $resultProject = mysqli_query($conn,$qryProject);
                    $resProject = $resultProject->fetch_assoc();
                    echo "<td>".$resProject['projectName']."</td>";
                    echo "<td>".$getReportRes['amount']."</td>";
                    echo '<td class="center" align="center">

                    <a href="editDeposit.php?edit='.$getReportRes["deposit_id"].'" style="display: inline-block;width: 28px;"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;

                    <a href="deleteDeposit.php?delete='.$getReportRes["deposit_id"].'" onclick="return confirm(\'Are you sure you want to delete?\');" style="display: inline-block;width: 28px;"><i class="fa fa-trash-o"></i></a>
                   
                    </td>';
                echo "</tr>";
                $i++;
                $amounts += $getReportRes['amount'];
            }
        } 
        //echo '<pre>'; print_r($dateReportQry);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    }
?>
 <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>Total : <?php echo $amounts ?></td>
    <td></td>
</tr>
