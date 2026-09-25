<?php
$active_menu = 'employees';
include 'header.php';

function oecrm_designation_can_manage($conn, $action = 'view')
{
    return oecrm_is_super_admin()
        || oecrm_can($conn, 'employees', $action)
        || oecrm_legacy_can($conn, 'employee')
        || oecrm_legacy_can($conn, 'settings');
}

if (!oecrm_designation_can_manage($conn, 'view')) {
    $_SESSION['designation_flash'] = 'You do not have permission to view designations.';
    header('Location:dashboard.php');
    exit;
}

if (isset($_POST['addDesignation'])) {
    oecrm_require_csrf();
    oecrm_require_permission($conn, 'employees', 'create');

    $designation = trim($_POST['designation'] ?? '');
    if ($designation === '') {
        $_SESSION['designation_flash'] = 'Designation is required.';
        header('Location: manageDesignation.php');
        exit;
    }

    $check = mysqli_prepare($conn, 'SELECT id FROM designation WHERE LOWER(designation)=LOWER(?) LIMIT 1');
    mysqli_stmt_bind_param($check, 's', $designation);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);
    if ($exists) {
        $_SESSION['designation_flash'] = 'Designation already exists.';
        header('Location: manageDesignation.php');
        exit;
    }

    $stmt = mysqli_prepare($conn, 'INSERT INTO designation (designation) VALUES (?)');
    mysqli_stmt_bind_param($stmt, 's', $designation);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION['designation_flash'] = 'Designation added successfully.';
    header('Location: manageDesignation.php');
    exit;
}

$designations = mysqli_query($conn, 'SELECT id, designation FROM designation ORDER BY id DESC');
$flash = $_SESSION['designation_flash'] ?? '';
$warning = !empty($_SESSION['designation_warning']);
unset($_SESSION['designation_flash'], $_SESSION['designation_warning']);
?>

<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?>
        <div class="alert alert-info"><?php echo oecrm_h($flash); ?></div>
    <?php endif; ?>

    <div class="panel panel-default">
        <div class="panel-heading">Manage Designation</div>
        <div class="panel-body">
            <style>
                .designation-form .is-invalid {
                    border-color: #d9534f !important;
                    box-shadow: 0 0 0 0.2rem rgba(217, 83, 79, 0.15) !important;
                }
                .designation-form .field-error {
                    display: block;
                    color: #d9534f;
                    font-size: 12px;
                    margin-top: 6px;
                    line-height: 1.3;
                }
            </style>

            <form method="post" action="" class="designation-form" id="designationForm" novalidate>
                <?php echo oecrm_csrf_field(); ?>
                <div class="form-group">
                    <label for="designation">Designation</label>
                    <input id="designation" class="form-control" type="text" name="designation" placeholder="Enter designation" required>
                </div>
                <button class="btn btn-primary" type="submit" name="addDesignation" value="1">
                    <i class="fa fa-plus"></i> Add Designation
                </button>
                <a class="btn btn-default" href="manageDesignation.php">Cancel</a>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Designation Table</div>
        <div class="panel-body table-responsive">
            <table class="table table-striped table-bordered table-hover foundation-table" id="designationTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($designations && mysqli_num_rows($designations) > 0): ?>
                        <?php $counter = 0; ?>
                        <?php while ($designation = mysqli_fetch_assoc($designations)): ?>
                            <tr>
                                <td><?php echo ++$counter; ?></td>
                                <td><?php echo oecrm_h($designation['designation']); ?></td>
                                <td>
                                    <?php if (oecrm_designation_can_manage($conn, 'delete') || oecrm_designation_can_manage($conn, 'edit')): ?>
                                        <form method="post" action="deleteDesignation.php" style="display:inline;">
                                            <?php echo oecrm_csrf_field(); ?>
                                            <input type="hidden" name="deleteDesignation" value="<?php echo (int) $designation['id']; ?>">
                                            <button type="submit" class="icon-action" data-confirm="Delete this designation?" title="Delete"><i class="fa fa-trash"></i></button>
                                        </form>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center">No designations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    var form = document.getElementById('designationForm');
    if (!form) return;

    var input = form.querySelector('input[name="designation"]');
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
            showError('Designation is required.');
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        clearError();
    });
})();

<?php if ($warning): ?>
Swal.fire({
    title: 'Warning',
    text: '<?php echo addslashes($flash); ?>',
    icon: 'warning',
    confirmButtonText: 'OK'
});
<?php endif; ?>
</script>

<?php include 'footer.php'; ?>
