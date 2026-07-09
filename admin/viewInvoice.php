<?php
    include 'header.php';
?> 
    <style type="text/css">
        @media print {
            @page {
                margin-left: 0.5in;
                margin-right: 0.5in;
                margin-top: 0;
                margin-bottom: 0;
            }
            .printbtn{
                display: none !important;
            }
            footer{
                display: none !important;   
            }
            .firsttd{
                width : 40%;
            }
            .secondtd{
                width : 50%;   
                text-align: right!important;
            }
        }
    </style>
        <div id="page-wrapper">
            <div class="row">
                <?php
                    if(isset($_GET['id'])){
                        $id = $_GET['id'];
                        $invoice_qry = "SELECT invoiceTbl.*, projectstbl.projectName FROM invoiceTbl left join projectstbl on projectstbl.id = invoiceTbl.project_Id WHERE invoiceTbl.invoice_id = '$id' limit 1";


                        $invoice_result = mysqli_query($conn,$invoice_qry);
                        if($invoice_result->num_rows > 0){
                            $invoice_row = $invoice_result->fetch_assoc();
                            $invoice_og_date = new DateTime($invoice_row['invoice_date']);
                            ?>
                            <div class="col-lg-12">
                                <div class="panel panel-default deposit_table">
                                    <div class="panel-heading">
                                        Invoice Detail
                                    </div>
                                    <div class="panel-body">
                                                
                                        <div class="col-lg-12">
                                            <!-- <table>
                                                <tr>
                                                    <td class="firsttd"><img src="../admin/img/logo.png" alt="logo" ></td>
                                                    <td class="secondtd" >
                                                        <h4>Invoice : <?php echo $id ?></h4>
                                                        <h4>Invoice No : <?php echo $invoice_row['invoice_no']; ?></h4>
                                                        
                                                        <p class="text-muted"><strong>Invoice Date : <?php echo $invoice_og_date->format('d-m-Y') ?></strong></p>
                                                        <address>
                                                            122, Shree Ugati Corporate Park<br>
                                                            Por - Kudasan Rd, Kudasan, <br>
                                                            Gujarat 382421<br>
                                                            <abbr title="Phone">P:</abbr> 9081022999
                                                        </address>
                                                    </td>
                                                </tr>
                                            </table> -->
                                            <div class="col-sm-6">
                                                    <img src="../admin/img/logo.png" alt="logo" width="250px" style="margin-top: 40px;">
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="float-right" style="text-align: right;">
                                                    <h4 class="m-0 d-print-none">Invoice : <?php echo $id ?></h4>
                                                    <div class="">
                                                        <p class="text-muted"><strong>Invoice Date : <?php echo $invoice_og_date->format('d-m-Y') ?></strong></p>
                                                        <address>
                                                            122, Shree Ugati Corporate Park<br>
                                                            Por - Kudasan Rd, Kudasan, <br>
                                                            Gujarat 382421<br>
                                                            <abbr title="Phone">P:</abbr> 9081022999
                                                        </address>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="col-md-6">
                                                <div class="">
                                                    <p><strong>To,</strong></p>
                                                    <p><strong><?php echo $invoice_row['person_name'] ?></strong></p>
                                                    <p><strong><?php echo $invoice_row['projectName'] ?></strong></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table class="table mt-4 table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th width="10%">#</th>
                                                                <th width="50%">Perticular</th>
                                                                <th width="25%">Bank</th>
                                                                <!-- <th width="15%">Currency</th> -->
                                                                <th width="15%" class="text-right">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            $details_qry = "SELECT * FROM invoice_details where invoice_id = '$id'";
                                                            $details_result = mysqli_query($conn,$details_qry);
                                                            if($details_result->num_rows > 0){
                                                                $dt = 1;
                                                                while($details_row = $details_result->fetch_assoc())
                                                                {
                                                                    $bank_name="";
                                                                    if($details_row['bank_id']!="" && $details_row['bank_id']!=null)
                                                                    {
                                                                        // $details_row['bank_id']
                                                                        $sel_projecttype_details=mysqli_query($conn,"SELECT * FROM account WHERE account_id='".$details_row['bank_id']."' ");
                                                                        $fetch_projecttype_details=mysqli_fetch_assoc($sel_projecttype_details);
                                                                        if(isset($fetch_projecttype_details['account_name']))
                                                                        {
                                                                            $bank_name=$fetch_projecttype_details['account_name'];
                                                                        }
                                                                    }
                                                                    $currancy_name="";
                                                                    if($details_row['country_id']!="" && $details_row['country_id']!=null)
                                                                    {
                                                                        // $details_row['country_id']
                                                                        $sel_country_details=mysqli_query($conn,"SELECT * FROM country WHERE country_id='".$details_row['country_id']."' ");
                                                                        $fetch_country_details=mysqli_fetch_assoc($sel_country_details);
                                                                        if(isset($fetch_country_details['country_symbols']))
                                                                        {
                                                                            $currancy_name=$fetch_country_details['country_symbols'];
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo $dt++; ?></td>
                                                                        <td><?php echo $details_row['perticular'] ?></td>
                                                                        <td><?php echo $bank_name ?></td>
                                                                        <!-- <td><?php echo $currancy_name ?></td> -->
                                                                        <td><i class="mdi mdi-currency-inr"></i><?php echo $currancy_name." ".number_format($details_row['amount'],2); ?></td>
                                                                    </tr>
                                                                    <?php 
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="col-md-6 text-left">
                                                <div class="float-left">
                                                    <h6 class="text-muted">Notes:</h6>
                                                    <small class="text-muted">
                                                        All accounts are to be paid within 7 days from receipt of
                                                        invoice. To be paid by cheque or credit card or direct payment
                                                        online. If account is not paid within 7 days the credits details
                                                        supplied as confirmation of work undertaken will be charged the
                                                        agreed quoted fee noted above.
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <div class="float-right">
                                                    <h5>Total: <i class="mdi mdi-currency-inr"></i><?php echo number_format($invoice_row['total_amount'],2) ?></h5>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                        <div class="mt-4 mb-1">
                                            <div class="text-right d-print-none">
                                                <a href="javascript:window.print()" class="printbtn btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
                                            </div>
                                        </div>
                                                        
                                                    
                                    </div>
                                </div>
                            </div>
                            <?php
                            
                        }
                    }
                ?>
            </div>
        </div>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../vendor/metisMenu/metisMenu.min.js"></script>
<script src="../vendor/raphael/raphael.min.js"></script>
<script src="../vendor/morrisjs/morris.min.js"></script>
<script src="../data/morris-data.js"></script>
<script src="../dist/js/sb-admin-2.js"></script>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../vendor/metisMenu/metisMenu.min.js"></script>
<script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
<script src="../vendor/raphael/raphael.min.js"></script>
<script src="../vendor/morrisjs/morris.min.js"></script>
<script src="../data/morris-data.js"></script>
<script src="../dist/js/sb-admin-2.js"></script>
<?php include 'footer.php'; ?>
