<?php include 'header.php'; ?>
<style>
    .text-left
    {
        text-align: left!important;
    }
</style>
<div id="page-wrapper">
    <div class="">
        <div class="panel panel-default">
            <div class="panel-heading panel-box">
                <h4>Update Access</h4>
            </div>
            <div class="panel-body manage_project">    
                <form id="leadForm" method="post" action="leads.php"  enctype="multipart/form-data">
                <?php
                    if (isset($_REQUEST['id'])) {
                        $sel_file_details = mysqli_query($conn, "SELECT * from user_type WHERE id='" . $_REQUEST['id'] . "' ");
                        $fetch = mysqli_fetch_assoc($sel_file_details);
                        echo '<input type="hidden" name="utype" value="' . $_REQUEST['id'] . '">';
                    } ?>
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th class="text-left">Permission</th>
                                <th class="text-center">Create</th>
                                <th class="text-center">Update</th>
                                <th class="text-center">View</th>
                                <th class="text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sel_pmenu = "SELECT * from admin_menu where pmenu='0' AND is_deleted='0'";
                            $que_pmenu = mysqli_query($conn, $sel_pmenu)or die(mysqli_error($conn));
                            while ($fet_pmenu = mysqli_fetch_array($que_pmenu)) 
                            {
                                $mname = $fet_pmenu['mname'];
                                $is_access = check_is_access_new($mname, $_REQUEST['id']);
                            ?>
                                <tr>
                                    <td  class="text-left">
                                        <input type="checkbox" onclick="select_child_checkbox(this)" <?php if ($is_access == 1) {
                                            echo "checked";
                                        } ?> name="<?php echo $fet_pmenu['mname']; ?>" id="chk_<?php echo $fet_pmenu['mname']; ?>" value="1">
                                        <label for="chk_<?php echo $fet_pmenu['mname']; ?>"><?php echo $fet_pmenu['mtitle']; ?></label>
                                    </td>
                                    <?php
                                    $sel_smenu = "SELECT * from admin_menu where pmenu='" . $fet_pmenu['mid'] . "' AND is_deleted='0' LIMIT 4";
                                    $que_smenu = mysqli_query($conn, $sel_smenu);
                                    if (mysqli_num_rows($que_smenu) > 0) {
                                        $mname_add = "add_" . $fet_pmenu['mname'];
                                        $is_access_add = check_is_access_new($mname_add, $_REQUEST['id']);
                                        $mname_update = "update_" . $fet_pmenu['mname'];
                                        $is_access_update = check_is_access_new($mname_update, $_REQUEST['id']);
                                        $mname_view = "view_" . $fet_pmenu['mname'];
                                        $is_access_view = check_is_access_new($mname_view, $_REQUEST['id']);
                                        $mname_delete = "delete_" . $fet_pmenu['mname'];
                                        $is_access_delete = check_is_access_new($mname_delete, $_REQUEST['id']);
                                    ?>
                                        <td class="text-center"><input onclick="select_parent_checkbox(this)" data-parent="<?php echo $fet_pmenu['mname']; ?>" <?php if ($is_access_add == 1) { echo "checked";} ?> type="checkbox" name="add_<?php echo $fet_pmenu['mname']; ?>" id="chk_add_<?php echo $fet_pmenu['mname']; ?>" value="1"></td>
                                        <td class="text-center"><input onclick="select_parent_checkbox(this)" data-parent="<?php echo $fet_pmenu['mname']; ?>" <?php if ($is_access_update == 1) {echo "checked";} ?> type="checkbox" name="update_<?php echo $fet_pmenu['mname']; ?>" id="chk_update_<?php echo $fet_pmenu['mname']; ?>" value="1"></td>
                                        <td class="text-center"><input onclick="select_parent_checkbox(this)" data-parent="<?php echo $fet_pmenu['mname']; ?>" <?php if ($is_access_view == 1) {echo "checked";} ?> type="checkbox" name="view_<?php echo $fet_pmenu['mname']; ?>" id="chk_view_<?php echo $fet_pmenu['mname']; ?>" value="1"></td>
                                        <td class="text-center"><input onclick="select_parent_checkbox(this)" data-parent="<?php echo $fet_pmenu['mname']; ?>" <?php if ($is_access_delete == 1) {echo "checked";} ?> type="checkbox" name="delete_<?php echo $fet_pmenu['mname']; ?>" id="chk_delete_<?php echo $fet_pmenu['mname']; ?>" value="1"></td>
                                    <?php
                                    } else {
                                    ?>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    <?php
                                    }
                                    ?>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="form-group col-sm-12">
                        <input type="submit" value="Submit" name="roleAccessSave" class="btn btn-primary">
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