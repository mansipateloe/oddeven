<section class="wrapper">
	<div class="row">
       <div class="col-lg-12">
           <ul class="breadcrumb">
              <li><a href="index.php?pid=home"><i class="fa fa-home"></i> Home</a></li>
              <li><a href="#" class="active1">Add Lead</a></li>
           </ul>
       </div>
    </div>
	<div class="row">
    	<div class="col-sm-12">
        	<div class="panel panel-flat">
           		 <div class="panel-heading">
					<h5 class="panel-title">Add Lead Data</h5>
                 </div>
		  		 <div class="panel-body">
                 	<div class="tab-content">
                         		<div class="col-sm-12 m-t-40">
                                	   <?php
                                                    $srno = "";
													if(isset($_REQUEST['id']))
													{
														$id = $_REQUEST['id'];
														$sel = "select * from cp_lead where lid='".$id."'";
														$qry = mysqli_query($conn,$sel);
														$fet = mysqli_fetch_array($qry);
													} else {
														$sel = "select * from cp_lead order by lid desc";
														$qry = mysqli_query($conn,$sel);
														if(@mysqli_num_rows($qry) > 0)
														{
															$fet = mysqli_fetch_array($qry);
															$scno = ($fet['srno'])+1;
															$cno  = strlen($scno);
															if($cno == 1){$cno = "00".$scno;} 
															else if($cno == 2){$cno = "0".$scno; } 
															else { $cno = $scno; }
																$srno = $cno;
														} else {
															$srno = "001";
														} 
													}
                                       ?>
                                       <?php $emp_code = get_lead_code(); ?>
                                       <?php include("message.php"); ?>
                                       <form class="form-horizontal" method="post" action="process/action_lead.php" enctype="multipart/form-data" onsubmit="return add_lead()">
                                            <div class="form-group">
                                                    <label class="col-sm-3 control-label" for="inputText1">SrNo. </label>
                                                    <div class="col-sm-6 srno mt5">
                                                        <?php echo $emp_code."-"; ?><?php if(isset($_REQUEST['id'])){ echo $fet['srno']; } else { echo $srno; } ?>
                                                    </div>
                                                    <input type="hidden" name="srno" id="srno" value="<?php if(isset($_REQUEST['id'])){ echo $fet['srno']; } else { echo $srno; } ?>" />
                                                    <input type="hidden" name="empcode" id="empcode" value="<?php echo $emp_code; ?>" />
                                              </div>
                                              <div class="form-group clearfix">
                                                  <label class="col-md-3 control-label " for="password"> Lead Date </label>
                                                  <div class="col-md-6 col-xs-11">
                                                      <input class="form-control" readonly="readonly" name="ldate" id="ldate" placeholder="dd-mm-yyyy" type="text" value="<?php if(isset($_REQUEST['id'])){ if($fet['ldate'] != "0000-00-00"){ echo date('d-m-Y',strtotime($fet['ldate'])); }} else { echo date('d-m-Y',time()); } ?>" />
                                                  </div>
                                              </div>
                                              
                                              <div class="form-group">
                                                 <label for="firstname" class="control-label col-sm-3">Executive Name</label>
                                                 <div class="col-sm-6">
                                                 	<input type="text" name="exname" id="exname" class="form-control" readonly value="<?php if(isset($_REQUEST['id'])){ echo get_exname($fet['exname']); }else{ echo get_exname($_SESSION['admin_id']);}?>">
                                                 </div>
                                              </div>
                                              <div class="form-group">
                                                  <label for="firstname" class="control-label col-sm-3">Company Name</label>
                                                  <div class="col-sm-6">
                                                      <input type="text" name="company" id="company" class="form-control" placeholder="Company Name" value="<?php if(isset($_REQUEST['id'])){ echo $fet['company']; } ?>" maxlength="100">
                                                  </div>
                                              </div>
                                              <div class="form-group">
                                                       <label for="firstname" class="control-label col-sm-3">Contact Person</label>
                                                       <div class="col-sm-6">
                                                        <input type="text" name="cperson" id="cperson" class="form-control" placeholder="Contact Person" value="<?php if(isset($_REQUEST['id'])){ echo $fet['cperson']; } ?>" maxlength="100">
                                                        <span class="help" id="msg1"></span>
                                                       </div>
                                              </div>
                                                <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Mobile No1</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="mobileno1" id="mobileno1" maxlength="10" class="decimal form-control" placeholder="Mobile No1" value="<?php if(isset($_REQUEST['id'])){ echo $fet['mobile1']; } ?>">
                                                        <span class="help" id="msg2"></span>
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Mobile No2</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="mobileno2" id="mobileno2" maxlength="10" class="decimal form-control" placeholder="Mobile No2" value="<?php if(isset($_REQUEST['id'])){ echo $fet['mobile2']; } ?>">
                                                        <span class="help" id="msg2_1"></span>
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Email ID</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="emailid" id="emailid" class="form-control" placeholder="Email ID" value="<?php if(isset($_REQUEST['id'])){ echo $fet['emailid']; } ?>">
                                                        <span class="help" id="msg3"></span>
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">City</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="city" id="city" class="form-control" placeholder="City" value="<?php if(isset($_REQUEST['id'])){ echo $fet['city']; } ?>" maxlength="100">
                                                        
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Address</label>
                                                   <div class="col-sm-6">
                                                        <textarea name="address" id="address" class="form-control"><?php if(isset($_REQUEST['id'])){ echo stripslashes($fet['address']); } ?></textarea>
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Product Name</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="pname" id="pname" class="form-control" placeholder="Product Name" value="<?php if(isset($_REQUEST['id'])){ echo $fet['pname']; } ?>" maxlength="100">
                                                   </div>    
                                                 </div>
                                                 <div class="form-group">
                                                   <label for="firstname" class="control-label col-sm-3">Deal Potential Value</label>
                                                   <div class="col-sm-6">
                                                        <input type="text" name="dpv" id="dpv" class="decimal decimal1 form-control" placeholder="Deal Potential Value" value="<?php if(isset($_REQUEST['id'])){ echo $fet['dpv']; } ?>" maxlength="100">
                                                   </div>    
                                                 </div>
                                                 <div class="form-group clearfix">
                                                    <label class="col-md-3 control-label " for="password">Lead Type  </label>
                                                    <div class="col-md-8 col-xs-11">
                                                        <span class="icheck-inline">
                                                            <input type="radio" name="ltype" id="ltype" value="0" <?php if(isset($_REQUEST['id'])){ if($fet['ltype'] == 0){ echo "checked"; }} else { echo "checked"; } ?>>
                                                            <label for="radio1"><span><span></span></span>Low</label>
                                                        </span>
                                                        <span class="icheck-inline">
                                                            <input type="radio" name="ltype" id="ltype" value="1" <?php if(isset($_REQUEST['id'])){ if($fet['ltype'] == 1){ echo "checked"; }} ?>>
                                                            <label for="radio1"><span><span></span></span>Medium</label>
                                                        </span>
                                                        <span class="icheck-inline">
                                                            <input type="radio" name="ltype" id="ltype" value="2" <?php if(isset($_REQUEST['id'])){ if($fet['ltype'] == 2){ echo "checked"; }} ?>>
                                                            <label for="radio1"><span><span></span></span>High</label>
                                                        </span>
                                                     </div>
                                                 </div>
                                                 <?php
													  if(!isset($_REQUEST['id']))
													  {
													  ?>
                                                      <div class="form-group">
                                                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">Followup Type</label>
                                                        <div class="col-sm-6">
                                                            <select name="ftype" id="ftype" class="select-search">
                                                                <option value="">Select Followup Type</option>
                                                                <option value="Email">Email</option>
                                                                <option value="Call">Call</option>
                                                                <option value="Visit">Visit</option>
                                                            </select>
                                                            <span class="help" id="msg4"></span>
                                                        </div>
                                                      </div>
													  <div class="form-group">
														<label class="col-sm-3 control-label" for="inputPassword4">Remarks </label>
														<div class="col-sm-6">
														  <textarea class="form-control" id="remarks" type="text" name="remarks"></textarea>
														  <span class="help" id="msg12"></span>
														</div>
													 </div>
													 <div class="form-group clearfix">
                                                          <label class="col-sm-3 control-label " for="password"> Next Followup Date </label>
                                                          <div class="col-sm-6">
                                                              <div class="iconic-input">
                                                                <i class="fa fa-calendar"></i>
                                                                  <input class="form-control form-control-inline input-medium default-date-picker" name="nfdate" id="nfdate" placeholder="dd-mm-yyyy"  size="16" type="text" value="<?php if(isset($_REQUEST['id'])){ echo date('d-m-Y',strtotime($fet['cdate'])); } else { echo date('d-m-Y',time()); } ?>" />
                                                              </div>
                                                          </div>
                                                      </div>
													  <div class="form-group">
														<label class="col-sm-3 control-label" for="inputPassword4">Next Followup Time </label>
														<div class="col-sm-6">
														   <input class="timepicker-default form-control" id="nftime" name="nftime" type="text" value="<?php echo date("h:i A",time()); ?>">
														</div>
													 </div>
													 <?php } ?>
                                                     <div class="form-group">
                                                           <label for="firstname" class="control-label col-sm-3">Attachments  </label>
                                                           <div class="col-sm-6">
                                                              <input type="file" name="lfile" id="inputfile1">
                                                              <?php
                                                                if(isset($_REQUEST['id']))
                                                                {
                                                                    $ss1 = "select * from cp_lead_file where lid='".$fet['lid']."'";
                                                                    $qq1 = mysqli_query($conn,$ss1) or die(mysqli_error($conn));
                                                                    ?>
                                                                      <div class="fileuploader-items">
                                                                           <ul class="fileuploader-items-list" style="margin:15px;">
                                                                            <?php
                                                                              $j = 1;
                                                                              while($ff1 = mysqli_fetch_array($qq1))
                                                                              {
                                                                                ?><li class="fileuploader-item file-type-image file-ext-jpg" id="file<?php echo $j; ?>">
                                                                                     <div class="column-thumbnail">
                                                                                          <img src="img/<?php echo $ff1['lfile']; ?>" width="36" height="36"  />
                                                                                     </div>
                                                                                     <div class="column-actions"><a href="javascript:void(0);" onclick="remove_lead_file('<?php echo $j; ?>','<?php echo $ff1['id']; ?>')" class="fileuploader-action fileuploader-action-remove" title="Remove"><i></i></a></div>
                                                                                </li>
                                                                            <?php $j++; } ?>
                                                                            </ul>
                                                                     </div>
                                                              <?php } ?>
                                                           </div>
                                                   </div>
                                                   <div class="form-group">
                                                    <label for="firstname" class="control-label col-sm-3">&nbsp;</label>
                                                    <div class="col-sm-6">
														<?php
                                                            if(isset($_REQUEST['id']))
                                                            {
                                                                ?><input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" /><?php
                                                            }	
                                                        ?>
                                                        <button type="submit" class="btn btn-primary">Submit Details </button>
                                                    </div> 
                                                 </div>
                                            </form>
	                                  </div>
                                </div>
                 </div>
           </div>
        </div>
     </div>
</section>