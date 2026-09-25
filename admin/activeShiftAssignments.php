<?php
$active_menu = "attendance";
include "header.php";
require_once __DIR__ . "/../foundation.php";
oecrm_require_permission($conn, "shift_assignments", "view");
$companyId = oecrm_current_company_id($conn);
$message = $_SESSION["shift_flash"] ?? "";
unset($_SESSION["shift_flash"]);
$shifts = mysqli_query(
    $conn,
    "SELECT id,name,start_time,end_time FROM shifts WHERE status=1 AND company_id=" .
        (int) $companyId .
        " ORDER BY name"
);
$employees = mysqli_query(
    $conn,
    "SELECT id,employeeCode,name FROM employeestbl WHERE company_id=" .
        (int) $companyId .
        " AND status=0 ORDER BY name"
);
$list = mysqli_query(
    $conn,
    "SELECT e.id employee_id,e.employeeCode,e.name,a.effective_from,s.name shift_name,s.start_time,s.end_time FROM employeestbl e LEFT JOIN employee_shift_assignments a ON a.id=(SELECT a2.id FROM employee_shift_assignments a2 WHERE a2.employee_id=e.id AND a2.status=1 AND a2.effective_from<=CURDATE() AND (a2.effective_to IS NULL OR a2.effective_to>=CURDATE()) ORDER BY a2.effective_from DESC,a2.id DESC LIMIT 1) LEFT JOIN shifts s ON s.id=a.shift_id WHERE e.company_id=" .
        (int) $companyId .
        " AND e.status=0 ORDER BY e.name"
);
$history = mysqli_query(
    $conn,
    "SELECT h.*,e.name employee_name,os.name old_shift,ns.name new_shift FROM employee_shift_history h JOIN employeestbl e ON e.id=h.employee_id LEFT JOIN shifts os ON os.id=h.old_shift_id JOIN shifts ns ON ns.id=h.new_shift_id WHERE e.company_id=" .
        (int) $companyId .
        " ORDER BY h.id DESC LIMIT 100"
);
?>
<div id="page-wrapper" class="compact-admin-page shift-page">
  <?php if ($message): ?><div class="alert alert-success"><?php echo oecrm_h(
    $message
); ?></div><?php endif; ?>
  <div class="panel panel-default">
    <div class="panel-heading">Assign Employee Shift</div>
    <div class="panel-body">
      <form method="post" action="shiftAction.php" class="shift-assignment-form"><?php echo oecrm_csrf_field(); ?><input
          type="hidden" name="action" value="assign">
        <div class="form-group"><label>Employee</label><select class="form-control" name="employee_id" required>
            <option value="">Select active employee</option><?php while (
    $employee = mysqli_fetch_assoc($employees)
): ?><option value="<?php echo (int) $employee["id"]; ?>"><?php echo oecrm_h(
    $employee["employeeCode"] . " - " . $employee["name"]
); ?></option><?php endwhile; ?>
          </select></div>
        <div class="form-group"><label>Shift</label><select class="form-control" name="shift_id" required>
            <option value="">Select shift</option><?php while (
    $shift = mysqli_fetch_assoc($shifts)
): ?><option value="<?php echo (int) $shift["id"]; ?>"><?php echo oecrm_h(
    $shift["name"] .
        " (" .
        substr($shift["start_time"], 0, 5) .
        " - " .
        substr($shift["end_time"], 0, 5) .
        ")"
); ?></option><?php endwhile; ?>
          </select></div>
        <div class="form-group"><label>Effective From</label><input type="date" class="form-control"
            name="effective_from" required value="<?php echo date(
    "Y-m-d"
); ?>"></div>
        <div class="form-group"><label>Reason</label><input class="form-control" name="reason"></div><button
          class="btn btn-primary"><i class="fa fa-random"></i> Assign Shift</button>
      </form>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">Current Active Employee Shifts</div>
    <div class="panel-body table-responsive">
      <table class="table foundation-table">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Current Shift</th>
            <th>Time</th>
            <th>Effective From</th>
          </tr>
        </thead>
        <tbody><?php while (
    $row = mysqli_fetch_assoc($list)
): ?><tr>
            <td><strong><?php echo oecrm_h(
    $row["name"]
); ?></strong><small><?php echo oecrm_h(
    $row["employeeCode"]
); ?></small></td>
            <td><?php echo oecrm_h(
    $row["shift_name"] ?: "Unassigned"
); ?></td>
            <td><?php echo $row["shift_name"]
    ? oecrm_h(
        substr($row["start_time"], 0, 5) .
            " - " .
            substr($row["end_time"], 0, 5)
    )
    : "-"; ?></td>
            <td><?php echo oecrm_h(
    $row["effective_from"] ?: "-"
); ?></td>
          </tr><?php endwhile; ?></tbody>
      </table>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">Shift Change History</div>
    <div class="panel-body table-responsive">
      <table class="table foundation-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Employee</th>
            <th>Previous</th>
            <th>New</th>
            <th>Reason</th>
          </tr>
        </thead>
        <tbody><?php while (
    $row = mysqli_fetch_assoc($history)
): ?><tr>
            <td><?php echo oecrm_h(
    $row["effective_from"]
); ?></td>
            <td><?php echo oecrm_h(
    $row["employee_name"]
); ?></td>
            <td><?php echo oecrm_h(
    $row["old_shift"] ?: "None"
); ?></td>
            <td><?php echo oecrm_h(
    $row["new_shift"]
); ?></td>
            <td><?php echo oecrm_h(
    $row["reason"]
); ?></td>
          </tr><?php endwhile; ?></tbody>
      </table>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>