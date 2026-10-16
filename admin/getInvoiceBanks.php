<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../invoiceBanks.php';
oecrm_require_admin_login();
oecrm_require_permission($conn, 'finance', 'view');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode(['options_html' => oecrm_invoice_bank_options($conn)]);