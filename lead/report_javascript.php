<script type="text/javascript">
var count=1;
var count1;
//Add more fields dynamically.
function addField() {
	var newcount = parseInt(count)+1;
	var newadd = '<div class="form-group"><label for="firstname" class="control-label col-sm-3">Expense Name</label><div class="col-sm-6"><select name="exp_name_'+newcount+'" id="exp_name_'+newcount+'" class="select-search" onchange="check_expense_name('+newcount+',this.value)"><option value="">Select Expense Name</option><?php $ss = "select * from cp_expense_name"; $qq = mysqli_query($conn,$ss); while($ff = mysqli_fetch_array($qq)){ ?><option value="<?php echo $ff['eid']; ?>"><?php echo $ff['ename']; ?></option><?php } ?><option value="*">Other</option></select></div></div><div class="form-group other_expense" id="other_expense_'+newcount+'"><label for="firstname" class="control-label col-sm-3">Other Name</label><div class="col-sm-6"><input type="text" name="other_exp_name_'+newcount+'" id="other_exp_name_'+newcount+'" class="form-control" placeholder="Other Expense Name"></div></div><div class="form-group"><label for="firstname" class="control-label col-sm-3">Expense Amount</label><div class="col-sm-6"><input type="text" name="exp_amount_'+newcount+'" id="exp_amount_'+newcount+'" class="decimal1 decimal form-control" placeholder="Expense Amount"></div></div><div class="form-group clearfix"><label class="col-md-3 control-label " for="password">Expense On </label><div class="col-md-8 col-xs-11"><span class="icheck-inline"><input type="radio" name="exp_on_'+newcount+'" id="exp_on_'+newcount+'" value="Client" onchange="check_exp_on('+newcount+',this.value)"><label for="radio1"><span><span></span></span>Customer</label></span><span class="icheck-inline"><input type="radio" name="exp_on_'+newcount+'" id="exp_on_'+newcount+'" value="Lead" onchange="check_exp_on('+newcount+',this.value)"><label for="radio1"><span><span></span></span>Lead</label></span></div></div><div class="form-group block_exp_client" id="block_exp_client_'+newcount+'"><label for="firstname" class="control-label col-sm-3">Customer Name</label><div class="col-sm-6"><select name="exp_on_cust_'+newcount+'" id="exp_on_cust_'+newcount+'" class="select-search"><option value="">Select Customer Name</option><?php $ss = "select * from cp_customer"; $qq = mysqli_query($conn,$ss); while($ff = mysqli_fetch_array($qq)){ ?><option value="<?php echo $ff['cid']; ?>"><?php echo $ff['company']; ?></option><?php } ?></select></div></div><div class="form-group block_exp_lead" id="block_exp_lead_'+newcount+'"><label for="firstname" class="control-label col-sm-3">Lead Name</label><div class="col-sm-6"><select name="exp_on_lead_'+newcount+'" id="exp_on_lead_'+newcount+'" class="select-search"><option value="">Select Lead Name</option> <?php $ss = "select * from cp_lead"; $qq = mysqli_query($conn,$ss); while($ff = mysqli_fetch_array($qq)) { ?><option value="<?php echo $ff['lid']; ?>"><?php echo $ff['company']; ?></option><?php } ?></select></div></div><div class="form-group"><label for="firstname" class="control-label col-sm-3">Upload Receipt</label><div class="col-sm-6"><input type="file" name="ureceipt_'+newcount+'" id="ureceipt_'+newcount+'" onchange="check_upload_receipt(this.value,'+newcount+')" class="form-control"> <span class="help" id="msg_'+newcount+'"></span></div><div class="col-md-2"><div class="form-group"><label for="firstname" class="control-label">&nbsp;</label> <a href="javascript:void(0);" class="btn btn-primary  btn-xs" onClick="remove_exinstallment('+newcount+')"><i class="fa fa-times addmoreicon"></i></a> </div> </div></div><div id="expense_details_'+newcount+'"></div>';
	
	document.getElementById('expense_details_'+count).innerHTML = newadd;
	count++;
	document.getElementById('expense_details').value = count;
	
	$('#exp_name_'+newcount).select2();
	$('#exp_on_cust_'+newcount).select2();
	$('#exp_on_lead_'+newcount).select2();
	
		$('.decimal').keyup(function(){
			var val = $(this).val();
			if(isNaN(val)){
				 val = val.replace(/[^0-9\.]/g,'');
				 if(val.split('.').length>2) 
					 val =val.replace(/\.+$/,"");
			}
			$(this).val(val); 
		});
		$(".decimal1").on("change", function() {
				var val = this.value;
				if (val.match(/\./g)) { //See if it already has a dot
					if (val.replace(/\./g, "").match(/^\d+$/) !== null) { //Check if it's number after removing the dot
						this.value = parseFloat(val, 10).toFixed(2); //Yes, so format number
					} else {
						//Not a number, so reset the input value
						//Log a msg if need be: "Only numbers allowed!";
						this.value = "";
						this.focus();
					}
				} else {
					//No dot found, so, see if it's a number        
					if (val.match(/^\d+$/) !== null) {
						this.value = parseFloat(val, 10).toFixed(2); //Yes, so format number
					} else {
						//Not a number, so reset the input value
						//Log a msg if need be: "Only numbers allowed!";
						this.value = "";
						this.focus();
					}
				}
			});
}
function addexField1(count1)
{
   	var newcount = parseInt(count1)+1;
	var newexadd = '<div class="form-group"> <label for="firstname" class="control-label col-sm-3">Expense Name</label> <div class="col-sm-6"> <select name="exp_name_'+newcount+'" id="exp_name_'+newcount+'" class="select-search" onchange="check_expense_name('+newcount+',this.value)"> <option value="">Select Expense Name</option> <?php $ss="select * from cp_expense_name";$qq=mysqli_query($conn,$ss);while($ff=mysqli_fetch_array($qq)){?> <option value="<?php echo $ff['eid']; ?>"><?php echo $ff['ename']; ?></option> <?php }?> <option value="*">Other</option> </select> </div></div><div class="form-group other_expense" id="other_expense_'+newcount+'"> <label for="firstname" class="control-label col-sm-3">Other Name</label> <div class="col-sm-6"> <input type="text" name="other_exp_name_'+newcount+'" id="other_exp_name_'+newcount+'" class="form-control" placeholder="Other Expense Name"> </div></div><div class="form-group"> <label for="firstname" class="control-label col-sm-3">Expense Amount</label> <div class="col-sm-6"> <input type="text" name="exp_amount_'+newcount+'" id="exp_amount_'+newcount+'" class="decimal1 decimal form-control" placeholder="Expense Amount"> </div></div><div class="form-group clearfix"> <label class="col-md-3 control-label " for="password">Expense On </label> <div class="col-md-8 col-xs-11"> <span class="icheck-inline"> <input type="radio" name="exp_on_'+newcount+'" id="exp_on_'+newcount+'" value="Client" onchange="check_exp_on('+newcount+',this.value)"> <label for="radio1"><span><span></span></span>Customer</label> </span> <span class="icheck-inline"> <input type="radio" name="exp_on_'+newcount+'" id="exp_on_'+newcount+'" value="Lead" onchange="check_exp_on('+newcount+',this.value)"> <label for="radio1"><span><span></span></span>Lead</label> </span> </div></div><div class="form-group block_exp_client" id="block_exp_client_'+newcount+'"> <label for="firstname" class="control-label col-sm-3">Customer Name</label> <div class="col-sm-6"> <select name="exp_on_cust_'+newcount+'" id="exp_on_cust_'+newcount+'" class="select-search"> <option value="">Select Customer Name</option> <?php $ss="select * from cp_customer";$qq=mysqli_query($conn,$ss);while($ff=mysqli_fetch_array($qq)){?> <option value="<?php echo $ff['cid']; ?>"><?php echo $ff['company']; ?></option> <?php }?> </select> </div></div><div class="form-group block_exp_lead" id="block_exp_lead_'+newcount+'"> <label for="firstname" class="control-label col-sm-3">Lead Name</label> <div class="col-sm-6"> <select name="exp_on_lead_'+newcount+'" id="exp_on_lead_'+newcount+'" class="select-search"> <option value="">Select Lead Name</option> <?php $ss="select * from cp_lead";$qq=mysqli_query($conn,$ss);while($ff=mysqli_fetch_array($qq)){?> <option value="<?php echo $ff['lid']; ?>"><?php echo $ff['company']; ?></option> <?php }?> </select> </div></div><div class="form-group"> <label for="firstname" class="control-label col-sm-3">Upload Receipt</label> <div class="col-sm-6"> <input type="file" name="ureceipt_'+newcount+'" id="ureceipt_'+newcount+'" onchange="check_upload_receipt(this.value,'+newcount+')" class="form-control"> <span class="help" id="msg_'+newcount+'"></span> </div><div class="col-md-2"> <div class="form-group"> <label for="firstname" class="control-label">&nbsp;</label> <a href="javascript:void(0);" class="btn btn-primary btn-xs" onClick="remove_exinstallment('+newcount+')"><i class="fa fa-times addmoreicon"></i></a> </div></div></div><div id="expense_details_'+newcount+'"></div>';
	
	document.getElementById('expense_details_'+count1).innerHTML = newexadd;
	count1++;
	document.getElementById('expense_details').value = count1;
	$('#exp_name_'+newcount).select2();
	$('#exp_on_cust_'+newcount).select2();
	$('#exp_on_lead_'+newcount).select2();
	
	    $('.decimal').keyup(function(){
			var val = $(this).val();
			if(isNaN(val)){
				 val = val.replace(/[^0-9\.]/g,'');
				 if(val.split('.').length>2) 
					 val =val.replace(/\.+$/,"");
			}
			$(this).val(val); 
		});
		$(".decimal1").on("change", function() {
				var val = this.value;
				if (val.match(/\./g)) { //See if it already has a dot
					if (val.replace(/\./g, "").match(/^\d+$/) !== null) { //Check if it's number after removing the dot
						this.value = parseFloat(val, 10).toFixed(2); //Yes, so format number
					} else {
						//Not a number, so reset the input value
						//Log a msg if need be: "Only numbers allowed!";
						this.value = "";
						this.focus();
					}
				} else {
					//No dot found, so, see if it's a number        
					if (val.match(/^\d+$/) !== null) {
						this.value = parseFloat(val, 10).toFixed(2); //Yes, so format number
					} else {
						//Not a number, so reset the input value
						//Log a msg if need be: "Only numbers allowed!";
						this.value = "";
						this.focus();
					}
				}
			});
}
function remove_complain_attach(str,iid)
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
			document.getElementById("product_details_"+str).style.display = "none";
		}
	}
												
	xmlhttp.open("GET","remove_complain_attach.php?iid="+iid,true);
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
function remove_expense_1(str,str1,exid)
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
			document.getElementById("eid_"+str1).value = ""; 
			document.getElementById("exp_name_"+str1).value = ""; 
			document.getElementById("other_exp_name_"+str1).value = ""; 
		  	document.getElementById("exp_amount_"+str1).value = ""; 
		    document.getElementById("exp_on_"+str1).value = "";
		    document.getElementById("exp_on_lead_"+str1).value = ""; 
			document.getElementById("exp_on_cust_"+str1).value = ""; 
			document.getElementById("ureceipt_"+str1).value = ""; 
			document.getElementById("oldureceipt_"+str1).value = ""; 
			document.getElementById("expense_details_"+str).style.display = "none";
			
		}
	}
												
	xmlhttp.open("GET","remove_report_expense.php?exid="+exid,true);
	xmlhttp.send(null);
}




function remove_exinstallment(eremoveid)
{
	
	
	
	count = parseInt(eremoveid)-1;
	if(count == 1)
	{
     	document.getElementById("expense_details_"+count).innerHTML = "";
		document.getElementById('expense_details').value = count;
	} else {
		document.getElementById("expense_details_"+count).innerHTML = "";
		document.getElementById('expense_details').value = count;
	}
	
	
	
}


</script>