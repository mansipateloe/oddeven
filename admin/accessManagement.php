<?php
$active_menu = "access_management";
include "header.php";
require_once __DIR__ . "/../foundation.php";
oecrm_require_permission($conn, "access_management", "view");
$companyId = oecrm_current_company_id($conn);
$q = trim($_GET["q"] ?? "");
$where = "a.company_id=?";
$types = "i";
$params = [$companyId];
if ($q !== "") {
    $where .=
        " AND (a.service_name LIKE ? OR a.account_username LIKE ? OR a.owner_email LIKE ?)";
    $like = "%" . $q . "%";
    $types .= "sss";
    array_push($params, $like, $like, $like);
}
$sql =
    'SELECT a.*,(SELECT COUNT(*) FROM access_assignments x WHERE x.account_id=a.id AND x.status="active") active_users FROM access_accounts a WHERE ' .
    $where .
    " ORDER BY a.service_type,a.service_name";
$s = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($s, $types, ...$params);
mysqli_stmt_execute($s);
$rows = mysqli_stmt_get_result($s);
$stats = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        'SELECT COUNT(*) total,SUM(status="active") active,(SELECT COUNT(*) FROM access_assignments WHERE company_id=' .
            (int) $companyId .
            ' AND status="active") assignments,(SELECT COUNT(*) FROM access_assignments WHERE company_id=' .
            (int) $companyId .
            ' AND status="active" AND expires_on BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 30 DAY)) expiring FROM access_accounts WHERE company_id=' .
            (int) $companyId
    )
);
$flash = $_SESSION["access_flash"] ?? "";
unset($_SESSION["access_flash"]);
?><div id="page-wrapper" class="compact-admin-page access-page"><?php if (
    $flash
): ?><div class="alert alert-success"><?php echo oecrm_h(
    $flash
); ?></div><?php endif; ?><div class="resource-summary">
    <div><i class="fa fa-key"></i><span><small>Access Accounts</small><strong><?php echo (int) $stats[
    "total"
]; ?></strong></span></div>
    <div><i class="fa fa-check-circle"></i><span><small>Active Accounts</small><strong><?php echo (int) $stats[
    "active"
]; ?></strong></span></div>
    <div><i class="fa fa-users"></i><span><small>Assignments</small><strong><?php echo (int) $stats[
    "assignments"
]; ?></strong></span></div>
    <div><i class="fa fa-clock-o"></i><span><small>Expiring Soon</small><strong><?php echo (int) $stats[
    "expiring"
]; ?></strong></span></div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading foundation-heading"><span>Access Register</span><a class="btn btn-primary btn-sm"
        href="accessAccount.php"><i class="fa fa-plus"></i> Add Account</a></div>
    <div class="panel-body">
      <form class="client-filters"><input class="form-control" name="q" value="<?php echo oecrm_h(
    $q
); ?>" placeholder="Search service, username or owner"><button class="btn btn-default"><i class="fa fa-search"></i>
          Search</button></form>
      <div class="table-responsive">
        <table class="table foundation-table">
          <thead>
            <tr>
              <th>Service</th>
              <th>Type</th>
              <th>Username</th>
              <th>Owner</th>
              <th>Users</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody><?php
if (
    mysqli_num_rows($rows) === 0
): ?><tr>
              <td colspan="7" class="empty-cell">No access accounts found.</td>
            </tr><?php endif;
while ($a = mysqli_fetch_assoc($rows)): ?><tr>
              <td><strong><?php echo oecrm_h(
    $a["service_name"]
); ?></strong><small><?php echo oecrm_h(
    $a["login_url"]
); ?></small></td>
              <td><?php echo ucfirst(
    $a["service_type"]
); ?></td>
              <td><?php echo oecrm_h(
    $a["account_username"] ?: "-"
); ?></td>
              <td><?php echo oecrm_h(
    $a["owner_email"] ?: "-"
); ?></td>
              <td><?php echo (int) $a[
    "active_users"
]; ?></td>
              <td><span class="client-status <?php echo $a[
    "status"
]; ?>"><?php echo ucfirst(
    $a["status"]
); ?></span></td>
              <td><a class="icon-action" href="accessAccount.php?id=<?php echo $a[
    "id"
]; ?>"><i class="fa fa-arrow-right"></i></a></td>
            </tr><?php endwhile;
?></tbody>
        </table>
      </div>
    </div>
  </div>
</div><?php include "footer.php"; ?>