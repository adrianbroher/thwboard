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

// http://www.securiteam.com/securitynews/5FP0C204KE.html
$post['posttext'] = ((isset($HTTP_POST_VARS['post']['posttext'])) ? $HTTP_POST_VARS['post']['posttext'] : '');

$post['posttext'] = strip_session($post['posttext']);

if(!isset($post['postcode']))
{
  $post['postcode'] = 0;
}

if(!isset($post['postsmilies']))
{
  $post['postsmilies'] = 0;
}

if(!isset($post['postemailnotify']))
{
  $post['postemailnotify'] = 0;
}

if( $g_user['userid'] == 0 && !$P->has_permission( P_REPLY ) )
{
	$navpath .= "Neue Antwort";
	message('Fehler', 'Sie m&uuml;ssen registriert sein, um Beitr&auml;ge erstellen zu k&ouml;nnen');
}

requires_permission( P_REPLY );

$msg = '';

if( (time() - $config['postdelay'] < ((!empty($g_user['userlastpost'])) ? $g_user['userlastpost'] : 0)) && !$P->has_permission( P_NOFLOODPROT ) )
{
	$navpath .= "Neue Antwort";
	message("Fehler", "Sie k&ouml;nnen nur alle $config[postdelay] Sekunden einen neuen Post erstellen.");
}

// ANTI Guest Spamm
if ( $g_user['userid'] == 0 && $P->has_permission( P_REPLY ) )
{
	prevent_guestspam();
}

$testlen = strlen(preg_replace("/(\s+|(&#032;)+)/", '', strip_tags(parse_code(preparse_code($post['posttext']), 1, ($config['imageslevel'] ? 0 : 1), $post['postcode'], $post['postsmilies']))));

if( $testlen  < $config['message_minlength'] )
{
	$msg .= "Der Text ist zu kurz<br>";
}
if( $testlen > $config['message_maxlength'] )
{
	$msg .= "Der Text ist zu lang<br>";
}

// andere error messages l&ouml;schen, nur diese
if( $thread['threadclosed'] == 1 )
{
	$msg = "Dieser Thread ist leider geschlossen, es k&ouml;nnen keine weiteren Antworten erstellt werden.";
}

if( isset($msg) && strlen($msg) > 0 )
{
	$navpath .= "Neue Antwort";
	message("Fehler", "Es sind leider folgende Fehler aufgetreten:<br><br><font color='$style[color_err]'>$msg</font>");
}

$ctime = time();

if( $g_user['userid'] == 0 && $P->has_permission( P_REPLY ) )
{
	// check username
	check_username($post['postguestname']);

	// override notify
	$post['postemailnotify'] = 0;

	$g_user['username'] = $config['guestprefix'] . $post['postguestname'];
	$post['postguestname'] = $config['guestprefix'] . addslashes($post['postguestname']);
}
else
{
	$post['postguestname'] = '';
}

if( $config["usebwordprot"] >= BWORD_POST )
{
	$post["posttext"] = check_banned($post["posttext"]);
}

if ( isset($config['auto_close']) && $config['auto_close'] > 0 )
{
	thwb_query("UPDATE  " . $pref . "thread SET threadclosed = '1' WHERE threadtime < '" . ( time() - ( ( $config['auto_close']+1 ) * 86400 ) ) . "'");
}

if ( isset($config['auto_delete']) && $config['auto_delete'] > 0 )
{
	thwb_query("DELETE FROM " . $pref . "thread WHERE threadtime < " . ( time() - ( $config['auto_delete'] * 86400 ) ) . "");
}


// neue nachricht posten
thwb_query("INSERT INTO ".$pref."post (posttime, posttext, userid, threadid, postemailnotify, postsmilies, postcode, postip, postguestname)
	VALUES('$ctime',
	'" . addslashes(preparse_code($post['posttext'])) . "',
	'$g_user[userid]',
	'$thread[threadid]',
	'" . ($post['postemailnotify'] ? 1 : 0) . "',
	'" . ($post['postsmilies'] ? 1 : 0) . "',
	'" . ($post['postcode'] ? 1 : 0) . "',
	'".addslashes($REMOTE_ADDR)."',
	'" . $post['postguestname'] . "')");

// Replys um 1 erh&ouml;hen in der board datenbank
thwb_query("UPDATE ".$pref."board SET
	boardlastpost='$ctime',
	boardposts=boardposts+1,
	boardlastpostby='" . addslashes($g_user['username']) . "',
	boardthreadtopic='" . addslashes($thread['threadtopic']) . "',
	boardthreadid=$thread[threadid] WHERE boardid='$board[boardid]'");

if( $g_user['userid'] )
{	
	// Den postings wert des postenden users erh&ouml;hen
	thwb_query("UPDATE ".$pref."user SET userlastpost=$ctime, userposts=userposts+1 WHERE userid='$g_user[userid]'");
}

// Replys um 1 erh&ouml;hen in der topic datenbank + time aktualisieren
thwb_query("UPDATE ".$pref."thread SET threadtime='$ctime', threadreplies=threadreplies+1,
	threadlastreplyby='" . addslashes($g_user['username']) . "' WHERE threadid='$thread[threadid]'");

// email zeug
if( $config['use_email'] )
{
	$TRegmail = new Template("./templates/mail/newreply.mail");

	$r_email = thwb_query("SELECT DISTINCT
		user.useremail as useremail, thread.threadtopic as threadtopic
	FROM
		".$pref."post as post, ".$pref."user as user, ".$pref."thread as thread
	WHERE
		thread.threadid=$thread[threadid] AND
		post.threadid=$thread[threadid] AND
		post.userid=user.userid AND
		post.postemailnotify=1 AND
		user.userid<>$g_user[userid]");
	
	while( $email = mysql_fetch_array($r_email) )
	{
		$text = '';
		eval($TRegmail->GetTemplate("text"));

		@mail($email['useremail'], $config['board_name'] . " - Neue Antwort", $text, "From: $config[board_admin]");
	}
}

header("Location: ".build_link("showtopic.php?threadid=$thread[threadid]&time=$time&pagenum=lastpage#bottom", true));

?>
