<?php
$active_menu = 'projects';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$companyId = oecrm_current_company_id($conn);
$id = (int) ($_GET['id'] ?? 0);
oecrm_require_permission($conn, 'projects', $id ? 'edit' : 'create');

$project = null;
$selectedTeam = [];
if ($id) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM projectstbl WHERE id=? AND company_id=?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
    mysqli_stmt_execute($stmt);
    $project = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$project) {
        http_response_code(404);
        exit('Project not found.');
    }
    $teamResult = mysqli_query($conn, 'SELECT employee_id FROM project_team_members WHERE project_id=' . (int) $id . ' AND left_at IS NULL');
    while ($teamRow = mysqli_fetch_assoc($teamResult)) {
        $selectedTeam[] = (int) $teamRow['employee_id'];
    }
}

$clients = mysqli_query($conn, 'SELECT id,display_name FROM clients WHERE company_id=' . (int) $companyId . ' AND status IN ("active","prospect") ORDER BY display_name');
$employees = mysqli_query($conn, 'SELECT e.id,e.employeeCode,e.name,c.display_name company_name FROM employeestbl e LEFT JOIN companies c ON c.id=e.company_id WHERE e.status=0 ORDER BY c.display_name,e.name');
$error = $_SESSION['project_error'] ?? '';
unset($_SESSION['project_error']);
?>
<div id="page-wrapper" class="compact-admin-page resource-form-page">
    <div class="foundation-titlebar">
        <a href="projectWorkspace.php"><i class="fa fa-arrow-left"></i> Projects</a>
        <h2><?php echo $id ? 'Edit Project' : 'New Project'; ?></h2>
    </div>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading">Project Setup</div>
        <div class="panel-body">
            <style>.resource-form select.form-control{height:38px}.resource-form select[multiple].form-control{height:160px;padding:8px}.resource-form small{color:#667085}</style>
            <form method="post" action="projectModernAction.php" class="resource-form">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $id ? 'update_project' : 'create_project'; ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="form-group"><label>Project Name</label><input class="form-control" name="project_name" required value="<?php echo oecrm_h($project['projectName'] ?? ''); ?>"></div>
                <div class="form-group">
                    <label>Client</label>
                    <select class="form-control" name="client_id" required>
                        <option value="">Select client</option>
                        <?php while ($client = mysqli_fetch_assoc($clients)): ?>
                            <option value="<?php echo (int) $client['id']; ?>" <?php echo (int) ($project['client_id'] ?? 0) === (int) $client['id'] ? 'selected' : ''; ?>><?php echo oecrm_h($client['display_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group"><label>Status</label><select class="form-control" name="status"><?php foreach (['pending', 'inprogress', 'completed', 'cancel'] as $value): ?><option value="<?php echo $value; ?>" <?php echo ($project['status'] ?? 'pending') === $value ? 'selected' : ''; ?>><?php echo ucwords(str_replace('_', ' ', $value)); ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Priority</label><select class="form-control" name="priority"><?php foreach (['low', 'medium', 'high', 'critical'] as $value): ?><option value="<?php echo $value; ?>" <?php echo ($project['priority'] ?? 'medium') === $value ? 'selected' : ''; ?>><?php echo ucfirst($value); ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Start Date</label><input class="form-control" type="date" name="start_date" value="<?php echo oecrm_h($project['startdate'] ?? date('Y-m-d')); ?>" required></div>
                <div class="form-group"><label>Deadline</label><input class="form-control" type="date" name="end_date" value="<?php echo oecrm_h($project['enddate'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Budget Amount</label><input class="form-control" type="number" min="0" step=".01" name="budget" value="<?php echo oecrm_h($project['amount'] ?? ''); ?>"></div>
                <div class="form-group"><label>Budget Hours</label><input class="form-control" type="number" min="0" step=".25" name="budget_hours" value="<?php echo oecrm_h($project['budget_hours'] ?? ''); ?>"></div>
                <div class="form-group">
                    <label>Team Members</label>
                    <select class="form-control" name="team[]" multiple size="7">
                        <?php while ($employee = mysqli_fetch_assoc($employees)): ?>
                            <option value="<?php echo (int) $employee['id']; ?>" <?php echo in_array((int) $employee['id'], $selectedTeam, true) ? 'selected' : ''; ?>><?php echo oecrm_h($employee['company_name'] . ' - ' . $employee['employeeCode'] . ' - ' . $employee['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <small>Only active employees are listed.</small>
                </div>
                <div class="form-group resource-notes"><label>Description</label><textarea class="form-control" name="description" rows="4"><?php echo oecrm_h($project['description'] ?? ''); ?></textarea></div>
                <div class="resource-actions">
                    <button class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $id ? 'Update Project' : 'Create Project'; ?></button>
                    <a href="<?php echo $id ? 'projectBoard.php?id=' . $id : 'projectWorkspace.php'; ?>" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
