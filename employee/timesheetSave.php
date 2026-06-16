<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_employee_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
oecrm_require_csrf();

$employeeId = (int) $_SESSION['employeeId'];
try {
    $projectId = (int) ($_POST['project_id'] ?? 0);
    $taskId = (int) ($_POST['task_id'] ?? 0);
    $date = $_POST['work_date'] ?? '';
    $hours = (float) ($_POST['hours'] ?? 0);
    $billable = (float) ($_POST['billable_hours'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = ($_POST['save_mode'] ?? 'draft') === 'submitted' ? 'submitted' : 'draft';
    if (!$projectId || !$taskId || !strtotime($date) || $date > date('Y-m-d') || $hours <= 0 || $hours > 24 || $billable < 0 || $billable > $hours || $description === '') {
        throw new RuntimeException('Select an assigned project/task and enter valid date, hours and work description.');
    }
    $project = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT p.id,p.company_id
        FROM projectstbl p
        WHERE p.id=' . $projectId . ' AND (
            EXISTS(SELECT 1 FROM project_team_members tm WHERE tm.project_id=p.id AND tm.employee_id=' . $employeeId . ' AND tm.left_at IS NULL)
            OR EXISTS(SELECT 1 FROM tasktbl assigned_task JOIN task_assignees assigned_to ON assigned_to.task_id=assigned_task.id WHERE CAST(assigned_task.projectId AS UNSIGNED)=p.id AND assigned_to.employee_id=' . $employeeId . ')
        ) LIMIT 1'));
    if (!$project) {
        throw new RuntimeException('Invalid or unassigned project.');
    }
    $companyId = (int) $project['company_id'];
    $task = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT t.id FROM tasktbl t JOIN task_assignees ta ON ta.task_id=t.id WHERE t.id=' . $taskId . ' AND t.company_id=' . $companyId . ' AND CAST(t.projectId AS UNSIGNED)=' . $projectId . ' AND ta.employee_id=' . $employeeId . ' AND t.status NOT IN ("2","cancel","cancelled")'));
    if (!$task) {
        throw new RuntimeException('Selected task does not belong to this project or employee.');
    }
    $safeDate = mysqli_real_escape_string($conn, $date);
    $daily = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(hours),0) total FROM timesheet_entries WHERE employee_id=$employeeId AND work_date='$safeDate'"));
    if ((float) $daily['total'] + $hours > 24) {
        throw new RuntimeException('Total daily timesheet hours cannot exceed 24.');
    }
    $attendance = mysqli_fetch_assoc(mysqli_query($conn, "SELECT effective_minutes FROM attendance_sessions WHERE employee_id=$employeeId AND attendance_date='$safeDate' LIMIT 1"));
    if ($status === 'submitted' && $attendance && $hours * 60 > (int) $attendance['effective_minutes']) {
        throw new RuntimeException('Timesheet hours cannot exceed attendance effective hours.');
    }
    $submitted = $status === 'submitted' ? date('Y-m-d H:i:s') : null;
    $taskValue = $taskId;
    $stmt = mysqli_prepare($conn, 'INSERT INTO timesheet_entries(company_id,employee_id,project_id,task_id,work_date,description,hours,billable_hours,status,submitted_at) VALUES(?,?,?,?,?,?,?,?,?,?)');
    mysqli_stmt_bind_param($stmt, 'iiiissddss', $companyId, $employeeId, $projectId, $taskValue, $date, $description, $hours, $billable, $status, $submitted);
    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    oecrm_audit($conn, 'timesheets', 'create', 'timesheet', $id, 'Timesheet entry created', null, ['employee_id'=>$employeeId,'project_id'=>$projectId,'task_id'=>$taskId,'hours'=>$hours,'status'=>$status]);
    $_SESSION['timesheet_flash'] = 'Timesheet entry saved.';
} catch (Throwable $exception) {
    $_SESSION['timesheet_error'] = $exception->getMessage();
}
header('Location: timesheets.php');
exit;
