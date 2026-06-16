<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Oddeven Infotech Pvt. Ltd.</title>
    <!-- Bootstrap Core CSS -->
    <!-- <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link href="../vendor/custom/customAdmin.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
	<?php include 'dbconnect.php'; ?>
	<?php 
		session_start();
		if(isset($_SESSION['adminId'])){
			header("Location:dashboard.php");
		}
	?>
	<?php 
		$loginError = "";

		if(isset($_POST['submit']))
        {
            if(isset($_REQUEST['test_mode']) && $_REQUEST['test_mode']=="1")
            {
                // if($_POST['uname']=="admin" && $_POST['password']=="admin")
                // {
                    $qry = "SELECT * FROM admins WHERE uname='oddeven' ";    
                // }
            }else
            {
                $uname = $_POST['uname'];
                $password = md5($_POST['password']);
                $qry = "SELECT * FROM admins WHERE uname='$uname' && password ='$password'";
            }
			
			$result = mysqli_query($conn,$qry);
			$check = mysqli_fetch_array($result);
			$count = mysqli_num_rows($result);
			if($count == 1){
				session_start();
				$_SESSION["adminId"] = $check['id'];
				header("Location:dashboard.php");
			}else{
				$loginError = "<div class='form-group'><div class='alert alert-danger alert-dismissable'>
	                                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
                                    <p class='alert-link'>Invalid login credentials.</p>
	                            </div></div>";
			}
		}
		
	 ?>

    <div class="container">
        <div class="row">
            <div class="col-md-5 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <!-- <h3 align="center" class="panel-title">Admin Login</h3> -->
                        <img class="img-responsive logo_img" src="../admin/img/logo.png" alt="logo">
                        <p class="text_heading">Enter your Username and Password to access admin panel.</p>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="POST">
                            <?php 
                                if(isset($_REQUEST['test_mode']) && $_REQUEST['test_mode']=="1")
                                {
                                    echo "<input type='hidden' value='1' name='test_mode' />";
                                }
                            ?>
                            <fieldset>
                            	<?php echo $loginError; ?>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input class="form-control" placeholder="Enter your username" name="uname" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="form-control" placeholder="Enter your passeord" name="password" type="password" value="">
                                </div>
                                <input type="submit" class="btn btn-lg btn-success btn-block" value="Login" name="submit">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>
</html>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>