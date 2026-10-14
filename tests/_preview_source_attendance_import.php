<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../attendance.php';
$sourceInfo = [28 => ['code' => 'E024', 'name' => 'Brijkishor Sapovadiya', 'joining' => '2023-01-05'], 52 => ['code' => 'E43', 'name' => 'Pratyush Maheshbhai Baldha', 'joining' => '2018-01-01'], 54 => ['code' => 'E45', 'name' => 'Krunal Katakiya', 'joining' => '2018-01-01']];
$attendance = [];
foreach (file('C:/Users/xyz/Downloads/workhoursTbl.sql') as $line) {
    $line = trim($line);
    if (!preg_match('/^\(\d+,\s*(28|52|54),/', $line)) {
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
    $date = DateTimeImmutable::createFromFormat('!j-n-Y', trim($values[2]));
    if (!$date) {
        $date = DateTimeImmutable::createFromFormat('!d-m-Y', trim($values[2]));
    }
    if (!$date) {
        continue;
    }
    $key = $sourceId . '|' . $date->format('Y-m-d');
    $hours = trim($values[11]);
    $minutes = null;
    if (preg_match('/^(\d+):(\d+)$/', $hours, $duration)) {
        $minutes = (int) $duration[1] * 60 + (int) $duration[2];
    }
    $attendance[$key] = ['date' => $date->format('Y-m-d'), 'source_row' => (int) $values[0], 'signin' => trim($values[5]), 'signout' => trim($values[10]), 'hours' => $hours, 'minutes' => $minutes];
}
foreach ($sourceInfo as $sourceId => $employee) {
    $dates = [];
    foreach ($attendance as $key => $row) {
        if (strpos($key, $sourceId . '|') === 0) {
            $dates[] = $row['date'];
        }
    }
    sort($dates);
    $firstSourceDate = reset($dates);
    $lastSourceDate = end($dates);
    $start = max('2026-01-01', $firstSourceDate);
    $end = '2026-10-13';
    $counts = ['source_2026_rows' => 0, 'source_no_signin' => 0, 'source_no_signout' => 0, 'source_short_or_unknown' => 0, 'missing_workdays' => 0, 'casual_leave_candidates' => 0, 'paid_candidates' => 0, 'unpaid_candidates' => 0];
    $cursor = new DateTimeImmutable($start);
    $last = new DateTimeImmutable($end);
    for (; $cursor <= $last; $cursor = $cursor->modify('+1 day')) {
        $date = $cursor->format('Y-m-d');
        $isOff = (int) $cursor->format('w') === 0 || oecrm_shift_is_weekly_off($conn, 9, $date) || oecrm_is_holiday($conn, $date, 1);
        if ($isOff) {
            continue;
        }
        $row = $attendance[$sourceId . '|' . $date] ?? null;
        $isFull = $row && $row['signin'] !== '' && $row['signout'] !== '' && $row['minutes'] !== null && $row['minutes'] >= 510;
        if ($row) {
            $counts['source_2026_rows']++;
            $counts['source_no_signin'] += $row['signin'] === '' ? 1 : 0;
            $counts['source_no_signout'] += $row['signout'] === '' ? 1 : 0;
            $counts['source_short_or_unknown'] += !$isFull ? 1 : 0;
        } else {
            $counts['missing_workdays']++;
        }
        if (!$isFull) {
            $counts['casual_leave_candidates']++;
            if ($counts['casual_leave_candidates'] <= 12) {
                $counts['paid_candidates']++;
            } else {
                $counts['unpaid_candidates']++;
            }
        }
    }
    echo json_encode(['source_id' => $sourceId, 'employee' => $employee, 'first_source_date' => $firstSourceDate, 'last_source_date' => $lastSourceDate, 'scan_start' => $start, 'scan_end' => $end, 'counts' => $counts], JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
echo 'PARSED_ATTENDANCE_ROWS=' . count($attendance) . PHP_EOL;
