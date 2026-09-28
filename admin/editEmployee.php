<?php
include 'header.php';
require_once __DIR__ . '/../foundation.php';

$id = 0;
if (isset($_GET['edit']) && preg_match('/^\d+$/', (string)$_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $_SESSION['edit_employee_id'] = $id;
} else {
    $id = (int)($_SESSION['edit_employee_id'] ?? 0);
}
$rowView = null;

if ($id > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT e.*, p.employment_type, p.employment_status, p.confirmation_date, p.notice_period_days, p.exit_date, p.exit_reason
         FROM employeestbl e
         LEFT JOIN employee_profiles p ON p.employee_id = e.id
         WHERE e.id = ?
         LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $rowView = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

$employeeCompanies = [];
$companyResult = mysqli_query($conn, "SELECT id, display_name FROM companies WHERE status = 1 ORDER BY display_name");
while ($company = mysqli_fetch_assoc($companyResult)) {
    $employeeCompanies[] = $company;
}

$employeeDepartments = [];
$departmentResult = mysqli_query($conn, "SELECT id, company_id, name FROM departments WHERE status = 1 ORDER BY name");
while ($department = mysqli_fetch_assoc($departmentResult)) {
    $employeeDepartments[] = $department;
}

$designations = [];
$designationResult = mysqli_query($conn, "SELECT designation FROM designation ORDER BY designation");
while ($designation = mysqli_fetch_assoc($designationResult)) {
    $designations[] = $designation['designation'];
}

$value = function ($key, $default = '') use (&$rowView) {
    return oecrm_h((string)($rowView[$key] ?? $default));
};

$dateValue = function ($key) use (&$rowView) {
    $date = (string)($rowView[$key] ?? '');
    return oecrm_h($date === '0000-00-00' ? '' : $date);
};

$currentCompanyId = (int)($rowView['company_id'] ?? 0);
$currentDepartmentId = (int)($rowView['department_id'] ?? 0);
$currentEmploymentType = (string)($rowView['employment_type'] ?? 'permanent');
$currentEmploymentStatus = (string)($rowView['employment_status'] ?? (((int)($rowView['status'] ?? 0) === 1) ? 'inactive' : 'active'));
?>

<style>
    .employee-edit-page {
        background: #f6f7fb;
        min-height: calc(100vh - 52px);
    }
    .employee-edit-shell {
        padding: 22px 28px 36px;
    }
    .employee-edit-hero {
        align-items: center;
        background: linear-gradient(135deg, #ffffff 0%, #f4f7ff 100%);
        border: 1px solid #e7ebf5;
        border-radius: 18px;
        box-shadow: 0 12px 35px rgba(26, 35, 126, 0.08);
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        padding: 22px 24px;
    }
    .employee-edit-kicker {
        color: #8a94ad;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        margin-bottom: 6px;
        text-transform: uppercase;
    }
    .employee-edit-hero h1 {
        color: #202846;
        font-size: 28px;
        font-weight: 800;
        margin: 0 0 6px;
    }
    .employee-edit-hero p {
        color: #667085;
        margin: 0;
    }
    .employee-summary-card {
        align-items: center;
        background: #fff;
        border: 1px solid #e8ecf6;
        border-radius: 16px;
        display: flex;
        gap: 12px;
        min-width: 245px;
        padding: 12px 14px;
    }
    .employee-summary-avatar {
        align-items: center;
        background: #eef4ff;
        border-radius: 14px;
        color: #5b5ff6;
        display: flex;
        font-size: 22px;
        height: 48px;
        justify-content: center;
        width: 48px;
    }
    .employee-summary-card strong {
        color: #202846;
        display: block;
        font-size: 15px;
    }
    .employee-summary-card span {
        color: #7b849b;
        font-size: 12px;
    }
    .employee-edit-section {
        background: #fff;
        border: 1px solid #e7ebf5;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(31, 41, 55, 0.06);
        margin-bottom: 18px;
        overflow: hidden;
    }
    .employee-section-heading {
        align-items: center;
        border-bottom: 1px solid #edf0f7;
        display: flex;
        gap: 12px;
        padding: 18px 22px;
    }
    .employee-section-heading i {
        align-items: center;
        background: #f0efff;
        border-radius: 12px;
        color: #625df5;
        display: flex;
        height: 38px;
        justify-content: center;
        width: 38px;
    }
    .employee-section-heading h3 {
        color: #202846;
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }
    .employee-section-heading p {
        color: #8790a6;
        font-size: 12px;
        margin: 2px 0 0;
    }
    .employee-form-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        padding: 22px;
    }
    .employee-form-grid .form-group {
        margin-bottom: 0;
    }
    .employee-form-grid .span-2 {
        grid-column: span 2;
    }
    .employee-form-grid .span-3 {
        grid-column: 1 / -1;
    }
    .employee-edit-form label {
        color: #2f3954;
        display: block;
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 7px;
    }
    .employee-edit-form .required-star {
        color: #ff5a5f;
    }
    .employee-edit-form .form-control {
        background: #fff;
        border: 1px solid #d8deeb;
        border-radius: 11px;
        box-shadow: none;
        color: #1f2a44;
        font-size: 14px;
        height: 44px;
        transition: border-color .18s ease, box-shadow .18s ease;
    }
    .employee-edit-form .form-control:focus {
        border-color: #6b63ff;
        box-shadow: 0 0 0 4px rgba(107, 99, 255, .12);
    }
    .employee-edit-form textarea.form-control {
        height: auto;
        min-height: 126px;
        resize: vertical;
    }
    .employee-input-wrap {
        position: relative;
    }
    .employee-input-wrap .form-control {
        padding-left: 42px;
    }
    .employee-input-wrap .has-action {
        padding-right: 48px;
    }
    .employee-input-icon {
        color: #98a2b3;
        left: 15px;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 3;
    }
    .employee-field-action {
        align-items: center;
        background: transparent;
        border: 0;
        color: #667085;
        display: flex;
        height: 42px;
        justify-content: center;
        position: absolute;
        right: 2px;
        top: 1px;
        width: 44px;
        z-index: 4;
    }
    .employee-help-text {
        color: #8a94ad;
        display: block;
        font-size: 12px;
        margin-top: 6px;
    }
    .employee-edit-actions {
        align-items: center;
        background: rgba(255, 255, 255, .94);
        border: 1px solid #e7ebf5;
        border-radius: 18px;
        bottom: 18px;
        box-shadow: 0 14px 34px rgba(31, 41, 55, 0.12);
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 8px;
        padding: 14px;
        position: sticky;
        z-index: 20;
    }
    .employee-edit-actions .btn {
        border-radius: 11px;
        font-weight: 800;
        min-width: 145px;
        padding: 11px 18px;
    }
    .employee-edit-actions .btn-primary {
        background: #625df5;
        border-color: #625df5;
        box-shadow: 0 8px 18px rgba(98, 93, 245, .25);
    }
    .employee-edit-actions .btn-default {
        background: #f2f4f8;
        border-color: #e0e5ef;
        color: #475467;
    }
    .employee-not-found {
        background: #fff;
        border: 1px solid #fee4e2;
        border-radius: 18px;
        color: #b42318;
        padding: 22px;
    }
    @media (max-width: 1199px) {
        .employee-form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .employee-form-grid .span-3 {
            grid-column: 1 / -1;
        }
    }
    @media (max-width: 767px) {
        .employee-edit-shell {
            padding: 14px;
        }
        .employee-edit-hero {
            align-items: flex-start;
            flex-direction: column;
        }
        .employee-summary-card {
            min-width: 100%;
        }
        .employee-form-grid {
            grid-template-columns: 1fr;
            padding: 16px;
        }
        .employee-form-grid .span-2,
        .employee-form-grid .span-3 {
            grid-column: auto;
        }
        .employee-edit-actions {
            flex-direction: column-reverse;
            position: static;
        }
        .employee-edit-actions .btn {
            width: 100%;
        }
    }
</style>

<div id="page-wrapper" class="compact-admin-page employee-edit-page">
    <div class="employee-edit-shell">
        <div class="employee-edit-hero">
            <div>
                <div class="employee-edit-kicker">Admin / Employee Management</div>
                <h1>Edit Employee</h1>
                <p>Update organization, personal, contact, payroll and bank details from one clean screen.</p>
            </div>
            <div class="employee-summary-card">
                <div class="employee-summary-avatar"><i class="fa fa-user"></i></div>
                <div>
                    <strong><?php echo $rowView ? $value('name', 'Employee') : 'Employee'; ?></strong>
                    <span><?php echo $rowView ? $value('employeeCode', 'Code not set') : 'Record not found'; ?></span>
                </div>
            </div>
        </div>

        <?php if (!$rowView): ?>
            <div class="employee-not-found">
                <strong>Employee not found.</strong> Please go back to employee listing and select a valid employee.
            </div>
        <?php else: ?>
            <form class="employee-edit-form" role="form" method="POST" action="employeeSave.php" autocomplete="off">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="mode" value="update">
                <input type="hidden" name="employee_id" value="<?php echo (int)$id; ?>">

                <div class="employee-edit-section">
                    <div class="employee-section-heading">
                        <i class="fa fa-building-o"></i>
                        <div>
                            <h3>Organization & Employment</h3>
                            <p>Company mapping, department, employment status and exit details.</p>
                        </div>
                    </div>
                    <div class="employee-form-grid">
                        <div class="form-group">
                            <label>Company <span class="required-star">*</span></label>
                            <select class="form-control company-selector" name="company_id" required>
                                <option value="">Select Company</option>
                                <?php foreach ($employeeCompanies as $company): ?>
                                    <option value="<?php echo (int)$company['id']; ?>" <?php echo $currentCompanyId === (int)$company['id'] ? 'selected' : ''; ?>>
                                        <?php echo oecrm_h($company['display_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <select class="form-control department-selector" name="department_id">
                                <option value="0">No Department</option>
                                <?php foreach ($employeeDepartments as $department): ?>
                                    <option data-company="<?php echo (int)$department['company_id']; ?>" value="<?php echo (int)$department['id']; ?>" <?php echo $currentDepartmentId === (int)$department['id'] ? 'selected' : ''; ?>>
                                        <?php echo oecrm_h($department['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Employment Type</label>
                            <select class="form-control" name="employment_type">
                                <?php foreach (['permanent', 'probation', 'contract', 'intern', 'consultant'] as $type): ?>
                                    <option value="<?php echo $type; ?>" <?php echo $currentEmploymentType === $type ? 'selected' : ''; ?>>
                                        <?php echo ucwords($type); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Employment Status</label>
                            <select class="form-control" name="employment_status">
                                <?php foreach (['active', 'inactive', 'notice_period', 'resigned', 'terminated', 'retired'] as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo $currentEmploymentStatus === $status ? 'selected' : ''; ?>>
                                        <?php echo ucwords(str_replace('_', ' ', $status)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Joining Date <span class="required-star">*</span></label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-calendar-o employee-input-icon"></i>
                                <input type="date" class="form-control" name="joiningDate" value="<?php echo $dateValue('joiningDate'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Confirmation Date</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-check-circle-o employee-input-icon"></i>
                                <input type="date" class="form-control" name="confirmation_date" value="<?php echo $dateValue('confirmation_date'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Notice Period Days</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-clock-o employee-input-icon"></i>
                                <input type="number" min="0" class="form-control" name="notice_period_days" value="<?php echo (int)($rowView['notice_period_days'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Exit Date</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-calendar-times-o employee-input-icon"></i>
                                <input type="date" class="form-control" name="exit_date" value="<?php echo $dateValue('exit_date'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Exit Reason</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-commenting-o employee-input-icon"></i>
                                <input class="form-control" name="exit_reason" value="<?php echo $value('exit_reason'); ?>" placeholder="Reason, if applicable">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="employee-edit-section">
                    <div class="employee-section-heading">
                        <i class="fa fa-id-card-o"></i>
                        <div>
                            <h3>Basic Information</h3>
                            <p>Identity, login and designation details.</p>
                        </div>
                    </div>
                    <div class="employee-form-grid">
                        <div class="form-group">
                            <label>Employee Code <span class="required-star">*</span></label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-hashtag employee-input-icon"></i>
                                <input type="text" class="form-control" name="employeeCode" value="<?php echo $value('employeeCode'); ?>" maxlength="6" placeholder="Employee code" required>
                            </div>
                            <?php if (!empty($employeeCodeError)): ?><span class="text-danger"><?php echo oecrm_h($employeeCodeError); ?></span><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Designation <span class="required-star">*</span></label>
                            <select class="form-control" name="designation" required>
                                <option value="">Select Designation</option>
                                <?php foreach ($designations as $designation): ?>
                                    <option value="<?php echo oecrm_h($designation); ?>" <?php echo (string)($rowView['designation'] ?? '') === (string)$designation ? 'selected' : ''; ?>>
                                        <?php echo oecrm_h($designation); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Full Name <span class="required-star">*</span></label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-user employee-input-icon"></i>
                                <input type="text" class="form-control" name="name" value="<?php echo $value('name'); ?>" placeholder="Employee full name" required>
                            </div>
                            <?php if (!empty($nameError)): ?><span class="text-danger"><?php echo oecrm_h($nameError); ?></span><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Username <span class="required-star">*</span></label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-at employee-input-icon"></i>
                                <input type="text" class="form-control" name="employeeUname" value="<?php echo $value('employeeUname'); ?>" placeholder="Username" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-key employee-input-icon"></i>
                                <input id="employeePassword" type="password" class="form-control has-action" name="employeeUpass" value="" placeholder="Leave blank to keep current" minlength="6" maxlength="13" autocomplete="new-password">
                                <button type="button" class="employee-field-action" data-toggle-password="#employeePassword" aria-label="Show password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            <span class="employee-help-text">Only enter password if you want to change it.</span>
                        </div>
                        <div class="form-group">
                            <label>Birth Date</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-birthday-cake employee-input-icon"></i>
                                <input type="date" class="form-control" name="birthdate" value="<?php echo $dateValue('birthdate'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="employee-edit-section">
                    <div class="employee-section-heading">
                        <i class="fa fa-address-book-o"></i>
                        <div>
                            <h3>Contact Details</h3>
                            <p>Email, phone and communication information.</p>
                        </div>
                    </div>
                    <div class="employee-form-grid">
                        <div class="form-group">
                            <label>Company Email <span class="required-star">*</span></label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-envelope-o employee-input-icon"></i>
                                <input type="email" class="form-control" name="companyEmail" value="<?php echo $value('companyEmail'); ?>" placeholder="Company email address" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Personal Email</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-envelope employee-input-icon"></i>
                                <input type="email" class="form-control" name="personalEmail" value="<?php echo $value('personalEmail'); ?>" placeholder="Personal email address">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Mobile</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-phone employee-input-icon"></i>
                                <input type="text" class="form-control" name="mobile1" minlength="10" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" data-digits-only value="<?php echo $value('mobile1'); ?>" placeholder="10 digit mobile">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-phone-square employee-input-icon"></i>
                                <input type="text" class="form-control" name="mobile2" minlength="10" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" data-digits-only value="<?php echo $value('mobile2'); ?>" placeholder="10 digit phone">
                            </div>
                        </div>
                        <div class="form-group span-2">
                            <label>Skype</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-skype employee-input-icon"></i>
                                <input type="text" class="form-control" name="skypeUname" value="<?php echo $value('skypeUname'); ?>" placeholder="Skype username">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="employee-edit-section">
                    <div class="employee-section-heading">
                        <i class="fa fa-credit-card"></i>
                        <div>
                            <h3>Payroll & Bank</h3>
                            <p>Salary, bank account and address details.</p>
                        </div>
                    </div>
                    <div class="employee-form-grid">
                        <div class="form-group">
                            <label>Salary</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-rupee employee-input-icon"></i>
                                <input type="text" class="form-control" name="salary" pattern="[0-9]{1,10}" inputmode="numeric" data-digits-only value="<?php echo $value('salary'); ?>" placeholder="Salary">
                            </div>
                            <?php if (!empty($salaryError)): ?><span class="text-danger"><?php echo oecrm_h($salaryError); ?></span><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Bank Name</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-bank employee-input-icon"></i>
                                <input type="text" class="form-control" name="bankName" value="<?php echo $value('bankName'); ?>" placeholder="Bank name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Bank IFSC No.</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-code employee-input-icon"></i>
                                <input type="text" class="form-control" name="bankIFSCno" value="<?php echo $value('bankIFSCno'); ?>" placeholder="IFSC code">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Account Holder Name</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-user-o employee-input-icon"></i>
                                <input type="text" class="form-control" name="bankAcHolderName" value="<?php echo $value('bankAcHolderName'); ?>" placeholder="Account holder name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Account No.</label>
                            <div class="employee-input-wrap">
                                <i class="fa fa-university employee-input-icon"></i>
                                <input type="text" class="form-control" name="bankAcNo" pattern="[0-9]*" inputmode="numeric" data-digits-only value="<?php echo $value('bankAcNo'); ?>" placeholder="Bank account number">
                            </div>
                        </div>
                        <div class="form-group span-3">
                            <label>Address</label>
                            <textarea rows="5" name="address" class="form-control" placeholder="Employee address"><?php echo $value('address'); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="employee-edit-actions">
                    <a href="manageEmployee.php" class="btn btn-default">
                        <i class="fa fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update Employee
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../vendor/metisMenu/metisMenu.min.js"></script>
<script src="../dist/js/sb-admin-2.js"></script>
<script>
(function () {
    var company = document.querySelector('.company-selector');
    var department = document.querySelector('.department-selector');

    function filterDepartments() {
        if (!company || !department) return;
        var companyId = company.value;
        Array.prototype.forEach.call(department.options, function (option, index) {
            if (index === 0) return;
            option.hidden = option.getAttribute('data-company') !== companyId;
        });
        if (department.selectedOptions[0] && department.selectedOptions[0].hidden) {
            department.value = '0';
        }
    }

    Array.prototype.forEach.call(document.querySelectorAll('[data-digits-only]'), function (input) {
        input.addEventListener('input', function () {
            var maxLength = parseInt(input.getAttribute('maxlength') || '0', 10);
            var value = input.value.replace(/\D/g, '');
            input.value = maxLength ? value.slice(0, maxLength) : value;
        });
    });

    Array.prototype.forEach.call(document.querySelectorAll('[data-toggle-password]'), function (button) {
        button.addEventListener('click', function () {
            var input = document.querySelector(button.getAttribute('data-toggle-password'));
            var icon = button.querySelector('i');
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            if (icon) {
                icon.className = input.type === 'password' ? 'fa fa-eye' : 'fa fa-eye-slash';
            }
        });
    });

    if (company) {
        company.addEventListener('change', filterDepartments);
        filterDepartments();
    }
}());
</script>
<?php include 'footer.php'; ?>
