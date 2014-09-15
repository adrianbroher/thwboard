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


// ===================================================
// Hier kann die maximale Anzahl der Avatar-Bilder pro
// Zeile eingestellt werden, einfach die vorgegebene
// Nummer durch die gewünschte Zahl ersetzen:
$maxpics = 5;
// ===================================================

define('THWB_NOSESSION_PAGE', true);

include "./inc/header.inc.php";

$navpath .= 'Alle Avatare auflisten';

$Tframe=new Template("templates/" . $style['styletemplate'] . "/frame.html");
$Tavatar=new Template("templates/" . $style['styletemplate'] . "/listavatar.html");

if( isset($sortbyname) && $sortbyname )
{
	$avatar_sort = "$style[stdfont]<a href=\"".build_link("listavatar.php")."\">Nach Avatar-Nummer sortieren</a>$style[stdfontend]";
	$orderby = "avatarname";
} 
else 
{
	$avatar_sort = "$style[stdfont]<a href=\"".build_link("listavatar.php?sortbyname=1")."\">Nach Namen sortieren</a>$style[stdfontend]";
	$orderby = "avatarid";
}

$start = $e = 0;
$r_avatar = thwb_query("SELECT avatarid, avatarname, avatarurl FROM ".$pref."avatar ORDER BY $orderby");
$avatar_rows = '';
while( $avatar_data = mysql_fetch_array($r_avatar) ) 
{
	$start++;

	$avatar_rows .= "<td".($e % 2 == 0 ? ' bgcolor="'.$style['CellA'].'"' : ' bgcolor="'.$style['CellB'].'"')."><img src=\"$avatar_data[avatarurl]\" border=\"0\"><br><b>".$style['smallfont']."$avatar_data[avatarid]. $avatar_data[avatarname]</font></b></td>\n";
	if ( $start == $maxpics )
	{
		$avatar_rows .= "</tr><tr>";
		$start = 0;
		$e++;
	}
}

eval($Tavatar->GetTemplate("CONTENT"));
eval($Tframe->GetTemplate());
