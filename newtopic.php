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
$post['posttext'] = ((isset($HTTP_POST_VARS['post'])) ? $HTTP_POST_VARS['post']['posttext'] : '');

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

if( $g_user['userid'] == 0 && !$P->has_permission( P_POSTNEW ) )
{
	message("Hinweis", "Sie m&uuml;ssen registriert sein, um Beitr&auml;ge erstellen zu k&ouml;nnen");
}

requires_permission( P_POSTNEW );

if( !isset($Submit) )
{
	$newtopicicons = '';
	$j = 1;
	while( list($iconid, $img) = each($topicicon) )
	{
		$newtopicicons .= "<INPUT type=\"radio\" name=\"thread[threadiconid]\" value=\"$iconid\" >
			<img src=\"templates/".$style['styletemplate']."/images/icon/".$img."_new.png\">&nbsp;&nbsp;&nbsp;";
		if( $j % 6 == 0 )
		{
			$newtopicicons .= "<br>";
		}
	
		$j++;
	}
	
	if( $config['smilies'] )
	{
		$smilies_on_off = "AN";
	}
	else
	{
		$smilies_on_off = "AUS";
	}

	if( $config['use_email'] )
	{
		$notifyavailable = '';
	}
	else
	{
		$notifyavailable = ' (Derzeit nicht verf&uuml;gbar)';
	}
//               <input class="tbinput" type="text" name="thread[postguestname]">
	if( $g_user['userid'] )
	{
		$replyusername = "$style[stdfont]$g_user[userhtmlname]$style[stdfontend]$style[smallfont] [ <a href=\"".build_link('logout.php?uid='.$g_user['userid'])."\">Logout</a> ]$style[smallfontend]";
	}
	else
	{
		if( $g_user['userid'] == 0 && $P->has_permission( P_POSTNEW ) )
		{
			$replyusername = '<input class="tbinput" name="post[postguestname]" type="text">' . $style['smallfont'] .
			' (Minimal ' . $config['min_usernamelength'] . ', maximal ' . $config['max_usernamelength'] .
			' Zeichen, keine Sonderzeichen) <b>Das Forum speichert ihre IP-Addresse!</b>' . $style['smallfontend'];
		}
		else
		{
			$replyusername = '';
		}
	}
	
	$Tframe= new Template("templates/" . $style['styletemplate'] . "/frame.html");
	$Tform= new Template("templates/" . $style['styletemplate'] . "/newtopic.html");

	$navpath .= 'Neues Thema erstellen';
	
	eval($Tform->GetTemplate("CONTENT"));
	eval($Tframe->GetTemplate());
}
else
{
  $msg = '';

	$post['posttext'] = strip_session($post['posttext']);

  
  // Bannedwords-Protection
  if( $config["usebwordprot"] == BWORD_TOPIC || $config["usebwordprot"] == BWORD_ALL )
    {
      $thread["threadtopic"] = check_banned($thread["threadtopic"]);
    }
  if( $config["usebwordprot"] >= BWORD_POST )
    {
		$post["posttext"] = check_banned($post["posttext"]);
    }
  if( strlen($thread['threadtopic']) > $config['subject_maxlength'] )
    {
      $msg .= "Das Subject ist zu lang!<br>";
    }
  if( strlen(preparse_code($thread['threadtopic'])) < $config['subject_minlength'] )
    {
      $msg .= "Das Subject ist zu kurz!<br>";
    }
  $testlen = strlen(preg_replace("/(\s+|(&#032;)+)/", '', strip_tags(parse_code(preparse_code($post['posttext']), 1, ($config['imageslevel'] ? 0 : 1), $post['postcode'], $post['postsmilies']))));
  if( $testlen < $config['message_minlength'] )
    {
      $msg .= "Der Text ist zu kurz<br>";
    }
  if( $testlen > $config['message_maxlength'] )
    {
      $msg .= "Der Text ist zu lang<br>";
    }
  
  if( strlen($msg) > 0 )
    {
      $navpath .= 'Neuer Thread';
      message("Fehler","Es sind leider folgende Fehler aufgetreten:<br><br><font color='$style[color_err]'>$msg</font>");
    }
  
  if( !$P->has_permission( P_NOFLOODPROT ) && time() - $config['postdelay'] < $g_user['userlastpost'] )
    {
      $navpath .= 'Neuer Thread';
      message("Fehler", "Sie k&ouml;nnen nur alle $config[postdelay] Sekunden einen neuen Thread erstellen.");
    }

  // ANTI Guest Spamm
  if( $g_user['userid'] == 0 && $P->has_permission( P_REPLY ) )
  {
	  prevent_guestspam();
  }
  
  $time = time();
  
  (isset($post['postemailnotify']) && $post['postemailnotify']) ? $post['postemailnotify'] = 1 : $post['postemailnotify'] = 0;
  
  if( !isset($thread['topiciconid']) || !$topicicon[($thread['topiciconid'])] )
    {
      $thread['topiciconid'] = 0;
    }
  
  if( $P->has_permission( P_POSTNEW ) && ($g_user['userid'] == 0) )
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
  
  if( $config['uppercase_prot'] )
    {
      $thread['threadtopic'] = killshout( $thread['threadtopic'] );
    }
  
  // Autoclose & delete
  if ( isset($config['auto_close']) && $config['auto_close'] > 0 )
    {
      thwb_query("UPDATE  " . $pref . "thread SET threadclosed = '1' WHERE threadtime < '" . ( time() - ( ( $config['auto_close'] + 1 ) * 86400 ) ) . "'");
    }
  if ( isset($config['auto_delete']) && $config['auto_delete'] > 0 )
    {
      thwb_query("DELETE FROM " . $pref . "thread WHERE threadtime < " . ( time() - ( $config['auto_delete'] * 86400 ) ) . "");
    }
  // die neue nachricht abspeichern in dem topics table
  thwb_query("INSERT INTO ".$pref."thread (threadtime, threadtopic, threadauthor, boardid,
		threadlastreplyby, threadiconid, threadcreationtime)
		VALUES('$time',
		'".addslashes(preparse_code($thread['threadtopic']))."',
		'".addslashes($g_user['username'])."',
		'$board[boardid]',
		'".addslashes($g_user['username'])."',
		'".intval($thread['threadiconid'])."',
		'$time')");
  
  $thread['threadid'] = mysql_insert_id();
  
  // die neue nachricht abspeichern in dem messages table
  thwb_query("INSERT INTO ".$pref."post (posttime, posttext, userid, threadid, postemailnotify, postip, postsmilies,
		postcode, postguestname)
		VALUES('$time',
		'" . addslashes(preparse_code($post['posttext'])) . "',
		'$g_user[userid]',
		'$thread[threadid]',
		'$post[postemailnotify]',
		'".addslashes($REMOTE_ADDR)."',
		'" . ($post['postsmilies'] ? 1 : 0) . "',
		'" . ($post['postcode'] ? 1 : 0) . "',
		'" . addslashes($post['postguestname']) . "')");

  // Den topics-count-wert des board erh&ouml;hen
  thwb_query("UPDATE ".$pref."board SET
	boardthreads=boardthreads+1,
	boardlastpost='$time',
	boardposts=boardposts+1,
	boardlastpostby='" . addslashes($g_user['username']) . "',
	boardthreadtopic='" . addslashes(preparse_code($thread['threadtopic'])) . "',
	boardthreadid=$thread[threadid]
		WHERE boardid='$board[boardid]'");
  
  if( $g_user['userid'] )
    {
      // Den postings wert des postenden users erh&ouml;hen
      thwb_query("UPDATE ".$pref."user SET userlastpost=$time, userposts=userposts+1 WHERE userid='$g_user[userid]'");
    }
  
  header("Location: ".build_link("showtopic.php?threadid=$thread[threadid]", true));
}

?>
