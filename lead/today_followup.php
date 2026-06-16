<section class="wrapper">
	<div class="row">
       <div class="col-lg-12">
           <ul class="breadcrumb">
              <li><a href="index.php?pid=home"><i class="fa fa-home"></i> Home</a></li>
              <li><a href="#" class="active1">Today Followup</a></li>
           </ul>
       </div>
    </div>
	<div class="row">
    	<div class="col-sm-12">
        	<div class="panel panel-flat">
           		 <div class="panel-heading">
					<h5 class="panel-title">Today Followup Data</h5>
                 </div>
		  		 <div class="panel-body">
                            <div class="col-sm-12">
                                 <?php include("message.php"); ?>
                                 <div class="table-responsive m-t-15">
                                      <table class="table table-bordered table-striped table-condensed" id="datatable">
                                  <thead>
                                  <tr>
                                      <th class="text-center">SrNo</th>
                                      <th>Ex. Name</th>
                                      <th>Followup Type</th>
                                      <th>Company Name</th>
                                      <th>Contact Person</th>
                                      <th>Mobile / Email ID</th>
                                      <th>Remarks</th>
                                      <th class="text-center">Next Followup</th>
                                      <th class="text-center">Actions</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                  	    $query2 = "select lid from cp_lead_followup where nfdate = DATE(NOW()) group by lid ";
										$query = mysqli_query($conn,$query2);
                                        while($fetch = mysqli_fetch_array($query))
                                        {
											$sel = "select * from cp_lead where lid='".$fetch['lid']."'";
											$qry = mysqli_query($conn,$sel);
											$fet = mysqli_fetch_array($qry);
											
											$sel1 = "select * from cp_lead_followup where lid='".$fetch['lid']."' order by fid desc";
											$qry1 = mysqli_query($conn,$sel1);
											$fet1 = mysqli_fetch_array($qry1);
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $fet['empcode']."-".$fet['srno']; ?></td>	
                                            <td><?php echo get_exname($fet1['exname']); ?></td>
                                            <td><?php echo $fet1['ftype']; ?></td>
                                            <td><?php echo $fet['company']; ?></td>
                                            <td><?php echo $fet['cperson']; ?></td>
                                            <td>
                                        		<?php if($fet['mobile1'] != ""){ ?><a href="tel:<?php echo $fet['mobile1']; ?>"><?php echo $fet['mobile1']; ?></a><?php } ?>
                                           		 <?php if($fet['emailid'] != ""){ ?><a href="mailto:<?php echo $fet['emailid']; ?>"><?php echo $fet['emailid']; ?></a><?php } ?>
                                        	</td>
                                            <td><?php echo $fet1['remarks']; ?></td>
                                            <td><?php echo date('d-m-Y',strtotime($fet1['nfdate'])); ?></td>
                                            <td class="text-center">
                                            	  <a href="index.php?pid=view_lead_details&id=<?php echo $fetch['lid']; ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                                           </td>
                                        </tr>
                                 <?php } ?>
                                 
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