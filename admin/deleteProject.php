<?php
include 'dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();
oecrm_require_csrf();
oecrm_require_permission($conn, 'projects', 'delete');

$id = oecrm_int_param($_GET, 'delete');
$companyId = oecrm_current_company_id($conn);
$project = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM projectstbl WHERE id=' . (int) $id . ' AND company_id=' . (int) $companyId));
if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}
$stmt = mysqli_prepare($conn, "UPDATE projectstbl SET status='cancel' WHERE id = ? AND company_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

mysqli_query($conn, 'UPDATE tasktbl SET status="cancel",board_status="done" WHERE CAST(projectId AS UNSIGNED)=' . (int) $id . ' AND company_id=' . (int) $companyId . ' AND status NOT IN ("completed","2")');
mysqli_query($conn, 'UPDATE project_team_members SET left_at=COALESCE(left_at,CURDATE()) WHERE project_id=' . (int) $id);
oecrm_audit($conn, 'projects', 'archive', 'project', $id, 'Project archived', $project, ['status'=>'cancel']);
$_SESSION['project_flash'] = 'Project archived successfully. Historical tasks and timesheets were preserved.';
header('Location:projectWorkspace.php');
exit;
?>
