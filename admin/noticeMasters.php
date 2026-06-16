<?php
$active_menu='notices';
include 'header.php';
require_once __DIR__.'/../foundation.php';
oecrm_require_permission($conn,'notices','view');
$companyId=oecrm_current_company_id($conn);
if($_SERVER['REQUEST_METHOD']==='POST'){
    oecrm_require_csrf();
    oecrm_require_permission($conn,'notices','edit');
    $kind=$_POST['kind']??'';
    $name=trim($_POST['name']??'');
    if($name!==''){
        if($kind==='type'){
            $key=strtolower(trim(preg_replace('/[^a-z0-9]+/i','_',$name),'_'));
            $stmt=mysqli_prepare($conn,'INSERT INTO notice_types(company_id,type_key,name) VALUES(?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name),status=1');
            mysqli_stmt_bind_param($stmt,'iss',$companyId,$key,$name);
        }else{
            $stmt=mysqli_prepare($conn,'INSERT INTO notice_categories(company_id,name) VALUES(?,?) ON DUPLICATE KEY UPDATE status=1');
            mysqli_stmt_bind_param($stmt,'is',$companyId,$name);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
$types=mysqli_query($conn,'SELECT * FROM notice_types WHERE company_id='.$companyId.' ORDER BY status DESC,name');
$categories=mysqli_query($conn,'SELECT * FROM notice_categories WHERE company_id='.$companyId.' ORDER BY status DESC,name');
?>
<div id="page-wrapper" class="compact-admin-page"><div class="foundation-titlebar"><a href="manageNotice.php"><i class="fa fa-arrow-left"></i> Notices</a><h2>Notice Types & Categories</h2></div><div class="row"><div class="col-md-6"><div class="panel panel-default"><div class="panel-heading">Notice Types</div><div class="panel-body"><form method="post" class="form-inline"><?php echo oecrm_csrf_field();?><input type="hidden" name="kind" value="type"><input class="form-control" name="name" placeholder="Type name" required> <button class="btn btn-primary">Add</button></form><hr><table class="table"><?php while($row=mysqli_fetch_assoc($types)):?><tr><td><?php echo oecrm_h($row['name']);?></td><td><code><?php echo oecrm_h($row['type_key']);?></code></td></tr><?php endwhile;?></table></div></div></div><div class="col-md-6"><div class="panel panel-default"><div class="panel-heading">Notice Categories</div><div class="panel-body"><form method="post" class="form-inline"><?php echo oecrm_csrf_field();?><input type="hidden" name="kind" value="category"><input class="form-control" name="name" placeholder="Category name" required> <button class="btn btn-primary">Add</button></form><hr><table class="table"><?php while($row=mysqli_fetch_assoc($categories)):?><tr><td><?php echo oecrm_h($row['name']);?></td></tr><?php endwhile;?></table></div></div></div></div></div>
<?php include 'footer.php';?>
