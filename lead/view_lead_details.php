<section class="wrapper">
	<div class="row">
       <div class="col-lg-12">
           <ul class="breadcrumb">
              <li><a href="index.php?pid=home"><i class="fa fa-home"></i> Home</a></li>
              <li><a href="index.php?pid=view_all_leads">View All Leads</a></li>
              <li><a href="#" class="active1">Lead Details</a></li>
           </ul>
       </div>
    </div>
	<div class="row">
    	 <div class="col-sm-12 pn-button">
         	<div class="btn-group">
	            <?php 
					$prev = get_prev_record_of_lead($_REQUEST['id']); 
					if($prev != "")
					{
					   ?><a href="index.php?pid=view_lead_details&id=<?php echo $prev; ?>" class="btn btn-primary">Previous</a><?php
					}
					$next = get_next_record_of_lead($_REQUEST['id']);
					if($next != "")
					{
		               ?><a href="index.php?pid=view_lead_details&id=<?php echo $next; ?>" class="btn btn-primary">Next</a><?php
					}
				?>
            </div>
         </div>
    	 <div class="col-sm-12">
         	 <?php
			 	$lead_id = $_REQUEST['id'];
			 	$select = "select * from cp_lead where lid='".$lead_id."'";
				$query = mysqli_query($conn,$select);
				$fetch = mysqli_fetch_array($query);
			 ?>
             <?php include("message.php"); ?>
             <section class="panel">
                 <div class="panel-body">
                 		 <div class="col-sm-12 m-b-15">
                         	
                         	<div class="btn-group pull-right">
                                <a href="index.php?pid=add_lead&id=<?php echo $lead_id; ?>" class="btn btn-warning" type="button"> Edit Lead</a>
                                <?php
									if(isset($_REQUEST['transfer']))
									{
										$lid = $_REQUEST['lid'];
										$exname = $_REQUEST['exname'];
										$oexname = $_REQUEST['oexname'];
										$moniby = get_moniby($exname);
										$emp_code = get_lead_code1($exname);
										
										if(mysqli_query($conn,"update cp_lead set empcode='".$emp_code."', exname='".$exname."', moniby='".$moniby."' where lid='".$lid."'") or die(mysqli_error($conn)))
										{
											$_SESSION['msg'] = "done";
											$ldate = date('Y-m-d',time());
											mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Lead details transfer.','".$ldate."','".getHostByName(php_uname('n'))."')");
										}
										header("location:index.php?pid=view_lead_details&id=$lid");
									}
								?>
                                <?php
								  if(($_SESSION['admin_id'] == $fetch['exname']) or ($_SESSION['utype'] == 1) or ($_SESSION['utype'] == 2))
								  {
									 ?><a href="#transfer" data-toggle="modal" class="btn btn-primary">Transfer Lead</a><?php
								  }
								?>
                                      <div class="modal fade modal-dialog-top " id="transfer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                          <div class="modal-dialog ">
                                                              <div class="modal-content-wrap">
                                                                  <div class="modal-content">
                                                                      <div class="modal-header text-left">
                                                                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                          <h4 class="modal-title">Transfer Lead</h4>
                                                                      </div>
                                                                      <div class="modal-body text-left">
                                                                      		<table class="table table-striped table-bordered table-hover">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td width="10%">Current Executive</td>
                                                                                    <td width="2%">:</td>
                                                                                    <td width="30%"><?php echo get_exname($fetch['exname']); ?></td>
                                                                                </tr>
                                                                            </tbody>
                                                                            </table>
                                                                            <br /><br />
                                                                            <form class="form-horizontal" method="post" enctype="multipart/form-data" action="#" role="form">
                                                                            <input type="hidden" name="lid" value="<?php echo $lead_id; ?>" />
                                                                            <input type="hidden" name="oexname" value="<?php echo $fetch['exname']; ?>" />
                                                                            <input type="hidden" name="srno1" value="<?php echo $fetch['srno']; ?>" />
                                                                            <div id="horizontal-form">
                                                                             <div class="form-group">
                                                                                    <label for="inputEmail3" class="col-sm-3 control-label no-padding-right">Transfer To</label>
                                                                                    <div class="col-sm-6">
                                                                                        <select class="form-control" name="exname" id="exname">
                                                                                        <?php
                                                                                            $sel1 = "select * from cp_users";
                                                                                            $qry1 = mysqli_query($conn,$sel1);
                                                                                            while($fet1 = mysqli_fetch_array($qry1))
                                                                                            {
                                                                                                ?><option <?php if($fet1['emailid'] == $fetch['exname']){ echo "selected"; } ?> value="<?php echo $fet1['emailid']; ?>"><?php echo ucwords($fet1['fname']." ".$fet1['lname']); ?></option><?php
                                                                                            }
                                                                                        ?>
                                                                                        </select>	
                                                                                    </div>
                                                                                </div>
                                                                                 <div class="form-group">
                                                                                 	 <label for="inputEmail3" class="col-sm-3 control-label no-padding-right">&nbsp;</label>
                                                                                    <div class="col-sm-6">
                                                                                        <button type="submit" name="transfer" class="btn btn-primary">Transfer Details</button>
                                                                                    </div>
                                                                                </div>
                                                                                <br /><br /><br /><br />
                                                                             </div>
                                                                            </form>
                                                                      </div>
                                                                      <div class="modal-footer">
                                                                         <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                       </div>
                                                      <?php
													  	if(isset($_REQUEST['status']))
														{
															$lid = $_REQUEST['lid'];
															$status = $_REQUEST['status'];
															
															$update = "update cp_lead set ltype='".$status."' where lid='".$lid."'";
															mysqli_query($conn,$update);
															$_SESSION['msg'] = "done";
															
															if($status == 3)
															{
																$sel = "select * from cp_customer order by cid desc";
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
																$sel1 = "select * from cp_lead where lid='$lid'";
																$qry1 = mysqli_query($conn,$sel1);
																$fet1 = mysqli_fetch_array($qry1);
																
																$insert = "insert into cp_customer (cid,srno,company,cperson,mobile1,mobile2,emailid,city,address) values(NULL,'".$srno."','".$fet1['company']."','".$fet1['cperson']."','".$fet1['mobile1']."','".$fet1['mobile2']."','".$fet1['emailid']."','".$fet1['city']."','".$fet1['address']."')";
																mysqli_query($conn,$insert);
															}
															
															$ldate = date('Y-m-d',time());
															mysqli_query($conn,"insert into cp_log (lid,exname,details,ldate,cip) values(NULL,'".$_SESSION['admin_id']."','Lead status details change.','".$ldate."','".getHostByName(php_uname('n'))."')");
															
															header("location:index.php?pid=view_lead_details&id=$lid");
														}
													?>  
                                                    <?php
													  if(($_SESSION['admin_id'] == $fetch['exname']) or ($_SESSION['utype'] == 1) or ($_SESSION['utype'] == 2))
													  {
														  if($fetch['ltype'] != 4 and $fetch['ltype'] != 3)
														  {
                               						   	   
														   	?><a href="#closeinquiry" data-toggle="modal" class="btn btn-danger"> Close Lead</a><?php 
														  }
													  }
													 ?>
                                                       <div class="modal fade modal-dialog-top " id="closeinquiry" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                          <div class="modal-dialog ">
                                                              <div class="modal-content-wrap">
                                                                  <div class="modal-content">
                                                                      <div class="modal-header text-left">
                                                                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                          <h4 class="modal-title">Message</h4>
                                                                      </div>
                                                                      <div class="modal-body text-left">Are you sure to close the Lead?</div>
                                                                      <div class="modal-footer">
                                                                         <a href="index.php?pid=view_lead_details&lid=<?php echo $lead_id; ?>&status=4" class="btn btn-danger btn-xs">YES</a>
                                                                         <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">NO</button>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                      <?php
														  if(($_SESSION['admin_id'] == $fetch['exname']) or ($_SESSION['utype'] == 1) or ($_SESSION['utype'] == 2))
														  {
															  if($fetch['ltype'] != 4 and $fetch['ltype'] != 3)
															  {
															   
																?><a href="#doneinquiry" data-toggle="modal" class="btn btn-success"> Done Lead</a><?php 
															  }
														  }
													 ?>
                               						   
                                                       <div class="modal fade modal-dialog-top " id="doneinquiry" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                          <div class="modal-dialog ">
                                                              <div class="modal-content-wrap">
                                                                  <div class="modal-content">
                                                                      <div class="modal-header text-left">
                                                                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                          <h4 class="modal-title">Message</h4>
                                                                      </div>
                                                                      <div class="modal-body text-left">Are you sure to Done the Lead?</div>
                                                                      <div class="modal-footer">
                                                                         <a href="index.php?pid=view_lead_details&lid=<?php echo $lead_id; ?>&status=3" class="btn btn-danger btn-xs">YES</a>
                                                                         <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">NO</button>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                       </div>
                            </div>
                         </div>
                         <div class="col-sm-12">
                             <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td width="200">Lead Date </td>
                                    <td width="5">:</td>
                                    <td><?php echo date('d-m-Y',strtotime($fetch['ldate'])); ?></td>
                                    <td>Executive Name</td>
                                    <td width="5">:</td>
                                    <td><?php echo get_exname($fetch['exname']); ?></td>
                                </tr>
                                <tr>
                                    <td>Company Name</td>
                                    <td width="5">:</td>
                                    <td><?php echo $fetch['company']; ?></td>
                                    <td width="200">Contact Person</td>
                                    <td width="5">:</td>
                                    <td><?php echo $fetch['cperson']; ?></td>
                                </tr>
                                <tr>
                                    <td rowspan="2">Address</td>
                                    <td rowspan="2" width="5">:</td>
                                    <td rowspan="2"><?php echo $fetch['address']; ?></td>
                                    <td width="200">Mobile No1</td>
                                    <td width="5">:</td>
                                    <td><a href="tel:<?php echo $fetch['mobile1']; ?>"><?php echo $fetch['mobile1']; ?></a></td>
                                </tr>
                                <tr>
                                    <td width="200">Mobile No2</td>
                                    <td width="5">:</td>
                                    <td><a href="tel:<?php echo $fetch['mobile2']; ?>"><?php echo $fetch['mobile2']; ?></a></td>
                                </tr>
                                <tr>
                                    <td width="200">City</td>
                                    <td width="5">:</td>
                                    <td><?php echo $fetch['city']; ?></td>
                                	<td width="200">Email ID</td>
                                    <td width="5">:</td>
                                    <td><a href="mailto:<?php echo $fetch['emailid']; ?>"><?php echo $fetch['emailid']; ?></a></td>
                                </tr>
                                <tr>
                                    <td width="200">Product Name</td>
                                    <td width="5">:</td>
                                    <td><?php echo $fetch['pname']; ?></td>
                                	<td width="200">Status</td>
                                    <td width="5">:</td>
                                    <td><?php echo get_lead_status($fetch['ltype']); ?></td>
                                </tr>
                                <tr>
                                    <td width="200">SrNo</td>
                                    <td width="5">:</td>
                                    <td><?php echo $fetch['empcode']."-".$fetch['srno']; ?></td>
                                	<td width="200">Deal Potential Value</td>
                                    <td width="5">:</td>
                                    <td><?php if($fetch['dpv'] != ""){ echo number_format($fetch['dpv'], 2, '.', ''); } ?></td>
                                 </tr>
                            </tbody>
                            </table>
                        </div>
                        <br>
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1" data-toggle="tab">Followups</a></li>
                            <li><a href="#tab2" data-toggle="tab">Attachments</a></li>
                        </ul>
                        <br>
                        <div class="tab-content">
                          <div id="tab1" class="tab-pane active">
                         		    <div class="table-responsive">
                                        <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Followup Type</th>
                                            <th>Ex. Name</th>
                                            <th>Followup Time</th>
                                            <th>Remarks</th>
                                            <th>Next Followup Time</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
											$sel = "select * from cp_lead_followup where lid='".$lead_id."' order by fid desc";
											$qry = mysqli_query($conn,$sel);
											$i = 1;
											while($fet = mysqli_fetch_array($qry))
											{
											   ?>
												<tr>
													<td class="text-center"><?php echo $i; ?></td>
													<td><?php echo $fet['ftype']; ?></td>
													<td><?php echo get_exname($fet['exname']); ?></td>
													<td><?php echo date('d-m-Y',strtotime($fet['fdate']))." ".$fet['ftime']; ?></td>
													<td><?php echo stripslashes($fet['remarks']); ?></td>
													<td><?php echo date('d-m-Y',strtotime($fet['nfdate']))." ".$fet['nftime']; ?></td>
													<td class="text-center">
													      <a href="index.php?pid=view_lead_details&id=<?php echo $lead_id; ?>&fid=<?php echo $fet['fid']; ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-pencil"></i></a>
                                                          <a href="#<?php echo $fet['fid']; ?>" data-toggle="modal" class="btn btn-danger btn-xs" title="Delete"><i class="fa fa-trash-o "></i></a>
                                                          <div class="modal fade modal-dialog-top " id="<?php echo $fet['fid']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                             <div class="modal-dialog ">
                                                                 <div class="modal-content-wrap">
                                                                    <div class="modal-content">
                                                                       <div class="modal-header">
                                                                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                          <h4 class="modal-title text-left">Message</h4>
                                                                       </div>
                                                                       <div class="modal-body text-left">Are you sure to delete the details?</div>
                                                                       <div class="modal-footer">
                                                                            <a href="process/delete_lead_followup.php?id=<?php echo $lead_id; ?>&fid=<?php echo $fet['fid']; ?>" class="btn btn-danger btn-minier">Delete</a>
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
                                     <hr>
                                     <?php
									 	if(isset($_REQUEST['fid']))
										{
											$fid = $_REQUEST['fid'];
									 		$select1 = "select * from cp_lead_followup where fid='".$fid."'";
											$query1 = mysqli_query($conn,$select1);
											$fetch1 = mysqli_fetch_array($query1);
										}
									 ?>
                                     <form class="form-horizontal" name="frmRegistration" method="post" action="process/action_lead_followup.php" onsubmit="return add_followup()" enctype="multipart/form-data">
                                    	<input type="hidden" name="lid" value="<?php echo $lead_id; ?>">
                    					<h2 class="ctitle"><span>Add Followup</span></h2>	
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label " for="password"> Ex. Name </label>
                                            <div class="col-sm-6">
                                                <input type="text" name="exname" id="exname" class="form-control" readonly value="<?php if(isset($_REQUEST['fid'])){ echo get_exname($fetch1['exname']); } else { echo get_exname($_SESSION['admin_id']);}?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                           <label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">Followup Type</label>
                                            <div class="col-sm-6">
                                               <select name="ftype" id="ftype" class="select-search">
                                                  <option value="">Select Followup Type</option>
                                                  <option <?php if(isset($_REQUEST['fid'])){ if($fetch1['ftype'] == "Email"){ echo "selected"; }} ?> value="Email">Email</option>
                                                  <option <?php if(isset($_REQUEST['fid'])){ if($fetch1['ftype'] == "Call"){ echo "selected"; }} ?> value="Call">Call</option>
                                                  <option <?php if(isset($_REQUEST['fid'])){ if($fetch1['ftype'] == "Visit"){ echo "selected"; }} ?> value="Visit">Visit</option>
                                               </select>
                                               <span class="help" id="msg1"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label " for="password"> Followup Date </label>
                                            <div class="col-sm-6">
                                               <div class="iconic-input">
                                                 <i class="fa fa-calendar"></i>
                                                 <input class="form-control form-control-inline input-medium default-date-picker" name="fdate" id="fdate" placeholder="dd-mm-yyyy" size="16" type="text" value="<?php if(isset($_REQUEST['fid'])){ echo date('d-m-Y',strtotime($fetch1['fdate'])); } else { echo date('d-m-Y',time()); } ?>">
                                               </div>
                                               <span class="help" id="msg2"></span>
                                            </div>
                                        </div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="inputPassword4">Followup Time </label>
											<div class="col-sm-6">
											   <input class="timepicker-default form-control" id="ftime" name="ftime" type="text"  value="<?php if(isset($_REQUEST['fid'])){ echo $fetch1['ftime']; } else { echo date("h:i A",time()); } ?>">
                                               <span class="help" id="msg3"></span>
											</div>
										 </div> 
                                        <div class="form-group">
											<label class="col-sm-3 control-label" for="inputPassword4">Remarks </label>
											<div class="col-sm-6">
											  <textarea class="form-control" id="remarks" type="text" name="remarks"><?php if(isset($_REQUEST['fid'])){ echo stripslashes($fetch1['remarks']); } ?></textarea>
											  
											</div>
										</div>
										<div class="form-group">
                                            <label class="col-sm-3 control-label " for="password"> Next Followup Date </label>
                                            <div class="col-sm-6">
                                               <div class="iconic-input">
                                                 <i class="fa fa-calendar"></i>
                                                 <input class="form-control form-control-inline input-medium default-date-picker" name="nfdate" id="nfdate" placeholder="dd-mm-yyyy" size="16" type="text" value="<?php if(isset($_REQUEST['fid'])){ echo date('d-m-Y',strtotime($fetch1['nfdate'])); } else { echo date('d-m-Y',time()); } ?>">
                                               </div>
                                               <span class="help" id="msg4"></span>
                                            </div>
                                        </div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="inputPassword4">Next Followup Time </label>
											<div class="col-sm-6">
											   <input class="timepicker-default form-control" id="nftime" name="nftime" type="text" value="<?php if(isset($_REQUEST['fid'])){ echo $fetch1['nftime']; } else { echo date("h:i A",time()); } ?>">
                                               <span class="help" id="msg5"></span>
											</div>
										 </div>    
                                         <div class="form-group">
                                             <label for="firstname" class="control-label col-sm-3">&nbsp;</label>
                                             <div class="col-sm-6">
                                                <?php
													if(isset($_REQUEST['fid']))
													{
														?><input type="hidden" name="fid" value="<?php echo $fid; ?>" /><?php
													}
												?>
                                                <button type="submit" class="btn btn-primary">Submit </button>
                                             </div> 
                                         </div>
                               		 </form>
                                     
                             </div>
                             <div id="tab2" class="tab-pane">
                         		    <div class="table-responsive">
                                        <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="50" class="text-center">#</th>
                                            <th>Attachment</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
											$sel2 = "select * from cp_lead_file where lid='".$lead_id."'";
											$qry2 = mysqli_query($conn,$sel2);
											$i = 1;
											while($fet2 = mysqli_fetch_array($qry2))
											{
												?>
												<tr>
													<td class="text-center"><?php echo $i; ?></td>
													<td><a href="img/<?php echo $fet2['lfile']; ?>" target="_blank"><?php echo $fet2['lfile']; ?></a></td>
												</tr>
                                         <?php $i++; } ?>
                                          </tbody>
                                        </table>
                                     </div>
                             </div>
                        </div>
                   </div>
			</section>
		</div>
	</div>
</section>