<?php include 'header.php'; ?>
<?php 
    $employeeId = $_SESSION['employeeId'];
    $qryView = "SELECT * FROM employeestbl WHERE id=".$employeeId;
    $resultView = mysqli_query($conn,$qryView);
    $rowView = $resultView->fetch_assoc();
?>
        <div id="page-wrapper">
            <section class="table-box">
            <div class="row">
                <div class="col-lg-12">
                     <div class="panel-default">
                        <div class="panel-heading panel-box">
                            <h4>Update Profile</h4> 
                        </div>
                        <div class="panel-body userInfo">
                            <form role="form" method="POST">
                                <div class="row">
                                    <div class="form-group col-lg-3">
                                        <span for="disabledSelect">Employee Code :</span>
                                        <input type="text" class="form-control" name="employeeCode" value="<?php echo $rowView['employeeCode'] ?>" disabled>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <span for="disabledSelect">Designation :</span>
                                        <select class="form-control" disabled="">
                                            <option value="<?php echo $rowView['designation']; ?>" >
                                                <?php echo $rowView['designation']; ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>Name :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p  class="fa fa-user"></p></span>
                                            <input type="text" class="form-control" name="name" value="<?php echo $rowView['name'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row">
                                    <div class="col-lg-6">
                                        <span>Designation :</span>
                                        <div class="form-group input-group">
                                            <select disabled>
                                                <option class="form-control" value="<?php echo $rowView['designation']; ?>"><?php echo $rowView['designation']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div> -->
                                <div class="row">
                                    <div class="col-lg-6">
                                        <span>Username :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 9px 0px 16px;"><p  class="fa fa-user"></p>@</span>
                                            <input type="text" class="form-control" name="employeeUname" value="<?php echo $rowView['employeeUname'] ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>Password :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 16px 0px 20px;"><p class="fa fa-key"></p></span>
                                            <input type="password" class="form-control" name="employeeUpass" value="<?php echo $rowView['employeeUpass'] ?>" required>
                                            <input type="hidden" name="oldEmployeeUpass" value="<?php echo $rowView['employeeUpass'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <span>Company Email :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-envelope"></span></span>
                                            <input type="text" class="form-control" name="companyEmail" value="<?php echo $rowView['companyEmail'] ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>Personal Email :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-envelope"></span></span>
                                            <input type="text" class="form-control" name="personalEmail" value="<?php echo $rowView['personalEmail'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <span>Mobile :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-phone"></p></span>
                                            <input type="text" class="form-control" name="mobile1" value="<?php echo $rowView['mobile1'] ?>" >
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>Phone :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-phone-alt"></span></span>
                                            <input type="text" class="form-control" name="mobile2" value="<?php echo $rowView['mobile2'] ?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <span>Skype :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fab fa-skype"></p></span>
                                            <input type="text" class="form-control" name="skypeUname" value="<?php echo $rowView['skypeUname'] ?>" >
                                        </div>
                                        <span>Bank Name :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>
                                            <input type="text" class="form-control" name="bankName" value="<?php echo $rowView['bankName'] ?>" >
                                        </div>
                                        <span>Bank A/c Holder Name :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>
                                            <input type="text" class="form-control" name="bankAcHolderName" value="<?php echo $rowView['bankAcHolderName'] ?>" >
                                        </div>
                                        <span>Bank A/c No. :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>
                                            <input type="text" class="form-control" pattern="\d*" name="bankAcNo" value="<?php echo $rowView['bankAcNo'] ?>" >
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>Joining Date :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 20px 0px 20px;"><p class="fa fa-calendar-o"></p></span>
                                            <input type="date" class="form-control" name="joiningDate" value="<?php echo $rowView['joiningDate'] ?>" placeholder="Joining Date" disabled>
                                        </div>
                                        <span>Bank IFSC No. :</span>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon" style="padding: 8px 15px 0px 20px;"><p class="fa fa-bank"></p></span>
                                            <input type="text" class="form-control" name="bankIFSCno" value="<?php echo $rowView['bankIFSCno'] ?>" >
                                        </div>
                                        <span>Address :</span>
                                        <div class="form-group input-group address">
                                            <span class="input-group-addon" style="padding: 0px 17px 0px 20px;"><span class="glyphicon glyphicon-home"></span></span>
                                            <textarea type="text" rows="4" name="address" class="form-control"><?php echo $rowView['address'] ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-xs-6" align="right">
                                        <div class="form-group buttons btn-size">
                                        <input type="submit" class="btn btn-primary" name="updateEmployee" value="Update Info">
                                    </div>
                                    </div>
                                    <div class="col-lg-6 col-xs-6" align="left">
                                        <div class="form-group buttons btn-size">
                                        <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                    </div>
                                    </div>
                                </div>
                            </form>
                        </div>  
                    </div>                   
                </div>
                <!-- /.col-lg-12 -->
            </div>

            <!-- /.row -->
            
        </section>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../data/morris-data.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>
<?php include 'footer.php'; ?>
