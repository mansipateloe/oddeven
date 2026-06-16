<?php
require_once __DIR__.'/dbconnect.php';
require_once __DIR__.'/../security.php';
require_once __DIR__.'/../foundation.php';
require_once __DIR__.'/../notices.php';
oecrm_require_admin_login();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
oecrm_require_csrf();
$action=$_POST['action']??'';
$companyId=oecrm_current_company_id($conn);
$actor=(int)$_SESSION['adminId'];
try{
    if($action==='cancel'){
        $id=(int)($_POST['id']??0);
        oecrm_require_permission($conn,'notices','cancel');
        $stmt=mysqli_prepare($conn,'UPDATE notices SET status="cancelled" WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt,'ii',$id,$companyId);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
        oecrm_notice_event($conn,$id,null,'admin',$actor,'cancel','Notice cancelled');
        $_SESSION['notice_flash']='Notice cancelled.';
        header('Location: noticeCenter.php');exit;
    }
    if($action!=='save')throw new RuntimeException('Invalid action.');
    $id=(int)($_POST['id']??0);
    oecrm_require_permission($conn,'notices',$id?'edit':'create');
    $type=$_POST['notice_type']??'';
    $categoryId=(int)($_POST['category_id']??0);
    $priority=$_POST['priority']??'normal';
    $audience=$_POST['audience_type']??'all';
    $title=trim($_POST['title']??'');
    $body=trim($_POST['body_html']??'');
    $publish=str_replace('T',' ',$_POST['publish_at']??'');
    $expires=str_replace('T',' ',$_POST['expires_at']??'')?:null;
    $typeRow=mysqli_fetch_assoc(mysqli_query($conn,"SELECT type_key FROM notice_types WHERE company_id=$companyId AND status=1 AND type_key='".mysqli_real_escape_string($conn,$type)."'"));
    $category=$categoryId?mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM notice_categories WHERE id=$categoryId AND company_id=$companyId AND status=1")):true;
    if(!$typeRow||!$category||!in_array($priority,['low','normal','high','urgent'],true)||!in_array($audience,['all','department','employee'],true)||$title===''||$body===''||!strtotime($publish))throw new RuntimeException('Complete all required notice fields.');
    if($expires&&strtotime($expires)<=strtotime($publish))throw new RuntimeException('Expiry must be after publish time.');
    $targets=$audience==='department'?array_values(array_unique(array_map('intval',$_POST['department_ids']??[]))):($audience==='employee'?array_values(array_unique(array_map('intval',$_POST['employee_ids']??[]))):[]);
    if($audience!=='all'&&!$targets)throw new RuntimeException('Select at least one target.');
    $popup=isset($_POST['show_popup'])?1:0;$ack=isset($_POST['acknowledgement_required'])?1:0;
    $status=($_POST['save_mode']??'draft')==='draft'?'draft':(strtotime($publish)>time()?'scheduled':'published');
    $stored=$original=$mime='';$size=0;
    if(isset($_FILES['attachment'])&&$_FILES['attachment']['error']!==UPLOAD_ERR_NO_FILE){$stored=oecrm_safe_upload($_FILES['attachment'],__DIR__.'/../storage/notice_attachments',['pdf','doc','docx','jpg','jpeg','png','xls','xlsx']);$original=$_FILES['attachment']['name'];$mime=$_FILES['attachment']['type'];$size=(int)$_FILES['attachment']['size'];}
    mysqli_begin_transaction($conn);
    if($id){
        $existing=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notices WHERE id=$id AND company_id=$companyId"));
        if(!$existing)throw new RuntimeException('Notice not found.');
        if(!$stored){$stored=$existing['attachment_stored_name'];$original=$existing['attachment_original_name'];$mime=$existing['attachment_mime'];$size=(int)$existing['attachment_size'];}
        $stmt=mysqli_prepare($conn,'UPDATE notices SET notice_type=?,category_id=?,title=?,body_html=?,priority=?,audience_type=?,publish_at=?,expires_at=?,show_popup=?,acknowledgement_required=?,attachment_stored_name=?,attachment_original_name=?,attachment_mime=?,attachment_size=?,status=?,published_at=IF(?="published",COALESCE(published_at,NOW()),published_at) WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt,'sissssssiisssissii',$type,$categoryId,$title,$body,$priority,$audience,$publish,$expires,$popup,$ack,$stored,$original,$mime,$size,$status,$status,$id,$companyId);
    }else{
        $stmt=mysqli_prepare($conn,'INSERT INTO notices(company_id,notice_type,category_id,title,body_html,priority,audience_type,publish_at,expires_at,show_popup,acknowledgement_required,attachment_stored_name,attachment_original_name,attachment_mime,attachment_size,status,created_by,published_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,IF(?="published",NOW(),NULL))');
        mysqli_stmt_bind_param($stmt,'isissssssiisssisis',$companyId,$type,$categoryId,$title,$body,$priority,$audience,$publish,$expires,$popup,$ack,$stored,$original,$mime,$size,$status,$actor,$status);
    }
    mysqli_stmt_execute($stmt);if(!$id)$id=mysqli_insert_id($conn);mysqli_stmt_close($stmt);
    mysqli_query($conn,'DELETE FROM notice_targets WHERE notice_id='.$id);
    foreach($targets as $target){if($target<=0)continue;if($audience==='department'){$valid=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM departments WHERE id=$target AND company_id=$companyId"));if($valid)mysqli_query($conn,"INSERT INTO notice_targets(notice_id,department_id) VALUES($id,$target)");}elseif($audience==='employee'){$valid=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM employeestbl WHERE id=$target AND company_id=$companyId AND status=0"));if($valid)mysqli_query($conn,"INSERT INTO notice_targets(notice_id,employee_id) VALUES($id,$target)");}}
    mysqli_commit($conn);
    oecrm_notice_event($conn,$id,null,'admin',$actor,$status==='published'?'publish':($status==='scheduled'?'schedule':'save_draft'),'Notice saved');
    oecrm_audit($conn,'notices','save','notice',$id,'Notice saved',null,['status'=>$status,'audience'=>$audience]);
    $_SESSION['notice_flash']='Notice saved successfully.';
    header('Location: noticeCenter.php');exit;
}catch(Throwable $exception){
    @mysqli_rollback($conn);
    $_SESSION['notice_error']=$exception->getMessage();
    header('Location: noticeCenter.php');exit;
}
