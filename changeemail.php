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

$navpath .= 'Email Adresse &auml;ndern';

if( $g_user['userid'] == 0 )
{
    message('Fehler', 'Bitte erst einloggen.');
}
else
{
    $user = ((isset($HTTP_POST_VARS['user'])) ? $HTTP_POST_VARS['user'] : '');

    if( !isset($change) || !$change )
    {
        $TFrame = new Template('./templates/'.$style['styletemplate'].'/frame.html');
        $TChangeemail = new Template('./templates/'.$style['styletemplate'].'/changeemail.html');

        $t_changewarning = '';

        if( $config['use_email'] )
        {
            $TChangeemail_warning = new Template('./templates/'.$style['styletemplate'].'/changeemail_warning.html');
            eval($TChangeemail_warning->GetTemplate('t_changewarning'));
        }

        eval($TChangeemail->GetTemplate("CONTENT"));
        eval($TFrame->GetTemplate());
    }
    else
    {
        $r_user = thwb_query("SELECT userpassword, username FROM ".$pref."user WHERE userid='$g_user[userid]'");
        $dbuser = mysql_fetch_array($r_user);

        if( !$user['useroldpassword'] || $dbuser['userpassword'] != md5($user['useroldpassword']) )
        {
            message('Fehler', 'Das Passwort ist leider nicht korrekt.');
        }
        else if( !check_email($user['usernewemail']) )
        {
            message('Fehler', 'Sie haben keine g&uuml;ltige E-Mailadresse angegeben!<br>Eine g&uuml;ltige E-Mailadresse hat das Format <b>name@example.com</b> .');
        }
        else
        {
            if( $config['use_email'] )
            {
                $email = '';

                $TRegistermail = new Template("./templates/mail/change_mail.mail");
                eval($TRegistermail->GetTemplate("email"));

                @mail($user['usernewemail'], $config['board_name'] . " - Email-Adressenaenderung", $email, "From: $config[board_admin]");
                
                message("&Auml;nderung erfolgreich!",
                        "Sie erhalten in K&uuml;rze eine Email mit einem Best&auml;tigungslink zur &Auml;nderung ihrer Email-Adresse.");
            }
            else
            {
                thwb_query("UPDATE ".$pref."user SET useremail='$user[usernewemail]'
                    WHERE userid=$g_user[userid]");
                
                message('Hinweis',
                    'Ihre Email-Adresse wurde erfolgreich ge&auml;ndert.');
            }
        }
    }
}
