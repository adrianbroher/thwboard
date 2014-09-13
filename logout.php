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
require('./inc/header.inc.php');

if($g_user['userid'] && (empty($uid) || ($uid != $g_user['userid'])))
{
    message('Fehler', 'Die User-ID ist ung&uuml;ltig.');
}


setcookie("thwb_cookie");
setcookie("thwb_session");

unset($s);

$g_user['issession'] = false;
$g_user['have_cookie'] = false;

!empty($g_user['userid']) && thwb_query("DELETE FROM $pref"."online WHERE userid='$g_user[userid]'");

header("Location: index.php");
//message_redirect('Sie wurden erfolgreich ausgeloggt, bitte warten ...', 'index.php');
?>
