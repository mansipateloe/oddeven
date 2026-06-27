<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
oecrm_require_permission($conn, 'projects', 'view');
$id = oecrm_int_param($_GET, 'id');
$companyId = oecrm_current_company_id($conn);
$stmt = mysqli_prepare($conn, 'SELECT a.* FROM task_attachments a JOIN tasktbl t ON t.id=a.task_id WHERE a.id=? AND t.company_id=?');
mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
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
