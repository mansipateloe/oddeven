<?php

include 'header.php';
$empId = $_SESSION['employeeId'];
?>
<div id="page-wrapper">
    <h2>Activity Log</h2>

    <div class="row" style="padding: 30px;">
        <div class="table-responsive">
            <table class="table" id="leaveTable">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Date</th>
                        <th>Message</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $leaveQuery = mysqli_query($conn, "SELECT * from activity_log where emp_id=$empId ORDER BY created_at DESC");
                    $count = 1;
                    while ($leaveRow = mysqli_fetch_assoc($leaveQuery)) {
                    ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= $leaveRow['created_at'] ?></td>
                            <td><?= $leaveRow['message'] ?></td>


                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php

include 'footer.php';
?>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
<script>
    $(document).ready(function() {
        $('#leaveTable').DataTable({
            /*responsive: true,*/
            // order: [[4, 'desc']],
            // columnDefs: [
            // { "orderable": false, "targets": 5 }
            // ]
        });
    });
</script>