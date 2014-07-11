<?php
/* $Id: showevent.php 87 2004-11-07 00:19:15Z td $ */
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

function format_db_date($string_date)
{
  if ( $string_date == '')
  {
    $output = "<B>Error :</B> Bad Time String !";
  }
  else
  {
    $output = substr($string_date,8,2) . "." . substr($string_date,5,2) . "." . substr($string_date,0,4);
  }
return $output;
}

include "./inc/header.inc.php";
$Tshowevent = new Template("templates/" . $style['styletemplate'] . "/showevent.html");

if(!isset($event['eventtime']) || !isset($event['eventsubject']) || !isset($event['eventtext']))
{
  die("denied.");
}

// Searching Event
$r_calendar = mysql_query("SELECT calendar.*, user.username FROM " . $pref . "calendar AS calendar LEFT JOIN " . $pref . "user AS user ON calendar.userid=user.userid WHERE eventid = '".addslashes($event)."'");
$calendar = mysql_fetch_array($r_calendar);

if( $calendar['userid'] > 0 )
{
	$calendar['user'] = "	$style[smallfont]Von <a href=\"".build_link("v_profile.php?user[userid]=$calendar[userid]")."\" target=\"_blank\">" . parse_code( $calendar['username'] ) . "</a>$style[smallfontend]";
}
$calendar['eventtime'] = format_db_date($calendar['eventtime']);
$calendar['eventsubject'] = parse_code($calendar['eventsubject']);
$calendar['eventtext'] = parse_code($calendar['eventtext'], 1, 1, 1);

eval($Tshowevent->GetTemplate());
?>