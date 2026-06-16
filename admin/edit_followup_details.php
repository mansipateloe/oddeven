<?php include 'header.php'; ?>

<?
$id = $_GET['leadId'];
$viewdetails = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * from lead_followup where id=$id"));

?>

<?php

$id = $_GET['edit'];

$qryView = "SELECT * FROM lead_followup WHERE id=" . $id;

$resultView = mysqli_query($conn, $qryView);

$rowView = $resultView->fetch_assoc();
// print_r($rowView);action_update_followup

?>

        <div id="page-wrapper">
            <div class="panel panel-default">
                <div class="panel-heading panel-box">
                    <h4>Edit Follow Up</h4>
                </div>
                <div class="panel-body manage_project">
                
                    <form role="form" action='action_update_followup.php' method="POST"  >
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="lead_id" value="<?php echo $rowView['lead_id'] ?>">
                        <div class="form-row" style="display:none;">
                            <div class="form-group col-md-12">
                                <label for="" class="form-label">Lead Type</label>
                                <select name="lead_type" class="form-control">
                                        <option value="">Select Lead Type</option>
                                        <?php
                                        if ($rowView['lead_type'] == "low") {
                                            echo '<option value="low" selected>Low</option>';
                                            echo '<option value="medium">Medium</option>';
                                            echo '<option value="high">High</option>';
                                        }
                                        if ($rowView['lead_type'] == "medium") {
                                            echo '<option value="low">Low</option>';
                                            echo '<option value="medium" selected>Medium</option>';
                                            echo '<option value="high">High</option>';
                                        }
                                        if ($rowView['lead_type'] == "high") {
                                            echo '<option value="low">Low</option>';
                                            echo '<option value="medium">Medium</option>';
                                            echo '<option value="high" selected>High</option>';
                                        }

                                        if (empty($rowView['lead_type'])) {
                                            echo '<option value="low">Low</option>';
                                            echo '<option value="medium">Medium</option>';
                                            echo '<option value="high">High</option>';
                                        }
                                        ?>
                                    </select>
                            </div>
                            <!-- Add other form fields here -->
                        </div>

                        <!-- Repeat the following block for each form field -->
                        <div class="form-group col-sm-6">
                            <label for="" class="form-label">Followup Type</label>

                            <select name="followup_type" class="form-control">
                                <option value="">Select Followup Type</option>
                                <?php 
                                    $sel_followup_types=mysqli_query($conn,"SELECT * from followup_type_tbl");
                                    while ($fet_followup_types=mysqli_fetch_assoc($sel_followup_types)) 
                                    {
                                        $selected_type="";
                                        if($rowView['followup_type']==$fet_followup_types['name'])
                                        {
                                            $selected_type="selected";
                                        }

                                        echo "<option value='".$fet_followup_types['name']."' ".$selected_type.">".$fet_followup_types['name']."</option>";
                                    }
                                ?>
                                <!-- <option value="Call" <?php if ($rowView['followup_type'] == "Call") { echo "selected";}?>>Call</option>
                                <option value="Email" <?php if ($rowView['followup_type'] == "Email") { echo "selected";}?>>Email</option>
                                <option value="Message" <?php if ($rowView['followup_type'] == "Message") { echo "selected";}?>>Message</option>
                                <option value="Other" <?php if ($rowView['followup_type'] == "Other") { echo "selected";}?>>Other</option> -->
                                

                            </select>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="" class="form-label">Status</label>

                            <select name="status" class="form-control">
                                <option value="">Select Status</option>
                                <option value="close" <?php if ($rowView['status'] == "close") { echo "selected";}?>>Close</option>
                                <option value="inprogress" <?php if ($rowView['status'] == "inprogress") { echo "selected";}?>>Inprogress</option>
                                <option value="complete" <?php if ($rowView['status'] == "complete") { echo "selected";}?>>Complete</option>
                                <option value="pending" <?php if ($rowView['status'] == "pending") { echo "selected";}?>>Pending</option>
                                

                            </select>
                        </div>
                        <!-- Remarks -->

                        <div class="form-group col-sm-6">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"  required><?php echo $rowView['remarks'] ?></textarea>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="nextFollowupDate" class="form-label">Next Followup Date</label>
                            <input type="date" class="form-control" id="nextFollowupDate" name="next_followup_date" value="<?php echo $rowView['next_followup_date'] ?>" required>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="nextFollowupTime" class="form-label">Next Followup Time</label>
                            <input type="time" class="form-control" id="nextFollowupTime" name="next_followup_time" value="<?php echo $rowView['next_followup_time'] ?>" required>
                        </div>

                
                        <!--<div class="mb-3">
                                <label for="status">Status</label>
                                <select class="form-control" id="leadType" name="status" required>
                                    <option value="status" selected disabled>Select Status</option>
                                    <option value="close">Close</option>
                                    <option value="inprogress">Inprogress</option>
                                    <option value="complete">Complete</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div> -->
                        <!-- <div class="form-group col-sm-6">
                            <label for="" class="form-label">status</label>

                                <select name="status" class="form-control">
                                    <option value="status">Select status</option>
                                    <?php
                                    if ($rowView['status'] == "close") {
                                        echo '<option value="close" selected>close</option>';
                                        echo '<option value="inprogress">inprogress</option>';
                                        echo '<option value="complete">complete</option>';
                                        echo '<option value="pending">pending</option>';
                                    }
                                    if ($rowView['status'] == "inprogress") {
                                        echo '<option value="close">close</option>';
                                        echo '<option value="inprogress" selected>inprogress</option>';
                                        echo '<option value="complete">complete</option>';
                                        echo '<option value="pending">pending</option>';
                                    }
                                    if ($rowView['status'] == "complete") {
                                        echo '<option value="close">close</option>';
                                        echo '<option value="inprogress">inprogress</option>';
                                        echo '<option value="complete" selected>complete</option>';
                                        echo '<option value="pending">pending</option>';
                                    }

                                    if ($rowView['status'] == "pending") {
                                        echo '<option value="close">close</option>';
                                        echo '<option value="inprogress">inprogress</option>';
                                        echo '<option value="complete">complete</option>';
                                        echo '<option value="pending">pending</option>';
                                    }
                                    if (empty($rowView['status'])) {
                                        echo '<option value="close">close</option>';
                                        echo '<option value="inprogress">inprogress</option>';
                                        echo '<option value="complete">complete</option>';
                                        echo '<option value="pending">pending</option>';
                                    }
                                    ?>

                                </select>
                            </div>
                        <div> -->
                        <input type="submit" class="btn btn-primary " name="saveLeadFollowup" value="Update ">
                                <!-- <a href="lead_details.php?leadId=<?= $rowView['id'] ?>&lead_id=<?= $rowView['lead_id'] ?>"class="btn btn-primary" title="update">update</a>  -->
                                    
                        
                        <!-- <input type="submit" name="saveLeadFollowup" class="btn btn-primary" value="Update"> -->

                    </form>
                </div>
            </div>

        </div>

<script src="../vendor/jquery/jquery.min.js"></script>

<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

<script src="../vendor/metisMenu/metisMenu.min.js"></script>

<script src="../vendor/raphael/raphael.min.js"></script>

<script src="../vendor/morrisjs/morris.min.js"></script>

<script src="../data/morris-data.js"></script>

<script src="../dist/js/sb-admin-2.js"></script>

<?php include 'footer.php'; ?>