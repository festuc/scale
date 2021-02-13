<?php
/*Bascula personalitzada @festuc.info

This is my main script.
Almost all you see in screen is formated from there.

Basicaly is a switch running from a variable, each variable value have a user view.
'pesa' is standar view. It ask in background data to tlinea.php about witch  scale weight and code are you entered in a html form.
In the bottom (in vertical view) or in rigth side of the page it add some info about opened relation. And if the case if seller want to cash this relation, it be elevated to a invoice (factura) and close relation, and appear a option to print in termal printer.

'tracto' is a special case for evaluate whatever you put in a pesa form. for example if you put a code in it, it will go to other case for entry a new line for a relation
if you put a +number it will enter a not codifiqued product (direct price)
in these cases show a new confirm form and ask for a seller code (its a multicode seller, ofc)
if you put a --10 it make a discount in last line entered in that scale (every scale are independent so you only make a discount in last line in actual scale)
if you put a **code it will print a stiker with price of product with that code. **code*n make n copies of that
tancament, reimprimeix, sing+code are special codes what i make with a qr generator, for Sing at work, reprint some relations, close daily cash etc.

'entralinea' enter a new line into relation. It came from 'tracto'form.
'subtotal' show a special form where can seller can select what kind of relation are, assing a relation to knowed client, delete lines (in a elimina.php)

if login is not set, or is false, index.php ask for a username and password.

*/
// print_r($_REQUEST);
require('login.php');
if($login){
    $_REQUEST['m']['estat']!=false?$estat=$_REQUEST['m']['estat']:$estat='pesa';
    mysqli_query($link,"UPDATE `ultimpes` SET `preu` = '0' WHERE `ID` = '$idbascula';");
	if($_REQUEST['m']['estat']=='extra'){
		$estat='tracto';
		$_REQUEST['magic']='+'.$_REQUEST['id'];
	}
	switch ($estat){
		case 'pesa':
			estil_capcal(false);
			$_SESSION['pes0']=0;
			form($_SERVER['DOCUMENT_URI'].'#'.$c[0],'GET');
			hidden("m[estat]",'tracto');
			divclass("caixa");
				if($w<$h)divclass("caixacodi","style=\"width: ".($w-15)."px;\"");
				else divclass("caixacodi","style=\"width: ".(($w/2)+10)."px; left:".($w/2)."px\"");
					?>
					<div id="codi">
						<div style=" display:table-cell; vertical-align:middle; width:<?=$w-15?>px;height:115px; position:relative; top: 8px">
							<input type="search" name="magic" size=8 id="magic" placeholder="Codi, * / + ." class="form-control" autofocus autocomplete="off" onChange="this.form.submit()"/>
					</div>
					<?php
				tdiv();
			sumit1('e',"style =\"font-size:1px; position:absolute; left: -50px;top : -10px\"");
				tdiv();
			div("dades");

			tdiv();
			tform();
			echo'
				<script src="jquery-3.4.1.min.js"></script>
				<script>
					$(document).ready(function(){
						function tlinea(ID){
							$.ajax({
								url:"tlinea.php",
								method:"POST",
								data:{"ID":ID,"w":window.innerWidth,"h":window.innerHeight},
								success:function(data){
									$(dades).html(data);
								}
							});setInterval
						};
						// set timer
						setInterval(function() {
							document.getElementById("magic").focus();
							tlinea(document.getElementById("magic").value);
						},100)
					});
					$(\'#magic\').keyup(function(){
						function tlinea(ID){
							$.ajax({
								url:"tlinea.php",
								method:"POST",
								data:{"ID":ID},
								success:function(data){
									$(dades).html(data);
								}
							});
						};
					});
				</script>';//fi del echo dels scripts
			//
			//
			//							Peu del pesa
			//							utltima relació, relació obertes...
			//
			//
				$FROMindex_relacio="FROM
					{$prefixrelacio}index_relacio";// where ID_tipus_relacio=3";
				$qva="SELECT ID_Venedor $FROMindex_relacio where `data` = curdate()
					group by ID_Venedor ORDER BY ID_Venedor limit 6";
//  				echo $qva;
//				pre($_SESSION);
				$venedors_avui=$link->query($qva);
				if($w<$h)$mida_lletra=58-(($venedors_avui->num_rows-2)*10);
				else $mida_lletra=66-(($venedors_avui->num_rows-3)*5);
//  	$mida_lletra=45;
    			if($w<$h)$scaixa="style =\"position:absolute;background-color:#ACAB00;
					top:720px; left:-5px; width:".(($w)+8)."px;
					height:".($h-720)."px; font-size:{$mida_lletra}px; line-height: 1.7;\"";
				else $scaixa="style =\"position:absolute;
					top:110px; left: ".(($w/2)-18)."px; width:".(($w/2)+80)."px; height:".($h-17)."px;
					font-size:{$mida_lletra}px;line-height: 1.4;\"";
    			//divclass('caixadescripcio',$scaixa);
				divclass('caixatotal',$scaixa);
// 				echo "venedors actius: ".$venedors_avui->num_rows;
				while($v = $venedors_avui->fetch_object()){
					$qid="SELECT max(ID) AS ID  $FROMindex_relacio
					 	where `ID_Venedor`='{$v->ID_Venedor}'  ";
// 					echo $qid;
					$id=$link->query($qid)->fetch_assoc();
//			 		pre($id);
				$quuid="SELECT max(uuid) as uuid  $FROMindex_relacio
				 	where `ID_Venedor`='{$v->ID_Venedor}' and `data` = curdate()";//and `ID`='{$id['ID']}'";
				$uuid=$link->query($quuid)->fetch_assoc();
//		 		echo $quuid;
// 				echo $uuid['uuid'];
				$r=detallrelacio($uuid['uuid'],$ID_Empresa,date('Y'),$link);
				$nom_venedor=explode(" ",$r['Nom_Venedor']);
				if( $r['relacio_oberta']=='1'){
					a("{$scale_uri}total.php?via=1&ID={$r['ID']}&ID_tipus_relacio={$r['ID_tipus_relacio']}&uuid={$uuid['uuid']}","&#128182;");
					a("{$scale_uri}?search=/{$v->ID_Venedor}&m[estat]=subtotal",$r['activelines'].' '.substr($nom_venedor[0],0,2).' '.
						number_format($r['Total'],2,$decimalsing,$thousandsing));
					a("{$scale_uri}total.php?via=2&ID={$r['ID']}&ID_tipus_relacio={$r['ID_tipus_relacio']}&uuid={$uuid['uuid']}","&#128179;");
				}
				else{
					if($r['ID_Pagament']==2)
						a("{$scale_uri}total.php?via=1&ID={$r['ID']}&ID_tipus_relacio={$r['ID_tipus_relacio']}&uuid={$uuid['uuid']}","&#128182;");
					if($r['ID_Pagament']==1)a("{$scale_uri}total.php?via=2&ID={$r['ID']}&ID_tipus_relacio={$r['ID_tipus_relacio']}&uuid={$uuid['uuid']}","&#128179;");
					echo $r['activelines'].' '.substr($nom_venedor[0],0,2).' '.number_format($r['Total'],2,$decimalsing,$thousandsing);
					if($ID_Empresa!='2')a("{$scale_uri}tiquet.php?uuid={$r['uuid']}","&#128424;");
					else a("{$scale_uri}total.php?uuid={$r['uuid']}","&#128424;");
				}
		 		br();
					//div('pre',"style=font-size:10px;");pre($_SESSION);tdiv();
			}
			if (isset($_SESSION['lastline'])){
				$_SESSION['lastline'][$_SESSION['lastline']['table']]
					['units']>0?$b="":$b='background:red';
					if($w<$h)div('caixapreu',"style =\"$b;position:absolute; top:265px;
						left:25px;width:480px; /*width:57px; height:13px;*/
						font-size:25px;padding:2px;\"");
					else div('caixapreu',"style =\"$b;position:absolute; top:".($h-180)."px;
						left:25px;width:".($w/2)."px; /*width:57px; height:13px;*/
						font-size:25px;padding:2px;\"");
					$_SESSION['lastline'][$_SESSION['lastline']
										  ['table']]['bulk']=='1'?$ku='Kg':'Un';
					echo 'ultim: '.$_SESSION['lastline'][$_SESSION['lastline']
						['table']]['units']."$ku ".$_SESSION['lastline']
						[$_SESSION['lastline']['table']]['description'];
// 				pre($_SESSION['lastline']);
				tdiv();
			}
		break;
		case 'tracto'://quan hem rebut algo al magic
			$repesa=10;
			$ID=$_REQUEST['magic'];
			if($ID['0']=='-'){
				$ID=substr($ID,1);
				$abono='-';
			}
			if(is_numeric($ID[0])){//el primer caracter es un número, per tant un codi
				if(!strpos($ID,'*')&&!strpos($ID,'+'))$ID=$ID;//no conte ni * ni +
					if((!strpos($ID,'*')) && (strpos($ID,'+')))list($ID,$lotnumber)=explode("+",$ID);// no*si+
					if(strpos($ID,'*')&&(!strpos($ID,'+')))list($ID,$units)=explode("*",$ID);// si * no +
					if(strpos($ID,'*')&&strpos($ID,'+')){ //si * si +
						list($ID,$units)=explode("*",$ID);
						list($units,$lotnumber)=explode("+",$units);
					}
				}
			//no es un numeral, per tant nous casos apareixen... codis de barres, els que comencen amb +/*- ...
			switch ($ID){
				case '':
					$link->close();
					header("Location: $scale_uri");
				break;
				case 'tancament':
					echo "tancament";
					$link->close();
					header("Location: $scale_uri/tancament.php");
					die;
				break;
				case 'reimprimeix':
					echo "llistarel.php";
					$link->close();
					header("Location: $scale_uri/llistarel.php");
					die;
				break;
				case strpos($ID,'sing'):
					list($case,$codi)=explode('+',$ID);
					echo 'sing in at work';
					$link->close();
					header ("Location: $sing_uri?id=".urlencode($codi)."&ID_Empresa=$ID_Empresa&singkey=$singkey&return_uri=".urlencode($scale_uri));
					die;
					break;
				case'-10':
						if (!isset($_SESSION['lastline'])){
							$link->close();
							header("Location: $scale_uri?discount=no");
							die;
						}
						$l=$_SESSION['lastline'];
						$q="UPDATE `{$l['table']}` SET `discount` = '10'
						WHERE `ID` = '{$l['ID']}' AND `ID_tipus_relacio` = '{$l[$l['table']]['ID_tipus_relacio']}'
						AND `line` = '{$l[$l['table']]['line']}';";
						if($link->query($q)===FALSE){
							echo $q.mysqli_error($link);
							die;
						}
						$link->close();
						header("Location: $scale_uri?discount=yes");
				break;
				//case stripos($ID,".."):
				//		echo 'dos punts';
				//	header("Location: {$scale_uri}dospunts.php?id=$ID");
				//	die;
				//	break;
				case is_numeric($ID[0]):
					$sql="select exist,value, formatpvp, bulk, pvp from scalelist where ID=$ID ";
// 					echo $sql;br();
					$o= mysql2vector($sql,$link);
					if($o[0]==1){//codi vàlid
						if($o[3]==1){//granel
							if(is_numeric($units)&&($units!=" ")){//codi*unitats granel
								$units=$abono.$units;
								$bulk=1;
							}
							else{//codi i pes granel
								$pes=mysql1r("Select pes from ultimpes where ID=$idbascula",$link)/1000;
								if($pes==0){
									$link->close();
									sleep(0.5);
									$_SESSION['pes0']++;
// 									a("$scale_uri?m[estat]=tracto&magic={$_REQUEST['magic']}");
// 									pre($_SESSION);
									if($_SESSION['pes0']==$repesa){
										$_SESSION['pes0']=0;estil_capcal(false);
// 										div('error',"style =\"position:absolute; top:".($h/2)."px;font-size:75px;padding:2px;center;\"");
										div('error',"style =\"position:absolute; top:".($h/2)."px;left:".($h/2)."px;font-size:75px;padding:2px;center;\"");
										a($scale_uri."?w={$_SESSION['w']}&h={$_SESSION['h']}",('pes invàlid apreta aqui sobre'));
										tdiv();
									}
									else //echo "<script>location.replace(\"{$scale_uri}?m[estat]=tracto&magic={$_REQUEST['magic']}\")</script>";
									 header("Location: $scale_uri?m[estat]=tracto&magic={$_REQUEST['magic']}&w={$_SESSION['w']}&h={$_SESSION['h']}");
									die;
								}
								$units=$abono.$pes;
								$bulk=1;
							}
						}
						else{ //envasat
							if(is_numeric($units)&&($units!=" ")){//codi*unitats
								$units=$abono.$units;
								$bulk=0;
							}
							else{//codi i una unitat
								$units=$abono.'1';
								$bulk=0;
							}
						}
						$tipusIVA=mysql1r("SELECT `tipusiva` FROM `tarifes` WHERE `ultimpreu` = '1' AND `ID` = '$ID'",$link);
						$IVA=trim(mysql1r("SELECT `Value` from IVA where ID=$tipusIVA",$link),'%');
						$formularilinea=true;
						$o['1']=urldecode($o['1']);
					}
					else{//codi invalid
						$link->close();
						header("Location: $scale_uri");
						die;
					}
					//Aqui sabem que ja podrem fer el el formulari per entrar la línea a algun venedor
				break;
				case (($ID[0]=='+')&&(is_numeric($ID[1])))://preu directe
					unset ($units);
					if(strpos($ID,'*'))list($o[4],$units)=explode('*',substr($ID,1));//$o[4]=pvp
					else list($o[4],$altres)=explode('/',substr($ID,1));
					/*else list($o[4],$altres)=explode('/',substr($ID,1));
					if(strpos($altres,'/'))list($units,$IVA)=explode('/',$altres);
					else $units=$altres;
					if(!strpos($altres,'*')){
					//cal treballari més preu directe amb pes i iva diferent de 10% +2+4 per exmple j
					$IVA=$altres;
					}*/
					if (is_numeric($units)){
					 //preu directe unitats contades
						$units=$abono.$units;
						$bulk=0;
						$o[3]=0;//$o[3]=boolean_bulk
					}
					if(!is_numeric($units)){
						//preu directe unitats granel
						$o[3]=1;
						$pes=mysql1r("Select pes from ultimpes where ID=$idbascula",$link)/1000;
						if($pes==0){
							$link->close();
							sleep(0.5);
							$_SESSION['pes0']++;
							$a=true;
							if($_SESSION['pes0']>=$repesa){
								$_SESSION['pes0']=0;
								estil_capcal(false);
								div('error',"style =\"position:absolute; top:".($h/2)."px;left:".($h/2)."px;font-size:75px;padding:2px;center;\"");
								a($scale_uri."?w={$_SESSION['w']}&h={$_SESSION['h']}",('pes invàlid apreta aqui sobre'));
								tdiv();
							}
							else
								header("Location: $scale_uri?m[estat]=extra&id={$o['4']}");
							die;
						}
						$units=$abono.$pes;
						$bulk=1;
		    		}
					if($IVA=='')$IVA=$ivageneric; //$ivageneric=10 a vars.php
					$o[1]="ARTICLE GENERIC AMB  $IVA% d'IVA";
					$formularilinea=true;
					$ID=0;
					if(!$IVA==mysql1r("select value from IVA where Value='$IVA'",$link)){
						//IVA invàlid
						$link->close();
						if(!isset($a))header("Location: $scale_uri");
					}
				break;
            //case (($ID[0]=='+')&&($ID[1]=='+'))://per agafar el torn següent
			case (($ID['0']=='*')&&($ID['1']=='*')&&((is_numeric($ID['2']))))://imprimir etiquetes
					list($codi,$units)=explode('*',substr($ID,2));
					if(!isset($units))$units=1;
					if($codi==mysql1r("SELECT `id` FROM `scalelist` WHERE `id` = '$codi' ",$link )){
						$q="INSERT INTO `imprimeix` (`ID_product`, `units`, `preu_or_etiqueta`)
							VALUES ('$codi', '$units', '0');";
						if($link->query($q)===FALSE){
							echo $q.mysqli_error($link);
							die;
						}
					}
					$link->close();
					header("Location: {$scale_uri}");
			break;
			case (($ID['0']=='*')&&($ID['1']=='*')&&($ID['2']=='*')&&((is_numeric($ID['3']))))://imprimir etiquetes del producte
				list($codi,$units)=explode('*',substr($ID,3));
				if(!isset($units))$units=1;
				//if($codi==mysql1r("SELECT `id` FROM `scalelist` WHERE `id` = '$codi' ",$link )){
					$q="INSERT INTO `imprimeix` (`ID_product`, `units`, `preu_or_etiqueta`)
						VALUES ('$codi', '$units', '1');";
					if($link->query($q)===FALSE){
						echo $q.mysqli_error($link);
						die;
					}
				//}
				header("Location: {$scale_uri}");
				$link->close();
			break;
			case (($ID[0]=='*')&&(is_numeric(substr($ID,1)))): //modifico unitats úlitma línea TODO
				$l=$_SESSION['lastline'];
				if (!isset($_SESSION['lastline'])){
						$link->close();
						header("Location: $scale_uri?discount=no");
						die;
					}
				$units=urlencode(substr($ID,1));
				$q="UPDATE `{$l['table']}` SET `units` = $units*(if `units`='0', 1,`units`)
					WHERE `ID` = '{$l['ID']}' AND `ID_tipus_relacio` = '{$l[$l['table']]['ID_tipus_relacio']}'
					AND `line` = '{$l[$l['table']]['line']}';";
				if($link->query($q)===FALSE){
					echo $q.mysqli_error($link);
					die;
				}
				$link->close();
				header("Location: $scale_uri?units=yes");
			break;//final del case *n modifico unitats útima línea
			case (($ID[0]=='/')&&(is_numeric($ID[1]))): //mostra el subtotal
				//	arribo aqui si no existeix el subtotal, per que si existeix tlinea et redirigeix aixi que ...
				$link->close();
				header("Location: $scale_uri");
				echo subtotal;die;//aqui no arribo mai
			break;
			default:
				$link->close();
				header("Location: $scale_uri?$ID");
				break;
		}//final switch id de case tracto
		if($formularilinea){//creo el formularilinea per casos que hagi entrat amb codi o preu directe
			estil_capcal(false);
			//creo el formularilinea per casos que hagi entrat amb codi o preu directe
			form($_SERVER['DOCUMENT_URI'].'#'.$c[0],'GET');
			divclass("caixa","style =\"background:#f40e85;\" ");
			if($w<$h) $scaixa="style =\"background:#ACAB00;\" ";
			else $scaixa="style =\"background:#ACAB00;height:".($h/5)."px;width: ".(($w/2)-90)."px; left:".(($w/2)+80)."px\" ";
			divclass("caixacodi",$scaixa);
			if ($w<$s) echo "<div style=\" display:table-cell; text-align: center; vertical-align:middle; width:".(($w/2)-90)."px; height:119px; font-size:33px; color:#defeca; \">";
			else echo "<div style=\" display:table-cell; text-align: center; vertical-align:middle; width:594px; height:119px; font-size:33px; color:#defeca; \">";
			if($w<$h) echo '<input type="search" name="venedor" size=7 id="magic" placeholder="Codi venedor o 0" class="form-control" autofocus autocomplete="off" onChange="this.form.submit()"; style=" display:table-cell; vertical-align:middle; width:524px; height:80px; position:relative;  font-size: 33px;" />';
			else echo '<input type="search" name="venedor" size=7 id="magic" placeholder="Codi venedor o 0" class="form-control" autofocus autocomplete="off" onChange="this.form.submit()"; style=" display:table-cell; vertical-align:middle; width:'.(($w/2)-110).'px; height:80px; position:relative;  font-size: 33px;" />';
			//text_js("venedor",4,"",30,FALSE,TRUE,FALSE);
			echo"</div>";
			tdiv();
			if($w>$h)$scaixa="style =\"background:#ACAB00;top:5px\" ";
			divclass('pes',$scaixa);
			echo number_format($units,$decimalstoshowunits,$decimalsing,$thousandsing); 
			echo $o[3]==1?"Kg":"U";tdiv();
			$sep=20;
			if($w>$h)$scaixa="style =\"background:#ACAB00;top:".(($h/5)+$sep)."px\" ";
			divclass('caixapreu',$scaixa);echo number_format($o[4],$decimalstoshowmoney,$decimalsing,$thousandsing);
			echo $o[3]==1?"€/Kg":"€/U";tdiv();
			if($w>$h)$scaixa="style =\"background:#ACAB00;top:".((2*($h/5))+$sep)."px\" ";
			divclass('caixatlinea',$scaixa);echo number_format($units*$o[4],$decimalstoshowmoney,$decimalsing,$thousandsing);
			echo"€";tdiv();
			if($w>$h)$scaixa="style =\"background:#ACAB00;top:".((3*($h/5))+$sep)."px\" ";
			divclass('caixadescripcio',$scaixa);divclass('Descripcio');echo $o[1];tdiv();tdiv();
			tdiv();
			$m['ID_product']=$ID;
			$m['description']=$o[1];
			$m['units']=$units;
			//cost el modificaré sí es major al proxim estat
			$m['cost']=$o[4]/(($IVA/100)+1);
			$m['lotnumber']=$lotnumber;
			$m['IVA']=$IVA;
			$m['estat']='entralinea';
			$m['bulk']=$bulk;
			hidden('m',$m);
			sumit1("e","style =\"font-size:1px; position:absolute; left: -30px; top: -10px ;\"");tdiv();
			tform();
		}
	break;//final case tracto
	case 'entralinea':
		$ID_venedor=$_REQUEST['venedor'];
		if($ID[0]=='+'){//TODO es amb número client.
			header("Location: $scale_uri");
		}
		if((is_numeric($ID_venedor))&&($ID_venedor==mysql1r("SELECT ID from {$ID_Empresa}_Venedor where ID='$ID_venedor'",$link))){
			if(!mysql1r("SHOW TABLES LIKE '{$prefixrelacio}index_relacio' ;",$link)){
				$link->query("CREATE TABLE `{$prefixrelacio}index_relacio` LIKE `meta_index_relacio`;");
			}if(!mysql1r("SHOW TABLES LIKE '{$prefixrelacio}line';",$link)){
				$link->query("CREATE TABLE `{$prefixrelacio}line` LIKE `meta_line`;");
			}
			$ID_relacio=mysql1r("select MAX(ID) from `{$prefixrelacio}index_relacio`  where
						ID_Venedor=$ID_venedor	and relacio_oberta=1",$link); //aqui he borrat id_tipus_relació
			if(!$ID_relacio){
				$new=true;
				$ID_relacio=mysql1r("select MAX(ID)+1 from `{$prefixrelacio}index_relacio` where ID_tipus_relacio=$ID_tipus_relacio",$link);
			}
			if(!$ID_relacio){//aixo només pasa un cop l'any o per instalació nova se suposa
				$ID_relacio=1;
				$new=true;
			}
			$m["{$prefixrelacio}line"]=$_REQUEST['m'];
			$m["ID"]=$ID_relacio;
			if($new)$m["{$prefixrelacio}line"]["ID_tipus_relacio"]=$ID_tipus_relacio;//3 factura a tipus_relacio
			else $m["{$prefixrelacio}line"]["ID_tipus_relacio"]=mysql1r("select ID_tipus_relacio from `{$prefixrelacio}index_relacio`  where
						ID_Venedor=$ID_venedor	and relacio_oberta=1",$link);
			$m["do"]="new";
			$m['table']="{$prefixrelacio}line";
			$m["{$prefixrelacio}line"]["line"]=$new?1:mysql1r("Select max(line)+1 from `{$prefixrelacio}line` where ID=$ID_relacio and
															ID_tipus_relacio={$m["{$prefixrelacio}line"]["ID_tipus_relacio"]}",$link);
			if($new){//creo referencia al index_relacio
				$m2['table']=$prefixrelacio.'index_relacio';
				$m2['ID']=$ID_relacio;
				$m2['do']='new';
				$d['ID_Venedor']=$ID_venedor;
				$d['ID_client']=1;
				$d['ID_tipus_relacio']=$ID_tipus_relacio;//relació nova tipus default
				$d['fiscal_name']=$Venda_detall;
				$d['data']['mday']=date('j');
				$d['data']['mon']=date('n');
				$d['data']['year']=date('Y');
				$d['uuid']=uniqid($ID_Empresa);
				$m2[$m2['table']]=$d;
				new_update($m2,$link);
			}
			new_update($m,$link,TRUE);
			unset($_SESSION['lastline']);
			$_SESSION['lastline']=$m;
		}
		$link->close();
		header("Location: $scale_uri");
		break;
        case 'subtotal':
			estil_capcal(false);
			form($scale_uri,'GET');
			hidden("m[estat]",'tracto_subtotal');
			divclass("caixa");
				if($w<$h)divclass("caixacodi","style=\"height: 215px;\"");
				else divclass("caixacodi","style=\"top:2px;left:3px;height:".($h-10)."px;width:".($w-10)."px\"");
			$ID_venedor=substr($_REQUEST['search'],1);
			hidden('Venedor',$ID_venedor);
			$FROMindex_relacio="FROM {$prefixrelacio}index_relacio WHERE ID_venedor=$ID_venedor and relacio_oberta=1"; // and ID_tipus_relacio=$ID_tipus_relacio";
			$quuid="SELECT max(ID) AS ID,uuid $FROMindex_relacio";
			$uuid=$link->query($quuid)->fetch_assoc();
// 			pre($uuid);
			$r=detallrelacio($uuid['uuid'],$ID_Empresa,date('Y'),$link);
			if ($r['ID_client']=='1')$placeholder="&#128241; 1&#129082;&#128182; 2&#129082;&#128179;";//simbol movil 1->simbol bitllet 2->simbol tarja crèdit
			else $placeholder="1&#129082;&#128182; 2&#129082;&#128179";
			if($w<$h)echo'<div style="display:table-cell;vertical-align:left;width:580px;position:absolute;top: 8px;left:6px;" ">';
			else echo'<div style="display:table-cell;vertical-align:left;width:'.(($w/2)-30).'px;position:absolute;top: 8px;left:6px;" ">';
			echo '				<input type="search" name="magic" size=10 id="magic"  placeholder="'.$placeholder.'" class="form-control" onfocus="this.select()"  autofocus autocomplete="off" onChange="this.form.submit()" style="font-size:50px"/>';
			if(($r['ID_tipus_relacio']==3)||($r['ID_tipus_relacio']==4))$v=mysql2array("SELECT * FROM `Pagament` WHERE `ID` <= '10'",$link);
			else $v=mysql2array("SELECT * FROM Pagament",$link);
				echo'</div>';
			if($w<$h)echo'<div style=" display:table-cell; vertical-align:middle; width:580px; top:80px;position:absolute; ">';
			else echo '<div style=" display:table-cell; vertical-align:middle; width:580px;  left:'.(($w/2)-30).'px;top:2px;position:absolute; ">';
			select2llistes_js('viapagament',$v[0],$v[1],0,'no',false,"class=\"select-css\"");
					tdiv();
			tdiv();
			tdiv();
			if($w<$h)$scaixa="style =\"background:#ACAB00; position:relative; top:220px; left:5px; width:580px; height:815px; font-size:33px;padding:1%;\"";
			else $scaixa="style =\"background:#ACAB00; position:relative; top:110px; left:5px; width:".($w)."px; height:815px; font-size:33px;padding:1%;\"";
			divclass('caixadescripcio',$scaixa);

       	if ($r['ID_client']=='1')echo "Subtotal {$r['tipus_relacio']['Value']} ".mysql1r("SELECT Value from {$ID_Empresa}_Venedor where ID='{$r['ID_Venedor']}' ",$link);
		else echo "{$r['tipus_relacio']['Value']} a ".urldecode($r['fiscal_name']);
			if($w<$h)br();
			echo " Ref:".$r['activelines'];
			//hidden('r',$r);
			hidden('uuid',$uuid['uuid']);
			$total="Total";
			if($r['discount']>0)$total.=" (Desc. -{$r['discount']}%)";
			$total.=": ".number_format($r['Total'],$decimalstoshowmoney,$decimalsing,$thousandsing)."€";
			echo $total;
			$parell=0;
			$count=0;
			$_REQUEST['LSTART']=$_REQUEST['LSTART']??0;
			$_REQUEST['LEND']=$_REQUEST['LEND']??10;
			foreach (array_reverse ($r['active_lines'])as $v ){
				if($parell==0){
							div("Descripcio","style=\"position:relative;padding: 10px; background-color:  #00acab; \"");
							$parell=1;
						}
						else{
							div("Descripcio","style=\"position:relative;padding: 10px; background-color:  #acab00; \"");
							$parell=0;
							}
				$nom= $v['urlde'];
				if($w<$h)divclass('linea',"style=\"display: table-cell; text-align:left; width: 580px; \"");
				else divclass('linea',"style=\"display: table-cell; text-align:left; width:".($w-20)."px; \"");
				$linea="$nom ";
				if($w<$h)$linea.="<br/>";
				if($v['u']=='Kg')$linea.=number_format($v['units'],$decimalstoshowunits,$decimalsing,$thousandsing).$v['u'].'*';
				else $linea.=number_format_trim($v['units'],$decimalstoshowunits,$decimalsing,$thousandsing).$v['u'].'*';
				$linea.=number_format($v['cost'],$decimalstoshowmoney,$decimalsing,$thousandsing).'€/'.$v['u'].'';
				if($v['discount']>0)$linea.=' -'.number_format_trim($v['discount'],2,$decimalsing,$thousandsing).'%';
				$linea.="=";
				$linea.=number_format($v['tline'],$decimalstoshowmoney,$decimalsing,$thousandsing).'€';
				echo $linea;
				tdiv();
				divclass('elimina',"style=\"display: table-cell; text-align:right; font-size: 150%; vertical-align: middle;\"");
							a ("./elimina.php?ID={$r['ID']}&ID_tipus_relacio={$r['ID_tipus_relacio']}&line={$v['line']}&Venedor=$ID_venedor",'X');
						tdiv();
				$count++;
				tdiv();
			}
			tform();
			tdiv();
			break;
		case 'tracto_subtotal':
			$r=detallrelacio($_REQUEST['uuid'],$ID_Empresa,date('Y'),$link);
			//amb el subtotal assignem relació a client haurem d'assignar els preus, i els recarregs de equivalencia si en té
			if(mysql1r("SELECT ID_Client From Clients_phone WHERE Value='{$_REQUEST['magic']}'",$link)!=''){
				$a=asigna_relacio_per_telefon($r,trim($_REQUEST['magic']),$ID_Empresa,date('Y'),$link);
			}
			if(($_REQUEST['magic'][0]=='+')||(strlen($_REQUEST['magic'])==9))
					//assigno igualment el teléfon per enviar-lo per sms
				$a=asigna_relacio_per_telefon($r,trim($_REQUEST['magic']),$ID_Empresa,date('Y'),$link);
			if($r['phone']!='')
				$a=asigna_relacio_per_telefon($r,$r['phone'],$ID_Empresa,date('Y'),$link);
				$a=false;
			if($_REQUEST['magic']=='--5'){
				$q="UPDATE `{$prefixrelacio}index_relacio` SET `discount` = '5' WHERE  `ID` = '{$r['ID']}'
				AND `ID_tipus_relacio` = '$ID_tipus_relacio' ;";
				if($link->query($q)===FALSE){
							echo $q.mysqli_error($link);
							die;
						}
				$a=true;
			}
			if(($_REQUEST['magic']==mysql1r("select ID from Pagament where `ID`='{$_REQUEST['magic']}'",$link))
			   &&($_REQUEST['magic']>0)){
					$data="total.php?via={$_REQUEST['magic']}&ID={$r['ID']}&ID_tipus_relacio={$r['ID_tipus_relacio']}']}&uuid={$r['uuid']}";
			}
			if($_REQUEST['viapagament']>0){
					$data="total.php?via={$_REQUEST['viapagament']}&ID={$r['ID']}&ID_tipus_relacio={$r['ID_tipus_relacio']}&uuid={$r['uuid']}";
			}
			unset($_SESSION['r']);
			$_SESSION['r']=$_REQUEST['r'];
			if ($a)header("Location: $scale_uri?m[estat]=subtotal&search=/{$_REQUEST['Venedor']}");
			else header("Location: {$scale_uri}{$data}");
			break;
		}
		//fi swich estat
}//if login
else require('tanca.php');
?>
