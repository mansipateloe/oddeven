<?php 
ob_start();
session_start();
include("process/config.php");
include("functions.php");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo get_company_name(); ?>">
    <meta name="author" content="<?php echo get_company_name(); ?>">
    <meta name="keyword" content="<?php echo get_company_name(); ?>">
    <link rel="shortcut icon" href="img/favicon.ico">
    <title><?php echo get_company_name(); ?></title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <script src="js/ajax.js"></script>
</head>
<body class="login-body">
	<div id="cover"></div>
    <div class="container">
      <form class="form-signin" onSubmit="return check_login()" action="#" method="post">
          <div class="login-wrap">
          	<div class="login_user_avatar"></div>
        	<?php include("message.php"); ?>
            <div class="alert alert-danger alert-block fade in none" id="fillb"></div>
		   	<input class="form-control" type="email" id="login_username" name="login_username" placeholder="Email ID" />
		    <input class="form-control" type="password" id="login_password" name="login_password" placeholder="Password" />
            <p id="loading_spinner"><img src="img/input-spinner.gif"></p>
			<button class="btn btn-lg btn-login btn-block" type="submit">Sign in</button>
		    <span class="pull-right">
                 <a data-toggle="modal" class="forgot" href="#myModal"> Forgot Password?</a>
            </span>
		  </div>
      </form>
	  <!-- Modal -->
          <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                 	  <form onSubmit="return check_forgot()" action="process/action_forgot.php" method="post">
                      <div class="modal-body">
                          <p>Enter your e-mail address below to receive login credentials details.</p>
                          <input type="email" name="email" id="email" placeholder="Email" autocomplete="off" class="form-control placeholder-no-fix">
                          <span class="help" id="msg4"></span>
                      </div>
                      <div class="modal-footer">
                          <button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>
                          <button type="submit" class="btn btn-primary">Submit</button>
                      </div>
                      </form>
                  </div>
              </div>
          </div>
          <!-- modal -->
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>