<?php
/*
*
*			Funcions per dibuixar taules més ràpidament que de costum :)
*			Eduard Vidal i Tulsà festuc@amena.com
*
*
*	Llista de funcions
		*br() esrciu </br>
		*brr() retorna </br>
		*a($link,$text="")
			Escriu un Link amb el nom de $text i apunta a $link, si no conté $text, mostra el $link
		*ar
			retorna el que mostraria a
		*ainf($link,$text="",$tar="inferior")
			Apunta un link a un altre frame
		*ainfr retrona el que fa ainf()
	Taules:
		*tr() comença una fila de una taula de html.
		*table($amplada="100",$cellpadding="0", $cellspacing="0", $border="0"){
			$amplada, ens diu el tros que omplirà tota la taula, i els altres tres escriuen lo tipic
		*td($tan="",$colspan="1",$align="left",$color="")comença una cel·la
			$tan []	amplada de la cel·la dintre la taula,
				per defecte el tros que el navegador deixi.
			$colspan [1] 	cel·les que adjunta normarment només esta
					junta a si mateixa.
			$align ["left"] alineacio del text 	left--> esquerra
								rigth -->dreta
								center -->centrat
			$color[""] 	ens escriu la cel·la d'un color determinat
		*tdc()cel·la centrada mateixes variables d'entrada però amb $align="center"
		*tdo($color="",$tan="",$colspan="1",$align="center")
			varia la posició de les variables, per poder posar el $color al primer terme, i centrada.
		*ttd(); tanca una cel·la
		*ttr(); tanca una fila
		*tod()totd() tanca una cel·la i l'obre  els mateixos parametres de td()
		*$todo() tanca una cel·la i l'obre amb els parametres que tdo();
		*totr() tanca i obre una fila.
		*tor-->ttr()+tr()+td()
		toro() --> ttr()+ tr()+ tdo()
		*torc()--> ttr()+ tr()+ tdc()
		*torr()--> ttr()+ tr()+ tdr()
		*tab(apladacela="",$colspan="1",$align="left",$color="", $cellpadding="2", $cellspacing="2", $border="1")-->taula()+td()
			*tabc(... $align="center"...)-->taula()+"tdc"()
			tabr(...  $align="rigth" ...)-->taula()+"tdr"()
		*tabo($color="",$apladacela="",$colspan="1",$align="left", $cellpadding="2", $cellspacing="2", $border="1")
			taula amb color i linees, si es deixa per defecte
		*tab2($ampladataula="100",$widthrow="100",$colspan="1",$align="left")
			varació de tab:D
		*ttable() tanca taula
	Formularis:
		*form($desti="./")
		*tria_dataihora($dia, [$nom], [$idioma=ct], [$diff_year_past=10], [$diff_year_begin=0])
			escriu 6 selects amb les hores, minuts, dia, mes, i any, o l'ordre que demana l'idioma
			suportats: ct, es, en.
			crea les 6 variables segons els estandars php $array[hours] $array[minutes], $array[mday], $array[mon], $array[year]
			li pots pasar els anys que ha de mostrar per sota i per sobre.
		*tria_data($dia, [$nom], [$idioma=ct], [$diff_year_past=10], [$diff_year_begin=0])
			El mateix que l'anterior però no permet sel·leccionar hora i minut
		*sumit($sumit="Ejecutar",$reset="Vaciar Formulario")
			Ens mostra dos botons, un per validar el formulari, i l'altre per buidar-lo.
		*hidden($name,$valor)
			Entra una opcio oculta al formulari, si és una matriu, tota la matriu, $name conté el nom que donarà
			a la nova variable, i $valor la variabre en si.
		*hiddenarray($name,$valor)
			Ens oculta al formulari una matriu, normalment la crida hidden
		*chek($name,$checked=0,$valor="yes")
			Ens mostra una sel·lecció al formulari :P
		*select2radios($name,$array1,$array2) //not implemented yet!!!!
			Ens permet sel·lccionar amb una llista de radios :D
		*selectnum($objectiu="num",$predeterminat=30,$inici=10,$maxim=100,$increment=10)
			Ens  fa una select numerica amb les dades que li pasem.
		*js_selectnum
			Ens fara sumit al form quan hagem sel·leccionat
		*select2llistes($objectiu,$value,$name,$predeterminat=1)
			Ens fa una select a partir dels dos vectors: $value, i $name i li dona la variable $objectiu.
		*text($name,$size=30,$valor="",$max="255"){ crea una entrada de text a un form amb el nom, valor, etc.
		tform() escriu "</form>"
	Cascading Style Sheet:
		hn	|
		hnc	|
		hnr	|On n=1,2,3,4, 5 hn es fa un titol de nivell n, hnc fa hn més centrat, hnr retorna el hn, hncr retorna hnc, 
		hncr	|hncr retorna hnc, hnri hn a la dreta hnrir retorna hri
		hnri	|
		hnrir	|
		center	centra el text que conté
		rigth rigthr posa a la dreta el text que conté rigthr retorna l'sting
		p	|
		pr	|
		pc	|
		pcr	| Igual que el h però en paragrafs, j justifica el texte.
		pri	|
		prir	|
		b()	en negreta!
		bre()	retorna en negreta!
		address	escriu a la dreta en estil adressa.
		addressr retorna a la dreta en estil adressa.

*
*/
function br(){echo "<br/>
";}
function imgr($link,$width="",$height="",$alt=""){
	$r= "<img src=\"$link\" ";
	if($alt=="")$r.=" alt=\"noimporta\" ";
	else $r.= " alt=\"$alt\" ";
	if(($width!="")||($heigth!=""))$r.="style=\"border: 0px solid ;";
	if($width!="")$r.="width: $width;";
	if($height!="")$r.="height: $height; ";
	if(($width!="")||($heigth!="")) $r.="\">";
	else $r.=">";
	return $r;
	}
function img($link,$width="",$height="",$alt=""){echo imgr($link,$width,$height,$alt);}
function brr(){return "<br/>";}
function tria_dataihora($today,$nom="dataihora",$idioma='ca', $diff_years_past=10, $diff_yers_begin=0){
	//version 2.1 8-11-2002 :D la 3.1 es del 2-2-2021
$llistamesos=array(" ","gener","febrer","mar&ccedil;","abril","maig","juny","juliol","agost","setembre","octubre","novembre","desembre");
$listameses=array(" ","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
for($i=1;$i<=13;$i++)$nmesos[$i]=$i;
for($i=1;$i<=32;$i++)$ndies[$i]=$i;
for($i=$today['year']-$diff_years_past;$i<=$today['year']+$diff_years_begin;$i++)$nyear[$i]=$i;
for($i=0;$i<60;$i++)$segons[$i]=$i;
for($i=0;$i<24;$i++)$hora[$i]=$i;
select2llistes($nom."[hours]",$hora,$hora,$today['hours']);
echo ":";
select2llistes($nom."[minutes]",$segons,$segons,$today['minutes']);
echo"/";
select2llistes($nom."[mday]",$ndies,$ndies,$today['mday']);
if($idioma=='ct')select2llistes($nom."['mon']",$nmesos,$llistamesos,$today['mon']);
if($idioma=='ca')select2llistes($nom."['mon']",$nmesos,$llistamesos,$today['mon']);
if($idioma=='es')select2llistes($nom."['mon']",$nmesos,$listameses,$today['mon']);
select2llistes($nom."[year]",$nyear,$nyear,$today['year']);

}
function tria_data($today,$nom="data",$idioma=ca, $diff_years_past=10, $diff_years_begin=0){
	$llistamesos=array("","gener","febrer","mar&ccedil;","abril","maig","juny","juliol","agost","setembre","octubre","novembre","desembre");
	$listameses=array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	//pre();print_r($today);tpre();
	for($i=1;$i<=12;$i++)$nmesos[$i]=$i;
	for($i=1;$i<=31;$i++)$ndies[$i]=$i;
	for($i=$today['year']-$diff_years_past;$i<=$today['year']+$diff_years_begin;$i++)$nyear[$i]=$i;
	$avui=getdate();
	select2llistes($nom."['mday']",$ndies,$ndies,$today['mday']);
	if($idioma=='ct')select2llistes($nom."['mon']",$nmesos,$llistamesos,$today['mon']);
	if($idioma=='ca')select2llistes($nom."['mon']",$nmesos,$llistamesos,$today['mon']);
	if($idioma=='es')select2llistes($nom."['mon']",$nmesos,$listameses,$today['mon']);
	select2llistes($nom."['year']",$nyear,$nyear,$today['year']);
}
function sumit($sumit="Ejecutar",$reset="Vaciar Formulario"){
echo' <br>
<input name="sumit" value="'.$sumit.'" type="submit">
<input name="reset" value="'.$reset.'" type="reset">
';
}
function sumit1($sumit="Ejecutar",$style=""){
echo'
<input name="'.$sumit.'" value="'.$sumit.'"'.$style.' type="submit">
';
}//funcions de dibuixar les taules
function tr(){echo "<tr>\n	";}
function table($amplada="100",$cellpadding="0", $cellspacing="0", $border="0"){
	echo "\n<table cellpadding=$cellpadding cellspacing=$cellspacing border=$border style=\"width: $amplada%\"><tbody>\n";
	tr();}
function td($tan="",$colspan="1",$align="left",$color=""){
	if(($tan!="")&&($color==""))echo "	<td colspan=\"$colspan\" style=\"text-align: $align; width: $tan%;\">\n			";
	if(($tan=="")&&($color!=""))echo "	<td bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: $align;\">\n		";
	if (($tan!="")&&($color!=""))echo "	<td  bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: $align; width: $tan%;\">\n			";
	if(($tan=="")&&($color=="")) echo "	<td colspan=\"$colspan\" style=\"text-align: $align;\">\n		";
}
function tdclass($class,$tan="",$colspan="1",$align="left",$color=""){
	if(($tan!="")&&($color==""))echo "	<td colspan=\"$colspan\" style=\"text-align:\" $align\"; width: \"$tan%; class=\"$class\";\">\n			";
	if(($tan=="")&&($color!=""))echo "	<td bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: \"$align\"; class=$class;\">\n		";
	if (($tan!="")&&($color!=""))echo "	<td  bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: \"$align\"; width:\" $tan%\";\">\n			";
	if(($tan=="")&&($color=="")) echo "	<td colspan=\"$colspan\" style=\"text-align: \"$align\" class=\"$class\";;\">\n		";
}

function tdc($tan="",$colspan="1",$align="center",$color=""){
	if(($tan!="")&&($color==""))echo "	<td colspan=\"$colspan\" style=\"text-align: $align; width: $tan%;\">\n			";
	if(($tan=="")&&($color!=""))echo "	<td bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: $align;\">\n		";
	if (($tan!="")&&($color!=""))echo "	<td  bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: $align; width: $tan%;\">\n			";
	if(($tan=="")&&($color=="")) echo "	<td colspan=\"$colspan\" style=\"text-align: $align;\">\n		";
}function tdr($tan="",$colspan="1",$align="rigth",$color=""){
	if(($tan!="")&&($color==""))echo "	<td colspan=\"$colspan\" style=\"text-align: $align; width: $tan%;\">\n			";
	if(($tan=="")&&($color!=""))echo "	<td bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: $align;\">\n		";
	if (($tan!="")&&($color!=""))echo "	<td  bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: $align; width: $tan%;\">\n			";
	if(($tan=="")&&($color=="")) echo "	<td colspan=\"$colspan\" style=\"text-align: $align;\">\n		";
}

function tdo($color="",$tan="",$colspan="1",$align="center"){
	if(($tan!="")&&($color==""))echo "	<td colspan=\"$colspan\" style=\"text-align: $align; width: $tan%;\">\n			";
	if(($tan=="")&&($color!=""))echo "	<td bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: $align;\">\n		";
	if (($tan!="")&&($color!=""))echo "	<td  bgcolor=\"$color\" colspan=\"$colspan\" style=\"text-align: $align; width: $tan%;\">\n			";
	if(($tan=="")&&($color=="")) echo "	<td colspan=\"$colspan\" style=\"text-align: $align;\">\n		";
}
function ttr(){echo "</tr>\n";}
function ttd(){echo "	</td>\n";}
function totd($tan="",$colspan="1",$align="left",$color=""){
	ttd();td($tan,$colspan,$align,$color);
}
function totdclass($class,$tan="",$colspan="1",$align="left",$color=""){
	ttd();tdclass($class,$tan,$colspan,$align,$color);
}

function tod($tan="",$colspan="1",$align="left",$color=""){
	totd($tan,$colspan,$align,$color);
}
function todo($color="",$tan="",$colspan="1",$align="center"){
	todc($tan,$colspan,$align,$color);
}
function todr($tan="",$colspan="1",$align="right",$color=""){
	totd($tan,$colspan,$align,$color);
}
function todc($tan="",$colspan="1",$align="center",$color=""){
	totd($tan,$colspan,$align,$color);
}
function todclass($class,$tan="",$colspan="1",$align="center",$color=""){
	totdclass($class,$tan,$colspan,$align,$color);
}

function totr(){ttr();tr();}
function tor($tan="",$colspan="1",$align="left",$color=""){
	ttd();
	totr();
	td($tan,$colspan,$align,$color);
}
function toro($color="",$tan="",$colspan="1",$align="center"){
	ttd();
	totr();
	tdo($color,$tan,$colspan,$align);
}
function torc($tan="",$colspan="1",$align="center",$color=""){
	ttd();
	totr();
	td($tan,$colspan,$align,$color);
}
function torclass($class,$tan="",$colspan="1",$align="center",$color=""){
	ttd();
	totr();
	tdclass($class,$tan,$colspan,$align,$color);
}

function torr($tan="",$colspan="1",$align="right",$color=""){ttd();totr();td($tan,$colspan,$align,$color);}
function tab($apladacela="",$colspan="1",$align="left",$color="", $cellpadding="2", $cellspacing="2", $border="1"){
	table();
	td($ampladacela,$colspan,$align,$color);
}
function tabo($color="",$apladacela="",$colspan="1",$align="left", $cellpadding="2", $cellspacing="2", $border="1"){
	table();
	td($ampladacela,$colspan,$align,$color);
}
function tabr($ampladacela="",$colspan="1",$align="right",$color="", $cellpadding="2", $cellspacing="2", $border="1"){
	table();
	td($ampladacela,$colspan,$align,$color);
}
function tabc($ampladacela="",$colspan="1",$align="center",$color="", $cellpadding="2", $cellspacing="2", $border="1"){
	table();
	td($ampladacela,$colspan,$align,$color);
}
function tabclass($class,$ampladacela="",$colspan="1",$align="center",$color="", $cellpadding="2", $cellspacing="2", $border="1"){
	table();
	tdclass($class,$ampladacela,$colspan,$align,$color);
}

function tab2($ampladataula="100",$widthrow="100",$colspan="1",$align="left"){
	table($ampladataula);
	td($widthrow,$colspan,$align);
}
function ttable(){ttd();ttr();echo "</tbody></table>\n";}
function formfile($desti="./"){
	echo "<form action=\"{$desti}\" method=post enctype=\"multipart/form-data\">";
}
function ifile($name,$size=30,$valor=""){
	echo "<input type=file name=\"$name\" value=\"$valor\" size=\"$size\">\n";
}
function form($desti="./", $M="get"){
echo "
<form ACTION =\"$desti\" METHOD=$M>
";}
function text($name,$size=30,$valor="",$max="255"){
echo"<input type=text name=$name value=\"$valor\" size=$size MAXLENGTH=$max> \n";
}
function tform(){echo"</form>";}
function h1($S){echo h1r($S);}
function h1r($S){return"<h1>$S</h1>";}
function h1c($S){echo"<h1 style=\"text-align: center\">$S</h1>";}
function h1cr($S){return"<h1 style=\"text-align: center\">$S</h1>";}
function h1ri($S){echo"<h1 style=\"text-align: rigth\">$S</h1>";}
function h1rir($S){return"<h1 style=\"text-align: rigth\">$S</h1>";}
function h2($S){echo"<h2>$S</h2>";}
function h2r($S){return"<h2>$S</h2>";}
function h2c($S){echo"<h2 style=\"text-align: center\">$S</h2>";}
function h2cr($S){return"<h2 style=\"text-align: center\">$S</h2>";}
function h2rir($S){return "<h2 style=\"text-align: rigth\">$S</h2>";}
function h2ri($s){hrir($s);}
function h3($S){echo"<h3>$S</h3>";}
function h3r($S){return "<h3>$S</h3>";}
function h3c($S){echo h3cr($S);}
function h3cr($S){return "<h3 style=\"text-align: center\">$S</h3>";}
function h3ri($S){echo h3rir($S);}
function h3rir($S){return "<h3 style=\"text-align: rigth\">$S</h3>";}
function h4($S){echo"<h4>$S</h4>";}
function h4r($S){return"<h4>$S</h4>";}
function h4c($S){echo h4cr($S);}
function h4cr($S){return "<h4 style=\"text-align: center\">$S</h4>";}
function h4ri($S){echo h4rir($S);}
function h4rir($S){return "<h4 style=\"text-align: rigth\">$S</h4>";}
function h5($S){echo"<h5>$S</h5>";}
function h5r($S){return"<h5 style=\"text-align: center\">$S</h5>";}
function h5c($S){echo h5cr($S);}
function h5cr($S){return "<h3 style=\"text-align: center\">$S</h3>";}
function h5ri($S){echo h3rir($S);}
function h5rir($S){return "<h3 style=\"text-align: rigth\">$S</h3>";}
function p($S){echo"<p>$S</p>";}
function small($S){echo"<small>$S</small>";}
function pr($S){return"<p>$S</p>";}
function pc($S){echo"<p style=\"text-align: center\">$S</p>";}
function pcr($S){return "<p style=\"text-align: center\">$S</p>";}
function pri($S){echo"<p style=\"text-align: rigth\">$S</p>";}
function prir($S){return "<p style=\"text-align: rigth\">$S</p>";}
function pj($S){echo"<p style=\"text-align: justify\">$S</p>";}
function pjr($S){return "<p style=\"text-align: justify\">$S</p>";}
function b($S){echo bre($S);}
function bre($S){return "<b>$S</b>";}
function address($S,$align="right"){echo addressr($S,$align);}
function addressr($S,$align="right"){return "<address style=\"text-align: $align;\">$S</address>";}
function center($s){return '<div style="text-align: center;">'.$s.'</div>';}
function rigthr($s){return '<div style="text-align: rigth;">'.$s.'</div>';}
function rigth($s){echo rigthr($s);}

// Formularis
function hidden($name,$valor){
	if (is_array($valor)) hiddenarray($name,$valor);
	else echo "<INPUT TYPE=\"hidden\" NAME=\"$name\" VALUE=\"$valor\">\n";
}
function hiddenarray($name,$valor){
	$noms=array_keys($valor);
	$i=0;
	while($i<sizeof($noms)){
		if(is_array($valor[$noms[$i]])){
			hiddenarray($name.'['.$noms[$i].']',$valor[$noms[$i]]);
		}
		else {
			hidden($name.'['.$noms[$i].']',$valor[$noms[$i]]);
		}
		$i++;
	}

}
function error($S){echo errorr($S);}
function errorr($S){return "<h2 style=\"text-align: center;\"><i>$S</i></h2>";}
function pass($name,$size=30,$valor="",$max="255"){
	echo'<input type="password" name="'.$name.'" value="'.$valor.'" size='.$size.'
		MAXLENGTH='.$max.' >'."\n";
}
function chek($name,$checked=0,$valor="yes"){
if ($checked==0)echo "<input type=\"checkbox\" name=\"$name\" value=\"$valor\">";
else echo "<input type=\"checkbox\" name=\"$name\" value=\"$valor\" checked>";
}
function a($link,$text=""){
	echo ar($link,$text);
}
function ar($link,$text=""){
	if($text=="")return "<a href=\"$link\">$link</a>";
        else return "<a href=\"$link\">$text </a>";
}
function aconfirm($link,$text="",$pregunta="Vols fer aixó?"){
	echo aconfirmr($link,$text,$pregunta);
}
function aconfirmr($link,$text="",$pregunta="Vols fer aixó?"){
	if(!$text){return "<a href=$link 
	onclick=\"return confirmLink(this, '$pregunta')\")>$link</a>";}
        else {return "<a href=$link onclick=\"alert();\">$text</a>";}
}

function ainf ($link,$text="",$tar="inferior"){
        if($text=="")echo "<a href=$link target=\"$tar\">$link</a>";
        else echo "<a href=$link target=\"$tar\">$text </a>";
}function ainfr ($link,$text="",$tar="inferior"){
	if($text=="")return "<a href=$link target=\"$tar\">$link</a>";
        else return "<a href=$link target=\"$tar\">$text </a>";
}
/*older....
	function consulta_maxim($link,$taula,$numeracio){
	//	Aquesta Funció calcula el màxim valor de una columna de MySQL anomenada $numeracio,
	//	de la taula $taula i del acces a la BBDD $link.
	//

	//consulta de MySQL
	$queri="SELECT $numeracio FROM $taula ORDER BY $numeracio DESC";
	//echo $queri;
	$consulta=mysql_query($queri,$link);
	$row= mysql_fetch_row($consulta);
	$valor=$row[0];
	//si no hi ha cap valor a una taula! no retorna res per aixó he fet açó
	if (!$valor)$valor=0;
	return $valor;
}*/
function selectnum($objectiu="num",$predeterminat=30,$inici=10,$maxim=100,$increment=10){
	for($i=$inici;$i<=$maxim;$i=$i+$increment)$v[$i]=$i;
	select2llistes($objectiu,$v,$v,$predetermat);
}
function js_selectnum($objectiu="num",$predeterminat=30,$inici=10,$maxim=100,$increment=10){
	/*tabc();
	echo'	<select name="'.$objectiu.'">';
	for($i=$inici;$i<=$maxim;$i=$i+$increment){
		echo '<option ';
		if ($predeterminat==$i)echo "selected ";
		echo "value=\"$i\">$i\n";
	}
	ttable();
	*/echo "js_selectnum not implemented yet!";
}
function select2llistes_js($objectiu,$value,$name,$predeterminat=1,$urldeco='no',$autofocus=false,$style=""){
	//Version 2 23/04/2020
	echo "<select name=\"$objectiu\" onChange=\"this.form.submit()\"".($autofocus?' autofocus':'')." "."$style>";
	$a=array_keys($value);
	$inici=$a[0];
	if(!is_int($inici))$inici=0;
	$fi=sizeof($value)+$inici;
		for($i=$inici;$i<$fi;$i++){
			echo "<option ";
			if ($predeterminat==$value[$i])echo "selected ";
		//	echo "value=".str_replace(" ","+",$value[$i])." >".$name[$i]."";
			echo "value=".$value[$i]." >".($urldeco=='no'?$name[$i]:urldecode($name[$i]))."\n";
		}
		echo "</select><!-- pre: $predeterminat -->";
}
function select2llistes($objectiu,$value,$name,$predeterminat=1,$urldeco='no',$style=""){
	//Version 5 23/04/2020
	echo "<select name=\"$objectiu\">\n";
	$a=array_keys($value);
	$inici=$a[0];
	if(!is_int($inici))$inici=0;
	$fi=sizeof($value)+$inici;
		for($i=$inici;$i<$fi;$i++){
			echo "<option ";
			if ($predeterminat==$value[$i])echo "selected ";
			echo "value=".$value[$i]." >".($urldeco=='no'?$name[$i]:urldecode($name[$i]))."\n";
		}
		echo "</select>\n<!-- el predetermintat era $predeterminat -->\n";
}
function select1llista_js($objectiu,$values,$predeterminat=1){
	$a=array_keys($values);
	echo "<select name=\"$objectiu\" onChange=\"this.form.submit()\" >\n";
	for($i=0;$i<sizeof($a);$i++){
		echo "<option ";
		if ($predeterminat==$a[$i])echo "selected ";
		echo "value=".$a[$i].">".$values[$a[$i]]."\n";
	}
	echo "</select><!-- predeterminat:$predeterminat -->";
}
function select1llista($objectiu,$values,$predeterminat=1){
	$a=array_keys($values);
	echo "<select name=\"$objectiu\">\n";
	for($i=0;$i<sizeof($a);$i++){
		echo "<option ";
		if ($predeterminat==$a[$i])echo "selected ";
		echo "value=".$a[$i].">".$values[$a[$i]]."\n";
	}
	echo "</select><!-- predeterminat:$predeterminat -->";
}
function urlencodea($name,$valor){
	if(is_array($valor))$noms=array_keys($valor);
	else $noms=1;
	$i=0;
	while($i<sizeof($noms)){
		if(is_array($valor[$noms[$i]])){
			$url.=urlencodea($name.'['.$noms[$i].']',$valor[$noms[$i]]);
		}
		else {
			$url.=urlencod($name.'['.$noms[$i].']',$valor[$noms[$i]]);
		}
		$i++;
	}
	return $url;
}
function urlencod($name,$valor){
	//return $name."=".str_replace(" ","+",$valor).'&';
	if (is_array($valor)) return urlencodea($name,$valor);
	else return urlencode($name)."=".urlencode($valor).'&';

}
function back($i=1){
	echo "<html><head></head><body onLoad=\"history.go(-$i)\"></body></html>";
	}
function div($target=false,$extra=''){
	if($target!=false )echo "<div id=\"$target\" $extra>";
	else echo "<div $extra>";
}

function divclass($target='body',$extra=''){
	echo "<div class=\"$target\" $extra>";
}
function tdiv(){
	echo "</div>";
}
function pre($array=false){
	if($array!=FALSE){echo '<pre>';print_r($array);echo '</pre>';}
	 else echo '<pre>';
}
function tpre(){
	echo '</pre>';
}
function ul(){
echo "<ul>";
}
function tul(){
	echo"</ul>";
}
function il(){
	echo"<il>";
}
function til(){
	echo"</il>";}
function li(){
	echo"<li>";
}
function tli(){
	echo"</li>";}
function textarea($name,$rows,$cols,$defect=''){
	echo "<textarea name=$name rows=$rows cols=$cols>$defect</textarea>";
}
?>