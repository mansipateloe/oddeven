<?php include 'header.php'; $flash = $_SESSION['role_flash'] ?? ''; unset($_SESSION['role_flash']); ?>
<div id="page-wrapper">
    <div class="">
        <div class="panel panel-default">
            <div class="panel-heading panel-box">
                <h4>Add Role</h4>
            </div>
            <div class="panel-body manage_project">    
                <?php if ($flash): ?>
                    <div class="alert alert-info"><?php echo oecrm_h($flash); ?></div>
                <?php endif; ?>
                <form id="leadForm" method="post" action="validation.php"  enctype="multipart/form-data">
                    <?php echo oecrm_csrf_field(); ?>
                    <?php
                        if(isset($_REQUEST['id']))
                        {
                            $roleId = (int) $_REQUEST['id'];
                            $sel_role_details=mysqli_query($conn,"SELECT * FROM user_type WHERE id=".$roleId);
                            $fet_role_details=mysqli_fetch_assoc($sel_role_details);
                            echo '<input type="hidden" id="id" name="id" value="'.$roleId.'">';
                        }
                    ?>
                    <!-- Repeat the following block for each form field -->
                    <div class="form-group col-sm-12">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" value="<?php if(isset($_REQUEST['id']) && isset($fet_role_details['name']) ){echo oecrm_h($fet_role_details['name']);}?>" placeholder="Name" id="name" name="name" required>
                    </div>

                    
                    <div class="form-group col-sm-12">
                        <input type="submit" value="Submit" name="roleSave" class="btn btn-primary">
                        <a href='all_user_roles.php' name="" class="btn btn-cancel">Cancel</a>
                    </div>
                </form>
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
<script src="../dist/js/sb-admin-2.js"></script>
<!-- Custom JavaScript for form validation -->
<script>
    // Example of basic form validation using JavaScript
    // document.getElementById('leadForm').addEventListener('submit', function(event) {
    //     var valid = true;

    //     // Check each required field
    //     var requiredFields = ['leadDate', 'executiveName', 'leadType', 'followupType', 'remarks'];
    //     requiredFields.forEach(function(field) {
    //         var value = document.getElementById(field).value.trim();
    //         if (value === '') {
    //             valid = false;
    //             alert('Please fill in all required fields.');
    //             event.preventDefault();
    //         }
    //     });

    //     // Additional validation logic can be added here

    //     if (!valid) {
    //         event.preventDefault();
    //     }
    // });
</script>



<?php
include 'footer.php';
?>
