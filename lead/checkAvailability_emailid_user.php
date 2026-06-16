<?php
require_once("process/config.php");


if(!empty($_REQUEST["str"])) 
{
  $result = mysqli_query($conn,"SELECT count(*) FROM cp_users WHERE emailid='" . $_REQUEST["str"] . "'");
  $row = mysqli_fetch_row($result);
  $user_count = $row[0];
  if($user_count>0) {
      echo 1;
  }else{
      echo 0;
  }
}
?>