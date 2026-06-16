          <div class="col-sm-12">
            <form name="frmRegistration" id="searchform" method="post" enctype="multipart/form-data" action="index.php?pid=master_search_report">
              <fieldset>
              <legend><i class="fa fa-search"></i>&nbsp;&nbsp; Search Detail</legend>
              <div class="col-md-4">
                <label for="#" class="control-label">Select Date</label>
                <?php 
				
				$fromDate = date("d-m-Y", strtotime("-6 months"));
				$currentdate = date('d-m-Y');
			
				?>
                
              
                <div class="input-group input-large"  data-date-format="mm-dd-yyyy">
                <input type="text" class="form-control form-control-inline input-medium default-date-picker1" data-date-format="dd-mm-yyyy" name="search_from" id="search_from" autocomplete="off" value="<?php if(isset($_REQUEST['search_from']) && $_REQUEST['search_from']!='1970-01-01'){echo $_REQUEST['search_from'];} else{ echo $fromDate; } ?>">
                  <span class="input-group-addon">To</span>
                  <input type="text" class="form-control form-control-inline input-medium default-date-picker1" data-date-format="dd-mm-yyyy" name="search_to" id="search_to" autocomplete="off" value="<?php if(isset($_REQUEST['search_to']) && $_REQUEST['search_to']!='1970-01-01'){echo $_REQUEST['search_to'];} else{echo $currentdate; } ?>">
                
               
                </div>
                <span class="help" id="msg92"></span>
              </div>
              <div class="col-md-4">
                <label for="conpass" class="control-label col-sm-12">&nbsp;</label>
                <button type="submit" name="master_search" id="master_search" class="btn btn-danger btn-sm">Search Details</button>
              </div>
              </fieldset>
            </form>
          </div>