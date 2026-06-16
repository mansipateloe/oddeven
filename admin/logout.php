<?php
require_once __DIR__.'/dbconnect.php';require_once __DIR__.'/../security.php';require_once __DIR__.'/../foundation.php';
if(!empty($_SESSION['adminId'])){$actor=(int)$_SESSION['adminId'];$company=oecrm_current_company_id($conn);$employee=(!empty($_SESSION['is_admin'])&&(int)$_SESSION['is_admin']===0)?$actor:null;oecrm_auth_audit($conn,'admin',$actor,$company,'logout','Admin portal logout',$employee);}
$_SESSION=[];if(ini_get('session.use_cookies')){$params=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);}session_destroy();header('Location: index.php');exit;
