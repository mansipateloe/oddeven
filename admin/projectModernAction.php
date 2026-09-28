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

$action = $_POST['action'] ?? '';
$companyId = oecrm_current_company_id($conn);
oecrm_project_deadline_ensure_schema($conn);

function oecrm_project_payload($conn, $companyId)
{
    $name = trim($_POST['project_name'] ?? '');
    $clientId = (int) ($_POST['client_id'] ?? 0);
    $priority = $_POST['priority'] ?? 'medium';
    $status = $_POST['status'] ?? 'pending';
    $start = trim((string) ($_POST['start_date'] ?? ''));
    $end = oecrm_project_clean_date($_POST['end_date'] ?? '');
    $budget = (float) ($_POST['budget'] ?? 0);
    $extraScopeValue = max(0, (float) ($_POST['extra_scope_value'] ?? 0));
    $hours = (float) ($_POST['budget_hours'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $deadlineReason = trim((string) ($_POST['deadline_reason'] ?? ''));
    $team = array_values(array_unique(array_filter(array_map('intval', $_POST['team'] ?? []))));

    if ($name === '' || !$clientId || !strtotime($start)) {
        throw new RuntimeException('Project name, client and start date are required.');
    }
    $start = date('Y-m-d', strtotime($start));
    if ($end !== null && strtotime($end) < strtotime($start)) {
        throw new RuntimeException('Deadline date cannot be earlier than the start date.');
    }

    $client = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM clients WHERE id=' . (int) $clientId . ' AND company_id=' . (int) $companyId));
    if (!$client) {
        throw new RuntimeException('Invalid client.');
    }
    if (!$team) {
        throw new RuntimeException('Please select at least one team member.');
    }
    if ($team) {
        $ids = implode(',', array_map('intval', $team));
        $validTeam = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM employeestbl WHERE status=0 AND id IN (' . $ids . ')'));
        if ((int) $validTeam['total'] !== count($team)) {
            throw new RuntimeException('One or more selected team members are inactive or invalid.');
        }
    }

    return compact('name', 'clientId', 'priority', 'status', 'start', 'end', 'budget', 'extraScopeValue', 'hours', 'description', 'deadlineReason', 'team', 'client');
}

function oecrm_sync_project_team($conn, $projectId, array $team, $start)
{
    mysqli_query($conn, 'UPDATE project_team_members SET left_at=COALESCE(left_at,CURDATE()) WHERE project_id=' . (int) $projectId);
    if (!$team) return;
    $stmt = mysqli_prepare($conn, 'INSERT INTO project_team_members(project_id,employee_id,joined_at,left_at) VALUES(?,?,?,NULL) ON DUPLICATE KEY UPDATE joined_at=VALUES(joined_at),left_at=NULL');
    foreach ($team as $employeeId) {
        mysqli_stmt_bind_param($stmt, 'iis', $projectId, $employeeId, $start);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
}

try {
    if ($action === 'create_project') {
        oecrm_require_permission($conn, 'projects', 'create');
        $data = oecrm_project_payload($conn, $companyId);
        $empty = '';
        $stmt = mysqli_prepare($conn, 'INSERT INTO projectstbl(company_id,client_id,projectName,developerId,status,priority,attachment,startdate,enddate,original_deadline,current_deadline,amount,extra_scope_value,budget_hours,expence,customerName,nickName,platform,projectType,phone,email,location,country,reference,description) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'iisssssssssdddsssssssssss', $companyId, $data['clientId'], $data['name'], $empty, $data['status'], $data['priority'], $empty, $data['start'], $data['end'], $data['end'], $data['end'], $data['budget'], $data['extraScopeValue'], $data['hours'], $empty, $data['client']['display_name'], $empty, $empty, $empty, $data['client']['phone'], $data['client']['email'], $data['client']['city'], $data['client']['country'], $empty, $data['description']);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        oecrm_sync_project_team($conn, $id, $data['team'], $data['start']);
        oecrm_audit($conn, 'projects', 'create', 'project', $id, 'Project created', null, ['project_name' => $data['name'], 'client_id' => $data['clientId'], 'team' => $data['team']]);
        $_SESSION['project_flash'] = 'Project created successfully.';
        header('Location: projectBoard.php?id=' . $id);
        exit;
    }

    if ($action === 'update_project') {
        oecrm_require_permission($conn, 'projects', 'edit');
        $id = (int) ($_POST['id'] ?? 0);
        $old = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM projectstbl WHERE id=' . (int) $id . ' AND company_id=' . (int) $companyId));
        if (!$old) {
            throw new RuntimeException('Project not found.');
        }
        $data = oecrm_project_payload($conn, $companyId);
        $oldDeadline = oecrm_project_current_deadline($old);
        $newDeadline = $data['end'];
        $deadlineChanged = $oldDeadline !== $newDeadline;
        $deadlineExtended = $oldDeadline && $newDeadline && strtotime($newDeadline) > strtotime($oldDeadline);
        if ($deadlineChanged && $oldDeadline && $newDeadline && $data['deadlineReason'] === '') {
            throw new RuntimeException('Please enter a reason for changing the project deadline.');
        }
        $deadlineCountIncrement = $deadlineExtended ? 1 : 0;
        $originalDeadlineSeed = oecrm_project_original_deadline($old) ?: $newDeadline;
        $stmt = mysqli_prepare($conn, 'UPDATE projectstbl SET client_id=?,projectName=?,developerId="",status=?,priority=?,startdate=?,enddate=?,original_deadline=COALESCE(NULLIF(original_deadline,"0000-00-00"),?),current_deadline=?,amount=?,extra_scope_value=?,budget_hours=?,deadline_extended_count=deadline_extended_count+?,customerName=?,phone=?,email=?,location=?,country=?,description=? WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'isssssssdddissssssii', $data['clientId'], $data['name'], $data['status'], $data['priority'], $data['start'], $data['end'], $originalDeadlineSeed, $data['end'], $data['budget'], $data['extraScopeValue'], $data['hours'], $deadlineCountIncrement, $data['client']['display_name'], $data['client']['phone'], $data['client']['email'], $data['client']['city'], $data['client']['country'], $data['description'], $id, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($deadlineChanged && ($oldDeadline || $newDeadline)) {
            $actor = (int) $_SESSION['adminId'];
            $reason = $data['deadlineReason'] ?: 'Project deadline set from project editor.';
            $changeType = $deadlineExtended ? 'manual_extension' : 'manual_update';
            $statusValue = 'approved';
            $approvedAt = date('Y-m-d H:i:s');
            $scopeDelta = max(0, $data['extraScopeValue'] - (float) ($old['extra_scope_value'] ?? 0));
            $stmt = mysqli_prepare($conn, 'INSERT INTO project_deadline_history(company_id,project_id,task_id,old_deadline,new_deadline,reason,change_type,status,scope_value,requested_by,approved_by,approved_at) VALUES(?,?,NULL,?,?,?,?,?,?,?,?,?)');
            mysqli_stmt_bind_param($stmt, 'iisssssdiis', $companyId, $id, $oldDeadline, $newDeadline, $reason, $changeType, $statusValue, $scopeDelta, $actor, $actor, $approvedAt);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        oecrm_sync_project_team($conn, $id, $data['team'], $data['start']);
        oecrm_audit($conn, 'projects', 'update', 'project', $id, 'Project updated', $old, ['project_name' => $data['name'], 'client_id' => $data['clientId'], 'team' => $data['team']]);
        $_SESSION['project_flash'] = 'Project updated successfully.';
        header('Location: projectBoard.php?id=' . $id);
        exit;
    }

    throw new RuntimeException('Invalid action.');
} catch (Throwable $exception) {
    $_SESSION['project_error'] = $exception->getMessage();
    $id = (int) ($_POST['id'] ?? 0);
    header('Location: projectEditor.php' . ($id ? '?id=' . $id : ''));
    exit;
}

