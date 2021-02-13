<?php
require '../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;
class Printerfestuc extends Printer{
	public function line($v){
		$this -> selectPrintMode('1');
		global $decimalsing,$thousandsing;
		$this-> setJustification(Printer::JUSTIFY_LEFT);
		$linea="{$v['urlde']} ";
		if($v['lotnumber']!='')$linea.="Lot: {$v['lotnumber']} ";
		if($v['bulk']==0)$num1=number_format_trim($v['units'],3,$decimalsing,$thousandsing);
		else $num1=number_format($v['units'],3,$decimalsing,$thousandsing);
		$num1.=$v['u']." ".number_format($v['cost'],2,$decimalsing,$thousandsing).chr(213)."/{$v['u']} ";
		if($v['discount']>0)$num1.='-'.number_format_trim($v['discount'],2,$decimalsing,$thousandsing).'% ';
		if($v['tax_detall']==1){
			$num1.='IVA '.number_format_trim($v['IVA'],2,$decimalsing,$thousandsing).'% ';
			if($v['EQUIVALENCY_CHARGE']>0)
				{$num1.='RE '.number_format_trim($v['EQUIVALENCY_CHARGE'],2,$decimalsing,$thousandsing).'% ';}
			$num1.=number_format($v['Base'],2,$decimalsing,$thousandsing).chr(213);
		}
		if($v['tax_detall']==0)$num1.=number_format($v['tline'],2,$decimalsing,$thousandsing).chr(213);
		$maxespais=64;
		$espaisocupats=strlen($linea)+strlen($num1);
		$espaisblancs=$maxespais-$espaisocupats;
		if ($espaisocupats>=$maxespais){
			$text="$linea\n";
			$num1=str_pad($num1,$maxespais,' ',STR_PAD_LEFT);
		}
		else $text=str_pad($linea, $maxespais-strlen($num1));
		$this->text($text);
		$this -> selectCharacterTable(2);
		$this ->textRaw($num1);
		$this -> selectCharacterTable();
		$this -> text("\n");
		/*$this -> selectPrintMode();
		$this->text("{$maxespais}=".strlen($num1));
		$this->text("+".strlen($linea)."+{$espaisblancs}=");
		$this ->text(strlen($text).'+'.strlen($num1)."\n");*/
	}
	public function euro(){
		$this -> selectCharacterTable(2);
	    $chars = str_repeat(' ', 256);
	    for ($i = 0; $i < 255; $i++) {
	        $chars[$i] = ($i > 32 && $i != 127) ? chr($i) : ' ';
	    }
	// 		return($chars[213]);5.5727272034
		$this->textRaw($chars[213]);
		$this -> selectCharacterTable();
	}
}

require("login.php");
if(!$login){
	header("Location: {$scale_uri}");
	die;
}
$ticket="out.lp";
try {
$connector = new FilePrintConnector("out.lp");
$printer = new Printerfestuc($connector);
/* Initialize */
$printer -> initialize();
$printer -> selectCharacterTable(2);
$printer -> text("Total Empresa $ID_Empresa ". date(" j/m/Y h:i:s A")."\n");
$q="select * from {$prefixrelacio}index_relacio WHERE `data`=curdate() ";
if ($_REQUEST['sempre']==yes)$q="select * from {$prefixrelacio}index_relacio ";
if ($_REQUEST['ahir']==yes)$q="select * from {$prefixrelacio}index_relacio WHERE `data`=curdate()-1";
$qlist=$link->query($q);
$printer -> text($q);
while($rel=$qlist->fetch_assoc()){
	$r=detallrelacio($rel['uuid'],$ID_Empresa,date(Y),$link);
	/*
	tot aixó no ho necesitem per ara
	foreach ($r['IVA']as $iva=>$viva){
			$t['B'][$iva]=$t[ 'B'][$iva]+$r['B'][$iva];
			$t['T'][$iva]=$t[ 'T'][$iva]+$r['T'][$iva];
	}*/ 
	$t['via'][$r['ID_Pagament']][$r["ID_Venedor"]]["Total"]=$t['via'][$r['ID_Pagament']][$r["ID_Venedor"]]["Total"]+$r["Total"];
	$t['venedor'][$r["ID_Venedor"]]["Total"]=$t['venedor'][$r["ID_Venedor"]]["Total"]+$r["Total"];
	$t['via'][$r['ID_Pagament']]["Total"]=$t['via'][$r['ID_Pagament']]["Total"]+$r["Total"];
}
foreach ($t['via']as $ID_Pagament => $valors){
	br();
	$printer -> text( "cobrat en ".mysql1r("select value from Pagament where ID=$ID_Pagament",$link).":\n");
	br();
	$printer -> text("{$valors['Total']}\n");
}
br();

a("./","tornar");

$printer -> cut();
$printer -> close();
// pre();print_r($r);tpre();
ticket_print_command($ticket);
 `rm $ticket -f`;
echo `echo out.lp`;
header("Location: {$scale_uri}");

 	} catch (Exception $e) {
     echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";}
?>
