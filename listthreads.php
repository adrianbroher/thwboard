<?php
/* $Id: listthreads.php 87 2004-11-07 00:19:15Z td $ */
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

$navpath .= 'Abonnierte Themen';

if( $g_user['userid'] == 0 )
{
    message("Nur f&uuml;r Mitglieder", "Diese Funktion ist nur f&uuml;r Mitglieder. Sie k&ouml;nnen sich <a href=\"register.php\">hier</a> kostenlos registrieren.");
}

if(!$config['use_email'])
{
    message('Funktion nicht verf&uuml;gbar', 'Diese Funktion wurde vom Administrator deaktiviert.');
}

$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$TTopics = new Template("templates/" . $style['styletemplate'] . "/markedlist.html");
$TTopicrow = new Template("templates/" . $style['styletemplate'] . "/markedrow.html");

if(!empty($do_delthreads) && ($do_delthreads))
{
  if(empty($delthreads) || !count($delthreads))
    {
      message('Fehler', 'Sie m&uuml;ssen ein Thema ausw&auml;hlen.');
    }

  thwb_query("UPDATE ".$pref."post SET postemailnotify='0' WHERE userid='".$g_user['userid']."' AND threadid IN (".join(',', $delthreads).")");

  message('Themen abbestellt', 'Die markierten Themen wurden abbestellt.');
}


$r_usermarkedthreads = thwb_query("SELECT DISTINCT threadid FROM ".$pref."post WHERE postemailnotify = '1' AND userid = '".$g_user['userid']."' GROUP BY threadid");

$i = 0;
$TOPICROWS = '';

if( !mysql_num_rows($r_usermarkedthreads) )
{
	$TTopicrow = new Template('./templates/'.$style['styletemplate'].'/board_nothreads.html');
	eval($TTopicrow->GetTemplate("TOPICROWS"));
}
else
{
  while ($a_thread = mysql_fetch_assoc($r_usermarkedthreads)) 
    {
		$i % 2 > 0 ? $thisrowbg = $style['CellB'] : $thisrowbg = $style['CellA'];
		$i++;
		$r_thread = mysql_query("SELECT threadid, threadauthor, threadtopic, threadviews, threadreplies, threadtime, boardid, threadlastreplyby FROM " . $pref . "thread WHERE threadid = '" . $a_thread['threadid'] . "'");
		if ( mysql_num_rows( $r_thread ) != 0 )
		{
			$thread = mysql_fetch_array( $r_thread );
			$r_board = mysql_query("SELECT boardname FROM " . $pref . "board WHERE boardid = '" . $thread['boardid'] . "'");
			$board = mysql_fetch_array( $r_board );
			
			$thread['threadtopic'] .= "<BR><span style=\"color:" . $style['color1'] . "\">". $style['smallfont'] . "Forum: " . $board['boardname'] . $style['smallfontend'] . "</span>";
			
			$thread['threadtime'] = form_date($thread['threadtime']);		
			eval($TTopicrow->GetTemplate("TOPICROWS"));
		}
		else
		{
			$usermarkedthreads = str_replace( ";" . $threadid . ";", ";", $g_user['usermarkedthreads'] );
			if ( strlen( $usermarkedthreads ) == 1 )
			{
				$usermarkedthreads = "";
			}
			mysql_query("UPDATE " . $pref . "user SET usermarkedthreads = '" . $usermarkedthreads . "' WHERE userid = '" . $g_user['userid'] . "'");
		}
	}
}

eval($TTopics->GetTemplate("CONTENT"));
eval($Tframe->GetTemplate());
?>