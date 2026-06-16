<?php include 'header.php'; ?>
<div id="page-wrapper">
    <div class="">
        <div class="panel panel-default">
            <div class="panel-heading panel-box">
                <h4>Lead Form Create</h4>
            </div>
            <div class="panel-body manage_project">    
                <form id="leadForm" method="post" action="leads.php"  enctype="multipart/form-data">
                    
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="leadDate">Lead Date</label>
                            <input type="date" class="form-control" placeholder="dd-mm-yyyy" id="leadDate" name="leadDate" required>
                        </div>
                    </div>

                    <!-- Repeat the following block for each form field -->
                    <div class="form-group col-sm-3">
                        <label for="executiveName">Client Name</label>
                        <input type="text" class="form-control" placeholder=" Client Name" id="executiveName" name="executiveName" required>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="firstname" class="control-label ">Nick Name</label>
                        <div class="">
                            <input type="text" name="nick_name" id="nick_name" class="form-control" placeholder="Nick Name" value="" maxlength="100">
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="firstname" class="control-label ">Company Name</label>
                        <div class="">
                            <input type="text" name="company" id="company" class="form-control" placeholder="Company Name" value="" maxlength="100">
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="firstname" class="control-label ">Contact Person</label>
                        <div class="">
                            <input type="text" name="cperson" id="cperson" class="form-control" placeholder="Contact Person" value="" maxlength="100">
                            <span class="help" id="msg1"></span>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="firstname" class="control-label ">Mobile No1</label>
                        <div class="">
                            <input type="text" name="mobileno1" id="mobileno1" maxlength="10" class="decimal form-control" placeholder="Mobile No1" value="">
                            <span class="help" id="msg2"></span>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="firstname" class="control-label ">Mobile No2</label>
                        <div class="">
                            <input type="text" name="mobileno2" id="mobileno2" maxlength="10" class="decimal form-control" placeholder="Mobile No2" value="">
                            <span class="help" id="msg2_1"></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="firstname" class="control-label "> Company Email ID</label>
                        <div class="">
                            <input type="email" name="emailid" id="emailid" class="form-control" placeholder="Email ID" value="">
                            <span class="help" id="msg3"></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="firstname" class="control-label ">Personal Email ID</label>
                        <div class="">
                            <input type="email" name="emailid2" id="emailid2" class="form-control" placeholder="Email ID" value="">
                            <span class="help" id="msg3"></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="firstname" class="control-label ">City</label>
                        <div class="">
                            <input type="text" name="city" id="city" class="form-control" placeholder="City" value="" maxlength="100">

                        </div>
                    </div>
                    
                    <div class="form-group col-sm-3">
                        <label for="lead_source">Lead Source</label>
                        <select class="form-control" id="lead_source" name="lead_source" required>
                            <option value=""  disabled>Select Lead Source</option>
                            <?php 
                                $sel_lead_sources=mysqli_query($conn,"SELECT * from lead_source_tbl");
                                while ($fet_sources=mysqli_fetch_assoc($sel_lead_sources)) 
                                {
                                    echo "<option value='".$fet_sources['id']."'>".$fet_sources['name']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="firstname" class="control-label ">Address</label>
                        <div class="">
                            <textarea name="address" id="address" placeholder="Address" class="form-control"></textarea>
                        </div>
                    </div>
                    <!-- Repeat the block ends here -->

                    

                    <div class="form-group col-sm-4">
                        <label for="leadType">Lead Priority</label>
                        <select class="form-control" id="leadType" name="leadType" required>
                            <option value="" selected disabled>Select Lead priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="followupType">Followup Type</label>
                        <select name="followupType" id="followupType" class="form-control" required>
                            <option value="Call">Call</option>
                            <option value="Email">Email</option>
                            <option value="Message">Message</option>
                            <option value="meeting">Meeting</option>
                        </select>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" ></textarea>
                    </div>

                    <div class="form-row col-sm-4">
                        <div class="form-group col-md-6">
                            <label for="nextFollowupDate">Next Followup Date</label>
                            <input type="date" class="form-control" id="nextFollowupDate" name="nextFollowupDate" >
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nextFollowupTime">Next Followup Time</label>
                            <input type="time" class="form-control" id="nextFollowupTime" name="nextFollowupTime" >
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" >
                            <option value="" selected disabled>Select Status</option>
                            <option value="close">Close</option>
                            <option value="inprogress">Inprogress</option>
                            <option value="complete">Complete</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="attachments">Attachments</label>
                        <input type="file" class="form-control-file" id="attachments" name="attachments">
                    </div>
                    <div class="form-group col-sm-12">
                        <input type="submit" value="Submit" name="leadSave" class="btn btn-primary">
                    </div>
                </form>
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
<script src="../dist/js/sb-admin-2.js"></script>
<!-- Custom JavaScript for form validation -->
<script>
    // Example of basic form validation using JavaScript
    document.getElementById('leadForm').addEventListener('submit', function(event) {
        var valid = true;

        // Check each required field
        var requiredFields = ['leadDate', 'executiveName', 'leadType', 'followupType', 'remarks'];
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