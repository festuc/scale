<?php
require("login.php");
// pre($_REQUEST);
// die;
if($_REQUEST['singkey']==$singkey)$login=true;
$_REQUEST['id']=urlencode($_REQUEST['id']);
if ($login==false){$link->close();header("Location: {$scale_uri}");$link->close();die;}
mysql_quer("INSERT INTO `SingInAtWork` (`ID`,`ID_Empresa`,`diahora`)
VALUES ('{$_REQUEST['id']}', '{$_REQUEST['ID_Empresa']}', now());",$link);
$scale_uri=urldecode($_REQUEST['return_uri']);
// a($scale_uri);die;
$link->close();header("Location: {$scale_uri}");die;
?>