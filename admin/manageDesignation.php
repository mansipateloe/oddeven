<?php
$active_menu = 'employees';
include 'header.php';
oecrm_require_permission($conn, 'employees', 'view');

$designations = mysqli_query($conn, 'SELECT id,designation FROM designation ORDER BY designation');
$flash = $_SESSION['designation_flash'] ?? '';
unset($_SESSION['designation_flash']);
?>

<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?>
        <div class="alert alert-info"><?php echo oecrm_h($flash); ?></div>
    <?php endif; ?>

    <div class="panel panel-default">
        <div class="panel-heading">Manage Designation</div>
        <div class="panel-body">
            <form method="post" class="exit-init-form">
                <?php echo oecrm_csrf_field(); ?>
                <div class="form-group">
                    <label for="designation">Designation</label>
                    <input id="designation" class="form-control" type="text" name="designation" placeholder="Enter designation" required>
                </div>
                <button class="btn btn-primary" type="submit" name="addDesignation" value="1">
                    <i class="fa fa-plus"></i> Add Designation
                </button>
                <button class="btn btn-default" type="reset">Cancel</button>
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
                    <?php while ($designation = mysqli_fetch_assoc($designations)): ?>
                        <tr>
                            <td><?php echo (int) $designation['id']; ?></td>
                            <td><?php echo oecrm_h($designation['designation']); ?></td>
                            <td>
                                <a class="icon-action" href="deleteDesignation.php?deleteDesignation=<?php echo (int) $designation['id']; ?>" data-confirm="Delete this designation?" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
