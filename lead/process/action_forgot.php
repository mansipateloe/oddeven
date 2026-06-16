<?php
ob_start();
session_start();
include("config.php");
include("../functions.php");

$email = $_REQUEST['email'];


$sel = "select * from cp_login where email='".$email."' and status != '1'";
$qry = mysqli_query($conn,$sel) or die(mysqli_error($conn));
if(mysqli_num_rows($qry)<=0)
{
	$sel1 = "select * from cp_users where emailid='".$email."' and status != '1'";
	$qry1 = mysqli_query($conn,$sel1) or die(mysqli_error($conn));
	if(mysqli_num_rows($qry1)<=0)
	{
		$_SESSION['msg'] = "ferror";	
		header("location:../login.php");
	} else {
				$fet1 = mysqli_fetch_array($qry1);
				
				require("class.phpmailer.php");
				$mail = new PHPMailer();
				$mail->IsSMTP(); 
				$mail->Host = HOST;
				$mail->Port = PORT;
				$mail->Username = USERNAME; 
				$mail->Password = PASSWORD; 	
				$mail->SMTPAuth = true; 
				$mail->From = FROMEMAIL; 
				$mail->FromName = FROMNAME;
				$mail->AddAddress($email," ");
				$mail->IsHTML(true); 
				$mail->Subject = "Login Credentials Details";
				$mailmsg = "<div>
								<table width='620' border='0' cellspacing='0' cellpadding='0' align='center' bgcolor='#eff2f3' style='border-top:1px solid #d5d8d9;border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#000'>
									<tbody><tr>
									<td style='padding:13px 12px 0px 12px'>
									<table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' style='border-top:1px solid #d5d8d9;border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9;background:#FFFFFF;border-bottom: 1px solid #D5D8D9;'>
									  <tbody><tr>
										<td style='padding:7px'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
										  <tbody><tr>
											<td style='color:#000;padding-bottom:5px'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
											  <tbody><tr>
												<td style='line-height:33px'>&nbsp;</td>
													<td>
														<div style='width:100%'>
															<br>
															<div style='font-size:20px;font-family:arial;line-height:30px; color:#fff;'><img src='".ROOT."/img/logo.png' width='200' title='Logo'></div>
														</div>
													</td>
												  </tr>
												</tbody></table></td>
											  </tr>
											</tbody></table></td>
										  </tr>
										</tbody></table></td>
									  </tr>
									</tbody></table>
									<table width='620' border='0' cellspacing='0' cellpadding='0' align='center' bgcolor='#eff2f3' style='border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#000'>
									<tbody><tr>
									<td style='padding:0px 12px 0px 12px'><table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' style='border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9'>
									  <tbody><tr>
										<td style='padding:0px 14px'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
										  <tbody><tr>
											<td><table width='100%' border='0' cellspacing='0' cellpadding='0'>
											  <tbody><tr>
												<td valign='top'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
												  <tbody>
												  <tr>
												  <td style='padding-top:5px;color:#313131; font-size:15px;'>
												  <br>
												  	<h2>Hello,</h2>
													<p>We noticed that your requested for a login credentials details. </p>
													<p>Please check below details for login.</p>
													<br>
													<p><strong>Email ID :</strong> ".$fet1['emailid']."</p>
													<p><strong>Password :</strong> ".$fet1['password']."</p>
													<br>
												  </td>                
												 </tr>
												 <tr>
													<td></td>
												 </tr>
												</tbody></table></td>
											  </tr>
											</tbody></table></td>
										  </tr>
										</tbody></table></td>
									  </tr>
									</tbody></table></td>
								  </tr>
								</tbody></table>
								<table width='620' border='0' cellspacing='0' cellpadding='0' align='center' bgcolor='#eff2f3' style='border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9;border-bottom:1px solid #d5d8d9;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#000'>
								  <tbody><tr>
									<td style='padding:0px 12px 0px 12px'><table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF'>
									  <tbody><tr>
										<td style='padding:0px 14px 0px;border-bottom:1px solid #d5d8d9;border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9'>&nbsp;</td>
									  </tr>
									  <tr>
										<td bgcolor='#eff2f3' style='font-size:11px;color:#727272;'>
										  &nbsp;
									  </tr>
									</tbody></table></td>
								  </tr>
								</tbody></table>
								<div class='yj6qo'></div>
								<div class='adL'>
								</div></div>";
								
								 $mail->Body = $mailmsg; //HTML Body
								if($mail->Send())
								{
									$_SESSION['msg'] = "done";
								} else {
									$_SESSION['msg'] = "error";
								}
								header("location:../login.php");
	}
} else {
				$fet = mysqli_fetch_array($qry);
				
				require("class.phpmailer.php");
				$mail = new PHPMailer();
				$mail->IsSMTP(); 
				$mail->Host = HOST;
				$mail->Port = PORT;
				$mail->Username = USERNAME; 
				$mail->Password = PASSWORD; 	
				$mail->SMTPAuth = true; 
				$mail->From = FROMEMAIL; 
				$mail->FromName = FROMNAME;
				$mail->AddAddress($email," ");
				$mail->IsHTML(true); 
				$mail->Subject = "Login Credentials Details";
				$mailmsg = "<div>
								<table width='620' border='0' cellspacing='0' cellpadding='0' align='center' bgcolor='#eff2f3' style='border-top:1px solid #d5d8d9;border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#000'>
									<tbody><tr>
									<td style='padding:13px 12px 0px 12px'>
									<table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' style='border-top:1px solid #d5d8d9;border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9;background:#FFFFFF;border-bottom: 1px solid #D5D8D9;'>
									  <tbody><tr>
										<td style='padding:7px'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
										  <tbody><tr>
											<td style='color:#000;padding-bottom:5px'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
											  <tbody><tr>
												<td style='line-height:33px'>&nbsp;</td>
													<td>
														<div style='width:100%'>
															<br>
															<div style='font-size:20px;font-family:arial;line-height:30px; color:#fff;'><img src='".ROOT."'/img/logo.png' width='200' title='Logo'></div>
														</div>
													</td>
												  </tr>
												</tbody></table></td>
											  </tr>
											</tbody></table></td>
										  </tr>
										</tbody></table></td>
									  </tr>
									</tbody></table>
									<table width='620' border='0' cellspacing='0' cellpadding='0' align='center' bgcolor='#eff2f3' style='border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#000'>
									<tbody><tr>
									<td style='padding:0px 12px 0px 12px'><table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' style='border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9'>
									  <tbody><tr>
										<td style='padding:0px 14px'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
										  <tbody><tr>
											<td><table width='100%' border='0' cellspacing='0' cellpadding='0'>
											  <tbody><tr>
												<td valign='top'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
												  <tbody>
												  <tr>
												  <td style='padding-top:5px;color:#313131; font-size:15px;'>
												  <br>
												  	<h2>Hello,</h2>
													<p>We noticed that your requested for a login credentials details. </p>
													<p>Please check below details for login.</p>
													<br>
													<p><strong>Email ID :</strong> ".$fet['email']."</p>
													<p><strong>Password :</strong> ".$fet['password']."</p>
													<br>
												  </td>                
												 </tr>
												 <tr>
													<td></td>
												 </tr>
												</tbody></table></td>
											  </tr>
											</tbody></table></td>
										  </tr>
										</tbody></table></td>
									  </tr>
									</tbody></table></td>
								  </tr>
								</tbody></table>
								<table width='620' border='0' cellspacing='0' cellpadding='0' align='center' bgcolor='#eff2f3' style='border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9;border-bottom:1px solid #d5d8d9;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#000'>
								  <tbody><tr>
									<td style='padding:0px 12px 0px 12px'><table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF'>
									  <tbody><tr>
										<td style='padding:0px 14px 0px;border-bottom:1px solid #d5d8d9;border-right:1px solid #d5d8d9;border-left:1px solid #d5d8d9'>&nbsp;</td>
									  </tr>
									  <tr>
										<td bgcolor='#eff2f3' style='font-size:11px;color:#727272;'>
										  &nbsp;
									  </tr>
									</tbody></table></td>
								  </tr>
								</tbody></table>
								<div class='yj6qo'></div>
								<div class='adL'>
								</div></div>";
								
								 $mail->Body = $mailmsg; //HTML Body
								if($mail->Send())
								{
									$_SESSION['msg'] = "done";
								} else {
									$_SESSION['msg'] = "error";
								}
								header("location:../login.php");
}



?>