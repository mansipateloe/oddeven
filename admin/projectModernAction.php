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

$action = $_POST['action'] ?? '';
$companyId = oecrm_current_company_id($conn);

function oecrm_project_payload($conn, $companyId)
{
    $name = trim($_POST['project_name'] ?? '');
    $clientId = (int) ($_POST['client_id'] ?? 0);
    $priority = $_POST['priority'] ?? 'medium';
    $status = $_POST['status'] ?? 'pending';
    $start = $_POST['start_date'] ?? '';
    $end = $_POST['end_date'] ?? '';
    $budget = (float) ($_POST['budget'] ?? 0);
    $hours = (float) ($_POST['budget_hours'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $team = array_values(array_unique(array_filter(array_map('intval', $_POST['team'] ?? []))));

    if ($name === '' || !$clientId || !strtotime($start) || !strtotime($end)) {
        throw new RuntimeException('Project name, client and dates are required.');
    }

    $client = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM clients WHERE id=' . (int) $clientId . ' AND company_id=' . (int) $companyId));
    if (!$client) {
        throw new RuntimeException('Invalid client.');
    }
    if ($team) {
        $ids = implode(',', array_map('intval', $team));
        $validTeam = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM employeestbl WHERE status=0 AND id IN (' . $ids . ')'));
        if ((int) $validTeam['total'] !== count($team)) {
            throw new RuntimeException('One or more selected team members are inactive or invalid.');
        }
    }

    return compact('name', 'clientId', 'priority', 'status', 'start', 'end', 'budget', 'hours', 'description', 'team', 'client');
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
        $stmt = mysqli_prepare($conn, 'INSERT INTO projectstbl(company_id,client_id,projectName,developerId,status,priority,attachment,startdate,enddate,amount,budget_hours,expence,customerName,nickName,platform,projectType,phone,email,location,country,reference,description) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'iisssssssdssssssssssss', $companyId, $data['clientId'], $data['name'], $empty, $data['status'], $data['priority'], $empty, $data['start'], $data['end'], $data['budget'], $data['hours'], $empty, $data['client']['display_name'], $empty, $empty, $empty, $data['client']['phone'], $data['client']['email'], $data['client']['city'], $data['client']['country'], $empty, $data['description']);
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
        $stmt = mysqli_prepare($conn, 'UPDATE projectstbl SET client_id=?,projectName=?,developerId="",status=?,priority=?,startdate=?,enddate=?,amount=?,budget_hours=?,customerName=?,phone=?,email=?,location=?,country=?,description=? WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'isssssddssssssii', $data['clientId'], $data['name'], $data['status'], $data['priority'], $data['start'], $data['end'], $data['budget'], $data['hours'], $data['client']['display_name'], $data['client']['phone'], $data['client']['email'], $data['client']['city'], $data['client']['country'], $data['description'], $id, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
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
