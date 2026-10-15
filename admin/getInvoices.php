<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../depositCollections.php';
oecrm_require_admin_login();
if (!oecrm_can($conn, 'finance', 'view') && !oecrm_can($conn, 'finance', 'create') && !oecrm_can($conn, 'finance', 'edit')) {
    http_response_code(403);
    exit;
}
header('Content-Type: application/json; charset=utf-8');
try {
    $data = oecrm_collection_invoice_options($conn, oecrm_current_company_id($conn), (int) ($_POST['proj_id'] ?? 0), (int) ($_POST['deposit_id'] ?? 0));
    echo json_encode(['data' => $data]);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['data' => [], 'error' => $e->getMessage()]);
}