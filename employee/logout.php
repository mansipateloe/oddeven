<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
if(!empty($_SESSION['employeeId'])){$actor=(int)$_SESSION['employeeId'];$company=oecrm_current_company_id($conn);oecrm_auth_audit($conn,'employee',$actor,$company,'logout','Employee portal logout',$actor);}
$_SESSION=[];if(ini_get('session.use_cookies')){$params=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);}session_destroy();header('Location: index.php');exit;
