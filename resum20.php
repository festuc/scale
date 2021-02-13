<!--/aquest arxiu retorna el preu i la descripció d'un article /-->
<?php
require("login.php");
if(!$login){header("Location: {$scale_uri}");$link->close(); die;}
$prefixrelacio="2020_Empresa_"."$ID_Empresa".'_';
// $dies="select data from  {$prefixrelacio}index_relacio where data > curdate()-8 and data < curdate()  group by data";

$dies="select data from  {$prefixrelacio}index_relacio where data >='2020/01/01' and data <= '2020/12/01'  group by data";
// $dies="select data from  {$prefixrelacio}index_relacio where data >='2020/12/16' and data <= curdate() group by data";
echo $dies;
br();
$qdies=$link->query($dies) or die($link->error());
while($d=$qdies->fetch_assoc()){
		$q="select * from {$prefixrelacio}index_relacio WHERE `data`='{$d['data']}' ";//and (`ID`='1038' or `ID`='1001')";
		$q="select * from `{$prefixrelacio}index_relacio` WHERE `data`='{$d['data']}' ";//and (`ID`='1038' or `ID`='1001')";
		$qlist=$link->query($q);
		echo $q;
		br();
		unset($t);
		while($rel=$qlist->fetch_assoc()){
		$r=detallrelacio($rel['uuid'],$ID_Empresa,2020,$link);
//	 	pre($r);
		/*
		tot aixó no ho necesitem per ara
		foreach ($r['IVA']as $iva=>$viva){
				$t['B'][$iva]=$t[ 'B'][$iva]+$r['B'][$iva];
				$t['T'][$iva]=$t[ 'T'][$iva]+$r['T'][$iva];
		}*/
		$t['via'][$r['ID_Pagament']][$r["ID_Venedor"]]["Total"]=$t['via'][$r['ID_Pagament']][$r["ID_Venedor"]]["Total"]+$r["Total"];

		$t['venedor'][$r["ID_Venedor"]]["Total"]=$t['venedor'][$r["ID_Venedor"]]["Total"]+$r["Total"];
		$t['via'][$r['ID_Pagament']]["Total"]=$t['via'][$r['ID_Pagament']]["Total"]+$r["Total"];
		$t3=$t3+$r["Total"];
		$t['dia']=$t['dia']+$r["Total"];
		$t2['via'][$r['ID_Pagament']][$r["ID_Venedor"]]["Total"]=$t2['via'][$r['ID_Pagament']][$r["ID_Venedor"]]["Total"]+$r["Total"];
		$t2['venedor'][$r["ID_Venedor"]]["Total"]=$t2['venedor'][$r["ID_Venedor"]]["Total"]+$r["Total"];
		$t2['via'][$r['ID_Pagament']]["Total"]=$t2['via'][$r['ID_Pagament']]["Total"]+$r["Total"];
	}
		foreach ($t['via']as $ID_Pagament => $valors){
		echo  "cobrat en ".mysql1r("select value from Pagament where ID=$ID_Pagament",$link).': ';
		echo $valors['Total'];
		br();
	}
	echo "total diari: ". $t['dia'];br();

}
h2('tot el cicle');
	foreach ($t2['via']as $ID_Pagament => $valors){

		echo  "cobrat en ".mysql1r("select value from Pagament where ID=$ID_Pagament",$link).': ';
		echo $valors['Total'];
		br();
	}
h3($t3);
?>
