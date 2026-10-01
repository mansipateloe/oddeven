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
            SUM(CASE WHEN status=0 AND joiningDate REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' AND joiningDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND CURDATE() THEN 1 ELSE 0 END) recent_joiners,
            SUM(CASE WHEN status=0 AND joiningDate REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' AND joiningDate > CURDATE() THEN 1 ELSE 0 END) upcoming_joiners,
            SUM(CASE WHEN status=0 AND TRIM(COALESCE(companyEmail,''))<>'' THEN 1 ELSE 0 END) profile_ready,
            SUM(CASE WHEN status=0 AND TRIM(COALESCE(companyEmail,''))<>'' AND TRIM(COALESCE(mobile1,''))<>'' THEN 1 ELSE 0 END) fully_ready
        FROM employeestbl
        WHERE company_id=" . (int) $companyId
    )
);

$recentJoiners = mysqli_query(
    $conn,
    "SELECT id, employeeCode, name, designation, joiningDate, companyEmail, mobile1, status
     FROM employeestbl
     WHERE company_id=" . (int) $companyId . "
       AND status=0
       AND joiningDate REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'
       AND joiningDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND CURDATE()
     ORDER BY joiningDate DESC"
);

$upcomingJoiners = mysqli_query(
    $conn,
    "SELECT id, employeeCode, name, designation, joiningDate, companyEmail, mobile1, status
     FROM employeestbl
     WHERE company_id=" . (int) $companyId . "
       AND status=0
       AND joiningDate REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'
       AND joiningDate > CURDATE()
     ORDER BY joiningDate ASC"
);

$onboarding = mysqli_query(
    $conn,
    "SELECT id, employeeCode, name, designation, joiningDate,
            CASE WHEN TRIM(COALESCE(companyEmail,''))<>'' THEN 1 ELSE 0 END has_email,
            CASE WHEN TRIM(COALESCE(mobile1,''))<>'' THEN 1 ELSE 0 END has_mobile,
            CASE WHEN TRIM(COALESCE(birthdate,''))<>'' THEN 1 ELSE 0 END has_birthdate,
            CASE WHEN TRIM(COALESCE(address,''))<>'' THEN 1 ELSE 0 END has_address
     FROM employeestbl
     WHERE company_id=" . (int) $companyId . "
       AND status=0
     ORDER BY joiningDate DESC, name ASC"
);
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="foundation-titlebar"><span></span><h2>Hiring &amp; Onboarding</h2></div>

    <div class="resource-summary">
        <div>
            <i class="fa fa-users"></i>
            <span>
                <small>Total Employees</small>
                <strong><?php echo (int) ($summary['total_employees'] ?? 0); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-user-plus"></i>
            <span>
                <small>Recent Joiners</small>
                <strong><?php echo (int) ($summary['recent_joiners'] ?? 0); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-calendar"></i>
            <span>
                <small>Upcoming Joiners</small>
                <strong><?php echo (int) ($summary['upcoming_joiners'] ?? 0); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-check-circle"></i>
            <span>
                <small>Profile Ready</small>
                <strong><?php echo (int) ($summary['profile_ready'] ?? 0); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-clipboard"></i>
            <span>
                <small>Fully Ready</small>
                <strong><?php echo (int) ($summary['fully_ready'] ?? 0); ?></strong>
            </span>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Recent Joiners</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Designation</th>
                        <th>Joining Date</th>
                        <th>Email</th>
                        <th>Mobile</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recentJoiners) === 0): ?>
                        <tr><td colspan="5">No recent joiners found in the last 90 days.</td></tr>
                    <?php endif; ?>
                    <?php while ($row = mysqli_fetch_assoc($recentJoiners)): ?>
                        <tr>
                            <td><strong><?php echo oecrm_h($row['name'] ?: '-'); ?></strong><small style="display:block"><?php echo oecrm_h($row['employeeCode'] ?: '-'); ?></small></td>
                            <td><?php echo oecrm_h($row['designation'] ?: '-'); ?></td>
                            <td><?php echo oecrm_h($row['joiningDate'] ?: '-'); ?></td>
                            <td><?php echo oecrm_h($row['companyEmail'] ?: '-'); ?></td>
                            <td><?php echo oecrm_h($row['mobile1'] ?: '-'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Upcoming Joiners</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Designation</th>
                        <th>Joining Date</th>
                        <th>Email</th>
                        <th>Mobile</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($upcomingJoiners) === 0): ?>
                        <tr><td colspan="5">No upcoming joiners scheduled.</td></tr>
                    <?php endif; ?>
                    <?php while ($row = mysqli_fetch_assoc($upcomingJoiners)): ?>
                        <tr>
                            <td><strong><?php echo oecrm_h($row['name'] ?: '-'); ?></strong><small style="display:block"><?php echo oecrm_h($row['employeeCode'] ?: '-'); ?></small></td>
                            <td><?php echo oecrm_h($row['designation'] ?: '-'); ?></td>
                            <td><?php echo oecrm_h($row['joiningDate'] ?: '-'); ?></td>
                            <td><?php echo oecrm_h($row['companyEmail'] ?: '-'); ?></td>
                            <td><?php echo oecrm_h($row['mobile1'] ?: '-'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Onboarding Readiness</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Joining Date</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Birthdate</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($onboarding) === 0): ?>
                        <tr><td colspan="6">No employee onboarding data available.</td></tr>
                    <?php endif; ?>
                    <?php while ($row = mysqli_fetch_assoc($onboarding)): ?>
                        <tr>
                            <td><strong><?php echo oecrm_h($row['name'] ?: '-'); ?></strong><small style="display:block"><?php echo oecrm_h($row['employeeCode'] ?: '-'); ?></small></td>
                            <td><?php echo oecrm_h($row['joiningDate'] ?: '-'); ?></td>
                            <td><?php echo (int) $row['has_email'] === 1 ? '<span class="label label-success">Ready</span>' : '<span class="label label-default">Missing</span>'; ?></td>
                            <td><?php echo (int) $row['has_mobile'] === 1 ? '<span class="label label-success">Ready</span>' : '<span class="label label-default">Missing</span>'; ?></td>
                            <td><?php echo (int) $row['has_birthdate'] === 1 ? '<span class="label label-success">Ready</span>' : '<span class="label label-default">Missing</span>'; ?></td>
                            <td><?php echo (int) $row['has_address'] === 1 ? '<span class="label label-success">Ready</span>' : '<span class="label label-default">Missing</span>'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
