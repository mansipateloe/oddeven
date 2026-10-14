<?php
require_once __DIR__ . '/../dbconnect.php';
$queries = [
    'SELECT DATABASE() database_name',
    'SELECT id,employeeCode,name,company_id,status,joiningDate FROM employeestbl WHERE id=52',
    'SELECT id,employeeCode,name,company_id,status,joiningDate FROM employeestbl WHERE employeeCode IN ("E43","E43 ") OR name LIKE "%Pratyush%" ORDER BY id',
    'SELECT id,employeeId,workDate,signinTime,signoutTime,workHours FROM workhourstbl WHERE employeeId=52 ORDER BY id LIMIT 20',
    'SELECT year,COUNT(*) rows_count,SUM(signinTime<>"") days_with_signin,MIN(id) first_id,MAX(id) last_id FROM workhourstbl WHERE employeeId=52 GROUP BY year ORDER BY year',
    'SELECT COUNT(*) sessions_for_employee_52 FROM attendance_sessions WHERE employee_id=52',
    'SELECT employee_id,attendance_date,actual_in,actual_out,attendance_status FROM attendance_sessions WHERE employee_id IN (SELECT id FROM employeestbl WHERE employeeCode="E43" OR name LIKE "%Pratyush%") ORDER BY attendance_date LIMIT 30',
    'SHOW COLUMNS FROM employeestbl',
    'SHOW COLUMNS FROM workhourstbl'
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
