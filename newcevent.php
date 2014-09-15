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

if( !$P->has_permission( P_CEVENT ) )
    message('Fehlende Berechtigung', 'Fehler: Sie haben nicht die ben&ouml;tigte Berechtigung, um diese Seite zu ben&uuml;tzen.');

$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$Tnewevent = new Template("templates/" . $style['styletemplate'] . "/newcalendarentry.html");

$navpath .= 'Neuer Kalendereintrag';

$event['day'] = ((isset($day)) ? $day : '');
$event['month'] = ((isset($month)) ? $month : '');
$event['year'] = ((isset($year)) ? $year : '');

if(!isset($event['subject']))
{
  $event['subject'] = '';
}

if(!isset($event['text']))
{
  $event['text'] = '';
}

eval($Tnewevent->GetTemplate('CONTENT'));
eval($Tframe->GetTemplate());
