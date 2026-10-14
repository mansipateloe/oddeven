<?php
require_once __DIR__ . '/../dbconnect.php';
$queries = [
    'SHOW INDEX FROM employeestbl',
    'SELECT id,company_id,name,status FROM departments ORDER BY company_id,id',
    'SELECT id,employeeCode,name,designation,department_id,employeeUname,companyEmail,status FROM employeestbl WHERE designation IN ("Salesforce Developer","QA","Web Designer") ORDER BY designation,name',
    'SELECT shift_id,weekday,week_of_month,is_off FROM shift_weekly_off_rules WHERE shift_id=9 ORDER BY weekday,week_of_month',
    'SHOW COLUMNS FROM holidaytbl',
    'SHOW COLUMNS FROM employee_leave_balances',
    'SELECT s.company_id,s.paid_leave_per_year,p.annual_entitlement,p.is_paid,t.name FROM leave_settings s JOIN leave_policies p ON p.company_id=s.company_id JOIN leavetypetbl t ON t.id=p.leave_type_id WHERE s.company_id=1 AND LOWER(t.name)="casual leave"'
];
foreach ($queries as $query) {
    echo '## ' . $query . PHP_EOL;
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo 'ERROR: ' . mysqli_error($conn) . PHP_EOL;
        continue;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row, JSON_UNESCAPED_SLASHES) . PHP_EOL;
    }
}
