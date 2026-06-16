<section class="wrapper">
	<div class="row">
       <div class="col-lg-12">
           <ul class="breadcrumb">
              <li><a href="index.php?pid=home"><i class="fa fa-home"></i> Home</a></li>
              <li><a href="#" class="active1">View All Leads</a></li>
           </ul>
       </div>
    </div>
	<div class="row">
    	<div class="col-sm-12">
        	<div class="panel panel-flat">
           		 <div class="panel-heading">
					<h5 class="panel-title">All Leads Data</h5>
                 </div>
		  		 <div class="panel-body">
                            <div class="col-sm-12">
                                 <?php include("message.php"); ?>
                                 <div class="table-responsive m-t-15">
                                      <table class="table table-bordered table-striped table-condensed" id="datatable">
                                  <thead>
                                  <tr>
                                      <th class="text-center">SrNo</th>
                                      <th class="text-center">Date</th>
                                      <th>Ex. Name</th>
                                      <th>Company Name</th>
                                      <th>Contact Person</th>
                                      <th>Mobile / Email ID</th>
                                      <th>Product Name</th>
                                      <th>Deal Potential Value</th>
                                      <th class="text-center">Status</th>
                                      <th class="text-center">Actions</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <?php
								  	$status = $_REQUEST['status'];
								  	if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
									{
										$select = "select * from cp_lead where ltype='".$status."' order by lid desc";
									} else {
										$select = "select * from cp_lead where ltype='".$status."' and exname='".$_SESSION['admin_id']."' order by lid desc";
									}
									$query = mysqli_query($conn,$select);
									while($fetch = mysqli_fetch_array($query))
									{
									  ?>
									  <tr>
										<td class="text-center"><?php echo $fetch['empcode']."-".$fetch['srno']; ?></td>
										<td class="text-center"><?php echo date('d-m-Y',strtotime($fetch['ldate'])); ?></td>
										<td><?php echo get_exname($fetch['exname']); ?></td>
										<td><?php echo $fetch['company']; ?></td>
										<td><?php echo $fetch['cperson']; ?></td>
										<td>
                                        	<?php if($fetch['mobile1'] != ""){ ?><a href="tel:<?php echo $fetch['mobile1']; ?>"><?php echo $fetch['mobile1']; ?></a><?php } ?>
                                            <?php if($fetch['emailid'] != ""){ ?><a href="mailto:<?php echo $fetch['emailid']; ?>"><?php echo $fetch['emailid']; ?></a><?php } ?>
                                        </td>
										<td><?php echo $fetch['pname']; ?></td>
										<td><?php if($fetch['dpv'] != ""){ echo number_format($fetch['dpv'], 2, '.', ''); } ?></td>
										<td class="text-center"><?php echo get_lead_status($fetch['ltype']); ?></td>
										<td class="text-center">
												  <a href="index.php?pid=view_lead_details&id=<?php echo $fetch['lid']; ?>" class="btn btn-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a>
                                                  <a href="index.php?pid=add_lead&id=<?php echo $fetch['lid']; ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-pencil"></i></a>
                                                  <a href="#<?php echo $fetch['lid']; ?>" data-toggle="modal" class="btn btn-danger btn-xs" title="Delete"><i class="fa fa-trash-o "></i></a>
                                                  <div class="modal fade modal-dialog-top " id="<?php echo $fetch['lid']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                     <div class="modal-dialog ">
                                                         <div class="modal-content-wrap">
                                                            <div class="modal-content">
                                                               <div class="modal-header">
                                                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                  <h4 class="modal-title text-left">Message</h4>
                                                               </div>
                                                               <div class="modal-body text-left">Are you sure to delete the details?</div>
                                                               <div class="modal-footer">
                                                                    <a href="process/delete_lead.php?id=<?php echo $fetch['lid']; ?>" class="btn btn-danger btn-minier">Delete</a>
                                                                    <button data-dismiss="modal" class="btn btn-default btn-minier" type="button">Cancel</button>
                                                               </div>
                                                           </div>
                                                         </div>
                                                       </div>
                                                     </div>
										</td>
									</tr>
                                <?php
									}
								?>
                            </tbody>
                          </table>
                          </div>
                        </div>
	                  </div>
                 </div>
           </div>
        </div>
     </div>
</section>