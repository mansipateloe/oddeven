<?php
$active_menu= 'role';
$active_submenu='view_role';
include 'header.php';
$flash = $_SESSION['role_flash'] ?? '';
unset($_SESSION['role_flash']);
?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading panel-box">
            <h4>User Roles</h4>
        </div>
        <div class="panel-body manage_project">
            <?php if ($flash): ?>
                <div class="alert alert-info"><?php echo oecrm_h($flash); ?></div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="table-responsive">
                        <table class="table" id="role_table">
                            <thead>
                                <tr>
                                    <th class="text-center" width="32">#</th>
                                    <th>Name</th>
                                    <th class="text-center" width="200">Actions</th>
                                </tr>

                            </thead>
                            <tbody id="result">

                                <?php

                                $sel = "select * from user_type";

                                $qry = mysqli_query($conn, $sel);

                                $ci = 0;

                                

                                    while ($row = mysqli_fetch_assoc($qry)) {
                                        $ci++;
                                        
                                        echo "<tr>";  
                                        echo "<td>".$ci."</td>";
                                        echo "<td>{$row['name']}</td>";
                                        echo "<td class='text-right'>";
                                        if (oecrm_can($conn,"roles","manage_permissions")) { 
                                                echo "
                                                <a  href='role_permissions.php?role_id=" . $row['id'] . "'>
                                                    <i class='fa fa-key mr-2'></i>
                                                </a>";
                                            }
                                            if (oecrm_can($conn,"roles","edit")) {     
                                                echo "
                                                <a  href='add_role.php?id=" . $row['id'] . "'>
                                                    <i class='fa fa-pencil mr-2'></i>
                                                </a>";
                                            }
                                            if (oecrm_can($conn,"roles","delete")) {     
                                                echo "
                                                <a  data-toggle='modal' data-target='#delete-role-modal-" . $row['id'] . "'>
                                                    <i style class='fa fa-trash-o'></i>
                                                </a>";
                                            }
                                            echo "</td>";
                                        echo "</tr>";
                        
                                        // Modal for Delete Confirmation
                                        echo "
                                        <div class='modal fade' id='delete-role-modal-" . $row['id'] . "' tabindex='-1' role='dialog' aria-labelledby='modalTitle-" . $row['id'] . "' aria-hidden='true'>
                                            <div class='modal-dialog modal-sm modal-dialog-centered' role='document'>
                                                <div class='modal-content'>
                                                    <form action='action_delete_role.php' method='POST'>
                                                        " . oecrm_csrf_field() . "
                                                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                                                        <div class='modal-body text-center'>
                                                            <i class='fa fa-trash fa-3x text-danger mb-3'></i>
                                                            <p>Are you sure you want to delete this role? This Action Cannot Be Reversed</p>
                                                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
                                                            <button type='submit' class='btn btn-danger'>Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>";
                        
                                        
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
$('#role_table').DataTable();
</script>

<script>
    function confirmDelete() {
        // Display a confirmation dialog
        var confirmation = confirm("Are you sure you want to delete this item?");

        if (confirmation) {

        } else {
            // If the user clicks "Cancel" or closes the dialog, do nothing
        }
    }
</script>
<?php
include 'footer.php';
?>
