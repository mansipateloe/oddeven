<section class="wrapper">
 <?php
	  	$select = "select * from cp_login where id='".$_SESSION['admin_login_id']."'";
		$query = mysqli_query($conn,$select);
		$fetch1 = mysqli_fetch_array($query);
		$username = get_username($fetch1['email']);
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
                 	<form class="form-horizontal" method="post" onsubmit="return check_up_profile()" action="process/action_profile.php" enctype="multipart/form-data">
          		 	<?php include("message.php"); ?>
                	     <div class="form-group">
	                           <label for="firstname" class="control-label col-sm-3">First Name</label>
                               <div class="col-sm-6">
                                <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name" value="<?php echo $fetch1['fname']; ?>" maxlength="100">
                                <span class="help" id="msg1"></span>
                               </div>
                         </div>
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">Last Name</label>
                           <div class="col-sm-6">
	                            <input type="text" name="lname" id="lname" class="form-control" placeholder="Last Name" value="<?php echo $fetch1['lname']; ?>" maxlength="100">
                                <span class="help" id="msg2"></span>
                           </div>    
                         </div>
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">Desination</label>
                           <div class="col-sm-6">
	                            <input type="text" name="design" id="design" class="form-control" placeholder="Desination" value="<?php echo $fetch1['desig']; ?>" maxlength="100">
                                <span class="help" id="msg3"></span>
                           </div>    
                         </div>
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">Username</label>
                           <div class="col-sm-6">
	                            <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="<?php echo $username; ?>" maxlength="100">
                           </div>    
                         </div>
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">Mobile</label>
                           <div class="col-sm-6">
	                            <input type="text" name="mobile" id="mobile" maxlength="10" class="form-control decimal" placeholder="Mobile" value="<?php echo $fetch1['mobile']; ?>">
                                <span class="help" id="msg5"></span>
                           </div>    
                         </div>
                         <div class="form-group">
                           <label for="firstname" class="control-label col-sm-3">Email ID</label>
                           <div class="col-sm-6">
	                            <input type="text" name="emailid" id="emailid" class="form-control" placeholder="Email ID" value="<?php echo $fetch1['email']; ?>">
                                <span class="help" id="msg4"></span>
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
                 	<form class="form-horizontal" method="post" onsubmit="return change_pass()" action="process/action_password.php" enctype="multipart/form-data">
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