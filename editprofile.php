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

define('THWB_NOSESSION_PAGE', true);

include "./inc/header.inc.php";

if(!$g_user['userid'])
{
  message('Fehler', 'G&auml;ste k&ouml;nnen ihr Profil nicht &auml;ndern.');
}

function EditboxEncode($string)
{
	$string = str_replace('&', '&amp;', $string);
	$string = str_replace('"', '&quot;', $string);
	$string = str_replace('<', '&lt;', $string);
	$string = str_replace('>', '&gt;', $string);
	
	return $string;
}

$Tframe=new Template("templates/" . $style['styletemplate'] . "/frame.html");
$Tprofile=new Template("templates/" . $style['styletemplate'] . "/profile.html");

$r_user = thwb_query("SELECT userid, username, userhomepage, userlocation, usericq, useraim, usermsn,
	useroccupation, userinterests, usersignature,
	userhideemail, userhidesig, userinvisible, usernoding,
	styleid, useremail, useravatar, userbday 
	FROM ".$pref."user WHERE userid='$g_user[userid]'");

$user = mysql_fetch_array($r_user);

$user['userhomepage'] = EditboxEncode($user['userhomepage']);
$user['userlocation'] = EditboxEncode($user['userlocation']);
$user['useroccupation'] = EditboxEncode($user['useroccupation']);
$user['useraim'] = htmlspecialchars($user['useraim']);
$user['usermsn'] = htmlspecialchars($user['usermsn']);

$avatarselect = '';
$AVATRAPRE = '';
$AVATRAOP = '';
$AVATRAROW = '';

if( ( $config['useravatar'] != 0 ) && ( $user['useravatar'] != 'notallowed' ) )
{
	$Tavatarrow = new Template("templates/" . $style['styletemplate'] . "/avatarrow.html");
	$Tavatarpre = new Template("templates/" . $style['styletemplate'] . "/avatarpre.html");
	$Tavatarop = new Template("templates/" . $style['styletemplate'] . "/avatarop.html");
	
	if( $config['useravatar'] == 3 ) 
	{
		$q_avatar = thwb_query("SELECT avatarurl FROM ".$pref."avatar WHERE avatarurl='".addslashes($user['useravatar'])."'");
		
		if( $a_avatar = mysql_fetch_array($q_avatar) ) 
		{
			if ( $a_avatar['avatarurl'] == $user['useravatar'] ) 
			{
				$isinstal = true;
			}
		}
	}
	
	if( $config['useravatar'] == 1 || $config['useravatar'] == 3 )
	{
		$r_avatar = thwb_query("SELECT avatarname, avatarurl FROM ".$pref."avatar");
		$avatarselect .= "<option value=\"avatar/noavatar.png\"" . ( (!$user['useravatar']) || (!isset($install) || !$isinstal) ? " selected" : "" ) . ">* Kein Avatar *</option>\n";
		
		while ( $avatars = mysql_fetch_array($r_avatar) ) 
		{
			if ( $avatars['avatarurl'] == 'avatar/noavatar.png' ) 
			{ 
				$avatars['avatarurl'] = ''; 
			}
			$avatarselect .= "<option value=\"" . $avatars['avatarurl'] . "\"" . ( $avatars['avatarurl'] == $user['useravatar'] ? " selected" : "" ) . ">" . $avatars['avatarname'] . "</option>\n";
		}
		
		eval($Tavatarpre->GetTemplate("AVATRAPRE"));
	}
	
	if ( ( $config['useravatar'] == 2 ) || ( $config['useravatar'] == 3) )
	{
		
		if ( $config['useravatar'] == 3 ) 
		{
			$alter = '<br>'.$style['smallfont'].'Alternative Avatarquelle des Benutzers:' . $style['smallfontend'] . '<br>';
			$alt_message = ' oder um die vorinstallierte Avatar Auswahl zu aktivieren';
			
			if ( !isset($isinstal) || !$isinstal ) 
			{ 
				$avatarentry = $user['useravatar']; 
			} 
			else
			{ 
				$avatarentry = ''; 
			}
		}
		else
		  {
		    $alter = $alt_message = '';
			$avatarentry = $user['useravatar']; 
		}
		
		eval($Tavatarop->GetTemplate('AVATRAOP'));
	}

	eval($Tavatarrow->GetTemplate('AVATRAROW'));
}
else
{
	$AVATRAROW = '';
}

if( $user['userhomepage'] == '' )
{
	$user['userhomepage'] = 'http://';
}

if( !isset($user['usericq']) || $user['usericq'] == 0 )
{
	$user['usericq'] = "";
}

if( !isset($user['userage']) || $user['userage'] == 0 )
{
	$user['userage'] = "";
}

if( $user['userhidesig'] == 1 )
{
	$hidesigyes = ' checked';
	$hidesigno = '';
}
else
{
	$hidesigyes = '';
	$hidesigno = ' checked';
}

if( $user['userhideemail'] == 1 )
{
	$hideemailno = '';
	$hideemailyes = ' checked';
}
else
{
	$hideemailno = ' checked';
	$hideemailyes = '';
}

if( $user['userinvisible'] == 1 )
{
	$invisibleyes = ' checked';
	$invisibleno = '';
}
else
{
	$invisibleyes = '';
	$invisibleno = ' checked';
}

if( $user['usernoding'] == 1 )
{
	$nodingno = '';
	$nodingyes = ' checked';
}
else
{
	$nodingno = ' checked';
	$nodingyes = '';
}

$styleoptions = '';
$r_style = thwb_query("SELECT styleid, stylename FROM ".$pref."style WHERE styleispublic=1");
if( mysql_num_rows($r_style) > 0 )
{
	$styleoptions = '<option value="0">-----------------------------</option>';
	while( $tstyle = mysql_fetch_array($r_style) )
	{
		$styleoptions .= "<option value=\"$tstyle[styleid]\"" . ($tstyle['styleid'] == $user['styleid'] ? " selected" : "") . ">$tstyle[stylename]</option>\n";
	}
}


// birthday stuff
$a_month = array(
	1 => 'Januar',
	2 => 'Februar',
	3 => 'M&auml;rz',
	4 => 'April',
	5 => 'Mai',
	6 => 'Juni',
	7 => 'Juli',
	8 => 'August',
	9 => 'September',
	10 => 'Oktober',
	11 => 'November',
	12 => 'Dezember'
);
$bdayform = '';

$user['userbday_year'] = (int)(substr($user['userbday'], 0, 4));
$user['userbday_month'] = (int)(substr($user['userbday'], 5, 2));
$user['userbday_day'] = (int)(substr($user['userbday'], 8, 2));

// day
$bdayform .= '<select name="user[userbday_day]" class="tbselect"><option value="0"></option>';
for( $i = 1; $i <= 31; $i++ )
{
	$bdayform .= '<option value="' . $i . '"' . ( $i == $user['userbday_day'] ? ' selected' : '' ) . '>' . sprintf('%02d', $i) . '</option>';
}
$bdayform .= '</select>';

// month
$bdayform .= '&nbsp;<select name="user[userbday_month]" class="tbselect"><option value="0"></option>';
for( $i = 1; $i <= 12; $i++ )
{
	$bdayform .= '<option value="' . $i . '"' . ( $i == $user['userbday_month'] ? ' selected' : '' ) . '>' . $a_month[$i] . '</option>';
}
$bdayform .= '</select>';

// year
$bdayform .= '&nbsp;<select name="user[userbday_year]" class="tbselect"><option value="0"></option>';
for( $i = 1930; $i <= 2000; $i++ )
{
	$bdayform .= '<option value="' . $i . '"' . ( $i == $user['userbday_year'] ? ' selected' : '' ) . '>' . $i . '</option>';
}
$bdayform .= '</select>';

// HTML Special Chars
$user['usersignature'] = htmlspecialchars($user['usersignature']);
$user['userinterests'] = htmlspecialchars($user['userinterests']);

$navpath .= 'Profil modifizieren';

eval($Tprofile->GetTemplate("CONTENT"));
eval($Tframe->GetTemplate());
