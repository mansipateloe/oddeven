<section class="wrapper">
	<div class="row">
       <div class="col-lg-12">
           <ul class="breadcrumb">
              <li><a href="index.php?pid=home"><i class="fa fa-home"></i> Home</a></li>
              <li><a href="#" class="active1">View All Followup</a></li>
           </ul>
       </div>
    </div>
	<div class="row">
    	<div class="col-sm-12">
        	<div class="panel panel-flat">
           		 <div class="panel-heading">
					<h5 class="panel-title">View All Followup Data</h5>
                 </div>
                 <?php
				 	if(isset($_REQUEST['ftype']))
					{
						$ftype = $_REQUEST['ftype'];
						if($ftype == 1)
						{
							$where = " AND nfdate = DATE(NOW())";
						}
						if($ftype == 2)
						{
							$where = " AND fid IN (select max(fid) from cp_lead_followup group by lid) AND nfdate < DATE(NOW()) ";
						}
						if($ftype == 3)
						{
							$where = " AND nfdate > DATE(NOW())";
						}
						if($ftype == "")
						{
							$where = "";
						}
					} else {
						$where = "";
					}
				 ?>
		  		 <div class="panel-body">
                 			<div class="col-sm-12">
                            	<form name="frmRegistration" method="post" action="#">
                                 <div class="col-md-4">
                                    <label for="#" class="control-label">Select Followup Type </label>
                                    <select name="ftype" class="form-control">
                                    	<option value="">Select Type</option>
                                        <option <?php if(isset($_REQUEST['ftype'])){ if($ftype == 1){ echo "selected"; }} ?> value="1">Today Followup</option>
                                        <option <?php if(isset($_REQUEST['ftype'])){ if($ftype == 2){ echo "selected"; }} ?> value="2">Pending Followup</option>
                                        <option <?php if(isset($_REQUEST['ftype'])){ if($ftype == 3){ echo "selected"; }} ?> value="3">Future Followup</option>
                                    </select>
                                  </div>
                                  <div class="col-md-4">
                                    <label for="conpass" class="control-label col-sm-12">&nbsp;</label>
                                    <button type="submit" name="master_search" id="master_search" class="btn btn-danger btn-sm">Search Details</button>
                                  </div>
                                </form>
                            </div>
                            <div class="col-sm-12">
                                 <?php include("message.php"); ?>
                                 <div class="table-responsive m-t-15">
                                 <table class="table table-bordered table-striped table-condensed">
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
									  	$i = 1;
									    include("Pagination.php");
										if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
										{
											$sel1 = "select * from cp_lead_followup where fid > 0 ".$where." group by lid order by fid desc";
										} else {
											$sel1 = "select * from cp_lead_followup where exname='".$_SESSION['admin_id']."' ".$where."  group by lid order by fid desc";
										}
										$res1= mysqli_query($conn,$sel1);
										$total_records = mysqli_num_rows($res1);
										$page = 1;
										if(isset($_POST['psize'])) {
											$size = (int)$_POST['psize'];
											$_SESSION['pagesize']= $size;
										} else if(isset($_SESSION['pagesize']) && $_SESSION['pagesize'] != ''){
											$size = $_SESSION['pagesize'];
										} else {
											$size = 10;
											$_SESSION['pagesize']= 10;
										}
										if (isset($_GET['page'])){
											$page = (int) $_GET['page'];
										}
										// create the pagination class
										$pagination = new Pagination();
										$pagination->setLink("index.php?pid=view_all_followups&page=%s"); 
										$pagination->setPage($page);
										$pagination->setSize($size);
										$pagination->setTotalRecords($total_records);  
										
										if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
										{
											$query2 = "select * from cp_lead_followup where fid > 0  ".$where." group by lid order by fid desc ".$pagination->getLimitSql();
										} else {
											$query2 = "select * from cp_lead_followup where exname='".$_SESSION['admin_id']."' ".$where."  group by lid order by fid desc ".$pagination->getLimitSql();
										}
										$query = mysqli_query($conn,$query2) or die(mysqli_error($conn));
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
                          <div class="table-footer">
                                <div class="col-md-2">
                                    <form action="" method="post" >
                                        <select name="psize" onchange="this.form.submit();" class="form-control">
                                            <option <?php if($_SESSION['pagesize'] == 10) { echo 'selected="selected"'; } ?>>10</option>
                                            <option <?php if($_SESSION['pagesize'] == 20) { echo 'selected="selected"'; } ?>>20</option>
                                            <option <?php if($_SESSION['pagesize'] == 30) { echo 'selected="selected"'; } ?>>30</option>
                                            <option <?php if($_SESSION['pagesize'] == 50) { echo 'selected="selected"'; } ?>>50</option>
                                            <option <?php if($_SESSION['pagesize'] == 100) { echo 'selected="selected"'; } ?>>100</option>
                                            <option <?php if($_SESSION['pagesize'] == 200) { echo 'selected="selected"'; } ?>>200</option>
                                            <option <?php if($_SESSION['pagesize'] == 500) { echo 'selected="selected"'; } ?>>500</option>
                                       </select>
                                    </form>                
                                </div>
                                <div class="col-md-2 mt5">
                                  <small class="text-muted inline m-t-small m-b-small"><?php echo 'Total Records : '.$total_records; ?></small>
                                </div>
                                <div class="col-md-8">
                                  <div class="pagination pull-right">  
                                      <ul class="pagination">
                                        <?php 
                                          $navigation = $pagination->create_links();
                                          echo $navigation;
                                        ?>
                                      </ul>  
                                  </div>
                                </div>
                              </div>
                          </div>
                        </div>
	                  </div>
                 </div>
           </div>
        </div>
     </div>
</section>