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

include './inc/header.inc.php';

$TFrame = new Template('./templates/' . $style['styletemplate'] . '/frame.html');
$TTeam = new Template('./templates/' . $style['styletemplate'] . '/team.html');
$TRow = new Template('./templates/' . $style['styletemplate'] . '/teamrow.html');

// who is online?
$a_online = array();
$r_online = thwb_query ("SELECT online.userid, online.onlinetime FROM ".$pref."online AS online, ".$pref."user AS user WHERE user.userid=online.userid");
while( $online = mysql_fetch_array($r_online) )
{
	if( $online['onlinetime'] > (time() - 60 * 5) )
	{
		$a_online[$online['userid']] = 1;
	}
}
mysql_free_result($r_online);

$a_group = array();
$group_ids = '';
$r_group = thwb_query("SELECT
		groupid,
		title
	FROM
		$pref"."group
        WHERE
                SUBSTRING(accessmask, ".(P_INTEAM + 1).", 1)
	ORDER BY
		titlepriority DESC"
);
if( mysql_num_rows($r_group) < 1 )
	message('Info', 'Kein Staff vorhanden.');

while( $group = mysql_fetch_array($r_group) )
{
	$a_group[] = $group;
	$group_ids .= "OR INSTR(groupids, ',$group[groupid],')>0 ";
}
$group_ids = substr($group_ids, 3);

$TEAMROWS = '';

$r_user = thwb_query("SELECT userid, username, useremail, userhideemail, usertitle, userinvisible, groupids FROM $pref"."user WHERE $group_ids ORDER BY username ASC");
while( $user = mysql_fetch_array($r_user) )
{
	$user['username'] = parse_code($user['username']);
	if( $user['usertitle'] )
	{
		$user['userlevel'] = $user['usertitle'];
	}
	else
	{
		// group titling..
		reset($a_group);
		while( list(, $group) = each($a_group) )
		{
			if( strstr($user['groupids'], ','.$group['groupid'].',' ) )
			{
				$user['userlevel'] = $group['title'];
				break;
			}
		}
	}
	
	$user['useremail'] = get_email( $user, true );

	if( isset($a_online[$user['userid']]) && $user['userinvisible'] != 1 )
	{
		$user['userisonline'] = 'Ja';
	}
	else
	{
		$user['userisonline'] = 'Nein';
	}
	
	eval($TRow->GetTemplate("TEAMROWS"));
}

$navpath .= 'Forumstaff';

eval($TTeam->GetTemplate("CONTENT"));
eval($TFrame->GetTemplate());

?>
