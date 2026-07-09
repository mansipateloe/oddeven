<?php
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS professionalTaxTbl (
    id INT AUTO_INCREMENT PRIMARY KEY,
    startingAmount DECIMAL(12,2) NOT NULL,
    endingAmount DECIMAL(12,2) NOT NULL,
    professionalTax DECIMAL(12,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$flash = $_SESSION['professional_tax_flash'] ?? '';
unset($_SESSION['professional_tax_flash']);

if (isset($_POST['addProfessionalTax'])) {
    oecrm_require_csrf();
    $startingAmount = (float) ($_POST['startingAmount'] ?? 0);
    $endingAmount = (float) ($_POST['endingAmount'] ?? 0);
    $professionalTax = (float) ($_POST['professionalTax'] ?? 0);
    if ($startingAmount < 0 || $endingAmount < 0 || $professionalTax < 0 || $endingAmount < $startingAmount) {
        $_SESSION['professional_tax_flash'] = 'Please enter a valid tax range.';
        header('Location:manageProfessionalTax.php');
        exit;
    }
    $duplicate = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM professionalTaxTbl WHERE startingAmount='$startingAmount' AND endingAmount='$endingAmount' AND professionalTax='$professionalTax' LIMIT 1"));
    if ($duplicate) {
        $_SESSION['professional_tax_flash'] = 'Professional tax already exists.';
        header('Location:manageProfessionalTax.php');
        exit;
    }
    $qryAddProTax = "INSERT INTO professionalTaxTbl (startingAmount, endingAmount, professionalTax) VALUES ('$startingAmount', '$endingAmount', '$professionalTax')";
    if (mysqli_query($conn, $qryAddProTax)) {
        $_SESSION['professional_tax_flash'] = 'Professional tax added successfully.';
    } else {
        $_SESSION['professional_tax_flash'] = 'Professional tax could not be added.';
    }
    header('Location:manageProfessionalTax.php');
    exit;
}
include 'header.php';
?>
<div id="page-wrapper">
    <div class="panel panel-default managetax">
        <div class="panel-heading">Add New Professional Tax</div>
        <div class="panel-body">
            <?php if ($flash): ?>
                <div class="alert alert-info"><?php echo oecrm_h($flash); ?></div>
            <?php endif; ?>
            <div class="search_box_area">
                <form method="POST">
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group col-lg-2">
                                <label>*From</label>
                                <input type="number" class="form-control" name="startingAmount" placeholder="Starting Amount" required>
                            </div>
                            <div class="form-group col-lg-2">
                                <label>*To </label>
                                <input type="number" class="form-control" name="endingAmount" placeholder="Ending Amount" required>
                            </div>
                            <div class="form-group col-lg-2">
                                <label>*Tax</label>
                                <input type="number" class="form-control" name="professionalTax" placeholder="Tax" required>
                            </div>
                            <div class="form-group col-lg-1">
                                <br>
                                <input type="submit" class="btn btn-primary profeesionaltax_btn" name="addProfessionalTax" value="Add">
                            </div>
                            <div class="form-group col-lg-1">
                                <br>
                                <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Professional Tax Table</div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                                    <thead>
                                        <tr role="row">
                                            <th class="center-bold">From</th>
                                            <th class="center-bold">To</th>
                                            <th class="center-bold">Tax Amount</th>
                                            <th style="text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $qryProTax = "SELECT * FROM professionalTaxTbl ORDER BY startingAmount ASC";
                                        $resultProTax = mysqli_query($conn, $qryProTax);
                                        if ($resultProTax && $resultProTax->num_rows > 0) {
                                            while ($rowProTax = $resultProTax->fetch_assoc()) {
                                                echo "<tr class='gradeA even' role='row'>";
                                                echo "<td class='center-bold'>" . oecrm_h($rowProTax['startingAmount']) . "</td>";
                                                echo "<td class='center-bold'>" . oecrm_h($rowProTax['endingAmount']) . "</td>";
                                                echo "<td class='center-bold'>" . oecrm_h($rowProTax['professionalTax']) . "</td>";
                                                echo '<td class="center" align="center"><form method="post" action="deleteProfessionalTax.php" style="display:inline;">' . oecrm_csrf_field() . '<input type="hidden" name="deleteProfessionalTax" value="' . (int) $rowProTax["id"] . '"><button type="submit" class="btn btn-link" style="padding:0;border:0;" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></button></form></td>';
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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
</div>
<?php include 'footer.php'; ?>
