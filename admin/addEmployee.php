<style type="text/css">
    /*.loader{
        position: absolute;
        right: 0;
        z-index: 10;
        top: 7px;
    }
    .loader img{
        width: 100%;
    }*/
</style>
<?php include 'header.php'; ?>
<?php require_once __DIR__ . '/../foundation.php'; $currentCompanyId=oecrm_current_company_id($conn); $employeeCompanies=mysqli_query($conn,"SELECT id,display_name FROM companies WHERE status=1 ORDER BY display_name"); $employeeDepartments=mysqli_query($conn,"SELECT id,company_id,name FROM departments WHERE status=1 ORDER BY name"); ?>
        <div id="page-wrapper" class="compact-admin-page employee-form-page">
            <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default add_employee">

                            <div class="panel-heading">

                                Add Employee

                            </div>
                            <div class="panel-body">

                                <form id="employeeForm" role="form" method="POST" action="employeeSave.php" novalidate>
<?php echo oecrm_csrf_field(); ?><input type="hidden" name="mode" value="create">
<section class="employee-form-section"><div class="employee-section-heading"><h3>Organization & Employment</h3></div><div class="employee-form-grid">
<div class="form-group"><label for="employee-company_id">Company <span class="text-danger">*</span></label><select id="employee-company_id" class="form-control company-selector" name="company_id" required><?php while($company=mysqli_fetch_assoc($employeeCompanies)): ?><option value="<?php echo (int)$company['id']; ?>" <?php echo $currentCompanyId===(int)$company['id']?'selected':''; ?>><?php echo oecrm_h($company['display_name']); ?></option><?php endwhile; ?></select></div>
<div class="form-group"><label for="employee-department_id">Department</label><select id="employee-department_id" class="form-control department-selector" name="department_id"><option value="0">No Department</option><?php while($department=mysqli_fetch_assoc($employeeDepartments)): ?><option data-company="<?php echo (int)$department['company_id']; ?>" value="<?php echo (int)$department['id']; ?>"><?php echo oecrm_h($department['name']); ?></option><?php endwhile; ?></select></div>
<div class="form-group"><label for="employee-employment_type">Employment Type</label><select id="employee-employment_type" class="form-control" name="employment_type"><?php foreach(['permanent','probation','contract','intern','consultant'] as $type): ?><option value="<?php echo $type; ?>"><?php echo ucwords($type); ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label for="employee-employment_status">Employment Status</label><select id="employee-employment_status" class="form-control" name="employment_status"><option value="active">Active</option><option value="notice_period">Notice Period</option><option value="inactive">Inactive</option></select></div>
<div class="form-group"><label for="employee-confirmation_date">Confirmation Date</label><input id="employee-confirmation_date" type="date" class="form-control" name="confirmation_date"></div>
<div class="form-group"><label for="employee-notice_period_days">Notice Period Days</label><input id="employee-notice_period_days" type="number" min="0" class="form-control" name="notice_period_days" value="0"></div>
</div></section>
<section class="employee-form-section"><div class="employee-section-heading"><h3>Basic Information</h3></div><div class="employee-form-grid">
<div class="form-group"><label for="employee-employeeCode">Employee Code <span class="text-danger">*</span></label><input id="employee-employeeCode" type="text" class="form-control" name="employeeCode" value="<?php echo oecrm_h($employeeCode); ?>" maxlength="6" placeholder="Employee code" required><span class="field-error"><?php echo oecrm_h($employeeCodeError); ?></span></div>
<div class="form-group"><label for="employee-designation">Designation <span class="text-danger">*</span></label><select id="employee-designation" class="form-control" name="designation" required>
                                                <option value="">--- Select Designation ---</option>
                                                <?php 
                                                    $qryDesignation = "select * from designation";
                                                    $resultDesignation = mysqli_query($conn,$qryDesignation);
                                                    //$rowDesignation = $resultDesignation->fetch_assoc();
                                                    if(mysqli_num_rows($resultDesignation) > 0){
                                                        while($rowDesignation = mysqli_fetch_assoc($resultDesignation)){
                                                            echo "<option value='".$rowDesignation['designation']."'>".$rowDesignation['designation']."</option>";
                                                        }
                                                    }
                                                ?>
                                            </select></div>
<div class="form-group"><label for="employee-name">Full Name <span class="text-danger">*</span></label><input id="employee-name" type="text" class="form-control" name="name" placeholder="Name" value="<?php echo oecrm_h($name); ?>" required><span class="field-error"><?php echo oecrm_h($nameError); ?></span></div>
<div class="form-group"><label for="employeeUname">Username <span class="text-danger">*</span></label><input type="text" class="form-control" name="employeeUname" id="employeeUname" onkeyup="checkname();" value="<?php echo oecrm_h($employeeUname); ?>" placeholder="Username" required><span id="user-availability-status"></span><span id="name_status" role="status"></span></div>
<div class="form-group"><label for="employeeUpass">Password <span class="text-danger">*</span></label><div class="employee-password-group"><input type="password" class="form-control" id="employeeUpass" minlength="6" name="employeeUpass" value="<?php echo oecrm_h($employeeUpass); ?>" placeholder="Password" required><button type="button" class="employee-password-toggle" id="toggleEmployeePassword" aria-label="Show password" aria-controls="employeeUpass"><i class="fa fa-eye" aria-hidden="true"></i></button></div></div>
<div class="form-group"><label for="employee-birthdate">Birth Date</label><input id="employee-birthdate" type="date" class="form-control" name="birthdate" value="<?php echo oecrm_h($birthdate); ?>" placeholder="Birth Date"></div>
<div class="form-group"><label for="employee-joiningDate">Joining Date <span class="text-danger">*</span></label><input id="employee-joiningDate" type="date" class="form-control" name="joiningDate" value="<?php echo oecrm_h($joiningDate); ?>" placeholder="Joining Date" required></div>
</div></section>
<section class="employee-form-section"><div class="employee-section-heading"><h3>Contact Details</h3></div><div class="employee-form-grid">
<div class="form-group"><label for="companyEmail">Company Email <span class="text-danger">*</span></label><input type="email" class="form-control" name="companyEmail" id="companyEmail" onkeyup="checkemail();" value="<?php echo oecrm_h($companyEmail); ?>" placeholder="Email address" required><span id="email_status" role="status"></span></div>
<div class="form-group"><label for="employee-personalEmail">Personal Email</label><input id="employee-personalEmail" type="email" class="form-control" name="personalEmail" value="<?php echo oecrm_h($personalEmail); ?>" placeholder="Email address"></div>
<div class="form-group"><label for="employee-mobile1">Mobile</label><input id="employee-mobile1" type="text" class="form-control" pattern="\d*" name="mobile1" minlength="10" maxlength="10" value="<?php echo oecrm_h($mobile1); ?>" placeholder="Contact No.1"><span class="field-error"><?php echo oecrm_h($mobile1Error); ?></span></div>
<div class="form-group"><label for="employee-mobile2">Phone</label><input id="employee-mobile2" type="text" class="form-control" pattern="\d*" name="mobile2" minlength="10" maxlength="12" value="<?php echo oecrm_h($mobile2); ?>" placeholder="Contact No.2"><span class="field-error"><?php echo oecrm_h($mobile2Error); ?></span></div>
<div class="form-group"><label for="employee-skypeUname">Skype</label><input id="employee-skypeUname" type="text" class="form-control" name="skypeUname" value="<?php echo oecrm_h($skypeUname); ?>" placeholder="Skype username"></div>
<div class="form-group span-2"><label for="employee-address">Address</label><textarea id="employee-address" type="text" rows="7" name="address" class="form-control" placeholder="Address"><?php echo oecrm_h($address); ?></textarea></div>
</div></section>
<section class="employee-form-section"><div class="employee-section-heading"><h3>Payroll & Bank</h3></div><div class="employee-form-grid">
<div class="form-group"><label for="employee-salary">Salary</label><input id="employee-salary" type="text" class="form-control" name="salary" pattern="[0-9]{1,10}" value="<?php echo oecrm_h($salary); ?>" placeholder="Salary"></div>
<div class="form-group"><label for="employee-bankName">Bank Name</label><input id="employee-bankName" type="text" class="form-control" name="bankName" value="<?php echo oecrm_h($bankName); ?>" placeholder="Bank Name"></div>
<div class="form-group"><label for="employee-bankIFSCno">Bank IFSC No.</label><input id="employee-bankIFSCno" type="text" class="form-control" name="bankIFSCno" value="<?php echo oecrm_h($bankIFSCno); ?>" placeholder="Bank IFSC No."></div>
<div class="form-group"><label for="employee-bankAcHolderName">Bank Account Holder</label><input id="employee-bankAcHolderName" type="text" class="form-control" name="bankAcHolderName" value="<?php echo oecrm_h($bankAcHolderName); ?>" placeholder="Bank A/c Holder Name"></div>
<div class="form-group"><label for="employee-bankAcNo">Bank Account No.</label><input id="employee-bankAcNo" type="text" class="form-control" pattern="\d*" name="bankAcNo" value="<?php echo oecrm_h($bankAcNo); ?>" placeholder="Bank A/c No."></div>
</div></section>
<div class="employee-edit-actions"><button class="btn btn-primary" type="submit"><i class="fa fa-plus" aria-hidden="true"></i> Add Employee</button><a class="btn btn-default" href="manageEmployee.php">Cancel</a></div></form>

                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../data/morris-data.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>
    <script type="text/javascript">
    </script>
    <script>
        (function () {
            var password = document.getElementById('employeeUpass');
            var toggle = document.getElementById('toggleEmployeePassword');
            if (!password || !toggle) return;

            toggle.addEventListener('click', function () {
                var visible = password.type === 'password';
                password.type = visible ? 'text' : 'password';
                toggle.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
                toggle.innerHTML = '<i class="fa ' + (visible ? 'fa-eye-slash' : 'fa-eye') + '" aria-hidden="true"></i>';
            });
        }());
    </script>
    <script type="text/javascript">
        function checkname(){
            $("#loaderIcon").show();
            /*name=("#employeeUname").val();*/
            var name=document.getElementById( "employeeUname" ).value;

            if(name){
                $.ajax({
                    type:'post',
                    url:'checkAvailibility.php',
                    data: {
                        employeeUname:name,
                        company_id:document.querySelector('.company-selector').value,
                    },
                    success: function (response){
                        $("#loaderIcon").hide();
                        $('#name_status').html(response);
                        if(response=="Username Available"){
                            $('#name_status').css("color", "green");
                            return true;
                        }else{
                            $('#name_status').css("color", "red");
                            return false;
                        }
                    }
                });
            }else{
                $('#name_status').html("");
                return false;
            }
        }

        function checkemail(){
            $("#loaderIcon").show();
            //email=('#emailUname').val();
            var email=document.getElementById( "companyEmail" ).value;
            if(email){
                $.ajax({
                    type:'post',
                    url:'checkAvailibility.php',
                    data: {
                        companyEmail:email,
                    },
                    success: function (response){
                        $("#loaderIcon").hide();
                        $('#email_status').html(response);
                        if(response=="Email Available"){
                            $('#email_status').css("color", "green");
                            return true;
                        }else{
                            $('#email_status').css("color", "red");
                            return false;
                        }
                    }
                });
            }else{
                $('#email_status').html("");
                return false;
            }
        }

        function checkall(){
            var namehtml=document.getElementById("name_status").innerHTML;
            var emailhtml=document.getElementById("email_status").innerHTML;

            if(namehtml=="Username Available" && emailhtml=="Email Available"){
                return true;
            }else{
                return false;
            }
        }

    </script>
    <script>
    (function(){
      var company=document.querySelector('.company-selector');
      var department=document.querySelector('.department-selector');
      function filterDepartments(){if(!company||!department)return;var companyId=company.value;Array.prototype.forEach.call(department.options,function(option,index){if(index===0)return;option.hidden=option.getAttribute('data-company')!==companyId;});if(department.selectedOptions[0]&&department.selectedOptions[0].hidden)department.value='0';}
      if(company){company.addEventListener('change',filterDepartments);filterDepartments();}
    }());
    </script>
    <script>
    (function () {
        var form = document.getElementById('employeeForm');
        if (!form) return;

        var requiredFields = [
            'company_id',
            'employeeCode',
            'designation',
            'name',
            'employeeUname',
            'employeeUpass',
            'companyEmail',
            'joiningDate'
        ];

        function getParentContainer(field) {
            var parent = field.closest('.form-group');
            if (parent) return parent;
            var inputGroup = field.closest('.input-group');
            if (inputGroup) return inputGroup;
            return field.parentElement || field;
        }

        function markField(field, message) {
            field.classList.add('is-invalid');
            var parent = getParentContainer(field);
            if (!parent) return;
            var error = parent.querySelector('.field-error');
            if (!error) {
                error = document.createElement('div');
                error.className = 'field-error';
                parent.appendChild(error);
            }
            error.textContent = message;
        }

        function clearField(field) {
            field.classList.remove('is-invalid');
            var parent = getParentContainer(field);
            if (!parent) return;
            var error = parent.querySelector('.field-error');
            if (error) error.remove();
        }

        requiredFields.forEach(function (name) {
            var field = form.querySelector('[name="' + name + '"]');
            if (!field) return;
            field.addEventListener('input', function () {
                if (field.value && field.value.trim() !== '') clearField(field);
            });
            field.addEventListener('change', function () {
                if (field.value && field.value.trim() !== '') clearField(field);
            });
        });

        form.addEventListener('submit', function (event) {
            var valid = true;
            requiredFields.forEach(function (name) {
                var field = form.querySelector('[name="' + name + '"]');
                if (!field) return;
                if (!field.value || field.value.trim() === '') {
                    valid = false;
                    markField(field, 'This field is required.');
                } else {
                    clearField(field);
                }
            });

            if (!valid) {
                event.preventDefault();
                var firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    })();
    </script>
    <style>
        .employee-password-group .form-control {
            padding-right: 44px;
        }
        .employee-password-toggle {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 4;
            width: 42px;
            height: 34px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #667085;
            cursor: pointer;
        }
        .employee-password-toggle:hover,
        .employee-password-toggle:focus {
            color: #2e6da4;
            outline: none;
        }
        .employee-password-toggle:focus-visible {
            box-shadow: 0 0 0 2px rgba(46, 109, 164, .25);
        }
        .employee-form-page .is-invalid {
            border-color: #d9534f !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 83, 79, 0.15) !important;
        }
        .employee-form-page .field-error {
            display: block;
            color: #d9534f;
            font-size: 12px;
            margin-top: 6px;
            line-height: 1.3;
        }
    </style>
<?php include 'footer.php'; ?>
