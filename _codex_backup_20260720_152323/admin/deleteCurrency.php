<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
oecrm_require_csrf();
oecrm_require_permission($conn, 'finance', 'delete');

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS currency_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    symbol VARCHAR(20) NOT NULL,
    rate DECIMAL(14,6) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$id = oecrm_int_param($_POST, 'deleteCurrency');
$row = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM currency_master WHERE id=' . (int) $id));
if (!$row) {
    http_response_code(404);
    exit('Currency not found.');
}

$stmt = mysqli_prepare($conn, 'DELETE FROM currency_master WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['currency_flash'] = 'Currency deleted successfully.';
header('Location:manageCurrency.php');
exit;
