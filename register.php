<?php
/* $Id: register.php 87 2004-11-07 00:19:15Z td $ */
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

if( !$config['allowregister'] )
{
	$navpath .= "User Registrierung &raquo; Fehler";
	message('Registrierung nicht m&ouml;lich!', 'Eine Registrierung ist derzeit leider nicht m&ouml;lich.<br>Bitte versuchen Sie es zu einem sp&auml;teren Zeitpunkt erneut.');
}

if($g_user['userid'])
{
    $navpath .= 'User Registrierung';

    message('Fehler', 'Sie sind bereits registriert.<br>Eine weitere Registrierung ist deshalb nicht m&ouml;glich.');
}

if(is_flooding(FLOOD_REGISTER))
{
    message('Fehler', 'IP wegen '.$config['flood_login_count'].' Registrierungen f&uuml;r '.$config['flood_login_timeout'].' Minuten gesperrt.');
}

if( !isset($accept) || !$accept )
{
  $rules = '';
  $TRules = new Template('templates/'.$style['styletemplate'].'/forumrules.html');
  eval($TRules->GetTemplate("rules"));
	$navpath .= "User Registrierung";
	message("Forumregeln", $rules, 0, 0);
}
else
{
	$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
	$Tregform = new Template("templates/" . $style['styletemplate'] . "/register.html");

	$passwordfield = '';
	
	$navpath .= "User Registrierung &raquo; Dateneingabe";

	$TPasswordfield = new Template('./templates/'.$style['styletemplate'].'/register_pwdfield.html');
	eval($TPasswordfield->GetTemplate('passwordfield'));

	eval($Tregform->GetTemplate("CONTENT"));
	eval($Tframe->GetTemplate());
}
?>