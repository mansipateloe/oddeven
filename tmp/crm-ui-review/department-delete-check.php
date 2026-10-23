<?php
require __DIR__ . '/../../dbconnect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
// Connection-local temporary tables shadow production tables; real rows are never changed.
mysqli_query($conn, 'CREATE TEMPORARY TABLE departments (id INT PRIMARY KEY, company_id INT, name VARCHAR(100), status INT)');
mysqli_query($conn, 'CREATE TEMPORARY TABLE employeestbl (id INT PRIMARY KEY, department_id INT, status INT)');
mysqli_query($conn, 'CREATE TEMPORARY TABLE notice_targets (id INT PRIMARY KEY, department_id INT)');
function oecrm_require_csrf() {}
function oecrm_require_permission($conn, $module, $action) {}
function oecrm_audit(...$args) { $GLOBALS['audit'][] = $args; }
$source = file_get_contents(__DIR__ . '/../../admin/departments.php');
$start = strpos($source, "if (\$_SERVER['REQUEST_METHOD'] === 'POST' && \$isDeleteAction)");
$end = strpos($source, "if (\$_SERVER['REQUEST_METHOD'] === 'POST' && !\$isDeleteAction)");
if ($start === false || $end === false) throw new Exception('Delete handler not found');
$handler = substr($source, $start, $end - $start);
$cases = [
 ['unused active', 1, 1, null, false, true],
 ['unused inactive', 2, 0, null, false, true],
 ['active employee assigned', 3, 1, 0, false, false],
 ['inactive employee assigned', 4, 1, 1, false, false],
 ['notice assignment', 5, 1, null, true, false],
 ['other company', 6, 1, null, false, false],
 ['inactive assigned department', 7, 0, 0, false, false],
];
foreach ($cases as [$label, $testId, $initialStatus, $employeeStatus, $notice, $expectedDeleted]) {
 mysqli_query($conn, 'DELETE FROM departments');
 mysqli_query($conn, 'DELETE FROM employeestbl');
 mysqli_query($conn, 'DELETE FROM notice_targets');
 $testCompany = $label === 'other company' ? 2 : 1;
 mysqli_query($conn, "INSERT INTO departments VALUES ($testId, $testCompany, 'Test department', $initialStatus)");
 if ($employeeStatus !== null) mysqli_query($conn, "INSERT INTO employeestbl VALUES (1,$testId,$employeeStatus)");
 if ($notice) mysqli_query($conn, "INSERT INTO notice_targets VALUES (1,$testId)");
 $companyId = 1; $isDeleteAction = true; $message = $error = $warning = ''; $audit = [];
 $_SERVER['REQUEST_METHOD'] = 'POST'; $_POST = ['action'=>'delete', 'id'=>$testId];
 eval($handler);
 $remaining = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM departments WHERE id=$testId"));
 if (($remaining === null) !== $expectedDeleted) throw new Exception("Wrong deletion result: $label");
 if (!$expectedDeleted && (int)$remaining['status'] !== $initialStatus) throw new Exception("Status changed: $label");
 if ($expectedDeleted && (!$message || count($audit) !== 1)) throw new Exception("Missing success/audit: $label");
 if (!$expectedDeleted && !$warning && !$error) throw new Exception("Missing warning: $label");
 echo "PASS: $label\n";
}
$companyId=1; $isDeleteAction=true; $message=$error=$warning=''; $_POST=['id'=>999]; eval($handler);
if ($error !== 'Department not found in the active company.') throw new Exception('Missing ID handling failed');
echo "PASS: missing department\n";
mysqli_close($conn);
