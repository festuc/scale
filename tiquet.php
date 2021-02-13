<?php
require_once('login.php');
// imprimeixo tiquet 
if($login==false){
	echo "nologin";
	$link->close();
	die;
}
if($ID_Empresa!=31){
	//citizen
	$euro=213;//a citizen 213
	$taula=2;//a citizen 2
	$fcap=184;//184
	$f2=8;//'41'
	$f3=9;
	$fref=40;//40
	$llpreus=66;//66
	$lldescompte=48;//48
	$llref=25;//25
	$llates=49;//49
	$maxespais=64;
	$atencio="ATENCIÓ: Les bosses compostables fan que el\nproducte torrat perdi el cruixent\n";
}
if($ID_Empresa==31){
// 	die;
	//csi 100
	$euro=128;//a citizen 213
	$taula=16;//a citizen 2
	$fcap=177;//184
	$f2=8;//'41'
	$f3=9;
	$fref=8;//40
	$llpreus=58;//66
	$lldescompte=42;//48
	$llref=30;//25
	$llates=43;//49
	$maxespais=56;
	$atencio="ATENCIÓ: Les bosses compostables fan que\n el producte torrat perdi el cruixent\n";

}
require '../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;
class Printerfestuc extends Printer{
	public function line($v){
		$this -> selectPrintMode('1');
		global $decimalsing,$thousandsing,$euro,$taula,$maxespais;
		$this-> setJustification(Printer::JUSTIFY_LEFT);
		$linea="{$v['urlde']} ";
		if($v['lotnumber']!='')$linea.="Lot: {$v['lotnumber']} ";
		if($v['bulk']==0)$num1=number_format_trim($v['units'],3,$decimalsing,$thousandsing);
		else $num1=number_format($v['units'],3,$decimalsing,$thousandsing);
		$num1.=$v['u']." ".number_format($v['cost'],2,$decimalsing,$thousandsing).chr($euro)."/{$v['u']} ";
		if($v['discount']>0)$num1.='-'.number_format_trim($v['discount'],2,$decimalsing,$thousandsing).'% ';
		if($v['tax_detall']==1){
			$num1.='IVA '.number_format_trim($v['IVA'],2,$decimalsing,$thousandsing).'% ';
			if($v['EQUIVALENCY_CHARGE']>0)
				{$num1.='RE '.number_format_trim($v['EQUIVALENCY_CHARGE'],2,$decimalsing,$thousandsing).'% ';}
			$num1.=number_format($v['Base'],2,$decimalsing,$thousandsing).chr($euro);
		}
		if($v['tax_detall']==0)$num1.=number_format($v['tline'],2,$decimalsing,$thousandsing).chr($euro);
		$espaisocupats=strlen($linea)+strlen($num1);
		$espaisblancs=$maxespais-$espaisocupats;
		if ($espaisocupats>=$maxespais){
			$text="$linea\n";
			$num1=str_pad($num1,$maxespais,' ',STR_PAD_LEFT);
		}
		else $text=str_pad($linea, $maxespais-strlen($num1));
		$this->text($text);
		$this -> selectCharacterTable($taula);
		$this ->textRaw($num1);
		$this -> selectCharacterTable();
		$this -> text("\n");
		/*$this -> selectPrintMode();
		$this->text("{$maxespais}=".strlen($num1));
		$this->text("+".strlen($linea)."+{$espaisblancs}=");
		$this ->text(strlen($text).'+'.strlen($num1)."\n");*/
	}
	public function euro(){
		global $taula;
		$this -> selectCharacterTable($taula);
	    $chars = str_repeat(' ', 256);
	    for ($i = 0; $i < 255; $i++) {
	        $chars[$i] = ($i > 32 && $i != 127) ? chr($i) : ' ';
	    }
		//return($chars[$euro]);
		$this->textRaw($chars[$euro]);
		$this -> selectCharacterTable();
	}
}

// Establecer la zona horaria predeterminada a usar. Disponible desde PHP 5.1
date_default_timezone_set('Europe/Andorra');
// pre($_REQUEST);die;
if($_REQUEST['uuid']==''){$link->close(); header("$scale_uri");}
$r=detallrelacio($_REQUEST['uuid'],$ID_Empresa,date(Y),$link);
// pre($r);die;
$ticket="out.lp";
try {
$connector = new FilePrintConnector("out.lp");
$printer = new Printerfestuc($connector);
/* Initialize */
$printer -> initialize();
$printer -> selectCharacterTable($taula);
/* Text */
//head:
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> selectPrintMode($fcap);
$printer -> text("{$r['Empresa']['termal_head_1']}\n");
$printer -> feed(1);
$printer -> selectPrintMode($f2);
$printer -> text("{$r['Empresa']['termal_head_2']}\n");
$printer -> text("{$r['Empresa']['termal_head_3']}\n");
$printer -> selectPrintMode($f3);
$idrelacio="{$r['tipus_relacio']['Value']}:{$r['Empresa']['ID']}.{$r['ID']}";
if($r['tax_detall']==1){
	$preus="PREUS IVA NO INClÒS";//linees actives
	}
else $preus="PREUS IVA INClÒS";
	$data=" Data:".latindate($r['data']).' '. date("H:i")."\n";
$preus=str_pad($preus,$llpreus-(strlen($idrelacio)+strlen($data)),' ',STR_PAD_BOTH);
$printer->text($idrelacio.$preus.$data);
	$printer->feed(1);
$printer -> selectPrintMode();
	$printer -> setJustification(Printer::JUSTIFY_CENTER);
if($r['ID_client']>1)$printer -> text(" Client:".urldecode($r['fiscal_name']));
if($r['NIF']!='')$printer -> text(" NIF:".urldecode($r['NIF']));
if($r['fiscal_address']!='')$printer -> text("\nD. Fiscal:".urldecode($r['fiscal_address'])."\n");
if($r['comercial_name']!='')$printer -> text(" Nom comercial: ".urldecode($r['comercial_name'])."\n");
if($r['deliver_address']!='')$printer -> text(" Direccio entrega: ".urldecode($r['deliver_address'])."\n");
if($r['phone']!='')$printer -> text(" Telèfon: ".urldecode($r['phone'])."\n");
// $printer->feed(1);
	$printer -> setJustification(Printer::JUSTIFY_RIGHT);
foreach ($r['active_lines'] AS $v){
	$v['tax_detall']=$r['tax_detall'];
	$printer->line($v);
}
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> selectPrintMode();
// $printer -> selectCharacterTable();
$printer->feed(1);
	$printer-> selectCharacterTable();
$descompte="-".$r['discount']."%";
if($r['discount']!='0')
	$printer->text(str_pad("Descompte:",$lldescompte-strlen($descompte)).$descompte."\n");
unset($detall);
		$printer-> selectCharacterTable($taula);
// if($index_relacio['tax_detall']==1){
	$base=number_format($r['Base'],2,$decimalsing,$thousandsing).chr($euro);
	$printer ->textRaw(str_pad("Base imponible:",$lldescompte-strlen($base)).$base);
	$printer->text("\n");
// }
//arsort($r['IVA'],SORT_NUMERIC);

foreach ($r['IVA'] AS $iva => $v){
	if ($v!=''){
		$fiva=number_format_trim($iva,2,$decimalsing,$thousandsing);
		$v=number_format_trim($v,2,$decimalsing,$thousandsing);
		$detall.=" {$fiva}%: $v".chr($euro);
	}
}
$iva=str_pad("IVA :",$lldescompte-strlen($detall));

	$printer->textRaw($iva.$detall);

// arsort($r['EC']);
if($r['equivalency_charge']==1){
	$printer->text("\nR.E. ");
	foreach ($r['EC'] AS $ec => $v){
		$v=number_format_trim($v,2,$decimalsing,$thousandsing);
		if ($v!='')$printer -> text(" RE {$ec}%:  $v");$printer->euro();
	}
}
$printer->feed(1);
$printer-> selectCharacterTable();
$printer -> selectPrintMode($fref);
$apagar="A Pagar:".number_format($r['Total'],2,$decimalsing,$thousandsing).chr($euro)."\n";
$printer -> text(str_pad("Ref.: {$r['activelines']}",$llref-strlen($apagar)));
$printer -> selectPrintMode('56');
   $printer -> setJustification(Printer::JUSTIFY_RIGHT);
	$printer -> selectCharacterTable($taula);
$printer -> textRaw($apagar);
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> selectPrintMode();
	$printer -> text(str_pad("Atés per: {$r['Nom_Venedor']}",$llates-strlen($r['Empresa']['termal_tail_1'])));

$printer -> selectPrintMode('16');
$printer -> text("{$r['Empresa']['termal_tail_1']}");
	$printer -> selectPrintMode();
	$printer -> text("\n");
$printer-> selectPrintMode('0');
$printer -> text($atencio);
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> text("Gràcies per la vostra compra\n");
	$r['email']=urldecode($r['email']);
	if (correuvalid($r['email'])){
		$printer -> text("Us hem enviat copia en pdf de la factura a\n {$r['email']}\n");
	}
//linees borrades
if($r['deletedlines']>0){
	//$printer -> cut(66);
	$printer -> selectPrintMode();
	$printer -> text("Linees borrades \n");
	foreach ($r['deleted_lines'] AS $v){
		$printer -> line($v);
	}
}
$printer -> cut();
$printer -> close();
ticket_print_command($ticket);
`rm $ticket -f`;
header("Location: {$scale_uri}");
 	} catch (Exception $e) {
     echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";}
?>
