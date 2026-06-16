<?php
include 'header.php';
require_once __DIR__.'/../foundation.php';

$employeeId = (int)$_SESSION['employeeId'];
$stmt = mysqli_prepare($conn, 'SELECT l.*,e.name employee_name FROM login_details l JOIN employeestbl e ON e.id=l.user_id WHERE l.user_id=? ORDER BY l.login_datetime DESC,l.id DESC');
mysqli_stmt_bind_param($stmt, 'i', $employeeId);
mysqli_stmt_execute($stmt);
$logs = mysqli_stmt_get_result($stmt);
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="panel panel-default">
        <div class="panel-heading">My Login History</div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Date & Time</th><th>Employee</th><th>IP Address</th><th>Browser</th><th>Browser Type</th><th>Device</th><th>Platform</th></tr></thead>
                <tbody>
                <?php if(mysqli_num_rows($logs)===0):?><tr><td colspan="7">No login history found.</td></tr><?php endif;?>
                <?php while($log=mysqli_fetch_assoc($logs)):
                    $browserType=trim((string)$log['browser_type'],'"');
                    $deviceType=trim((string)$log['device_type'],'"');
                    $platform=(string)$log['mobile_browser']==='1'?'Mobile':'PC';
                ?>
                    <tr>
                        <td><?php echo oecrm_h(date('d M Y h:i A',strtotime($log['login_datetime'])));?></td>
                        <td><?php echo oecrm_h($log['employee_name']);?></td>
                        <td><?php echo oecrm_h($log['ip_address']);?></td>
                        <td><?php echo oecrm_h($log['browser_details']?:'-');?></td>
                        <td><?php echo oecrm_h($browserType?:'-');?></td>
                        <td><?php echo oecrm_h($deviceType?:'-');?></td>
                        <td><?php echo $platform;?></td>
                    </tr>
                <?php endwhile;mysqli_stmt_close($stmt);?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
