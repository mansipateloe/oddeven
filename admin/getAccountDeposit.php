<?php
    require_once __DIR__ . '/dbconnect.php';
    require_once __DIR__ . '/../security.php';
    require_once __DIR__ . '/../foundation.php';
    oecrm_require_admin_login();
    oecrm_require_permission($conn, 'finance', 'view');
    //$projectId = $_GET['projectId'];
    $accountId = oecrm_int_param($_GET, 'accountId');
    //echo '<pre>'; print_r($projectId);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
    $i = 1;
    $amounts = 0;
    if($accountId){
        $displayProject = "select * from deposit where account_Id = $accountId ORDER BY deposit_date ASC" ;
        $getdeposit = mysqli_query($conn, $displayProject);
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
?>
 <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>Total : <?php echo $amounts ?></td>
    <td></td>
</tr>
