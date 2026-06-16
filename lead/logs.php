<section class="wrapper">
			  <div class="row">
              <?php
			  	if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
				{
				?>
                <form name="frmRegistration" method="post" action="index.php?pid=logs" enctype="multipart/form-data">
              	   <fieldset>
					<legend><i class="fa fa-search"></i>&nbsp;&nbsp; Search Logs</legend>
                     <div class="col-sm-12">
                        <div class="col-md-6">
                            <select name="search_executive" id="search_executive" class="form-control mt10">
                               <option value="">Select Member</option>
                               <option value="all">All</option>
                               <?php
							   		$ss1 = "select * from cp_users";
									$qq1 = mysqli_query($conn,$ss1);
									while($ff1 = mysqli_fetch_array($qq1))
									{
							   			?><option value="<?php echo $ff1['emailid']; ?>"><?php echo $ff1['fname']." ".$ff1['lname']; ?></option><?php
									}
							   ?>
                           </select>
                        </div>
                       <div class="col-md-2 mt10">
                       	   <button type="submit" name="master_search" id="master_search" class="btn btn-primary" style="width:100%;">Search Details</button>
                       </div>
                    </div>
				  </fieldset>
                </form>
               <?php } ?>
              </div>
              <div class="row">
                  <div class="col-lg-12">
                      <!--timeline start-->
                      <section class="panel">
                          <div class="panel-body tutorial_list">
                                  <div class="text-center mbot30">
                                      <h3 class="timeline-title">Latest Activities</h3>
                                  </div>
                                  <div class="table-responsive m-t-15">
                              	   <table class="table table-bordered table-striped">
                                   <thead>
                                         <tr>
                                             <th>#</th>
                                             <th>Date</th>
                                             <th>Username</th>
                                             <th>Details</th>
                                         </tr>
                                  </thead>
                                  <tbody id="result">
                                  <?php
								    $where = "";
									$page_query = "";
								  	if(isset($_REQUEST['search_executive'])){ if($_REQUEST['search_executive'] == "all"){ $where = ""; } else { $where = " where exname='".$_REQUEST['search_executive']."'"; $page_query .= "&search_executive=".$_REQUEST['search_executive']; } } else { $where = ""; }
									
									$i = 1;
									include("pagination.php");
									$sel1 = "select * from cp_log ".$where." order by lid desc";
									$res1= mysqli_query($conn,$sel1) or die(mysqli_error($conn));
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
									$pagination->setLink("index.php?pid=logs&".$page_query."&page=%s"); 
									$pagination->setPage($page);
									$pagination->setSize($size);
									$pagination->setTotalRecords($total_records);  
									
									$query2 = "select * from cp_log ".$where." order by lid desc ".$pagination->getLimitSql();
									$query = mysqli_query($conn,$query2);
									 while($fet1 = mysqli_fetch_array($query))
									 {
									    $tutorial_id = 	$fet1['lid'];
									  ?>
                                          <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo date('d-m-Y',strtotime($fet1['ldate'])); ?></td>
                                            <td><?php echo get_exname($fet1['exname']); ?></td>
                                            <td><?php echo stripslashes($fet1['details']); ?></td>
                                         </tr>
									  <?php
									 	$i++;
									 }
									?>
                                  </tbody>
                          </table>
                          <div class="col-md-12 m-b-15">
                          	<div class="col-md-2">
                              <form action="" method="post" >
                                <select name="psize" onchange="this.form.submit();" class="form-control wd70">
                                   <option <?php if($_SESSION['pagesize'] == 10) { echo 'selected="selected"'; } ?>>10</option>
                                   <option <?php if($_SESSION['pagesize'] == 20) { echo 'selected="selected"'; } ?>>20</option>
                                   <option <?php if($_SESSION['pagesize'] == 50) { echo 'selected="selected"'; } ?>>50</option>
                                   <option <?php if($_SESSION['pagesize'] == 100) { echo 'selected="selected"'; } ?>>100</option>
                                </select>
					         </form>
                            </div>
                            <div class="col-md-10">
                              <div class="dataTables_paginate paging_bootstrap pagination">
                                  <ul>
                                    <?php 
                                       $navigation = $pagination->create_links();
                                       echo $navigation;
                                       echo '<li class="disabled"><a href="#">Total Records : '.$total_records.'</a></li>';
                                    ?>
                                 </ul>
                             </div>
                           </div>
                        </div>
                      </div>
                  </div>
                 </section>
               </div>
            </div>
       </section>
       