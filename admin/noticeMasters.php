<?php
$active_menu='notices';
include 'header.php';
require_once __DIR__.'/../foundation.php';
oecrm_require_permission($conn,'notices','view');
$companyId=oecrm_current_company_id($conn);
$flash = $_SESSION['notice_type_flash'] ?? '';
unset($_SESSION['notice_type_flash']);
if($_SERVER['REQUEST_METHOD']==='POST'){
    oecrm_require_csrf();
    oecrm_require_permission($conn,'notices','edit');
    $kind=$_POST['kind']??'';
    $name=trim($_POST['name']??'');
    if($name!==''){
        if($kind==='type'){
            $key=strtolower(trim(preg_replace('/[^a-z0-9]+/i','_',$name),'_'));
            $check=mysqli_prepare($conn,'SELECT id FROM notice_types WHERE company_id=? AND (LOWER(name)=LOWER(?) OR type_key=?) LIMIT 1');
            mysqli_stmt_bind_param($check,'iss',$companyId,$name,$key);
            mysqli_stmt_execute($check);
            $exists=mysqli_fetch_assoc(mysqli_stmt_get_result($check));
            mysqli_stmt_close($check);
            if($exists){
                $_SESSION['notice_type_flash']='Notice Type already exists.';
            }else{
                $stmt=mysqli_prepare($conn,'INSERT INTO notice_types(company_id,type_key,name) VALUES(?,?,?)');
                mysqli_stmt_bind_param($stmt,'iss',$companyId,$key,$name);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $_SESSION['notice_type_flash']='Notice Type added successfully.';
            }
        }else{
            $check=mysqli_prepare($conn,'SELECT id FROM notice_categories WHERE company_id=? AND LOWER(name)=LOWER(?) LIMIT 1');
            mysqli_stmt_bind_param($check,'is',$companyId,$name);
            mysqli_stmt_execute($check);
            $exists=mysqli_fetch_assoc(mysqli_stmt_get_result($check));
            mysqli_stmt_close($check);
            if($exists){
                $_SESSION['notice_type_flash']='Notice Category already exists.';
            }else{
                $stmt=mysqli_prepare($conn,'INSERT INTO notice_categories(company_id,name) VALUES(?,?)');
                mysqli_stmt_bind_param($stmt,'is',$companyId,$name);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $_SESSION['notice_type_flash']='Notice Category added successfully.';
            }
        }
    }
}
$types=mysqli_query($conn,'SELECT * FROM notice_types WHERE company_id='.$companyId.' ORDER BY status DESC,name');
$categories=mysqli_query($conn,'SELECT * FROM notice_categories WHERE company_id='.$companyId.' ORDER BY status DESC,name');
?>
<div id="page-wrapper" class="compact-admin-page"><div class="foundation-titlebar"><a href="manageNotice.php"><i class="fa fa-arrow-left"></i> Notices</a><h2>Notice Types & Categories</h2></div><?php if($flash):?><div class="alert alert-info"><?php echo oecrm_h($flash);?></div><?php endif;?><div class="row"><div class="col-md-6"><div class="panel panel-default"><div class="panel-heading">Notice Types</div><div class="panel-body"><form method="post" class="form-inline"><?php echo oecrm_csrf_field();?><input type="hidden" name="kind" value="type"><input class="form-control" name="name" placeholder="Type name" required> <button class="btn btn-primary">Add</button></form><hr><table class="table"><?php while($row=mysqli_fetch_assoc($types)):?><tr><td><?php echo oecrm_h($row['name']);?></td><td><code><?php echo oecrm_h($row['type_key']);?></code></td></tr><?php endwhile;?></table></div></div></div><div class="col-md-6"><div class="panel panel-default"><div class="panel-heading">Notice Categories</div><div class="panel-body"><form method="post" class="form-inline"><?php echo oecrm_csrf_field();?><input type="hidden" name="kind" value="category"><input class="form-control" name="name" placeholder="Category name" required> <button class="btn btn-primary">Add</button></form><hr><table class="table"><?php while($row=mysqli_fetch_assoc($categories)):?><tr><td><?php echo oecrm_h($row['name']);?></td></tr><?php endwhile;?></table></div></div></div></div></div>
<?php include 'footer.php';?>
