<?php
/************
*
*	Tractament de magic mentre no hi ha intro, part ajax omple a un div de index anomenat dades
*	també fa el hack de mostrar-se dintre de index.php quan es el que necesitem alla
*	es el mateix que ensenyem aqui /n venedor p.ex.
*
************/
require("login.php");
if($_REQUEST['key']==$tlineakey){
	$_SESSION['login']==TRUE;
	$login=true;
	$prefixrelacio=date('Y')."_Empresa_"."{$_REQUEST['ID_Empresa']}".'_';
}
if($login){
	$pes=mysql1r("Select pes from ultimpes where ID=$idbascula",$link);
    $ID=$_REQUEST['ID'];
	if(isset($_SESSION['lasttlineaid'])){
		if($ID!=$_SESSION['lasttlineaid'])if($_SESSION['user']=='over')$old_error_handler = set_error_handler("myErrorHandler");
		$_SESSION['lasttlineaid']==$ID;
	}
	if($ID['0']=='-'){
		$ID=substr($ID,1);
		$abono='-';
	}
    if(is_numeric($ID['0'])){
        if(!strpos($ID,'*')&&!strpos($ID,'+'))$ID=$ID;
        if((!strpos($ID,'*')) && ( strpos($ID,'+')))list($ID,$lotnumber)=explode("+",$ID);
        if(strpos($ID,'*')&&!strpos($ID,'+'))list($ID,$units)=explode("*",$ID);
        if(strpos($ID,'*')&&strpos($ID,'+')){
                list($ID,$units)=explode("*",$ID);
                list($units,$lotnumber)=explode("+",$units);
        }
    }
    //else $ID=$ID;
    switch($ID){
        // TO DO case si ID leng es codi de barres!
	case '-10':
			//afegeixo un 10% de descompte a l'ultima línea.
			$scaixa="style =\"background:#ACAB00; position:absolute; top:120px; left:5px; width:580px; height:815px; font-size:33px;padding:1%;\"";
       	divclass('caixadescripcio',$scaixa);
			echo "Afegirè un 10% de descompte a l'ultima fila entrada :<br/>".urldecode($_SESSION['lastline'][$_SESSION['lastline']['table']]['description']);
// 			pre($_SESSION['lastline']);
			tdiv();
			break;
    case '':
        if($w<$h)divclass('pes',"style =width:".($w-25)."px;");
		else divclass('pes',"style = height:".($h/5)."px;top:2px;width:".(($w/2)-15)."px;");
			echo "{$abono}".number_format(($pes/1000),3,$decimalsing,$thousandsing);
			echo "Kg&nbsp;";
		tdiv();
        if($w<$h)divclass('caixapreu',"style =width:".($w-25)."px;");
        else divclass('caixapreu',"style =height:".($h/5)."px;top:".(($h/5)+2)."px;width:".(($w/2)-15)."px;");
			mysqli_query($link,"UPDATE `ultimpes` SET `preu` = '0' WHERE `ID` = '$idbascula';");
			echo"€/Kg&nbsp;";
		tdiv();
		if($w<$h)divclass('caixatlinea',"style =width:".($w-25)."px;");
		else divclass('caixatlinea',"style =height:".($h/5)."px;top:".(2*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
			echo "€&nbsp;";
		tdiv();

		if($w<$h)divclass('caixadescripcio',"style =width:".($w-25)."px;");
		else divclass('caixadescripcio',"style =height:".(2*($h/5))."px;top:".(3*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
		if($w<$h)divclass('Descripcio');
		else divclass('Descripcio',"style =width:".(($w/2)-5)."px;text-aling:center;");
			echo "entra <br/> codi o *, /, +, -";
// 			pre($_REQUEST['w']);
			tdiv();
		tdiv();
        break;
    case ((is_numeric($ID['0']))&&(is_numeric($ID))&&($ID!='')):
        //$codi=substr
        $sql="select exist,value, formatpvp, bulk, pvp from scalelist where ID=$ID ";
        //echo 'entro';
        $o= mysql2vector($sql,$link);
        if($o[0]==1){#article existeix
			if($w<$h)divclass('caixapreu',"style =width:".($w-25)."px;");
        	else divclass('caixapreu',"style =height:".($h/5)."px;top:".(($h/5)+2)."px;width:".(($w/2)-15)."px;");
			echo $o[2].'&nbsp;</div>';
				if($o[3]==1){
					if(is_numeric($units)&&($units!=" ")){
							if($w<$h)divclass('pes',"style =width:".($w-25)."px;");
							else divclass('pes',"style = height:".($h/5)."px;top:2px;width:".(($w/2)-15)."px;");
						echo "{$abono}".number_format($units,3,
											$decimalsing,$thousandsing)."Kg&nbsp;";
								tdiv();
						if($w<$h)divclass('caixatlinea',"style =width:".($w-25)."px;");
						else divclass('caixatlinea',"style =height:".($h/5)."px;top:".(2*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
						echo "{$abono}".number_format($o[4]*$units,2,$decimalsing,$thousandsing).'€&nbsp;';
								tdiv();
						}
						else{
							if($w<$h)divclass('pes',"style =width:".($w-25)."px;");
							else divclass('pes',"style = height:".($h/5)."px;top:2px;width:".(($w/2)-15)."px;");
			echo "{$abono}".number_format(($pes/1000),3,
										$decimalsing,$thousandsing); echo "Kg&nbsp;";tdiv();
		if($w<$h)divclass('caixatlinea',"style =width:".($w-25)."px;");
		else divclass('caixatlinea',"style =height:".($h/5)."px;top:".(2*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
							echo"{$abono}".
							number_format(($pes/1000)*$o[4],2,$decimalsing,$thousandsing).'€&nbsp;';
							tdiv();
							$preu=$o[4]*100;
							mysqli_query($link,"UPDATE `ultimpes` SET `preu` = '$preu' WHERE `ID` = '$idbascula';");
						}
		}
		else{
			if(is_numeric($units)&&($units!=" ")){
				if($w<$h)divclass('pes',"style =width:".($w-25)."px;");
				else divclass('pes',"style = height:".($h/5)."px;top:2px;width:".(($w/2)-15)."px;");
		echo "{$abono}".number_format_trim( $units,3,$decimalsing,$thousandsing).
					" unitats&nbsp;";tdiv();
				if($w<$h)divclass('caixatlinea',"style =width:".($w-25)."px;");
				else divclass('caixatlinea',"style =height:".($h/5)."px;top:".(2*(($h/5)+2))."px;width:".(($w/2)-15)."px;");

				echo "{$abono}".
					number_format( $o[4]*$units,2,$decimalsing,$thousandsing).'€'; tdiv();
			}
			else{
				if($w<$h)divclass('pes',"style =width:".($w-25)."px;");
				else divclass('pes',"style = height:".($h/5)."px;top:2px;width:".(($w/2)-15)."px;");
				echo "{$abono}1 unitat&nbsp;";tdiv();
				if($w<$h)divclass('caixatlinea',"style =width:".($w-25)."px;");
				else divclass('caixatlinea',"style =height:".($h/5)."px;top:".(2*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
				echo "{$abono}".number_format( $o[4],2,$decimalsing,$thousandsing).
					'€&nbsp;'; tdiv();
			}
			}
           	if($w<$h)divclass('caixadescripcio',"style =width:".($w-25)."px;");
			else divclass('caixadescripcio',"style =height:".(2*($h/5))."px;top:".(3*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
			if($w<$h)divclass('Descripcio');
			else divclass('Descripcio',"style =width:".(($w/2)-5)."px;text-aling:center;");
			echo urldecode($o['1']);tdiv();tdiv();
        }
		else{
            if($w<$h)divclass('pes',"style =width:".($w-25)."px;");
			else divclass('pes',"style = height:".($h/5)."px;top:2px;width:".(($w/2)-15)."px;");
		echo "{$abono}".number_format(($pes/1000),3,$decimalsing,$thousandsing); 
			echo " Kg&nbsp;";tdiv();
            if($w<$h)divclass('caixapreu',"style =width:".($w-25)."px;");
   	        else divclass('caixapreu',"style =height:".($h/5)."px;top:".(($h/5)+2)."px;width:".(($w/2)-15)."px;");
			echo"€/Kg&nbsp;";tdiv();
            mysqli_query($link,"UPDATE `ultimpes` SET `preu` = '0' WHERE `ID` = '$idbascula';");
            if($w<$h)divclass('caixatlinea',"style =width:".($w-25)."px;");
			else divclass('caixatlinea',"style =height:".($h/5)."px;top:".(2*(($h/5)+2))."px;width:".(($w/2)-15)."px;");	echo "€&nbsp;";tdiv();
            if($w<$h)divclass('caixadescripcio',"style =width:".($w-25)."px;");
			else divclass('caixadescripcio',"style =height:".(2*($h/5))."px;top:".(3*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
			if($w<$h)divclass('Descripcio');
			else divclass('Descripcio',"style =width:".(($w/2)-5)."px;text-aling:center;");
			echo "Codi no vàlid";tdiv();tdiv();
        }
        break;
    case ($ID[0]=='+')://preu directe
        list($preu,$altres)=explode('*',substr($ID,1));
        list($units,$lotnumber)=explode('+',$altres);
        if (is_numeric($units)){
			if($w<$h)divclass('pes',"style =width:".($w-25)."px;");
			else divclass('pes',"style = height:".($h/5)."px;top:2px;width:".(($w/2)-15)."px;");
			echo "{$abono}".number_format($units,2,$decimalsing,$thousandsing)."U&nbsp;";tdiv();
            if($w<$h)divclass('caixapreu',"style =width:".($w-25)."px;");
			else divclass('caixapreu',"style =height:".($h/5)."px;top:".(($h/5)+2)."px;width:".(($w/2)-15)."px;");
			echo number_format($preu,2,$decimalsing,$thousandsing)."€/U&nbsp;";tdiv();
            if($w<$h)divclass('caixatlinea',"style =width:".($w-25)."px;");
			else divclass('caixatlinea',"style =height:".($h/5)."px;top:".(2*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
			echo "{$abono}".number_format( $preu*$units,2,
						$decimalsing,$thousandsing).'€&nbsp;'; tdiv();
            if($w<$h)divclass('caixadescripcio',"style =width:".($w-25)."px;");
			else divclass('caixadescripcio',"style =height:".(2*($h/5))."px;top:".(3*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
		if($w<$h)divclass('Descripcio');
		else divclass('Descripcio',"style =width:".(($w/2)-5)."px;text-aling:center;");
			echo "Article sense codi amb un iva del 10%";tdiv();tdiv();

        }
        else {
            if($w<$h)divclass('pes',"style =width:".($w-25)."px;");
			else divclass('pes',"style = height:".($h/5)."px;top:2px;width:".(($w/2)-15)."px;");
			echo "{$abono}".number_format(($pes/1000),3,$decimalsing,$thousandsing);
			echo "Kg&nbsp;";tdiv();
            if($w<$h)divclass('caixapreu',"style =width:".($w-25)."px;");
        	else divclass('caixapreu',"style =height:".($h/5)."px;top:".(($h/5)+2)."px;width:".(($w/2)-15)."px;");
			echo number_format($preu,2,$decimalsing,$thousandsing)."€/Kg&nbsp;";tdiv();
			if($w<$h)divclass('caixatlinea',"style =width:".($w-25)."px;");
			else divclass('caixatlinea',"style =height:".($h/5)."px;top:".(2*(($h/5)+2))."px;width:".(($w/2)-15)."px;");			echo "{$abono}".number_format( ($pes/1000)*$preu,2,$decimalsing,$thousandsing).'€&nbsp;';
			tdiv();
            $preu=$preu*100;
            mysqli_query($link,"UPDATE `ultimpes` SET `preu` = '$preu' WHERE `ID` = '$idbascula';");
            if($w<$h)divclass('caixadescripcio',"style =width:".($w-25)."px;");
			else divclass('caixadescripcio',"style =height:".(2*($h/5))."px;top:".(3*(($h/5)+2))."px;width:".(($w/2)-15)."px;");
		if($w<$h)divclass('Descripcio');
		else divclass('Descripcio',"style =width:".(($w/2)-5)."px;text-aling:center;");
			echo "Article sense codi amb un iva del 10%";tdiv();tdiv();
        }
        break;
	case (($ID['0']=='/')&&(is_numeric($ID['1'])&&substr($ID,-1)=='.'))://recarrego ultim
			$ID_Empresa_a_mxgestio=mysql1r("select ID_Empresa_a_mxgestio FROM Empresa where ID=$ID_Empresa ",$link);
// 			echo 'recarrego';
		  	$ID_Venedor=substr($ID,1,-1);
		    $hiharelaciooberta=mysql1r("select count(*)FROM {$prefixrelacio}index_relacio WHERE ID_venedor=$ID_Venedor  and relacio_oberta=1 And `editable`='1'",$link);// and ID_tipus_relacio=$ID_tipus_relacio
		  	if($hiharelaciooberta==0){
				if($ID_Empresa_a_mxgestio==1)$tipus=4;//reobro la proforma o bé la factura *depen si té un programa de facturació a part o no.
				else $tipus=3;
				$qultima="select MAX(ID) from {$prefixrelacio}index_relacio WHERE ID_venedor=$ID_Venedor  and relacio_oberta=0 and ID_tipus_relacio=$tipus and editable=1";
				$ultima=mysql1r($qultima,$link);
				//debugtelegram($qultima);
				if($ultima>0)$link->query("UPDATE {$prefixrelacio}index_relacio SET relacio_oberta=1 WHERE ID=$ultima and ID_tipus_relacio=$tipus");
			}
			$link->close();
		  	echo "<script>location.replace(\"{$scale_uri}\")</script>";
			break;
	case (($ID['0']=='*')&&($ID['1']=='*')&&((is_numeric(substr($ID,2))))):
		//mostro etiqueta
		$scaixa="style =\"background:#ACAB00; position:absolute; top:120px; left:5px; width:580px; height:815px; font-size:33px;padding:1%;\"";
       	divclass('caixadescripcio',$scaixa);
		$codi=substr($ID,2);
	 	$a=$link->query("select * from scalelist where id='$codi'")->fetch_assoc();
		if($a['id']==$codi){
			echo"Imprimir: $codi:".brr().urldecode($a['value']);
			br();
			echo "PVP: ".$a['formatpvp'];
		}
		else echo "Codi $codi, no vàlid";
		//pre();print_r($a);tpre();
		//echo file_get_contents("http://{$_SERVER['HTTP_HOST']}/scale/etiqueta.php?key=$tlineakey&ID=".substr($ID,2));
		tdiv();
			$link->close();
			die;
			break;
	case (($ID['0']=='*')&&(is_numeric(substr($ID,1)))):
			//echo 'modifico unitats ultima línea';
			$link->close();
			if (is_numeric(substr($ID,1))){
				$scaixa="style =\"background:#ACAB00; position:absolute; top:120px; left:5px; width:580px; height:815px; font-size:33px;padding:1%;\"";
       			divclass('caixadescripcio',$scaixa);
// 				pre($_SESSION);
				echo "Canvio les unitats de: ".brr(). urldecode($_SESSION['lastline'][$_SESSION['lastline']['table']]['description']);
				tdiv();
			}
			else echo"<script> window.location.href = \"{$scale_uri}\";</script>";
// 			$ID_Venedor=substr($ID,1);
// 			$FROMindex_relacio="FROM {$prefixrelacio}index_relacio WHERE ID_venedor=$ID_Venedor and relacio_oberta=1 and ID_tipus_relacio=$ID_tipus_relacio";
// 			$r=mysql2vectorassoc("SELECT * $FROMindex_relacio",$link)or header ;
			break;

    case (($ID['0']=='/')&&((is_numeric($ID['1'])))):
	//mostra el subtotal només implementat per *nvenedor anyway
	//list($accio,$venedor)=explode('*'||'/',$ID);
// 			echo 'subtotal';
	$ID_venedor=substr($ID,1);
	if($w<$h)$scaixa="style =\"background:#ACAB00; position:absolute; top:120px; left:5px; width:580px; height:815px; font-size:33px;padding:1%;\"";
	else $scaixa="style =\"background:#ACAB00; position:absolute; top:10px; left:5px; width:".(($w/2)-30)."px; height:815px; font-size:33px;padding:1%;\"";
       	divclass('caixadescripcio',$scaixa);
		$FROMindex_relacio="FROM {$prefixrelacio}index_relacio WHERE ID_venedor=$ID_venedor and relacio_oberta=1 ";//and ID_tipus_relacio=$ID_tipus_relacio";
		//$ID_relacio=mysql1r("SELECT ID $FROMindex_relacio",$link);
		$r=$link->query("SELECT max(ID) AS ID $FROMindex_relacio")->fetch_assoc();
		if($r['ID']!=''){
			echo"<script> window.location.href = \"{$scale_uri}?search=$ID&m[estat]=subtotal\";</script>";
		}
		else echo "No hi ha cap tiquet obert, vols recuperar el anterior? <br/>
					Has de posar /$ID_venedor. per carregar l'anterior";
		tdiv();
		break;
	} //s'acaba el switch id
	// aqui fem el peu de pagina, amb l'ultima línea, el rellotge i els tiquets del dia

	if($w<$h)div('hora',"style =\"position:absolute; top:680"."px; left:".(($w)-400)."px; width:".($w)."px;  height:13px; font-size:15px;padding:2px;\"");
	else div('hora',"style =\"position:absolute; bottom:".(15)."px; left:".(($w/2)-150)."px; width:".($w/2)."px;  height:13px; font-size:15px;padding:2px;\"");
	echo "id:{$ID_Empresa}.$idbascula ".date("H:i:s");
	tdiv();
    tdiv();
}
else echo "nologin";
// require('tanca.php');
?>
