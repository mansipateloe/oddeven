<?php
$active_menu = "clients";
include "header.php";
require_once __DIR__ . "/../foundation.php";

oecrm_require_permission($conn, "clients", "view");

$companyId = oecrm_current_company_id($conn);
$status = trim($_GET["status"] ?? "");
$q = trim($_GET["q"] ?? "");
$where = "c.company_id=?";
$types = "i";
$params = [$companyId];

if (
    in_array(
        $status,
        ["prospect", "active", "inactive", "on_hold", "closed"],
        true
    )
) {
    $where .= " AND c.status=?";
    $types .= "s";
    $params[] = $status;
}

if ($q !== "") {
    $where .=
        " AND (c.display_name LIKE ? OR c.client_code LIKE ? OR c.email LIKE ?)";
    $like = "%" . $q . "%";
    $types .= "sss";
    array_push($params, $like, $like, $like);
}

$sql =
    'SELECT c.*,(SELECT COUNT(*) FROM client_contacts x WHERE x.client_id=c.id AND x.status=1) contact_count,(SELECT COUNT(*) FROM client_contracts x WHERE x.client_id=c.id AND x.status="active") contract_count,(SELECT COUNT(*) FROM projectstbl p WHERE p.client_id=c.id) project_count,(SELECT MAX(communication_at) FROM client_communications x WHERE x.client_id=c.id) last_contact FROM clients c WHERE ' .
    $where .
    ' ORDER BY FIELD(c.status,"active","prospect","on_hold","inactive","closed"),c.display_name';

$s = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($s, $types, ...$params);
mysqli_stmt_execute($s);
$clients = mysqli_stmt_get_result($s);

$stats = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        'SELECT COUNT(*) total,SUM(status="active") active,SUM(status="prospect") prospects,SUM(status="on_hold") on_hold FROM clients WHERE company_id=' .
            (int) $companyId
    )
);

$flash = $_SESSION["client_flash"] ?? "";
unset($_SESSION["client_flash"]);
?>
<style>
.client-page .client-filters {
    display: grid;
    grid-template-columns: minmax(0, 360px) minmax(160px, 190px) max-content;
    align-items: center;
    gap: 10px;
}
.client-page .client-filters .form-control {
    width: 100%;
    max-width: none;
    min-width: 0;
    height: 40px;
}
.client-page .client-filters select { color: #344054; background-color: #fff; }
.client-page .client-filters > .btn { min-height: 40px; }
.client-page .client-contact-details { display: grid; gap: 6px; }
.client-page .client-contact-detail { display: flex; align-items: baseline; gap: 8px; }
.client-page .client-contact-detail > .fa { flex: 0 0 14px; text-align: center; color: #858ba1; }
.client-page .client-contact-detail > span { min-width: 0; overflow-wrap: anywhere; }
@media (max-width: 700px) {
    .client-page .client-filters { grid-template-columns: minmax(0, 1fr); }
}
</style>
<div id="page-wrapper" class="compact-admin-page client-page">
  <?php if ($flash): ?>
    <div class="alert alert-success"><?php echo oecrm_h($flash); ?></div>
  <?php endif; ?>

  <div class="client-summary">
    <div>
      <i class="fa fa-address-book"></i>
      <span>
        <small>Total Clients</small>
        <strong><?php echo (int) $stats["total"]; ?></strong>
      </span>
    </div>
    <div>
      <i class="fa fa-check-circle"></i>
      <span>
        <small>Active</small>
        <strong><?php echo (int) $stats["active"]; ?></strong>
      </span>
    </div>
    <div>
      <i class="fa fa-user-plus"></i>
      <span>
        <small>Prospects</small>
        <strong><?php echo (int) $stats["prospects"]; ?></strong>
      </span>
    </div>
    <div>
      <i class="fa fa-pause-circle"></i>
      <span>
        <small>On Hold</small>
        <strong><?php echo (int) $stats["on_hold"]; ?></strong>
      </span>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading foundation-heading">
      <span>Client Directory</span>
      <a class="btn btn-primary btn-sm" href="clientProfile.php">
        <i class="fa fa-plus"></i> Add Client
      </a>
    </div>

    <div class="panel-body">
      <form method="get" class="client-filters">
        <input
          type="search"
          class="form-control"
          name="q"
          value="<?php echo oecrm_h($q); ?>"
          placeholder="Search client, code or email" aria-label="Search clients"
        >
        <select class="form-control" name="status" aria-label="Filter by client status" data-oecrm-native="1">
          <option value="">All statuses</option>
          <?php foreach (
              ["active", "prospect", "on_hold", "inactive", "closed"]
              as $v
          ): ?>
            <option value="<?php echo $v; ?>" <?php echo $status === $v
    ? "selected"
    : ""; ?>>
              <?php echo ucwords(str_replace("_", " ", $v)); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-default">
          <i class="fa fa-search"></i> Search
        </button>
      </form>

      <div class="table-responsive">
        <table class="table foundation-table">
          <thead>
            <tr>
              <th>Client</th>
              <th>Contact</th>
              <th>Status</th>
              <th>Projects</th>
              <th>Contracts</th>
              <th>Last Contact</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($clients) === 0): ?>
              <tr>
                <td colspan="7" class="empty-cell">No clients found.</td>
              </tr>
            <?php endif; ?>

            <?php while ($c = mysqli_fetch_assoc($clients)): ?>
              <tr>
                <td>
                  <strong><?php echo oecrm_h($c["display_name"]); ?></strong>
                  <small><?php echo oecrm_h(
                      $c["client_code"] .
                          " | " .
                          ($c["industry"] ?: ucfirst($c["client_type"]))
                  ); ?></small>
                </td>
                <td>
                  <div class="client-contact-details">
                    <?php if (trim($c['email'] ?? '') !== ''): ?>
                      <div class="client-contact-detail"><i class="fa fa-envelope" aria-hidden="true"></i><span><span class="sr-only">Email: </span><?php echo oecrm_h($c['email']); ?></span></div>
                    <?php endif; ?>
                    <?php if (trim($c['phone'] ?? '') !== ''): ?>
                      <div class="client-contact-detail"><i class="fa fa-phone" aria-hidden="true"></i><span><span class="sr-only">Phone: </span><?php echo oecrm_h($c['phone']); ?></span></div>
                    <?php endif; ?>
                    <?php if (trim($c['email'] ?? '') === '' && trim($c['phone'] ?? '') === ''): ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <span class="client-status <?php echo $c["status"]; ?>">
                    <?php echo ucwords(str_replace("_", " ", $c["status"])); ?>
                  </span>
                </td>
                <td><?php echo (int) $c["project_count"]; ?></td>
                <td><?php echo (int) $c["contract_count"]; ?></td>
                <td>
                  <?php echo $c["last_contact"]
                      ? date("d M Y", strtotime($c["last_contact"]))
                      : "Never"; ?>
                </td>
                <td>
                  <a
                    class="icon-action"
                    href="clientProfile.php?id=<?php echo (int) $c["id"]; ?>&amp;mode=view"
                    title="View Client"
                    aria-label="View Client"
                  >
                    <i class="fa fa-eye"></i>
                  </a>
                  <?php if (oecrm_can($conn, 'clients', 'edit')): ?>
                    <a class="icon-action" href="clientProfile.php?id=<?php echo (int) $c['id']; ?>&amp;mode=edit" title="Edit Client" aria-label="Edit Client"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                  <?php endif; ?>
                  <?php if (
                      !in_array($c["status"], ["inactive", "closed"], true) &&
                      oecrm_can($conn, "clients", "delete")
                  ): ?>
                    <form
                      method="post"
                      action="clientAction.php"
                      style="display:inline"
                      data-confirm="Archive this client? Active projects will place the client on hold."
                    >
                      <?php echo oecrm_csrf_field(); ?>
                      <input type="hidden" name="action" value="archive_client">
                      <input type="hidden" name="client_id" value="<?php echo (int) $c[
                          "id"
                      ]; ?>">
                      <button class="icon-action danger" title="Archive">
                        <i class="fa fa-archive"></i>
                      </button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
(function () {
    var form = document.querySelector('.client-filters');
    var searchInput = form.querySelector('input[name="q"]');
    var hasAppliedSearch = searchInput.defaultValue.trim() !== '';
    var reloading = false;

    function reloadWhenCleared() {
        if (!hasAppliedSearch || reloading || searchInput.value.trim() !== '') return;
        reloading = true;
        searchInput.value = '';
        if (form.requestSubmit) {
            form.requestSubmit();
        } else {
            form.submit();
        }
    }

    searchInput.addEventListener('input', reloadWhenCleared);
    searchInput.addEventListener('search', reloadWhenCleared);
})();
</script>
<?php include "footer.php"; ?>
