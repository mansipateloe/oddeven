<?php
require_once __DIR__ . '/../dbconnect.php';
$sourcePath = 'C:/Users/xyz/Downloads/employeesTbl.sql';
$source = [];
$invalidRows = 0;
foreach (file($sourcePath) as $line) {
    $line = trim($line);
    if (!preg_match('/^\(\d+,/', $line)) {
        continue;
    }
    if (substr($line, -1) === ',') {
        $line = substr($line, 0, -1);
    }
    $values = str_getcsv(substr($line, 1, -1), ',', "'", '\\');
    if (count($values) < 22) {
        $invalidRows++;
        continue;
    }
    $source[] = ['id' => (int) $values[0], 'code' => trim($values[3]), 'name' => trim($values[5]), 'username' => trim($values[7]), 'email' => trim($values[9])];
}
$current = [];
$result = mysqli_query($conn, 'SELECT id,employeeCode,name,employeeUname,companyEmail FROM employeestbl');
while ($row = mysqli_fetch_assoc($result)) {
    $key = strtolower(trim($row['employeeCode'])) . '|' . strtolower(trim($row['name']));
    $current[$key][] = $row;
}
$missing = [];
foreach ($source as $row) {
    $key = strtolower($row['code']) . '|' . strtolower($row['name']);
    if (empty($current[$key])) {
        $missing[] = $row;
    }
}
echo 'SOURCE_PARSED=' . count($source) . ' INVALID_ROWS=' . $invalidRows . PHP_EOL;
echo 'LIVE_EMPLOYEES=' . count($current) . PHP_EOL;
echo 'MISSING_SOURCE_ROWS=' . count($missing) . PHP_EOL;
foreach ($missing as $row) {
    echo json_encode($row, JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
