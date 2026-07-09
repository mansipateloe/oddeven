<?php
$active_menu = 'setting';
$active_submenu = 'setting_currency';
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
oecrm_require_admin_login();

$id = (int) ($_GET['edit'] ?? 0);
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS currency_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    symbol VARCHAR(20) NOT NULL,
    rate DECIMAL(14,6) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$stmt = mysqli_prepare($conn, 'SELECT * FROM currency_master WHERE id=? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$currency = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$currency) {
    $_SESSION['currency_flash'] = 'Currency record not found.';
    header('Location:manageCurrency.php');
    exit;
}

include 'header.php';
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4>Edit Currency</h4>
            <div class="dataTablesbox2 dataTablesbox">
                <form role="form" method="POST" action="validation.php">
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int) $currency['id']; ?>">
                    <div class="row">
                        <div class="col-lg-3">
                            <label>Name :</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" required maxlength="20" value="<?php echo oecrm_h($currency['name']); ?>">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label>Symbol :</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="symbol" required maxlength="3" value="<?php echo oecrm_h($currency['symbol']); ?>">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label>Rate :</label>
                            <div class="form-group">
                                <input type="number" class="form-control" name="rate" required step="0.000001" value="<?php echo oecrm_h($currency['rate']); ?>">
                            </div>
                        </div>
                        <div class="col-lg-3" align="right">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary viewreport" name="updateCurrency" value="Update Currency">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../vendor/metisMenu/metisMenu.min.js"></script>
<script src="../vendor/raphael/raphael.min.js"></script>
<script src="../vendor/morrisjs/morris.min.js"></script>
<script src="../data/morris-data.js"></script>
<script src="../dist/js/sb-admin-2.js"></script>
<?php include 'footer.php'; ?>
