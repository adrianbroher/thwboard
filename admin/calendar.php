<?php
/* $Id: calendar.php 87 2004-11-07 00:19:15Z td $ */
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
include "common.inc.php";

if ( $action == "add" ) 
{
  if ( $newsubject == '') 
  {
    $newsubject = "&laquo; N/A &raquo;";
  }
  if ( $newtext == '') 
  {
    $newtext = "&laquo; N/A &raquo;";
  }
  if ( $newstatus == '') 
  {
    $newstaus = "1";
  }
  $insert_event = mysql_query("INSERT INTO " . $pref . "calendar (eventtime,eventsubject,eventtext,eventactive,userid) VALUES ('$newdate[year]-$newdate[month]-$newdate[day]','".addslashes($newsubject)."','".addslashes($newtext)."','$newstatus','$g_user[userid]');");
  echo mysql_error();
}
if ( $action == "delete" )
{
  $del_event = mysql_query("DELETE FROM " . $pref . "calendar WHERE eventid = '".addslashes($event)."' ");
}
if ( $action == "saveedit" )
{
  $del_event = mysql_query("UPDATE " . $pref . "calendar SET eventsubject = '".addslashes($newsubject)."', eventtext = '".addslashes($newtext)."', eventtime = '$newdate[year]-$newdate[month]-$newdate[day]', eventactive = '$newstatus' WHERE eventid = '$event'");
}
tb_header();
if ( $action == "edit" )
{
  $r_calendar = mysql_query("SELECT * FROM " . $pref . "calendar WHERE eventid = '$event'");
  $calendar = mysql_fetch_array($r_calendar);
  print('<B>calendar Settings &raquo; Edit Event "' . $calendar['eventsubject'] . '":</B><BR>
  <hr width="100%" noshade>
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td colspan="2" bgcolor="#999999">
        <font size="2" color="white"><b>Event settings :</b></font>
        </td>
      </tr>
  </table>
  <Table border=0 cellspacing=0>
  <form action="calendar.php?session=' . $session . '&action=saveedit" method="post">
  <TR><TD><B>Date :</B></TD></TR>
  <TR><TD><input class="tbinput" type="Text" name="newdate[day]" size="3" maxlength="2" value="' .substr($calendar['eventtime'],8,2). '">.<input class="tbinput" type="text" name="newdate[month]" size="3" maxlength="2" value="' . substr($calendar['eventtime'],5,2) . '">.<input class="tbinput" type="text" name="newdate[year]" size="5" maxlength="4" value="' . substr($calendar['eventtime'],0,4) . '"></TD></TR>
  <TR><TD><B>Subject :</B></TD></TR>
  <TR><TD><input class="tbinput" type="text" name="newsubject" size="30" value="' . $calendar['eventsubject'] . '"></TD></TR>
  <TR><TD><B>Text :</B></TD></TR>
  <TR><TD><textarea class="tbinput" name="newtext" cols=30 rows=10>' . $calendar['eventtext'] . '</textarea></TR></TD>
  <TR><TD><B>Status :</B></TD></TR>
  <TR><TD><input type=radio name=newstatus value="1" ' . ( $calendar['eventactive'] == "1" ? " checked" : "" ) . '> - Active 
  <BR><input type=radio name=newstatus value="0" ' . ( $calendar['eventactive'] == "0" ? " checked" : "" ) . '> - Deactivated</TD></TR>
  <TR><td colspan="2" bgcolor="#999999" align="center"><font size="2" color="white"><input type="hidden" name="event" value="' . $event . '"><input type=submit value="Save Event"> </font></td></TR>
  </TABLE>
  </form>
  ');
}
else
{
  // Creating lists
  $r_calendar['active'] = mysql_query("SELECT calendar.eventid, calendar.eventtime, calendar.eventsubject, calendar.eventtext, calendar.eventactive, user.username FROM " . $pref . "calendar AS calendar LEFT JOIN " . $pref . "user AS user ON calendar.userid=user.userid WHERE eventactive = 1 AND eventtime > '" . date("Y-m-d") . "' ORDER BY eventtime ASC");
  $r_calendar['active_old'] = mysql_query("SELECT calendar.eventid, calendar.eventtime, calendar.eventsubject, calendar.eventtext, calendar.eventactive, user.username FROM " . $pref . "calendar AS calendar LEFT JOIN " . $pref . "user AS user ON calendar.userid=user.userid WHERE eventactive = 1 AND eventtime < '" . date("Y-m-d") . "' ORDER BY eventtime ASC");
  $r_calendar['active_today'] = mysql_query("SELECT calendar.eventid, calendar.eventtime, calendar.eventsubject, calendar.eventtext, calendar.eventactive, user.username FROM " . $pref . "calendar AS calendar LEFT JOIN " . $pref . "user AS user ON calendar.userid=user.userid WHERE eventactive = 1 AND eventtime = '" . date("Y-m-d") . "' ORDER BY eventtime ASC");
  $r_calendar['deactive'] = mysql_query("SELECT calendar.eventid, calendar.eventtime, calendar.eventsubject, calendar.eventtext, calendar.eventactive, user.username FROM " . $pref . "calendar AS calendar LEFT JOIN " . $pref . "user AS user ON calendar.userid=user.userid WHERE eventactive = 0 AND eventtime > '" . date("Y-m-d") . "' ORDER BY eventtime ASC");
  $r_calendar['deactive_old'] = mysql_query("SELECT calendar.eventid, calendar.eventtime, calendar.eventsubject, calendar.eventtext, calendar.eventactive, user.username FROM " . $pref . "calendar AS calendar LEFT JOIN " . $pref . "user AS user ON calendar.userid=user.userid WHERE eventactive = 0 AND eventtime < '" . date("Y-m-d") . "' ORDER BY eventtime ASC");
  $r_calendar['deactive_today'] = mysql_query("SELECT calendar.eventid, calendar.eventtime, calendar.eventsubject, calendar.eventtext, calendar.eventactive, user.username FROM " . $pref . "calendar AS calendar LEFT JOIN " . $pref . "user AS user ON calendar.userid=user.userid WHERE eventactive = 0 AND eventtime = '" . date("Y-m-d") . "' ORDER BY eventtime ASC");

  print('<B>calendar Settings :</B><BR>
  <hr width="100%" noshade>
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td colspan="2" bgcolor="#999999">
        <font size="2" color="white"><b>Public Events</b></font>
        </td>
      </tr>
  </table>
  <Table>');
  while ( $calendar_active_old = mysql_fetch_array($r_calendar['active_old']) )
  {
    print("<TR><TD width=\"130\" align=right valign=\"top\"><font color=\"#FF0000\" size=1>( old )</font> " . format_db_date($calendar_active_old['eventtime']) . "</TD><TD width=\"200\"valign=\"top\"> ".strip_tags($calendar_active_old['eventsubject'])." </TD><TD>[ <A HREF=\"calendar.php?session=$session&action=edit&event=$calendar_active_old[eventid]\">Edit</A> ] [ <A HREF=\"calendar.php?session=$session&action=delete&event=$calendar_active_old[eventid]\">Delete</a> ]</TD><TD WIDTH=\"100\">$calendar_active_old[username]</TD></TR>");
  }
  while ( $calendar_active_today = mysql_fetch_array($r_calendar['active_today']) )
  {
    print("<TR><TD width=\"130\" align=right valign=\"top\"><font color=\"#000080\" size=1>( today )</font> " . format_db_date($calendar_active_today['eventtime']) . "</TD><TD width=\"200\"valign=\"top\"> ".strip_tags($calendar_active_today['eventsubject'])." </TD><TD>[ <A HREF=\"calendar.php?session=$session&action=edit&event=$calendar_active_today[eventid]\">Edit</A> ] [ <A HREF=\"calendar.php?session=$session&action=delete&event=$calendar_active_today[eventid]\">Delete</a> ]</TD><TD WIDTH=\"100\">$calendar_active_today[username]</TD></TR>");
  }
  while ( $calendar_active = mysql_fetch_array($r_calendar['active']) )
  {
    print("<TR><TD width=\"130\" align=right valign=\"top\">" . format_db_date($calendar_active['eventtime']) . "</TD><TD width=\"200\"valign=\"top\"> ".strip_tags($calendar_active['eventsubject'])." </TD><TD>[ <A HREF=\"calendar.php?session=$session&action=edit&event=$calendar_active[eventid]\">Edit</A> ] [ <A HREF=\"calendar.php?session=$session&action=delete&event=$calendar_active[eventid]\">Delete</a> ]</TD><TD WIDTH=\"100\">$calendar_active[username]</TD></TR>");
  }
  print('
  </TABLE>
  <BR>
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td colspan="2" bgcolor="#999999">
      <font size="2" color="white"><b>Deactivated Events</b></font>
      </td>
    </tr>
  </table>
  <Table>
  ');
  while ( $calendar_deactive_old = mysql_fetch_array($r_calendar['deactive_old']) )
  {
    print("<TR><TD width=\"130\" align=right valign=\"top\"><font color=\"#FF0000\" size=1>( old )</font> " . format_db_date($calendar_deactive_old['eventtime']) . "</TD><TD width=\"200\"valign=\"top\"> ".strip_tags($calendar_deactive_old['eventsubject'])." </TD><TD>[ <A HREF=\"calendar.php?session=$session&action=edit&event=$calendar_deactive_old[eventid]\">Edit</A> ] [ <A HREF=\"calendar.php?session=$session&action=delete&event=$calendar_deactive_old[eventid]\">Delete</a> ]</TD><TD WIDTH=\"100\">$calendar_deactive_old[username]</TD></TR>");
  }
  while ( $calendar_deactive_today = mysql_fetch_array($r_calendar['deactive_today']) )
  {
    print("<TR><TD width=\"130\" align=right valign=\"top\"><font color=\"#000080\" size=1>( today )</font> " . format_db_date($calendar_deactive_today['eventtime']) . "</TD><TD width=\"200\"valign=\"top\"> ".strip_tags($calendar_deactive_today['eventsubject'])." </TD><TD>[ <A HREF=\"calendar.php?session=$session&action=edit&event=$calendar_deactive_today[eventid]\">Edit</A> ] [ <A HREF=\"calendar.php?session=$session&action=delete&event=$calendar_deactive_today[eventid]\">Delete</a> ]</TD><TD WIDTH=\"100\">$calendar_deactive_today[username]</TD></TR>");
  }
  while ( $calendar_deactive = mysql_fetch_array($r_calendar['deactive']) )
  {
    print("<TR><TD width=\"130\" align=right valign=\"top\">" . format_db_date($calendar_deactive['eventtime']) . "</TD><TD width=\"200\"valign=\"top\"> ".strip_tags($calendar_deactive['eventsubject'])." </TD><TD>[ <A HREF=\"calendar.php?session=$session&action=edit&event=$calendar_deactive[eventid]\">Edit</A> ] [ <A HREF=\"calendar.php?session=$session&action=delete&event=$calendar_deactive[eventid]\">Delete</a> ]</TD><TD WIDTH=\"100\">$calendar_deactive[username]</TD></TR>");
  }
  print('
  </TABLE><BR>
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td colspan="2" bgcolor="#999999">
          <font size="2" color="white"><b>New Event</b></font>
        </td>
      </tr>
  </table>
  <form action="calendar.php?session=' . $session . '&action=add" method="post">
  <Table border=0 cellspacing=0>
  <TR><TD><B>Date :</B></TD></TR>
  <TR><TD><input class="tbinput" type="Text" name="newdate[day]" size="3" maxlength="2">.<input class="tbinput" type="text" name="newdate[month]" size="3" maxlength="2">.<input class="tbinput" type="text" name="newdate[year]" size="5" maxlength="4"></TD></TR>
  <TR><TD><B>Subject :</B></TD></TR>
  <TR><TD><input class="tbinput" type="text" name="newsubject" size="30"></TD></TR>
  <TR><TD><B>Text :</B></TD></TR>
  <TR><TD><textarea class="tbinput" name="newtext" cols=30 rows=10></textarea></TR></TD>
  <TR><TD><B>Status :</B></TD></TR>
  <TR><TD><input type=radio name=newstatus value="1"> - Active <BR><input type=radio name=newstatus value="0"> - Deactivated</TD></TR>
  <TR><td colspan="2" bgcolor="#999999" align="center"><font size="2" color="white"><input type=submit value="Save Event"> </font></td></TR>
  </TABLE>
  </form>
  ');
}

tb_footer();
?>


















