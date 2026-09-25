<?php 

include 'header.php';

if (isset($_POST['addFollowupType'])) {
    oecrm_require_csrf();
    oecrm_require_permission($conn, 'clients', 'create');

    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $_SESSION['followup_type_flash'] = 'Follow-up type is required.';
        $_SESSION['followup_type_flash_type'] = 'error';
        header('Location: manageFollowupType.php');
        exit;
    }

    $check = mysqli_prepare($conn, 'SELECT id FROM followup_type_tbl WHERE name=? LIMIT 1');
    mysqli_stmt_bind_param($check, 's', $name);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);
    if ($exists) {
        $_SESSION['followup_type_flash'] = 'Follow-up type already exists.';
        $_SESSION['followup_type_flash_type'] = 'warning';
        header('Location: manageFollowupType.php');
        exit;
    }

    $name = mysqli_real_escape_string($conn, $name);
    $qry = "INSERT INTO `followup_type_tbl`(`id`, `name`) VALUES (NULL,'$name')";

    if (mysqli_query($conn, $qry)) {
        $_SESSION['followup_type_flash'] = 'Follow-up type added successfully.';
        $_SESSION['followup_type_flash_type'] = 'success';
        header('Location: manageFollowupType.php');
        exit;
    }

    $_SESSION['followup_type_flash'] = 'Follow-up type could not be saved.';
    $_SESSION['followup_type_flash_type'] = 'error';
    header('Location: manageFollowupType.php');
    exit;
}

$followupTypeFlash = $_SESSION['followup_type_flash'] ?? '';
$followupTypeFlashType = $_SESSION['followup_type_flash_type'] ?? 'info';
unset($_SESSION['followup_type_flash'], $_SESSION['followup_type_flash_type']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($followupTypeFlash): ?>
        <div class="alert alert-<?php echo $followupTypeFlashType === 'success' ? 'success' : ($followupTypeFlashType === 'warning' ? 'warning' : ($followupTypeFlashType === 'error' ? 'danger' : 'info')); ?>" style="margin-top: 20px; margin-bottom: 0;">
            <?php echo oecrm_h($followupTypeFlash); ?>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="dataTablesbox">
                <form id="followupTypeForm" role="form" method="POST" action="" novalidate>
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Followup Type</label>
                            <input id="followupTypeName" class="form-control" value="" type="text" name="name" placeholder="Enter follow-up type">
                        </div>

                    </div>
                    <div class="col-lg-2" style="margin-left: 20px;">
                        <br>
                        <div class="form-group" align="right">
                            <input class="btn btn-primary" type="submit" name="addFollowupType" value="Add Followup Type" style="margin-top: 7px; margin-left: 10px;">
                        </div>
                    </div>
                    <div class="col-lg-2" style="margin-left: 10px;">
                        <br>
                        <div class="form-group" align="left">
                            <a class="btn btn-danger cancel_btn" href="followup.php" style="margin-top: 7px; margin-left: 10px;">Cancel</a>
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
                    <div class="panel-heading">
                        Followup Type Table
                    </div>
                    <!-- /.panel-heading -->
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
                                                    echo "<td>" . $row['name'] . "</td>";
                                                    echo '<td class="center" align="center"><form method="post" action="deleteFollowupType.php" style="display:inline;">' . oecrm_csrf_field() . '<input type="hidden" name="deleteFollowupType" value="' . (int)$row["id"] . '"><button type="submit" class="btn btn-link delete-followup-type" data-confirm="Are you sure you want to delete this follow-up type?" style="padding:0;border:0;"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></button></form></td>';
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td>";
                                                echo "Nothing to display";
                                                echo "</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <?php if ($followupTypeFlash): ?>
    Swal.fire({
        title: '<?php echo $followupTypeFlashType === 'success' ? 'Success' : ($followupTypeFlashType === 'warning' ? 'Warning' : 'Error'); ?>',
        text: '<?php echo addslashes($followupTypeFlash); ?>',
        icon: '<?php echo $followupTypeFlashType === 'success' ? 'success' : ($followupTypeFlashType === 'warning' ? 'warning' : 'error'); ?>',
        confirmButtonText: 'OK'
    });
    <?php endif; ?>

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-followup-type').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                var form = this.closest('form');

                Swal.fire({
                    title: 'Delete Followup Type?',
                    text: button.getAttribute('data-confirm') || 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
<?php include 'footer.php'; ?>
