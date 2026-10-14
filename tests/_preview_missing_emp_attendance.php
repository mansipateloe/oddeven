<?php
require_once __DIR__ . '/../dbconnect.php';
$sourcePath = 'C:/Users/xyz/Downloads/workhoursTbl.sql';
$sourceIds = [28 => 'E024 Brijkishor', 52 => 'E43 Pratyush', 54 => 'E45 Krunal'];
$stats = [];
foreach (file($sourcePath) as $line) {
    $line = trim($line);
    if (!preg_match('/^\(\d+,\s*(28|52|54),/', $line, $match)) {
        continue;
    }
    if (substr($line, -1) === ',') {
        $line = substr($line, 0, -1);
    }
    $values = str_getcsv(substr($line, 1, -1), ',', "'", '\\');
    if (count($values) < 12) {
        continue;
    }
    $sourceId = (int) $values[1];
    $year = trim($values[4]);
    $month = trim($values[3]);
    $signin = trim($values[5]);
    $signout = trim($values[10]);
    $hours = trim($values[11]);
    $minutes = null;
    if (preg_match('/^(\d+):(\d+)$/', $hours, $duration)) {
        $minutes = (int) $duration[1] * 60 + (int) $duration[2];
    }
    $key = $sourceId . '|' . $year . '|' . $month;
    $stats[$key] ??= ['source' => $sourceIds[$sourceId], 'year' => $year, 'month' => $month, 'rows' => 0, 'no_signin' => 0, 'no_signout' => 0, 'below_510_minutes' => 0, 'unknown_hours' => 0];
    $stats[$key]['rows']++;
    $stats[$key]['no_signin'] += $signin === '' ? 1 : 0;
    $stats[$key]['no_signout'] += $signout === '' ? 1 : 0;
    $stats[$key]['below_510_minutes'] += $minutes !== null && $minutes < 510 ? 1 : 0;
    $stats[$key]['unknown_hours'] += $minutes === null ? 1 : 0;
    if ($year === '2026' && $signin === '') {
        echo 'NO_SIGNIN_SOURCE_ROW=' . json_encode(['source_employee' => $sourceIds[$sourceId], 'date' => $values[2], 'source_row' => $values[0]], JSON_UNESCAPED_SLASHES) . PHP_EOL;
    }
}
ksort($stats);
foreach ($stats as $row) {
    echo 'MONTH=' . json_encode($row, JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
foreach ([28, 52, 54] as $sourceId) {
    $stmt = mysqli_prepare($conn, 'SELECT COUNT(*) existing FROM employeestbl WHERE employeeCode=?');
    $code = $sourceId === 28 ? 'E024' : ($sourceId === 52 ? 'E43' : 'E45');
    mysqli_stmt_bind_param($stmt, 's', $code);
    mysqli_stmt_execute($stmt);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    echo 'CURRENT_CODE=' . $code . ' count=' . $exists['existing'] . PHP_EOL;
}
