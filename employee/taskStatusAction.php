<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
require_once __DIR__.'/../foundation.php';

oecrm_require_employee_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
oecrm_require_csrf();

$employeeId = (int)$_SESSION['employeeId'];
$taskId = (int)($_POST['task_id'] ?? 0);
$status = $_POST['status'] ?? '';
try {
    if (!$taskId) {
        throw new RuntimeException('Select a valid task.');
    }
    $stmt = mysqli_prepare($conn, 'SELECT t.id,t.status,t.company_id,ta.is_primary,e.designation,(SELECT COUNT(*) FROM task_assignees qa JOIN employeestbl qe ON qe.id=qa.employee_id WHERE qa.task_id=t.id AND qa.is_primary=0 AND qe.designation LIKE "%QA%") qa_count FROM tasktbl t JOIN task_assignees ta ON ta.task_id=t.id JOIN employeestbl e ON e.id=ta.employee_id WHERE t.id=? AND ta.employee_id=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ii', $taskId, $employeeId);
    mysqli_stmt_execute($stmt);
    $task = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$task) {
        throw new RuntimeException('This task is not assigned to you.');
    }
    $isQa = ((int)$task['is_primary'] === 0 && stripos((string)$task['designation'], 'QA') !== false);
    $qaWorkflowStatuses = ['in_review', 'to_be_tested', 'staging_server', 'production'];
    if (!$isQa && in_array($task['status'], $qaWorkflowStatuses, true)) {
        throw new RuntimeException('This task is with QA. Only the assigned QA employee can update its review status.');
    }
    $developerTransitions = [
        'open' => ['in_progress', 'on_hold'],
        'in_progress' => ['completed', 'on_hold'],
        'on_hold' => ['in_progress'],
    ];
    $qaTransitions = [
        'in_review' => ['to_be_tested', 'on_hold', 'cancelled'],
        'to_be_tested' => ['in_review', 'staging_server', 'on_hold', 'cancelled'],
        'staging_server' => ['to_be_tested', 'production', 'on_hold', 'cancelled'],
        'production' => ['staging_server', 'closed', 'on_hold', 'cancelled'],
        'on_hold' => ['in_review', 'cancelled'],
    ];
    $allowed = $isQa ? ($qaTransitions[$task['status']] ?? []) : ($developerTransitions[$task['status']] ?? []);
    if (!in_array($status, $allowed, true)) {
        throw new RuntimeException('Select a valid next task status.');
    }
    if (in_array($task['status'], ['cancel', 'cancelled'], true)) {
        throw new RuntimeException('A cancelled task cannot be updated.');
    }
    if (!$isQa && $status === 'completed') {
        $qaCount = (int) $task['qa_count'];
        if ($qaCount === 0) {
            $qaCount = oecrm_assign_task_qa_reviewers($conn, $taskId, $employeeId);
        }
        if ($qaCount > 0) {
            $status = 'in_review';
        }
    }
    $boardStatus = ['open'=>'todo','in_progress'=>'in_progress','completed'=>'review','in_review'=>'review','to_be_tested'=>'review','on_hold'=>'backlog','cancelled'=>'backlog','staging_server'=>'review','production'=>'review','closed'=>'done'][$status] ?? 'todo';
    $stmt = mysqli_prepare($conn, 'UPDATE tasktbl SET status=?,board_status=?,updated_at=NOW() WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'ssi', $status, $boardStatus, $taskId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    oecrm_audit($conn, 'tasks', 'employee_status_update', 'task', $taskId, 'Employee updated assigned task status', ['status'=>$task['status']], ['status'=>$status,'employee_id'=>$employeeId]);
    $_SESSION['task_flash'] = 'Task status updated successfully.';
} catch (Throwable $exception) {
    $_SESSION['task_error'] = $exception->getMessage();
}
header('Location: viewTask.php');
exit;
