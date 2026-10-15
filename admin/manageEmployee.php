<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../foundation.php";
oecrm_require_admin_login();

$companyId = oecrm_current_company_id($conn);

if (isset($_POST["activebtn"]) || isset($_POST["deactivebtn"])) {
    oecrm_require_csrf();
    oecrm_require_permission($conn, "employees", "edit");

    $employeeId = (int) ($_POST["activebtn"] ?? ($_POST["deactivebtn"] ?? 0));
    $status = isset($_POST["activebtn"]) ? 0 : 1;
    if (oecrm_is_super_admin()) {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE employeestbl SET status=? WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, "ii", $status, $employeeId);
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE employeestbl SET status=? WHERE id=? AND company_id=?"
        );
        mysqli_stmt_bind_param($stmt, "iii", $status, $employeeId, $companyId);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION["employee_flash"] =
        $status === 0
            ? "Employee activated successfully."
            : "Employee deactivated successfully.";
    header("Location: manageEmployee.php");
    exit();
}

$active_menu = "employees";
include "header.php";

$employeeSql = 'SELECT e.id,e.employeeCode,e.name,e.designation,e.mobile1,e.mobile2,
                       e.companyEmail,e.personalEmail,e.status,c.display_name company_name
                FROM employeestbl e
                LEFT JOIN companies c ON c.id=e.company_id';
if (oecrm_is_super_admin()) {
    $stmt = mysqli_prepare(
        $conn,
        $employeeSql . " ORDER BY e.status ASC,e.name ASC"
    );
} else {
    $stmt = mysqli_prepare(
        $conn,
        $employeeSql . " WHERE e.company_id=? ORDER BY e.status ASC,e.name ASC"
    );
    mysqli_stmt_bind_param($stmt, "i", $companyId);
}
mysqli_stmt_execute($stmt);
$employees = mysqli_stmt_get_result($stmt);
?>

<style>
#employeeTable .employee-status-cell{vertical-align:middle}
#employeeTable .employee-status-controls{display:flex;flex-direction:column;align-items:flex-start;gap:7px;min-width:112px}
#employeeTable .employee-status-badge{display:inline-flex;align-items:center;gap:7px;min-width:85px;padding:5px 10px;border-radius:20px;font-size:12px;font-weight:700;line-height:18px;white-space:nowrap}
#employeeTable .employee-status-badge i{font-size:7px}
#employeeTable .employee-status-badge.is-active{background:#e8f6ee;color:#1b7445}
#employeeTable .employee-status-badge.is-inactive{background:#f1f3f6;color:#596579}
#employeeTable .employee-status-form{display:block;margin:0}
#employeeTable .employee-status-action{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-width:108px;min-height:32px;padding:5px 10px;border:1px solid;border-radius:7px;background:#fff;font-size:12px;font-weight:600;line-height:20px;white-space:nowrap;cursor:pointer}
#employeeTable .employee-status-action.is-deactivate{border-color:#f1c4c9;color:#b83744}
#employeeTable .employee-status-action.is-deactivate:hover{background:#fff0f1;border-color:#dc9da5}
#employeeTable .employee-status-action.is-activate{border-color:#b9deca;color:#1b7445}
#employeeTable .employee-status-action.is-activate:hover{background:#e8f6ee;border-color:#88c5a5}
#employeeTable .employee-status-action:focus-visible{outline:2px solid #696cff;outline-offset:2px}
</style>
<div id="page-wrapper" class="compact-admin-page">
    <?php if (!empty($_SESSION["employee_flash"])): ?>
        <div class="alert alert-success"><?php echo oecrm_h(
            $_SESSION["employee_flash"]
        ); ?></div>
        <?php unset($_SESSION["employee_flash"]); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION["employee_error"])): ?>
        <div class="alert alert-danger"><?php echo oecrm_h(
            $_SESSION["employee_error"]
        ); ?></div>
        <?php unset($_SESSION["employee_error"]); ?>
    <?php endif; ?>

    <div class="lead-page-header">
        <div>
            <h2>Employees</h2>
            <p><?php echo oecrm_is_super_admin()
                ? "All company employees."
                : "Active and inactive employees for the selected company."; ?></p>
        </div>
        <div>
            <a href="addEmployee.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add Employee</a>
            <a href="manageDesignation.php" class="btn btn-info"><i class="fa fa-briefcase"></i> Manage Designation</a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Employees Table</div>
        <div class="panel-body table-responsive">
            <table class="table table-striped table-bordered table-hover foundation-table" id="employeeTable">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Designation</th>
                        <th>Contact No.</th>
                        
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($employee = mysqli_fetch_assoc($employees)): ?>
                        <tr>
                            <td><?php echo oecrm_h(
                                $employee["employeeCode"]
                            ); ?></td>
                            <td><?php echo oecrm_h($employee["name"]); ?></td>
                            <td><?php echo oecrm_h(
                                $employee["company_name"] ?: "Not assigned"
                            ); ?></td>
                            <td><?php echo oecrm_h(
                                $employee["designation"]
                            ); ?></td>
                            <td>
                                <span class="employee-contact-number"><?php echo oecrm_h($employee["mobile1"]); ?></span>
                                <?php if ($employee["mobile2"]): ?>
                                    <br><span class="employee-contact-number"><?php echo oecrm_h(
                                        $employee["mobile2"]
                                    ); ?></span>
                                <?php endif; ?>
                            </td>
                          
                            <td class="employee-status-cell">
                                <?php $isActive = (int) $employee['status'] === 0; ?>
                                <div class="employee-status-controls">
                                    <span class="employee-status-badge <?php echo $isActive ? 'is-active' : 'is-inactive'; ?>">
                                        <i class="fa fa-circle" aria-hidden="true"></i>
                                        <?php echo $isActive ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <form method="post" class="employee-status-form" action="manageEmployee.php">
                                        <?php echo oecrm_csrf_field(); ?>
                                        <button type="submit"
                                            name="<?php echo $isActive ? 'deactivebtn' : 'activebtn'; ?>"
                                            value="<?php echo (int) $employee['id']; ?>"
                                            class="employee-status-action <?php echo $isActive ? 'is-deactivate' : 'is-activate'; ?>"
                                            title="<?php echo $isActive ? 'Deactivate Employee' : 'Activate Employee'; ?>"
                                            aria-label="<?php echo oecrm_h(($isActive ? 'Deactivate ' : 'Activate ') . $employee['name']); ?>">
                                            <i class="fa <?php echo $isActive ? 'fa-ban' : 'fa-check'; ?>" aria-hidden="true"></i>
                                            <?php echo $isActive ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="employee-actions">
                                <a class="icon-action" href="employeeProfile.php?id=<?php echo (int) $employee[
                                    "id"
                                ]; ?>" title="Employee Profile" aria-label="Employee Profile"><i class="fa fa-user"></i></a>
                                <a class="icon-action" href="editEmployee.php?edit=<?php echo (int) $employee[
                                    "id"
                                ]; ?>" title="Edit" aria-label="Edit Employee"><i class="fa fa-pencil"></i></a>
                                <a class="icon-action" href="employeeContinuations.php?employee_id=<?php echo (int) $employee[
                                    "id"
                                ]; ?>#renewForm" title="Renew / Continue" aria-label="Renew Employee"><i class="fa fa-refresh"></i></a>
                                <form method="post" action="deleteEmployee.php" style="display:inline;">
                                    <?php echo oecrm_csrf_field(); ?>
                                    <input type="hidden" name="delete" value="<?php echo (int) $employee[
                                        "id"
                                    ]; ?>">
                                    <button type="submit" class="icon-action" title="Deactivate" data-confirm="Deactivate this employee? Existing payroll, attendance and project history will be preserved."><i class="fa fa-ban"></i></button>
                                </form>
                                <a href="loginLog.php?id=<?php echo (int) $employee[
                                    "id"
                                ]; ?>" title="Login Log"><i class="fa fa-sign-in"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
mysqli_stmt_close($stmt);
include "footer.php";
?>

