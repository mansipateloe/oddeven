<?php
require_once __DIR__ . "/dbconnect.php";
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../foundation.php";
require_once __DIR__ . "/../payroll_export.php";
oecrm_require_admin_login();
oecrm_require_permission($conn, "payroll", "export");
$companyId = oecrm_current_company_id($conn);
$runId = oecrm_int_param($_GET, "run_id");
$stmt = mysqli_prepare(
    $conn,
    'SELECT * FROM payroll_runs WHERE id=? AND company_id=? AND status IN ("approved","locked")'
);
mysqli_stmt_bind_param($stmt, "ii", $runId, $companyId);
mysqli_stmt_execute($stmt);
$run = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$run) {
    http_response_code(403);
    exit("Bulk salary slips are available after payroll approval.");
}
$items = mysqli_query(
    $conn,
    "SELECT id FROM payroll_items WHERE payroll_run_id=" .
        (int) $runId .
        " ORDER BY employee_id"
);
oecrm_audit(
    $conn,
    "payroll",
    "bulk_salary_slips",
    "payroll_run",
    $runId,
    "Bulk salary slips generated"
);
?><!doctype html><html><head><meta charset="utf-8"><title>Bulk Salary Slips</title><?php echo oecrm_salary_slip_styles(); ?></head><body><div class="print-toolbar"><a href="payrollManagement.php?year=<?php echo $run[
    "period_year"
]; ?>&month=<?php echo $run[
    "period_month"
]; ?>">Back</a><button onclick="window.print()">Print / Save All as PDF</button></div><?php while (
    $row = mysqli_fetch_assoc($items)
) {
    echo oecrm_salary_slip_html(
        oecrm_payroll_item_details($conn, (int) $row["id"], $companyId),
        "../admin/img/salary-slip-logo.png"
    );
} ?></body></html>