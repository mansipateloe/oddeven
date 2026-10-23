<?php
require __DIR__.'/../../dbconnect.php';
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
mysqli_query($conn,'CREATE TEMPORARY TABLE designation (id INT PRIMARY KEY, designation VARCHAR(100)) ENGINE=InnoDB');
mysqli_query($conn,'CREATE TEMPORARY TABLE employeestbl (id INT PRIMARY KEY, designation VARCHAR(100)) ENGINE=InnoDB');
function oecrm_int_param($source,$key){return (int)($source[$key]??0);}
$source=file_get_contents(__DIR__.'/../../admin/updateDesignation.php');
$start=strpos($source,'$id = oecrm_int_param');$end=strpos($source,'if ($saved) {');
$handler=substr($source,$start,$end-$start);
$cases=[['rename',1,'Senior Developer',true],['duplicate',1,'manager',false],['blank',1,'  ',false],['missing',999,'Architect',false],['unchanged',1,'Developer',true]];
foreach($cases as [$label,$testId,$newName,$expected]){
 mysqli_query($conn,'DELETE FROM designation');mysqli_query($conn,'DELETE FROM employeestbl');
 mysqli_query($conn,"INSERT INTO designation VALUES (1,'Developer'),(2,'Manager')");
 mysqli_query($conn,"INSERT INTO employeestbl VALUES (1,'Developer'),(2,'Developer'),(3,'Manager')");
 $_SESSION=[];$_POST=['id'=>$testId,'designation'=>$newName];eval($handler);
 if($saved!==$expected)throw new Exception('Wrong result: '.$label);
 $row=mysqli_fetch_assoc(mysqli_query($conn,'SELECT designation FROM designation WHERE id=1'));
 $employee=mysqli_fetch_assoc(mysqli_query($conn,'SELECT designation FROM employeestbl WHERE id=1'));
 $expectedName=$expected?trim($newName):'Developer';
 if($row['designation']!==$expectedName||$employee['designation']!==$expectedName)throw new Exception('Assignment inconsistency: '.$label);
 if(mysqli_fetch_assoc(mysqli_query($conn,'SELECT designation FROM employeestbl WHERE id=3'))['designation']!=='Manager')throw new Exception('Unrelated employee changed');
 echo 'PASS: '.$label.PHP_EOL;
}
mysqli_close($conn);
