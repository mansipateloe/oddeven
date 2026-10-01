<?php
$active_menu = 'analytics';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'dashboards', 'view');
$companyId = oecrm_current_company_id($conn);

$summary = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT
            COUNT(*) total_employees,
            SUM(CASE WHEN status=0 THEN 1 ELSE 0 END) active_employees,
            SUM(CASE WHEN status=1 THEN 1 ELSE 0 END) inactive_employees,
            SUM(CASE WHEN TRIM(COALESCE(mobile1,''))<>'' THEN 1 ELSE 0 END) with_mobile,
            SUM(CASE WHEN TRIM(COALESCE(companyEmail,''))<>'' THEN 1 ELSE 0 END) with_email
        FROM employeestbl
        WHERE company_id=" . (int) $companyId
    )
);

$employees = mysqli_query(
    $conn,
    "SELECT id, employeeCode, name, designation, mobile1, companyEmail, status
     FROM employeestbl
     WHERE company_id=" . (int) $companyId . "
     ORDER BY status ASC, name ASC"
);
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="foundation-titlebar"><span></span><h2>Employee Summary</h2></div>

    <div class="resource-summary">
        <div>
            <i class="fa fa-users"></i>
            <span>
                <small>Total Employees</small>
                <strong><?php echo (int) ($summary['total_employees'] ?? 0); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-user-check"></i>
            <span>
                <small>Active</small>
                <strong><?php echo (int) ($summary['active_employees'] ?? 0); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-user-times"></i>
            <span>
                <small>Inactive</small>
                <strong><?php echo (int) ($summary['inactive_employees'] ?? 0); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-phone"></i>
            <span>
                <small>With Mobile</small>
                <strong><?php echo (int) ($summary['with_mobile'] ?? 0); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-envelope"></i>
            <span>
                <small>With Email</small>
                <strong><?php echo (int) ($summary['with_email'] ?? 0); ?></strong>
            </span>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Employee List</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($employees) === 0): ?>
                        <tr>
                            <td colspan="6">No employees found for this company.</td>
                        </tr>
                    <?php endif; ?>
                    <?php while ($row = mysqli_fetch_assoc($employees)): ?>
                        <tr>
                            <td><?php echo oecrm_h($row['employeeCode'] ?: '-'); ?></td>
                            <td><strong><?php echo oecrm_h($row['name'] ?: '-'); ?></strong></td>
                            <td><?php echo oecrm_h($row['designation'] ?: '-'); ?></td>
                            <td><?php echo oecrm_h($row['mobile1'] ?: '-'); ?></td>
                            <td><?php echo oecrm_h($row['companyEmail'] ?: '-'); ?></td>
                            <td>
                                <span class="label <?php echo (int) $row['status'] === 0 ? 'label-success' : 'label-default'; ?>">
                                    <?php echo (int) $row['status'] === 0 ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
