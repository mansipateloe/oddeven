<?php
ob_start();
$active_menu = 'lead';
require_once __DIR__ . '/../security.php';
include __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();

function oecrm_followup_type_flash($type, $message)
{
    $_SESSION['followup_type_flash'] = ['type' => $type, 'message' => $message];
}

function oecrm_followup_type_redirect()
{
    header('Location: manageFollowupType.php');
    exit;
}

function oecrm_followup_type_can_manage($conn)
{
    return oecrm_is_super_admin()
        || oecrm_can($conn, 'clients', 'create')
        || oecrm_can($conn, 'clients', 'edit')
        || oecrm_can($conn, 'client_communications', 'create')
        || oecrm_can($conn, 'client_communications', 'edit')
        || oecrm_can($conn, 'client_communications', 'view')
        || oecrm_legacy_can($conn, 'lead')
        || oecrm_legacy_can($conn, 'add_lead')
        || oecrm_legacy_can($conn, 'view_lead');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addFollowupType'])) {
    oecrm_require_csrf();

    if (!oecrm_followup_type_can_manage($conn)) {
        http_response_code(403);
        exit('You do not have permission to manage follow-up types.');
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $name = preg_replace('/\s+/', ' ', $name);

    if ($name === '') {
        oecrm_followup_type_flash('danger', 'Follow-up type is required.');
        oecrm_followup_type_redirect();
    }

    $check = mysqli_prepare($conn, 'SELECT id FROM followup_type_tbl WHERE LOWER(name)=LOWER(?) LIMIT 1');
    if (!$check) {
        oecrm_followup_type_flash('danger', 'Follow-up type could not be checked.');
        oecrm_followup_type_redirect();
    }
    mysqli_stmt_bind_param($check, 's', $name);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);

    if ($exists) {
        oecrm_followup_type_flash('warning', 'Follow-up type already exists.');
        oecrm_followup_type_redirect();
    }

    $stmt = mysqli_prepare($conn, 'INSERT INTO followup_type_tbl(name) VALUES(?)');
    if (!$stmt) {
        oecrm_followup_type_flash('danger', 'Follow-up type could not be saved.');
        oecrm_followup_type_redirect();
    }
    mysqli_stmt_bind_param($stmt, 's', $name);
    $saved = mysqli_stmt_execute($stmt);
    $newId = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if (!$saved) {
        oecrm_followup_type_flash('danger', 'Follow-up type could not be saved.');
        oecrm_followup_type_redirect();
    }

    oecrm_audit($conn, 'client_communications', 'create', 'followup_type', $newId, 'Follow-up type created', null, ['name' => $name]);
    oecrm_followup_type_flash('success', 'Follow-up type added successfully.');
    oecrm_followup_type_redirect();
}

$followupTypeFlash = $_SESSION['followup_type_flash'] ?? null;
unset($_SESSION['followup_type_flash']);
include 'header.php';
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($followupTypeFlash['message'])): ?>
                <div class="alert alert-<?php echo oecrm_h($followupTypeFlash['type']); ?> alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo oecrm_h($followupTypeFlash['message']); ?>
                </div>
            <?php endif; ?>
            <div class="dataTablesbox">
                <form role="form" method="POST" action="manageFollowupType.php" novalidate>
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Followup Type <span class="text-danger">*</span></label>
                            <input class="form-control" value="" required maxlength="100" type="text" name="name">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <br>
                        <div class="form-group" align="right">
                            <button class="btn btn-primary" type="submit" name="addFollowupType" value="1" style="margin-top: 7px;"><i class="fa fa-plus"></i> Add Followup Type</button>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <br>
                        <div class="form-group" align="left">
                            <a class="btn btn-default cancel_btn" href="followup.php" style="margin-top: 7px;">Cancel</a>
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
                    <div class="panel-heading">Followup Type Table</div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Title</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn, 'SELECT id,name FROM followup_type_tbl ORDER BY name');
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . (int) $row['id'] . "</td>";
                                            echo "<td>" . oecrm_h($row['name']) . "</td>";
                                            echo '<td class="center" align="center"><form method="post" action="deleteFollowupType.php" style="display:inline;">' . oecrm_csrf_field() . '<input type="hidden" name="deleteFollowupType" value="' . (int) $row['id'] . '"><button type="submit" class="btn btn-link oecrm-action-btn oecrm-action-danger" style="padding:0;border:0;" data-confirm="Are you sure you want to delete this Follow-up Type?" title="Delete"><i class="fa fa-trash-o"></i></button></form></td>';
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo '<tr><td colspan="3">Nothing to display</td></tr>';
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
</div>
<?php include 'footer.php'; ?>
