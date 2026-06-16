<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$parentId = (int) ($_GET['parent_id'] ?? 0);

if ($type === 'states') {
    $stmt = mysqli_prepare($conn, 'SELECT id,name FROM states WHERE country_id=? AND flag=1 ORDER BY name');
} elseif ($type === 'cities') {
    $stmt = mysqli_prepare($conn, 'SELECT id,name FROM cities WHERE state_id=? AND flag=1 ORDER BY name');
} else {
    echo json_encode([]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $parentId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = ['id' => (int) $row['id'], 'name' => $row['name']];
}
mysqli_stmt_close($stmt);

echo json_encode($rows);
