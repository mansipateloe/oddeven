<section class="wrapper">
	<div class="row">
       <div class="col-lg-12">
           <ul class="breadcrumb">
              <li><a href="index.php?pid=home"><i class="fa fa-home"></i> Home</a></li>
              <li><a href="#" class="active1">Users</a></li>
           </ul>
       </div>
    </div>
	<div class="row">
    	<div class="col-sm-12">
        	<div class="panel panel-flat">
           		 <div class="panel-heading">
					<h5 class="panel-title">Users Data</h5>
                 </div>
		  		 <div class="panel-body">
                 	<?php include("message.php"); ?>
                 	<ul class="nav nav-tabs">
                        <li <?php if(isset($_REQUEST['id'])){} else { echo "class='active'"; } ?>><a href="#view_all_user" data-toggle="tab">View All Users</a></li>
                        <li <?php if(isset($_REQUEST['id'])){ echo "class='active'"; } else { } ?>><a href="#add_user" data-toggle="tab">Add User </a></li>
                    </ul>
                    <div class="tab-content">
                         <div id="view_all_user" class="tab-pane  <?php if(isset($_REQUEST['id'])){} else { echo "active"; } ?>">
                            <div class="col-sm-12">
                                 
                                 <div class="table-responsive m-t-15">
                                      <table class="table table-bordered table-striped" id="datatable">
                                         <thead>
                                           <tr>
                                            <th class="text-center" width="32">#</th>
                                            <th>Full Name</th>
                                           
                                          
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" width="200">Actions</th>
                                           </tr>
                                        </thead>
                                     <tbody id="result">
                                     <?php
									 	$sel = "select * from cp_users";
										$qry = mysqli_query($conn,$sel);
										$i = 1;
										while($fet = mysqli_fetch_array($qry))
										{
									   ?>
                                     	<tr>
                                            <td class="text-center"><?php echo $i; ?></td>	
                                            <td><?php echo $fet['fname']." ".$fet['lname']; ?></td>
                                         
                                            <td><?php echo $fet['phoneno']; ?></td>
                                            <td><?php echo $fet['emailid']; ?></td>
                                            <td class="text-center">
                                            <?php
                                               if($fet['status'] == 0)
                                               {
                                                     ?><a href="index.php?pid=users&status=1&sid=<?php echo $fet['uid']; ?>" class="btn btn-xs btn-success">Active</a><?php
                                               } else {
                                                    ?><a href="index.php?pid=users&status=0&sid=<?php echo $fet['uid']; ?>" class="btn btn-xs btn-primary">De-Active</a><?php
                                               }
                                                if(isset($_REQUEST['status']))
                                                {
            
                                                    mysqli_query($conn,"update cp_users set status='".$_REQUEST['status']."' where uid='".$_REQUEST['sid']."'");
                                                    header("location:index.php?pid=users");
                                                }
                                            ?>
                                           </td>
                                           <td class="text-center">
                                                  <a href="index.php?pid=users&id=<?php echo $fet['uid']; ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-pencil"></i></a>
                                                  <a href="#<?php echo $fet['uid']; ?>" data-toggle="modal" class="btn btn-danger btn-xs" title="Delete"><i class="fa fa-trash-o "></i></a>
                                                  <div class="modal fade modal-dialog-top " id="<?php echo $fet['uid']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                     <div class="modal-dialog ">
                                                         <div class="modal-content-wrap">
                                                            <div class="modal-content">
                                                               <div class="modal-header">
                                                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                  <h4 class="modal-title text-left">Message</h4>
                                                               </div>
                                                               <div class="modal-body text-left">Are you sure to delete the details?</div>
                                                               <div class="modal-footer">
                                                                    <a href="process/delete_user.php?id=<?php echo $fet['uid']; ?>" class="btn btn-danger btn-minier">Delete</a>
                                                                    <button data-dismiss="modal" class="btn btn-default btn-minier" type="button">Cancel</button>
                                                               </div>
                                                           </div>
                                                         </div>
                                                       </div>
                                                     </div>
                                           </td>
                                        </tr>
                                     <?php $i++; } ?>
                                 </tbody>
                             </table>
                          </div>
                            </div>
                         </div>
                         <div id="add_user" class="tab-pane <?php if(isset($_REQUEST['id'])){ echo "active"; } else { } ?>">
                         		<div class="col-sm-12 m-t-40">
                                       <form class="form-horizontal" method="post" onsubmit="return add_member()" action="process/action_user.php" enctype="multipart/form-data">
                                            
                                            <?php
                                                if(isset($_REQUEST['id']))
                                                {
                                                    $select1 = "select * from cp_users where uid='".$_REQUEST['id']."'";
                                                    $query1 = mysqli_query($conn,$select1);
                                                    $fetch1 = mysqli_fetch_array($query1);
                                                }
                                            ?>
                                                 <div class="form-group">
                                                       <label for="firstname" class="control-label col-sm-3">First Name</label>
                                                       <div class="col-sm-6">
                                                        <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name" value="<?php if(isset($_REQUEST['id'])){ echo $fetch1['fname']; } ?>" maxlength="100">
                                                        <span class="help" id="msg1"></span>
                                                       </div>
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Last Name</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="lname" id="lname" class="form-control" placeholder="Last Name" value="<?php if(isset($_REQUEST['id'])){ echo $fetch1['lname']; } ?>" maxlength="100">
                                                        <span class="help" id="msg2"></span>
                                                   </div>    
                                                 </div>
                                                 
                                                 
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">EMP Code</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="empcode" id="empcode" class="form-control" placeholder="EMP Code" value="<?php if(isset($_REQUEST['id'])){ echo $fetch1['empcode']; } ?>" maxlength="100">
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Mobile No</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="phoneno" id="phoneno" maxlength="10" class="decimal form-control" placeholder="Mobile No" value="<?php if(isset($_REQUEST['id'])){ echo $fetch1['phoneno']; } ?>">
                                                        <span class="help" id="msg3"></span>	
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Email ID</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="emailid" id="emailid" class="form-control" placeholder="emailid (For Login)" value="<?php if(isset($_REQUEST['id'])){ echo $fetch1['emailid']; } ?>" onBlur="checkAvailability_emailid_user(this.value,1)">
                                                        <span class="help" id="msg4"></span>
                                                   </div>    
                                                 </div>
                                                 <div class="form-group clearfix">
                                                      <label class="col-md-3 control-label " for="password"> Date of Joining </label>
                                                      <div class="col-md-6 col-xs-11">
                                                          <div class="iconic-input">
                                                            <i class="fa fa-calendar"></i>
                                                              <input class="form-control form-control-inline input-medium default-date-picker1" name="doj" id="doj" placeholder="dd-mm-yyyy" type="text" value="<?php if(isset($_REQUEST['id'])){ if($fetch1['doj'] != "0000-00-00"){ echo date('d-m-Y',strtotime($fetch1['doj'])); }} ?>" />
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="form-group clearfix">
                                                      <label class="col-md-3 control-label " for="password"> Address </label>
                                                      <div class="col-md-6 col-xs-11">
                                                          <textarea class="form-control" name="address" id="address" placeholder="Address"><?php if(isset($_REQUEST['id'])){ echo $fetch1['address']; } ?></textarea>
                                                      </div>
                                                  </div>
                                                 <div class="form-group">
                                                    <label for="firstname" class="control-label col-sm-3">Profile Photo</label>
                                                    <div class="col-sm-6">
                                                        <div class="media no-margin-top">
                                                            <?php
                                                             if(isset($_REQUEST['id']))
                                                             {
                                                                ?>
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
                                                            <?php } ?>
                                                            <div class="media-body">
                                                                <input type="file" name="photo" id="photo" class="form-control">
                                                            </div>
                                                        </div>
                                                        <span class="help" id="msg8"></span>
                                                    </div>
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Password</label>
                                                   <div class="col-sm-6">
                                                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" value="<?php if(isset($_REQUEST['id'])){ echo $fetch1['password']; } ?>">
                                                        <span class="help" id="msg5"></span>
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Confirm Password</label>
                                                   <div class="col-sm-6">
                                                        <input type="password" name="cpassword" id="cpassword" class="form-control" placeholder="Confirm Password" value="<?php if(isset($_REQUEST['id'])){ echo $fetch1['password']; } ?>">
                                                        <span class="help" id="msg6"></span>
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1-1"> Role</label>
                                                    <div class="col-sm-6">
                                                        <select name="utype" id="utype" onchange="check_type(this.value)" class="select-search">
                                                            <option value="">Select Role</option>
                                                            <option <?php if(isset($_REQUEST['id'])){ if($fetch1['utype'] == "1"){ echo "selected"; }} ?> value="1">Admin</option>
                                                            <option <?php if(isset($_REQUEST['id'])){ if($fetch1['utype'] == "2"){ echo "selected"; }} ?> value="2">Technical</option>
                                                            <option <?php if(isset($_REQUEST['id'])){ if($fetch1['utype'] == "3"){ echo "selected"; }} ?> value="3">Sales Executive</option>
                                                        </select>
                                                        <span class="help" id="msg7"></span>
                                                    </div>
                                                 </div>
                                                 <div class="form-group" id="raccess" <?php if(isset($_REQUEST['id'])){ if($fetch1['utype'] == 4){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } } else { echo "style='display:none;'"; } ?>>
                                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">Sales Coordinator</label>
                                                    <div class="col-sm-6">
                                                        <select name="scoordinator" id="scoordinator" class="select-search">
                                                            <option value="">Select Sales Coordinator</option>
                                                            <?php
																$ss = "select * from cp_users where utype='3'";
																$qq = mysqli_query($conn,$ss);
																while($ff = mysqli_fetch_array($qq))
																{
																	?><option <?php if(isset($_REQUEST['id'])){ if($fetch1['scoordinator'] == $ff['emailid']){ echo "selected"; }} ?> value="<?php echo $ff['emailid']; ?>"><?php echo $ff['fname']." ".$ff['lname']; ?></option><?php
																}
															?>
                                                        </select>
                                                    </div>
                                                 </div>
                                                 <div class="form-group">
                                                    <label for="firstname" class="control-label col-sm-3">&nbsp;</label>
                                                    <div class="col-sm-6">
                                                    	<?php
															if(isset($_REQUEST['id']))
															{
																?>
                                                             	 <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
																<?php
															}
														?>
                                                        <button type="submit" id="submit_btn" class="btn btn-primary">Submit Details  </button>
                                                    </div> 
                                                 </div>
                                            </form>
	                                  </div>
                                </div>
                         </div>
                    </div>
                 </div>
           </div>
        </div>
     </div>
</section>