<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../projectDeadlineHelpers.php';

oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
oecrm_require_csrf();
oecrm_require_permission($conn, 'projects', 'edit');

$companyId = oecrm_current_company_id($conn);
oecrm_project_deadline_ensure_schema($conn);
$action = $_POST['action'] ?? '';

function oecrm_sync_task_assignee($conn, $taskId, $projectId, $employeeId, $actorId)
{
    $stmt = mysqli_prepare($conn, 'DELETE FROM task_assignees WHERE task_id=?');
    mysqli_stmt_bind_param($stmt, 'i', $taskId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($conn, 'INSERT INTO task_assignees(task_id,employee_id,is_primary,assigned_by) VALUES(?,?,1,?)');
    mysqli_stmt_bind_param($stmt, 'iii', $taskId, $employeeId, $actorId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return oecrm_assign_task_qa_reviewers($conn, $taskId, $actorId);
}

function oecrm_ensure_task_attachment_table($conn)
{
    mysqli_query($conn, 'CREATE TABLE IF NOT EXISTS task_attachments (id BIGINT AUTO_INCREMENT PRIMARY KEY, task_id INT NOT NULL, stored_name VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, mime_type VARCHAR(100) NULL, file_size BIGINT NOT NULL DEFAULT 0, uploaded_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_task_attachments_task (task_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
}
function oecrm_store_task_attachment($conn, $taskId, $actorId)
{
    if (empty($_FILES['task_attachment']) || ($_FILES['task_attachment']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return;
    }
    oecrm_ensure_task_attachment_table($conn);
    if ($_FILES['task_attachment']['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Task attachment upload failed.');
    }
    $allowedExtensions = ['pdf','doc','docx','xls','xlsx','png','jpg','jpeg','txt'];
    $original = basename((string) $_FILES['task_attachment']['name']);
    $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('Task attachment file type is not allowed.');
    }
    if ((int) $_FILES['task_attachment']['size'] > 10 * 1024 * 1024) {
        throw new RuntimeException('Task attachment must be 10MB or smaller.');
    }
    $storage = __DIR__ . '/../storage/task_attachments';
    if (!is_dir($storage)) {
        mkdir($storage, 0775, true);
    }
    $stored = 'task_' . (int) $taskId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $target = $storage . '/' . $stored;
    if (!move_uploaded_file($_FILES['task_attachment']['tmp_name'], $target)) {
        throw new RuntimeException('Task attachment could not be saved.');
    }
    $mime = function_exists('mime_content_type') ? (mime_content_type($target) ?: 'application/octet-stream') : ($_FILES['task_attachment']['type'] ?? 'application/octet-stream');
    $size = (int) filesize($target);
    $stmt = mysqli_prepare($conn, 'INSERT INTO task_attachments(task_id,stored_name,original_name,mime_type,file_size,uploaded_by) VALUES(?,?,?,?,?,?)');
    mysqli_stmt_bind_param($stmt, 'isssii', $taskId, $stored, $original, $mime, $size, $actorId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $legacyStmt = mysqli_prepare($conn, 'UPDATE tasktbl SET files=?,filePath=? WHERE id=?');
    mysqli_stmt_bind_param($legacyStmt, 'ssi', $original, $stored, $taskId);
    mysqli_stmt_execute($legacyStmt);
    mysqli_stmt_close($legacyStmt);
}

$transactionStarted = false;
function oecrm_apply_task_deadline_policy($conn, $companyId, array $project, $taskId, $expectedDate, $deadlineAction, $newProjectDeadline, $deadlineReason, $extraScopeValue, $actorId)
{
    $currentDeadline = oecrm_project_current_deadline($project);
    $isBeyondDeadline = oecrm_project_task_beyond_deadline($expectedDate, $currentDeadline);
    if ($deadlineAction === 'extend') {
        $newProjectDeadline = oecrm_project_clean_date($newProjectDeadline) ?: $expectedDate;
        if ($isBeyondDeadline && strtotime($newProjectDeadline) < strtotime($expectedDate)) {
            throw new RuntimeException('New project deadline must be on or after the task expected date.');
        }
        if ($currentDeadline !== null && strtotime($newProjectDeadline) <= strtotime($currentDeadline)) {
            throw new RuntimeException('New project deadline must be later than current project deadline.');
        }
        oecrm_project_deadline_extend(
            $conn,
            (int) $companyId,
            (int) $project['id'],
            (int) $taskId,
            $currentDeadline,
            $newProjectDeadline,
            $deadlineReason,
            (float) $extraScopeValue,
            (int) $actorId,
            'extra_task'
        );
        return ' Project deadline extended to ' . oecrm_project_deadline_format($newProjectDeadline) . '.';
    }

    return $isBeyondDeadline ? ' Task due date is beyond project deadline; deadline kept same.' : '';
}

try {
    $id = (int) ($_POST['id'] ?? 0);
    $projectId = (int) ($_POST['project_id'] ?? 0);
    $developerId = (int) ($_POST['developer_id'] ?? 0);
    $title = trim($_POST['task_title'] ?? '');
    $details = trim($_POST['task_details'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $allowedStatuses = ['open','in_progress','completed','in_review','to_be_tested','on_hold','cancelled','staging_server','production','closed'];
    $status = in_array($_POST['status'] ?? '', $allowedStatuses, true) ? $_POST['status'] : 'open';
    $assignDate = oecrm_project_clean_date($_POST['assign_date'] ?? '') ?: date('Y-m-d');
    $expectedDate = oecrm_project_clean_date($_POST['expected_date'] ?? '') ?: $assignDate;
    $estimatedHours = (float) ($_POST['estimated_hours'] ?? 0);
    $deadlineAction = ($_POST['deadline_action'] ?? 'keep') === 'extend' ? 'extend' : 'keep';
    $newProjectDeadline = oecrm_project_clean_date($_POST['new_project_deadline'] ?? '');
    $deadlineReason = trim((string) ($_POST['deadline_reason'] ?? ''));
    $extraScopeValue = max(0, (float) ($_POST['extra_scope_value'] ?? 0));
    $actorId = (int) $_SESSION['adminId'];

    $project = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM projectstbl WHERE id=' . (int) $projectId . ' AND company_id=' . (int) $companyId));
    $assignee = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT e.id FROM project_team_members tm JOIN employeestbl e ON e.id=tm.employee_id WHERE tm.project_id=' . (int) $projectId . ' AND tm.employee_id=' . (int) $developerId . ' AND tm.left_at IS NULL AND e.status=0 LIMIT 1'));
    if (!$project || !$assignee || $title === '') {
        throw new RuntimeException('Project, assignee and task title are required.');
    }

    if ($action === 'create_task') {
        mysqli_begin_transaction($conn);
        $transactionStarted = true;
        $legacyAssignee = '';
        $boardStatus = ['open'=>'todo','in_progress'=>'in_progress','completed'=>'review','in_review'=>'review','to_be_tested'=>'review','on_hold'=>'backlog','cancelled'=>'backlog','staging_server'=>'review','production'=>'review','closed'=>'done'][$status] ?? 'todo';
        $stmt = mysqli_prepare($conn, 'INSERT INTO tasktbl(company_id,projectId,developerId,assignDate,expectedDate,taskTitle,task_details,priority,estimated_hours,status,board_status,files,filePath,end_time,totalHour) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'iissssssdssssss', $companyId, $projectId, $legacyAssignee, $assignDate, $expectedDate, $title, $details, $priority, $estimatedHours, $status, $boardStatus, $legacyAssignee, $legacyAssignee, $legacyAssignee, $legacyAssignee);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        $qaCount = oecrm_sync_task_assignee($conn, $id, $projectId, $developerId, (int) $_SESSION['adminId']);
        oecrm_store_task_attachment($conn, $id, (int) $_SESSION['adminId']);
        $deadlineMessage = oecrm_apply_task_deadline_policy($conn, $companyId, $project, $id, $expectedDate, $deadlineAction, $newProjectDeadline, $deadlineReason, $extraScopeValue, $actorId);
        if ($status === 'completed' && $qaCount > 0) {
            mysqli_query($conn, "UPDATE tasktbl SET status='in_review',board_status='review' WHERE id=" . (int) $id);
        }
        oecrm_audit($conn, 'tasks', 'create', 'task', $id, 'Task created', null, ['project_id' => $projectId, 'task_title' => $title]);
        mysqli_commit($conn);
        $transactionStarted = false;
        $_SESSION['project_flash'] = 'Task created successfully.' . $deadlineMessage;
        header('Location: projectBoard.php?id=' . $projectId);
        exit;
    }

    if ($action === 'update_task') {
        mysqli_begin_transaction($conn);
        $transactionStarted = true;
        $old = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM tasktbl WHERE id=' . (int) $id . ' AND company_id=' . (int) $companyId));
        if (!$old) {
            throw new RuntimeException('Task not found.');
        }
        $boardStatus = ['open'=>'todo','in_progress'=>'in_progress','completed'=>'review','in_review'=>'review','to_be_tested'=>'review','on_hold'=>'backlog','cancelled'=>'backlog','staging_server'=>'review','production'=>'review','closed'=>'done'][$status] ?? 'todo';
        $stmt = mysqli_prepare($conn, 'UPDATE tasktbl SET projectId=?,developerId="",assignDate=?,expectedDate=?,taskTitle=?,task_details=?,priority=?,estimated_hours=?,status=?,board_status=? WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'isssssdssii', $projectId, $assignDate, $expectedDate, $title, $details, $priority, $estimatedHours, $status, $boardStatus, $id, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $qaCount = oecrm_sync_task_assignee($conn, $id, $projectId, $developerId, (int) $_SESSION['adminId']);
        oecrm_store_task_attachment($conn, $id, (int) $_SESSION['adminId']);
        $deadlineMessage = oecrm_apply_task_deadline_policy($conn, $companyId, $project, $id, $expectedDate, $deadlineAction, $newProjectDeadline, $deadlineReason, $extraScopeValue, $actorId);
        if ($status === 'completed' && $qaCount > 0) {
            mysqli_query($conn, "UPDATE tasktbl SET status='in_review',board_status='review' WHERE id=" . (int) $id);
        }
        oecrm_audit($conn, 'tasks', 'update', 'task', $id, 'Task updated', $old, ['project_id' => $projectId, 'task_title' => $title]);
        mysqli_commit($conn);
        $transactionStarted = false;
        $_SESSION['project_flash'] = 'Task updated successfully.' . $deadlineMessage;
        header('Location: projectBoard.php?id=' . $projectId);
        exit;
    }

    throw new RuntimeException('Invalid action.');
} catch (Throwable $exception) {
    if ($transactionStarted) {
        mysqli_rollback($conn);
    }
    $_SESSION['task_error'] = $exception->getMessage();
    $redirectProject = (int) ($_POST['project_id'] ?? 0);
    $redirectTask = (int) ($_POST['id'] ?? 0);
    header('Location: taskEditor.php' . ($redirectTask ? '?id=' . $redirectTask : '?project_id=' . $redirectProject));
    exit;
}

