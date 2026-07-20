<?php include 'header.php'; $flash = $_SESSION['lead_flash'] ?? ''; unset($_SESSION['lead_flash']); ?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?><div class="alert alert-info"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <div class="">
        <div class="panel panel-default lead-compact-panel">
            <div class="panel-heading panel-box lead-compact-heading">
                <div>
                    <h4>Lead Form Create</h4>
                    <span>Capture client, contact and follow-up details</span>
                </div>
                <a href="leads.php" class="btn btn-default btn-sm lead-compact-back"><i class="fa fa-list-alt"></i> View Leads</a>
            </div>
            <div class="panel-body manage_project lead-compact-body">
                <form id="leadForm" class="lead-compact-form" method="post" action="leads.php" enctype="multipart/form-data">
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="lead-compact-grid">
                        <div class="form-group">
                            <label for="leadDate">Lead Date</label>
                            <input type="date" class="form-control" placeholder="dd-mm-yyyy" id="leadDate" name="leadDate" value="<?php echo $today_date; ?>">
                        </div>

                        <div class="form-group">
                            <label for="executiveName">Client Name</label>
                            <input type="text" class="form-control" placeholder="Client Name" id="executiveName" name="executiveName">
                        </div>

                        <div class="form-group">
                            <label for="nick_name">Nick Name</label>
                            <input type="text" name="nick_name" id="nick_name" class="form-control" placeholder="Nick Name" maxlength="100">
                        </div>

                        <div class="form-group">
                            <label for="company">Company Name *</label>
                            <input type="text" name="company" id="company" class="form-control" placeholder="Company Name" maxlength="100" required>
                        </div>

                        <div class="form-group">
                            <label for="cperson">Contact Person *</label>
                            <input type="text" name="cperson" id="cperson" class="form-control" placeholder="Contact Person" maxlength="100" required>
                            <span class="help" id="msg1"></span>
                        </div>

                        <div class="form-group">
                            <label for="mobileno1">Mobile No1</label>
                            <input type="text" name="mobileno1" id="mobileno1" maxlength="10" class="form-control only-digits" placeholder="Mobile No1" inputmode="numeric" pattern="[0-9]{10}" autocomplete="off">
                            <span class="help" id="msg2"></span>
                        </div>

                        <div class="form-group">
                            <label for="mobileno2">Mobile No2</label>
                            <input type="text" name="mobileno2" id="mobileno2" maxlength="10" class="form-control only-digits" placeholder="Mobile No2" inputmode="numeric" pattern="[0-9]{10}" autocomplete="off">
                            <span class="help" id="msg2_1"></span>
                        </div>

                        <div class="form-group">
                            <label for="emailid">Company Email ID</label>
                            <input type="email" name="emailid" id="emailid" class="form-control" placeholder="Company Email ID">
                            <span class="help" id="msg3"></span>
                        </div>

                        <div class="form-group">
                            <label for="emailid2">Personal Email ID</label>
                            <input type="email" name="emailid2" id="emailid2" class="form-control" placeholder="Personal Email ID">
                        </div>

                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" name="city" id="city" class="form-control" placeholder="City" maxlength="100">
                        </div>

                        <div class="form-group">
                            <label for="lead_source">Lead Source</label>
                            <select class="form-control" id="lead_source" name="lead_source">
                                <option value="" selected>Select Lead Source</option>
                                <?php
                                    $sel_lead_sources = mysqli_query($conn, "SELECT * from lead_source_tbl");
                                    while ($fet_sources = mysqli_fetch_assoc($sel_lead_sources)) {
                                        echo "<option value='".$fet_sources['id']."'>".$fet_sources['name']."</option>";
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="leadType">Lead Priority</label>
                            <select class="form-control" id="leadType" name="leadType">
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                                
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div class="form-group lead-span-4">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" placeholder="Address" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="followupType">Followup Type</label>
                            <select name="followupType" id="followupType" class="form-control">
                                <option value="Call">Call</option>
                                <option value="Email">Email</option>
                                <option value="Message">Message</option>
                                <option value="meeting">Meeting</option>
                            </select>
                        </div>

                        <div class="lead-followup-pair">
                            <div class="form-group">
                                <label for="nextFollowupDate">Next Followup Date</label>
                                <input type="date" class="form-control" id="nextFollowupDate" name="nextFollowupDate">
                            </div>
                            <div class="form-group">
                                <label for="nextFollowupTime">Next Followup Time</label>
                                <input type="time" class="form-control" id="nextFollowupTime" name="nextFollowupTime">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="" selected disabled>Select Status</option>
                                <option value="closed">Closed</option>
                                <option value="inprogress">Inprogress</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <input type="file" class="form-control-file" id="attachments" name="attachments">
                        </div>

                        <div class="form-group lead-span-4">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                        </div>

                        <div class="lead-compact-actions">
                            <button type="submit" name="leadSave" class="btn btn-primary"><i class="fa fa-save"></i> Submit Lead</button>
                            <a href="leads.php" class="btn btn-default"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
