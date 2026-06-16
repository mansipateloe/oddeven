<section class="wrapper">
			  <div class="row">
                   <div class="col-lg-12">
                       <ul class="breadcrumb">
                          <li><a href="index.php?pid=home"><i class="fa fa-home"></i> Home</a></li>
                           <li><a href="#" class="active1">Daily Reports</a></li>
                       </ul>
                   </div>
               </div>
               <?php
							$search_from = date('Y-m-d',strtotime(@$_REQUEST['search_from']));
							$search_to = date('Y-m-d',strtotime(@$_REQUEST['search_to']));


							$page_query = "";
							$page_query .= "&search_from=".$search_from;
							$page_query .= "&search_to=".$search_to;
							
							
							$where = " where rdate >= '".$search_from."' and rdate <= '".$search_to."'";
				 ?>	
               <div class="row">
              	<?php 
					 include("report_searchbar.php"); 
				?>
              </div>	
              <!-- page start-->
              <div class="row">
                  <div class="col-lg-12">
                      <section class="panel">
                          <header class="panel-heading">
                             Search Results report
                          </header>
                          <div class="panel-body">
                          	  <?php include("message.php"); ?>
                              <div class="table-responsive m-t-15">
                              	  <table class="table table-bordered table-striped">
                                         <thead>
                                           <tr>
                                            <th class="text-center" width="32">#</th>
                                            <th class="text-center">Date</th>
                                            <?php if($_SESSION['utype'] != 4){?> <th>FullName</th><?php } ?>
                                            <th class="text-center">No of Visits</th>
                                            <th class="text-center">Distance Travelled</th>
                                            <th class="text-center">Total Sales</th>
                                            <th class="text-center">Total Expense</th>
                                            <th class="text-center">Paid Amount</th>
                                            <th class="text-center">Pending Amount</th>
                                            <th class="text-center" width="200">Actions</th>
                                           </tr>
                                        </thead>
                                     <tbody id="result">
                                      <?php 
									  	$i = 1;
									    include("Pagination.php");
										if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
										{
											$sel1 = "select * from cp_report ".$where." and status='1' order by rid desc";
										} else {
											$sel1 = "select * from cp_report ".$where." and (exname='".$_SESSION['admin_id']."' or exname IN (select emailid from cp_users where scoordinator='".$_SESSION['admin_id']."')) order by rid desc";
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
										$pagination->setLink("index.php?pid=master_search_report&page=%s"); 
										$pagination->setPage($page);
										$pagination->setSize($size);
										$pagination->setTotalRecords($total_records);  
										
										if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
										{
											$query2 = "select * from cp_report ".$where." and status='1' order by rid desc ".$pagination->getLimitSql();
										} else {
											$query2 = "select * from cp_report ".$where." and (exname='".$_SESSION['admin_id']."' or exname IN (select emailid from cp_users where scoordinator='".$_SESSION['admin_id']."')) order by rid desc ".$pagination->getLimitSql();
										}
										$query = mysqli_query($conn,$query2);
										while($fetch = mysqli_fetch_array($query))
										{
									      ?>
                                     	  <tr>
                                            <td class="text-center"><?php echo $i; ?></td>	
                                            <td class="text-center"><?php echo date('Y-m-d',strtotime($fetch['rdate'])); ?></td>
                                            <?php if($_SESSION['utype'] != 4){?> <td><?php echo get_exname($fetch['exname']); ?></td><?php } ?>
                                            <td class="text-center"><?php echo $fetch['no_of_visit']; ?></td>
                                            <td class="text-center"><?php echo $fetch['dist_travel']; ?></td>
                                            <td class="text-center">
											<?php 
												if($fetch['total_sale'] != "")
												{
													echo number_format($fetch['total_sale'], 2, '.', '');
												}
											?>
                                            </td>
                                            <td class="text-center">
											<?php 
												$t_expense = get_total_expense_report($fetch['rid']); 
												echo number_format($t_expense, 2, '.', '');
											?>
                                            </td>
                                            <td class="text-center">
											<?php 
												$t_expense_paid = get_total_expense_paid_report($fetch['rid']);
												echo number_format($t_expense_paid, 2, '.', ''); 
											?>
                                            </td>
                                            <td class="text-center">
											<?php 
												$t_expense_unpaid = get_total_expense_unpaid_report($fetch['rid']); 
												echo number_format($t_expense_unpaid, 2, '.', ''); 
											?>
                                            </td>
                                           <td class="text-center">
                                                   <a href="index.php?pid=view_report_detail&id=<?php echo $fetch['rid']; ?>" class="btn btn-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a>
                                                   <a href="index.php?pid=add_report&id=<?php echo $fetch['rid']; ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-pencil"></i></a>
                                                  <a href="#<?php echo $fetch['rid']; ?>" data-toggle="modal" class="btn btn-danger btn-xs" title="Delete"><i class="fa fa-trash-o "></i></a>
                                                  <div class="modal fade modal-dialog-top " id="<?php echo $fetch['rid']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                     <div class="modal-dialog ">
                                                         <div class="modal-content-wrap">
                                                            <div class="modal-content">
                                                               <div class="modal-header">
                                                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                  <h4 class="modal-title text-left">Message</h4>
                                                               </div>
                                                               <div class="modal-body text-left">Are you sure to delete the details?</div>
                                                               <div class="modal-footer">
                                                                    <a href="process/delete_report.php?id=<?php echo $fetch['rid']; ?>" class="btn btn-danger btn-minier">Delete</a>
                                                                    <button data-dismiss="modal" class="btn btn-default btn-minier" type="button">Cancel</button>
                                                               </div>
                                                           </div>
                                                         </div>
                                                       </div>
                                                     </div>
                                                      <?php
												  	if($fetch['status'] == 0)
													{
														?><span class='label label-default'>Draft</span><?php
													}
												  ?>
                                           </td>
                                        </tr>
                                     <?php $i++; } ?>
                                     
                                 </tbody>
                             </table>
                          
                                  <div class="col-md-12 m-b-15">
                                    <div class="col-md-2">
                                      <form action="" method="post" >
                                        <select name="psize" onchange="this.form.submit();" class="form-control wd70">
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