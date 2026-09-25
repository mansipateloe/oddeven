<?php
$active_menu = "activity_audit";
$active_submenu = "activity_dashboard";
include "header.php";
require_once __DIR__ . "/../foundation.php";
oecrm_require_permission($conn, "activity_audit", "view");
$companyId = oecrm_current_company_id($conn);
$module = trim($_GET["module"] ?? "");
$actorType = trim($_GET["actor_type"] ?? "");
$employeeId = max(0, (int) ($_GET["employee_id"] ?? 0));
$risk = trim($_GET["risk"] ?? "");
$from = $_GET["from"] ?? date("Y-m-d", strtotime("-30 days"));
$to = $_GET["to"] ?? date("Y-m-d");
$where = [
    "a.company_id=" . (int) $companyId,
    "a.created_at>='" . mysqli_real_escape_string($conn, $from) . " 00:00:00'",
    "a.created_at<='" . mysqli_real_escape_string($conn, $to) . " 23:59:59'",
];
if ($module !== "") {
    $where[] =
        "a.module_key='" . mysqli_real_escape_string($conn, $module) . "'";
}
if (in_array($actorType, ["admin", "employee", "system"], true)) {
    $where[] = "a.actor_type='" . $actorType . "'";
}
if ($employeeId) {
    $where[] = "a.employee_id=" . (int) $employeeId;
}
if (in_array($risk, ["normal", "review", "suspicious"], true)) {
    $where[] = "a.risk_level='" . $risk . "'";
}
$whereSql = implode(" AND ", $where);
$logs = mysqli_query(
    $conn,
    "SELECT a.*,COALESCE(e.name,ad.uname,CONCAT(UPPER(LEFT(a.actor_type,1)),SUBSTRING(a.actor_type,2),' #',a.actor_id)) actor_name,e.employeeCode FROM audit_logs a LEFT JOIN employeestbl e ON e.id=a.employee_id LEFT JOIN admins ad ON a.actor_type='admin' AND ad.id=a.actor_id AND a.employee_id IS NULL WHERE $whereSql ORDER BY a.id DESC LIMIT 500"
);
$stats = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) total,SUM(action='login') logins,SUM(action='failed_login') failed,SUM(risk_level='suspicious') suspicious,COUNT(DISTINCT employee_id) employees FROM audit_logs a WHERE $whereSql"
    )
);
$modules = mysqli_query(
    $conn,
    "SELECT DISTINCT module_key FROM audit_logs WHERE company_id=" .
        (int) $companyId .
        " ORDER BY module_key"
);
$employees = mysqli_query(
    $conn,
    "SELECT id,employeeCode,name FROM employeestbl WHERE company_id=" .
        (int) $companyId .
        " ORDER BY name"
);
$settings = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT * FROM audit_retention_settings WHERE company_id=" .
            (int) $companyId
    )
);
$flash = $_SESSION["audit_flash"] ?? "";
unset($_SESSION["audit_flash"]);
$query = $_GET;
$queryString = http_build_query($query);
?><div id="page-wrapper" class="compact-admin-page activity-audit-page"><?php if (
    $flash
): ?><div class="alert alert-success"><?php echo oecrm_h(
    $flash
); ?></div><?php endif; ?><div class="activity-summary">
    <div><i class="fa fa-list-alt"></i><span><small>Activities</small><strong><?php echo (int) $stats[
    "total"
]; ?></strong></span></div>
    <div><i class="fa fa-sign-in"></i><span><small>Logins</small><strong><?php echo (int) $stats[
    "logins"
]; ?></strong></span></div>
    <div><i class="fa fa-user-times"></i><span><small>Failed Logins</small><strong><?php echo (int) $stats[
    "failed"
]; ?></strong></span></div>
    <div class="risk"><i class="fa fa-exclamation-triangle"></i><span><small>Suspicious</small><strong><?php echo (int) $stats[
    "suspicious"
]; ?></strong></span></div>
    <div><i class="fa fa-users"></i><span><small>Employees</small><strong><?php echo (int) $stats[
    "employees"
]; ?></strong></span></div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading foundation-heading"><span>Employee Activity & Audit Dashboard</span>
      <div><a class="btn btn-default btn-sm" href="auditExport.php?<?php echo oecrm_h(
    $queryString
); ?>"><i class="fa fa-download"></i> Export CSV</a></div>
    </div>
    <div class="panel-body">
      <form method="get" class="activity-filters"><select class="form-control" name="module">
          <option value="">All modules</option><?php while (
    $m = mysqli_fetch_assoc($modules)
): ?><option value="<?php echo oecrm_h(
    $m["module_key"]
); ?>" <?php echo $module === $m["module_key"]
    ? "selected"
    : ""; ?>><?php echo oecrm_h(
    ucwords(str_replace("_", " ", $m["module_key"]))
); ?></option><?php endwhile; ?>
        </select><select class="form-control" name="employee_id">
          <option value="0">All employees</option><?php while (
    $e = mysqli_fetch_assoc($employees)
): ?><option value="<?php echo (int) $e["id"]; ?>" <?php echo $employeeId ===
(int) $e["id"]
    ? "selected"
    : ""; ?>><?php echo oecrm_h(
    $e["employeeCode"] . " - " . $e["name"]
); ?></option><?php endwhile; ?>
        </select><select class="form-control" name="actor_type">
          <option value="">All actors</option><?php foreach (
    ["admin", "employee", "system"]
    as $v
): ?><option <?php echo $actorType === $v
    ? "selected"
    : ""; ?>><?php echo ucfirst(
    $v
); ?></option><?php endforeach; ?>
        </select><select class="form-control" name="risk">
          <option value="">All risk levels</option><?php foreach (
    ["normal", "review", "suspicious"]
    as $v
): ?><option <?php echo $risk === $v ? "selected" : ""; ?>><?php echo ucfirst(
    $v
); ?></option><?php endforeach; ?>
        </select><input type="date" class="form-control" name="from" value="<?php echo oecrm_h(
    $from
); ?>"><input type="date" class="form-control" name="to" value="<?php echo oecrm_h(
    $to
); ?>"><button class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button></form>
      <div class="table-responsive">
        <table class="table foundation-table activity-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Actor</th>
              <th>Activity</th>
              <th>Entity</th>
              <th>Device & IP</th>
              <th>Risk</th>
              <th>Changes</th>
            </tr>
          </thead>
          <tbody><?php
if (
    mysqli_num_rows($logs) === 0
): ?><tr>
              <td colspan="7" class="empty-cell">No activities found for these filters.</td>
            </tr><?php endif;
while ($r = mysqli_fetch_assoc($logs)):
    $hasChanges =
        $r["old_values"] ||
        $r["new_values"]; ?><tr class="risk-row-<?php echo $r[
    "risk_level"
]; ?>">
              <td><?php echo date(
    "d M Y",
    strtotime($r["created_at"])
); ?><small><?php echo date(
    "h:i:s A",
    strtotime($r["created_at"])
); ?></small></td>
              <td><strong><?php echo oecrm_h(
    $r["actor_name"]
); ?></strong><small><?php echo oecrm_h(
    ucfirst($r["actor_type"]) .
        ($r["employeeCode"] ? " | " . $r["employeeCode"] : "")
); ?></small></td>
              <td><strong><?php echo oecrm_h(
    ucwords(str_replace("_", " ", $r["action"]))
); ?></strong><small><?php echo oecrm_h(
    ucwords(str_replace("_", " ", $r["module_key"]))
); ?> | <?php echo oecrm_h(
     $r["description"]
 ); ?></small></td>
              <td><?php echo oecrm_h(
    $r["entity_type"] ?: "-"
); ?><small><?php echo oecrm_h(
    $r["entity_id"] ?: ""
); ?></small></td>
              <td><strong><?php echo oecrm_h(
    ($r["browser_name"] ?: "Unknown") . " / " . ($r["device_type"] ?: "Unknown")
); ?></strong><small><?php echo oecrm_h(
    ($r["platform_name"] ?: "Unknown") . " | " . $r["ip_address"]
); ?></small></td>
              <td><span class="activity-risk <?php echo $r[
    "risk_level"
]; ?>"><?php echo ucfirst($r["risk_level"]); ?></span><?php if (
    $r["risk_reasons"]
): ?><small><?php echo oecrm_h(
    $r["risk_reasons"]
); ?></small><?php endif; ?></td>
              <td><?php if (
    $hasChanges
): ?><button type="button" class="icon-action compare-btn" data-old="<?php echo oecrm_h(
    $r["old_values"] ?: "null"
); ?>" data-new="<?php echo oecrm_h(
    $r["new_values"] ?: "null"
); ?>" title="Compare"><i class="fa fa-exchange"></i></button><?php else: ?>-<?php endif; ?></td>
            </tr><?php
endwhile;
?></tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">Audit Retention</div>
    <div class="panel-body">
      <form method="post" action="auditAction.php" class="retention-form"><?php echo oecrm_csrf_field(); ?><input
          type="hidden" name="action" value="settings">
        <div class="form-group"><label>Retention Days</label><input type="number" min="90" max="3650"
            name="retention_days" class="form-control" value="<?php echo (int) ($settings[
    "retention_days"
] ??
    730); ?>"></div><label><input type="checkbox" name="preserve_security_events" <?php echo !isset(
    $settings["preserve_security_events"]
) || $settings["preserve_security_events"]
    ? "checked"
    : ""; ?>> Preserve login, logout, failed login and suspicious events</label><button class="btn btn-primary"><i
            class="fa fa-save"></i> Save Policy</button>
      </form><?php if (
    oecrm_can($conn, "activity_audit", "purge")
): ?><form method="post" action="auditAction.php" class="purge-form"
        onsubmit="return confirm('Purge audit records older than the retention policy?');">
        <?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="purge"><button
          class="btn btn-danger"><i class="fa fa-trash"></i> Purge Expired Logs</button></form><?php endif; ?>
    </div>
  </div>
</div>
<div class="modal fade" id="compareModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><button class="close" data-dismiss="modal">&times;</button>
        <h4>Before / After Comparison</h4>
      </div>
      <div class="modal-body audit-compare">
        <div>
          <h5>Before</h5>
          <pre id="oldValues"></pre>
        </div>
        <div>
          <h5>After</h5>
          <pre id="newValues"></pre>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
documetn.querySeletorAll('.compare-btn').foreach(function(btn){
  btn.addEventListener('click', function(){
    function prertyy(value){
      try{
        return JSON.stringify(JSON.parse(value), null, 2);
      } catch(e){
        return value;
      }
    }
  });
});


document.querySelectorAll('.compare-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    function pretty(value) {
      try {
        return JSON.stringify(JSON.parse(value), null, 2);
      } catch (e) {
        return value;
      }
    }
    document.getElementById('oldValues').textContent = pretty(btn.dataset.old);
    document.getElementById('newValues').textContent = pretty(btn.dataset.new);
    $('#compareModal').modal('show');
  });
});
</script><?php include "footer.php"; ?>