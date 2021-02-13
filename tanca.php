<?php
if($login==false){
	//formulari d'entrada
estil_capcal("tulsa@login");
// form($_SERVER['DOCUMENT_URI'],'POST');
form($_SERVER['PHP_SELF'],'POST');
divclass('row');
divclass('row');
// echo 'user';br();echo'<input name="user" value="usuari" onfocus="this.select();" autofocus />';
    echo 'user';br();echo'<input name="user" value="" size=5 autofocus />';
//text('user');
tdiv();divclass('row');
echo 'contrasenya';br();
pass('contra',5);
tdiv();divclass('row');
sumit1('Entrar!');
// address("Aqui has de posar usuari i contrasenya",left);
tdiv();
tdiv();
tform();
}
// else {
// divclass('sortir');
// 	a("{$_SERVER['SCRIPT_NAME']}?tancar=true","sortir");
// 	a("./?tancar=true","sortir");
// 	tdiv();
// }

//estil_capcal($webname,'https://'.$webname);
//$_SESSION['lang']=prefered_language($avaiable_languages,_SERVER['HTTP_ACCEPT_LANGUAGE']);
//echo '$_SERVER[\'REQUEST_URI\']'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].$_SERVER['REQUEST_URI'].'//'.$_SESSION['lang'][1];
//phpinfo();

//close web...
$filemod = filemtime(__FILE__);
$filemodtime = date(" j/m/Y h:i:s A", $filemod);
divclass('peu');
echo '&copy;'.mysql1r('select year(now()) from dual;',$link).' '.$ownername.' '.
ar($mytelegram,imgr('./img/telegram.png',"10px","10px" ,'mytelegram'),'blank').
ar($mytwitter,imgr('./img/twitter.png',"10px","10px" ,'mytwitter'),'blank').
ar($myfacebook,imgr('./img/facebook.png',"10px","10px" ,'myfacebook'),'blank').
ar($myinstagram,imgr('./img/instagram.png',"10px","10px",'mytelegram'),'blank').
' '.
ar('https://'.$webname.'/cookies/',imgr('./img/cookies.gif',"10px","10px",'cookies').$s[$lang]['see privacy']);//.$filemodtime;  
tdiv();

//divclass('cookie-bar');
//echo ar('https://'.$webname.'/cookies/',imgr('/img/cookies.gif',"10px","10px",'cookies').$s[$lang]['see privacy']);
//tdiv();
     //divclass('address');print ("Modificada el  $filemodtime");tdiv();
//echo'
//<script src="phpgeneral/bootstrap.min.js"></script>
//<script src="phpgeneral/jquery-2.1.4.min.js"></script>
//';
echo "</body>
</html>";
$link->close();

?>
