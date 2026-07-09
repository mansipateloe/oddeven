<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();
oecrm_require_permission($conn, 'leave_requests', 'edit');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
$id = oecrm_int_param($_POST, 'id');
$stmt = mysqli_prepare($conn, 'SELECT * FROM leave_master WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$leaveRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$leaveRow) {
    http_response_code(404);
    exit('Leave request not found.');
}
?>

<div class="modal fade" id="editLeavemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form role="form" method="POST" action="validation.php">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Update Leave</h4>
                </div>
                <div class="modal-body custom-alert-msg">
                    <?php echo oecrm_csrf_field(); ?>
                    <select name="status" id="" class="form-control">
                        <option value="0" <?= $leaveRow['is_approved'] == 0 ? "selected" : "" ?>>Pending</option>
                        <option value="1" <?= $leaveRow['is_approved'] == 1 ? "selected" : "" ?>>Approved</option>
                        <option value="2" <?= $leaveRow['is_approved'] == 2 ? "selected" : "" ?>>Disapproved</option>
                    </select>
                    <textarea name="remarks" id="" cols="30" rows="10" class="form-control" placeholder="remarks"></textarea>
                    <input type="hidden" name="id" value="<?= $id ?>">
                </div>
                <div class="modal-footer">
                    <input type="submit" value="Save" class="btn btn-success" name="LeaveSave">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $('#editLeavemodal').modal('show');
</script>
