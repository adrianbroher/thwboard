<?php
/* $Id: v_profile.php 87 2004-11-07 00:19:15Z td $ */
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

define('THWB_NOSESSION_PAGE', true);

include "./inc/header.inc.php";

if(!isset($user) || !isset($user['userid']))
{
  $user['userid'] = -1;
}

// age calculation taken from http://www.mysql.com/doc/en/Date_calculations.html
$r_user = thwb_query( "SELECT
		userid,
		username,
		useremail,
		userhomepage,
		userlocation,
		usericq,
		useraim,
		usermsn,
		userbday,
		useroccupation,
		userinterests,
		usersignature,
		userposts,
		userjoin,
		userlastpost,
		userrating,
		uservotes,
		userhideemail,
		userbday,
		usertitle,
		(YEAR(CURRENT_DATE)-YEAR(userbday)) - (RIGHT(CURRENT_DATE,5)<RIGHT(userbday,5)) AS userage
	FROM
		$pref"."user
	WHERE
		userid=".intval($user['userid'])
);
	
if( mysql_num_rows($r_user) < 1 )
{
	message("Fehler","Benutzer existiert nicht");
}

$user = mysql_fetch_array($r_user);

$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$Tprofile = new Template("templates/" . $style['styletemplate'] . "/viewprofile.html");


$user['userhomepage'] = str_replace('"', '', $user['userhomepage']);

if( trim($user['userhomepage']) == "http://" )
	$user['userhomepage'] = '';
else
	$user['userhomepage'] = '<a href="http://'. substr($user['userhomepage'], 7) .'" target="_blank">'. parse_code($user['userhomepage']) .'</a>';
$user['userjoin'] = form_date($user['userjoin']);
$user['userlastpost'] = form_date($user['userlastpost']);
$user['userinterests'] = parse_code($user['userinterests'], 1, !$config['imageslevel'], 1, $config['smilies']);
$user['usersignature'] = parse_code($user['usersignature'], 1, !$config['imageslevel'], 1, $config['smilies']);
$user['userlocation'] = parse_code($user['userlocation']);
$user['useroccupation'] = parse_code($user['useroccupation']);

$user['userrating'] = "- (Vom Administrator deaktiviert)";

if( $user['userbday'] == '0000-00-00' )
{
	$user['userage'] = '';
}
else
{
	$user['userage'] = (int)($user['userage']);
}

if( $user['usericq'] == 0 )
{
	$user['usericq'] = "";
}

if( $config['showpostslevel'] == 0 )
{
	$user['userposts'] = "- (Vom Administrator deaktiviert)";
}
elseif( $config['showpostslevel'] == 1 && ($g_user['userid'] != $user['userid']) )
{
	if( $g_user['userisadmin'] )
	{
		$user['userposts'] = '- (Versteckt)'.$style['smallfont'].' [Admin: Postcount = '.$user['userposts'].' ]'.$style['smallfontend'];
	}
	else
	{
		$user['userposts'] = '- (Versteckt)';
	}
}

$user['useremail'] = get_email($user);

$user['username'] = parse_code($user['username']);

$user['userip'] = '';

if( $g_user['userisadmin'] )
{
	$r_online = thwb_query("SELECT onlineip FROM $pref"."online WHERE userid='$user[userid]' AND onlinetime > ".(time() - $config['session_timeout']) );
	if( mysql_num_rows($r_online) > 0 )
	{
		$online = mysql_fetch_array($r_online);
		$user['userip'] = $style['smallfont'].' [Admin: IP = '.$online['onlineip'].', Hostname = '.gethostbyaddr($online['onlineip']).' ]'.$style['smallfontend'];
	}
}

$user['useraim'] = parse_code($user['useraim']);
$user['usermsn'] = parse_code($user['usermsn']);
$userurlname = rawurlencode($user['username']);

$navpath .= 'Profilansicht';

eval($Tprofile->GetTemplate("CONTENT"));
eval($Tframe->GetTemplate());

?>
