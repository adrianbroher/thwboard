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

$navpath .= "Passwort vergessen";

if( $config['use_email'] )
{
	$Tframe=new Template("templates/" . $style['styletemplate'] . "/frame.html");
	$Tsendform=new Template("templates/" . $style['styletemplate'] . "/send_password.html");

	eval($Tsendform->GetTemplate("CONTENT"));
	eval($Tframe->GetTemplate());
}
else
{
	message('Sorry',
		'Der Forumadministrator hat diese Funktion leider deaktiviert. Wenn Sie Ihr Passwort vergessen haben, wenden
		Sie sich bitte an den <a href="mailto:' . $config['board_admin'] . '">Forumadministrator</a>.');
}

?>
