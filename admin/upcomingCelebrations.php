<?php
$active_menu = 'analytics';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'dashboards', 'view');
$companyId = oecrm_current_company_id($conn);
$upcomingDays = 90;
$today = new DateTime('today', new DateTimeZone('Asia/Kolkata'));

$birthdays = [];
$birthdayResult = mysqli_query(
    $conn,
    "SELECT id, employeeCode, name, designation, birthdate FROM employeestbl WHERE company_id=" . (int) $companyId . " AND status=0 AND birthdate REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' ORDER BY name"
);
while ($birthdayResult && ($employee = mysqli_fetch_assoc($birthdayResult))) {
    $birthDate = DateTime::createFromFormat('!Y-m-d', $employee['birthdate']);
    if (!$birthDate) continue;

    $nextBirthday = DateTime::createFromFormat('!Y-m-d', $today->format('Y') . '-' . $birthDate->format('m-d'));
    if ($nextBirthday <= $today) {
        $nextBirthday->modify('+1 year');
    }

    $daysUntil = (int) $today->diff($nextBirthday)->format('%a');
    if ($daysUntil <= 0 || $daysUntil > $upcomingDays) {
        continue;
    }

    $birthdays[] = [
        'id' => (int) $employee['id'],
        'employeeCode' => $employee['employeeCode'] ?? '-',
        'name' => $employee['name'] ?? '-',
        'designation' => $employee['designation'] ?? '-',
        'event_date' => $nextBirthday->format('Y-m-d'),
        'days_until' => $daysUntil,
        'age' => (int) $nextBirthday->format('Y') - (int) $birthDate->format('Y'),
    ];
}
usort($birthdays, static function ($left, $right) {
    return $left['days_until'] <=> $right['days_until'];
});

$anniversaries = [];
$anniversaryResult = mysqli_query(
    $conn,
    "SELECT id, employeeCode, name, designation, joiningDate FROM employeestbl WHERE company_id=" . (int) $companyId . " AND status=0 AND joiningDate REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' ORDER BY name"
);
while ($anniversaryResult && ($employee = mysqli_fetch_assoc($anniversaryResult))) {
    $joinDate = DateTime::createFromFormat('!Y-m-d', $employee['joiningDate']);
    if (!$joinDate) continue;

    $nextAnniversary = DateTime::createFromFormat('!Y-m-d', $today->format('Y') . '-' . $joinDate->format('m-d'));
    if ($nextAnniversary <= $today) {
        $nextAnniversary->modify('+1 year');
    }

    $daysUntil = (int) $today->diff($nextAnniversary)->format('%a');
    if ($daysUntil <= 0 || $daysUntil > $upcomingDays) {
        continue;
    }

    $anniversaries[] = [
        'id' => (int) $employee['id'],
        'employeeCode' => $employee['employeeCode'] ?? '-',
        'name' => $employee['name'] ?? '-',
        'designation' => $employee['designation'] ?? '-',
        'event_date' => $nextAnniversary->format('Y-m-d'),
        'days_until' => $daysUntil,
        'years_completed' => (int) $nextAnniversary->format('Y') - (int) $joinDate->format('Y'),
    ];
}
usort($anniversaries, static function ($left, $right) {
    return $left['days_until'] <=> $right['days_until'];
});
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="foundation-titlebar"><span></span><h2>Upcoming Celebrations</h2></div>

    <div class="resource-summary">
        <div>
            <i class="fa fa-birthday-cake"></i>
            <span>
                <small>Upcoming Birthdays</small>
                <strong><?php echo count($birthdays); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-calendar-check-o"></i>
            <span>
                <small>Upcoming Anniversaries</small>
                <strong><?php echo count($anniversaries); ?></strong>
            </span>
        </div>
        <div>
            <i class="fa fa-gift"></i>
            <span>
                <small>Total Upcoming</small>
                <strong><?php echo count($birthdays) + count($anniversaries); ?></strong>
            </span>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Upcoming Birthdays (next <?php echo (int) $upcomingDays; ?> days)</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Designation</th>
                        <th>Birthday Date</th>
                        <th>Age</th>
                        <th>In</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$birthdays): ?>
                        <tr><td colspan="5">No upcoming birthdays found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($birthdays as $employee): ?>
                        <tr>
                            <td><strong><?php echo oecrm_h($employee['name']); ?></strong><small style="display:block"><?php echo oecrm_h($employee['employeeCode']); ?></small></td>
                            <td><?php echo oecrm_h($employee['designation'] ?: '-'); ?></td>
                            <td><?php echo date('d F Y', strtotime($employee['event_date'])); ?></td>
                            <td><?php echo (int) $employee['age']; ?></td>
                            <td><?php echo (int) $employee['days_until']; ?> day(s)</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Upcoming Work Anniversaries (next <?php echo (int) $upcomingDays; ?> days)</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Designation</th>
                        <th>Anniversary Date</th>
                        <th>Years Completed</th>
                        <th>In</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$anniversaries): ?>
                        <tr><td colspan="5">No upcoming work anniversaries found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($anniversaries as $employee): ?>
                        <tr>
                            <td><strong><?php echo oecrm_h($employee['name']); ?></strong><small style="display:block"><?php echo oecrm_h($employee['employeeCode']); ?></small></td>
                            <td><?php echo oecrm_h($employee['designation'] ?: '-'); ?></td>
                            <td><?php echo date('d F Y', strtotime($employee['event_date'])); ?></td>
                            <td><?php echo (int) $employee['years_completed']; ?> years</td>
                            <td><?php echo (int) $employee['days_until']; ?> day(s)</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
