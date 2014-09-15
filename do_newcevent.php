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

if( !$P->has_permission( P_CEVENT ) )
	message( 'Fehlende Berechtigung', 'Fehler: Sie haben nicht die ben&ouml;tigte Berechtigung, um diese Seite zu benützen.' );

$a_errmsg = array();
if( !strlen(trim($event['subject'])) )
	$a_errmsg[] = 'Sie haben kein Subject angegeben.';

if( !strlen(trim($event['text'])) )
	$a_errmsg[] = 'Sie haben keinen Text definiert.';

if( !checkdate($event['month'], $event['day'], $event['year']) )
	$a_errmsg[] = 'Sie haben ein ungültiges Datum angegeben.';

if( array_count_values($a_errmsg) )
	message( 'Fehler bei der Eingabe', $style['stdfont'].'Folgende Fehler sind bei der Eingabe aufgetreten:<br>'.implode( $a_errmsg, '<br>' ).'<br>Gehen Sie mit dem Zur&uuml;ck-Button Ihres Browsers zur vorherigen Seite, um die Angaben zu korrigieren.'.$style['stdfontend'] );

thwb_query("INSERT INTO " . $pref . "calendar (eventtime,eventsubject,eventtext,eventactive,userid) VALUES ('$event[year]-$event[month]-$event[day]','".addslashes($event['subject'])."','".addslashes($event['text'])."','1',$g_user[userid]);");

message_redirect("Eintrag erfolgreich", "calendar.php?month=$event[month]&amp;year=$event[year]");
