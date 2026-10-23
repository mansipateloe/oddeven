<?php
require __DIR__.'/../../dbconnect.php';
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
mysqli_query($conn,'CREATE TEMPORARY TABLE followup_type_tbl (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100)) ENGINE=InnoDB');
mysqli_query($conn,'CREATE TEMPORARY TABLE lead_followup (id INT PRIMARY KEY, followup_type VARCHAR(100)) ENGINE=InnoDB');
class FollowupRedirect extends RuntimeException {}
function oecrm_require_csrf(){}
function oecrm_followup_type_can_manage($conn){return true;}
function oecrm_int_param($source,$key){return (int)($source[$key]??0);}
function oecrm_followup_type_flash($type,$message){$GLOBALS['flash']=[$type,$message];}
function oecrm_followup_type_redirect(){throw new FollowupRedirect();}
function oecrm_audit(...$args){}
$source=file_get_contents(__DIR__.'/../../admin/manageFollowupType.php');
$start=strpos($source,"if (\$_SERVER['REQUEST_METHOD'] === 'POST'");
$end=strpos($source,'$editType = null;');
$handler=substr($source,$start,$end-$start);
$cases=[['rename',1,'Video Call','update',true],['unchanged',1,'Call','update',true],['duplicate',1,'email','update',false],['blank',1,' ','update',false],['missing',999,'Meeting','update',false],['add normalized',0,'  Site   Visit ','add',true]];
foreach($cases as [$label,$testId,$newName,$mode,$expected]){
 mysqli_query($conn,'DELETE FROM followup_type_tbl');mysqli_query($conn,'DELETE FROM lead_followup');
 mysqli_query($conn,"INSERT INTO followup_type_tbl VALUES (1,'Call'),(2,'Email')");mysqli_query($conn,"INSERT INTO lead_followup VALUES (1,'Call'),(2,'Email')");
 $_SERVER['REQUEST_METHOD']='POST';$_POST=['id'=>$testId,'name'=>$newName,($mode==='update'?'updateFollowupType':'addFollowupType')=>1];$flash=[];
 try{eval($handler);}catch(FollowupRedirect $redirect){}
 if(($flash[0]==='success')!==$expected)throw new Exception('Wrong result: '.$label);
 $row=mysqli_fetch_assoc(mysqli_query($conn,'SELECT name FROM followup_type_tbl WHERE id=1'));
 $followup=mysqli_fetch_assoc(mysqli_query($conn,'SELECT followup_type FROM lead_followup WHERE id=1'));
 $expectedName=$expected&&$mode==='update'?trim($newName):'Call';
 if($row['name']!==$expectedName||$followup['followup_type']!==$expectedName)throw new Exception('Reference mismatch: '.$label);
 if($mode==='add'&&!mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM followup_type_tbl WHERE name='Site Visit'")))throw new Exception('Add normalization failed');
 echo 'PASS: '.$label.PHP_EOL;
}
mysqli_close($conn);
