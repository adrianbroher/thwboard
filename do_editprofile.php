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

if(!isset($HTTP_POST_VARS['Submit']))
{
  die("denied");
}

// http://www.securiteam.com/securitynews/5FP0C204KE.html
$user = ((isset($HTTP_POST_VARS['user'])) ? $HTTP_POST_VARS['user'] : '');

$err_msg = '';

function EditboxDecode($string)
{
    $string = str_replace('&amp;', '&', $string);
    $string = str_replace('&quot;', '"', $string);
    $string = str_replace('&lt;', '<', $string);
    $string = str_replace('&gt;', '>', $string);

    return $string;
}

function check_siglen($string)
{
    global $config;

    // lines
    $a_line = explode("\n", $string);
    if( count($a_line) > $config['sig_maxlines'] )
    {
        return 0;
    }

    // len
    $length = 0;
    if( $a_line )
    {
        while( list(, $line) = each($a_line) )
        {
            $length += strlen(ereg_replace('\[([^\[]*)\]', '', $line));
        }
    }

    if( $length > $config['sig_maxlen'] )
    {
        return 0;
    }

    return 1;
}

$navpath .= "Profil modifizieren";
if( $g_user['userid'] == 0 )
{
    message('Fehler', 'Beim Ausf&uuml;hren dieses Befehls ist ein Fehler aufgetreten. Entweder sind Sie nicht eingeloggt, oder haben versucht, sich widerrechtlich Zugang zu Ihnen nicht zug&auml;nglichen Funktionen zu schaffen.<br>Bitte kontaktieren Sie den Administrator dieses Boards, wenn Sie Probleme oder Fragen haben.');
}

$user['styleid'] = intval( $user['styleid']);
if( $user['styleid'] != 0 )
{
    // verify that the user selected a public style ..
    $r_style = thwb_query("SELECT styleispublic FROM ".$pref."style WHERE styleid=$user[styleid]");
    $tstyle = mysql_fetch_array($r_style);

    if( $tstyle['styleispublic'] != 1 )
    {
        message('Fehler', 'Der von Ihnen gew&auml;hlte Style ist leider nicht g&uuml;ltig.');
    }
}

if( md5($user['userpassword']) != $g_user['userpassword'] )
{
    $err_msg .= "Das angegebene (derzeitige) Passwort ist nicht korrekt!<br>";
}


if( $user['usernewpassword'] || $user['usernewpassword2'] )
{
    if ( $user['usernewpassword'] != $user['usernewpassword2'] )
    {
        $err_msg .= "Bei dem Versuch das Passwort zu &auml;ndern muss ein Tippfehler vorgekommen sein. Bitte achten Sie auf Gro&szlig;- und Kleinschreibung<br>";
    }
    else
    {
        $user['userpassword'] = md5($user['usernewpassword']);
    }
}
else
{
    $user['userpassword'] = $g_user['userpassword'];
}

$user['userhomepage'] = str_replace('"', '', EditboxDecode($user['userhomepage']));
if( substr($user['userhomepage'], 0, 7) != "http://" )
{
    $user['userhomepage'] = "http://" . $user['userhomepage'];
}
$user['userlocation'] = EditboxDecode($user['userlocation']);
$user['useroccupation'] = EditboxDecode($user['useroccupation']);

if(isset($intavatar) && strchr($intavatar, '"'))
{
  $intavatar = '';
}

if(isset($intavatar))
{
     $check_r = thwb_query( "SELECT avatarid FROM " . $pref . "avatar WHERE avatarurl='" . addslashes( $intavatar ) . "'" );
     if( mysql_num_rows( $check_r ) == 0 )
       {
     $intavatar = '';
       }
}

if(!isset($g_user['useravatar']))
{
  $g_user['useravatar'] = '';
}

if(!isset($user['useravatar']))
{
  $user['useravatar'] = '';
}
else
{
  $user['useravatar'] = str_replace('"', '', EditboxDecode($user['useravatar']));
}

if ( $g_user['useravatar'] == "notallowed" )
{
    $user['useravatar'] = "notallowed";
}
elseif ( $config['useravatar'] == 1 )
{
    if ( $intavatar == "avatar/noavatar.png" )
    {
        $user['useravatar'] = "";
    }
    else
    {
        $user['useravatar'] = $intavatar;
    }
}
elseif ( $config['useravatar'] == 2 )
{
    if ( $user['useravatar'] )
    {
      checksize($user['useravatar']);
    }
}
elseif ( $config['useravatar'] == 3 )
{
    if( ( $intavatar != "avatar/noavatar.png" ) && ( !$user['useravatar'] ) )
    {
        $user['useravatar'] = $intavatar;
    }
    elseif ( $user['useravatar'] )
    {
        checksize($user['useravatar']);
    }
    else
    {
        $user['useravatar'] = "";
    }
}
else
{
    $user['useravatar'] = "";
}

// check signature length.
if( $config['sig_restrict'] )
{
    if( !check_siglen(preparse_code($user['usersignature'])) )
    {
        $err_msg .= 'Ihre Signatur enth&auml;lt zuviele Zeichen (max. '.$config['sig_maxlen'].') oder besteht aus zu vielen Zeilen (max. '.$config['sig_maxlines'].').';
    }
}

if( !$user['userpassword'] )
{
    $err_msg .= 'Bitte geben Sie ein Passwort an';
}

if( strlen($err_msg)>0 )
{
    message("Fehler","Es sind leider folgende Fehler aufgetreten:<br><br><font color='$style[color_err]'>$err_msg</font>");
}
else
{
    if( $user['userhomepage'] == 'http://' )
    {
        $user['userhomepage'] = '';
    }

    if( $user['userbday_day'] == 0 || $user['userbday_month'] == 0 || $user['userbday_year'] == 0 )
    {
        $user['userbday'] = '0000-00-00';
    }
    else
    {
        $user['userbday'] = sprintf('%04d-%02d-%02d', $user['userbday_year'], $user['userbday_month'], $user['userbday_day']);
    }
    if( !thwb_query("UPDATE ".$pref."user SET
        userpassword='".addslashes($user['userpassword'])."',
        userhomepage='".addslashes($user['userhomepage'])."',
        userlocation='".addslashes($user['userlocation'])."',
        usericq='".addslashes($user['usericq'])."',
        useraim='".addslashes($user['useraim'])."',
        usermsn='".addslashes($user['usermsn'])."',
        userbday='".addslashes($user['userbday'])."',
        useroccupation='".addslashes($user['useroccupation'])."',
        useravatar='" .addslashes($user['useravatar']). "',
        userinterests='".addslashes($user['userinterests'])."',
        usersignature='".addslashes(preparse_code($user['usersignature']))."',
        userhideemail='".intval($user['userhideemail'])."',
        userinvisible='".intval($user['userinvisible'])."',
        usernoding='".intval($user['usernoding'])."',
        styleid='".intval($user['styleid'])."',
        userhidesig='".intval($user['userhidesig'])."' WHERE userid='$g_user[userid]'") )
    {
        message("Fehler","Interner Fehler!");
    }
}

// *try* to reset password
if( $user['usernewpassword'] || $user['usernewpassword2'] )
{
    setcookie("thwb_cookie", $user['userpassword'] . $g_user['userid'], (time() + 60 * 60 * 24 * 365));
}

message("Update erfolgreich!","Das Update war erfolgreich!");
