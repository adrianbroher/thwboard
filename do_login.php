<?php
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
$login_name = ((isset($HTTP_POST_VARS['login_name'])) ? $HTTP_POST_VARS['login_name'] : '');

if(is_flooding(FLOOD_LOGIN))
{
    message('Fehler', 'IP wegen '.$config['flood_login_count'].' fehlerhafter Loginversuche f&uuml;r '.$config['flood_login_timeout'].' Minuten gesperrt.');
}

if(!isset($login_cookie))
{
  $login_cookie = 0;
}

$msg = '';

if(!isset($login_password))
{
  $login_password = '';
}

$navpath .= "Login";
if( !$login_name )
{
	$msg .= "Sie haben vergessen einen Usernamen anzugeben.<br>";
}
if( !$login_password )
{
	$msg .= "Sie haben vergessen ein Passwort anzugeben.<br>";
}

$r_user = thwb_query("SELECT userid, userpassword, useractivate FROM ".$pref."user WHERE username='" . addslashes($login_name) . "'");
if( mysql_num_rows($r_user) < 1 )
{
	$msg .= "Der Angegebene Benutzername existiert nicht.<br>";
}

$user = mysql_fetch_array($r_user);
if( $user['userpassword'] != md5($login_password) )
{
	$msg .= "Das Passwort ist leider falsch.<br>";

    possible_flood(FLOOD_LOGIN, $user['userid']);
}

if( $user['useractivate'] )
{
  $msg .= "Sie haben ihren Account noch nicht aktiviert.";
}

if( isset($msg) && strlen($msg) > 0 )
{
	message("Fehler", "Es sind leider Fehler aufgetreten:<font color='$style[color_err]'><br><br>$msg</font>");
}

global $g_user, $s;

$g_user = array();
$g_user['userisadmin'] = false;
$g_user['userid'] = $user['userid'];
$g_user['have_cookie'] = $login_cookie;

$s = new_session();

$g_user['have_cookie'] = false;

if($login_cookie)
{
  setcookie("thwb_cookie", md5($login_password) . $user['userid'], (time() + 60 * 60 * 24 * 365));
}

if(empty($source))
{
    $source = 'index.php';
}
else
{
    $source = urldecode($source);
}

// $source xss vuln fix by tendor
$source = str_replace(array('"', '<', '>'), array('%22', '%3c', '%3e'), $source);

message_redirect('Sie wurden erfolgreich eingeloggt, bitte warten ...', $source);

?>
