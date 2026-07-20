<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$files = glob(__DIR__ . '/Unit/*Test.php');
sort($files);

$total = 0;
$failed = 0;
$failures = [];

foreach ($files as $file) {
    $tests = require $file;
    foreach ($tests as $name => $test) {
        $total++;
        try {
            $test();
            echo "[PASS] {$name}\n";
        } catch (Throwable $exception) {
            $failed++;
            $failures[] = $name . ': ' . $exception->getMessage();
            echo "[FAIL] {$name}\n";
            echo '       ' . $exception->getMessage() . "\n";
        }
    }
}

echo "\nTotal: {$total}\n";
echo "Passed: " . ($total - $failed) . "\n";
echo "Failed: {$failed}\n";

if ($failed > 0) {
    echo "\nFailures:\n";
    foreach ($failures as $failure) {
        echo '- ' . $failure . "\n";
    }
    exit(1);
}

