<?php
/*
Aquest script està pensat per executar-se en segon pla a través d'una crida periòdica
a la línea de comandes tipus wget --spider https://domini/scale/p.php
Només un dels ordinadors de la granja ha de fer correr això.
En cas contrari surtirien etiquetes duplicades
mireu a /etc/rc.local
This script is intended for calling from a command line.
It work in a background.
I do it for allow users do not wait untill inkscape make PostScript file and lp print it.

*/
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require_once('../../keys.php');
require_once('phpgeneral/estil.php');
require_once('phpgeneral/taula.php');
require_once('phpgeneral/mysqli.php');
require_once('phpgeneral/lib.php');
require_once('phpgeneral/etiquetapreu.php');
require_once('vars.php');
session_start();
$link=new mysqli($myserver,$myuser,$mypassword,$mydatabase);
//$lang=new lang();
$lang='ca';
$qprint=$link->query("SELECT * FROM imprimeix");
while($print=$qprint->fetch_assoc()){
	$borra="DELETE FROM `imprimeix` WHERE `ID` = '{$print['ID']}';";
    if($link->query($borra)===FALSE){ echo $q.mysqli_error($link);die;	}
	for ($i=1;$i<=$print['units'];$i++){
        if($print['preu_or_etiqueta']==0){
			$decimalsing=',';
			$thousandsing=' ';
			$ID=$print['ID_product'];
			$d=$link->query("select * from scalelist where ID='$ID'")->fetch_assoc();
			$pvp=number_format($d['pvp'],2,$decimalsing,$thousandsing);
			$nom=strtoupper(urldecode($d['value']));
			$d['bulk']==1?$u='Kg':$u='uni';
			$origen='Preguntar a botiga';
			$arxiu=etiquetapreu($ID,$pvp,$nom,$u,$origen);
			$nf=str_pad($ID,4, "0", STR_PAD_LEFT).'_'.urlencode(str_replace(' ','_',$nom)).'.svg';
			$f=fopen($nf,'w')or die ;
			fwrite($f, $arxiu);
			fclose($f);
            $scp="scp $nf $tag_destination";
           	exec ($scp) ;
            tag_print_command($nf);
			exec ("rm -f $nf");
		}
        else{ //és una etiqueta de producteecho "imprimeixo copia $i\n";
			$_SESSION['Empresa']=$link->query("SELECT * FROM `Empresa` where
			`ID`='{$_REQUEST['ID_Empresa']}'")->fetch_assoc();
// 			pre($_SESSION);
			$pri="ssh {$_SESSION['Empresa']['printerserver']} \"lp /home/festuc/Documents/etiquetes/{$print['ID_product']}.ps -d {$_SESSION['Empresa']['sticker_printer']}\" ";
// 			echo $print;
            exec ($pri);
			}
	}
}session_destroy();
$link->close();
?>
