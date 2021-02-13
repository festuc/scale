<?php
$myserver='myserver';
$myuser='mymysqluser';#chosse other than root if you are not in a localserver developement
$mypassword="your unsecure password on your insecure server";
$mydatabase='devel';
$telegramtoken="your bot token";
$telegrambotname="your_bot";
$telegramdebugchannel="Number";#a channel for debugtelegram function
$singkey='a very random string';
$sing_uri='https://yourcentralserver/SingInAtWork.php';
$tag_destination="printeruser@localhost:/home/printeruser/Documents/etiquetes";
$em['user']='your smtp mail '; #I use a gmail not very secure account
$em['passwd']='awS0m3password'; #this gmail password
function tag_print_command($tag_filename){
	exec(" ssh printuser@localhost \"inkscape /home/printuser/Documents/etiquetes/$tag_filename -o /tmp/tmp.ps ; lp /tmp/tmp.ps -d etiquetes \"");

}
function ticket_print_command($filename){
	//production mode? fix your /devel/filename to other folder if is not your folder chosse
  exec ("cat {$_SERVER['DOCUMENT_ROOT']}/devel/$filename >/dev/usb/lp0");	
//if do not work try this pairing ssh's http user keys with root, and fix your setup after it
		//exec ("ssh root@localhost \"cat {$_SERVER['DOCUMENT_ROOT']}/devel/$filename >/dev/usb/lp0\"");	
	
} 
function crea_pdf($nf){
	 exec("cd {$_SERVER['DOCUMENT_ROOT']}/devel/pdf; texi2pdf {$nf} ; chown http * ");
}
?>
