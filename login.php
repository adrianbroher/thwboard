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

if($g_user['userid'])
{
  message('Fehler', 'Du bist schon eingeloggt.');
}

if(empty($source))
{
  $source = 'index.php';
}

if(is_flooding(FLOOD_LOGIN))
{
    message('Fehler', 'IP wegen '.$config['flood_login_count'].' fehlerhafter Loginversuche f&uuml;r '.$config['flood_login_timeout'].' Minuten gesperrt.');
}

$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$Tloginform = new Template("templates/" . $style['styletemplate'] . "/login.html");

$navpath .= 'Einloggen';
$source = urlencode($source);

eval($Tloginform->GetTemplate("CONTENT"));
eval($Tframe->GetTemplate());
?>
