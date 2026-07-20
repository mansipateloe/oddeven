<?php
$active_menu = 'clients';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'clients', isset($_GET['id']) ? 'view' : 'create');

$companyId = oecrm_current_company_id($conn);
$id = (int) ($_GET['id'] ?? 0);
$client = null;

if ($id) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM clients WHERE id=? AND company_id=?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
    mysqli_stmt_execute($stmt);
    $client = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$client) {
        http_response_code(404);
        exit('Client not found.');
    }
}

$contacts = $id ? mysqli_query($conn, 'SELECT * FROM client_contacts WHERE client_id=' . (int) $id . ' ORDER BY is_primary DESC,id DESC') : null;
$contracts = $id ? mysqli_query($conn, 'SELECT * FROM client_contracts WHERE client_id=' . (int) $id . ' ORDER BY id DESC') : null;
$documents = $id ? mysqli_query($conn, 'SELECT * FROM client_documents WHERE client_id=' . (int) $id . ' ORDER BY id DESC') : null;
$communications = $id ? mysqli_query($conn, 'SELECT x.*,ct.name contact_name FROM client_communications x LEFT JOIN client_contacts ct ON ct.id=x.contact_id WHERE x.client_id=' . (int) $id . ' ORDER BY x.communication_at DESC,x.id DESC LIMIT 100') : null;
$projects = $id ? mysqli_query($conn, 'SELECT id,projectName,status,startdate,enddate,amount FROM projectstbl WHERE client_id=' . (int) $id . ' ORDER BY id DESC') : null;
$countries = mysqli_query($conn, 'SELECT id,name FROM countries WHERE flag=1 ORDER BY name');
$codePrefix = 'CL-' . str_pad($companyId, 2, '0', STR_PAD_LEFT) . '-' . date('Ym') . '-';
$last = mysqli_fetch_assoc(mysqli_query($conn, "SELECT client_code FROM clients WHERE company_id=" . (int) $companyId . " AND client_code LIKE '" . mysqli_real_escape_string($conn, $codePrefix) . "%' ORDER BY id DESC LIMIT 1"));
$nextSequence = 1;
if (!empty($last['client_code']) && preg_match('/(\d+)$/', $last['client_code'], $match)) $nextSequence = (int) $match[1] + 1;
$nextCode = $codePrefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
$flash = $_SESSION['client_flash'] ?? '';
$error = $_SESSION['client_error'] ?? '';
unset($_SESSION['client_flash'], $_SESSION['client_error']);
?>
<div id="page-wrapper" class="compact-admin-page client-profile-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
    <div class="client-profile-head">
        <div>
            <a href="clients.php"><i class="fa fa-arrow-left"></i> Clients</a>
            <h2><?php echo oecrm_h($client['display_name'] ?? 'New Client'); ?></h2>
            <?php if ($client): ?>
                <span><?php echo oecrm_h($client['client_code']); ?></span>
                <span class="client-status <?php echo $client['status']; ?>" id="clientStatusBadge"><?php echo ucwords(str_replace('_', ' ', $client['status'])); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="client-profile-grid">
        <main>
                <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-id-card-o"></i> Client Profile</div>
                <div class="panel-body">
                    <style>
                        .client-form select.form-control{height:38px;background:#fff}
                        .client-form .required-field:after{content:" *";color:#d9534f;font-weight:700}
                        .client-profile-page .panel-heading i{margin-right:8px;color:#2f74e8}
                    </style>
                    <form method="post" action="clientAction.php" class="client-form" id="clientProfileForm">
                        <?php echo oecrm_csrf_field(); ?>
                        <input type="hidden" name="action" value="save_client">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="client-form-grid three">
                            <div class="form-group">
                                <label class="required-field">Client Code</label>
                                <input class="form-control" name="client_code" readonly required value="<?php echo oecrm_h($client['client_code'] ?? $nextCode); ?>">
                                <small>Auto generated</small>
                            </div>
                            <div class="form-group"><label class="required-field">Display Name</label><input class="form-control" name="display_name" required value="<?php echo oecrm_h($client['display_name'] ?? ''); ?>"></div>
                            <div class="form-group"><label class="required-field">Legal Name</label><input class="form-control" name="legal_name" required value="<?php echo oecrm_h($client['legal_name'] ?? ''); ?>"></div>
                            <div class="form-group"><label>Type</label><select class="form-control" name="client_type"><?php foreach (['company', 'individual'] as $value): ?><option <?php echo ($client['client_type'] ?? 'company') === $value ? 'selected' : ''; ?>><?php echo ucfirst($value); ?></option><?php endforeach; ?></select></div>
                            <div class="form-group"><label>Status</label><select class="form-control" name="status"><?php foreach (['prospect', 'active', 'on_hold', 'inactive', 'closed'] as $value): ?><option value="<?php echo $value; ?>" <?php echo ($client['status'] ?? 'active') === $value ? 'selected' : ''; ?>><?php echo ucwords(str_replace('_', ' ', $value)); ?></option><?php endforeach; ?></select></div>
                            <div class="form-group"><label>Industry</label><input class="form-control" name="industry" value="<?php echo oecrm_h($client['industry'] ?? ''); ?>"></div>
                            <div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" value="<?php echo oecrm_h($client['email'] ?? ''); ?>"></div>
                            <div class="form-group"><label>Phone</label><input class="form-control only-digits" name="phone" type="text" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" autocomplete="off" value="<?php echo oecrm_h($client['phone'] ?? ''); ?>"></div>
                            <div class="form-group"><label>Website</label><input class="form-control" name="website" value="<?php echo oecrm_h($client['website'] ?? ''); ?>"></div>
                            <div class="form-group"><label class="required-field">Currency</label><input class="form-control" name="currency_code" maxlength="3" value="<?php echo oecrm_h($client['currency_code'] ?? 'INR'); ?>"></div>
                            <div class="form-group"><label>Payment Terms (days)</label><input type="number" min="0" class="form-control" name="payment_terms_days" value="<?php echo (int) ($client['payment_terms_days'] ?? 0); ?>"></div>
                            <div class="form-group"><label>GST / Tax ID</label><input class="form-control" name="tax_id" maxlength="15" value="<?php echo oecrm_h($client['tax_id'] ?? ''); ?>" placeholder="15-character GSTIN / Tax ID"></div>
                        </div>
                        <div class="alert alert-info" style="margin-top:-4px;">Provide at least one of Email or Phone. Each one must be unique inside the company.</div>
                        <div class="form-group"><label>Billing Address</label><textarea class="form-control" name="billing_address"><?php echo oecrm_h($client['billing_address'] ?? ''); ?></textarea></div>
                        <div class="client-form-grid three">
                            <div class="form-group">
                                <label>Country</label>
                                <select class="form-control location-native-select" id="countrySelect" name="country" data-oecrm-native="1">
                                    <option value="">Select Country</option>
                                    <?php while ($country = mysqli_fetch_assoc($countries)): ?>
                                        <option value="<?php echo oecrm_h($country['name']); ?>" data-id="<?php echo (int) $country['id']; ?>" <?php echo ($client['country'] ?? '') === $country['name'] ? 'selected' : ''; ?>><?php echo oecrm_h($country['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>State</label><select class="form-control location-native-select" id="stateSelect" name="state" data-oecrm-native="1" data-selected="<?php echo oecrm_h($client['state'] ?? ''); ?>" disabled><option value="">Select State</option></select></div>
                            <div class="form-group"><label>City</label><select class="form-control location-native-select" id="citySelect" name="city" data-oecrm-native="1" data-selected="<?php echo oecrm_h($client['city'] ?? ''); ?>" disabled><option value="">Select City</option></select></div>
                        </div>
                        <div class="form-group"><label>Notes</label><textarea class="form-control" name="notes"><?php echo oecrm_h($client['notes'] ?? ''); ?></textarea></div>
                        <button class="btn btn-primary"><i class="fa fa-save"></i> Save Client</button>
                        <a class="btn btn-default" href="clients.php">Cancel</a>
                    </form>
                </div>
            </div>
            <?php if ($id): ?>
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-comments-o"></i> Communication History</div>
                    <div class="panel-body">
                        <form method="post" action="clientAction.php" class="communication-form">
                            <?php echo oecrm_csrf_field(); ?>
                            <input type="hidden" name="action" value="communication">
                            <input type="hidden" name="client_id" value="<?php echo $id; ?>">
                            <select class="form-control" name="communication_type"><?php foreach (['call', 'email', 'meeting', 'message', 'note'] as $value): ?><option><?php echo $value; ?></option><?php endforeach; ?></select>
                            <input class="form-control" name="subject" placeholder="Subject" required>
                            <input type="datetime-local" class="form-control" name="communication_at" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                            <textarea class="form-control" name="details" placeholder="Discussion notes"></textarea>
                            <button class="btn btn-primary"><i class="fa fa-plus"></i> Add Activity</button>
                        </form>
                        <div class="client-timeline">
                            <?php while ($item = mysqli_fetch_assoc($communications)): ?>
                                <div><i class="fa fa-<?php echo $item['communication_type'] === 'email' ? 'envelope' : ($item['communication_type'] === 'call' ? 'phone' : 'comment-o'); ?>"></i><section><strong><?php echo oecrm_h($item['subject']); ?></strong><small><?php echo date('d M Y h:i A', strtotime($item['communication_at'])); ?> | <?php echo ucfirst($item['communication_type']); ?></small><p><?php echo nl2br(oecrm_h($item['details'])); ?></p></section></div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
        <?php if ($id): ?>
            <aside>
                <div class="panel panel-default"><div class="panel-heading"><i class="fa fa-address-book-o"></i> Contacts</div><div class="panel-body"><form method="post" action="clientAction.php" class="mini-form"><?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="contact"><input type="hidden" name="client_id" value="<?php echo $id; ?>"><input class="form-control" name="name" placeholder="Contact name" required><input class="form-control" name="designation" placeholder="Designation"><input type="email" class="form-control" name="email" placeholder="Email"><input class="form-control only-digits" name="phone" placeholder="Phone" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" autocomplete="off"><label><input type="checkbox" name="is_primary"> Primary contact</label><button class="btn btn-default btn-block"><i class="fa fa-plus"></i> Add Contact</button></form><div class="client-mini-list"><?php while ($item = mysqli_fetch_assoc($contacts)): ?><div><strong><?php echo oecrm_h($item['name']); ?></strong><small><?php echo oecrm_h($item['designation']); ?></small><span><?php echo oecrm_h($item['email'] . ' ' . $item['phone']); ?></span></div><?php endwhile; ?></div></div></div>
                <div class="panel panel-default"><div class="panel-heading"><i class="fa fa-file-contract"></i> Contracts & NDA</div><div class="panel-body"><form method="post" action="clientAction.php" class="mini-form"><?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="contract"><input type="hidden" name="client_id" value="<?php echo $id; ?>"><select class="form-control" name="contract_type"><?php foreach (['service', 'retainer', 'dedicated_resource', 'nda', 'other'] as $value): ?><option value="<?php echo $value; ?>"><?php echo ucwords(str_replace('_', ' ', $value)); ?></option><?php endforeach; ?></select><input class="form-control" name="title" placeholder="Contract title" required><div class="client-form-grid"><input type="date" class="form-control" name="start_date"><input type="date" class="form-control" name="end_date"></div><input type="number" step="0.01" class="form-control" name="value_amount" placeholder="Contract value"><button class="btn btn-default btn-block"><i class="fa fa-plus"></i> Add Contract</button></form><div class="client-mini-list"><?php while ($item = mysqli_fetch_assoc($contracts)): ?><div><strong><?php echo oecrm_h($item['title']); ?></strong><small><?php echo oecrm_h(ucwords(str_replace('_', ' ', $item['contract_type'])) . ' | ' . ucfirst($item['status'])); ?></small><span><?php echo oecrm_h($item['start_date'] . ' to ' . $item['end_date']); ?></span></div><?php endwhile; ?></div></div></div>
                <div class="panel panel-default"><div class="panel-heading"><i class="fa fa-files-o"></i> Documents</div><div class="panel-body"><form method="post" action="clientAction.php" enctype="multipart/form-data" class="mini-form"><?php echo oecrm_csrf_field(); ?><input type="hidden" name="action" value="document"><input type="hidden" name="client_id" value="<?php echo $id; ?>"><select class="form-control" name="document_type"><?php foreach (['contract', 'nda', 'tax', 'proposal', 'other'] as $value): ?><option><?php echo $value; ?></option><?php endforeach; ?></select><input class="form-control" name="title" placeholder="Document title" required><input type="file" class="form-control" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required><button class="btn btn-default btn-block"><i class="fa fa-upload"></i> Upload Document</button></form><div class="client-mini-list"><?php while ($item = mysqli_fetch_assoc($documents)): ?><div><?php $isImage = preg_match('/\.(jpe?g|png|gif|webp)$/i', $item['original_name'] ?? $item['stored_name']); ?><strong><?php echo oecrm_h($item['title']); ?></strong><small><?php echo ucfirst($item['document_type']); ?></small><?php if ($isImage): ?><a href="clientDocument.php?id=<?php echo (int) $item['id']; ?>" target="_blank" title="View image"><i class="fa fa-image"></i></a><?php endif; ?><a href="clientDocument.php?id=<?php echo (int) $item['id']; ?>" title="Download"><i class="fa fa-download"></i></a></div><?php endwhile; ?></div></div></div>
                <div class="panel panel-default"><div class="panel-heading"><i class="fa fa-suitcase"></i> Linked Projects</div><div class="panel-body client-mini-list"><?php while ($item = mysqli_fetch_assoc($projects)): ?><div><strong><?php echo oecrm_h($item['projectName']); ?></strong><small><?php echo oecrm_h(ucfirst($item['status'])); ?></small><a href="projectBoard.php?id=<?php echo (int) $item['id']; ?>"><i class="fa fa-arrow-right"></i></a></div><?php endwhile; ?></div></div>
            </aside>
        <?php endif; ?>
    </div>
</div>
<script>
function getSelectedOptionId(select) {
    var value = select.value;
    for (var index = 0; index < select.options.length; index++) {
        if (select.options[index].value === value) {
            return select.options[index].getAttribute('data-id') || '';
        }
    }
    var option = select.options[select.selectedIndex];
    return option ? option.getAttribute('data-id') || '' : '';
}
function setLoading(select, label) {
    select.disabled = true;
    select.innerHTML = '<option value="">Loading ' + label + '...</option>';
}
function fillSelect(select, label, rows, selected) {
    select.innerHTML = '<option value="">Select ' + label + '</option>';
    rows.forEach(function (row) {
        var option = document.createElement('option');
        option.value = row.name;
        option.textContent = row.name;
        option.setAttribute('data-id', row.id);
        if (selected && selected === row.name) option.selected = true;
        select.appendChild(option);
    });
    select.disabled = rows.length === 0;
}
var locationRequest = {states:null,cities:null};
function loadLocation(type, parentId, target, selected) {
    var label = type === 'states' ? 'State' : 'City';
    fillSelect(target, label, [], '');
    if (!parentId) {
        target.disabled = true;
        return Promise.resolve([]);
    }
    if (locationRequest[type]) locationRequest[type].abort();
    locationRequest[type] = new AbortController();
    setLoading(target, label);
    return fetch('locationAjax.php?type=' + encodeURIComponent(type) + '&parent_id=' + encodeURIComponent(parentId), {signal:locationRequest[type].signal})
        .then(function (response) {
            if (!response.ok) throw new Error('Unable to load ' + label.toLowerCase() + ' list.');
            return response.json();
        })
        .then(function (rows) {
            fillSelect(target, label, rows, selected);
            if (type === 'states') {
                loadLocation('cities', getSelectedOptionId(target), document.getElementById('citySelect'), document.getElementById('citySelect').getAttribute('data-selected') || '');
            }
            return rows;
        })
        .catch(function (error) {
            if (error.name === 'AbortError') return [];
            fillSelect(target, label, [], '');
            target.innerHTML = '<option value="">Unable to load ' + label + '</option>';
            target.disabled = true;
            return [];
        });
}
document.addEventListener('DOMContentLoaded', function () {
    var country = document.getElementById('countrySelect');
    var state = document.getElementById('stateSelect');
    var city = document.getElementById('citySelect');
    function countryChanged() {
        state.setAttribute('data-selected', '');
        city.setAttribute('data-selected', '');
        fillSelect(city, 'City', [], '');
        loadLocation('states', getSelectedOptionId(country), state, '');
    }
    function stateChanged() {
        city.setAttribute('data-selected', '');
        loadLocation('cities', getSelectedOptionId(state), city, '');
    }
    country.addEventListener('change', countryChanged);
    state.addEventListener('change', stateChanged);
    if (getSelectedOptionId(country)) {
        loadLocation('states', getSelectedOptionId(country), state, state.getAttribute('data-selected') || '');
    }
});
</script>
<script>
(function () {
    document.addEventListener('input', function (event) {
        if (event.target && event.target.classList && event.target.classList.contains('only-digits')) {
            event.target.value = event.target.value.replace(/\D+/g, '').slice(0, 15);
        }
    });
})();
</script>
<script>
(function(){
    var form = document.getElementById('clientProfileForm');
    if (!form) return;
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var body = new FormData(form);
        body.append('response', 'json');
        fetch('clientAction.php', {
            method: 'POST',
            body: body,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        }).then(function (response) {
            return response.json().then(function (data) {
                return {ok: response.ok, data: data};
            });
        }).then(function (payload) {
            var flashHost = document.querySelector('.client-profile-page');
            var oldAlert = flashHost ? flashHost.querySelector('.alert') : null;
            if (oldAlert) oldAlert.remove();
            var alertBox = document.createElement('div');
            alertBox.className = 'alert ' + (payload.ok ? 'alert-success' : 'alert-danger');
            alertBox.textContent = payload.data.message || (payload.ok ? 'Saved successfully.' : 'Save failed.');
            if (flashHost) flashHost.insertBefore(alertBox, flashHost.firstChild);
            if (payload.ok && payload.data.id) {
                form.querySelector('input[name="id"]').value = payload.data.id;
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, '', 'clientProfile.php?id=' + payload.data.id);
                }
                var statusField = form.querySelector('select[name="status"]');
                var statusBadge = document.getElementById('clientStatusBadge');
                if (statusBadge && statusField) {
                    var statusValue = statusField.value || 'active';
                    statusBadge.className = 'client-status ' + statusValue;
                    statusBadge.textContent = statusField.options[statusField.selectedIndex] ? statusField.options[statusField.selectedIndex].text : statusValue;
                }
            }
        }).catch(function () {
            form.submit();
        });
    });
})();
</script>
<?php include 'footer.php'; ?>
