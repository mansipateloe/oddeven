<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../invoiceTax.php';
oecrm_require_admin_login();
if (!oecrm_can($conn, 'projects', 'view') && !oecrm_can($conn, 'finance', 'view') && !oecrm_can($conn, 'finance', 'create') && !oecrm_can($conn, 'finance', 'edit')) {
    http_response_code(403);
    exit;
}
header('Content-Type: application/json; charset=utf-8');
try {
    $client = oecrm_invoice_project_client($conn, (int) ($_POST['proj_id'] ?? 0), oecrm_current_company_id($conn));
    echo json_encode(['data' => [
        'customerName' => $client['display_name'] ?: $client['customerName'],
        'client_country' => $client['country'],
        'is_indian_client' => $client['is_indian_client'],
    ]]);
} catch (InvalidArgumentException $e) {
    http_response_code(404);
    echo json_encode(['error' => $e->getMessage()]);
}