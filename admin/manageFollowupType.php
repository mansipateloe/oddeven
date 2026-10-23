<?php
ob_start();
$active_menu = 'lead';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();

function oecrm_followup_type_flash($type, $message)
{
    $_SESSION['followup_type_flash'] = $message;
    $_SESSION['followup_type_flash_type'] = $type === 'danger' ? 'error' : $type;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['addFollowupType']) || isset($_POST['updateFollowupType']))) {
    oecrm_require_csrf();

    if (!oecrm_followup_type_can_manage($conn)) {
        http_response_code(403);
        exit('You do not have permission to manage follow-up types.');
    }

    $editing = isset($_POST['updateFollowupType']);
    $id = $editing ? oecrm_int_param($_POST, 'id') : 0;
    $old = null;
    if ($editing) {
        $lookup = mysqli_prepare($conn, 'SELECT id, name FROM followup_type_tbl WHERE id=?');
        mysqli_stmt_bind_param($lookup, 'i', $id);
        mysqli_stmt_execute($lookup);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($lookup));
        mysqli_stmt_close($lookup);
        if (!$old) {
            oecrm_followup_type_flash('danger', 'Follow-up type not found.');
            oecrm_followup_type_redirect();
        }
    }
    $name = trim((string) ($_POST['name'] ?? ''));
    $name = preg_replace('/\s+/', ' ', $name);

    if ($name === '') {
        oecrm_followup_type_flash('danger', 'Follow-up type is required.');
        oecrm_followup_type_redirect();
    }

    $check = mysqli_prepare($conn, 'SELECT id FROM followup_type_tbl WHERE LOWER(TRIM(name))=LOWER(?) AND id<>? LIMIT 1');
    if (!$check) {
        oecrm_followup_type_flash('danger', 'Follow-up type could not be checked.');
        oecrm_followup_type_redirect();
    }
    mysqli_stmt_bind_param($check, 'si', $name, $id);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);

    if ($exists) {
        oecrm_followup_type_flash('warning', 'Follow-up type already exists.');
        oecrm_followup_type_redirect();
    }

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, $editing ? 'UPDATE followup_type_tbl SET name=? WHERE id=?' : 'INSERT INTO followup_type_tbl(name) VALUES(?)');
        if ($editing) {
            mysqli_stmt_bind_param($stmt, 'si', $name, $id);
        } else {
            mysqli_stmt_bind_param($stmt, 's', $name);
        }
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Follow-up type could not be saved.');
        }
        $savedId = $editing ? $id : (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        if ($editing) {
            // Existing follow-ups store the type name rather than its ID.
            $stmt = mysqli_prepare($conn, 'UPDATE lead_followup SET followup_type=? WHERE followup_type=?');
            mysqli_stmt_bind_param($stmt, 'ss', $name, $old['name']);
            if (!mysqli_stmt_execute($stmt)) {
                throw new RuntimeException('Follow-up records could not be updated.');
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_commit($conn);
    } catch (Exception $exception) {
        mysqli_rollback($conn);
        oecrm_followup_type_flash('danger', 'Follow-up type could not be saved.');
        oecrm_followup_type_redirect();
    }
    oecrm_audit($conn, 'client_communications', $editing ? 'update' : 'create', 'followup_type', $savedId, $editing ? 'Follow-up type updated' : 'Follow-up type created', $old, ['name' => $name]);
    oecrm_followup_type_flash('success', $editing ? 'Follow-up type updated successfully.' : 'Follow-up type added successfully.');
    oecrm_followup_type_redirect();
}

$editType = null;
if (!empty($_GET['edit']) && oecrm_followup_type_can_manage($conn)) {
    $editId = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, 'SELECT id, name FROM followup_type_tbl WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $editType = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$editType) oecrm_followup_type_flash('warning', 'Follow-up type not found.');
}
include 'header.php';

$followupTypeFlash = $_SESSION['followup_type_flash'] ?? '';
$followupTypeFlashType = $_SESSION['followup_type_flash_type'] ?? 'info';
unset($_SESSION['followup_type_flash'], $_SESSION['followup_type_flash_type']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($followupTypeFlash): ?>
        <div class="alert alert-<?php echo $followupTypeFlashType === 'success' ? 'success' : ($followupTypeFlashType === 'warning' ? 'warning' : ($followupTypeFlashType === 'error' ? 'danger' : 'info')); ?>" data-master-feedback role="status" style="margin-top: 20px; margin-bottom: 0;">
            <?php echo oecrm_h($followupTypeFlash); ?>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="dataTablesbox">
                <form id="followupTypeForm" role="form" method="POST" action="manageFollowupType.php" novalidate>
                    <?php echo oecrm_csrf_field(); ?>
                    <?php if ($editType): ?><input type="hidden" name="id" value="<?php echo (int) $editType['id']; ?>"><?php endif; ?>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="followupTypeName"><?php echo $editType ? 'Edit Followup Type' : 'Followup Type'; ?></label>
                            <input id="followupTypeName" class="form-control" value="<?php echo oecrm_h($editType['name'] ?? ''); ?>" type="text" name="name" placeholder="Enter follow-up type" required>
                        </div>
                    </div>
                    <div class="col-lg-2" style="margin-left: 20px;">
                        <br>
                        <div class="form-group" align="right">
                            <input class="btn btn-primary" type="submit" name="<?php echo $editType ? 'updateFollowupType' : 'addFollowupType'; ?>" value="<?php echo $editType ? 'Update Followup Type' : 'Add Followup Type'; ?>" style="margin-top: 7px;">
                        </div>
                    </div>
                    <div class="col-lg-2" style="margin-left: 10px;">
                        <br>
                        <div class="form-group" align="left">
                            <a class="btn btn-default cancel_btn" href="<?php echo $editType ? 'manageFollowupType.php' : 'followup.php'; ?>" style="margin-top: 7px;">Cancel</a>
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
                                            <tr role="row">
                                                <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">No.</th>
                                                <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">Title</th>
                                                <!-- <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">Notice</th> -->
                                                <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 110px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $qry = "SELECT * FROM followup_type_tbl ORDER BY id DESC";
                                            $result = mysqli_query($conn, $qry);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr class='gradeA even' role='row'>";
                                                    echo "<td class='sorting_1'>" . $row['id'] . "</td>";
                                                    echo "<td>" . oecrm_h($row['name']) . "</td>";
                                                    echo '<td class="center" align="center">';
                                                    if (oecrm_followup_type_can_manage($conn)) {
                                                        echo '<a class="icon-action" href="manageFollowupType.php?edit=' . (int)$row['id'] . '" title="Edit follow-up type" aria-label="Edit follow-up type"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                                                    }
                                                    echo '<form method="post" action="deleteFollowupType.php" style="display:inline;" data-confirm="Delete this follow-up type?">' . oecrm_csrf_field() . '<input type="hidden" name="deleteFollowupType" value="' . (int)$row["id"] . '"><button type="submit" class="btn btn-link delete-followup-type" title="Delete follow-up type" style="padding:0;border:0;"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></button></form></td>';
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3'>";
                                                echo "Nothing to display";
                                                echo "</td></tr>";
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
<style>
    #followupTypeForm .is-invalid {
        border-color: #d9534f !important;
        box-shadow: 0 0 0 0.2rem rgba(217, 83, 79, 0.15) !important;
    }
    .field-error {
        display: block;
        color: #d9534f;
        font-size: 12px;
        margin-top: 6px;
        line-height: 1.3;
    }
</style>

<script>
    (function () {
        var form = document.getElementById('followupTypeForm');
        if (!form) return;

        var input = form.querySelector('input[name="name"]');
        if (!input) return;

        function showError(message) {
            input.classList.add('is-invalid');
            var group = input.closest('.form-group');
            if (!group) return;
            var error = group.querySelector('.field-error');
            if (!error) {
                error = document.createElement('div');
                error.className = 'field-error';
                group.appendChild(error);
            }
            error.textContent = message;
        }

        function clearError() {
            input.classList.remove('is-invalid');
            var group = input.closest('.form-group');
            if (!group) return;
            var error = group.querySelector('.field-error');
            if (error) error.remove();
        }

        input.addEventListener('input', function () {
            if (input.value.trim() !== '') clearError();
        });

        form.addEventListener('submit', function (event) {
            if (input.value.trim() === '') {
                event.preventDefault();
                showError('Follow-up type is required.');
                input.focus();
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            clearError();
        });
    })();

</script>
<script src="../js/oecrm-master-feedback.js?v=20261015-1"></script>
<?php include 'footer.php'; ?>
