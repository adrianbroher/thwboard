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

function getusercount()
{
	global $pref;
	$r_user = thwb_query("SELECT count(userid) AS usercount FROM ".$pref."user");
	$user = mysql_fetch_array($r_user);
	
	return $user['usercount'];
}

function getactiveusers()
{						
	global $pref;
	$r_user = thwb_query("SELECT count(userid) AS usercount FROM ".$pref."user WHERE userlastpost > " . (time() - 60 * 60 * 24 * 31));
	$user = mysql_fetch_array($r_user);
	
	return $user['usercount'];
}

$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$Tindex = new Template("templates/" . $style['styletemplate'] . "/main.html");
$Tcategory = new Template("templates/" . $style['styletemplate'] . "/categoryrow.html");
$Tboard = new Template("templates/" . $style['styletemplate'] . "/forumrow.html");

$lastthreads = '';
if( $config['uselastthreads'] )
{
	$Tlastthreads = new Template('templates/'.$style['styletemplate'].'/lastthreads.html');
	eval($Tlastthreads->GetTemplate('lastthreads'));
}

if( isset($category['categoryid']) && $category['categoryid'] )
	$onlycat = ", (categoryid = '".intval($category['categoryid'])."') AS display";
else
	$onlycat = '';

// category _
$r_category = thwb_query("SELECT categoryid, categoryname"."$onlycat FROM
	".$pref."category ORDER BY categoryorder ASC");


$a_lastvisited = array();
// lastvisited
if( $g_user['userid'] )
{
	$r_lastvisited = thwb_query("SELECT boardid, lastvisitedtime FROM ".$pref."lastvisited WHERE userid=$g_user[userid]");
	while( $lastvisited = mysql_fetch_array($r_lastvisited) )
	{
		$a_lastvisited[$lastvisited['boardid']] = $lastvisited['lastvisitedtime'];
	}
}

// precache boards
$a_board = array();
$r_board = thwb_query("SELECT b.boardid, b.boardname, b.boardthreads, b.boardposts, 
	b.boardlastpost, b.boarddescription, b.boardlastpostby, b.boardthreadtopic,
	b.boardthreadid, b.categoryid
	FROM ".$pref."board AS b
	WHERE boarddisabled = 0 
	ORDER BY b.boardorder ASC");

while( $board = mysql_fetch_array($r_board) )
{
	$P->set_boardid($board['boardid']);
	if( $P->has_permission( P_VIEW ) /*has_access($board['boardid'], &$a_groupboard)*/ )
	{
		if( $board['boardlastpost'] == 0 )
		{
			$board['modified'] = "(Noch kein Post)";
		}
		else
		{
			$board['modified'] = form_date($board['boardlastpost']) . "<br><a href=\"".build_link("showtopic.php?threadid=$board[boardthreadid]".((!empty($lastvisited)) ? "&amp;time=$lastvisited" : '')."&amp;pagenum=lastpage#bottom")."\" title=\"".str_replace('"', '&quot;', $board['boardthreadtopic'])."\">" . parse_code(chopstring($board['boardthreadtopic'], 30)) . '</a> von ' . parse_code($board['boardlastpostby']);
		}
	}
	else
	{
		if( $config['showprivateboards'] )
		{
			$board['modified'] = '<font color="' . $style['color_err'] . '">N/A</font>';
			$board['boardthreads'] = '<font color="' . $style['color_err'] . '">N/A</font>';
			$board['boardposts'] = '<font color="' . $style['color_err'] . '">N/A</font>';

			$board['boarddescription'] = '<font color="' . $style['color_err'] . '">[Kein Zugriff] </font>' . $board['boarddescription'];
		}
		else
		{
			continue;
		}
	}
	$a_board[$board['categoryid']][] = $board;
}
mysql_free_result($r_board);

$INDEXROWS = '';

// print boards
while( $category = mysql_fetch_array($r_category) )
{
	if( isset($a_board[$category['categoryid']]) )
	{
	  eval($Tcategory->GetTemplate("INDEXROWS"));

	  if(!empty($onlycat))
	    {
	      if(!$category['display'])
		{
		  continue;
		}

	      $navpath .= $category['categoryname']." &raquo; ";
	    }
		while( list(, $board) = each($a_board[$category['categoryid']]) )
		{
			if( isset($a_lastvisited[$board['boardid']]) && $board['boardlastpost'] > $a_lastvisited[$board['boardid']] && $a_lastvisited[$board['boardid']] != 0 )
			{
				$imagepath = './templates/'.$style['styletemplate'].'/images/board_new.png';
			}
			else
			{
				$imagepath = './templates/'.$style['styletemplate'].'/images/board.png';
			}
			eval($Tboard->GetTemplate("INDEXROWS"));
		}
	}
}

$stats = "Das Forum hat <b>" . getusercount() . "</b> registrierte Benutzer, davon sind <b>" . getactiveusers() . "</b> aktiv.";

$r_post = thwb_query("SELECT count(postid) AS postcount FROM ".$pref."post");
$posts = mysql_result($r_post, 0);

$r_thread = thwb_query("SELECT count(threadid) AS threadcount FROM ".$pref."thread");
$threads = mysql_result($r_thread, 0);

$stats .= '<br><b>' . $threads .'</b> Threads | <b>' . $posts . '</b> Posts';

$newthreads = '<a href="'.build_link("search.php?startsearch=1&amp;searchfor=today").'">Aktive Themen von Heute anzeigen</a> || ';
$servertime = 'Serverzeit: ' . form_date(time(), 0) . '.';

$r_pm = thwb_query("SELECT count(pmid) AS pmcount FROM ".$pref."pm WHERE pmtoid=$g_user[userid] AND pmflags=1");
$r_pms = thwb_query("SELECT COUNT(pmid) AS pmcount FROM ${pref}pm WHERE pmtoid=$g_user[userid]");
$pms = mysql_result($r_pm, 0);
$allpms = mysql_result($r_pms, 0);

$pmquotawarning = '';
$javascript = '';

if( !isset($g_user['usernoding']) )
	$g_user['usernoding'] = '0';

if(!$P->has_permission(P_NOPMLIMIT) && ($config['max_privmsg'] - intval($config['max_privmsg'] / 20)) <= $allpms)
{
    $pmquotawarning = 'Sie haben mehr als 95% ihres Speicherplatzes f&uuml;r Nachrichten belegt.';
}

if( $pms == 1 )
{
	$privmsgs = "<b>Sie haben eine neue <a href=\"".build_link("pm.php")."\">Nachricht</a>!".
        ((!empty($pmquotawarning)) ? ' '.$pmquotawarning : '')."</b>";
	if( $g_user['usernoding'] != 1 )
	{
		$javascript = "<script type=\"text/javascript\">alert(\"Sie haben eine neue Nachricht!".
        ((!empty($pmquotawarning)) ? '\n'.$pmquotawarning : '')."\")</script>";
    }
}
elseif( $pms > 1 )
{
	$privmsgs = "<b>Sie haben $pms neue <a href=\"".build_link("pm.php")."\">Nachrichten</a>!".
        ((!empty($pmquotawarning)) ? ' '.$pmquotawarning : '')."</b>";
	
    if( $g_user['usernoding'] != 1 )
	{
		$javascript = "<script type=\"text/javascript\">alert(\"Sie haben $pms neue Nachrichten!".
        ((!empty($pmquotawarning)) ? '\n'.$pmquotawarning : '')."\")</script>";
    }
}
else
{
	$privmsgs = "Sie haben keine neuen Nachrichten.".((!empty($pmquotawarning)) ? ' <b>'.$pmquotawarning.'</b>' : '');
	$javascript = ((!$g_user['usernoding'] && (!empty($pmquotawarning))) ? 
                   "<script type=\"text/javascript\">alert(\"".$pmquotawarning."\")</script>" : '');
}

$javascript = str_replace('&uuml;', 'ü', $javascript);

// online
$r_online = thwb_query("SELECT 	DISTINCT 
		online.onlineip,
		online.userid,
		user.username,
		user.userinvisible
	FROM
		".$pref."online AS online
	LEFT JOIN
		".$pref."user AS user
			ON online.userid=user.userid
	WHERE
		online.onlinetime > " . (time() - 300) . "");
/* vars
onlinecount: tatsaechliche anzahl der user die online sind (gaeste + user)
a_doubleuser: array mit userids die online sind
registered: registrierte user in array
guests: anzahl der gaeste/invisibles
useronline: html code das in das template kommt
*/
$onlinecount = 0;
$guests = 0;
$a_doubleuser = array();
$registered = array();

while( $online = mysql_fetch_array($r_online) )
{
	if( $online['username'] )
	{
		if( !in_array($online['userid'], $a_doubleuser) )
		{
			$a_doubleuser[] = $online['userid'];
			$onlinecount++;
			if( $online['userinvisible'] && !$P->has_permission( P_CANSEEINVIS ) )
			{
				$guests++;
			}
			else
			{
			    $registered[] = '<a href="'.build_link('v_profile.php?user[userid]='.$online['userid']).'">'.
				parse_code($online['username']).'</a>'.($online['userinvisible'] ? ' (Unsichtbar)' : '');
			}
		}
	}
	else
	{
		$onlinecount++;
		$guests++;
	}
}

$maxusers = explode('|', $config['max_useronline']);
if( $onlinecount > $maxusers[0] )
{
	thwb_query("UPDATE $pref"."registry SET keyvalue='".$onlinecount.'|'.time()."' WHERE keyname='max_useronline'");
	$maxusers[0] = $onlinecount;
	$maxusers[1] = time();
}

$useronline = 'Online Rekord: <b>'.$maxusers[0].'</b> Benutzer, und zwar am '.form_date($maxusers[1], 0).'.<br>';

if( $onlinecount == 1 )
	$useronline .= 'Es ist zur Zeit ein Benutzer online:<br>';
else
	$useronline .= 'Es sind zur Zeit '.$onlinecount.' Benutzer online:<br>';


$useronline .= @implode(', ', $registered);
if( $guests > 0 )
{
	if( $guests > 1 )
	{
		if( count($registered) > 0 )
			$useronline .= " sowie $guests G&auml;ste";
		else
			$useronline .= "$guests G&auml;ste";
	}
	else
	{
		if( count($registered) > 0 )
			$useronline .= " sowie ein Gast";
		else
			$useronline .= "Ein Gast";
	}
}


if(!empty($onlycat))
{
  $navpath .= "Kategorie&uuml;bersicht";
}
else
{
  $navpath .= 'Forum&uuml;bersicht';
}

eval($Tindex->GetTemplate("CONTENT"));
eval($Tframe->GetTemplate());

?>
