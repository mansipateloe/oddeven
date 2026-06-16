<?php
include 'header.php';
?>
<?php

$id = $_GET['edit'];

$qryView = "SELECT * FROM leads WHERE lead_id=" . $id;

$resultView = mysqli_query($conn, $qryView);

$rowView = $resultView->fetch_assoc();


$queryViewlead = "SELECT * FROM lead_followup WHERE id=" . $id;
$resultViewlead = mysqli_query($conn, $qryView);
$rowViewlead = $resultView->fetch_assoc();

?>

<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading panel-box">
            <h4>Edit Lead</h4>
        </div>
        <div class="panel-body manage_project">
        
        <!-- <form id="leadForm" action="action_update_lead.php" method="post" onsubmit="return checkall();" enctype="multipart/form-data"> -->
            <form id="leadForm" action="action_update_lead.php" method="post"  enctype="multipart/form-data">
            
            <input type="hidden" name="lead_id" value="<?= $id ?>">
            <!-- <input type="hidden" name="lead_id" value="<?php echo $rowView['lead_id'] ?>"> -->
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="leadDate">Lead Date</label>
                        <input type="date" class="form-control" id="leadDate" name="leadDate" value="<?php echo $rowView['lead_date'] ?>" required>
                    </div>
                    <!-- Add other form fields here -->
                </div>

                <!-- Repeat the following block for each form field -->
                <div class="form-group col-sm-6">
                    <label for="executiveName">Executive Name</label>
                    <input type="text" class="form-control" id="executiveName" name="executiveName" value="<?php echo $rowView['executive_name'] ?>" required>
                </div>

                <div class="form-group col-sm-6">
                    <label for="firstname" class="control-label ">Company Name</label>
                    <div class="">
                        <input type="text" name="company" id="company" class="form-control" placeholder="Company Name" value="<?php echo $rowView['company_name'] ?>" maxlength="100">
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="firstname" class="control-label ">Contact Person</label>
                    <div class="">
                        <input type="text" name="cperson" id="cperson" class="form-control" placeholder="Contact Person" value="<?php echo $rowView['contact_person'] ?>" maxlength="100">
                        <span class="help" id="msg1"></span>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="firstname" class="control-label ">Mobile No1</label>
                    <div class="">
                        <input type="text" name="mobileno1" id="mobileno1" maxlength="10" class="decimal form-control" placeholder="Mobile No1" value="<?php echo $rowView['mobile_no1'] ?>">
                        <span class="help" id="msg2"></span>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="firstname" class="control-label ">Mobile No2</label>
                    <div class="">
                        <input type="text" name="mobileno2" id="mobileno2" maxlength="10" class="decimal form-control" placeholder="Mobile No2" value="<?php echo $rowView['mobile_no2'] ?>">
                        <span class="help" id="msg2_1"></span>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="firstname" class="control-label ">Email ID</label>
                    <div class="">
                        <input type="email" name="emailid" id="emailid" class="form-control" placeholder="Email ID" value="<?php echo $rowView['email'] ?>">
                        <span class="help" id="msg3"></span>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="firstname" class="control-label ">Personal Email ID</label>
                    <div class="">
                        <input type="email" name="emailid2" id="emailid2" class="form-control" placeholder="Email ID" value="<?php echo $rowView['personal_email'] ?>">
                        <span class="help" id="msg3"></span>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="firstname" class="control-label ">City</label>
                    <div class="">
                        <input type="text" name="city" id="city" class="form-control" placeholder="City" value="<?php echo $rowView['city'] ?>" maxlength="100">

                    </div>

                </div>
                <div class="form-group col-sm-6">
                        <label for="firstname" class="control-label ">Nick Name</label>
                        <div class="">
                            <input type="text" name="nick_name" id="nick_name" class="form-control" placeholder="Nick Name" value="<?php echo $rowView['nick_name'] ?>" maxlength="100">
                        </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="lead_source">Lead Source</label>
                    <select class="form-control" id="lead_source" name="lead_source" required>
                        <option value=""  disabled>Select Lead Source</option>
                        <?php 
                            $sel_lead_sources=mysqli_query($conn,"SELECT * from lead_source_tbl");
                            while ($fet_sources=mysqli_fetch_assoc($sel_lead_sources)) 
                            {
                                $selected_source="";
                                if($rowView['lead_source']==$fet_sources['id'])
                                {
                                    $selected_source="selected";
                                }
                                echo "<option value='".$fet_sources['id']."' ".$selected_source.">".$fet_sources['name']."</option>";
                            }
                        ?>
                    </select>
                </div>
                <!-- <div class="form-group col-sm-6">
                    <label for="leadType">Status</label>
                    <select class="form-control" id="leadType" name="leadType" required>
                        <option value="" selected disabled>Select Status</option>
                        <option value="close">Close</option>
                        <option value="inprogress">Inprogress</option>
                        <option value="complete">Complete</option>
                        <option value="pending">Pending</option>


                    </select>
                </div> -->
                <div class="form-group col-sm-6">
                <label for="" class="form-label">Status</label>

                    <select name="status" class="form-control">
                        <option value="">Select Status</option>
                        <option value="close" <?php if ($rowView['status'] == "close"){echo "selected"; } ?>>Close</option>
                        <option value="inprogress" <?php if ($rowView['status'] == "inprogress"){echo "selected"; } ?>>Inprogress</option>
                        <option value="complete" <?php if ($rowView['status'] == "complete"){echo "selected"; } ?>>Complete</option>
                        <option value="pending" <?php if ($rowView['status'] == "pending"){echo "selected"; } ?>>Pending</option>
                        
                    </select>
                </div>



                <!--working code-->
                <div class="form-group col-sm-6">
                    <label for="firstname" class="control-label ">Address</label>
                    <div class="">
                        <textarea name="address" id="address" class="form-control" value="<?php echo $rowView['address'] ?>"></textarea>
                    </div>
                </div>
                <!-- end working code -->
                <!-- Repeat the block ends here -->

                <!-- <div class="form-group col-sm-6">
                    <label for="leadType">Lead Type</label>
                    <select class="form-control" id="leadType" name="leadType" required>
                        <option value="<?php echo $rowView['lead_type'] ?>" selected disabled>Select Lead Type</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div> -->
                <!-- working code-->
                <!-- <div class="form-group col-sm-6">
                    <select name="lead_type" class="form-control">
                        <option value="">Select Lead Type</option>
                        <?php
                        if ($rowViewlead['lead_type'] == "low") {
                            echo '<option value="low" selected>Low</option>';
                            echo '<option value="medium">Medium</option>';
                            echo '<option value="high">High</option>';
                        }
                        if ($rowViewlead['lead_type'] == "medium") {
                            echo '<option value="low">Low</option>';
                            echo '<option value="medium" selected>Medium</option>';
                            echo '<option value="high">High</option>';
                        }
                        if ($rowViewlead['lead_type'] == "high") {
                            echo '<option value="low">Low</option>';
                            echo '<option value="medium">Medium</option>';
                            echo '<option value="high" selected>High</option>';
                        }

                        if (empty($rowViewlead['lead_type'])) {
                            echo '<option value="low">Low</option>';
                            echo '<option value="medium">Medium</option>';
                            echo '<option value="high">High</option>';
                        }
                        ?>
                    </select>

                </div> -->
                <!--working code end -->
            <!--working code  -->
                <!-- <div class="form-group col-sm-6">
                    <select name="followup_type" class="form-control">
                        <option value="">Select Followup Type</option>
                        <?php
                        if ($rowViewlead['followup_type'] == "Call") {
                            echo '<option value="Call" selected>Call</option>';
                            echo '<option value="Email">Email</option>';
                            echo '<option value="Message">Message</option>';
                            echo '<option value="Other">Other</option>';
                        }
                        if ($rowViewlead['followup_type'] == "Email") {
                            echo '<option value="Call">Call</option>';
                            echo '<option value="Email" selected>Email</option>';
                            echo '<option value="Message">Message</option>';
                            echo '<option value="Other">Other</option>';
                        }
                        if ($rowViewlead['followup_type'] == "Message") {
                            echo '<option value="Call">Call</option>';
                            echo '<option value="Email">Email</option>';
                            echo '<option value="Message" selected>Message</option>';
                            echo '<option value="Other">Other</option>';
                        }

                        if (empty($rowViewlead['followup_type'])) {
                            echo '<option value="Call">Call</option>';
                            echo '<option value="Email">Email</option>';
                            echo '<option value="Message">Message</option>';
                            echo '<option value="Other">Other</option>';
                        }
                        if (empty($rowViewlead['followup_type'])) {
                            echo '<option value="Call">Call</option>';
                            echo '<option value="Email">Email</option>';
                            echo '<option value="Message">Message</option>';
                            echo '<option value="Other">Other</option>';
                        }
                        ?>

                    </select>

                </div>
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3" value="<?php echo $rowViewlead['remarks'] ?>" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nextFollowupDate">Next Followup Date</label>
                        <input type="date" class="form-control" id="nextFollowupDate" name="nextFollowupDate" value="<?php echo $rowViewlead['next_followup_date'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nextFollowupTime">Next Followup Time</label>
                        <input type="time" class="form-control" id="nextFollowupTime" name="nextFollowupTime" value="<?php echo $rowViewlead['next_followup_time'] ?>" required>
                    </div>
                </div> -->
            <!-- end workind code -->
                <!-- <div class="form-group">
                    <label for="attachments">Attachments</label>
                    <input type="file" class="form-control-file" id="attachments" name="attachments" value="<?php echo $rowView['attachments'] ?>">
                </div> -->

                <input type="submit" value="Update" name="leadSave" class="btn btn-primary">

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
<script>
    // Example of basic form validation using JavaScript
    document.getElementById('leadForm').addEventListener('submit', function(event) {
        var valid = true;

        // Check each required field
        var requiredFields = ['leadDate', 'executiveName', 'leadType', 'followupType', 'remarks', 'nextFollowupDate', 'nextFollowupTime'];
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
</script>



<?php
include 'footer.php';
?>