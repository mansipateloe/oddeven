<?php include 'header.php'; ?>
<?php require_once __DIR__ . '/../foundation.php'; ?>



    <?php 

        $id = oecrm_int_param($_GET, 'edit');

        $qryView = "SELECT e.*, p.employment_type, p.employment_status, p.confirmation_date, p.notice_period_days, p.exit_date, p.exit_reason FROM employeesTbl e LEFT JOIN employee_profiles p ON p.employee_id=e.id WHERE e.id=".$id;

        $resultView = mysqli_query($conn,$qryView);

        $rowView = $resultView->fetch_assoc();
$employeeCompanies=mysqli_query($conn,"SELECT id,display_name FROM companies WHERE status=1 ORDER BY display_name"); $employeeDepartments=mysqli_query($conn,"SELECT id,company_id,name FROM departments WHERE status=1 ORDER BY name");

    ?>    

        <div id="page-wrapper" class="compact-admin-page employee-form-page">

            <div class="row">

                <div class="col-lg-12">
                    <div class="panel panel-default">

                            <div class="panel-heading">

                                Edit Employee

                            </div>
                            <div class="panel-body">

                            	<div class="dataTablesbox2 edit_employee_detail">

                                    <form role="form" method="POST" action="employeeSave.php">
<?php echo oecrm_csrf_field(); ?><input type="hidden" name="mode" value="update"><input type="hidden" name="employee_id" value="<?php echo (int)$id; ?>">

                                        <div class="employee-form-section">
                                            <h4><i class="fa fa-building-o"></i> Organization & Employment</h4>
                                            <div class="employee-foundation-grid">
                                                <div class="form-group"><label>Company *</label><select class="form-control company-selector" name="company_id" required><?php while($company=mysqli_fetch_assoc($employeeCompanies)): ?><option value="<?php echo (int)$company['id']; ?>" <?php echo (int)$rowView['company_id']===(int)$company['id']?'selected':''; ?>><?php echo oecrm_h($company['display_name']); ?></option><?php endwhile; ?></select></div>
                                                <div class="form-group"><label>Department</label><select class="form-control department-selector" name="department_id"><option value="0">No Department</option><?php while($department=mysqli_fetch_assoc($employeeDepartments)): ?><option data-company="<?php echo (int)$department['company_id']; ?>" value="<?php echo (int)$department['id']; ?>" <?php echo (int)$rowView['department_id']===(int)$department['id']?'selected':''; ?>><?php echo oecrm_h($department['name']); ?></option><?php endwhile; ?></select></div>
                                                <div class="form-group"><label>Employment Type</label><select class="form-control" name="employment_type"><?php foreach(['permanent','probation','contract','intern','consultant'] as $type): ?><option value="<?php echo $type; ?>" <?php echo ($rowView['employment_type']?:'permanent')===$type?'selected':''; ?>><?php echo ucwords($type); ?></option><?php endforeach; ?></select></div>
                                                <div class="form-group"><label>Employment Status</label><select class="form-control" name="employment_status"><?php foreach(['active','inactive','notice_period','resigned','terminated','retired'] as $status): ?><option value="<?php echo $status; ?>" <?php echo ($rowView['employment_status']?:'active')===$status?'selected':''; ?>><?php echo ucwords(str_replace('_',' ',$status)); ?></option><?php endforeach; ?></select></div>
                                                <div class="form-group"><label>Confirmation Date</label><input type="date" class="form-control" name="confirmation_date" value="<?php echo oecrm_h($rowView['confirmation_date']); ?>"></div>
                                                <div class="form-group"><label>Notice Period Days</label><input type="number" min="0" class="form-control" name="notice_period_days" value="<?php echo (int)$rowView['notice_period_days']; ?>"></div>
                                                <div class="form-group"><label>Exit Date</label><input type="date" class="form-control" name="exit_date" value="<?php echo oecrm_h($rowView['exit_date']); ?>"></div>
                                                <div class="form-group"><label>Exit Reason</label><input class="form-control" name="exit_reason" value="<?php echo oecrm_h($rowView['exit_reason']); ?>"></div>
                                            </div>
                                        </div>
                                        <div class="employee-form-section"><h4><i class="fa fa-user"></i> Basic Information</h4>
                                        <div class="row">

                                            <div class="form-group col-lg-3">

                                                <span>*Employee Code :</span>

                                                <input type="text" class="form-control" name="employeeCode" value="<?php echo $rowView['employeeCode'] ?>" maxlength="6" placeholder="Employee code" required>

                                                <span style="color: red;"><?php echo $employeeCodeError; ?></span>

                                            </div>

                                            <div class="form-group col-lg-3">

                                                <span>*Designation :</span>

                                                <select class="form-control" name="designation" required>

                                                    <option value="">--- Select Designation ---</option>

                                                    <?php 

                                                        $qryDesignation = "select * from designation";

                                                        $resultDesignation = mysqli_query($conn,$qryDesignation);

                                                        if(mysqli_num_rows($resultDesignation) > 0){

                                                            while($rowDesignation = mysqli_fetch_assoc($resultDesignation)){                                

                                                                if($rowView['designation'] == $rowDesignation['designation']){

                                                                    echo "<option value='".$rowDesignation['designation']."'selected>".$rowDesignation['designation']."</option>";

                                                                }else{

                                                                    echo "<option value='".$rowDesignation['designation']."'>".$rowDesignation['designation']."</option>";

                                                                }

                                                            }

                                                        }

                                                    ?>

                                                </select>

                                            </div>

                                            <div class="col-lg-6">

                                                <span>*Name :</span>

                                                <div class=" input-group">

                                                    <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p  class="fa fa-user"></p></span>

                                                            <input type="text" class="form-control" name="name" value="<?php echo $rowView['name'] ?>" required>

                                                </div>

                                                <span style="color: red;"><?php echo $nameError; ?></span>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-6">

                                                <span>*Username :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 9px 0px 16px;"><p  class="fa fa-user"></p>@</span>

                                                    <input type="text" class="form-control" name="employeeUname" value="<?php echo $rowView['employeeUname'] ?>" placeholder="Username" required>

                                                </div>

                                            </div>

                                            <div class="col-lg-6">

                                                <span>*Password :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 16px 0px 20px;"><p class="fa fa-key"></p></span>

                                                    <input type="password" class="form-control" name="employeeUpass" value="" placeholder="Leave blank to keep current password">

                                                    

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-6">

                                                <span>*Company Email :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-envelope"></span></span>

                                                    <input type="text" class="form-control" name="companyEmail" placeholder="Company Email Address" value="<?php echo $rowView['companyEmail'] ?>">

                                                </div>

                                            </div>

                                            <div class="col-lg-6">

                                                <span>Personal Email :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-envelope"></span></span>

                                                    <input type="text" class="form-control" name="personalEmail" value="<?php echo $rowView['personalEmail'] ?>" placeholder="Personal Email Address" >

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-6">

                                                <span>Mobile :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-phone"></p></span>

                                                    <input type="text" class="form-control" name="mobile1" minlength="10" maxlength="10" placeholder="Mobile" value="<?php echo $rowView['mobile1'] ?>" >

                                                </div>

                                            </div>

                                            <div class="col-lg-6">

                                                <span>Phone :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-phone-alt"></span></span>

                                                    <input type="text" class="form-control" name="mobile2" minlength="10" maxlength="12" placeholder="Phone" value="<?php echo $rowView['mobile2'] ?>" >

                                                </div>

                                            </div>

                                            <div class="col-lg-6">

                                                <span>Birth Date :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-calendar-o"></p></span>

                                                    <input type="date" class="form-control" name="birthdate" value="<?php echo $rowView['birthdate'] ?>" placeholder="Birth Date">

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-6">

                                                <span>Skype :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-skype"></p></span>

                                                    <input type="text" class="form-control" name="skypeUname" placeholder="Skype" value="<?php echo $rowView['skypeUname'] ?>" >

                                                </div>

                                                <span>Bank Name :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>

                                                    <input type="text" class="form-control" name="bankName" placeholder="Bank Name" value="<?php echo $rowView['bankName'] ?>" >

                                                </div>

                                                <span>Bank IFSC No. :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>

                                                    <input type="text" class="form-control" name="bankIFSCno" placeholder="Bank IFSC No." value="<?php echo $rowView['bankIFSCno'] ?>" >

                                                </div>

                                                <span>Bank A/c Holder Name :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>

                                                    <input type="text" class="form-control" name="bankAcHolderName" placeholder="Bank A/c Holder Name" value="<?php echo $rowView['bankAcHolderName'] ?>" >

                                                </div>

                                                <span>Bank A/c No. :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>

                                                    <input type="text" class="form-control" pattern="\d*" name="bankAcNo" placeholder="Bank A/c No." value="<?php echo $rowView['bankAcNo'] ?>" >

                                                </div>

                                                

                                            </div>

                                            <div class="col-lg-6">

                                                <span>Joining Date :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-calendar-o"></p></span>

                                                    <input type="date" class="form-control" name="joiningDate" value="<?php echo $rowView['joiningDate'] ?>" placeholder="Joining Date">

                                                </div>

                                                <span>Salary :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 8px 25px 0px 20px;"><p class="fa  fa-rupee"></p></span>

                                                    <input type="text" class="form-control" name="salary" pattern="[0-9]{1,10}" value="<?php echo $rowView['salary'] ?>" placeholder="Salary">

                                                    <span style="color: red;"><?php echo $salaryError; ?></span>

                                                </div>

                                                <span>Address :</span>

                                                <div class="form-group input-group">

                                                    <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-home"></span></span>

                                                    <textarea type="text" rows="7" name="address" class="form-control"><?php echo $rowView['address'] ?></textarea>

                                                </div>

                                            </div>

                                        </div>

                                        </div>
                                        <div class="row employee-submit-row">
                                            <div class="col-lg-12" >

                                                <div class="col-lg-6" align="right">

                                                    <input type="submit" class="btn btn-primary update_btn" value="Update Employee">

                                                </div>

                                                <div class="col-lg-6" align="left">

                                                    <input type="reset" class="btn btn-danger cancel_btn" value="Cancel">

                                                </div>

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

<script>
(function(){var company=document.querySelector('.company-selector'),department=document.querySelector('.department-selector');function filterDepartments(){if(!company||!department)return;var companyId=company.value;Array.prototype.forEach.call(department.options,function(option,index){if(index===0)return;option.hidden=option.getAttribute('data-company')!==companyId;});if(department.selectedOptions[0]&&department.selectedOptions[0].hidden)department.value='0';}if(company){company.addEventListener('change',filterDepartments);filterDepartments();}}());
</script>
<?php include 'footer.php'; ?>

