<?php
/* $Id: do_register.php 87 2004-11-07 00:19:15Z td $ */
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
          (c) 2000-2004 by ThWboard Development Group



          download the latest version:
            http://www.thwboard.de

          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================

*/

include "./inc/header.inc.php";

// http://www.securiteam.com/securitynews/5FP0C204KE.html
$register['username'] = ((isset($HTTP_POST_VARS['register_username'])) ? $HTTP_POST_VARS['register_username'] : '');

$register['username'] = str_replace("\r", '', $register['username']);
$register['username'] = str_replace("\n", '', $register['username']);
$register['username'] = str_replace(chr(160), '', $register['username']);
$register['username'] = trim($register['username']);

$errmsg = '';

if( !$config['allowregister'] )
{
	message('Registrierung nicht m&ouml;lich!', 'Eine Registrierung ist derzeit leider nicht m&ouml;lich.<br>Bitte versuchen Sie es zu einem sp&auml;teren Zeitpunkt erneut.');
}

if(is_flooding(FLOOD_REGISTER))
{
    message('Fehler', 'IP wegen '.$config['flood_login_count'].' Registrierungen f&uuml;r '.$config['flood_login_timeout'].' Minuten gesperrt.');
}

// check username
check_username($register['username']);

// email verification
$r_user = thwb_query("SELECT userid FROM ".$pref."user WHERE useremail='".addslashes($register['useremail'])."'");
if( mysql_num_rows($r_user) > 0 )
{
	$errmsg .= "Es existiert bereits ein Benutzer mit der Email-Adresse &laquo;$register[useremail]&raquo;.<br>
Wenn Sie ihr Passwort vergessen haben, klicken Sie bitte <a href=\"send_password.php\">hier</a>.<br>";
}

if( !check_email($register['useremail']) )
{
	$errmsg .= "Ihre E-Mailadresse ist nicht g&uuml;ltig!<br>Eine g&uuml;tige E-Mailadresse hat das Format <b>bezeichner@hoster.toplvl</b> (toplvl max. 4 Zeichen).";
}

while( !empty($config['bannedmails']) && list(, $bannedmail) = @each($config['bannedemails']) )
{
	$bm = stristr($register['useremail'], $bannedmail);
	if( $bm )
	{
		message('Ung&uuml;ltige E-Mailadresse',
			'Die von Ihnen gew&auml;hlte E-Mailadresse ist leider nicht erlaubt.');
	}
}

if( isset($errmsg) && strlen($errmsg) > 0 )
{
	message("Fehler","Es sind leider folgende Fehler aufgetreten:<br><br><font color='$style[color_err]'>$errmsg</font>");
}

// ready to register
if( $register['userpassword'] != $register['userpassword2'] )
{
  message('Fehler', 'Das Passwort und die Passwortwiederholung unterscheiden sich, bitte &uuml;berpr&uuml;fen Sie ihre Angaben.');
}

if( !$register['userpassword'] )
{
	message('Fehler', 'Bitte geben Sie ein Passwort an');
}

$time = time();
$register['hash'] = md5($time);

thwb_query("INSERT INTO ".$pref."user (username, userjoin, useremail, userpassword, groupids, useractivate)
	VALUES('".addslashes($register['username']) . "', '" . $time . "',
	'".addslashes($register['useremail'])."',	'".md5($register['userpassword'])."', ',$config[default_groupid],', ".(($config['use_email']) ? 1 : 0).")");

$register['userid'] = mysql_insert_id();

possible_flood(FLOOD_REGISTER, $register['userid']);

if( $config['use_email'] )
{
  $email = '';

	$TRegistermail = new Template("./templates/mail/register.mail");
	eval($TRegistermail->GetTemplate("email"));

	@mail($register['useremail'], $config['board_name'] . " - Registrierung", $email, "From: $config[board_admin]");

	message("Registrierung erfolgreich!",
		"Der neue User wurde angelegt.<br>Sie erhalten in K&uuml;rze eine Email mit einem Best&auml;tigungslink zur Aktivierung ihres Accounts.<br><strong>Achtung: Bei AOL ist davon auszugehen, dass die Email vom Spamfilter abgefangen wird.</strong>");
}
else
{
  $s = new_session();
  $g_user['have_cookie'] = false;

	setcookie("thwb_cookie", md5($register['userpassword']) . $register['userid'], (time() + 60 * 60 * 24 * 365));
	
	message('Registrierung erfolgreich!',
		'Sie wurden soeben erfolgreich registriert und eingeloggt. Viel Spa&szlig;!');
}

?>