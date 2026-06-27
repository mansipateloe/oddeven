<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';
$companyId = oecrm_current_company_id($conn);
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
                        <table width="100%" class="table table-striped table-bordered table-hover" id="follow_table" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>#</th>
                                    <th>Lead Company Name</th>
                                    <th>Lead Contact Person</th>
                                    <th>Date</th>
                                    <th>Followup Type</th>
                                    <th>Remarks</th>
                                    <th>Followup Date</th>
                                    <th>Followup Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $sql = 'SELECT f.*, l.company_name, l.contact_person
                                        FROM lead_followup f
                                        INNER JOIN leads l ON l.lead_id = f.lead_id
                                        WHERE l.company_id = ' . (int) $companyId . '
                                        ORDER BY f.id DESC';
                                $result = mysqli_query($conn, $sql);
                                if ($result && mysqli_num_rows($result) > 0):
                                    while ($row = mysqli_fetch_assoc($result)):
                                        $status = $row['status'] === '0' || $row['status'] === '' ? 'pending' : $row['status'];
                                ?>
                                    <tr class="gradeA even" role="row">
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo oecrm_h($row['company_name']); ?></td>
                                        <td><?php echo oecrm_h($row['contact_person']); ?></td>
                                        <td><?php echo !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : '-'; ?></td>
                                        <td><?php echo oecrm_h($row['followup_type']); ?></td>
                                        <td><?php echo oecrm_h($row['remarks']); ?></td>
                                        <td><?php echo !empty($row['next_followup_date']) ? date('d-m-Y', strtotime($row['next_followup_date'])) : '-'; ?></td>
                                        <td><?php echo !empty($row['next_followup_time']) ? date('h:i A', strtotime($row['next_followup_time'])) : '-'; ?></td>
                                        <td><?php echo oecrm_h($status); ?></td>
                                        <td class="center" align="center"><a href="view_followup.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a></td>
                                    </tr>
                                <?php
                                    endwhile;
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
