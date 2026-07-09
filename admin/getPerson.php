<?php
    require_once __DIR__ . '/dbconnect.php';
    require_once __DIR__ . '/../security.php';
    require_once __DIR__ . '/../foundation.php';
    oecrm_require_admin_login();
    oecrm_require_permission($conn, 'projects', 'view');
    $proj_id = oecrm_int_param($_POST, 'proj_id');
    $data = new stdclass();

    if (isset($_POST['proj_id'])) {
        $project_sql = "select * from projectstbl where id = $proj_id limit 1" ;

        $project_result = mysqli_query($conn, $project_sql);

        if (mysqli_num_rows($project_result) > 0) {
            $project_row = mysqli_fetch_assoc($project_result);
            $data->customerName = $project_row['customerName'];
        }
        
    }
    echo json_encode(array("data"=>$data));
?>
