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

$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$Tcalframe = new Template("templates/" . $style['styletemplate'] . "/calendarframe.html");
$Tcalrow = new Template("templates/" . $style['styletemplate'] . "/calendarow.html");
$Tcalrow_empty = new Template("templates/" . $style['styletemplate'] . "/calendarow_empty.html");
$Tcaleventbox= new Template("templates/" . $style['styletemplate'] . "/calendareventbox.html");

$navpath .= 'Kalender';

function make_date( $date )
{
	return substr( $date, 8, 2 ) . "." . substr( $date, 5, 2 ) . "." . substr( $date, 0, 4 );
}

$a_current = array();
$a_current['date'] = date( "d.m.Y" );
$a_current['day'] = intval( date( "j" ) );
$a_current['month'] = intval( date( "m" ) );
$a_current['year'] = intval( date( "Y" ) );

$a_monthnames = array(
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
$a_daynames = array(
	1 => 'Montag',
	2 => 'Dienstag',
	3 => 'Mittwoch',
	4 => 'Donnerstag',
	5 => 'Freitag',
	6 => 'Samstag',
	7 => 'Sonntag'
);

if( empty( $month ) )
	$month = $a_current['month'];
$month = intval( $month );

if( empty( $year ) )
	$year = $a_current['year'];
$year = intval( $year );

$month_name = $a_monthnames[ intval( $month ) ];

if( $P->has_permission( P_CEVENT ) )
{
	$calendaradmin=' - <a href="'.build_link('newcevent.php?month=' . $month . '&amp;year=' . $year) . '">Neuer Eintrag</a>';
}
else
{
	$calendaradmin='';
}

// Creating Year Form
$gotoyearform = '&nbsp;<select name="year" class="tbselect">';
for( $i = $a_current['year'] - 25; $i <= $a_current['year'] + 25; $i++ )
{
	$gotoyearform .= '<option value="'. $i . '"' . ( $i == $year ? ' selected' : '' ) . '>'. $i . '</option>';
}
$gotoyearform .= '</select>';

// Creating Month Form
$gotomonthform = '<select name="month" class="tbselect">';
for( $i = 1; $i <= 12; $i++ )
{
	$gotomonthform .= '<option value="' . $i . '"' . ( $i == $month ? ' selected' : '' ) . '>' . $a_monthnames[$i] . '</option>';
}
$gotomonthform .= '</select>';

$firstday = intval( date( 'w', mktime( 0, 0, 0, $month, 1, $year ) ) );
if( $firstday == 0 )
	$firstday = 7;

// Find last day of month
$lastday = 28;
while( checkdate( $month, $lastday, $year ) )
{
	$lastday++;
}
$lastday--;

$nextmonth = $month+1;
if ($nextmonth == 13)
{
	$nextmonth = 1;
	$nextyear = $year + 1;
}
else
{
	$nextyear = $year;
}

$lastmonth = $month-1;
if ($lastmonth == 0)
{
	$lastmonth = 12;
	$lastyear = $year - 1;
}
else
{
	$lastyear = $year;
}

// Fetching all user bdays...
$a_birthdays = array();
$r_user = thwb_query( "SELECT username, userid, userbday FROM " . $pref . "user
	WHERE SUBSTRING(userbday,6,2)=LPAD('$month',2,'0') AND userbday<>'00-00-0000'
	ORDER BY userbday, username" );
while( $user = mysql_fetch_array( $r_user ) )
{
	$bday_year = $year - substr($user['userbday'], 0, 4);
	if( $bday_year > 0 )
		$a_birthdays[ intval( substr( $user['userbday'], 8, 2 ) ) - 1 ][] = "<a href=\"".build_link("v_profile.php?userid=$user[userid]")."\">$user[username]</a> ($bday_year)";
}

// Fetching all events...
$a_events = array();
$r_events = thwb_query( "SELECT * FROM " . $pref . "calendar
	WHERE eventtime>='$year-$month-01' AND eventtime<='$year-$month-$lastday'
	AND eventactive='1'
	ORDER BY eventtime, eventtext" );
while( $event = mysql_fetch_array( $r_events ) )
{
	$a_events[ intval( substr( $event['eventtime'], 8, 2 ) ) - 1 ][] = $event;
}

// Creating Eventbox
$eventbox = '';
$r_calendar = mysql_query("SELECT eventid, eventtime, eventsubject FROM " . $pref . "calendar WHERE eventtime >= '$a_current[year]-$a_current[month]-$a_current[day]' ORDER BY eventtime LIMIT 1");
if( mysql_num_rows( $r_calendar ) > 0 )
{
	$calendar = mysql_fetch_array( $r_calendar );
	$calendar['eventtime'] = make_date( $calendar['eventtime'] );
	$calendar['eventsubject'] = parse_code($calendar['eventsubject']);
	eval( $Tcaleventbox->GetTemplate( 'eventbox' ) );
}

$boxcount = $lastday + $firstday - 1;
if( $boxcount % 7 > 0 )
{
	$boxcount += 7 - ($boxcount % 7);
}
$calendar = '<tr>';
for ( $i = 1; $i <= $boxcount; $i++ )
{
	$userbday = '';
	$events = '';
	if( ($i < $firstday) || ($i >= $lastday + $firstday) )
	{
		eval( $Tcalrow_empty->GetTemplate( 'calendar' ) );
	}
	else
	{
		$thisday = ($i + 1) - $firstday;
		if( isset( $a_birthdays[$thisday - 1] ) )
		{
			$userbday = implode( $a_birthdays[$thisday - 1], ',<br>' );
			$userbday .= '<br>';
		}
		if( isset( $a_events[$thisday - 1] ) )
		{
			foreach( $a_events[$thisday - 1] AS $event )
			{
				$events .= "[ <a href=\"#\" onclick=\"window.open('".build_link("showevent.php?event=$event[eventid]")."','show_event','width=400,height=500,scrollbars=yes,menubar=no,toolbar=no,statusbar=no')\">".parse_code($event['eventsubject'])."</a> ]<br>";
			}
		}

		if(!strcmp(date( "d.m.Y" ), sprintf('%02d.%02d.%04d', $thisday, $month, $year)))
		{
			$style['specialfont'] = "<font color=\"$style[color_err]\"><i>";
			$style['specialfontend'] = "</i></font>";
		}
		else
		{
			$style['specialfont'] = "";
			$style['specialfontend'] = "";
		}
		eval( $Tcalrow->GetTemplate( 'calendar' ) );
	}
	if( $i % 7 == 0 )
	{
		$calendar .= "</tr>\n<tr>";
	}
}

eval( $Tcalframe->GetTemplate( 'CONTENT' ) );
eval( $Tframe->GetTemplate() );
?>
