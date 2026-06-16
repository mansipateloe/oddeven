<?php
    require_once __DIR__ . '/dbconnect.php';
    require_once __DIR__ . '/../security.php';
    require_once __DIR__ . '/../foundation.php';
    oecrm_require_admin_login();
    oecrm_require_permission($conn, 'finance', 'view');
    $proj_id = oecrm_int_param($_POST, 'proj_id');
    $data = array();

    if (isset($_POST['proj_id'])) {
        $project_sql = "select invoice_id from invoiceTbl where project_Id = $proj_id ORDER BY invoice_date DESC" ;

        $project_result = mysqli_query($conn, $project_sql);
        while($project_row = mysqli_fetch_assoc($project_result)){
            $data[] = $project_row;
        }
    }
    echo json_encode(array("data"=>$data));
?>
