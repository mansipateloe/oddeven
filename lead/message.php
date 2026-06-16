
<?php
if(isset($_SESSION['print']) && $_SESSION['print']!="")
{	$print = $_SESSION['print']; } else { $print = ""; }
if(isset($_SESSION['msg']) && $_SESSION['msg'] != "")
{
	$msg = $_SESSION['msg'];
	if($msg == "not_allowe")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
         <strong> Error!</strong> Excel file format is not correct. Please use .csv
       </div>
    <?php
	}
	if($msg == "up_black")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
         <strong> Error!</strong> username and password required.
       </div>
    <?php
	}
	if($msg == "pro_status_change")
	{
	?>
       <div class="alert alert-success alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Success!</strong> Your status change successfully.
       </div>
    <?php
	}
	if($msg == "wpass")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> Old password not match.
       </div>
    <?php
	}
	if($msg == "avail")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> Already available please use different.
       </div>
    <?php
	}
	if($msg == "bemail")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> Email id is required.
       </div>
    <?php
	}
	if($msg == "emailnotmatch")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> your email not found in database.
       </div>
    <?php
	}
	if($msg == "user_avail")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> you enter emailid already available please use different.
       </div>
    <?php
	}
	if($msg == "passsend")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> your login details send. please check email.
       </div>
	<?php
	}
	if($msg == "login_fail")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> username and password not match. please try gain.
       </div>
	<?php
	}
	if($msg == "otp_not_match")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> One time password not match. please try gain.
       </div>
	<?php
	}
	if($msg == "course_avail")
	{
	?>
       <div class="alert alert-warning alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Warning!</strong> Course available please enter others.
       </div>
	<?php
	}
	if($msg == "cat_avail")
	{
	?>
       <div class="alert alert-warning alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Warning!</strong> Category available please enter others..
       </div>
	<?php
	}
	if($msg == "done")
	{
	?>
       <div class="alert alert-success alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Success!</strong> Your action complete successfully.
       </div>
	<?php
	}
	if($msg == "report_succ")
	{
	?>
       <div class="alert alert-success alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Success!</strong> your work details save successfully.
       </div>
    <?php
	}
	if($msg == "transfer")
	{
	?>
       <div class="alert alert-success alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Success!</strong>  Inquiry transfer successfully.
       </div>
	<?php
	}
	if($msg == "error")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> Some problem please try again.
       </div>

	<?php
	}
	if($msg == "report_err")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong>  Some problem to save your work details.
       </div>
    <?php
	}
	if($msg == "mailsendyes")
	{
	?>
       <div class="alert alert-success alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Success!</strong>  Product bill mail send successfully.
       </div>
	<?php
	}
	if($msg == "mailsendno")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong>  Some problem in mail sending please try again.
       </div>
	<?php
	}
	if($msg == "deleteyes")
	{
	?>
       <div class="alert alert-success alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Success!</strong> Details delete successfully.
       </div>
	<?php
	}
	if($msg == "deleteno")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> Some problem please try again.
       </div>
	<?php
	}
	if($msg == "logout")
	{
	?>
       <div class="alert alert-success alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Success!</strong>  You Logout successfully.
       </div>
	<?php
	}
	if($msg == "not_pass_match")
	{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> password and confirm password not match.
       </div>
	<?php
	}

}
if(isset($_SESSION['print1']))
{
	?>
       <div class="alert alert-danger alert-block fade in">
         <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        <strong> Error!</strong> <?php echo $_SESSION['print1']; ?>
       </div>
   <?php
}
unset($_SESSION['msg']);
unset($_SESSION['print']);
unset($_SESSION['print1']);
?>
