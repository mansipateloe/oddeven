<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_employee_login();
$id = oecrm_int_param($_GET, 'id');
$employeeId = (int) $_SESSION['employeeId'];
$stmt = mysqli_prepare($conn, 'SELECT a.* FROM task_attachments a JOIN task_assignees ta ON ta.task_id=a.task_id WHERE a.id=? AND ta.employee_id=?');
mysqli_stmt_bind_param($stmt, 'ii', $id, $employeeId);
mysqli_stmt_execute($stmt);
$attachment = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$attachment) {
    http_response_code(404);
    exit('Attachment not found.');
}
$root = realpath(__DIR__ . '/../storage/task_attachments');
$path = $root ? realpath($root . '/' . $attachment['stored_name']) : false;
if (!$root || !$path || strpos($path, $root) !== 0 || !is_file($path)) {
    http_response_code(404);
    exit('Attachment not found.');
}
header('Content-Type: ' . ($attachment['mime_type'] ?: 'application/octet-stream'));
header('Content-Length: ' . filesize($path));
header('Content-Disposition: attachment; filename="' . str_replace(['"', "\r", "\n"], '', $attachment['original_name']) . '"');
readfile($path);
exit;
