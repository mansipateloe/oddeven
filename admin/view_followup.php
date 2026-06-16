<?php
include 'header.php';
$id = $_GET['id'];
$viewdetails = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * from lead_followup where id=$id"));

?>

<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading panel-box">
            <h4>Followup Details</h4>
        </div>
        <div class="panel-body manage_project">
    
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <!-- <td class="f-bold">Lead Type:</td> -->
                            <td class="f-bold">followup_type:</td>
                            <td class="f-bold">Next Followup Date/Time</td>
                            <td class="f-bold">Created By:</td>
                        
                        </tr>

                        <tr>
                            <!-- <td><?= $viewdetails['lead_type'] ?></td>   -->
                            <td><?= $viewdetails['followup_type'] ?></td>
                            <td><?= $viewdetails['next_followup_date'] ?></td>
                            <td><?= $viewdetails['created_by'] == 0 ? "Admin" : "User" ?></td>
                        </tr>




                    </table>



                </div>
            </div>
        </div>
    </div>


</div>
<!-- <div id="page-wrapper">

    <div class="row">

        <div class="col-lg-12">
            <div class="panel panel-default">

                <div class="panel-heading">

                    View followup details

                </div>
                <div class="panel-body">

                    <div class="dataTablesbox2 edit_employee_detail">


                        <form role="form" method="POST">

                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel">View New Followup</h4>
                                    </div>
                                    <form action="" method="POST">
                                        <div class="modal-body">
                                            <!-- Lead Type 
                                            <table class="table">
                                                <tr>
                                                    <td class="f-bold">Lead Type:</td>
                                                    <td><?= $viewdetails['lead_type'] ?></td>

                                                    <td class="f-bold">followup_type:</td>
                                                    <td><?= $viewdetails['followup_type'] ?></td>
                                                </tr>

                                                <tr>
                                                    <td class="f-bold">Next Followup Date/Time</td>
                                                    <td><?= $viewdetails['next_followup_date'] ?></td>
                                                    <td class="f-bold">Created By:</td>
                                                    <td><?= $viewdetails['created_by'] == 0 ? "Admin" : "User" ?></td>

                                                </tr>
                                            </table>
                                    </form>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div> -->




<!-- <div class="modal fade" id="viewLeavemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
    <form role="form" method="POST">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel2">View Followup</h4>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <!-- Lead Type 
                        <table class="table">
                            <tr>
                                <td class="f-bold">Lead Type:</td>
                                <td><?= $viewdetails['lead_type'] ?></td>

                                <td class="f-bold">followup_type:</td>
                                <td><?= $viewdetails['followup_type'] ?></td>
                            </tr>

                            <tr>
                                <td class="f-bold">Next Followup Date/Time</td>
                                <td><?= $viewdetails['next_followup_date'] ?></td>
                                <td class="f-bold">Created By:</td>
                                <td><?= $viewdetails['created_by'] == 0 ? "Admin" : "User" ?></td>

                            </tr>




                        </table>

                        <!-- Followup Type 

                    </div>
                </form>
            </div>
        </div>
    </form>
</div> -->


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
    $('#leads_table').DataTable();

    function openModal() {
        $('#editLeavemodal').modal('show');

    }
</script>
<script>
    $('#leads_table').DataTable();

    function openModalview() {
        $('#viewLeavemodal').modal('show');

    }
</script>
<?php
include 'footer.php';
?>