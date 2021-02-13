<?php
//login part
/*
This is a include file that create session, and set $login variable has true if login data it's correct.
Even it record some info about client screen for drawing css propouses.
It save a few variables from Empresa (enterprise, or shop) to a $_SESSION and regular variables.
It ask just one time to mariadb server for that info.




*/
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
date_default_timezone_set('Europe/Andorra');
require_once('../../keys.php');
require_once('estil.php');
require_once('taula.php');
require_once('mysqli.php');
require_once('lib.php');
require_once('vars.php');
session_start();
// if($_SESSION['user']=='over')$old_error_handler = set_error_handler("myErrorHandler");
$link=new mysqli($myserver,$myuser,$mypassword,$mydatabase);
//$lang=new lang();
$lang='ca';
if(isset($_REQUEST['tancar'])){
	session_unset();
}
//
//
//			Tema mida pantalla:
//
//
if(isset($_REQUEST['w']))$_SESSION['w']=$_REQUEST['w'];
if(isset($_REQUEST['h']))$_SESSION['h']=$_REQUEST['h'];
if (($_SESSION['vertical']==true)&&($_REQUEST['w']>$_REQUEST['h'])){
	unset ($_SESSION['w']);
	$_SESSION['vertical']=false;
}
if (($_SESSION['vertical']==false)&&($_REQUEST['h']>$_REQUEST['w'])){
	unset ($_SESSION['w']);
	$_SESSION['vertical']=true;
}
if(!isset($_SESSION['w'])){
	if(isset($_REQUEST['w']))$_SESSION['w']=$_REQUEST['w'];
	if(isset($_REQUEST['h']))$_SESSION['h']=$_REQUEST['h'];

	//echo"<script> window.location.href = \"$scale_uri?w=\"+window.innerWidth+\"&h=\"+screen.availHeight;</script>";
	if(!isset($_REQUEST['w']))echo"<script> window.location.href = \"./?w=\"+window.innerWidth+\"&h=\"+window.innerHeight;</script>";
}

	$w=$_SESSION['w'];
	$h=$_SESSION['h'];
	if($w<$h)$_SESSION['vertical']=true;

if($_SESSION['login']!=true){
    if(isset($_REQUEST['user']) &&($_REQUEST['user']!="")){
    	$_SESSION['user']=$_REQUEST['user'];
	    $_SESSION['contrasenya']=$_REQUEST["contra"];
    }
    if(isset($_SESSION['user'])&& ($_SESSION['user']!=""))
        if((mysql1r("SELECT `password` from `users` where `username`='".urlencode($_SESSION['user'])."'",$link)==$_SESSION["contrasenya"]) &
           (($_SESSION['user']!="") && $_SESSION["contrasenya"]!="")){
        $_SESSION['login']=true;
        $login=true;
    }
    else $login=false;
}
else $login=true;
if($login){
	if (!isset($_SESSION['ID_Empresa'])){
		$ID_Empresa=mysql1r("SELECT `ID_Empresa` FROM `users` where
			`username`='{$_SESSION['user']}'",$link);
		$idbascula=mysql1r("SELECT `id_bascula` FROM `users` where
			`username`='{$_SESSION['user']}'",$link);
		$scale_uri=mysql1r("SELECT `scale_uri` FROM `Empresa` where
			`ID`='{$ID_Empresa}'",$link);
		$_SESSION['Empresa']=$link->query("SELECT * FROM `Empresa` where
			`ID`='{$ID_Empresa}'")->fetch_assoc();
// 		debugtelegram($_SESSION['Empresa']);
		$_SESSION['ID_Empresa']=$ID_Empresa;
		$_SESSION['idbascula']=$idbascula;
		$_SESSION['scale_uri']=$scale_uri;
		$Empresa['scale_uri']=$scale_uri;
		$prefixrelacio=date('Y')."_Empresa_"."$ID_Empresa".'_';
	}
	else {
		$ID_Empresa=$_SESSION['ID_Empresa'];
		$idbascula=$_SESSION['idbascula'];
		$scale_uri=$_SESSION['scale_uri'];
		$Empresa['scale_uri']=$scale_uri;
		$prefixrelacio=date('Y')."_Empresa_"."$ID_Empresa".'_';
	}
}
?>
