<?php

include 'header.php';
?>

<?php

if (isset($_POST['status'])) {

    $id = $_POST['status'];

    $editStatus = "update lead_followup set status='1' where id=" . $id;

    $resultStatus = mysqli_query($conn, $editStatus);
}

?>

<?php

if (isset($_POST['status'])) {

    $id = $_POST['status'];

    $editStatus = "update lead_followup set status='0' where id=" . $id;

    $resultStatus = mysqli_query($conn, $editStatus);
}

?>
<?php

if (isset($_POST['status'])) {

    $id = $_POST['status'];

    $editStatus = "update lead_followup set status='2' where id=" . $id;

    $resultStatus = mysqli_query($conn, $editStatus);
}

?>



<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading panel-box">
            <h4>All Followups</h4>
        </div>
        <div class="panel-body manage_project">
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="expiring_tbl" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                        <thead>
                            <tr role="row">
                                <!-- <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 170px;">Project Name</th> -->
                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" >#</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" >Lead Company Name</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" >Lead Contact Person</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" >Date</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" >Followup Type</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" >Remarks</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" >Followup Date</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" >Followup Time</th>
                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" >Status</th>
                                <!-- <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" >Amount</th> -->
                                <th tabindex="0" rowspan="1" colspan="1" >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php
                            
                            $i = 1;
                            $detailMaster = "SELECT * from lead_followup";
                            $result = mysqli_query($conn, $detailMaster); 
                                $leadmaster = mysqli_fetch_assoc(mysqli_query($conn, "SELECT *FROM `leads`"));
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) 
                                {
                                    if($row['status']=="0"){$row['status']="pending";}

                                    echo "<tr class='gradeA even' role='row'>";
                                    echo "<td>" . $i++ . "</td>";
                                    echo "<td>" . $leadmaster['company_name'] . "</td>";
                                    echo "<td>" . $leadmaster['contact_person'] . "</td>";
                                
                                    

                                    echo "<td>" . date('d-m-Y',strtotime($followupRow['created_at'])) . "</td>";
                                    echo "<td>" . $row['followup_type'] . "</td>";
                                    echo "<td>" . $row['remarks'] . "</td>";
                                    echo "<td>" . date('d-m-Y',strtotime($followupRow['next_followup_date'])) ."</td>";
                                    echo "<td>" . date('h:i A',strtotime($followupRow['next_followup_time']))."</td>";
                                    echo "<td>" . $row['status'] . "</td>";

                                    /*if ($row['status'] == 0) {

                                        echo "<td><form method='POST'><button type='submit' value=" . $row['id'] . " class='btn btn-success' name='inprogress'>Inprogress</button></form></td>";
                                    } elseif ($row['status'] == 1) {

                                        echo "<td><form method='POST'><button type='submit' value=" . $row['id'] . " class='btn btn-danger' name='close'>Close</button></form></td>";
                                    }
                                        elseif ($row['status'] == 2) {

                                        echo "<td><form method='POST'><button type='submit' value=" . $row['id'] . " class='btn btn-warning' name='pending'>Pending</button></form></td>";
                                    }
                                        elseif ($row['status'] == 3) {

                                        echo "<td><form method='POST'><button type='submit' value=" . $row['id'] . " class='btn btn-info' name='complete'>Complete</button></form></td>";
                                    }*/

                                    // <td>
                                    // <a href="view_followup.php" class="btn btn-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a>
                                    // </td>
                                    echo '<td class="center" align="center"><a href="view_followup.php?id=' .$row["id"].'"  class="btn btn-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a></td>';


                                    echo "</tr>";
                                
                            
                                    // echo "<td>" . $row['amount'] . "</td>";
                                    // echo '<td><a href="editDomainHosting.php?edit=' . $row["id"] . '"><i class="fa fa-pencil" style="font-size:22px;"></i></a>&nbsp;&nbsp;
                                    //         <a href="deleteDomainHosting.php?delete=' . $row["id"] . '" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></a></td>';
                                    // echo "</tr>";
                                    // $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
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
<!-- Custom JavaScript for form validation -->
<script>
$('#follow_table').DataTable();
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