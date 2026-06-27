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
            <form method="post" action="validation.php" class="exit-init-form">
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
                    <?php while ($designation = mysqli_fetch_assoc($designations)): ?>
                        <tr>
                            <td><?php echo (int) $designation['id']; ?></td>
                            <td><?php echo oecrm_h($designation['designation']); ?></td>
                            <td>
                                <form method="post" action="deleteDesignation.php" style="display:inline;">
                                    <?php echo oecrm_csrf_field(); ?>
                                    <input type="hidden" name="deleteDesignation" value="<?php echo (int) $designation['id']; ?>">
                                    <button type="submit" class="icon-action" data-confirm="Delete this designation?" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
