<?php
ob_start();
$active_menu = 'setting';
require_once __DIR__ . '/../security.php';
include __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();

function oecrm_leave_type_flash($type, $message)
{
    $_SESSION['leave_type_flash'] = ['type' => $type, 'message' => $message];
}

function oecrm_leave_type_redirect()
{
    header('Location: manageLeaveType.php');
    exit;
}

function oecrm_leave_type_table_exists($conn, $table)
{
    $safeTable = mysqli_real_escape_string($conn, (string) $table);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$safeTable'");
    return $result && mysqli_num_rows($result) > 0;
}

function oecrm_leave_type_count($conn, $sql, $type, $value)
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return 0;
    }
    mysqli_stmt_bind_param($stmt, $type, $value);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return (int) ($row['total'] ?? 0);
}

function oecrm_leave_type_can_manage($conn)
{
    return oecrm_can($conn, 'leave_policies', 'edit')
        || oecrm_legacy_can($conn, 'settings')
        || oecrm_is_super_admin();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['addLeaveType']) || isset($_POST['deleteLeaveType']))) {
    oecrm_require_csrf();

    if (!oecrm_leave_type_can_manage($conn)) {
        http_response_code(403);
        exit('You do not have permission to manage leave types.');
    }

    if (isset($_POST['addLeaveType'])) {
        $name = trim((string) ($_POST['name'] ?? ''));
        $name = preg_replace('/\s+/', ' ', $name);

        if ($name === '') {
            oecrm_leave_type_flash('danger', 'Leave Type name is required.');
            oecrm_leave_type_redirect();
        }

        $duplicateStmt = mysqli_prepare($conn, 'SELECT id FROM leavetypetbl WHERE LOWER(name)=LOWER(?) LIMIT 1');
        mysqli_stmt_bind_param($duplicateStmt, 's', $name);
        mysqli_stmt_execute($duplicateStmt);
        $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($duplicateStmt));
        mysqli_stmt_close($duplicateStmt);

        if ($duplicate) {
            oecrm_leave_type_flash('warning', 'This Leave Type already exists.');
            oecrm_leave_type_redirect();
        }

        mysqli_begin_transaction($conn);
        $insertStmt = mysqli_prepare($conn, 'INSERT INTO leavetypetbl(name) VALUES(?)');
        if (!$insertStmt) {
            mysqli_rollback($conn);
            oecrm_leave_type_flash('danger', 'Leave Type could not be saved.');
            oecrm_leave_type_redirect();
        }
        mysqli_stmt_bind_param($insertStmt, 's', $name);
        $saved = mysqli_stmt_execute($insertStmt);
        $leaveTypeId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($insertStmt);

        if (!$saved || $leaveTypeId <= 0) {
            mysqli_rollback($conn);
            oecrm_leave_type_flash('danger', 'Leave Type could not be saved.');
            oecrm_leave_type_redirect();
        }

        if (oecrm_leave_type_table_exists($conn, 'leave_policies') && oecrm_leave_type_table_exists($conn, 'companies')) {
            mysqli_query($conn, 'INSERT IGNORE INTO leave_policies(company_id,leave_type_id,annual_entitlement,is_paid,sandwich_enabled,allow_half_day,status) SELECT id,' . $leaveTypeId . ',0,0,0,0,1 FROM companies WHERE status=1');
        }

        if (oecrm_leave_type_table_exists($conn, 'employee_leave_balances') && oecrm_leave_type_table_exists($conn, 'employeestbl')) {
            mysqli_query($conn, 'INSERT IGNORE INTO employee_leave_balances(employee_id,leave_type_id,balance_year,credited) SELECT id,' . $leaveTypeId . ',YEAR(CURDATE()),0 FROM employeestbl WHERE status=0');
        }

        mysqli_commit($conn);
        oecrm_audit($conn, 'leave_types', 'create', 'leave_type', $leaveTypeId, 'Leave type created', null, ['name' => $name]);
        oecrm_leave_type_flash('success', 'Leave Type added successfully.');
        oecrm_leave_type_redirect();
    }

    if (isset($_POST['deleteLeaveType'])) {
        $leaveTypeId = (int) ($_POST['leave_type_id'] ?? 0);
        if ($leaveTypeId <= 0) {
            oecrm_leave_type_flash('danger', 'Invalid Leave Type selected.');
            oecrm_leave_type_redirect();
        }

        $typeStmt = mysqli_prepare($conn, 'SELECT id,name FROM leavetypetbl WHERE id=? LIMIT 1');
        mysqli_stmt_bind_param($typeStmt, 'i', $leaveTypeId);
        mysqli_stmt_execute($typeStmt);
        $leaveType = mysqli_fetch_assoc(mysqli_stmt_get_result($typeStmt));
        mysqli_stmt_close($typeStmt);

        if (!$leaveType) {
            oecrm_leave_type_flash('warning', 'Leave Type not found.');
            oecrm_leave_type_redirect();
        }

        $requestCount = oecrm_leave_type_table_exists($conn, 'leave_requests')
            ? oecrm_leave_type_count($conn, 'SELECT COUNT(*) total FROM leave_requests WHERE leave_type_id=?', 'i', $leaveTypeId)
            : 0;
        $balanceCount = oecrm_leave_type_table_exists($conn, 'employee_leave_balances')
            ? oecrm_leave_type_count($conn, 'SELECT COUNT(*) total FROM employee_leave_balances WHERE leave_type_id=? AND (opening_balance<>0 OR credited<>0 OR used<>0 OR pending<>0 OR adjusted<>0)', 'i', $leaveTypeId)
            : 0;

        if ($requestCount > 0 || $balanceCount > 0) {
            oecrm_leave_type_flash('warning', 'This Leave Type is already used in leave requests or balances, so it cannot be deleted.');
            oecrm_leave_type_redirect();
        }

        mysqli_begin_transaction($conn);
        if (oecrm_leave_type_table_exists($conn, 'employee_leave_balances')) {
            $balanceDelete = mysqli_prepare($conn, 'DELETE FROM employee_leave_balances WHERE leave_type_id=?');
            mysqli_stmt_bind_param($balanceDelete, 'i', $leaveTypeId);
            mysqli_stmt_execute($balanceDelete);
            mysqli_stmt_close($balanceDelete);
        }
        if (oecrm_leave_type_table_exists($conn, 'leave_policies')) {
            $policyDelete = mysqli_prepare($conn, 'DELETE FROM leave_policies WHERE leave_type_id=?');
            mysqli_stmt_bind_param($policyDelete, 'i', $leaveTypeId);
            mysqli_stmt_execute($policyDelete);
            mysqli_stmt_close($policyDelete);
        }
        $deleteStmt = mysqli_prepare($conn, 'DELETE FROM leavetypetbl WHERE id=?');
        mysqli_stmt_bind_param($deleteStmt, 'i', $leaveTypeId);
        $deleted = mysqli_stmt_execute($deleteStmt);
        $affectedRows = mysqli_stmt_affected_rows($deleteStmt);
        mysqli_stmt_close($deleteStmt);

        if (!$deleted || $affectedRows < 1) {
            mysqli_rollback($conn);
            oecrm_leave_type_flash('danger', 'Leave Type could not be deleted.');
            oecrm_leave_type_redirect();
        }

        mysqli_commit($conn);
        oecrm_audit($conn, 'leave_types', 'delete', 'leave_type', $leaveTypeId, 'Leave type deleted', $leaveType, null);
        oecrm_leave_type_flash('success', 'Leave Type deleted successfully.');
        oecrm_leave_type_redirect();
    }
}

$leaveTypeFlash = $_SESSION['leave_type_flash'] ?? null;
unset($_SESSION['leave_type_flash']);
include 'header.php';
$leaveTypes = mysqli_query($conn, 'SELECT id,name FROM leavetypetbl ORDER BY name');
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($leaveTypeFlash['message'])): ?>
                <div class="alert alert-<?php echo oecrm_h($leaveTypeFlash['type']); ?> alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo oecrm_h($leaveTypeFlash['message']); ?>
                </div>
            <?php endif; ?>
            <div class="dataTablesbox">
                <form role="form" method="POST" action="manageLeaveType.php" novalidate>
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Leave Type <span class="text-danger">*</span></label>
                            <input class="form-control" value="" type="text" name="name" maxlength="100" required>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <br>
                        <div class="form-group" align="right">
                            <button class="btn btn-primary" type="submit" name="addLeaveType" value="1" style="margin-top: 7px;"><i class="fa fa-plus"></i> Add Leave Type</button>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <br>
                        <div class="form-group" align="left">
                            <button class="btn btn-default cancel_btn" type="reset" style="margin-top: 7px;">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="manage_designation">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Leave Type Table</div>
                    <div class="panel-body table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Title</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($leaveTypes && mysqli_num_rows($leaveTypes) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($leaveTypes)): ?>
                                        <tr>
                                            <td><?php echo (int) $row['id']; ?></td>
                                            <td><?php echo oecrm_h($row['name']); ?></td>
                                            <td class="center" align="center">
                                                <form method="post" action="manageLeaveType.php" class="oecrm-inline-delete" style="display:inline;">
                                                    <?php echo oecrm_csrf_field(); ?>
                                                    <input type="hidden" name="leave_type_id" value="<?php echo (int) $row['id']; ?>">
                                                    <button type="submit" name="deleteLeaveType" value="1" class="btn btn-link p-0 oecrm-action-btn oecrm-action-danger" data-confirm="Are you sure you want to delete this Leave Type?" title="Delete">
                                                        <i class="fa fa-trash-o"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3">Nothing to display</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



</div>
<!-- /#wrapper -->

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
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: false,
            "paging": false,
            "ordering": false,
            "info": false,
            "searching": false
        });
    });
</script>

<?php include 'footer.php'; ?>
