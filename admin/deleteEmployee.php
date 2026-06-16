<?php
include 'dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
oecrm_require_csrf();
oecrm_require_permission($conn, 'employees', 'edit');

$id = oecrm_int_param($_GET, 'delete');
$companyId = oecrm_current_company_id($conn);
$employee = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM employeestbl WHERE id=' . (int) $id . ' AND company_id=' . (int) $companyId));

if (!$employee) {
    $_SESSION['employee_error'] = 'Employee not found.';
    header('Location:manageEmployee.php');
    exit;
}

$dependencies = [];
$checks = [
    'Payroll' => ['payroll_items', 'employee_id'],
    'Attendance' => ['attendance_sessions', 'employee_id'],
    'Leave' => ['leave_requests', 'employee_id'],
    'Salary structure' => ['salary_structures', 'employee_id'],
    'Project team' => ['project_team_members', 'employee_id'],
    'Timesheet' => ['timesheet_entries', 'employee_id'],
    'Asset allocation' => ['asset_allocations', 'employee_id'],
    'Access allocation' => ['access_assignments', 'employee_id'],
];
foreach ($checks as $label => $check) {
    $result = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM ' . $check[0] . ' WHERE ' . $check[1] . '=' . (int) $id));
    if ((int) ($result['total'] ?? 0) > 0) {
        $dependencies[] = $label;
    }
}

$stmt = mysqli_prepare($conn, 'UPDATE employeestbl SET status=1,is_admin_access=0 WHERE id=? AND company_id=?');
mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

mysqli_query($conn, "UPDATE employee_shift_assignments SET status=0,effective_to=COALESCE(effective_to,CURDATE()) WHERE employee_id=" . (int) $id);
mysqli_query($conn, "UPDATE project_team_members SET left_at=COALESCE(left_at,CURDATE()) WHERE employee_id=" . (int) $id);

$description = $dependencies
    ? 'Employee deactivated because linked records exist: ' . implode(', ', $dependencies)
    : 'Employee deactivated.';
oecrm_audit($conn, 'employees', 'deactivate', 'employee', $id, $description, $employee, ['status' => 1]);
$_SESSION['employee_flash'] = $description;
header('Location:manageEmployee.php');
exit;
