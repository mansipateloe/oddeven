<?php
$active_menu = 'setting';
$active_submenu = 'setting_currency';
include 'header.php';

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS currency_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    symbol VARCHAR(20) NOT NULL,
    rate DECIMAL(14,6) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$flash = $_SESSION['currency_flash'] ?? '';
unset($_SESSION['currency_flash']);
$rows = mysqli_query($conn, 'SELECT * FROM currency_master ORDER BY name');
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4>Currency</h4>
            <?php if ($flash): ?>
                <div class="alert alert-info"><?php echo oecrm_h($flash); ?></div>
            <?php endif; ?>
            <div class="dataTablesbox2 dataTablesbox">
                <form role="form" method="POST" action="validation.php">
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-3">
                            <label>Name :</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" required maxlength="20">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label>Symbol :</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="symbol" required maxlength="3">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label>Rate :</label>
                            <div class="form-group">
                                <input type="number" class="form-control" name="rate" required step="0.000001">
                            </div>
                        </div>
                        <div class="col-lg-3" align="right">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary viewreport" name="addCurrency" value="Add Currency">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Currency Table</div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Symbol</th>
                                    <th>Rate</th>
                                    <th style="text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($rows && mysqli_num_rows($rows) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($rows)): ?>
                                        <tr>
                                            <td><?php echo oecrm_h($row['name']); ?></td>
                                            <td><?php echo oecrm_h($row['symbol']); ?></td>
                                            <td><?php echo oecrm_h($row['rate']); ?></td>
                                            <td class="center" align="center">
                                                <a href="editCurrency.php?edit=<?php echo (int) $row['id']; ?>" style="display:inline-block;width:28px;" title="Edit"><i class="fa fa-pencil"></i></a>
                                                <form method="post" action="deleteCurrency.php" style="display:inline;">
                                                    <?php echo oecrm_csrf_field(); ?>
                                                    <input type="hidden" name="deleteCurrency" value="<?php echo (int) $row['id']; ?>">
                                                    <button type="submit" class="btn btn-link" style="padding:0;border:0;" onclick="return confirm('Are you sure you want to delete?');" title="Delete">
                                                        <i class="fa fa-trash-o"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
