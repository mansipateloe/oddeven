<?php
require __DIR__.'/../../dbconnect.php';
foreach(['SELECT id,display_name,status FROM companies ORDER BY id','SELECT id,company_id,name,code,start_time,end_time,required_minutes,break_minutes,status FROM shifts ORDER BY id','SELECT shift_id,weekday,week_of_month,is_off FROM shift_weekly_off_rules ORDER BY shift_id,weekday,week_of_month'] as $sql){$r=mysqli_query($conn,$sql);echo json_encode(mysqli_fetch_all($r,MYSQLI_ASSOC)),PHP_EOL;}
