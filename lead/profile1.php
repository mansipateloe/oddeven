<section class="wrapper">
 <?php
	  	$select = "select * from cp_users where uid='".$_SESSION['admin_login_id']."'";
		$query = mysqli_query($conn,$select);
		$fetch1 = mysqli_fetch_array($query);
 ?>
 <div class="row">
	    <div class="col-sm-6">
	       <div class="panel panel-flat">
           		<div class="panel-heading">
							<h5 class="panel-title">Update Profile</h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
					<a class="heading-elements-toggle"><i class="icon-menu"></i></a>
                 </div>
		  		 <div class="panel-body">
                 	<form class="form-horizontal" method="post" onsubmit="return check_up_profile1()" action="process/action_profile1.php" enctype="multipart/form-data">
          		 	<?php include("message.php"); ?>
                    	 <div class="form-group clearfix">
                             <label class="col-md-3 control-label " for="password"> EMP. Code </label>
                             <div class="col-md-6 col-xs-11">
                                  <div class="srno mt5"><?php echo $fetch1['empcode']; ?></div>
                             </div>
                         </div>
                	     <div class="form-group">
	                           <label for="firstname" class="control-label col-sm-3">First Name</label>
                               <div class="col-sm-6">
                                <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name" value="<?php echo $fetch1['fname']; ?>">
                                <span class="help" id="msg1"></span>
                               </div>
                         </div>
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">Last Name</label>
                           <div class="col-sm-6">
	                            <input type="text" name="lname" id="lname" class="form-control" placeholder="Last Name" value="<?php echo $fetch1['lname']; ?>">
                                <span class="help" id="msg2"></span>
                           </div>    
                         </div>
                        
                       
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">Mobile No</label>
                           <div class="col-sm-6">
                              <input type="text" name="phoneno" id="phoneno" maxlength="10" class="form-control decimal" placeholder="Mobile No" value="<?php echo $fetch1['phoneno'];  ?>">
                              <span class="help" id="msg4"></span>
                           </div>    
                         </div>
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">Email ID</label>
                           <div class="col-sm-6">
	                            <input type="text" name="emailid" id="emailid" readonly="readonly" class="form-control" placeholder="Email ID" value="<?php echo $fetch1['emailid']; ?>">
                                <span class="help" id="msg5"></span>
                           </div>    
                         </div>
                         <div class="form-group clearfix">
                             <label class="col-md-3 control-label " for="password"> Date of Joining </label>
                             <div class="col-md-6 col-xs-11">
                                 <div class="iconic-input">
                                   <i class="fa fa-calendar"></i>
                                    <input class="form-control form-control-inline input-medium default-date-picker1" name="doj" id="doj" placeholder="dd-mm-yyyy" type="text" value="<?php if($fetch1['doj'] != "0000-00-00"){ echo date('d-m-Y',strtotime($fetch1['doj'])); } ?>" />
                                 </div>
                             </div>
                         </div>
                         <div class="form-group clearfix">
                              <label class="col-md-3 control-label " for="password"> Address </label>
                              <div class="col-md-6 col-xs-11">
                                  <textarea class="form-control" name="address" id="address" placeholder="Address"><?php echo $fetch1['address']; ?></textarea>
                              </div>
                         </div>
                         
                         <div class="form-group">
							<label for="firstname" class="control-label col-sm-3">Your Photo</label>
                            <div class="col-sm-6">
								<div class="media no-margin-top">
									<div class="thumbnail">
										<a href="#">
                                         <?php
										   if($fetch1['photo'] != "")
										   {
                                        	 ?><img src="img/<?php echo $fetch1['photo']; ?>" style="width: 150px;height: 150px;border-radius: 2px;" alt=""><?php
										   } else {
											 ?><img src="img/user.png" style="width: 150px;height: 150px;border-radius: 2px;" alt=""><?php  
										   }
										   ?><input type="hidden" name="oldphoto" value="<?php echo $fetch1['photo']; ?>" /><?php
										  ?>
                                        </a>
									</div>
									<div class="media-body">
										<div class="uploader bg-warning"><input type="file" name="photo" id="photo" class="form-control"></div>
									</div>
								</div>
                                <span class="help" id="msg6"></span>
							</div>
                         </div>
                         <div class="form-group">
							<label for="firstname" class="control-label col-sm-3">&nbsp;</label>
                            <div class="col-sm-6">
							<button type="submit" class="btn btn-primary">Submit Details <i class="icon-arrow-right14 position-right"></i></button>
						 	</div> 
                         </div>
					</form>
			</div>
          </div>
        </div>
        <div class="col-sm-6">
	       <div class="panel panel-flat">
           		<div class="panel-heading">
							<h5 class="panel-title">Update Profile</h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
					<a class="heading-elements-toggle"><i class="icon-menu"></i></a>
                 </div>
		  		 <div class="panel-body">
                 	<form class="form-horizontal" method="post" onsubmit="return change_pass()" action="process/action_password1.php" enctype="multipart/form-data">
                	     <div class="form-group">
	                           <label for="firstname" class="control-label col-sm-3">Old Password</label>
                               <div class="col-sm-6">
                                <input type="password" name="old_pass" id="old_pass" class="form-control" placeholder="Old Password">
                                <span class="help" id="msg11"></span>
                               </div>
                         </div>
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">New Password</label>
                           <div class="col-sm-6">
	                            <input type="password" name="new_pass" id="new_pass" class="form-control" placeholder="New Password">
                                <span class="help" id="msg12"></span>
                           </div>    
                         </div>
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">Confirm Password</label>
                           <div class="col-sm-6">
	                            <input type="password" name="con_pass" id="con_pass" class="form-control" placeholder="Confirm Password">
                                <span class="help" id="msg13"></span>
                           </div>    
                         </div>
                         <div class="form-group">
							<label for="firstname" class="control-label col-sm-3">&nbsp;</label>
                            <div class="col-sm-6">
							<button type="submit" class="btn btn-primary">Change Password <i class="icon-arrow-right14 position-right"></i></button>
						 	</div> 
                         </div>
					</form>
			</div>
          </div>
        </div>
      </div>
</section>