var xmlhttp;
function check_login()
{
 var email=$("#login_username").val();
 var pass=$("#login_password").val();
 if(email!="" && pass!="")
 {
  $("#loading_spinner").css({"display":"block"});
  $.ajax
  ({
  type:'post',
  url:'do_login.php',
  data:{
   do_login:"do_login",
   login_username:email,
   login_password:pass
  },
  success:function(response) {
  if(response=="success")
  {
    window.location.href="index.php?pid=home";
  }
  else
  {
    $("#loading_spinner").css({"display":"none"});
	$('#fillb').show();
    $('#fillb').text("Wrong Details");
  }
  }
  });
 }

 else
 {
 	 $('#fillb').show();
	 $('#fillb').text("Please Fill All The Details");
 }

 return false;
}
function check_other_department(str)
{
	if(str == "Others")
	{
		document.getElementById("other_department").style.display = "block";
	} else {
		document.getElementById("other_department").style.display = "none";
	}
}
function check_forgot()
{
 	
 	var error = 0;
	
	if(!($("#email").val())) {
		error = 1;
		$('#email').css('border','1px solid red');
		$('#msg4').text('Email ID is required.');
	} else if(!$("#email").val().match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
		error = 1;
		$('#email').css('border','1px solid red');
		$('#msg4').text('Input valid email address');
	} else {
		$('#email').css('border','1px solid #ccc');
		$('#msg4').text('');
	}
	if(error == 1)return false; else return true;
}

function GetDays()
 {

     var date1 = document.getElementById("end_date").value;
	 var date2 = document.getElementById("start_date").value;
	 if(date1 != "" && date2 != "")
	 {
		var a = moment(date2, 'DD/MM/YYYY');
		var b = moment(date1, 'DD/MM/YYYY');
		var days = b.diff(a, 'days');
		return days;
		//return parseInt((date1 - date2) / (24 * 3600 * 1000));
	 } else { 
		return "";
	 }
}

function check_up_profile()
{
	var error = 0;
	
	var fname = $('#fname').val();
	if(fname == "")
	{
		error = 1;
		$('#fname').css('border','1px solid red');
		$('#msg1').text('First name is required.');
    } else {
		$('#fname').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var lname = $('#lname').val();
	if(lname == "")
	{
		error = 1;
		$('#lname').css('border','1px solid red');
		$('#msg2').text('Last name is required.');
    } else {
		$('#lname').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	var design = $('#design').val();
	if(design == "")
	{
		error = 1;
		$('#design').css('border','1px solid red');
		$('#msg3').text('Designation is required.');
    } else {
		$('#design').css('border','1px solid #ccc');
		$('#msg3').text('');
	}
	var mobile = $('#mobile').val();
	if(mobile == "")
	{
		error = 1;
		$('#mobile').css('border','1px solid red');
		$('#msg5').text('Mobile no is required.');
	} else if(!mobile.match(/^\d{10}$/)) {
		error = 1;
		$('#mobile').css('border','1px solid red');
		$('#msg5').text('Input valid mobile number');
	} else {
		$('#mobile').css('border','1px solid #ccc');
		$('#msg5').text('');
	}
	if(!($("#emailid").val())) {
		error = 1;
		$('#emailid').css('border','1px solid red');
		$('#msg4').text('Email ID is required.');
	} else if(!$("#emailid").val().match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
		error = 1;
		$('#emailid').css('border','1px solid red');
		$('#msg4').text('Input valid email address');
	} else {
		$('#emailid').css('border','1px solid #ccc');
		$('#msg4').text('');
	}
	var photo = $('#photo').val();
	if(photo != "")
	{
		var validExtensions = ['jpg','png','jpeg']; //array of valid extensions
		var fileNameExt = photo.substr(photo.lastIndexOf('.') + 1);
		if($.inArray(fileNameExt, validExtensions) == -1) 
		{
            error = 1;
			$('#photo').css('border','1px solid red');	
			$('#msg6').text("Only these file types are accepted : "+validExtensions.join(', '));
        } else {
			$('#photo').css('border','1px solid #ccc');
			$('#msg6').text('');
		}
	}
	
	if(error == 1)return false; else return true;
}
function check_up_profile1()
{
	var error = 0;
	
	var fname = $('#fname').val();
	if(fname == "")
	{
		error = 1;
		$('#fname').css('border','1px solid red');
		$('#msg1').text('First name is required.');
    } else {
		$('#fname').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var lname = $('#lname').val();
	if(lname == "")
	{
		error = 1;
		$('#lname').css('border','1px solid red');
		$('#msg2').text('Last name is required.');
    } else {
		$('#lname').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	var design = $('#design').val();
	if(design == "")
	{
		error = 1;
		$('#design').css('border','1px solid red');
		$('#msg3').text('Designation is required.');
    } else {
		$('#design').css('border','1px solid #ccc');
		$('#msg3').text('');
	}
	var phoneno = $('#phoneno').val();
	if(phoneno == "")
	{
		error = 1;
		$('#phoneno').css('border','1px solid red');
		$('#msg4').text('Mobile no is required.');
	} else if(!phoneno.match(/^\d{10}$/)) {
		error = 1;
		$('#phoneno').css('border','1px solid red');
		$('#msg4').text('Input valid mobile number');
	} else {
		$('#phoneno').css('border','1px solid #ccc');
		$('#msg4').text('');
	}
	if(!($("#emailid").val())) {
		error = 1;
		$('#emailid').css('border','1px solid red');
		$('#msg5').text('Email ID is required.');
	} else if(!$("#emailid").val().match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
		error = 1;
		$('#emailid').css('border','1px solid red');
		$('#msg5').text('Input valid email address');
	} else {
		$('#emailid').css('border','1px solid #ccc');
		$('#msg5').text('');
	}
	var photo = $('#photo').val();
	if(photo != "")
	{
		var validExtensions = ['jpg','png','jpeg']; //array of valid extensions
		var fileNameExt = photo.substr(photo.lastIndexOf('.') + 1);
		if($.inArray(fileNameExt, validExtensions) == -1) 
		{
            error = 1;
			$('#photo').css('border','1px solid red');	
			$('#msg6').text("Only these file types are accepted : "+validExtensions.join(', '));
        } else {
			$('#photo').css('border','1px solid #ccc');
			$('#msg6').text('');
		}
	}
	
	if(error == 1)return false; else return true;
}
function change_pass()
{
	var error = 0;
	
	var old_pass = $('#old_pass').val();
	if(old_pass == "")
	{
		error = 1;
		$('#old_pass').css('border','1px solid red');
		$('#msg11').text('Old password is required.');
	} else {
		$('#old_pass').css('border','1px solid #ccc');
		$('#msg11').text('');
	}
	var new_pass = $('#new_pass').val();
	if(new_pass == "")
	{
		error = 1;
		$('#new_pass').css('border','1px solid red');
		$('#msg12').text('New password is required.');
	} else {
		$('#new_pass').css('border','1px solid #ccc');
		$('#msg12').text('');
	}
	var con_pass = $('#con_pass').val();
	if(con_pass == "")
	{
		error = 1;
		$('#con_pass').css('border','1px solid red');
		$('#msg13').text('Confirm password is required.');
	} else {
		if(new_pass != con_pass)
		{
			error = 1;
			$('#con_pass').css('border','1px solid red');
			$('#msg13').text('New and Confirm password not match.');
		} else {
			$('#con_pass').css('border','1px solid #ccc');
			$('#msg13').text('');
		}
	}
	
	if(error == 1)return false; else return true;
}
function add_region()
{
	var error = 0;
	
	var rname = $('#rname').val();
	if(rname == "")
	{
		error = 1;
		$('#rname').css('border','1px solid red');
		$('#msg1').text('Region name is required.');
	} else {
		$('#rname').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	
	if(error == 1)return false; else return true;
}
function add_material()
{
	var error = 0;
	
	var mname = $('#mname').val();
	if(mname == "")
	{
		error = 1;
		$('#mname').css('border','1px solid red');
		$('#msg1').text('Material name is required.');
	} else {
		$('#mname').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	
	if(error == 1)return false; else return true;
}
function add_member()
{
	var error = 0;
	
	var fname = $('#fname').val();
	if(fname == "")
	{
		error = 1;
		$('#fname').css('border','1px solid red');
		$('#msg1').text('First name is required.');
	} else {
		$('#fname').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var lname = $('#lname').val();
	if(lname == "")
	{
		error = 1;
		$('#lname').css('border','1px solid red');
		$('#msg2').text('Last name is required.');
	} else {
		$('#lname').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	var phoneno = $('#phoneno').val();
	if(phoneno == "")
	{
		error = 1;
		$('#phoneno').css('border','1px solid red');
		$('#msg3').text('Mobile no is required.');
	} else if(!phoneno.match(/^\d{10}$/)) {
		error = 1;
		$('#phoneno').css('border','1px solid red');
		$('#msg3').text('Input valid mobile number');
	} else {
		$('#phoneno').css('border','1px solid #ccc');
		$('#msg3').text('');
	}
	if(!($("#emailid").val())) {
		error = 1;
		$('#emailid').css('border','1px solid red');
		$('#msg4').text('Email ID is required.');
	} else if(!$("#emailid").val().match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
		error = 1;
		$('#emailid').css('border','1px solid red');
		$('#msg4').text('Input valid email address');
	} else {
		$('#emailid').css('border','1px solid #ccc');
		$('#msg4').text('');
	}
	var photo = $('#photo').val();
	if(photo != "")
	{
		var validExtensions = ['jpg','png','jpeg']; //array of valid extensions
		var fileNameExt = photo.substr(photo.lastIndexOf('.') + 1);
		if($.inArray(fileNameExt, validExtensions) == -1) 
		{
            error = 1;
			$('#photo').css('border','1px solid red');	
			$('#msg8').text("Only these file types are accepted : "+validExtensions.join(', '));
        } else {
			$('#photo').css('border','1px solid #ccc');
			$('#msg8').text('');
		}
	}
	var password = $('#password').val();
	if(password == "")
	{
		error = 1;
		$('#password').css('border','1px solid red');
		$('#msg5').text('Password is required.');
	} else {
		$('#password').css('border','1px solid #ccc');
		$('#msg5').text('');
	}
	var cpassword = $('#cpassword').val();
	if(cpassword == "")
	{
		error = 1;
		$('#cpassword').css('border','1px solid red');
		$('#msg6').text('Confirm password is required.');
	} else {
		if(password != cpassword)
		{
			error = 1;
			$('#cpassword').css('border','1px solid red');
			$('#msg6').text('Password and confirm password not match.');
		} else {
			$('#cpassword').css('border','1px solid #ccc');
			$('#msg6').text('');
		}
	}
	var utype = $('#utype').val();
	if(utype == "")
	{
		error = 1;
		$('#utype').css('border','1px solid red');
		$('#msg7').text('User role is required.');
	} else {
		$('#utype').css('border','1px solid #ccc');
		$('#msg7').text('');
	}
	
	
	if(error == 1)return false; else return true;
}
function add_setting()
{
	var error = 0;
	
	var cphone = $('#cphone').val();
	if(cphone == "")
	{
		error = 1;
		$('#cphone').css('border','1px solid red');
		$('#msg1').text('Phone no is required.');
	} else if(!cphone.match(/^\d{3}(?:\s|-)*\d{7}$/)) {
		error = 1;
		$('#cphone').css('border','1px solid red');
		$('#msg1').text('Input valid phone number');
	} else {
		$('#cphone').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var cmobile = $('#cmobile').val();
	if(cmobile == "")
	{
		error = 1;
		$('#cmobile').css('border','1px solid red');
		$('#msg2').text('Mobile no is required.');
	} else if(!cmobile.match(/^\d{10}$/)) {
		error = 1;
		$('#cmobile').css('border','1px solid red');
		$('#msg2').text('Input valid mobile number');
	} else {
		$('#cmobile').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	if(!($("#cemailid").val())) {
		error = 1;
		$('#cemailid').css('border','1px solid red');
		$('#msg3').text('Email ID is required.');
	} else if(!$("#cemailid").val().match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
		error = 1;
		$('#cemailid').css('border','1px solid red');
		$('#msg3').text('Input valid email address');
	} else {
		$('#cemailid').css('border','1px solid #ccc');
		$('#msg3').text('');
	}
	var clogo = $('#clogo').val();
	if(clogo != "")
	{
		var validExtensions = ['jpg','png','jpeg']; //array of valid extensions
		var fileNameExt = clogo.substr(clogo.lastIndexOf('.') + 1);
		if($.inArray(fileNameExt, validExtensions) == -1) 
		{
            error = 1;
			$('#clogo').css('border','1px solid red');	
			$('#msg4').text("Only these file types are accepted : "+validExtensions.join(', '));
        } else {
			$('#clogo').css('border','1px solid #ccc');
			$('#msg4').text('');
		}
	}
	
	if(error == 1)return false; else return true;
}
function checkAvailability_emailid_user(str,n)
{
	jQuery.ajax({
		url: "checkAvailability_emailid_user.php",
		data:'str='+str,
		type: "POST",
		success:function(data)
		{
			if(data == 1)
			{
				document.getElementById("submit_btn").disabled = true;
				$("#msg4").html("Email ID already exists in System. use different.");
			} else {
				document.getElementById("submit_btn").disabled = false;
				$("#msg4").html("");
			}
		},
	error:function (){}
	});
	
}
function checkAvailability_rname(str)
{
	jQuery.ajax({
		url: "checkAvailability_rname.php",
		data:'str='+str,
		type: "POST",
		success:function(data)
		{
			if(data == 1)
			{
				document.getElementById("finish").disabled = true;
				$("#msg1").html("Region name already exists in System. use different.");
			} else {
				document.getElementById("finish").disabled = false;
				$("#msg1").html("");
			}
		},
	error:function (){}
	});
}
function check_upload_receipt(str,n)
{
	var receipt = $('#ureceipt_'+n).val();
	if(receipt != "")
	{
		var validExtensions = ['jpg','png','jpeg','pdf']; //array of valid extensions
		var fileNameExt = receipt.substr(receipt.lastIndexOf('.') + 1);
		if($.inArray(fileNameExt, validExtensions) == -1) 
		{
            error = 1;
			$('#ureceipt_'+n).css('border','1px solid red');	
			$('#msg_'+n).text("Only these file types are accepted : "+validExtensions.join(', '));
			document.getElementById("submit_btn").disabled = true;
        } else {
			$('#ureceipt_'+n).css('border','1px solid #ccc');
			$('#msg_'+n).text('');
			document.getElementById("submit_btn").disabled = false;
		}
	}
}
function check_type(str)
{
	if(str == 4)
	{ 
		document.getElementById("raccess").style.display = "block"; 
	} else { 
		document.getElementById("raccess").style.display = "none"; 
	}
}
function check_ref(str)
{
	if(str == "Reference")
	{
		document.getElementById("ref_id").style.display = "block";
	} else { 
		document.getElementById("ref_id").style.display = "none";
	}
	if(str == "other")
	{
		document.getElementById("other_id").style.display = "block";
	} else { 
		document.getElementById("other_id").style.display = "none";
	}
}
function check_exp_on(cid,str)
{
	if(str == "Client")
	{
		document.getElementById("block_exp_client_"+cid).style.display = "block";
		document.getElementById("block_exp_lead_"+cid).style.display = "none";
	} else {
		document.getElementById("block_exp_client_"+cid).style.display = "none";
		document.getElementById("block_exp_lead_"+cid).style.display = "block";
	}
}
function check_expense_name(cid,str)
{
	if(str == "*")
	{
		document.getElementById("other_expense_"+cid).style.display = "block";
	} else {
		document.getElementById("other_expense_"+cid).style.display = "none";
	}
}
function submit_report()
{
	var error = 0;
	
	var no_of_visit = $('#no_of_visit').val();
	if(no_of_visit == "")
	{
		error = 1;
		$('#no_of_visit').css('border','1px solid red');
		$('#msg1').text('No of visit is required.');
	} else {
		$('#no_of_visit').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	
	if(error == 1)return false; else return true;
}
function add_product()
{
	var error = 0;
	
	var pname = $('#pname').val();
	if(pname == "")
	{
		error = 1;
		$('#pname').css('border','1px solid red');
		$('#msg1').text('Product name is required.');
	} else {
		$('#pname').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var pprice = $('#pprice').val();
	if(pprice == "")
	{
		error = 1;
		$('#pprice').css('border','1px solid red');
		$('#msg2').text('Product price is required.');
	} else {
		$('#pprice').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	var punit = $('#punit').val();
	if(punit == "")
	{
		error = 1;
		$('#punit').css('border','1px solid red');
		$('#msg3').text('Measurement unit is required.');
	} else {
		$('#punit').css('border','1px solid #ccc');
		$('#msg3').text('');
	}
	
	if(error == 1)return false; else return true;
}
function add_lead()
{
	var error = 0;
	
	var cperson = $('#cperson').val();
	if(cperson == "")
	{
		error = 1;
		$('#cperson').css('border','1px solid red');
		$('#msg1').text('Contact person is required.');
	} else {
		$('#cperson').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var mobileno1 = $('#mobileno1').val();
	if(mobileno1 == "")
	{
		error = 1;
		$('#mobileno1').css('border','1px solid red');
		$('#msg2').text('Mobile no is required.');
	} else if(!mobileno1.match(/^\d{10}$/)) {
		error = 1;
		$('#mobileno1').css('border','1px solid red');
		$('#msg2').text('Input valid mobile number');
	} else {
		$('#mobileno1').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	
	var mobileno2 = $('#mobileno2').val();
	if(mobileno2 != "")
	{
		if(!mobileno2.match(/^\d{10}$/)) {
			error = 1;
			$('#mobileno2').css('border','1px solid red');
			$('#msg2_1').text('Input valid mobile number');
		} else {
			$('#mobileno2').css('border','1px solid #ccc');
			$('#msg2_1').text('');
		}
	}
	var emailid = $('#emailid').val();
	if(emailid != "") 
	{
	   if(!emailid.match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
			error = 1;
			$('#email').css('border','1px solid red');
			$('#msg3').text('Input valid email address');
   	   } else {
			$('#email').css('border','1px solid #ccc');
			$('#msg3').text('');
		}
	}
	var ftype = $('#ftype').val();
	if(ftype == "") 
	{
	   	error = 1;
		$('#ftype').css('border','1px solid red');
		$('#msg4').text('Followup type is required.');
   } else {
		$('#ftype').css('border','1px solid #ccc');
		$('#msg4').text('');
	}
	
	if(error == 1)return false; else return true;
}
function add_followup()
{
	var error = 0;
	
	var ftype = $('#ftype').val();
	if(ftype == "")
	{
		error = 1;
		$('#ftype').css('border','1px solid red');
		$('#msg1').text('Followup type is required.');
	} else {
		$('#ftype').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var fdate = $('#fdate').val();
	if(fdate == "")
	{
		error = 1;
		$('#fdate').css('border','1px solid red');
		$('#msg2').text('Followup date is required.');
	} else {
		$('#fdate').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	var ftime = $('#ftime').val();
	if(ftime == "") 
	{
	   	error = 1;
		$('#ftime').css('border','1px solid red');
		$('#msg3').text('Followup time is required.');
    } else {
		$('#ftime').css('border','1px solid #ccc');
		$('#msg3').text('');
	}
	var nfdate = $('#nfdate').val();
	if(nfdate == "") 
	{
	   	error = 1;
		$('#nfdate').css('border','1px solid red');
		$('#msg4').text('Next Followup date is required.');
    } else {
		$('#nfdate').css('border','1px solid #ccc');
		$('#msg4').text('');
	}
	var nftime = $('#nftime').val();
	if(nftime == "") 
	{
	   	error = 1;
		$('#nftime').css('border','1px solid red');
		$('#msg5').text('Next Followup time is required.');
    } else {
		$('#nftime').css('border','1px solid #ccc');
		$('#msg5').text('');
	}
	
	if(error == 1)return false; else return true;
}
function add_customer()
{
	var error = 0;
	
	var cperson = $('#cperson').val();
	if(cperson == "")
	{
		error = 1;
		$('#cperson').css('border','1px solid red');
		$('#msg1').text('Contact person is required.');
	} else {
		$('#cperson').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var mobileno1 = $('#mobileno1').val();
	if(mobileno1 == "")
	{
		error = 1;
		$('#mobileno1').css('border','1px solid red');
		$('#msg2').text('Mobile no is required.');
	} else if(!mobileno1.match(/^\d{10}$/)) {
		error = 1;
		$('#mobileno1').css('border','1px solid red');
		$('#msg2').text('Input valid mobile number');
	} else {
		$('#mobileno1').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	var mobileno2 = $('#mobileno2').val();
	if(mobileno2 != "")
	{
		 if(!mobileno2.match(/^\d{10}$/)) {
			error = 1;
			$('#mobileno2').css('border','1px solid red');
			$('#msg2_1').text('Input valid mobile number');
		} else {
			$('#mobileno2').css('border','1px solid #ccc');
			$('#msg2_1').text('');
		}
	}
	var emailid = $('#emailid').val();
	if(emailid != "") 
	{
	   if(!emailid.match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
			error = 1;
			$('#email').css('border','1px solid red');
			$('#msg3').text('Input valid email address');
   	   } else {
			$('#email').css('border','1px solid #ccc');
			$('#msg3').text('');
		}
	}
	
	if(error == 1)return false; else return true;
}
function check_checkout()
{
	var error = 0;
	
	var customer = $('#customer').val();
	if(customer == "")
	{
		error = 1;
		$('#customer').css('border','1px solid red');
		$('#msg1').text('Customer name is required.');
	} else {
		$('#customer').css('border','1px solid #ccc');
		$('#msg1').text('');
	}

	if(error == 1)return false; else return true;
}
function add_goal()
{
	var error = 0;
	
	var fdate = $('#fdate').val();
	if(fdate == "")
	{
		error = 1;
		$('#fdate').css('border','1px solid red');
		$('#msg1').text('From date is required.');
	} else {
		$('#fdate').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var tdate = $('#tdate').val();
	if(tdate == "")
	{
		error = 1;
		$('#tdate').css('border','1px solid red');
		$('#msg2').text('To date is required.');
	} else {
		$('#tdate').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	var exname = $('#exname').val();
	if(exname == "")
	{
		error = 1;
		$('#exname').css('border','1px solid red');
		$('#msg3').text('Username is required.');
	} else {
		$('#exname').css('border','1px solid #ccc');
		$('#msg3').text('');
	}
	var gamount = $('#gamount').val();
	if(gamount == "")
	{
		error = 1;
		$('#gamount').css('border','1px solid red');
		$('#msg4').text('Goal amount is required.');
	} else {
		$('#gamount').css('border','1px solid #ccc');
		$('#msg4').text('');
	}

	if(error == 1)return false; else return true;
}
function add_bank_account()
{
	var error = 0;
	var aname = $('#aname').val();
	if(aname == "")
	{
		error = 1;
		$('#aname').css('border','1px solid red');
		$('#msg1').text('Account name is required.');
	} else {
		$('#aname').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var ibalanace = $('#ibalanace').val();
	if(ibalanace == "")
	{
		error = 1;
		$('#ibalanace').css('border','1px solid red');
		$('#msg2').text('Initial Balance is required.');
	} else {
		$('#ibalanace').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	

	if(error == 1)return false; else return true;
}
function add_deposit()
{
	var error = 0;
	var aname = $('#aname').val();
	if(aname == "")
	{
		error = 1;
		$('#aname').css('border','1px solid red');
		$('#msg1').text('Account name is required.');
	} else {
		$('#aname').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	var subject = $('#subject').val();
	if(subject == "")
	{
		error = 1;
		$('#subject').css('border','1px solid red');
		$('#msg2').text('Subject is required.');
	} else {
		$('#subject').css('border','1px solid #ccc');
		$('#msg2').text('');
	}
	var damount = $('#damount').val();
	if(damount == "")
	{
		error = 1;
		$('#damount').css('border','1px solid red');
		$('#msg3').text('Amount is required.');
	} else {
		$('#damount').css('border','1px solid #ccc');
		$('#msg3').text('');
	}
	

	if(error == 1)return false; else return true;
}
function add_notice()
{
	var error = 0;
	var subject = $('#subject').val();
	if(subject == "")
	{
		error = 1;
		$('#subject').css('border','1px solid red');
		$('#msg1').text('Subject is required.');
	} else {
		$('#subject').css('border','1px solid #ccc');
		$('#msg1').text('');
	}
	
	if(error == 1)return false; else return true;
}
function remove_product_image(str,rid)
{
	xmlhttp=GetXmlHttpObject();
	if(xmlhttp==null)
	{
		alert("Your browser does not support AJAX!");
		return;
	}
	xmlhttp.onreadystatechange=function()
	{
		if(xmlhttp.readyState==4)
		{
			var s = xmlhttp.responseText;
			document.getElementById("img"+str).style.display = "none";
		}
	}
												
	xmlhttp.open("GET","remove_product_image.php?rid="+rid,true);
	xmlhttp.send(null);
}
function remove_product_file(str,rid)
{
	xmlhttp=GetXmlHttpObject();
	if(xmlhttp==null)
	{
		alert("Your browser does not support AJAX!");
		return;
	}
	xmlhttp.onreadystatechange=function()
	{
		if(xmlhttp.readyState==4)
		{
			var s = xmlhttp.responseText;
			document.getElementById("file"+str).style.display = "none";
		}
	}
												
	xmlhttp.open("GET","remove_product_file.php?rid="+rid,true);
	xmlhttp.send(null);
}
function remove_lead_file(str,rid)
{
	xmlhttp=GetXmlHttpObject();
	if(xmlhttp==null)
	{
		alert("Your browser does not support AJAX!");
		return;
	}
	xmlhttp.onreadystatechange=function()
	{
		if(xmlhttp.readyState==4)
		{
			var s = xmlhttp.responseText;
			document.getElementById("file"+str).style.display = "none";
		}
	}
												
	xmlhttp.open("GET","remove_lead_file.php?rid="+rid,true);
	xmlhttp.send(null);
}
function GetXmlHttpObject()
{
	if(window.XMLHttpRequest)
	{
		return new XMLHttpRequest();
	}
	if(window.ActiveXObject)
	{
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
		return null;
}

