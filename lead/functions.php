<?php
	function get_parent_menu($mid)
	{
		global $conn;
		$sel = "select mname from cp_menu where mid='".$mid."'";
		$qry = mysqli_query($conn,$sel);
		if(mysqli_num_rows($qry)>0)
		{
			$fet = mysqli_fetch_array($qry);
			return $fet['mname'];
		} else {
			return "Root";
		}
	}
	function get_role($uname)
	{
		global $conn;
		$sel = "select rid from cp_role where uname='".$uname."'";
		$qry = mysqli_query($conn,$sel);
		if(mysqli_num_rows($qry)>0)
		{
			return "Yes";
		} else {
			return "No";
		}
	}
	function get_access($mtitle)
	{
		global $conn;
		$uname = $_SESSION['admin_id'];
		$sel = "select rstatus from cp_role where uname='".$uname."' and rname='".$mtitle."'";
		$qry = mysqli_query($conn,$sel);
		$fet = mysqli_fetch_array($qry);
		return $fet['rstatus'];
	}
	
	function get_exname_photo($uid)
	{
		global $conn;
		if($uid == "")
		{
			return "";
		} else {
			$result=mysqli_query($conn,"select photo from cp_users where emailid='$uid'");
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_array($result);
				return $row['photo'];
			} else {
				$sel = mysqli_query($conn,"select photo from cp_login where email='$uid'");
				$fet = mysqli_fetch_array($sel);
				return $fet['photo'];
			}
		}
	}
	function get_exname($uid)
	{
		global $conn;
		if($uid == "")
		{
			return "";
		} else {
			$result=mysqli_query($conn,"select fname,lname from cp_users where emailid='$uid'");
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_array($result);
				return $row['fname']." ".$row['lname'];
			} else {
				$sel = mysqli_query($conn,"select fname,lname from cp_login where email='$uid'");
				$fet = mysqli_fetch_array($sel);
				return $fet['fname']." ".$fet['lname'];
			}
		}
	}
	function get_exname_phone($uid)
	{
		global $conn;
		if($uid == "")
		{
			return "";
		} else {
			$result=mysqli_query($conn,"select phoneno from cp_users where emailid='$uid'");
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_array($result);
				return $row['phoneno'];
			} else {
				$sel = mysqli_query($conn,"select mobile from cp_login where email='$uid'");
				$fet = mysqli_fetch_array($sel);
				return $fet['mobile'];
			}
		}
	}
	function get_lead_code()
	{
		global $conn;
		$s = "";
		$uid = $_SESSION['admin_id'];
		if($uid == "")
		{
			$s = "";
		} else {
			$result=mysqli_query($conn,"select empcode from cp_users where emailid='$uid'");
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_array($result);
				$s = $row['empcode'];
			} else {
				$s = "AD";
			}
		}
		return $s."-".date('Y',time())."-".date('m',time());
	}
	function get_lead_code1($uid)
	{
		global $conn;
		$s = "";
		if($uid == "")
		{
			$s = "";
		} else {
			$result=mysqli_query($conn,"select empcode from cp_users where emailid='$uid'");
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_array($result);
				$s = $row['empcode'];
			} else {
				$s = "AD";
			}
		}
		return $s."-".date('Y',time())."-".date('m',time());
	}
	function get_moniby($emailid)
	{
		global $conn;
		$result = mysqli_query($conn,"select scoordinator from cp_users where emailid='$emailid'");
		$row = mysqli_fetch_array($result);
		if(mysqli_num_rows($result)>0)
		{
			$user = $row['scoordinator'];
		} else {
			$user = "";
		}
		return $user;	
	}
	function get_username($email)
	{
		global $conn;
		$result = mysqli_query($conn,"select fname,lname from cp_login where email='$email'");
		$row = mysqli_fetch_array($result);
		$user = substr($row['fname'],0,1)."".$row['lname'].""."A";
		return $user;
	}
	function get_vehical_name($cid)
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_car_type where cid='$cid'");
		$row = mysqli_fetch_array($result);
		return $row['ctype'];
	}
	function get_company_name()
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_setting where sname='cname'");
		$row = mysqli_fetch_array($result);
		return $row['svalue'];
	}
	function get_company_logo()
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_setting where sname='clogo'");
		$row = mysqli_fetch_array($result) or die(mysqli_error($conn));
		return $row['svalue'];
	}
	function get_company_address()
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_setting where sname='caddress'");
		$row = mysqli_fetch_array($result);
		return $row['svalue'];
	}
	function get_company_emailid()
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_setting where sname='cemailid'");
		$row = mysqli_fetch_array($result);
		return $row['svalue'];
	}
	function get_company_phone()
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_setting where sname='cphone'");
		$row = mysqli_fetch_array($result);
		return $row['svalue'];
	}
	function get_company_mobile()
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_setting where sname='cmobile'");
		$row = mysqli_fetch_array($result);
		return $row['svalue'];
	}
	function get_company_dob_templete()
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_setting where sname='dob'");
		$row = mysqli_fetch_array($result);
		return $row['svalue'];
	}
	function get_company_doa_templete()
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_setting where sname='doa'");
		$row = mysqli_fetch_array($result);
		return $row['svalue'];
	}
	function get_company_inquiry_sms_templete()
	{
		global $conn;
		$result = mysqli_query($conn,"select * from cp_setting where sname='inquiry_sms'");
		$row = mysqli_fetch_array($result);
		return $row['svalue'];
	}
	function get_total_expense_report($rid)
	{
		global $conn;
		$sel = "select * from cp_report_expense where rid='$rid'";
		$qry = mysqli_query($conn,$sel);
		$count = 0;
		while($fet = mysqli_fetch_array($qry))
		{
			$count = $count + $fet['exp_amount'];
		}
		return $count;
	}
	function get_total_expense_paid_report($rid)
	{
		global $conn;
		$sel = "select * from cp_report_expense where rid='$rid' and status='1'";
		$qry = mysqli_query($conn,$sel);
		$count = 0;
		while($fet = mysqli_fetch_array($qry))
		{
			$count = $count + $fet['exp_amount'];
		}
		return $count;
	}
	function get_total_expense_unpaid_report($rid)
	{
		global $conn;
		$sel = "select * from cp_report_expense where rid='$rid' and status='0'";
		$qry = mysqli_query($conn,$sel);
		$count = 0;
		while($fet = mysqli_fetch_array($qry))
		{
			$count = $count + $fet['exp_amount'];
		}
		return $count;
	}
	function get_expense_name($eid)
	{
		global $conn;
		$sel = "select ename from cp_expense_name where eid='$eid'";
		$qry = mysqli_query($conn,$sel);
		$fet = mysqli_fetch_array($qry);
		return $fet['ename'];
	}
	function get_expense_on_name($exp_on,$cust,$lead)
	{
		global $conn;
		if($exp_on != "")
		{
			if($exp_on == "Client")
			{
				$sel = "select company,cperson from cp_customer where cid='$cust'";
			} else {
				$sel = "select company,cperson from cp_lead where lid='$lead'";
			}
			$qry = mysqli_query($conn,$sel);
			$fet = mysqli_fetch_array($qry);
			return $fet['company']." (".$fet['cperson'].")";
		} else {
			return "";
		}
	}
	function get_exp_status_class($status)
	{
		global $conn;
		if($status == 0)
		{ 
			$s = "btn btn-warning";
		} else if($status == 1)
		{ 
			$s = "btn btn-success";
		} else if($status == 2)
		{ 
			$s = "btn btn-danger";
		} else if($status == 3)
		{ 
			$s = "btn btn-4";
		}
		return $s;
	}
	function get_exp_status1($status)
	{
		global $conn;
		if($status == 0)
		{ 
			$s = "Pending";
		} else if($status == 1)
		{ 
			$s = "Approve";
		} else if($status == 2)
		{ 
			$s = "Reject";
		} 
		return $s;
	}
	function get_exp_status($status)
	{
		global $conn;
		if($status == 0)
		{ 
			$s = "<span class='label label-warning'>Pending</span>";
		} else if($status == 1)
		{ 
			$s = "<span class='label label-success'>Approve</span>";
		} else if($status == 2)
		{ 
			$s = "<span class='label label-danger'>Reject</span>";
		} 
		return $s;
	}
	function get_order_status_class($status)
	{
		global $conn;
		if($status == 0)
		{ 
			$s = "btn btn-warning";
		} else if($status == 1)
		{ 
			$s = "btn btn-success";
		} 
		return $s;
	}
	function get_order_status1($status)
	{
		global $conn;
		if($status == 0)
		{ 
			$s = "Open";
		} else if($status == 1)
		{ 
			$s = "Completed";
		} 
		return $s;
	}
	function get_order_status($status)
	{
		global $conn;
		if($status == 0)
		{ 
			$s = "<span class='label label-warning'>Open</span>";
		} else if($status == 1)
		{ 
			$s = "<span class='label label-success'>Completed</span>";
		} 
		return $s;
	}
	function get_lead_status($status)
	{
		global $conn;
		if($status == 0)
		{ 
			$s = "<span class='label label-default'>Low</span>";
		} else if($status == 1)
		{ 
			$s = "<span class='label label-warning'>Medium</span>";
		} else if($status == 2)
		{ 
			$s = "<span class='label label-primary'>High</span>";
		}  else if($status == 3)
		{ 
			$s = "<span class='label label-success'>Done</span>";
		} 
		 else if($status == 4)
		{ 
			$s = "<span class='label label-danger'>Close</span>";
		} 
		return $s;
	}
	function addtocart($pid,$q,$pname)
	{
		if($pid<1 or $q<1) return;
		
		if(is_array($_SESSION['cart'])){
			if(product_exists($pid,$pname)) return;
			$max=count($_SESSION['cart']);
			$_SESSION['cart'][$max]['cart_type'] = "add";
			$_SESSION['cart'][$max]['productid']=$pid;
			$_SESSION['cart'][$max]['qty']=$q;
			$_SESSION['cart'][$max]['pname'] = $pname;
			$_SESSION['cart'][$max]['discount']=0;
			$_SESSION['cart'][$max]['vat'] = 0;
		}
		else{
			$_SESSION['cart']=array();
			$_SESSION['cart'][0]['cart_type'] = "add";
			$_SESSION['cart'][0]['productid']=$pid;
			$_SESSION['cart'][0]['qty']=$q;
			$_SESSION['cart'][0]['pname'] = $pname;
			$_SESSION['cart'][0]['discount']=0;
			$_SESSION['cart'][0]['vat'] = 0;
		}
	}
	function product_exists($pid,$pname)
	{
		$pid=intval($pid);
		$max=count($_SESSION['cart']);
		$flag=0;
		for($i=0;$i<$max;$i++){
			if(($pid==$_SESSION['cart'][$i]['productid']) and ($pname == $_SESSION['cart'][$i]['pname'])){
				$flag=1;
				break;
			}
		}
		return $flag;
	}
	function get_price($pid,$pname)
	{
		global $conn;
		$result=mysqli_query($conn,"select * from cp_product where pid='$pid'");
		$row=mysqli_fetch_array($result);
		return $row['pprice'];
	}
	function get_price1($pid,$pname)
	{
		global $conn;
		$result=mysqli_query($conn,"select * from cp_order_product where pid='$pid'");
		$row=mysqli_fetch_array($result);
		return $row['pprice'];
	}
	function remove_product($pid,$pname)
	{
		$pid=intval($pid);
		$max=count($_SESSION['cart']);
		for($i=0;$i<$max;$i++){
			if(($pid==$_SESSION['cart'][$i]['productid']) and ($pname == $_SESSION['cart'][$i]['pname'])){
				unset($_SESSION['cart'][$i]);
				break;
			}
		}
		$_SESSION['cart']=array_values($_SESSION['cart']);
	}
	function get_product_srno($pid)
	{
		global $conn;
		$result=mysqli_query($conn,"select srno from cp_product where pid=$pid");
		$row=mysqli_fetch_array($result);
		return $row['srno'];
	}
	function get_product_srno1($pid)
	{
		global $conn;
		$result=mysqli_query($conn,"select srno from cp_order_product where pid=$pid");
		$row=mysqli_fetch_array($result);
		return $row['srno'];
	}
	function get_order_srno($oid)
	{
		global $conn;
		$result=mysqli_query($conn,"select srno from cp_order where oid=$oid");
		$row=mysqli_fetch_array($result);
		return $row['srno'];
	}
	function get_product_unit($pid)
	{
		global $conn;
		$result=mysqli_query($conn,"select punit from cp_product where pid=$pid");
		$row=mysqli_fetch_array($result);
		return $row['punit'];
	}
	function get_product_unit1($pid)
	{
		global $conn;
		$result=mysqli_query($conn,"select punit from cp_order_product where pid=$pid");
		$row=mysqli_fetch_array($result);
		return $row['punit'];
	}
	function get_product_pimg($pid)
	{
		global $conn;
		$result=mysqli_query($conn,"select * from cp_product_images where pid=$pid");
		$row=mysqli_fetch_array($result);
		if(mysqli_num_rows($result)>0)
		{
			return $row['pimg'];
		} else {
			return "product.png";
		}
	}
	function get_product_pimg1($pid)
	{
		global $conn;
		$result=mysqli_query($conn,"select pimg from cp_order_product where pid=$pid");
		$row=mysqli_fetch_array($result);
		if(mysqli_num_rows($result)>0)
		{
			return $row['pimg'];
		} else {
			return "product.png";
		}
	}
	function get_order_total_paid_payment($oid)
	{
		global $conn;
		$result=mysqli_query($conn,"select * from cp_order_payment where oid='$oid'");
		$total = 0;
		while($fetch = mysqli_fetch_array($result))
		{
			$total = $total + $fetch['amount'];
		}
		return $total;
	}
	function get_order_total_due_payment($oid)
	{
		global $conn;
		$result=mysqli_query($conn,"select * from cp_order_payment where oid='$oid'");
		$amount = 0;
		while($fetch = mysqli_fetch_array($result))
		{
			$amount = $amount + $fetch['amount'];
		}
		$order_total_grand = get_order_total($oid);
		return $order_total_grand - $amount;
	}
	function get_order_total($oid)
	{
		global $conn;
		$select = "select * from cp_order where oid='".$oid."'";
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		$discount = $fetch['odis'];
		$extradis = $fetch['edis'];
		$vat = $fetch['ovat'];	
		$order_total = 0;
		$order_total_grand = 0;
		$sel2 = "select * from cp_order_product where oid='".$oid."'";
		$qry2 = mysqli_query($conn,$sel2);
		while($fet2 = mysqli_fetch_array($qry2))
		{
			$total = ($fet2['pprice']*$fet2['qty']);
			if($order_total == "")
            {
                $order_total = $total;
            } else {
                $order_total = $order_total + $total;
            }
			if($discount == 0)
			{
				$order_total_discount1 = 0;
			} else {
               	$order_total_discount1 = ($order_total * $discount)/100;
			}
										
			$order_total_totaldiscount = $order_total_discount1 + $extradis;
			$order_total_grand = $order_total - $order_total_totaldiscount;
									
			if($vat == 0)
			{
				$order_total_pvat1 = 0;
			} else {
               	$order_total_pvat1 = ($order_total_grand * $vat)/100;
			}
			$order_total_grand = $order_total_grand + $order_total_pvat1;
		}
		return $order_total_grand;
	}
	function get_goal_achivement($exname,$fdate,$tdate)
	{
		global $conn;
		$select = "select * from cp_order_payment where exname='".$exname."' and (pdate >= '".$fdate."' AND pdate <= '".$tdate."')";
		$query = mysqli_query($conn,$select);
		$amount = 0;
		while($fetch = mysqli_fetch_array($query))
		{
			$amount = $amount + $fetch['amount'];
		}
		return $amount;
	}
	function get_goal_progress($gamount,$exname,$fdate,$tdate)
	{
		global $conn;
		$select = "select * from cp_order_payment where exname='".$exname."' and (pdate >= '".$fdate."' AND pdate <= '".$tdate."')";
		$query = mysqli_query($conn,$select);
		$amount = 0;
		while($fetch = mysqli_fetch_array($query))
		{
			$amount = $amount + $fetch['amount'];
		}
		$per = round(($amount/$gamount)*100);;
		return $per;
	}
	function get_goal_lead($exname,$fdate,$tdate)
	{
		global $conn;
		$select = "select lid from cp_lead where exname='".$exname."' and (ldate >= '".$fdate."' AND ldate <= '".$tdate."')";
		$query = mysqli_query($conn,$select);
		$num = mysqli_num_rows($query);
		return $num;
	}
	function get_goal_lead_done($exname,$fdate,$tdate)
	{
		global $conn;
		$select = "select lid from cp_lead where exname='".$exname."' and ltype='3' and (ldate >= '".$fdate."' AND ldate <= '".$tdate."')";
		$query = mysqli_query($conn,$select);
		$num = mysqli_num_rows($query);
		return $num;
	}
	function get_bank_account_name($bid)
	{
		global $conn;
		$select = "select aname from cp_bank where bid='".$bid."'";
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['aname'];
	}
	function get_total_lead()
	{
		global $conn;
		if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
		{
			$select = "select lid from cp_lead order by lid desc";
		} else {
			$select = "select lid from cp_lead where exname='".$_SESSION['admin_id']."' order by lid desc";
		}
		$query = mysqli_query($conn,$select);
		$num = mysqli_num_rows($query);
		return $num;
	}
	function get_status_lead($status)
	{
		global $conn;
		if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
		{
			$select = "select lid from cp_lead where ltype='".$status."' order by lid desc";
		} else {
			$select = "select lid from cp_lead where ltype='".$status."' and exname='".$_SESSION['admin_id']."' order by lid desc";
		}
		$query = mysqli_query($conn,$select);
		$num = mysqli_num_rows($query);
		return $num;
	}
	function super_unique($array)
	{
	  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
	
	  foreach ($result as $key => $value)
	  {
		if ( is_array($value) )
		{
		  $result[$key] = super_unique($value);
		}
	  }
	
	  return $result;
	}
	
	function get_time_arr_interval($interval=15)
	{
		$time_arr=array();
		for($i=0;$i<24;$i++)
		{
			if(strlen($i)==1)
			{
				$hour="0".$i;
			}else
			{
				$hour=$i;
			}
			
			for($j=0;$j<60;)
			{
				if(strlen($j)==1)
				{
					$min="0".$j;
				}else
				{
					$min=$j;
				}
				if($hour > 12)
				{
					if(strlen(($hour-12))==1)
					{
						$time="0".($hour-12).":".$min." PM";
					}else
					{
						$time=($hour-12).":".$min." PM";
					}
				}elseif($hour==0)
				{
					$time="12:".$min." AM";
				}elseif($hour==12)
				{
					$time=$hour.":".$min." PM";
				}
				else
				{
					$time=$hour.":".$min." AM";
				}
				array_push($time_arr,$time);
				$j+=$interval;
			}
		}
		return $time_arr;
	}
	function get_assign_to_user($uid)
	{
		global $conn;
		$sel = "select fname,lname from cp_users where emailid='".$uid."'";
		$qry = mysqli_query($conn,$sel);
		$fet = mysqli_fetch_array($qry);
		return $fet['fname']." ".$fet['lname'];
	}
	function get_prev_record_of_lead($id)
	{
		global $conn;
		if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
		{
			$select = "select lid from cp_lead where lid < '".$id."'  order by lid desc LIMIT 1";
		} else {
			$select = "select lid from cp_lead where lid < '".$id."' and  exname='".$_SESSION['admin_id']."' order by lid desc LIMIT 1";
		}
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['lid'];
	}
	function get_next_record_of_lead($id)
	{
		global $conn;
		if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
		{
			$select = "select lid from cp_lead where lid > '".$id."' LIMIT 1";
		} else {
			$select = "select lid from cp_lead where lid > '".$id."' and  exname='".$_SESSION['admin_id']."' LIMIT 1";
		}
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['lid'];
	}
	function get_prev_record_of_work_report($id)
	{
		global $conn;
		if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
		{
			$select = "select rid from cp_report where rid < '".$id."'  order by rid desc LIMIT 1";
		} else {
			$select = "select rid from cp_report where rid < '".$id."' and (exname='".$_SESSION['admin_id']."' or exname IN (select emailid from cp_users where scoordinator='".$_SESSION['admin_id']."'))  order by rid desc LIMIT 1";
		}
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['rid'];
	}
	function get_next_record_of_work_report($id)
	{
		global $conn;
		if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
		{
			$select = "select rid from cp_report where rid > '".$id."' LIMIT 1";
		} else {
			$select = "select rid from cp_report where rid > '".$id."' and (exname='".$_SESSION['admin_id']."' or exname IN (select emailid from cp_users where scoordinator='".$_SESSION['admin_id']."')) LIMIT 1";
		}
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['rid'];
	}
	function get_prev_record_of_product($id)
	{
		global $conn;
		$select = "select pid from cp_product where pid < '".$id."'  order by pid desc LIMIT 1";
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['pid'];
	}
	function get_next_record_of_product($id)
	{
		global $conn;
		$select = "select pid from cp_product where pid > '".$id."' LIMIT 1";
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['pid'];
	}
	function get_prev_record_of_customer($id)
	{
		global $conn;
		$select = "select cid from cp_customer where cid < '".$id."'  order by cid desc LIMIT 1";
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['cid'];
	}
	function get_next_record_of_customer($id)
	{
		global $conn;
		$select = "select cid from cp_customer where cid > '".$id."' LIMIT 1";
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['cid'];
	}
	function get_prev_record_of_order($id)
	{
		global $conn;
		if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
		{
			$select = "select oid from cp_order where oid < '".$id."'  order by oid desc LIMIT 1";
		} else {
			$select = "select oid from cp_order where oid < '".$id."' and  exname='".$_SESSION['admin_id']."' order by oid desc LIMIT 1";
		}
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['oid'];
	}
	function get_next_record_of_order($id)
	{
		global $conn;
		if($_SESSION['utype'] == 1 || $_SESSION['utype'] == 2)
		{
			$select = "select oid from cp_order where oid > '".$id."' LIMIT 1";
		} else {
			$select = "select oid from cp_order where oid > '".$id."' and  exname='".$_SESSION['admin_id']."' LIMIT 1";
		}
		$query = mysqli_query($conn,$select);
		$fetch = mysqli_fetch_array($query);
		return $fetch['oid'];
	}
?>