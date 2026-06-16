<section class="wrapper">
              <!--state overview start-->
              <div class="row">
                <?php
					if($_SESSION['utype'] != 4){
				?>
              	<div class="col-sm-12">
                	<div class="col-sm-4">
                    	<div class="state-overview">
                           <section class="panel">
                           	  <a href="index.php?pid=view_all_leads">
                              	<div class="symbol blue"><i class="fa fa-bar-chart-o"></i></div>
	                          	<div class="value"><h1><?php echo get_total_lead(); ?></h1><p>Total Lead</p></div>
                              </a>
                           </section>
                         </div>
                    </div>
                    <div class="col-sm-4">
                    	<div class="state-overview">
                           <section class="panel">
                           	  <a href="index.php?pid=view_status_lead&status=3">	
                              	<div class="symbol terques"><i class="fa fa-bar-chart-o"></i></div>
	                          	<div class="value"><h1><?php echo get_status_lead(3); ?></h1><p>Done</p></div>
                              </a>
                           </section>
                         </div>
                    </div>
                    <div class="col-sm-4">
                    	<div class="state-overview">
                           <section class="panel">
                              <a href="index.php?pid=view_status_lead&status=4">
                              	<div class="symbol red"><i class="fa fa-bar-chart-o"></i></div>
	                          	<div class="value"><h1><?php echo get_status_lead(4); ?></h1><p>Close</p></div>
                              </a>
                           </section>
                         </div>
                    </div>
                    <div class="col-sm-4">
                    	<div class="state-overview">
                           <section class="panel">
                           	  <a href="index.php?pid=view_status_lead&status=2">	
                              	<div class="symbol terques"><i class="fa fa-bar-chart-o"></i></div>
	                          	<div class="value"><h1><?php echo get_status_lead(2); ?></h1><p>High</p></div>
                              </a>
                           </section>
                         </div>
                    </div>
                    <div class="col-sm-4">
                    	<div class="state-overview">
                           <section class="panel">
                           	  <a href="index.php?pid=view_status_lead&status=1">	
                              	<div class="symbol yellow"><i class="fa fa-bar-chart-o"></i></div>
	                          	<div class="value"><h1><?php echo get_status_lead(1); ?></h1><p>Medium</p></div>
                              </a>
                           </section>
                         </div>
                    </div>
                    <div class="col-sm-4">
                    	<div class="state-overview">
                           <section class="panel">
                           	  <a href="index.php?pid=view_status_lead&status=0">	
                              	<div class="symbol red"><i class="fa fa-bar-chart-o"></i></div>
	                          	<div class="value"><h1><?php echo get_status_lead(0); ?></h1><p>Low</p></div>
                              </a>
                           </section>
                         </div>
                    </div>
                </div>
                <?php } ?>
              </div>
              <div class="row">
                  <div class="col-lg-12">
                  	 <section class="panel">
                          <div class="panel-body">
                              <div id="fullcalendar-basic" class="has-toolbar"></div>
                          </div>
                      </section>
                  </div>
              </div>
              
              
</section>      