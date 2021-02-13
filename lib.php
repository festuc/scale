<?php
function debugtelegram($debug,$valor='debug',$return=FALSE){
	global $telegramtoken,$telegramdebugchannel;
	if (is_array($debug)){
		foreach ($debug AS $k=> $v){
			$telegramme.=debugtelegram($v,"$valor".'['.$k.']',true)."\n";
		}
	}
	else $telegramme="$valor: ".urldecode($debug);
	if($return)return $telegramme;
	else $response = file_get_contents("https://api.telegram.org/bot$telegramtoken/sendMessage?chat_id=-$telegramdebugchannel&text=".urlencode($telegramme) );
}
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
   /* if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        return false;
    }
	*/
	  // $errstr may need to be escaped:
    $errstr = htmlspecialchars($errstr);

    switch ($errno) {
    case E_USER_ERROR:
        $d=debugtelegram( "<b>My ERROR</b> [$errno] $errstr<br />\n",true);
        $d.=debugtelegram( "  Fatal error on line $errline in file $errfile",true);
      	$d.=debugtelegram(", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n",true);
        $d.=debugtelegram( "Aborting...<br />\n",true);
		debugtelegram($d);
        exit(1);

    case E_USER_WARNING:
        debugtelegram("<b>My WARNING</b> [$errno] $errstr<br />\n");
        break;

    case E_USER_NOTICE:
        debugtelegram("<b>My NOTICE</b> [$errno] $errstr<br />\n");
        break;
	case E_NOTICE:
		 $_SESSION['E_NOTICE'].="E_NOTICE $errstr on on line: $errline@$errfile\n";
		break;
    default:
        debugtelegram("Unknown error type: [$errno] $errstr on line: $errline@$errfile <br />\n");
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;

}
function prefered_language(array $available_languages, $http_accept_language) {
    $available_languages = array_flip($available_languages);
    $langs = [];
    preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($http_accept_language), $matches, PREG_SET_ORDER);
    foreach($matches as $match) {
        list($a, $b) = explode('-', $match[1]) + array('', '');
        $value = isset($match[2]) ? (float) $match[2] : 1.0;
        if(isset($available_languages[$match[1]])) {
            $langs[$match[1]] = $value;
            continue;
        }
        if(isset($available_languages[$a])) {
            $langs[$a] = $value - 0.1;
        }
    }
    arsort($langs);
    return $langs;
}
function number_format_trim($value,$maxdecimal=0,$decimalsing='.',$thousandsing=','){
		return rtrim(rtrim(number_format($value,$maxdecimal,$decimalsing,$thousandsing),0),$decimalsing);
}
function normalize($cadena){
    //treu dead keys
      $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuy
bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $cadena = utf8_decode($cadena);
    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
    $cadena = strtolower($cadena);
    return utf8_encode($cadena);
}
function treuapostrof($cadena){
    return str_replace("'", ' ',$cadena);
}
function tlinea($index_relacio,$qllista,$link){
if($llista=$link->query($qllista)){
	while ($f = $llista->fetch_assoc()){
		$tlinea=round($f['units']*$f['preu'],2);
		if($f['bulk'])$f['u']='Kg';
		else $f['u']='uni';
		$f['urlde']=urldecode($f['description']);
		if($index_relacio['tax_detall']==0){
			//iva inclos: si la relacio es amb iva inclós el descompte va sobre el preu amb iva
			$f['cost']=round($f['cost']*(1+($f['IVA']/100)),2);
			$f['tline']=round($f['units']*($f['cost']*(1-($f['discount']/100))),2);
			$f['Base']=round($f['tline']*(1-($f['IVA']/100)),2);
		}
		else{//si no, el descompte va sobre el preu de base
			$f['Base']=round($f['units']*$f['cost']*(1-($f['discount']/100)),2);
			$f['tline']=round($f['Base']*(1+($f['IVA']/100)),2);
		}
		if($index_relacio['equivalency_charge']==1)
			$f['tec']=round($f['tline']*($f['EQUIVALENCY_CHARGE']/100),2);
		$f['tiva']=$f['tline']-$f['Base'];
		if($f['deleted_line']==0){//si es línea activa sumem la línea als sumatoris totals
			if($index_relacio['equivalency_charge']==1)$index_relacio['EC'][$f['EQUIVALENCY_CHARGE']]=
					$index_relacio['EC'][$f['EQUIVALENCY_CHARGE']]+$f['tec'];
			$index_relacio['Base']=$index_relacio['Base']+$f['Base'];
			$index_relacio['Total']=$index_relacio['Total']+$f['Base']+$f['tiva']+$f['tec'];
			$index_relacio['IVA'][$f['IVA']]=$index_relacio['IVA'][$f['IVA']]+$f['tiva'];
			$index_relacio['B'][$f['IVA']]=$index_relacio['B'][$f['IVA']]+$f['Base'];
			$index_relacio['T'][$f['IVA']]=$index_relacio['T'][$f['IVA']]+$f['tline']+$f['tec'];
		}
		if($f['deleted_line']==0){
			$index_relacio['active_lines'][$f['line']]=$f;
			if($f['discount']!=0)$index_relacio['discountinline']=true;
		}
		else $index_relacio['deleted_lines'][$f['line']]=$f;
	}
}
	return $index_relacio;
}
function detallrelacio($uuid,$ID_Empresa,$year,$link,$debug=false){
//retorna tota una relació dintre una matriu.
$FROMindex_relacio="FROM {$year}_Empresa_{$ID_Empresa}_index_relacio WHERE uuid='$uuid'";
if($debug)echo"SELECT * $FROMindex_relacio";
$index_relacio=$link->query("SELECT * $FROMindex_relacio")->fetch_assoc();
$FROMactive_lines="FROM {$year}_Empresa_{$ID_Empresa}_line WHERE ID_tipus_relacio='{$index_relacio['ID_tipus_relacio']}' and ID='{$index_relacio['ID']}' and line>0 and deleted_line=0 ";
$FROMdeleted_lines="FROM {$year}_Empresa_{$ID_Empresa}_line where ID_tipus_relacio='{$index_relacio['ID_tipus_relacio']}' and ID='{$index_relacio['ID']}' and line>0 and deleted_line=1";
$index_relacio['Empresa']=$link->query("SELECT * from Empresa where ID='$ID_Empresa'")->fetch_assoc();
$index_relacio['tipus_relacio']=$link->query("SELECT * from tipus_relacio where
	ID='{$index_relacio['ID_tipus_relacio']}'")->fetch_assoc();
$index_relacio['Nom_Venedor']=mysql1r("SELECT Value from {$ID_Empresa}_Venedor where ID='{$index_relacio['ID_Venedor']}'",$link);
$index_relacio['activelines']=mysql1r("SELECT COUNT(*) $FROMactive_lines",$link);
$index_relacio['deletedlines']=mysql1r("SELECT COUNT(*) $FROMdeleted_lines",$link);
$FROM="FROM {$year}_Empresa_{$ID_Empresa}_line WHERE ID_tipus_relacio='{$index_relacio['ID_tipus_relacio']}' and ID='{$index_relacio['ID']}' and line>0";
$qllista="SELECT * $FROM ORDER BY line";
//poso els contadors a zero perque si fas un print_r estiguin a la part de dalt, no per a res més
$index_relacio['Base']=0;
$index_relacio['Total']=0;
$index_relacio['discountinline']=false;
// $qiva=$link->query("SELECT * from IVA ORDER BY Value");
$qiva=$link->query("SELECT IVA $FROMactive_lines group by IVA ORDER BY IVA");
while($iva=$qiva->fetch_assoc()){
	$index_relacio['IVA'][$iva['IVA']]=0;
	$index_relacio['B'][$iva['IVA']]=0;
	$index_relacio['T'][$iva['IVA']]=0;
// 	$qec=$link->query("SELECT * from equivalency_charge where ID_iva={$iva['ID']}");
	$qec=$link->query("SELECT EQUIVALENCY_CHARGE $FROMactive_lines AND IVA='{$iva['IVA']}' group by EQUIVALENCY_CHARGE ");
	while($eq=$qec->fetch_assoc()){
		$index_relacio['EC'][$eq['EQUIVALENCY_CHARGE']]=0;
	}
}
if($index_relacio['equivalency_charge']==1)$index_relacio['tax_detall']=1;//per assegurar-se
$index_relacio=tlinea($index_relacio,$qllista,$link);	//$index_relacio=tlinea($index_relacio,false,$qllista_eliminades,$link);
//aplicar el descompte total si convé TODO gairebe acabat
if($index_relacio['discount']!=0){
	if($index_relacio['tax_detall']==0)		{
// 			echo "hi ha discount sense detall";
		$index_relacio['Total']=0;
		$index_relacio['Base']=0;
		//fer el descompte per cada [t][iva]
		foreach ($index_relacio['T'] as $k=>$v){
			$nou=round($v*(1-($index_relacio['discount']/100)),2);
			$index_relacio['T'][$k]=$nou;
			$index_relacio['Total']=$nou+$index_relacio['Total'];
			$index_relacio['IVA'][$k]=$nou-round($nou/(1+($k/100)),2);
			$index_relacio['B'][$k]=$nou-$index_relacio['IVA'][$k];
			$index_relacio['Base']=$index_relacio['Base']+$index_relacio['B'][$k];
		}
	}
	else{
		$index_relacio['Total']=0;
		$index_relacio['Base']=0;
		//fer el descompte per cada b[iva]
		foreach ($index_relacio['B'] as $k=>$v){
			$nou=round($v*(1-($index_relacio['discount']/100)),2);
			$index_relacio['B'][$k]=$nou;
			$index_relacio['IVA'][$k]=round($nou*($k/100),2);
			$index_relacio['Base']=$index_relacio['Base']+$nou;
			$tax_ec=mysql1r("SELECT equivalency_charge $FROMactive_lines and IVA='$v'",$link);
			$index_relacio['EC'][$tax_ec]=$index_relacio['B'][$k]+round($nou*($tax_ec/100),2);
			$index_relacio['T'][$k]=$nou+$index_relacio['IVA'][$k]+$index_relacio['EC'][$tax_ec];
			$index_relacio['Total']=$index_relacio['T'][$k]+$index_relacio['Total'];
		}
	}
}
return $index_relacio;
}
function sms($r){
	global $decimalsing,$thousandsing,$link;
	$wa2.=$r['Empresa']['Value']."\n";
	$data=latindate($r['data']);
	$wa2.="NIF:{$r['Empresa']['NIF']} Data:$data\n";
	$wa2.=$r['tipus_relacio']['Value'].': '.$r['Empresa']['ID'].'.'.$r['ID']." en format SMS\n";
	$wa2.=urldecode($r['fiscal_name']);
	if($r['NIF']!='')$wa2.=" NIF: {$r['NIF']}";
	$wa2.="\n";
	if($r['tax_detall']==0){
		$espaidescripcio=$espaidescripcio-14;
		$$wa2.='Preus IVA inclós\n';
	}
	else $wa2.='Preus IVA no inclós\n';
	foreach ($r['active_lines'] as $v){
// 		$tex.=str_replace('%','\%','\raggedleft{'.$v['ID_product'].'}&'.$v['urlde']);
		$wa2.=$v['urlde'];
		if($v['lotnumber']!='')$wa2.=" Lot: {$v['lotnumber']}";
 		$wa2.=" ";
		if($v['bulk']==0)$wa2.=number_format_trim($v['units'],3,$decimalsing,$thousandsing);
		else $wa2.=number_format($v['units'],3,$decimalsing,$thousandsing);
		$wa.=$v['u']." ".number_format($v['cost'],2,$decimalsing,$thousandsing)."€/{$v['u']}";
		if($v['discount']>0)$wa2.=' -'.number_format_trim($v['discount'],2,$decimalsing,$thousandsing).'% ';
		if($r['tax_detall']==1){
			$tex.=''.number_format_trim($v['IVA'],2,$decimalsing,$thousandsing).'\% ';
			$tex.="".number_format($v['Base'],2,$decimalsing,$thousandsing)."€"."\n";
		}
		else $tex.="&".number_format($v['tline'],2,$decimalsing,$thousandsing)."€"."\n";
	}
	return $wa2;
}
function relacio2whatsapp($r,$wa2){

	$wa="https://api.whatsapp.com/send?phone=";
	if($r['phone'][0]=='+')$wa.=substr($r['phone'],1);
	else $wa.="34{$r['phone']}";
	$wa.="&text=";
	$wa2=urlencode($wa2);
	return $wa.$wa2;
}
function asigna_relacio_per_telefon($r,$tel,$ID_Empresa,$year,$link,$debug=FALSE){
	$a=true;
	if($debug)pre($r);
	$ID_Client=mysql1r("SELECT ID_Client FROM Clients_phone WHERE Value='$tel'  ",$link,$debug);
	if($ID_Client!=''){
		$nouclient=$link->query("select * from Clients where ID='$ID_Client' AND bool_Active=1")->fetch_assoc()or mysqli.error() ;
// 		if($ID_Client!=$nouclient['ID'])$a=false;
		$FROMindex_relacio="FROM {$year}_Empresa_{$ID_Empresa}_index_relacio WHERE uuid='{$r['uuid']}'";
		$FROM="FROM {$year}_Empresa_{$ID_Empresa}_line WHERE ID_tipus_relacio='{$r['ID_tipus_relacio']}' and ID='{$r['ID']}' and line>0 ";
		if($debug)echo"SELECT * $FROMindex_relacio";
		$index_relacio=$link->query("SELECT * $FROMindex_relacio")->fetch_assoc();
		$update_index="UPDATE `{$year}_Empresa_{$ID_Empresa}_index_relacio` SET
		`ID_client` = '$ID_Client',
		`tax_detall` = '{$nouclient['bool_tax_detall']}',
		`equivalency_charge` = '{$nouclient['bool_equivalency_charge']}',
		`fiscal_name` = '{$nouclient['Value']}',
		`Poblacio`= '{$nouclient['Poblacio']}',
		`NIF` = '{$nouclient['NIF']}',
		`fiscal_address` = '{$nouclient['fiscal_address']}',
		`comercial_name` = '{$nouclient['comercial_name']}',
		`deliver_address` = '{$nouclient['deliver_address']}',
		`phone` = '$tel',
		`email` = '{$nouclient['email']}'
		WHERE `ID` = '{$r['ID']}' AND `ID_tipus_relacio` = '{$r['ID_tipus_relacio']}';";
	}
	else if($tel!=$r['phone'])
		$update_index="UPDATE `{$year}_Empresa_{$ID_Empresa}_index_relacio` SET
		`phone` = '$tel'
		WHERE `ID` = '{$r['ID']}' AND `ID_tipus_relacio` = '{$r['ID_tipus_relacio']}';";
	if($debug)pre($update_index);
	if($a){
		$a=$link->query($update_index);
		if(!$a)echo"ha fallat $update_index";
	}
	if(($a)&&(($ID_Client!=''))) {
		$tarifa=mysql1r("SELECT column_name from tarifa where ID='{$nouclient['id_tarifa']}'",$link,$debug);
		$qllista="SELECT * $FROM ORDER BY line";
		if($llista=$link->query($qllista)){
			while ($f = $llista->fetch_assoc()){
				if($f['units']>=1){
					if($nouclient['id_tarifa']!=10){// sino es detall les altres tarifes venen sense iva
						$noupreu=mysql1r("SELECT $tarifa FROM tarifes where ID='{$f['ID_product']}' AND ultimpreu='1'",$link,$debug);
						$ulinea="update `{$year}_Empresa_{$ID_Empresa}_line` SET cost='$noupreu',`discount`='0'
						WHERE `ID` = '{$r['ID']}' AND `ID_tipus_relacio` = '{$r['ID_tipus_relacio']}' AND `line`='{$f['line']}';";
						if($debug)pre($ulinea);
						$a=$link->query($ulinea);
						if(!$a)break;
					}
				}
				if($nouclient['bool_equivalency_charge']==1){
					//aplico recàrreg a cada línea
					$id_iva=mysql1r("SELECT ID from IVA where `Value`='{$f['IVA']}'",$link);
					$rec=mysql1r("Select TAX FROM `equivalency_charge`  WHERE `ID_iva` = '$id_iva'",$link,true);
					$ulinea="update `{$year}_Empresa_{$ID_Empresa}_line` SET EQUIVALENCY_CHARGE='$rec'
					WHERE `ID` = '{$r['ID']}' AND `ID_tipus_relacio` = '{$r['ID_tipus_relacio']}' AND `line`='{$f['line']}';";
					if($debug)pre($ulinea);
					$a=$link->query($ulinea);
					if(!$a)break;
				}
			}
		}
	}
	return $a;
}
function pdf($r){
	global $decimalsing,$thousandsing,$link;
//  pre($r);
	$idrelacio="{$r['tipus_relacio']['Value']}&{$r['Empresa']['ID']}.{$r['ID']}";
	$data=latindate($r['data']);
	$nom_client=urldecode($r['fiscal_name']);
	$direccio=urldecode($r['fiscal_address']);
	if($r['deliver_address']='')$direccio.="\\\\ Direccio entrega: \\\\".urldecode($r['deliver_address']);
	$poblacio=urldecode($r['Poblacio']);
	$pagat=mysql1r("Select Value from Pagament where `ID`='{$r['ID_Pagament']}'",$link);
	$espaidescripcio=110;
	if($r['tax_detall']==0){
		$espaidescripcio=$espaidescripcio-14;
		$ivainclos='IVA inclós';
	}
	else {
		$espaidescripcio="100";
		$ivainclos='&IVA';
		$coliva='r|';
	}
	 if($r['discountinline']== 1)$espaidescripcio=$espaidescripcio-5;
		$linees=0;
		$discount=$r['discount']=='0'?'':'Descompte';
	 if($r['equivalency_charge'] == 1){
		$prec='\% equivalència';
		$rec='recàrrec eq.';
	 }
		$peu='\hline
		Base&\%IVA&IVA&'.$prec.'&'.$rec.'&'.$discount.'&T.'.$r['tipus_relacio']['Value'].'\\\\
		\hline';
// 		pre($r['IVA']);
		 foreach ($r['IVA'] AS $iva => $tiva){
			 $base='\raggedleft{'.number_format($r['B'][$iva],2,$decimalsing,$thousandsing)."€}";
			 $fiva='\raggedleft{'.number_format_trim($iva,2,$decimalsing,$thousandsing).'\%}';
		     $ftiva='\raggedleft{'.number_format($tiva,2,$decimalsing,$thousandsing)."€}";
			 if($r['equivalency_charge'] == 0){$lb[$iva]="$base&$fiva&{$ftiva}&&&&".'\\\\';}
			 else{
				 	$lrec=0;
				 foreach($r['EC'] AS $rec=>$trec){
					 if($linees==$lrec){
						 	$frec='\raggedleft{'.number_format_trim($rec,2,$decimalsing,$thousandsing).'\%}';
							$ftrec='\raggedleft{'.number_format($trec,2,$decimalsing,$thousandsing)."€}";
						 	$lb[$iva]="$base&$fiva&{$ftiva}&$frec&$ftrec&&".'\\\\';
					 }
					 $lrec++;
				}
			 }
// 			 echo"soc fent el peu aqui $iva, $ftiva ".$lb[$iva].brr();
			 $ultimiva=$iva;
			 $linees++;
		 }

// 		echo 'pagat;'.$pagat;
		$base='\raggedleft{'.number_format($r['B'][$ultimiva],2,$decimalsing,$thousandsing)."€}";
		$iva='\raggedleft{'.number_format_trim($ultimiva,2,$decimalsing,$thousandsing).'\%}';
		$tiva='\raggedleft{'.number_format($r['IVA'][$ultimiva],2,$decimalsing,$thousandsing)."€}";
		$total='\raggedleft{'.number_format($r['Total'],2,$decimalsing,$thousandsing)."€}";
		$discount=$r['discount']=='0'?'':'\raggedleft{'.number_format_trim($r['discount'],2,$decimalsing,$thousandsing).'\%}';
		//\multirow[〈vpos〉]{〈nrows〉}[〈bigstruts〉]{〈width〉}[〈vmove〉]{〈text〉
		//\multirow[t]{5}{*}[-\shiftdown]{\Huge\bfseries B}
// 		 if($r['equivalency_charge'] == 0)
		$lb[$ultimiva]=$base.'&'.$iva.'&'.$tiva.'&'.$frec.'&'.$ftrec.'&'.$discount.'&\multirow[r]{'.$linees.'}{*}{}{\Large '.$total.'}\\\\';
// 		else{//amb recàrreg
// 			$lb[$ultimiva]=$base.'&'.$iva.'&'.$tiva.'&$frec&&'.$discount.'&\multirow[r]{'.$linees.'}{*}{}{\Large '.$total.'}\\\\';
// 		}
//  		pre($lb);
		foreach ($lb AS $linea){
			$peu.=$linea.'
			';
		}
		$peu.='
		\hline';
	$tex='\documentclass[a4paper,10pt]{article}
\usepackage[utf8]{inputenc}
\usepackage{eurosym}
\usepackage{hyperref}
\hypersetup{
    colorlinks=true,
    linkcolor=blue,
    filecolor=magenta,
    urlcolor=cyan,
}
\DeclareUnicodeCharacter{20AC}{\euro}
\usepackage[spanish]{babel}
\addtolength{\voffset}{-1in}%desplaçament vertical
\setlength{\headheight}{50mm}
\addtolength{\hoffset}{-1.2in}%per defecte esta desplaçat 25.4mm (1 in)
\setlength{\textwidth}{18cm}
%\setlength{\textheight{16cm}}
%marges
%\setlength{\topmargin{15mm}}


%opening
\title{'.$idrelacio.' }
\author{'.$r['Empresa']['Value'].'}
%\usepackage{fontspec}
\usepackage{fancyhdr}
\usepackage{longtable}
\usepackage{graphics}
\usepackage{multirow}
\usepackage{array}
\newcolumntype{L}[1]{>{\raggedright\let\newline\\\arraybackslash\hspace{0pt}}m{#1}}
\newcolumntype{C}[1]{>{\centering\let\newline\\\arraybackslash\hspace{0pt}}m{#1}}
\newcolumntype{R}[1]{>{\raggedleft\let\newline\\hspace{0pt}}m{#1}}
\pagestyle{fancy}%format que permet no estar al estandar article de latex
\begin{document}
\lhead{
	\Huge '.$r['Empresa']['Value'].' \normalsize \\\\
	'.$r['Empresa']['Direccio'].'\\\\
    '.$r['Empresa']['Poblacio'].'\\\\
	NIF '.$r['Empresa']['NIF'].'\\\\
	Tel '.$r['Empresa']['Phone'].'\\\\
	'.$r['Empresa']['email'].'\\\\
	\vspace{2em}
	\begin{tabular}{|l|l|}
		\hline
		'.$idrelacio.'\\\\
		Data&'.$data.' 	\\\\
	\hline
	\end{tabular}
	\\
	\hspace{35ex}%\Huge \ '.$r['tipus_relacio']['Value'].'
	\normalsize
	}
\chead{}
\rhead{
	\rotatebox{12}{
		\scalebox{2}[4]{'.$r['tipus_relacio']['Value'].'}
		}
		\\\\
	\begin{tabular}{|p{8.5cm}|}
		\hline
		'.$nom_client.'\\\\
		NIF:'.$r['NIF'].' \\\\
		'.$direccio.'\\\\
		'.$poblacio.'\\\\
		Telef: '.$r['phone'].' '.urldecode($r['email']).' \\\\
		\hline
  	\end{tabular}\\\\
	\vspace{2em}
	}

\begin{longtable}{|p{1cm}|p{'.$espaidescripcio.'mm}|r|r|r|'.$coliva.'}
	\hline
	Codi&Descripció\hspace{'.$espaidescripcio.'mm}&Quantitat&Preu '.$ivainclos.'&Import\\\\
	\hline
	\endhead
';
	foreach ($r['active_lines'] as $v){
		$tex.=str_replace('%','\%','\raggedleft{'.$v['ID_product'].'}&'.$v['urlde']);
		if($v['lotnumber']!='')$tex.=" Lot: ".normalize(urldecode($v['lotnumber']));
		$tex.="&";
		if($v['bulk']==0)$tex.=number_format_trim($v['units'],3,$decimalsing,$thousandsing);
		else $tex.=number_format($v['units'],3,$decimalsing,$thousandsing);
		$tex.=$v['u']."&".number_format($v['cost'],2,$decimalsing,$thousandsing)."€/{$v['u']}";
		if($v['discount']>0)$tex.=' -'.number_format_trim($v['discount'],2,$decimalsing,$thousandsing).'\% ';
		if($r['tax_detall']==1){
			$tex.='&'.number_format_trim($v['IVA'],2,$decimalsing,$thousandsing).'\% ';
			$tex.="&".number_format($v['Base'],2,$decimalsing,$thousandsing)."€".'\\\\';
		}
		else $tex.="&".number_format($v['tline'],2,$decimalsing,$thousandsing)."€".'\\\\';
	}
	$tex.='
    \hline\end{longtable}
%\vspace{1cm}
\begin{table}[b]
	\begin{tabular}{|p{2cm}|p{1.9cm}|p{2cm}|p{2.4cm}|p{2.2cm}|p{1.9cm}||p{2cm}|}
		'.$peu.'
	\end{tabular}
\end{table}
\lfoot{Línees: '.$r['activelines'].'}
\cfoot{Consulteu condicions de venda a \href{https://www.tulsa.eu/condicions}{www.tulsa.eu/condicions}}
\rfoot{'.$pagat.'}
\end{document}
';
// 	pre($tex);
	$nf=$r['uuid'].'.tex';
	exec ("rm {$_SERVER['DOCUMENT_ROOT']}/scale/pdf/{$r['uuid']}.*  -f ");
	unlink ("pdf/*");
 	$f=fopen("pdf/".$nf,'w')or die ;
	fwrite($f, $tex);
	fclose($f);
	//creo el pdf
	crea_pdf($nf);
}

?>