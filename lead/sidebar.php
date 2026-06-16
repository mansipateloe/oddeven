	<aside>
          <div id="sidebar"  class="nav-collapse  hidden-print">
              <ul class="sidebar-menu" id="nav-accordion">
              	   <li><div class="logo2"><img src="img/logo.png" alt="Crisp" title="Crisp" /></div></li>
                   <li> <a href="javascript:;">Menu</a></li>
              	   <li>
                      <a href="index.php?pid=home">
                          <i class="fa fa-dashboard"></i>
                          <span>Dashboard</span>
                      </a>
                  </li>
                  <?php
				  	if(isset($_REQUEST['pid']))
					{
						$pid = $_REQUEST['pid'];
					} else {
						$pid = "home";
					}
				  ?>
          <li class="sub-menu dcjq-parent-li">
                      <a href="javascript:;" class="dcjq-parent <?php if($pid == "add_lead" || $pid == "view_all_leads" || $pid == "today_followup" || $pid == "pending_followup" || $pid == "view_lead_details"){ echo "active";  } ?>">
                          <i class="fa fa-book"></i>
                          <span>Leads</span>
                          <span class="dcjq-icon"></span></a>
                          <ul class="sub" style="display: none;">
                             <?php if($_SESSION['utype'] != 4){ ?><li><a href="index.php?pid=add_lead">Add Lead</a></li><?php } ?>
                               <li><a href="index.php?pid=today_followup">Todays' Followups</a></li>
                               <li><a href="index.php?pid=pending_followup">Pending Followups</a></li>
                               <li><a href="index.php?pid=view_all_leads">View All Leads</a></li>
                             <li><a href="index.php?pid=view_all_followups">View All Followups</a></li>
                          </ul>
                  </li>
                  <?php if($_SESSION['utype'] == "1"){ ?>
                  <li>
                      <a href="index.php?pid=users">
                          <i class="fa fa-users"></i>
                          <span>Users</span>
                      </a>
                  </li>
                  <?php } ?>
                  
                </ul>
          </div>
      </aside>