<?php

include 'header.php';
include "functions.php";
?>
<?php

$id = $_GET['edit'];

$qryView = "SELECT * FROM workhoursTbl WHERE id=$id";

$resultView = mysqli_query($conn, $qryView);

$rowView = $resultView->fetch_assoc();
if($rowView['breakinTime']=="" || $rowView['breakinTime']==null)
{
    $rowView['breakinTime']='["00:00"]';
}
if($rowView['breakoutTime']=="" || $rowView['breakoutTime']==null)
{
    $rowView['breakoutTime']='["00:00"]';
}

$breakinTime_arr=json_decode($rowView['breakinTime'],true);
$breakoutTime_arr=json_decode($rowView['breakoutTime'],true);
//echo $rowView->breakinTime;
// print_r($rowView);
// $queryViewlead = "SELECT * FROM lead_followup WHERE id=" . $id;
// $resultViewlead = mysqli_query($conn, $qryView);
// $rowViewlead = $resultView->fetch_assoc();

?>

<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading panel-box">
            <h4>Edit Report</h4>
        </div>
        <div class="panel-body manage_project">
        
        <!-- <form id="reportForm" action="action_update_lead.php" method="post" onsubmit="return checkall();" enctype="multipart/form-data"> -->
            <form id="reportForm" action="action_update_report.php" method="post"  enctype="multipart/form-data">
            <input type="hidden" name="employeeId" value="<?php echo $rowView['employeeId']; ?>">
            <input type="hidden" name="id" value="<?= $id ?>">
            <!-- <input type="hidden" name="lead_id" value="<?php echo $rowView['lead_id'] ?>"> -->
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                                <label for="workDate" class="form-label">workDate</label>
                                                <input type="workDate" class="form-control" id="workDate" name="workDate" value="<?php echo $rowView['workDate'] ?>" >
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="signinTime" class="form-label">Sign In</label>
                                                <input type="time" class="form-control" id="signinTime" name="signinTime" value="<?php echo SetTimeFormatforEdit($rowView['signinTime']) ?>" >
                                            </div>
                                    </div>
                                    <!-- Next Followup Time -->
                                    <!-- <div class="form-group col-md-4">
                                        <label for="signinTime" class="form-label">Sign In</label>
                                        <input type="time" class="form-control" id="signinTime" name="signinTime" >
                                    </div> -->
                                    <div class="form-group col-md-4">
                                        <label for="lunchinTime" class="form-label">Lunch In</label>
                                        <input type="time" class="form-control" id="lunchinTime" name="lunchinTime" value="<?php echo SetTimeFormatforEdit($rowView['lunchinTime']) ?>" >
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="lunchoutTime" class="form-label">Lunch Out</label>
                                        <input type="time" class="form-control" id="lunchoutTime" name="lunchoutTime" value="<?php echo SetTimeFormatforEdit($rowView['lunchoutTime']) ?>" >
                                    </div>
                                            <?php
                                                    for($i=0;$i<2;$i++){
                                                        if(isset($breakinTime_arr[$i]))
                                                        {
                                                            $currentBreakinTime= SetTimeFormatforEdit($breakinTime_arr[$i]);
                                                        }else
                                                        {
                                                            $currentBreakinTime= "";
                                                        }
                                                    
                                                    ?>
                                                    <div class="form-group col-md-4">
                                                            <label for="breakinTime" class="form-label">Break In (<?php echo $i+1; ?>)</label>
                                                            <input type="time" class="form-control" id="breakinTime<?php echo $i+1; ?>" name="breakinTime[]" value="<?php echo $currentBreakinTime;?>" >
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                            <?php
                                            // for($i=0;$i<count($breakoutTime_arr);$i++)
                                            for($i=0;$i<2;$i++)
                                            {
                                                if(isset($breakoutTime_arr[$i]))
                                                {
                                                    $currentBreakoutTime= SetTimeFormatforEdit($breakoutTime_arr[$i]);
                                                }else
                                                {
                                                    $currentBreakoutTime= "";
                                                }
                                            
                                            ?>
                                                <div class="form-group col-md-4">
                                                    <label for="breakoutTime" class="form-label">Break Out <?php echo $i+1; ?></label>
                                                    <input type="time" class="form-control" id="breakoutTime<?php echo $i+1; ?>" name="breakoutTime[]" value="<?php echo $currentBreakoutTime; ?>" >
                                                </div>
                                            <?php
                                            }
                                            ?>
                                    <div class="form-group col-md-4">
                                        <label for="signoutTime" class="form-label">Sign Out</label>
                                        <input type="time" class="form-control" id="signoutTime" name="signoutTime" value="<?php echo SetTimeFormatforEdit($rowView['signoutTime']) ?>" >
                                    </div>
                                    <div class="form-group col-md-4" style="display:none;">
                                        <label for="workHours" class="form-label">Work Hours</label>
                                        <input type="text" readonly class="form-control" id="workHours" name="workHours" value="<?php echo $rowView['workHours'] ?>" >
                                    </div>
                                    <input type="submit" value="Update" name="ReportSave" class="btn btn-primary">

                                </form>
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
<!-- Custom JavaScript for form validation -->
<!-- Custom JavaScript for form validation -->
<!-- <script>
    // Example of basic form validation using JavaScript
    document.getElementById('reportForm').addEventListener('submit', function(event) {
        var valid = true;

        // Check each required field
        var requiredFields = ['workDate','signinTime', 'lunchinTime', 'lunchoutTime', 'breakinTime', 'breakoutTime', 'signoutTime', 'workHours'];
        requiredFields.forEach(function(field) {
            var value = document.getElementById(field).value.trim();
            if (value === '') {
                valid = false;
                alert('Please fill in all required fields.');
                event.preventDefault();
            }
        });

        // Additional validation logic can be added here

        if (!valid) {
            event.preventDefault();
        }
    });
</script> -->



<?php
include 'footer.php';
?>