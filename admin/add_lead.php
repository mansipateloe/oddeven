<?php include 'header.php'; $flash = $_SESSION['lead_flash'] ?? ''; unset($_SESSION['lead_flash']); ?>
<?php
$flashType = $_SESSION['lead_flash_type'] ?? 'info';
$leadForm = $_SESSION['lead_form'] ?? [];
unset($_SESSION['lead_flash_type'], $_SESSION['lead_form']);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?><div class="alert alert-<?php echo oecrm_h($flashType); ?>"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <style>
        .lead-compact-form .is-invalid{border-color:#d9534f!important;box-shadow:0 0 0 0.2rem rgba(217,83,79,0.15)!important}
        .lead-compact-form .field-error{display:block;color:#d9534f;font-size:12px;margin-top:6px;line-height:1.3}
    </style>
    <div class="">
        <div class="panel panel-default lead-compact-panel">
            <div class="panel-heading panel-box lead-compact-heading">
                <div>
                    <h4>Lead Form Create</h4>
                    <span>Capture client, contact and follow-up details</span>
                </div>
                <a href="leads.php" class="btn btn-default btn-sm lead-compact-back"><i class="fa fa-list-alt"></i> View Leads</a>
            </div>
            <div class="panel-body manage_project lead-compact-body">
                <form id="leadForm" class="lead-compact-form" method="post" action="leads.php" enctype="multipart/form-data" novalidate>
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="lead-compact-grid">
                        <div class="form-group">
                            <label for="leadDate">Lead Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" placeholder="dd-mm-yyyy" id="leadDate" name="leadDate" value="<?php echo oecrm_h($leadForm['leadDate'] ?? $today_date); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="executiveName">Client Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Client Name" id="executiveName" name="executiveName" value="<?php echo oecrm_h($leadForm['executiveName'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="nick_name">Nick Name</label>
                            <input type="text" name="nick_name" id="nick_name" class="form-control" placeholder="Nick Name" maxlength="100" value="<?php echo oecrm_h($leadForm['nick_name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="company">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company" id="company" class="form-control" placeholder="Company Name" maxlength="100" value="<?php echo oecrm_h($leadForm['company'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="cperson">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" name="cperson" id="cperson" class="form-control" placeholder="Contact Person" maxlength="100" value="<?php echo oecrm_h($leadForm['cperson'] ?? ''); ?>" required>
                            <span class="help" id="msg1"></span>
                        </div>

                        <div class="form-group">
                            <label for="mobileno1">Mobile No1 <span class="text-danger">*</span></label>
                            <input type="text" name="mobileno1" id="mobileno1" maxlength="10" class="form-control only-digits" placeholder="Mobile No1" inputmode="numeric" pattern="[0-9]{10}" autocomplete="off" value="<?php echo oecrm_h($leadForm['mobileno1'] ?? ''); ?>">
                            <span class="help" id="msg2"></span>
                        </div>

                        <div class="form-group">
                            <label for="mobileno2">Mobile No2</label>
                            <input type="text" name="mobileno2" id="mobileno2" maxlength="10" class="form-control only-digits" placeholder="Mobile No2" inputmode="numeric" pattern="[0-9]{10}" autocomplete="off" value="<?php echo oecrm_h($leadForm['mobileno2'] ?? ''); ?>">
                            <span class="help" id="msg2_1"></span>
                        </div>

                        <div class="form-group">
                            <label for="emailid">Company Email ID <span class="text-danger">*</span></label>
                            <input type="email" name="emailid" id="emailid" class="form-control" placeholder="Company Email ID" value="<?php echo oecrm_h($leadForm['emailid'] ?? ''); ?>">
                            <span class="help" id="msg3"></span>
                        </div>

                        <div class="form-group">
                            <label for="emailid2">Personal Email ID</label>
                            <input type="email" name="emailid2" id="emailid2" class="form-control" placeholder="Personal Email ID" value="<?php echo oecrm_h($leadForm['emailid2'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" name="city" id="city" class="form-control" placeholder="City" maxlength="100" value="<?php echo oecrm_h($leadForm['city'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="lead_source">Lead Source <span class="text-danger">*</span></label>
                            <select class="form-control" id="lead_source" name="lead_source" required>
                                <option value="" selected>Select Lead Source</option>
                                <?php
                                    $sel_lead_sources = mysqli_query($conn, "SELECT * from lead_source_tbl");
                                    while ($fet_sources = mysqli_fetch_assoc($sel_lead_sources)) {
                                        $selected = (string) ($leadForm['lead_source'] ?? '') === (string) $fet_sources['id'] ? ' selected' : '';
                                        echo "<option value='".$fet_sources['id']."'".$selected.">".$fet_sources['name']."</option>";
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="leadType">Lead Priority</label>
                            <select class="form-control" id="leadType" name="leadType">
                                <option value="medium" <?php echo ($leadForm['leadType'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="low" <?php echo ($leadForm['leadType'] ?? 'medium') === 'low' ? 'selected' : ''; ?>>Low</option>
                                
                                <option value="high" <?php echo ($leadForm['leadType'] ?? 'medium') === 'high' ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>

                        <div class="form-group lead-span-4">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" placeholder="Address" class="form-control"><?php echo oecrm_h($leadForm['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="followupType">Followup Type</label>
                            <select name="followupType" id="followupType" class="form-control">
                                <?php foreach (['Call', 'Email', 'Message', 'meeting'] as $followupOption): ?>
                                    <option value="<?php echo oecrm_h($followupOption); ?>" <?php echo ($leadForm['followupType'] ?? 'Call') === $followupOption ? 'selected' : ''; ?>><?php echo oecrm_h($followupOption); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="lead-followup-pair">
                            <div class="form-group">
                                <label for="nextFollowupDate">Next Followup Date</label>
                                <input type="date" class="form-control" id="nextFollowupDate" name="nextFollowupDate" value="<?php echo oecrm_h($leadForm['nextFollowupDate'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="nextFollowupTime">Next Followup Time</label>
                                <input type="time" class="form-control" id="nextFollowupTime" name="nextFollowupTime" value="<?php echo oecrm_h($leadForm['nextFollowupTime'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="" selected>Select Status</option>
                                <?php foreach (['closed' => 'Closed', 'inprogress' => 'Inprogress', 'completed' => 'Completed', 'pending' => 'Pending'] as $statusValue => $statusLabel): ?>
                                    <option value="<?php echo oecrm_h($statusValue); ?>" <?php echo ($leadForm['status'] ?? '') === $statusValue ? 'selected' : ''; ?>><?php echo oecrm_h($statusLabel); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <input type="file" class="form-control-file" id="attachments" name="attachments">
                        </div>

                        <div class="form-group lead-span-4">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo oecrm_h($leadForm['remarks'] ?? ''); ?></textarea>
                        </div>

                        <div class="lead-compact-actions">
                            <button type="submit" name="leadSave" class="btn btn-primary"><i class="fa fa-save"></i> Submit Lead</button>
                            <a href="leads.php" class="btn btn-default"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var form = document.getElementById('leadForm');
    if (!form) return;

    function field(name) {
        return form.querySelector('[name="' + name + '"]');
    }

    function setError(input, message) {
        if (!input) return;
        input.classList.add('is-invalid');
        var group = input.closest('.form-group') || input.parentElement;
        if (!group) return;
        var error = group.querySelector('.field-error');
        if (!error) {
            error = document.createElement('span');
            error.className = 'field-error';
            group.appendChild(error);
        }
        error.textContent = message;
    }

    function clearError(input) {
        if (!input) return;
        input.classList.remove('is-invalid');
        var group = input.closest('.form-group') || input.parentElement;
        if (!group) return;
        var error = group.querySelector('.field-error');
        if (error) error.remove();
    }

    function isEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    form.addEventListener('submit', function (event) {
        var valid = true;
        var requiredFields = [
            ['leadDate', 'Lead date is required.'],
            ['executiveName', 'Client name is required.'],
            ['company', 'Company name is required.'],
            ['cperson', 'Contact person is required.'],
            ['lead_source', 'Lead source is required.'],
            ['status', 'Status is required.']
        ];
        var mobile = field('mobileno1');
        var mobile2 = field('mobileno2');
        var email = field('emailid');
        var email2 = field('emailid2');
        var followupDate = field('nextFollowupDate');

        form.querySelectorAll('.is-invalid').forEach(clearError);

        requiredFields.forEach(function (item) {
            var input = field(item[0]);
            if (!input || input.value.trim() === '') {
                valid = false;
                setError(input, item[1]);
            }
        });

        if (mobile.value.trim() === '' && email.value.trim() === '') {
            valid = false;
            setError(mobile, 'Enter Mobile No1 or Company Email ID.');
            setError(email, 'Enter Company Email ID or Mobile No1.');
        }
        if (mobile.value.trim() !== '' && !/^[0-9]{10}$/.test(mobile.value.trim())) {
            valid = false;
            setError(mobile, 'Mobile No1 must contain exactly 10 digits.');
        }
        if (mobile2.value.trim() !== '' && !/^[0-9]{10}$/.test(mobile2.value.trim())) {
            valid = false;
            setError(mobile2, 'Mobile No2 must contain exactly 10 digits.');
        }
        if (email.value.trim() !== '' && !isEmail(email.value.trim())) {
            valid = false;
            setError(email, 'Please enter a valid company email address.');
        }
        if (email2.value.trim() !== '' && !isEmail(email2.value.trim())) {
            valid = false;
            setError(email2, 'Please enter a valid personal email address.');
        }
        if (followupDate.value && followupDate.value < new Date().toISOString().slice(0, 10)) {
            valid = false;
            setError(followupDate, 'Next follow-up date cannot be in the past.');
        }

        if (!valid) {
            event.preventDefault();
            var firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        }
    });

    form.addEventListener('input', function (event) {
        if (event.target && event.target.matches('input, textarea')) clearError(event.target);
    });
    form.addEventListener('change', function (event) {
        if (event.target && event.target.matches('select, input')) clearError(event.target);
    });
}());
</script>
<?php include 'footer.php'; ?>

