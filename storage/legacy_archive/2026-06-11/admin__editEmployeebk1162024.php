<?php include 'header.php'; ?>



    <?php 

        $id = $_GET['edit'];

        $qryView = "SELECT * FROM employeesTbl WHERE id=".$id;

        $resultView = mysqli_query($conn,$qryView);

        $rowView = $resultView->fetch_assoc();

    ?>    

        <div id="page-wrapper">

            <div class="row">

                <div class="col-lg-12">
                    <div class="panel panel-default">

                            <div class="panel-heading">

                                Edit Employee

                            </div>
                            <div class="panel-body">

                            	<div class="dataTablesbox2 edit_employee_detail">

                                    <form role="form" method="POST">

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

                                                    <input type="password" class="form-control" name="employeeUpass" value="<?php echo $rowView['employeeUpass'] ?>" placeholder="Password" required>

                                                    <input type="hidden" name="oldEmployeeUpass" value="<?php echo $rowView['employeeUpass'] ?>">

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

                                        <div class="row">

                                            <div class="col-lg-12" >

                                                <div class="col-lg-6" align="right">

                                                    <input type="submit" class="btn btn-primary update_btn" name="updateEmployee" value="Update">

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

<?php include 'footer.php'; ?>

