<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
oecrm_require_permission($conn, 'client_documents', 'download');

$id = oecrm_int_param($_GET, 'id');
$companyId = oecrm_current_company_id($conn);
$inlineRequested = !empty($_GET['inline']) || (($_GET['mode'] ?? '') === 'view');

$stmt = mysqli_prepare(
    $conn,
    'SELECT d.* FROM client_documents d JOIN clients c ON c.id=d.client_id WHERE d.id=? AND c.company_id=?'
);
mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
mysqli_stmt_execute($stmt);
$document = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$document) {
    http_response_code(404);
    exit('Document not found.');
}

$root = realpath(__DIR__ . '/../storage/client_documents');
$path = $root ? realpath($root . '/' . $document['stored_name']) : false;

if (!$root || !$path || strpos($path, $root) !== 0 || !is_file($path)) {
    http_response_code(404);
    exit('Document file not found.');
}

$mime = 'application/octet-stream';
if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo) {
        $detected = @finfo_file($finfo, $path);
        if ($detected) {
            $mime = $detected;
        }
        finfo_close($finfo);
    }
} elseif (!empty($document['mime_type'])) {
    $mime = $document['mime_type'];
}

$filename = str_replace(['"', "\r", "\n"], '', (string) ($document['original_name'] ?: $document['stored_name']));
$disposition = $inlineRequested || preg_match('/^image\//', $mime) ? 'inline' : 'attachment';

oecrm_audit(
    $conn,
    'client_documents',
    $inlineRequested ? 'view' : 'download',
    'client_document',
    $id,
    $inlineRequested ? 'Client document opened' : 'Client document downloaded',
    null,
    ['client_id' => $document['client_id']]
);

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: ' . $disposition . '; filename="' . $filename . '"');
readfile($path);
exit;