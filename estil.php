<?php
//include que conté els estils de capçalera i peu de plana

function estil_capcal($titol="Hauries de pasar alguna cosa com a titol no?",$nom="./"){
//html5
echo  "<!DOCTYPE html><html lang=\"ct\"><head><meta charset=\"utf-8\">";
//echo'<link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet">';
echo"<link rel=\"stylesheet\" type=\"text/css\" href=\"./estil.css\"><title>$titol</title></head><body><a href=\"$nom\"><h1>$titol</h1></a>";https://ametlles.tulsa.eu/coder/assets/images/nav-close.gif
//echo'<meta name="viewport" content="width=device-width, initial-scale=1">';


}
function estil_tancament($link,$origen=__FILE__)
{
require_once('vars.php');
$filemod = filemtime($origen);
$filemodtime = date(" j/m/Y h:i:s A", $filemod);
divclass('peu');
echo '&copy;'.date('Y').' '.$ownername.' '.
ar($mytelegram,imgr('/img/telegram.png',"10px","10px" ,'mytelegram'),'blank').
ar($mytwitter,imgr('/img/twitter.png',"10px","10px" ,'mytwitter'),'blank').
ar($myfacebook,imgr('/img/facebook.png',"10px","10px" ,'myfacebook'),'blank').
ar($myinstagram,imgr('/img/instagram.png',"10px","10px",'mytelegram'),'blank').
' '.
ar('https://'.$webname.'/cookies/',imgr('/img/cookies.gif',"10px","10px",'cookies').$s[$lang]['see privacy']).$filemodtime;  
tdiv();
//phpinfo();

// divclass('cookie-bar');
// echo ar('https://'.$webname.'/cookies/',imgr('/img/cookies.gif',"10px","10px",'cookies').$s[$lang]['see privacy']);
// tdiv();
     //divclass('address');print ("Modificada el  $filemodtime");tdiv();
echo'
<script src="phpgeneral/bootstrap.min.js"></script>
<script src="phpgeneral/jquery-2.1.4.min.js"></script>
';
echo "</body>
</html>";
}
?>
