<?php
if(!isset($_SESSION['admin_id']))
{
	header("location:login.php");
} else {
	if(!isset($_REQUEST['pid']))
	{
		$pid = "home";
	} else {
		$pid = $_REQUEST['pid'];
	}
	if($pid == "home")
	{
		include("dashboard.php");
	}
	
	if($pid == "profile")
	{
		include("profile.php");
	}
	if($pid == "users")
	{
		include("users.php");
	}
	if($pid == "view_user")
	{
		include("view_user.php");
	}
	if($pid == "profile1")
	{
		include("profile1.php");
	}
	if($pid == "settings")
	{
		include("settings.php");
	}
	if($pid == "logs")
	{
		include("logs.php");
	}
	if($pid == "add_report")
	{
		include("add_report.php");
	}
	if($pid == "view_all_report")
	{
		include("view_all_report.php");
	}
	if($pid == "master_search_report")
	{
		include("master_search_report.php");
	}
	if($pid == "view_report_detail")
	{
		include("view_report_detail.php");
	}
	if($pid == "add_product")
	{
		include("add_product.php");
	}
	if($pid == "view_all_products")
	{
		include("view_all_products.php");
	}
	if($pid == "view_product_detail")
	{
		include("view_product_detail.php");
	}
	if($pid == "add_lead")
	{
		include("add_lead.php");
	}
	if($pid == "view_all_leads")
	{
		include("view_all_leads.php");
	}
	if($pid == "view_lead_details")
	{
		include("view_lead_details.php");
	}
	if($pid == "today_followup")
	{
		include("today_followup.php");
	}
	if($pid == "pending_followup")
	{
		include("pending_followup.php");
	}
	if($pid == "view_status_lead")
	{
		include("view_status_lead.php");
	}
	if($pid == "add_customer")
	{
		include("add_customer.php");
	}
	if($pid == "view_all_customers")
	{
		include("view_all_customers.php");
	}
	if($pid == "view_customer_detail")
	{
		include("view_customer_detail.php");
	}
	if($pid == "create_order")
	{
		include("create_order.php");
	}
	if($pid == "shoppingcart")
	{
		include("shoppingcart.php");
	}
	if($pid == "checkout")
	{
		include("checkout.php");
	}
	if($pid == "edit_order")
	{
		include("edit_order.php");
	}
	if($pid == "orders")
	{
		include("orders.php");
	}
	if($pid == "view_order_detail")
	{
		include("view_order_detail.php");
	}
	if($pid == "payment_received")
	{
		include("payment_received.php");
	}
	if($pid == "regions")
	{
		include("regions.php");
	}
	if($pid == "marketing_material")
	{
		include("marketing_material.php");
	}
	if($pid == "noticeboard")
	{
		include("noticeboard.php");
	}
	if($pid == "goals")
	{
		include("goals.php");
	}
	if($pid == "bank_account")
	{
		include("bank_account.php");
	}
	if($pid == "deposit")
	{
		include("deposit.php");
	}
	if($pid == "expense")
	{
		include("expense.php");
	}
	if($pid == "transaction_report")
	{
		include("transaction_report.php");
	}
	if($pid == "view_all_followups")
	{
		include("view_all_followups.php");
	}
	
}
?>