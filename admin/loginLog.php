<?php
include 'header.php';
$id = $_GET['id'];
?>

<div id="page-wrapper">
    <div class="">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">

                        Employees Login Log Table

                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="loginLogTable" class="table">
                                <thead>
                                    <tr>
                                        <th>Sr No.</th>
                                        <th>Date</th>
                                        <th>IP</th>
                                        <th>Browser</th>
                                        <th>Browser Specification</th>
                                        <th>Device Type</th>
                                        <th>Device Platform</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    $qry = mysqli_query($conn, "select * from login_details where user_id=$id");
                                    while ($db = mysqli_fetch_assoc($qry)) {
                                        $device_platform="PC";
                                        $db['browser_type']=str_replace('"',"",$db['browser_type']);
                                        $db['device_type']=str_replace('"',"",$db['device_type']);
                                        if($db['mobile_browser']==1){$device_platform="Mobile";}
                                    ?>
                                        <tr>
                                            <td> <?= $count++ ?> </td>
                                            <td> <?= $db['login_datetime'] ?> </td>
                                            <td> <?= $db['ip_address'] ?> </td>
                                            <td> <?= $db['browser_details'] ?> </td>
                                            <td> <?= $db['browser_type'] ?> </td>
                                            <td> <?= $device_platform ?> </td>
                                            <td> <?= $db['device_type'] ?> </td>
                                            
                                        </tr>

                                    <?php
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
</div>

<?php
include 'footer.php';
?>
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
    $(document).ready(function() {
        $('#loginLogTable').DataTable();
    });
</script>