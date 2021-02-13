<?php
require('login.php');
if ($login==false){$link->close();header("Location: {$Empresa['scale_uri']}");die;}
$q="select * from {$prefixrelacio}index_relacio WHERE `data`=curdate() order by ID DESC";
if ($_REQUEST['sempre']==yes)$q="select * from {$prefixrelacio}index_relacio order by ID DESC";
if ($_REQUEST['ahir']==yes)$q="select * from {$prefixrelacio}index_relacio WHERE `data`=curdate()-1 order by ID DESC";
$qlist=$link->query($q);
estil_capcal('apreteu sobre la linea per imprimir');
a("./","tornar");br();
while($rel=$qlist->fetch_assoc()){
	$r=detallrelacio($rel['uuid'],$ID_Empresa,date(Y),$link);
// 	pre($r);
	a("{$scale_uri}tiquet.php?uuid={$r['uuid']}",h2r($r['activelines'].' '.$r['Nom_Venedor'].' '.$r['active_lines'][$r['activelines']]['hidden_now'].' '.$r['Total']));
	br();
	/*
	tot aixó no ho necesitem per ara
	foreach ($r['IVA']as $iva=>$viva){
			$t['B'][$iva]=$t[ 'B'][$iva]+$r['B'][$iva];
			$t['T'][$iva]=$t[ 'T'][$iva]+$r['T'][$iva];
	}*/
}
a("./","tornar");


?>