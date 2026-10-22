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
        if (!$invoice) {
            throw new InvalidArgumentException('Invoice not found.');
        }
        if ((int) $invoice['status'] === 2) {
            throw new InvalidArgumentException('Paid invoices cannot be edited.');
        }
        if ((int) $invoice['status'] === 3) {
            throw new InvalidArgumentException('Cancelled invoices cannot be edited.');
        }
		$invoice_date = (string) ($_POST['invoice_date'] ?? '');
		$project_Id = (int) ($_POST['project_Id'] ?? 0);
		$person_name = (string) ($_POST['person_name'] ?? '');
		$invoice_no = (string) ($_POST['invoice_no'] ?? '');
		$perticulars = $_POST['perticulars'] ?? [];
		$amounts = $_POST['amounts'] ?? [];
		$bank_ids = $_POST['bank_id'] ?? [];
		$country_ids = $_POST['country_id'] ?? [];
		if (!is_array($perticulars) || !is_array($amounts) || !is_array($bank_ids) || !is_array($country_ids)
			|| count($perticulars) === 0 || count($perticulars) !== count($amounts)
			|| count($perticulars) !== count($bank_ids) || count($perticulars) !== count($country_ids)) {
			throw new InvalidArgumentException('Please add at least one valid invoice item.');
		}
		$total_amount = 0.0;

		for ($i=0; $i < count($amounts); $i++) {
			$total_amount += $amounts[$i] ?: "0";
		}

		$order_stmt = oecrm_invoice_schema_prepare($conn, 'UPDATE invoiceTbl SET invoice_date=?,project_Id=?,person_name=?,total_amount=?,invoice_no=? WHERE invoice_id=? AND status<>3');
		mysqli_stmt_bind_param($order_stmt, 'sisdsi', $invoice_date, $project_Id, $person_name, $total_amount, $invoice_no, $id);
		mysqli_stmt_execute($order_stmt);
		mysqli_stmt_close($order_stmt);

		$delete_stmt = mysqli_prepare($conn, 'DELETE FROM invoice_details WHERE invoice_id=?');
		mysqli_stmt_bind_param($delete_stmt, 'i', $id);
		mysqli_stmt_execute($delete_stmt);
		mysqli_stmt_close($delete_stmt);

		$detail_stmt = mysqli_prepare($conn, 'INSERT INTO invoice_details (invoice_id, perticular, amount, bank_id, country_id) VALUES (?, ?, ?, ?, ?)');
		for ($i=0; $i < count($perticulars); $i++) {
			$item = (string) ($perticulars[$i] ?? '');
			$amount = (string) ($amounts[$i] ?: '0');
			$bank_id = (int) ($bank_ids[$i] ?: 0);
			$country_id = (int) ($country_ids[$i] ?: 0);
			mysqli_stmt_bind_param($detail_stmt, 'issii', $id, $item, $amount, $bank_id, $country_id);
			mysqli_stmt_execute($detail_stmt);
		}
		mysqli_stmt_close($detail_stmt);

        $client = oecrm_invoice_project_client($conn, $project_Id, oecrm_current_company_id($conn));
        $tax = oecrm_invoice_tax_calculate($total_amount, $_POST, $client['is_indian_client']);
        oecrm_invoice_tax_save($conn, $id, $tax);
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
