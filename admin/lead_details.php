<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';
$id=oecrm_int_param($_GET,'leadId');$companyId=oecrm_current_company_id($conn);oecrm_require_permission($conn,'clients','view');
$stmt=mysqli_prepare($conn,'SELECT * FROM leads WHERE lead_id=? AND company_id=? AND is_active=1');mysqli_stmt_bind_param($stmt,'ii',$id,$companyId);mysqli_stmt_execute($stmt);$details=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);
if(!$details){http_response_code(404);exit('Lead not found.');}
?>

<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading panel-box">
            <h4>Lead Details</h4>
        </div>
        <div class="panel-body manage_project">
    
            <div class="row">
                <div class="col-md-12">
                    <table class="table lead-details">
                        <tr>
                            <td class="f-bold">Name:</td>
                            <td><?= $details['executive_name'] ?></td>

                            <td class="f-bold">Company Name:</td>
                            <td><?= $details['company_name'] ?></td>
                        </tr>

                        <tr>
                            <td class="f-bold">Contact Person:</td>
                            <td><?= $details['contact_person'] ?></td>

                            <td class="f-bold">Mobile Number 1:</td>
                            <td><?= $details['mobile_no1'] ?></td>
                        </tr>
                        <tr>
                            <td class="f-bold">Mobile Number 2:</td>
                            <td><?= $details['mobile_no2'] ?></td>

                            <td class="f-bold">Eamil:</td>
                            <td><?= $details['email'] ?></td>
                        </tr>

                        <tr>
                            <td class="f-bold">Personal Email:</td>
                            <td><?= $details['personal_email'] ?></td>

                            <td class="f-bold">City:</td>
                            <td><?= $details['city'] ?></td>
                        </tr>

                        <tr>
                            <td class="f-bold">Address:</td>
                            <td><?= $details['address'] ?></td>

                            <td class="f-bold">Created By:</td>
                            <td><?= $details['created_by'] == 0 ? "Admin" : "User"  ?></td>
                        </tr>
                    </table>



                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="panel panel-default">
        <div class="panel-heading panel-box">
            <h4>Followup Details</h4>
        </div>
        <div class="panel-body manage_project">

            <div class="row">
                <!-- <h2>Followup</h2> -->
                <!-- Button trigger modal -->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" onclick="openModal()">
                                Add New Followup
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="addLeavemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <form role="form" method="POST" action="validation.php">
                        <?php echo oecrm_csrf_field(); ?>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Add New Followup</h4>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="lead_id" value="<?= $id ?>">
                                    <!-- Lead Priority -->
                                    <div class="mb-3" style="display:none;">
                                        <label for="leadType" class="form-label">Lead Priority</label>
                                        <select class="form-control" id="leadType" name="lead_type">
                                            <option value="" selected disabled>Select Lead Priority</option>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>

                                        <!-- Followup Type -->
                                        <div class="mb-3">
                                            <label for="followupType" class="form-label">Followup Type</label>
                                            <select name="followupType" id="followupType" class="form-control" required>
                                                <!-- <option value="Call">Call</option>
                                                <option value="Email">Email</option>
                                                <option value="Message">Message</option>
                                                <option value="Other">Message</option> -->
                                                <?php 
                                                    $sel_followup_types=mysqli_query($conn,"SELECT * from followup_type_tbl");
                                                    while ($fet_followup_types=mysqli_fetch_assoc($sel_followup_types)) 
                                                    {
                                                        $selected_type="";
                                                        

                                                        echo "<option value='".$fet_followup_types['name']."' ".$selected_type.">".$fet_followup_types['name']."</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Remarks -->
                                        <div class="mb-3">
                                            <label for="remarks" class="form-label">Remarks</label>
                                            <textarea class="form-control" id="remarks" name="remarks" rows="3" required></textarea>
                                        </div>

                                        <!-- Next Followup Date -->
                                    <div class="mb-3">
                                        <label for="nextFollowupDate" class="form-label">Next Followup Date</label>
                                        <input type="date" class="form-control" id="nextFollowupDate" name="next_followup_date" min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>

                                        <!-- Next Followup Time -->
                                    <div class="mb-3">
                                        <label for="nextFollowupTime" class="form-label">Next Followup Time</label>
                                        <input type="time" class="form-control" id="nextFollowupTime" name="next_followup_time" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="leadType" name="status" required>
                                            <option value="status" selected disabled>Select Status</option>
                                            <option value="close">Close</option>
                                            <option value="inprogress">Inprogress</option>
                                            <option value="complete">Complete</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                    </div>

                                        <!-- Add other form fields as needed -->
                                    <input type="submit" value="Submit" name="saveLeadFollowup" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table" id="leads_table">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Date</th>
                                    <!-- <th>Lead Priority</th> -->
                                    <th>Followup Type</th>
                                    <th>Remark</th>
                                    <th>Next Followup Date</th>
                                    <th>Next Followup Time</th>
                                    
                                    <!-- <th>Created By</th> -->
                                    <th>status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                $stmt=mysqli_prepare($conn,'SELECT * FROM lead_followup WHERE lead_id=? AND status<>"cancelled" ORDER BY id DESC');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);$followupQuery=mysqli_stmt_get_result($stmt);
                                while ($followupRow = mysqli_fetch_assoc($followupQuery)) {
                                    if($followupRow['status']=="0"){$followupRow['status']="pending";}
                                ?>
                                    <tr>
                                        <td><?= $count++ ?> </td>
                                        <td><?= date('d-m-Y',strtotime($followupRow['created_at'])) ?></td>
                                        
                                        <td><?= $followupRow['followup_type'] ?></td>
                                        <td><?= $followupRow['remarks'] ?></td>
                                        <td><?= date('d-m-Y',strtotime($followupRow['next_followup_date'])) ?></td>
                                        <td><?= date('h:i A',strtotime($followupRow['next_followup_time'])) ?></td>
                                        
                                        <!-- <td><?= $followupRow['created_by'] == 0 ? "Admin" : "User" ?></td> -->
                                        <td><?= $followupRow['status'] ?></td>
                                        <td>
                                            <!-- <button type="button" class="btn btn-info btn-xs" onclick="openModaledit()" <?= $editrowView['id'] ?>><i class="fa fa-pencil"></i>

                                            </button>  -->
                                            <!-- <a href="view_lead_followup_details.php?leadId=<?= $followupRow['id'] ?>" class="btn btn-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a> -->
                                            <a href="edit_followup_details.php?edit=<?= $followupRow['id'] ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <!-- edit modal -->
                                        
                                            <!--end edit modal -->

                                            <form method="post" action="delete_lead_followup.php" style="display:inline;">
                                                <?php echo oecrm_csrf_field(); ?>
                                                <input type="hidden" name="delete" value="<?= (int) $followupRow['id'] ?>">
                                                <input type="hidden" name="lead_id" value="<?= (int) $followupRow['lead_id'] ?>">
                                                <button type="submit" class="btn btn-link" style="padding:0;border:0;" data-confirm="Cancel this follow-up?"><i class="fa fa-trash-o "></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
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
    // $('#leads_table').DataTable();

    function openModal() {
        $('#addLeavemodal').modal('show');

    }


    function openModaledit() {
        $('#editLeavemodal').modal('show');

    }
</script>

<?php
include 'footer.php';
?>
