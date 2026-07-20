<?php include 'header.php'; ?>
<?php
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS domainHostingTbl (
    domain_id INT AUTO_INCREMENT PRIMARY KEY,
    clientname VARCHAR(255) NOT NULL,
    service VARCHAR(100) NOT NULL,
    domainname VARCHAR(255) NOT NULL,
    startdate DATE NOT NULL,
    enddate DATE NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0
)");
?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Add Domain Hosting</div>
                        <div class="panel-body">
                        	<div class="dataTablesbox2">
                                <form role="form" method="POST" onsubmit="return checkall();" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Client Name :</label>
                                                <input class="form-control" placeholder="Client Name" id="projectName" onkeyup="checkprojectName();" type="text" name="clientname" >
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Services :</label>
                                                <select name="service" class="form-control" >
                                                    <option value="">Select Service</option>
                                                    <option value="Domain">Domain</option>
                                                    <option value="Hosting">Hosting</option>
                                                    <option value="SSL">SSL</option>
                                                    <option value="SMS">SMS</option>
                                                    <option value="Domain And Hosting">Domain And Hosting</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Domain Name :</label>
                                                <input class="form-control" placeholder="Domain Name" id="domainname" onkeyup="checkprojectName();" type="text" name="domainname" >
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Start Date :</label>
                                                <input class="form-control" id="startdate" onkeyup="checkprojectName();" value="<?php echo date('Y-m-d'); ?>" type="date" name="startdate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>End Date :</label>
                                                <input class="form-control" id="enddate" onkeyup="checkprojectName();" type="date" name="enddate">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Amount :</label>
                                                <input class="form-control" placeholder="Amount" id="amount" onkeyup="checkprojectName();" type="number" name="amount" min="0" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row" style="margin:15px 0">
                                            <input type="submit" class="btn viewreport" name="addHost" value="Add Domain/Hosting">
                                            <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">
                                        </div>
                                    </div>
                                </form>
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
    <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../data/morris-data.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTables-example').DataTable({
                responsive: true,
                "paging":   false,
                "ordering": false,
                "info":     false,
                "searching": false
                /*"pageLength": 50*/
            });
        });
    </script>
    <script type="text/javascript">
        function checkprojectName(){
            var project=document.getElementById( "projectName" ).value;
            if(project){
                $.ajax({
                    type:'post',
                    url:'checkAvailibility.php',
                    data: {
                        projectName:project,
                    },
                    success: function (response){
                        $('#projectName_status').html(response);
                        if(response=="Projectname Available"){
                            $('#projectName_status').css("color", "green");
                            return true;
                        }else{
                            $('#projectName_status').css("color", "red");
                            return false;
                        }
                    }
                });
            }else{
                $('#projectName_status').html("");
                return false;
            }
        }
        function checkall(){
            var projectNamehtml=document.getElementById("projectName_status").innerHTML;
            if(projectNamehtml=="Projectname Available"){
                return true;
            }else{
                return false;
            }
        }
    </script>
<?php include 'footer.php'; ?>
