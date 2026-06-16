<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();
oecrm_require_csrf();
if (!oecrm_is_super_admin()) {
    http_response_code(403);
    exit('Only the super administrator can switch companies.');
}
$companyId = oecrm_int_param($_POST, 'company_id');
$stmt = mysqli_prepare($conn, 'SELECT id, display_name FROM companies WHERE id=? AND status=1');
mysqli_stmt_bind_param($stmt, 'i', $companyId); mysqli_stmt_execute($stmt);
$company = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
if (!$company) { http_response_code(404); exit('Company not found.'); }
$oldCompanyId = $_SESSION['company_id'] ?? 0;
$_SESSION['company_id'] = $companyId;
oecrm_audit($conn, 'companies', 'switch', 'company', $companyId, 'Active company changed', ['company_id'=>$oldCompanyId], ['company_id'=>$companyId]);
$back = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
header('Location: ' . $back);
exit;