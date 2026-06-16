<section class="wrapper">
  <div class="row">
   <div class="col-sm-12">
       <div class="panel panel-default">
       		<header class="panel-heading">
                             View All Navigation
           </header>
           <div class="panel-body">
	          	<div class="table-responsive m-t-15">
                  <table id="datatable" class="table table-striped table-inverse table-colored table-bordered">
                     <thead>
                       <tr>
							<th class="text-center" width="50">#</th>
							<th class="text-center" >Menu Name</th>
                            <th class="text-center" >Menu Title</th>
		                    <th class="text-center">Parent Menu</th>
							<th class="text-center" width="200">Actions</th>
					   </tr>
                     </thead>
                     <tbody id="result">
                     <?php
				  	  $i = 1;
					  $query2 = "select * from cp_menu order by mid desc";
					  $query = mysqli_query($conn,$query2);
					  while($fetch = mysqli_fetch_array($query))
                      {
						?>
						<tr>
							<td class="text-center"><?php echo $fetch['mid']; ?></td>
                            <td class="text-center"><?php echo $fetch['mname']; ?></td>
                            <td class="text-center"><?php echo $fetch['mtitle']; ?></td>
                            <td class="text-center"><?php echo get_parent_menu($fetch['pmenu']); ?></td>
                            <td class="text-center">
                               <a href="index.php?pid=navigation&id=<?php echo $fetch['mid']; ?>" class="btn btn-xs btn-warning">Edit</a>
 	                           <a href="#<?php echo $fetch['mid']; ?>" data-toggle="modal" class="btn btn-danger btn-xs">Delete</a>
			                          <div class="modal fade modal-dialog-top " id="<?php echo $fetch['mid']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			                             <div class="modal-dialog ">
			                                 <div class="modal-content-wrap">
			                                    <div class="modal-content">
			                                       <div class="modal-header">
			                                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    			                                      <h4 class="modal-title text-left">Message</h4>
			                                       </div>
			                                       <div class="modal-body text-left">Are you sure to delete the details?</div>
			                                       <div class="modal-footer">
			                                            <a href="process/delete_menu.php?id=<?php echo $fetch['mid']; ?>" class="btn btn-danger btn-minier">Delete</a>
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
    </div>
</div>
<div class="row">
   <div class="col-sm-12">
       <div class="panel panel-color panel-inverse">
       	   <div class="panel-heading">
              <h3 class="panel-title">Add Navigation Details</h3>
           </div>
           <div class="panel-body">
           		<?php include("message.php"); ?>
                <?php
					if(isset($_REQUEST['id']))
					{
						$select1 = "select * from cp_menu where mid='".$_REQUEST['id']."'";
						$query1 = mysqli_query($conn,$select1);
						$fetch1 = mysqli_fetch_array($query1);
					}
				?>
                <form class="form-horizontal" role="form" method="post" action="process/action_menu.php" enctype="multipart/form-data">
					<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1"> Menu Name :</label>
						<div class="col-sm-5">
                           <input type="text" name="mname" id="mname" placeholder="Menu Name" class="form-control" <?php if(isset($_REQUEST['id'])){ echo "value='".$fetch1['mname']."'"; } ?>>
						</div>
					</div>
                    <div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1"> Menu Title :</label>
						<div class="col-sm-5">
                            <input type="text" name="mtitle" id="mtitle" placeholder="Menu Title" class="form-control" <?php if(isset($_REQUEST['id'])){ echo "value='".$fetch1['mtitle']."'"; } ?>>
                            <span class="help" id="msg3"></span>
						</div>
					</div>
                    <div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1"> Menu Parent :</label>
						<div class="col-sm-5">
							<select id="pmenu" name="pmenu" class="form-control">
                            	<option value="0">Select Parent</option>
                                <?php
									$ss = "select mid,mname from cp_menu where pmenu='0'";
									$qq = mysqli_query($conn,$ss);
									while($ff = mysqli_fetch_array($qq))
									{
										?><option value="<?php echo $ff['mid']; ?>"><?php echo $ff['mname']; ?></option><?php
									}
								?>
                            </select>
						</div>
					</div>
                    <div class="clearfix form-actions">
						<div class="col-md-offset-3 col-md-9">
                            <?php
								if(isset($_REQUEST['id']))
								{
									?><input type="hidden" name="mid" value="<?php echo $_REQUEST['id']; ?>" /><?php
								}
							?>
							<button class="btn btn-primary" type="submit">Save Details</button>
						</div>
					</div>
				</form>
           </div>
       </div>
   </div>
</div>