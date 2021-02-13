<?php
$host ="localhost";
$user= "root";
$pass="";
$bd="";
function obre_bd($host,$user,$pass,$bd){
	$link=mysqli_connect($host,$user,$pass,$bd);
	if (!$link){
			echo "<h2> No puc conectar a la base de dades!
			demana al administrador/a que l'inici-hi, gràcies. </h2>";
			exit;
		}
	//mysql_select_db("$bd");
	return $link;
}
function edat ($birthday){ //$birtday in mysqlformat...
    list($year,$month,$day) = explode("-",$birthday);
    $year_diff  = date("Y") - $year;
    $month_diff = date("m") - $month;
    $day_diff   = date("d") - $day;
    if ($day_diff < 0 || $month_diff < 0)
      $year_diff--;
    return $year_diff;
  }

function decodificadata($dataanglesa){
	//pasa d'una data mysql a una matriu de php amb el mday, el mon i el year
	$dia['year']=substr($dataanglesa,0,4);
	$dia['mon']=substr($dataanglesa,5,2);
	$dia['mday']=substr($dataanglesa,8,2);
	//print($dia);
	return $dia;
}
function latindatetime($mysqldate){
	$a=decodificadatetime($mysqldate);
	$r= $a['mday']."-".$a['mon']."-".$a['year']."  ".$a['hours'].":".$a['minutes'].":".$a['secons'];
	return $r;
}
function latindate($mysqldate){
	$a=decodificadata($mysqldate);
	$r= $a['mday']."-".$a['mon']."-".$a['year'];
	return $r;
}
function date2mysql($d,$zero_fil=false){
	pre($d);
// 	echo "any={$d['year']}";die;
        $mes=$d['mon'];
        $dia=$d['mday'];
	if ($zero_fil){
// 		echo "true";
		if (($d["'mon'"]==0)or ($d["'mday'"]==0) or ($d["'year'"]==0)){
// 			echo "un val 0";die;
			//return "{$d['year']}-{$d['mon']}-{$d['mday']}";
			return"0000-00-00";
		}
		else {echo 'recursiu';
			  return date2mysql($d);
			  }
	}
	else {
		if(checkdate($d["'mon'"],$d["'mday'"],$d["'year'"])){
			echo 'data correcta';
			return "{$d["'year'"]}-{$d["'mon'"]}-{$d["'mday'"]}";
		}
		else {
			error("El dia ".$d['mday']."/".$data['mon']."/".$data['year']." no existeix provo un dia menys" );
			$d['mday']=$d['mday']-1;
			if ($d['mday']<0) die("Dia negatiu");
			return date2mysql($data);
		}
	}
}
function totimeunix ($data){
	$hour=gmdate($data["hours"]);
	$min=gmdate($data["minutes"]);
	$sec=gmdate($data["secons"]);
	$month=gmdate($data["mon"]);
	$day=gmdate($data["mday"]);
	$year=gmdate($data["year"]);
	$t=mktime($hour,$min,$sec,$month,$day,$year);
	return $t;
}
function decodificadatetime($dataanglesa){
	//pasa d'una data mysql a una matriu de php amb el mday, el mon i el year
	$dia[year]=substr($dataanglesa,0,4);
	$dia[mon]=substr($dataanglesa,5,2);
	$dia[mday]=substr($dataanglesa,8,2);
	$dia[hours]=substr($dataanglesa,11,2);
	$dia[minutes]=substr($dataanglesa,14,2);
	$dia[secons]=substr($dataanglesa,17,2);
	//print_r($dia);
	return $dia;
}
function datetime2mysql($data){
	$any=$data['year'];
        $mes=$data['mon'];
        $dia=$data['mday'];
	$hora=$data['hours'];
	$minut=$data['minutes'];
	$seg=$data['seconds'];
	$hora?$hora:$hora='00';
	$minut?$minut:$minut='00';
	$seg?$seg:$seg='00';
	//print_r($data);
	if($year<0) die("mysql.php error, we only understod possitive days  :-)");
	if(checkdate($data[mon],$data[mday],$data[year]))
		return "$any-$mes-$dia $hora:$minut:$seg";
	else {
		if ($data[mday]==1){
			if($data[mon]==1)$data[year]--;
			else $data[mon]--;
			}
		else $data[mday]=$data[mday]-1;
		return datetime2mysql($data);
	}
}
function primer_de_mes($data){
	$data[mday]=1;
	return $data;
}
function primer_de_mes_ant($data){ //AQUEST NO FUNCIONA existeix mes 0
	$data=primer_de_mes($data);
	$data[mon]=$data[mon]-1;
	return $data;
}
function final_de_mes_ant($data){ //AQUEST NO FUNCIONA existeix mes 0

	$data[mon]--;
	$data[mday]=31;
	while(!checkdate($data[mon],$data[mday],$data[year]))$data[mday]=$data[mday]-1;
	return $data;
}
function final_de_mes($data) //AQUEST NO FUNCIONA existeix mes 0
{
	$data[mday]=31;
	while(!checkdate($data[mon],$data[mday],$data[year]))$data[mday]=$data[mday]-1;
	return $data;
}
function mysql1r($query,$link,$debug=false){
    if ($debug)echo $query."</br>";
    $i=mysqli_query($link,$query) or die(errorr(mysqli_error($link)));
    $a=mysqli_fetch_row($i);
    if ($debug)echo$query."=".$a[0];
    return $a[0];
}
function mysql2array($query,$link,$debug=FALSE){
	$q=mysqli_query($link,$query)or die (errorr($query).mysqli_error());
	/*** bucle que crea a partir de $q una matriu ***/
	if ($debug)echo $query;
	$j=0;
		while ($fila=mysqli_fetch_row($q)){
		$fi=sizeof($fila);
		for($i=0;$i<$fi;$i++){
			$a[$i][$j]=$fila[$i];
			if ($debug)echo "<pre>\"[$i,$j]:[".$fila[$i]."],[".$a[$i][$j]."]\"</tpre>";
		}
		$j++;
	}
	if($debug){pre();print_r($a);tpre();}
	if (!empty($a))
		return $a;
}
function mysql2vector($query,$link){
	$q=mysqli_query($link,$query)or die (errorr($query).mysqli_error());
	$fila=mysqli_fetch_row($q);
	return $fila;
}
function mysql2vectorassoc($query,$link,$debug=false){
	$q=mysqli_query($link,$query)or die (errorr($query).mysqli_error());
	$fila=mysqli_fetch_assoc($q);
	mysqli_free_result($q);
	if($debug){
		pre();
		echo $query;
		print_r($fila);
		tpre();
	}
	return $fila;
}
function enterprise_nom($empresa,$link){
	$q="Select name from empreses where code=$empresa";
	$q2=mysqli_query($link,$q) or die (errorr($q));
	$r=mysqli_fetch_row($q2);
	return $r[0];
}
function dema($data){
	$data['mday']++;
	if(checkdate($data['mon'],$data['mday'],$data['year']))
		return $data;
	else{
		$data[mday]=1;
		$data[mon]++;
		if(checkdate($data['mon'],$data['mday'],$data['year']))
			return $data;
		else{
			$data[mon]=1;
			$data[year]++;
			return $data;
		}
	}
}
function mysql_quer($q,$link,$debug=0){
	if ($debug>0)error($q);
	return mysqli_query($link,$q)or die(errorr($q).mysqli_error($link));
}
function correuvalid($correu){
	//echo "estic a correuvalid amb $correu";
	if (preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$/",$correu))
		return true; 
	else return false;
}
function tableform($config,$link,$debug=false){
	$camps=mysql2array("SHOW full COLUMNS FROM `{$config['table']}` ",$link);//Field	Type	Collation	Null	Key	Default	Extra	Privileges	Comment
	if($debug){pre();print_r($camps);print_r($config);tpre();}
	if ($debug){pre();echo"config tableform\n";print_r($config);tpre();}
	$avui=getdate();
	foreach ($camps[0] as  $k=> $camp){
		switch ($camp) {
			case 'ID':
				tab(20);
				switch($config['do']){
					case 'update':
						h3('Modificacio codi '.$config['id']);
						$olds=mysql2array("describe {$config['table']}",$link);
						foreach ($olds[0] as  $old){
							if(isset($config['id2'])){
									$d[$old]=mysql1r("SELECT $old from {$config['table']} where `id` ='{$config['id']}' and `{$config['id2']}`='{$config['id2Value']}'",$link,$debug);
							}
							else $d[$old]=mysql1r("SELECT $old from {$config['table']} where id ={$config['id']}",$link);
						}
						break;
					case 'new':
						h3('Nova entrada');//.mysql1r('select max(id)+1 from caixa',$link));
						foreach ($camps[0] as $k=>$camp){
							if(($camps[1][$k]!='time')and($camps[1][$k]!='datetime'))$d[$camp]=$camps[5][$k];
						}
						break;
					case 'search':
						h3('Cerca');
						break;
					}break;
			default: tor();
				if(!preg_match("/^hidden_/",$camp)){
					if($camps[8][$k]!='')echo $camps[8][$k];
					else  echo "$camp".$camps[8][$k];
						  br();//tod();
				}
				if(preg_match("/^id_/",$camp)){
					$taula=explode("id_", $camp);
					$query="SELECT `ID`, `Value` from {$taula[1]} WHERE `lang`='{$config['lang_app']}' ORDER BY `ID`";
						$a=mysql2array($query,$link);
					$config['do']=='search'?select2llistes("m[{$config['table']}][$camp]",$a[0],$a[1],$config['old'][$camp],true):select2llistes("m[{$config['table']}][$camp]",$a[0],$a[1],$d["$camp"],true);
				}
				else {
					if (preg_match("/^idW_/",$camp)){
						$taula=explode("idW_", $camp);
						$query="SELECT `ID`, `Value` from {$taula[1]} WHERE  {$camps[8][$k]} ORDER BY `Value`";
						$a=mysql2array($query,$link);
						$config['do']=='search'?select2llistes("m[{$config['table']}][$camp]",$a[0],$a[1],$config['old'][$camp],true):select2llistes("m[{$config['table']}][$camp]",$a[0],$a[1],$d["$camp"],true);
					}
					else {
						if(preg_match("/^bool_/",$camp)){
						//$taula=explode("bold_", $camp);
						//$query="SELECT `ID`, `Value` from {$taula[1]} WHERE `lang`='{$_SESSION['lang_app']}' ORDER BY `ID`";
						//$a=mysql2array($query,$link);
							//select2llistes("m[nou][$camp]",$a[0],$a[1],$d["$camp"]);
							$config['do']=='search'?chek("m[{$config['table']}][$camp]",$config['old'][$camp],1):chek("m[{$config['table']}][$camp]",$d[$camp],1);
						}
						elseif(preg_match("/^hidden_/",$camp)){
							//echo"hidden!";
							//hidden("m[{$config['table']}][$camp]","buit!");
						}
						else{
							if($camps[1][$k]=="date"){
								if(($config['today']=="yes") or ($camps[8][$k]=="today")){//check into comment field if contains today
									$data=getdate();
									tria_data($data,"m[{$config['table']}][$camp]",$config['lang_app'],2,1);
								}
								else{
									if(isset($d[$camp]))$data=decodificadata($d[$camp]);
									else{	$data["mday"]=0;
										$data["mon"]=0;
										$data["year"]=0;
									}
									$avui=getdate();
									if($data['year']!=0)$inici=80-($avui[year]-$data[year]);
									else $inici=80;
									$config['do']=='search'?tria_data($config['old'][$camp],"m[{$config['table']}][$camp]",$config['lang_app'],$inici,($avui['year']-$data['year']+2),TRUE,TRUE,TRUE):tria_data($data,"m[{$config['table']}][$camp]",$config['lang_app'],$inici,($avui['year']-$data['year']+2),TRUE,TRUE,TRUE);
								}
							}
							else if ($camps[1][$k]=="time"){
								if(!isset($config["old"][$camp])){
									$config['old'][$camp]['hours']=24;
									$config['old'][$camp]['minutes']=61;
								}
									$config['do']=='search'?tria_time("m[{$config['table']}][$camp]",$config["old"][$camp],TRUE):tria_time("m[{$config['table']}][$camp]",$d[$camp]);
								}
							elseif ($camps[1][$k]=="datetime"){
								echo "datetime! not yet implemented into mysql.php";
							}
							elseif($camps[1][$k]=="mediumtext"){
								 $config['do']=='search'?textarea("m[{$config['table']}][$camp]",8,140,urldecode($config['old']["$camp"])):textarea("m[{$config['table']}][$camp]",8,20,urldecode($d["$camp"]));
							}
							else $config['do']=='search'?text("m[{$config['table']}][$camp]",14,urldecode($config['old']["$camp"])):text("m[{$config['table']}][$camp]",14,urldecode($d["$camp"]));
						}
					}
				}
			break;
		}
	}
	ttable();
}// function tableform
function new_update($config,$link,$debug=FALSE){
	//new/update registry
	if($debug){pre();print_r($config);tpre();}
	//$taula="user";
	$camps=mysql2array("SHOW full COLUMNS FROM {$config['table']} ",$link);//,$debug);
	if(!isset($config['ID'])){
		$config['ID']=mysql1r("SELECT MAX(ID)+1 from `{$config['table']}`",$link,$debug);
		//echo '$_SESSION[id]=';
		//echo $_SESSION['id'];
	}
	if(!isset($config['ID']))$config['ID']=1;//si no hi ha cap id... serà id=1
	$insert="INSERT INTO `{$config['table']}` (`ID` ";
	$values="VALUES ('{$config['ID']}' ";	
	for ($i=1;$i<count($camps[0]);$i++){//id is first element of table
		$tracto=$camps[0][$i];
		if($config[$config['table']][$tracto]!='')$insert.=", `$tracto`";
		switch($camps[1][$i]){
			case "date":
				$data=$config[$config['table']][$tracto];
				$mydata=$data['year']!=0?$data['year']:"%";
				$mydate=date2mysql($config[$config['table']][$tracto],true);
				$values.=", '$mydate'";
				if($debug){pre();print_r($config[$config['table']][$tracto]);echo $mydate; tpre();}
				break;
			case "time"://notimplemented
				$mytime=$config[$config['table']][$tracto]['hours'].':'.$config[$config['table']][$tracto]['minutes'];
				$values.=", '$mytime'";
				break;
			case "datetime"://just implemented hidden_now field
				if((preg_match("/^hidden_/",$camps[0][$i]))and(preg_match("/_now$/",$camps[0][$i]))){
					$insert.=", $tracto";
					$values.=", now()";
				}
				break;
			default:
				//if($debug){pre();print_r(Array ($camps[0][$i],$camps[1][$i],$config[$config['table']][$tracto]));tpre();}
				if($config[$config['table']][$tracto]!='')$values.=", '".urlencode($config[$config['table']][$tracto])."'";
				break;
		}
	}
// 	if($config['id2'])if($config['do']=="update"){ 
		if($config['ID2'])mysql_quer("DELETE FROM {$config['table']} WHERE `ID`='{$config['ID']}' and `{$config['ID2']}`='{$config['ID2Value']}'",$link,$debug);
		else if($config['do']=="update") mysql_quer("DELETE FROM {$config['table']} WHERE `ID`=('{$config['ID']}')",$link,$debug);
// 	}
	if($debug)echo "debug new_update function: insert: <br>".$insert.")".$values.")";
	mysqli_query($link,$insert.")".$values.")")or die($insert.")".$values.")".mysqli_error($link));
	return $config['ID'];
}
function search($config,$link){
	$camps=mysql2array("SHOW full COLUMNS FROM {$config['table']} ",$link);
	$cerca="SELECT * FROM {$config['table']} WHERE ";
	unset($controlcoma);
	for ($i=0;$i<count($camps[0]);$i++){
		$tracto=$camps[0][$i];
		if($config['object'][$config['table']][$tracto]!=""){
			if ($controlcoma){$q.=" AND ";}
			else $controlcoma=1;
			if($camps[1][$i]=="date"){ 
				$data=$config['object'][$config['table']][$tracto];
				$mydata=$data[year]!=0?$data[year]:"%";
				$q.="`$tracto` like '%$mydata%'";
			}
			elseif($camps[1][$i]=="time"){
					$data=$config['object'][$config['table']][$tracto];
					if($data[hours]>23){
						$q.="`$tracto` like '%{$data[hours]}%'";
					}
					unset($controlcoma);
			}
			else $q.="`$tracto` like '%".urlencode(trim($config['object'][$config['table']][$tracto]))."%'";
		}
		//else echo $tracto.'='.$_REQUEST['m'][$config['table']][$tracto].brr();
	}
	//echo "search debug:".$cerca.$q;
	return $cerca.$q;
}
function my_llista($link,$nom,$llista=""){
        h2c($nom);
	//$llista="Suma total avui efectiu";
	($llista!="")?$q="select * from `$llista`":$q="select * from `$nom`";
	$cap="SHOW full COLUMNS FROM `$llista` ";
	tab();
	$i=1;
	if($entradescap=mysqli_query($link,$cap)or die(errorr($cap.mysqli_error($link))))
		while($c=mysqli_fetch_row($entradescap)){
			echo($c[0]);tod();
			$i++;
		}
	$d=mysqli_query($link,$q)or die (errorr($q.mysqli_error($link)));
	while($c=mysqli_fetch_row($d)){
		tor();
		foreach ($c as $v){
			echo $v;
			tod();
		}
	}
	ttable();
}
?>
