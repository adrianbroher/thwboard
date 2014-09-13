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

if(!isset($action))
{
  die("denied.");
}

function log_action($action)
{
	global $g_user, $post, $REMOTE_ADDR, $PHP_SELF, $pref;
	thwb_query( "INSERT INTO ".$pref."adminlog (logtype, logtime, loguser, logip, logscript,
	logaction, lognotes) VALUES ('LOG_MOD',
	".time().",
	'$g_user[username]',
	'$REMOTE_ADDR',
	'".basename($PHP_SELF)."',
	'".addslashes($action)."',
	'post id: $post[postid]')" );
}


if( $action == "showip" )
{
	$navpath .= "IP anzeigen";
	if( $P->has_permission( P_IP ) )
	{
		$r_post = thwb_query("SELECT postid, postip FROM ".$pref."post WHERE postid=". $post['postid'] );
		$post = mysql_fetch_array($r_post);

		log_action('reveal ip');
		message("IP", "Dieser Post wurde von IP $post[postip] (".gethostbyaddr($post['postip']).") aus erstellt.");
	}
	else
	{
		message("Fehler", "Sie haben keine Erlaubnis diese IP einzusehen.");
	}
}
elseif( $action == "delete" )
{
	$navpath .= "Post/Thread l&ouml;schen";
	$r_post = thwb_query("SELECT postid, userid, posttime FROM ".$pref."post WHERE postid=". $post['postid'] );
	$post = mysql_fetch_array($r_post);
	
	if( ($g_user['userid'] == $post['userid'] && $P->has_permission( P_DELPOST )) || $P->has_permission( P_ODELPOST )) 
	{
		//ttt: edit time limit now also applies to post delete
		if( $config['editlimit'] && !$P->has_permission( P_ODELPOST ) && !$P->has_permission( P_NOEDITLIMIT ) && ($post['posttime'] + $config['editlimit']) < time() )
			message('Fehler', 'Sie k&ouml;nnen diesen Post nicht mehr l&ouml;schen, da das Zeitlimit &uuml;berschritten wurde.');
		
		if( !isset($do_delete) || !$do_delete )
		{
			$r_thread = thwb_query("SELECT threadid, threadreplies FROM ".$pref."thread WHERE threadid=$thread[threadid]");
			$thread = mysql_fetch_array($r_thread);
	
			if( $thread['threadreplies'] < 1 )
			{
				message("&nbsp;", 'Soll dieser Thread wirklich GEL&Ouml;SCHT werden?<br>
<form name="theform" method="post" action="'.build_link("threadops.php").'">
  <input type="hidden" name="action" value="do_delete">
  <input type="hidden" name="thread[threadid]" value="' . $thread['threadid'] . '">
  <input class="tbbutton" type="submit" name="Submit" value="L&ouml;schen &gt;&gt;">
</form>');
			}
			else
			{
				message("Best&auml;tigung", '
<form name="theform" method="post" action="'.build_link("postops.php").'">
  M&ouml;chten Sie diesen Post wirklich l&ouml;schen?<br><br>
  <input type="hidden" name="do_delete" value="1">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="post[postid]" value="' . $post['postid'] . '">
  <input class="tbbutton" type="submit" name="Submit" value="L&ouml;schen &gt;&gt;">
</form>');
			}
		}
		else
		{
			// re-get $post
			$post = $HTTP_POST_VARS['post'];
			
			if( !($post['postid'] = intval($post['postid'])) )
				exit( 'nix da' );
			
			// decrease thread reply count
			thwb_query("UPDATE ".$pref."thread SET threadreplies=threadreplies-1 WHERE threadid=$thread[threadid]");
		
			// decrease board post count
			thwb_query("UPDATE ".$pref."board SET boardposts=boardposts-1 WHERE boardid=$board[boardid]");
		
			// remove post
			thwb_query("DELETE FROM ".$pref."post WHERE postid=$post[postid]");

			// display stuff
			updatethread($thread['threadid']);
			updateboard($board['boardid']);

			log_action('delete post');
			message("Post wurde gel&ouml;scht",
				"Post wurde gel&ouml;scht.<br><a href=\"".build_link("showtopic.php?thread[threadid]=$thread[threadid]")."\">Zur&uuml;ck zum Thread</a>");
		}
	}
	else
	{
		message("Fehler", "Sie haben keine Erlaubnis diesen Post zu l&ouml;schen");
	}
}
?>
