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

/* 11/12/2001: since passwords are encrypted we need to
 *   to send the current *passwordhash* to the email address
 *   and wait for a confirmation with this hash.
 */

$text = '';

if(empty($username) || empty($email))
{
    die('denied.');
}

if( $config['use_email'] )
{
	$r_user = thwb_query("SELECT username, userpassword, useremail FROM ".$pref."user WHERE username='".addslashes($username)."' AND useremail='".addslashes($email)."'");
	if( mysql_num_rows($r_user) != 1 )
	{
    	message("Fehler", "Es gibt leider keinen Benutzer mit diesem Namen und dieser Email!");
	}
	else
	{
		$user = mysql_fetch_array($r_user);
		if( !isset($hash) || !$hash  || empty($new_hash))
		{
		  if(!isset($HTTP_POST_VARS['Submit']))
		    {
		      die("denied");
		    }

            if($pass1 !== $pass2)
            {
                message('Fehler', 'Ihr neues Passwort und die Passwortwiederholung stimmen nicht &uuml;berein.');
            }

            $new_hash = md5sum($pass1);

			$TMail = new Template("./templates/mail/send_password_request.mail");

			$username = rawurlencode($user['username']);
			eval($TMail->GetTemplate("text"));

			@mail($email, "Passwort vergessen - $config[board_name]", $text, "From: $config[board_admin]");
			message("Best&#xE4;tigung", "Es wurde eine Mail an die angegebene Email Adresse geschickt. Bitte lesen Sie diese, um die Passwort&#xE4;nderung zu vervollst&#xE4;ndigen.");
		}
		else
		{
            if(empty($new_hash) || (strlen($new_hash) != 32))
            {
                message('Fehler', 'Ung&uuml;ltiger Hash.');
            }
            
			if( $hash != $user['userpassword'] )
			{
				message("Fehler", "Ung&#xFC;ltiger Hash.");
			}
			else
			{
                /* set new password */
                                      
				thwb_query("UPDATE ".$pref."user SET userpassword='" . $new_hash . "' WHERE username='" . addslashes($username) . "'");

				eval($TMail->GetTemplate("text"));
				@mail($email, "Passwort vergessen - $config[board_name]", $text, "From: $config[board_admin]");

                // now kill the cookies.

                setcookie("thwb_cookie");
                setcookie("thwb_session");

                unset($s);

                $g_user['issession'] = false;
                $g_user['have_cookie'] = false;

                thwb_query("DELETE FROM $pref"."online WHERE userid='$g_user[userid]'");

                // start new session

                $s = new_session();
                $g_user['have_cookie'] = false;

                setcookie("thwb_cookie", md5($register['userpassword']) . $register['userid'], (time() + 60 * 60 * 24 * 365));

				message("Passwort ge&auml;ndert", "Ihr Passwort wurde ge&auml;ndert.");
			}
		}
	}
}
else
{
	message('Sorry',
		'Der Forumadministrator hat diese Funktion leider deaktiviert. Wenn Sie Ihr Passwort vergessen haben, wenden
		Sie sich bitte an den <a href="mailto:' . $config['board_admin'] . '">Forumadministrator</a>.');
}
