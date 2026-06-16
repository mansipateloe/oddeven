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
$companyId = oecrm_current_company_id($conn);
$actor = (int) $_SESSION['adminId'];
$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);

try {
    if ($action === 'initiate') {
        oecrm_require_permission($conn, 'employee_exit', 'create');
        $employeeId = (int) ($_POST['employee_id'] ?? 0);
        $lastWorkingDate = $_POST['last_working_date'] ?? '';
        $exitType = $_POST['exit_type'] ?? 'resignation';
        $employee = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM employeestbl WHERE id=' . $employeeId . ' AND status=0'));
        if (!$employee || !strtotime($lastWorkingDate)) throw new RuntimeException('Select a valid active employee and last working date.');
        $companyId = (int) $employee['company_id'];
        $existing = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT id FROM employee_exits WHERE employee_id=' . $employeeId . ' AND status<>"completed" LIMIT 1'));
        if ($existing) throw new RuntimeException('An active exit process already exists for this employee.');
        mysqli_begin_transaction($conn);
        $stmt = mysqli_prepare($conn, 'INSERT INTO employee_exits(company_id,employee_id,last_working_date,exit_type,created_by) VALUES(?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'iissi', $companyId, $employeeId, $lastWorkingDate, $exitType, $actor);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        foreach ([['asset','Return all allocated assets'],['access','Revoke all system and account access'],['project','Remove active project allocations'],['document','Issue relieving letter'],['document','Issue experience letter'],['other','Complete knowledge transfer']] as $item) {
            $stmt = mysqli_prepare($conn, 'INSERT INTO exit_checklist(exit_id,item_type,item_label) VALUES(?,?,?)');
            mysqli_stmt_bind_param($stmt, 'iss', $id, $item[0], $item[1]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        $stmt = mysqli_prepare($conn, "INSERT INTO employee_profiles(employee_id,employment_status,exit_date,exit_reason) VALUES(?,'notice_period',?,?) ON DUPLICATE KEY UPDATE employment_status='notice_period',exit_date=VALUES(exit_date),exit_reason=VALUES(exit_reason)");
        mysqli_stmt_bind_param($stmt, 'iss', $employeeId, $lastWorkingDate, $exitType);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
        oecrm_audit($conn, 'employee_exit', 'create', 'employee_exit', $id, 'Employee exit initiated', null, ['employee_id'=>$employeeId,'last_working_date'=>$lastWorkingDate]);
        $_SESSION['exit_flash'] = 'Employee exit initiated.';
        header('Location: exitSettlement.php?id=' . $id);
        exit;
    }

    $exit = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM employee_exits WHERE id=' . $id . ' AND company_id=' . $companyId));
    if (!$exit) throw new RuntimeException('Exit record not found.');

    if ($action === 'check') {
        oecrm_require_permission($conn, 'employee_exit', 'edit');
        $item = (int) ($_POST['item_id'] ?? 0);
        $status = in_array($_POST['status'] ?? '', ['pending','cleared','not_applicable'], true) ? $_POST['status'] : 'pending';
        $stmt = mysqli_prepare($conn, 'UPDATE exit_checklist SET status=?,cleared_by=?,cleared_at=IF(?="cleared",NOW(),NULL) WHERE id=? AND exit_id=?');
        mysqli_stmt_bind_param($stmt, 'sisii', $status, $actor, $status, $item, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header('Location: exitSettlement.php?id=' . $id);
        exit;
    }

    if ($action === 'settle') {
        oecrm_require_permission($conn, 'employee_exit', 'settle');
        $salary = max(0, (float) ($_POST['pending_salary'] ?? 0));
        $retention = max(0, (float) ($_POST['retention_release'] ?? 0));
        $deductions = max(0, (float) ($_POST['deductions'] ?? 0));
        $final = $salary + $retention - $deductions;
        $pending = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM exit_checklist WHERE exit_id=' . $id . ' AND status="pending"'));
        $status = (int) $pending['total'] ? 'clearance' : 'completed';
        mysqli_begin_transaction($conn);
        $stmt = mysqli_prepare($conn, 'UPDATE employee_exits SET pending_salary=?,retention_release=?,deductions=?,final_settlement=?,status=? WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'ddddsii', $salary, $retention, $deductions, $final, $status, $id, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($status === 'completed') {
            $employeeId = (int) $exit['employee_id'];
            mysqli_query($conn, 'UPDATE employeestbl SET status=1,is_admin_access=0 WHERE id=' . $employeeId . ' AND company_id=' . $companyId);
            mysqli_query($conn, "UPDATE employee_profiles SET employment_status='inactive',exit_date='" . mysqli_real_escape_string($conn, $exit['last_working_date']) . "' WHERE employee_id=$employeeId");
            mysqli_query($conn, 'UPDATE employee_shift_assignments SET status=0,effective_to=COALESCE(effective_to,CURDATE()) WHERE employee_id=' . $employeeId . ' AND status=1');
            mysqli_query($conn, 'UPDATE project_team_members SET left_at=COALESCE(left_at,CURDATE()) WHERE employee_id=' . $employeeId . ' AND left_at IS NULL');
            mysqli_query($conn, "UPDATE resource_allocations SET status='completed',end_date=COALESCE(end_date,CURDATE()) WHERE employee_id=$employeeId AND status='active'");
            mysqli_query($conn, "UPDATE access_assignments SET status='revoked',revoked_on=CURDATE(),revoked_by=$actor WHERE employee_id=$employeeId AND status='active'");
            $allocations = mysqli_query($conn, "SELECT * FROM asset_allocations WHERE employee_id=$employeeId AND status='allocated'");
            while ($allocation=mysqli_fetch_assoc($allocations)) {
                mysqli_query($conn, "UPDATE asset_allocations SET status='returned',returned_on=CURDATE(),return_condition='good',returned_by=$actor WHERE id=" . (int) $allocation['id']);
                mysqli_query($conn, "UPDATE assets SET lifecycle_status='available' WHERE id=" . (int) $allocation['asset_id']);
                mysqli_query($conn, "INSERT INTO asset_history(company_id,asset_id,event_type,employee_id,details,performed_by) VALUES($companyId," . (int) $allocation['asset_id'] . ",'returned',$employeeId,'Auto-returned during employee exit',$actor)");
            }
        }
        mysqli_commit($conn);
        oecrm_audit($conn, 'employee_exit', 'settle', 'employee_exit', $id, 'Exit settlement updated', $exit, ['status'=>$status,'final_settlement'=>$final]);
        $_SESSION['exit_flash'] = $status === 'completed' ? 'Exit clearance and final settlement completed.' : 'Settlement saved. Complete all clearance items to deactivate the employee.';
        header('Location: employeeExits.php');
        exit;
    }

    throw new RuntimeException('Invalid action.');
} catch (Throwable $exception) {
    @mysqli_rollback($conn);
    $_SESSION['exit_flash'] = $exception->getMessage();
    header('Location: ' . ($id ? 'exitSettlement.php?id=' . $id : 'employeeExits.php'));
    exit;
}
