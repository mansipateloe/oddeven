<header class="header white-bg">
	  <div class="sidebar-toggle-box">
         <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
      </div>
      <a href="index.php?pid=home" class="logo1">Lead Management System</a>
            <div class="top-nav ">
              <ul class="nav pull-right top-menu">
               	  <li>
                  </li>
                  <li class="dropdown">
                  	  <?php
							$photo = get_exname_photo($_SESSION['admin_id']);
					  ?>
                      <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                           <?php
						   		if($photo == "")
								{
						   		   ?><img alt="Profile" title="Profile" src="img/user.png"><?php
								} else {
								   ?><img alt="Profile" title="Profile" src="img/<?php echo $photo; ?>"><?php
								}
						    ?>
                          <span class="username"><?php echo get_exname($_SESSION['admin_id']); ?></span>
                          <b class="caret"></b>
                      </a>
                      <ul class="dropdown-menu extended logout">
                          <div class="log-arrow-up"></div>
                          <li><a href="index.php?pid=<?php if($_SESSION['admin_login_id'] == 1){ echo "profile"; } else { echo "profile1"; } ?>">Profile</a></li>
                          <li><a href="process/logout.php"><i class="fa fa-key"></i> Log Out</a></li>
                      </ul>
                  </li>
                </ul>
            </div>
        </header>