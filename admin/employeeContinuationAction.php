<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../hr_letters.php';
require_once __DIR__ . '/../employee_continuations.php';

oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
oecrm_require_csrf();
oecrm_require_permission($conn, 'employees', 'edit');

$companyId = oecrm_current_company_id($conn);
$actor = (int) ($_SESSION['adminId'] ?? 0);
oecrm_ensure_employee_continuation_tables($conn);

try {
    $employeeId = (int) ($_POST['employee_id'] ?? 0);
    $renewalDate = oecrm_continuation_sql_date($_POST['renewal_date'] ?? '') ?: date('Y-m-d');
    $newDesignation = trim((string) ($_POST['new_designation'] ?? ''));
    $revisedSalary = trim((string) ($_POST['revised_salary'] ?? ''));
    $remarks = trim((string) ($_POST['remarks'] ?? ''));
    $createLetter = isset($_POST['create_letter']);

    if ($createLetter) {
        oecrm_require_permission($conn, 'hr_letters', 'create');
    }

    $stmt = mysqli_prepare($conn, 'SELECT * FROM employeestbl WHERE id=? AND company_id=? AND status=0');
    mysqli_stmt_bind_param($stmt, 'ii', $employeeId, $companyId);
    mysqli_stmt_execute($stmt);
    $employee = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$employee) {
        throw new RuntimeException('Active employee not found for selected company.');
    }

    $company = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM companies WHERE id=' . (int) $companyId));
    if (!$company) {
        throw new RuntimeException('Company not found.');
    }

    $latest = oecrm_employee_latest_continuation($conn, $employeeId, $companyId);
    $term = oecrm_employee_continuation_term($employee, $latest);
    $previousFrom = $term['current_from'];
    $previousTo = $term['current_to'];
    $renewedFrom = oecrm_continuation_sql_date($_POST['renewed_from'] ?? '') ?: $term['next_from'];
    $renewedTo = oecrm_continuation_sql_date($_POST['renewed_to'] ?? '') ?: oecrm_continuation_add_year_term($renewedFrom);
    $termNo = (int) $term['next_term_no'];
    $status = 'continued';

    if (strtotime($renewedTo) < strtotime($renewedFrom)) {
        throw new RuntimeException('Renewed period end date cannot be before start date.');
    }

    mysqli_begin_transaction($conn);
    $stmt = mysqli_prepare($conn, 'INSERT INTO employee_continuation_history(company_id,employee_id,term_no,previous_from,previous_to,renewed_from,renewed_to,renewal_date,status,new_designation,revised_salary,remarks,created_by) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
    mysqli_stmt_bind_param($stmt, 'iiisssssssssi', $companyId, $employeeId, $termNo, $previousFrom, $previousTo, $renewedFrom, $renewedTo, $renewalDate, $status, $newDesignation, $revisedSalary, $remarks, $actor);
    mysqli_stmt_execute($stmt);
    $historyId = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $letterId = null;
    if ($createLetter) {
        $history = [
            'id' => $historyId,
            'term_no' => $termNo,
            'previous_from' => $previousFrom,
            'previous_to' => $previousTo,
            'renewed_from' => $renewedFrom,
            'renewed_to' => $renewedTo,
            'renewal_date' => $renewalDate,
            'new_designation' => $newDesignation,
            'revised_salary' => $revisedSalary,
            'remarks' => $remarks,
        ];
        $letterId = oecrm_create_continuation_letter($conn, $companyId, $employee, $company, $history, $actor);
        $stmt = mysqli_prepare($conn, 'UPDATE employee_continuation_history SET letter_id=? WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'iii', $letterId, $historyId, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    if (function_exists('oecrm_audit')) {
        oecrm_audit($conn, 'employees', 'renew', 'employee_continuation', $historyId, 'Employee continuation recorded', null, ['employee_id' => $employeeId, 'term_no' => $termNo, 'letter_id' => $letterId]);
    }

    mysqli_commit($conn);
    $_SESSION['continuation_flash'] = 'Employee continuation saved' . ($letterId ? ' and continuation letter draft created.' : '.');
} catch (Throwable $exception) {
    if (mysqli_errno($conn)) {
        @mysqli_rollback($conn);
    }
    $_SESSION['continuation_error'] = $exception->getMessage();
}

header('Location: employeeContinuations.php' . ($employeeId ? '?employee_id=' . (int) $employeeId : ''));
exit;
