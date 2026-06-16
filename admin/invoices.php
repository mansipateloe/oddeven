<?php
	require_once __DIR__ . '/dbconnect.php';
	require_once __DIR__ . '/../security.php';
	require_once __DIR__ . '/../foundation.php';
	oecrm_require_admin_login();
	oecrm_require_permission($conn, 'finance', 'edit');
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		oecrm_require_csrf();
	}

	if(isset($_POST['addInvoice'])){
		$invoice_date = mysqli_real_escape_string($conn, $_POST['invoice_date']);
		$project_Id = mysqli_real_escape_string($conn, $_POST['project_Id']);
		$person_name = mysqli_real_escape_string($conn, $_POST['person_name']);
		$invoice_no = mysqli_real_escape_string($conn, $_POST['invoice_no']);
		$perticulars = $_POST['perticulars'];
		$amounts = $_POST['amounts'];
		$bank_ids=$_POST['bank_id'];
		$country_ids=$_POST['country_id'];
		$total_amount = 0;

		for ($i=0; $i < count($amounts); $i++) {
			$total_amount += $amounts[$i] ?: "0";
		}

		$order_qry = "INSERT INTO invoiceTbl (invoice_date, project_Id, person_name, total_amount,invoice_no) VALUES ('$invoice_date', '$project_Id', '$person_name', '$total_amount', '$invoice_no')";
        if (mysqli_query($conn,$order_qry)){
        	$invoice_id = mysqli_insert_id($conn);

        	for ($i=0; $i < count($perticulars); $i++) { 
				$item = $perticulars[$i] ?: "";
				$amount = $amounts[$i] ?: "0";
				$bank_id = $bank_ids[$i] ?: "0";
				$country_id = $country_ids[$i] ?: "0";

				$order_detail_qry = "INSERT INTO invoice_details (invoice_id, perticular, amount, bank_id, country_id) VALUES ('$invoice_id', '$item', '$amount','$bank_id','$country_id')";
		        mysqli_query($conn,$order_detail_qry);
			}
        }else{
        }
        header('Location:addInvoice.php');
	}elseif(isset($_POST['updateInvoice'])){
		$id = mysqli_real_escape_string($conn, $_POST['id']);
		$invoice_date = mysqli_real_escape_string($conn, $_POST['invoice_date']);
		$project_Id = mysqli_real_escape_string($conn, $_POST['project_Id']);
		$person_name = mysqli_real_escape_string($conn, $_POST['person_name']);
		$invoice_no = mysqli_real_escape_string($conn, $_POST['invoice_no']);
		$perticulars = $_POST['perticulars'];
		$amounts = $_POST['amounts'];
		$bank_ids=$_POST['bank_id'];
		$country_ids=$_POST['country_id'];
		$total_amount = 0;

		for ($i=0; $i < count($amounts); $i++) {
			$total_amount += $amounts[$i] ?: "0";
		}

		$order_qry = "UPDATE `invoiceTbl` SET `invoice_date`='$invoice_date',`project_Id`='$project_Id',`person_name`='$person_name',`total_amount`='$total_amount',`invoice_no`='$invoice_no' WHERE invoice_id=".$id;
        if (mysqli_query($conn,$order_qry)){

        	$qry = "DELETE FROM invoice_details WHERE invoice_id=".$id;
			mysqli_query($conn,$qry);

        	for ($i=0; $i < count($perticulars); $i++) { 
				$item = $perticulars[$i] ?: "";
				$amount = $amounts[$i] ?: "0";
				$bank_id = $bank_ids[$i] ?: "0";
				$country_id = $country_ids[$i] ?: "0";

				$order_detail_qry = "INSERT INTO invoice_details (invoice_id, perticular, amount, bank_id, country_id) VALUES ('$id', '$item', '$amount','$bank_id','$country_id')";
		        mysqli_query($conn,$order_detail_qry);
			}
        }else{
        }
        header('Location:editInvoice.php?edit='.$id);
    }elseif(isset($_GET['id'])){
    	echo '<pre>'; print_r($_GET);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
	}else{
		header('Location:addInvoice.php');
	}
?>
