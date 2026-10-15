<?php
	require_once __DIR__ . '/../dbconnect.php';
	require_once __DIR__ . '/../security.php';
	require_once __DIR__ . '/../foundation.php';
	oecrm_require_admin_login();
	oecrm_require_permission($conn, 'finance', 'edit');
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		oecrm_require_csrf();
	}

	require_once __DIR__ . '/../invoiceStatus.php';
    require_once __DIR__ . '/../invoiceNumbering.php';
    oecrm_invoice_numbering_schema($conn);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel_invoice') {
        $id = (int) ($_POST['id'] ?? 0);
        $returnPage = ($_POST['return_page'] ?? '') === 'editInvoice.php' && $id > 0
            ? 'editInvoice.php?edit=' . $id : 'addInvoice.php';
        try {
            $old = oecrm_cancel_legacy_invoice($conn, $id);
            $_SESSION['invoice_flash'] = [
                'type' => 'success',
                'message' => $old ? 'Invoice cancelled successfully.' : 'This invoice is already cancelled.',
            ];
        } catch (InvalidArgumentException $e) {
            $_SESSION['invoice_flash'] = ['type' => 'warning', 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            error_log('Invoice cancellation failed: ' . $e->getMessage());
            $_SESSION['invoice_flash'] = ['type' => 'danger', 'message' => 'Invoice could not be cancelled. Please try again.'];
        }
        if (!empty($old)) {
            oecrm_audit($conn, 'finance', 'cancel', 'invoiceTbl', $id, 'Invoice cancelled', $old, ['status' => 3]);
        }
        header('Location:' . $returnPage);
        exit;
    }
if(isset($_POST['addInvoice'])){
        try {
            $companyId = oecrm_current_company_id($conn);
            if ((int) ($_POST['invoice_company_id'] ?? 0) !== $companyId) {
                throw new InvalidArgumentException('The active company changed. Reload the invoice form before saving.');
            }
            $created = oecrm_create_numbered_legacy_invoice($conn, $companyId, $_POST);
            $_SESSION['invoice_flash'] = ['type' => 'success', 'message' => 'Invoice ' . $created['number'] . ' created successfully.'];
        } catch (Throwable $e) {
            error_log('Invoice creation failed: ' . $e->getMessage());
            $_SESSION['invoice_flash'] = ['type' => 'warning', 'message' => $e instanceof InvalidArgumentException ? $e->getMessage() : 'Invoice could not be saved. Its number has not been consumed.'];
        }
        header('Location:addInvoice.php');
        exit;}elseif(isset($_POST['updateInvoice'])){
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        mysqli_begin_transaction($conn);
        try {
		$id = (int) ($_POST['id'] ?? 0);
        $invoice = oecrm_legacy_invoice_lock($conn, $id);
        if (!$invoice || (int) $invoice['status'] === 3) {
            throw new InvalidArgumentException('Cancelled invoices cannot be edited.');
        }
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

		$order_qry = "UPDATE `invoiceTbl` SET `invoice_date`='$invoice_date',`project_Id`='$project_Id',`person_name`='$person_name',`total_amount`='$total_amount',`invoice_no`='$invoice_no' WHERE invoice_id=".$id . " AND status<>3";
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
        mysqli_commit($conn);
        header('Location:editInvoice.php?edit='.$id);
        exit;
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            error_log('Invoice edit failed: ' . $e->getMessage());
            $_SESSION['invoice_flash'] = ['type' => 'warning', 'message' => $e instanceof InvalidArgumentException ? $e->getMessage() : 'Invoice could not be updated. Please try again.'];
            header('Location:addInvoice.php');
            exit;
        }
    }elseif(isset($_GET['id'])){
    	echo '<pre>'; print_r($_GET);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
	}else{
		header('Location:addInvoice.php');
	}
?>
