<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
require_once __DIR__.'/../foundation.php';

oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
oecrm_require_csrf();

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? 'save';
oecrm_require_permission($conn, 'resources', $id ? 'edit' : 'create');
$companyId = oecrm_current_company_id($conn);
$actor = (int)$_SESSION['adminId'];

try {
    if ($action === 'cancel') {
        oecrm_require_permission($conn, 'resources', 'delete');
        $oldStmt = mysqli_prepare($conn, 'SELECT * FROM resource_allocations WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($oldStmt, 'ii', $id, $companyId);
        mysqli_stmt_execute($oldStmt);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($oldStmt));
        mysqli_stmt_close($oldStmt);
        if (!$old) {
            throw new RuntimeException('Allocation not found.');
        }
        $stmt = mysqli_prepare($conn, 'UPDATE resource_allocations SET status="cancelled",end_date=COALESCE(end_date,CURDATE()) WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        oecrm_audit($conn, 'resources', 'delete', 'resource_allocation', $id, 'Resource allocation cancelled', $old, ['status' => 'cancelled']);
        $_SESSION['resource_flash'] = 'Resource allocation cancelled successfully.';
        header('Location: resources.php');
        exit;
    }

    $employeeId = (int)($_POST['employee_id'] ?? 0);
    $clientId = (int)($_POST['client_id'] ?? 0);
    $projectId = (int)($_POST['project_id'] ?? 0);
    $projectId = $projectId ?: null;
    $type = $_POST['allocation_type'] ?? 'full_time';
    $percent = (float)($_POST['allocation_percent'] ?? 100);
    $billing = (float)($_POST['billing_rate'] ?? 0);
    $cycle = $_POST['billing_cycle'] ?? 'monthly';
    $cost = (float)($_POST['salary_cost'] ?? 0);
    $currency = strtoupper(substr(trim($_POST['currency_code'] ?? 'INR'), 0, 3));
    $start = $_POST['start_date'] ?? '';
    $end = trim($_POST['end_date'] ?? '') ?: null;
    $status = $_POST['status'] ?? 'active';
    $notes = trim($_POST['notes'] ?? '');

    if (!$employeeId || !$clientId || !strtotime($start) || $percent <= 0 || $percent > 100) {
        throw new RuntimeException('Employee, client, valid start date and allocation percentage are required.');
    }
    if ($end && strtotime($end) < strtotime($start)) {
        throw new RuntimeException('End date cannot be earlier than start date.');
    }

    $employeeStmt = mysqli_prepare($conn, 'SELECT id, company_id FROM employeestbl WHERE id=? AND status=0');
    mysqli_stmt_bind_param($employeeStmt, 'i', $employeeId);
    mysqli_stmt_execute($employeeStmt);
    $employee = mysqli_fetch_assoc(mysqli_stmt_get_result($employeeStmt));
    mysqli_stmt_close($employeeStmt);

    $clientStmt = mysqli_prepare($conn, 'SELECT id FROM clients WHERE id=? AND company_id=? AND status IN ("active","prospect")');
    mysqli_stmt_bind_param($clientStmt, 'ii', $clientId, $companyId);
    mysqli_stmt_execute($clientStmt);
    $client = mysqli_fetch_assoc(mysqli_stmt_get_result($clientStmt));
    mysqli_stmt_close($clientStmt);

    if (!$employee || !$client) {
        throw new RuntimeException('Invalid active employee or client.');
    }

    if ($projectId) {
        $projectStmt = mysqli_prepare($conn, 'SELECT id, client_id FROM projectstbl WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($projectStmt, 'ii', $projectId, $companyId);
        mysqli_stmt_execute($projectStmt);
        $project = mysqli_fetch_assoc(mysqli_stmt_get_result($projectStmt));
        mysqli_stmt_close($projectStmt);
        if (!$project) {
            throw new RuntimeException('Selected project does not belong to the active company.');
        }
        if (!empty($project['client_id']) && (int)$project['client_id'] !== $clientId) {
            throw new RuntimeException('Selected client does not match the project client.');
        }

        $teamStmt = mysqli_prepare($conn, 'SELECT id FROM project_team_members WHERE project_id=? AND employee_id=? AND (left_at IS NULL OR left_at>=?) LIMIT 1');
        mysqli_stmt_bind_param($teamStmt, 'iis', $projectId, $employeeId, $start);
        mysqli_stmt_execute($teamStmt);
        $teamMember = mysqli_fetch_assoc(mysqli_stmt_get_result($teamStmt));
        mysqli_stmt_close($teamStmt);
        if (!$teamMember) {
            throw new RuntimeException('Employee must be an active member of the selected project team.');
        }
    } elseif ((int)$employee['company_id'] !== $companyId) {
        throw new RuntimeException('Cross-company employees can only be allocated through a shared project team.');
    }

    if (in_array($status, ['planned', 'active'], true)) {
        $overlapEnd = $end ?: '9999-12-31';
        $overlapStmt = mysqli_prepare(
            $conn,
            "SELECT COALESCE(SUM(allocation_percent),0) total_percent
             FROM resource_allocations
             WHERE employee_id=? AND id<>? AND status IN ('planned','active')
               AND start_date<=? AND COALESCE(end_date,'9999-12-31')>=?"
        );
        mysqli_stmt_bind_param($overlapStmt, 'iiss', $employeeId, $id, $overlapEnd, $start);
        mysqli_stmt_execute($overlapStmt);
        $overlap = mysqli_fetch_assoc(mysqli_stmt_get_result($overlapStmt));
        mysqli_stmt_close($overlapStmt);
        if ((float)$overlap['total_percent'] + $percent > 100.0001) {
            throw new RuntimeException('Employee allocation exceeds 100% for the selected date range.');
        }
    }

    if ($id) {
        $oldStmt = mysqli_prepare($conn, 'SELECT * FROM resource_allocations WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($oldStmt, 'ii', $id, $companyId);
        mysqli_stmt_execute($oldStmt);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($oldStmt));
        mysqli_stmt_close($oldStmt);
        if (!$old) {
            throw new RuntimeException('Allocation not found.');
        }
        $stmt = mysqli_prepare($conn, 'UPDATE resource_allocations SET employee_id=?,client_id=?,project_id=?,allocation_type=?,allocation_percent=?,billing_rate=?,billing_cycle=?,salary_cost=?,currency_code=?,start_date=?,end_date=?,status=?,notes=? WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'iiisddsdsssssii', $employeeId, $clientId, $projectId, $type, $percent, $billing, $cycle, $cost, $currency, $start, $end, $status, $notes, $id, $companyId);
    } else {
        $old = null;
        $stmt = mysqli_prepare($conn, 'INSERT INTO resource_allocations(company_id,employee_id,client_id,project_id,allocation_type,allocation_percent,billing_rate,billing_cycle,salary_cost,currency_code,start_date,end_date,status,notes,created_by) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'iiiisddsdsssssi', $companyId, $employeeId, $clientId, $projectId, $type, $percent, $billing, $cycle, $cost, $currency, $start, $end, $status, $notes, $actor);
    }
    mysqli_stmt_execute($stmt);
    if (!$id) {
        $id = mysqli_insert_id($conn);
    }
    mysqli_stmt_close($stmt);

    oecrm_audit($conn, 'resources', $old ? 'update' : 'create', 'resource_allocation', $id, 'Resource allocation saved', $old, [
        'employee_id' => $employeeId,
        'client_id' => $clientId,
        'project_id' => $projectId,
        'allocation_percent' => $percent,
        'status' => $status,
    ]);
    $_SESSION['resource_flash'] = 'Resource allocation saved successfully.';
    header('Location: resources.php');
    exit;
} catch (Throwable $e) {
    $_SESSION['resource_error'] = $e->getMessage();
    header('Location: resourceAllocation.php'.($id ? '?id='.$id : ''));
    exit;
}
