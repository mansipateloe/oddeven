<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "Step 1: PHP OK - " . PHP_VERSION . "<br>\n";

echo "Step 2: loading security.php...<br>\n";
require_once __DIR__ . '/../security.php';
echo "security.php OK<br>\n";

echo "Step 3: loading admin/dbconnect.php...<br>\n";
include __DIR__ . '/dbconnect.php';
echo "dbconnect.php OK<br>\n";

if (!isset($conn) || !$conn) {
    die('DB connection variable $conn missing/failed<br>');
}

echo "Step 4: test DB query...<br>\n";
$result = mysqli_query($conn, 'SELECT 1 ok');
if (!$result) {
    die('DB query failed: ' . mysqli_error($conn) . '<br>');
}
echo "DB query OK<br>\n";

echo "Step 5: loading foundation.php...<br>\n";
require_once __DIR__ . '/../foundation.php';
echo "foundation.php OK<br>\n";

echo "Step 6: mysqli_stmt_get_result check...<br>\n";
echo function_exists('mysqli_stmt_get_result') ? 'mysqli_stmt_get_result OK<br>' : 'mysqli_stmt_get_result MISSING - enable mysqlnd<br>';

echo "DONE: base admin dependencies OK<br>\n";
