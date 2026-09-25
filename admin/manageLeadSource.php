<?php 

include 'header.php';

if (isset($_POST['addLeadSource'])) {
    oecrm_require_csrf();
    oecrm_require_permission($conn, 'clients', 'create');

    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $_SESSION['lead_source_flash'] = 'Lead source name is required.';
        $_SESSION['lead_source_flash_type'] = 'error';
        header('Location: manageLeadSource.php');
        exit;
    }

    $name = mysqli_real_escape_string($conn, $name);

    $qry = "INSERT INTO `lead_source_tbl`(`id`, `name`) VALUES (NULL, '$name')";
    if (mysqli_query($conn, $qry)) {
        $_SESSION['lead_source_flash'] = 'Lead source added successfully.';
        $_SESSION['lead_source_flash_type'] = 'success';
        header('Location: manageLeadSource.php');
        exit;
    }

    $_SESSION['lead_source_flash'] = 'Lead source could not be saved.';
    $_SESSION['lead_source_flash_type'] = 'error';
    header('Location: manageLeadSource.php');
    exit;
}

$leadSourceFlash = $_SESSION['lead_source_flash'] ?? '';
$leadSourceFlashType = $_SESSION['lead_source_flash_type'] ?? 'info';
unset($_SESSION['lead_source_flash'], $_SESSION['lead_source_flash_type']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($leadSourceFlash): ?>
        <div class="alert alert-<?php echo $leadSourceFlashType === 'success' ? 'success' : ($leadSourceFlashType === 'warning' ? 'warning' : ($leadSourceFlashType === 'error' ? 'danger' : 'info')); ?>" style="margin-top: 20px; margin-bottom: 0;">
            <?php echo oecrm_h($leadSourceFlash); ?>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="dataTablesbox">
                <form id="leadSourceForm" role="form" method="POST" action="" novalidate>
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Lead Source</label>
                            <input id="leadSourceName" class="form-control" value="" type="text" name="name" placeholder="Enter lead source name">
                        </div>

                    </div>
                    <div class="col-lg-2">
                        <br>
                        <div class="form-group" align="right">
                            <input class="btn btn-primary" type="submit" name="addLeadSource" value="Add Lead Source" style="margin-top: 7px;">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <br>
                        <div class="form-group" align="left">
                            <input class="btn btn-danger cancel_btn" type="reset" value="Cancel" style="margin-top: 7px;">
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
                        Lead Source Table
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
                                            $qry = "SELECT * FROM lead_source_tbl ORDER BY id DESC";
                                            $result = mysqli_query($conn, $qry);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr class='gradeA even' role='row'>";
                                                    echo "<td class='sorting_1'>" . $row['id'] . "</td>";
                                                    echo "<td>" . $row['name'] . "</td>";
                                                    echo '<td class="center" align="center"><form method="post" action="deleteLeadSource.php" class="delete-lead-source-form" style="display:inline;">' . oecrm_csrf_field() . '<input type="hidden" name="deleteLeadSource" value="' . (int)$row["id"] . '"><button type="submit" class="btn btn-link delete-lead-source-btn" style="padding:0;border:0;" data-confirm="Are you sure you want to delete this lead source?"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></button></form></td>';
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
    .lead-source-form .is-invalid,
    #leadSourceForm .is-invalid {
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
    var form = document.getElementById('leadSourceForm');
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
            showError('Lead source name is required.');
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        clearError();
    });
})();

<?php if ($leadSourceFlash): ?>
Swal.fire({
    title: '<?php echo $leadSourceFlashType === 'success' ? 'Success' : ($leadSourceFlashType === 'warning' ? 'Warning' : 'Error'); ?>',
    text: '<?php echo addslashes($leadSourceFlash); ?>',
    icon: '<?php echo $leadSourceFlashType === 'success' ? 'success' : ($leadSourceFlashType === 'warning' ? 'warning' : 'error'); ?>',
    confirmButtonText: 'OK'
});
<?php endif; ?>

(function () {
    document.querySelectorAll('.delete-lead-source-btn').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            var form = button.closest('form');
            if (!form) return;

            Swal.fire({
                title: 'Are you sure?',
                text: button.getAttribute('data-confirm') || 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d9534f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
})();
</script>
<?php include 'footer.php'; ?>
