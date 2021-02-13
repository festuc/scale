<?php
/************
*
* borro una linea
Delete a line in a relation.
*
************/
require("login.php");
if($login){
	$qelimina="UPDATE `{$prefixrelacio}line` SET `deleted_line` = '1'
				WHERE `ID` = '{$_REQUEST['ID']}' AND
				`ID_tipus_relacio` = '{$_REQUEST['ID_tipus_relacio']}' AND `line` = '{$_REQUEST['line']}';";
	//echo $qelimina;
	if($link->query($qelimina)===FALSE){
			echo $qelimina.mysqli_error($link);
			die;
	}
	$link->close();
	header("Location: $scale_uri?m[estat]=subtotal&search=/{$_REQUEST['Venedor']}");
}
else echo "nologin";
$link->close();
?>
