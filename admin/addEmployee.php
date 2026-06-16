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

                                <form role="form" method="POST" action="employeeSave.php">
<?php echo oecrm_csrf_field(); ?><input type="hidden" name="mode" value="create">
                                    <div class="employee-form-section">
                                        <h4><i class="fa fa-building-o"></i> Organization & Employment</h4>
                                        <div class="employee-foundation-grid">
                                            <div class="form-group"><label>Company *</label><select class="form-control company-selector" name="company_id" required><?php while($company=mysqli_fetch_assoc($employeeCompanies)): ?><option value="<?php echo (int)$company['id']; ?>" <?php echo $currentCompanyId===(int)$company['id']?'selected':''; ?>><?php echo oecrm_h($company['display_name']); ?></option><?php endwhile; ?></select></div>
                                            <div class="form-group"><label>Department</label><select class="form-control department-selector" name="department_id"><option value="0">No Department</option><?php while($department=mysqli_fetch_assoc($employeeDepartments)): ?><option data-company="<?php echo (int)$department['company_id']; ?>" value="<?php echo (int)$department['id']; ?>"><?php echo oecrm_h($department['name']); ?></option><?php endwhile; ?></select></div>
                                            <div class="form-group"><label>Employment Type</label><select class="form-control" name="employment_type"><?php foreach(['permanent','probation','contract','intern','consultant'] as $type): ?><option value="<?php echo $type; ?>"><?php echo ucwords($type); ?></option><?php endforeach; ?></select></div>
                                            <div class="form-group"><label>Employment Status</label><select class="form-control" name="employment_status"><option value="active">Active</option><option value="notice_period">Notice Period</option><option value="inactive">Inactive</option></select></div>
                                            <div class="form-group"><label>Confirmation Date</label><input type="date" class="form-control" name="confirmation_date"></div>
                                            <div class="form-group"><label>Notice Period Days</label><input type="number" min="0" class="form-control" name="notice_period_days" value="0"></div>
                                        </div>
                                    </div>
                                    <div class="employee-form-section"><h4><i class="fa fa-user"></i> Basic Information</h4>
                                    <div class="row">
                                        <div class="form-group col-lg-3">
                                            <span>*Employee Code :</span>
                                            <input type="text" class="form-control" name="employeeCode" value="<?php echo $employeeCode ?>" maxlength="6" placeholder="Employee code" required>
                                            <span style="color: red;"><?php echo $employeeCodeError; ?></span>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span>*Designation :</span>
                                            <select class="form-control" name="designation" required>
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
                                            </select>
                                        </div>
                                        <div class="col-lg-6">
                                            <span>*Name :</span>
                                            <div class=" input-group">
                                                <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p  class="fa fa-user"></p></span>
                                                <input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo $name ?>" required>
                                            </div>
                                            <span style="color: red;"><?php echo $nameError; ?></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <span>*Username :</span>
                                            <div class="form-group input-group" style="position: relative;">
                                                <span class="input-group-addon" style="padding: 8px 9px 0px 16px;"><p  class="fa fa-user"></p>@</span>
                                                <input type="text" class="form-control" name="employeeUname" id="employeeUname" onkeyup="checkname();" value="<?php echo $employeeUname ?>" placeholder="Username" required>
                                            </div>
                                            <span id="user-availability-status"></span>
                                            <span id="name_status"></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <span>*Password :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 16px 0px 20px;"><p class="fa fa-key"></p></span>
                                                <input type="password" class="form-control" minlength="6" name="employeeUpass" value="<?php echo $employeeUpass ?>" placeholder="Password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <span>*Company Email :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-envelope"></span></span>
                                                <input type="email" class="form-control" name="companyEmail" id="companyEmail" onkeyup="checkemail();" value="<?php echo $companyEmail ?>" placeholder="Email address" required>
                                            </div>
                                            <span style="color: red;"><?php echo $mobile1Error; ?></span>
                                            <span id="email_status"></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <span>Personal Email :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-envelope"></span></span>
                                                <input type="email" class="form-control" name="personalEmail" value="<?php echo $personalEmail ?>" placeholder="Email address">
                                            </div>
                                            <span style="color: red;"><?php echo $mobile1Error; ?></span>
                                            <span id="email_status"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <span>Mobile :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-phone"></p></span>
                                                <input type="text" class="form-control" pattern="\d*" name="mobile1" minlength="10" maxlength="10" value="<?php echo $mobile1 ?>" placeholder="Contact No.1">
                                            </div>
                                            <span style="color: red;"><?php echo $mobile1Error; ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <span>Phone :</span>
                                            <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-phone-alt"></span></span>
                                            <input type="text" class="form-control" pattern="\d*" name="mobile2" minlength="10" maxlength="12" value="<?php echo $mobile2 ?>" placeholder="Contact No.2">
                                            <span style="color: red;"><?php echo $mobile2Error; ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                        <span>Birth Date :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-calendar-o"></p></span>
                                                <input type="date" class="form-control" name="birthdate" value="<?php echo $birthdate ?>" placeholder="Birth Date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <span>Skype :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-skype"></p></span>
                                                <input type="text" class="form-control" name="skypeUname" value="<?php echo $skypeUname ?>" placeholder="Skype username">
                                            </div>
                                            <span>Bank Name :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>
                                                <input type="text" class="form-control" name="bankName" value="<?php echo $bankName ?>" placeholder="Bank Name">
                                            </div>
                                            <span>Bank IFSC No. :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>
                                                <input type="text" class="form-control" name="bankIFSCno" value="<?php echo $bankIFSCno ?>" placeholder="Bank IFSC No.">
                                            </div>
                                            <span>Bank A/c Holder Name :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>
                                                <input type="text" class="form-control" name="bankAcHolderName" value="<?php echo $bankAcHolderName ?>" placeholder="Bank A/c Holder Name">
                                            </div>
                                            <span>Bank A/c No. :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>
                                                <input type="text" class="form-control" pattern="\d*" name="bankAcNo" value="<?php echo $bankAcNo ?>" placeholder="Bank A/c No.">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <span>Joining Date :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-calendar-o"></p></span>
                                                <input type="date" class="form-control" name="joiningDate" value="<?php echo $joiningDate ?>" placeholder="Joining Date">
                                            </div>
                                            <span>Salary :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 8px 25px 0px 20px;"><p class="fa fa-rupee"></p></span>
                                                <input type="text" class="form-control" name="salary" pattern="[0-9]{1,10}" value="<?php echo $salary ?>" placeholder="Salary">
                                                <span style="color: red;"><?php echo $salaryError; ?></span>
                                            </div>
                                            <span>Address :</span>
                                            <div class="form-group input-group">
                                                <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-home"></span></span>
                                                <textarea type="text" rows="7" name="address" class="form-control" placeholder="Address"><?php echo $address ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="row employee-submit-row">
                                        <div class="col-lg-6" align="right">
                                            <input type="submit" class="btn btn-primary" id="addemployeeBtn" value="Add Employee">
                                        </div>
                                        <div class="col-lg-6" align="left">
                                            <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                        </div>
                                    </div>
                                </form>

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
<?php include 'footer.php'; ?>
