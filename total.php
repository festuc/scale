<?php
require("login.php");
if ($login==false){header("Location: {$scale_uri}");$link->close();die;}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';
// unset($_SESSION['lastline']);
$r=detallrelacio($_REQUEST['uuid'],$ID_Empresa,date('Y'),$link);
//$r=$_SESSION['r'];
// pre($r);
// die;
//Aqui faria falta la lògica de via de pago, en cas que n'hi hagi... Ara no en tenim i si convé tornar enrera abans del update
//switch ($_REQUEST['via']){
// 	 case 1: //efectiu
//  case 2: //tarjeta presencial
//  case 3: //bizum
//  case 4: //tarjeta no presencial
//  case 5: //coinbase
// }

if(isset($_REQUEST['via']))$r['ID_Pagament']=$_REQUEST['via'];

if($r['ID_tipus_relacio']==1){
	$NEW_tipus_relacio=mysql1r("select ID_tipus_relacio from Pagament where ID='{$_REQUEST['via']}'",$link);
	$ID_Empresa_a_mxgestio=mysql1r("SELECT `ID_Empresa_a_mxgestio` FROM `Empresa` WHERE `ID` = '$ID_Empresa'",$link);
	if(($ID_Empresa_a_mxgestio=='1')&&($NEW_tipus_relacio=='3'))$NEW_tipus_relacio=4;
	$NEWID=mysql1r("select COALESCE(MAX(ID), 0)+1 from `{$prefixrelacio}index_relacio` where ID_tipus_relacio='$NEW_tipus_relacio' ",$link);
	$qupdate_lines="UPDATE `{$prefixrelacio}line` SET`ID` = '$NEWID',`ID_tipus_relacio` = '$NEW_tipus_relacio' WHERE `ID`='{$r['ID']}' AND `ID_tipus_relacio` = '{$r['ID_tipus_relacio']}' ";
	if(!$link->query($qupdate_lines)) {error(mysqli_error($link));echo "error assignant línees";die;}
}
else {
	$NEW_tipus_relacio=$r['ID_tipus_relacio'];
	$NEWID=$r['ID'];
}

$qtanca="UPDATE `{$prefixrelacio}index_relacio` SET `relacio_oberta` = '0', `ID_Pagament`='{$r['ID_Pagament']}', `ID`='$NEWID',`ID_tipus_relacio` ='$NEW_tipus_relacio'
		WHERE `ID`='{$r['ID']}' AND `ID_tipus_relacio` = '{$r['ID_tipus_relacio']}' ";
$r['email']=urldecode($r['email']);
if(!$link->query($qtanca)) {error(mysqli_error($link));echo "no l'he trobada $qtanca ";die;}
$r['ID_tipus_relacio']=$NEW_tipus_relacio;
$r['ID']=$NEWID;
$r['tipus_relacio']['ID']=$NEW_tipus_relacio;
$r['tipus_relacio']['Value']=mysql1r("SELECT Value from tipus_relacio where ID='$NEW_tipus_relacio'",$link);

if(($ID_Empresa==2)&&($NEW_tipus_relacio==4)&&($r['ID_Pagament']==1)){ //obro cashkepper
	if (!extension_loaded('sockets')) {die('The sockets extension is not loaded.');}
            $service_port=16501;
            $address="figueres";
            $socket=socket_create(AF_INET, SOCK_STREAM,SOL_TCP);
            if ($socket === false) die( "socket_create() fall<C3><B3>: raz<C3><B3>n: " . socket_strerror(socket_last_error()) . "\n");
            $result=socket_connect($socket,$address,$service_port);
            if ($result === false) echo("no he pugut conectar amb el cash keeper no esta obert<B3>.\nRaz<C3><B3>n: ($result) " . socket_strerror(socket_last_error($socket)) . "\n");
			$in="C|m".mysql1r("select curdate()",$link)."{$r['tipus_relacio']['Value']}:{$ID_Empresa}.{$r['ID']}|{$r['Total']}";
			socket_write($socket,$in,strlen($in));
			socket_close ($socket);
}

if(($NEW_tipus_relacio==7)||($NEW_tipus_relacio==8)){
	$year=date('Y');
		//si es comanda o traspás no surt valorat TODO a pdf buit!
	$ulinea="update `{$year}_Empresa_{$ID_Empresa}_line` SET cost='0',`discount`='0',`IVA`='0'
						WHERE `ID` = '{$r['ID']}' AND `ID_tipus_relacio` = '{$r['ID_tipus_relacio']}' ;";
// 	if($debug)
// 	pre($ulinea);
	$a=$link->query($ulinea);
// 	die;
}
if($r['phone']!=''){
	pdf($r);
	if($r['Empresa']['telegram_relacions']!=''){
		exec ("scp ./pdf/{$r['uuid']}.pdf ametlles.tulsa.eu:/https/ametlles.tulsa.eu/html/pdf/");
		$pdf="https://ametlles.tulsa.eu/pdf/{$r['uuid']}.pdf";
// 		echo $pdf;br();
		$sms=sms($r);
		$wa=relacio2whatsapp($r,$sms);
		$telegramme=urlencode("Hola hem fet: ".$r['tipus_relacio']['Value']."\n té el teléfon: {$r['phone']}\n$wa\n$sms\n".$pdf);
		$pdf=urlencode($pdf);
		// echo "https://api.telegram.org/bot$telegramtoken/sendMessage?" . http_build_query($data) ;
		$response = file_get_contents("https://api.telegram.org/bot$telegramtoken/sendMessage?chat_id=-{$r['Empresa']['telegram_relacions']}&text=$telegramme" );
// 		$data[ 'chat_id' => '-'.$r['Empresa']['telegram'] , 'document' => $scale_uri.'/pdf/'.$r['tipus_relacio']['uuid'].'.pdf' ];
		$response = file_get_contents("https://api.telegram.org/bot$telegramtoken/sendDocument?-{$r['Empresa']['telegram_relacions']}&document=$pdf");
// 		echo 'telegram enviat?';
	}
// 	die;
}
if(($ID_Empresa==2)&&($NEW_tipus_relacio==4)){
	exec ("lp {$_SERVER['DOCUMENT_ROOT']}/scale/pdf/{$r['uuid']}}pdf -d figueres");
}
if(($ID_Empresa==2)&&($NEW_tipus_relacio==2)){
	exec ("lp {$_SERVER['DOCUMENT_ROOT']}/html/scale/pdf/{$r['uuid']}}pdf -d figueres");
	exec ("lp {$_SERVER['DOCUMENT_ROOT']}/html/scale/pdf/{$r['uuid']}}pdf -d figueres");
// 	
}
if (correuvalid($r['email'])){
	$mail = new PHPMailer(true);
	try 	{
	    //Server settings
// 	    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
	    $mail->isSMTP();                                            // Send using SMTP
	    $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
	    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
	    $mail->Username   = $em['user'];                     		// SMTP username
	    $mail->Password   = $em['passwd'];                               // SMTP password
	    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
   	 	$mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
		$mail->CharSet = 'UTF-8';
	    //Recipients
	    $mail->setFrom($r['Empresa']['email'], $r['Empresa']['Value']);
	    $mail->addAddress($r['email'],urldecode($r['fiscal_name']));     // Add a recipient
	    $mail->addBCC($r['Empresa']['email']);
	    // Attachments
	    $mail->addAttachment("pdf/{$r['uuid']}.pdf");         // Add attachments

	    // Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = 'Enviem '."{$r['tipus_relacio']['Value']}: {$r['Empresa']['ID']}.{$r['ID']}";
	    $mail->Body    = "
Moltes gràcies per confiar amb nosaltres
<br/>
L’informem que les seves dades personals,  que puguin constar en aquesta comunicació, estan incorporades en un fitxer propietat de {$r['Empresa']['Value']}, amb la finalitat d'enviar-li comunicacions de caire comercial o de prestació de serveis. Si desitja exercitar els drets d'accés, rectificació, cancel•lació o oposició pot dirigir-se per escrit a la següent direcció: {$r['Empresa']['Direccio']} {$r['Empresa']['Poblacio']}
<br/>
En el cas que no desitgi rebre més comunicacions a través del correu electrònic, pot enviar un missatge a la següent adreça electrònica: {$r['Empresa']['email']}<br/>
<small>

Advertencia: La Información incluida en este e-mail es CONFIDENCIAL, siendo para uso exclusivo del destinatario arriba mencionado. Si Ud. lee este mensaje y no es el destinatario indicado, le informamos que está totalmente prohibida cualquier utilización, divulgación, distribución y/o reproducción de esta comunicación, total o parcial, sin autorización expresa en virtud de la legislación vigente. Si ha recibido este mensaje por error, le rogamos nos lo notifique inmediatamente por esta vía y proceda a su eliminación junto con sus ficheros anexos sin leerlo, ni difundir, ni almacenar o copiar su contenido.
<br/>
Advertència: La informació inclosa en aquest e-mail és CONFIDENCIAL, essent per a ús exclusiu del destinatari a dalt esmentat. Si vostè llegeix aquest missatge i no és el destinatari indicat, li informem que està totalment prohibida qualsevol utilització, divulgació, distribució i/o reproducció d’aquesta comunicació, total o parcial, sense autorització expressa en virtut de la legislació vigent. Si ha rebut aquest missatge per error, li preguem que ens ho notifiqui immediatament per aquesta via i procedeixi a la seva eliminació juntament amb els fitxers annexes sense llegir-lo, ni difondre, ni emmagatzemar o copiar el seu contingut.<br/>
</small>
";
	    $mail->AltBody = "
Moltes gràcies per confiar amb nosaltres
L’informem que les seves dades personals,  que puguin constar en aquesta comunicació, estan incorporades en un fitxer propietat de {$r['Empresa']['Value']}, amb la finalitat d'enviar-li comunicacions de caire comercial o de prestació de serveis. Si desitja exercitar els drets d'accés, rectificació, cancel•lació o oposició pot dirigir-se per escrit a la següent direcció: {$r['Empresa']['Direccio']} {$r['Empresa']['Poblacio']}  En el cas que no desitgi rebre més comunicacions a través del correu electrònic, pot enviar un missatge a la següent adreça electrònica: {$r['Empresa']['email']}

Advertència: La informació inclosa en aquest e-mail és CONFIDENCIAL, essent per a ús exclusiu del destinatari a dalt esmentat. Si vostè llegeix aquest missatge i no és el destinatari indicat, li informem que està totalment prohibida qualsevol utilització, divulgació, distribució i/o reproducció d’aquesta comunicació, total o parcial, sense autorització expressa en virtut de la legislació vigent. Si ha rebut aquest missatge per error, li preguem que ens ho notifiqui immediatament per aquesta via i procedeixi a la seva eliminació juntament amb els fitxers annexes sense llegir-lo, ni difondre, ni emmagatzemar o copiar el seu contingut.

Advertencia: La Información incluida en este e-mail es CONFIDENCIAL, siendo para uso exclusivo del destinatario arriba mencionado. Si Ud. lee este mensaje y no es el destinatario indicado, le informamos que está totalmente prohibida cualquier utilización, divulgación, distribución y/o reproducción de esta comunicación, total o parcial, sin autorización expresa en virtud de la legislación vigente. Si ha recibido este mensaje por error, le rogamos nos lo notifique inmediatamente por esta vía y proceda a su eliminación junto con sus ficheros anexos sin leerlo, ni difundir, ni almacenar o copiar su contenido.
";

   		 $mail->send();
   		 echo 'Message has been sent';
	} catch (Exception $e) {
    	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
}

// else echo'correu no valid '.$r['email'];
unset ($_SESSION['r']);
$link->close();
exec ("rm pdf/{$r['uuid']}.*");
// header("Location: {$scale_uri}tiquet.php?uuid={$r['uuid']}");
// pre($r);
// a($scale_uri);die;
  header("Location: {$scale_uri}?{$r['uuid']}");
// phpinfo();
?>
