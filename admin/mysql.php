<?php
/* $Id: mysql.php 89 2004-11-07 00:43:23Z td $ */
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

include "common.inc.php";
tb_header();

if ( $action == '' )
{
	$r_post = query("SELECT count(postid) AS postcount FROM ".$pref."post");
	$post = mysql_fetch_array($r_post);
	$r_thread = query("SELECT count(threadid) AS threadcount FROM ".$pref."thread");
	$thread = mysql_fetch_array($r_thread);
	
	list($mysqlmajor,$mysqlminor,)=explode('.', mysql_get_server_info());
	if ( intval( $mysqlmajor ) > 3 || (intval($mysqlmajor) == 3 && intval($mysqlminor) >= 23) )
	{
		$r_table_details = query("SHOW TABLE STATUS FROM $mysql_db");
		while ( $table_details = mysql_fetch_array($r_table_details) )
		{
			if ( $showdbdetails )
			{
				if ( substr($table_details['Name'], 0, strlen( $pref ) ) == $pref )
				{
					$db_detail_row++;
					$db_detail_rows .= "<TR><TD bgcolor=" .  ( ( $db_detail_row % 2 ) == 0 ? "#DDDDDD" : "#EEEEEE" ) . ">" . $table_details['Name'] . "</TD><TD bgcolor=" .  ( ( $db_detail_row % 2 ) == 0 ? "#DDDDDD" : "#EEEEEE" ) . ">" . $table_details['Rows'] . "</TD><TD bgcolor=" .  ( ( $db_detail_row % 2 ) == 0 ? "#DDDDDD" : "#EEEEEE" ) . ">" . round ( ( ( $table_details['Data_length'] + $table_details['index_length'] ) / 1024 ),1 ). " kb</TD></TR>\n";
				}
				$db_box = '
			  <TABLE border="0" cellspacing="1" cellpadding="1" bgcolor="black" width="300">
			    <TR>
				  <TD bgcolor="#FCFCFC" colspan="2"><strong>Statistics: Mysql</strong></TD>
				</TR>
				<TR>
				  <TD bgcolor="#FCFCFC">
				    <Table border="0" cellspacing="0" cellpadding="0" width="100%">
					<TR><TD bgcolor="#DDDDDD"><strong>Table</strong></TD><TD bgcolor="#DDDDDD"><strong>Records</strong></TD><TD bgcolor="#DDDDDD"><strong>Size</strong></TD></TR>
					' . $db_detail_rows . '
					    <TD colspan="3" align="center"><A HREF="mysql.php?session=' . $session . '">&laquo; Hide table details &raquo;</A></TD>
					</TABLE>
				  </TD>
				</TR>
			  </TABLE>';
			}
			else
			{
				if ( substr($table_details['Name'], 0, strlen( $pref ) ) == $pref )
				{
					$db_detail['size_content'] += $table_details['Data_length'];
					$db_detail['size_index'] += $table_details['Index_length'];			
				}
				$db_box = '
			  <TABLE border="0" cellspacing="1" cellpadding="1" bgcolor="black" width="300">
			    <TR>
				  <TD bgcolor="#FCFCFC" colspan="2"><strong>Statistics: Mysql</strong></TD>
				</TR>
				<TR>
				  <TD bgcolor="#FCFCFC">
				    <Table border="0" cellspacing="0" cellpadding="0" width="100%">
					  <TR>
					    <TD width="150">Content:</TD>
						<TD width="150">' . round( ( $db_detail['size_content'] / 1024 ), 1) . ' Kbytes</TD>
					  </TR>
					  <TR>
					    <TD width="150">Index:</TD>
						<TD width="150">' . round( ( $db_detail['size_index'] / 1024 ), 1 ) . ' Kbytes</TD>
					  </TR>
					  <TR>
					    <TD width="150">Total:</TD>
						<TD width="150">' . round( ( ( $db_detail['size_index'] + $db_detail['size_content'] ) / 1024 ),1 ) . ' Kbytes</TD>
					  </TR>
                                          <TR>
                                             <TD width="150">Optimize:</TD>
                                             <TD width="150"><A HREF="mysql.php?session=' . $session . '&action=optimize">&laquo; optimize now &raquo;</a></TD>
                                         </TR>
					  <TR>
					    <TD colspan="2" align="center"><A HREF="mysql.php?session=' . $session . '&showdbdetails=1">&laquo; Show table details &raquo;</A></TD>
					  </TR>
					</TABLE>
				  </TD>
				</TR>
			  </TABLE>
			';
			}
		}
	}
	else
	{
		$db_box = '<strong>You have to update your MySQL Version in order to see table stats</strong>';
	}
	
	print("<strong>MySQL-Statistics</strong>");
	print('<BR><BR>
		<TABLE border="0" cellspacing="0" cellpadding="0">
		  <TR>
		    <TD valign="top">
			  <TABLE border="0" cellspacing="1" cellpadding="1" bgcolor="black" width="300">
			    <TR>
				  <TD bgcolor="#FCFCFC" colspan="2"><strong>Statistics: Users</strong></TD>
				</TR>
				<TR>
				  <TD bgcolor="#FCFCFC">
				    <Table border="0" cellspacing="0" cellpadding="0" width="100%">
					  <TR>
					    <TD width="150">Registered users:</TD>
						<TD width="150">' . getusercount() . '</TD>
					  </TR>
					  <TR>
					    <TD width="150">Active users:</TD>
						<TD width="150">' . getactiveusers() . ' ( ' . round( ( getactiveusers() / getusercount() ) * 100 ) . ' % )</TD>
					  </TR>
					</TABLE>
				  </TD>
				</TR>
			  </TABLE>
			</TD>
			<TD width="20">
			</TD>
			<TD valign="top">
			  <TABLE border="0" cellspacing="1" cellpadding="1" bgcolor="black" width="300">
			    <TR>
				  <TD bgcolor="#FCFCFC" colspan="2"><strong>Statistics: Threads & posts</strong></TD>
				</TR>
				<TR>
				  <TD bgcolor="#FCFCFC">
				    <Table border="0" cellspacing="0" cellpadding="0" width="100%">
					  <TR>
					    <TD>Threads:</TD>
						<TD>' . $thread['threadcount']  . '</TD>
					  </TR>
					  <TR>
					    <TD>Replys:</TD>
						<TD>' . ( $post['postcount'] - $thread['threadcount'] ) . ' </TD>
					  </TR>
					  <TR>
					    <TD>&oslash; number of replys per thread:</TD>
						<TD>' .
						/* fixed division durch null, wenn keine threads --dp */
						( $thread['threadcount'] ? round( ( $post['postcount'] - $thread['threadcount'] ) / $thread['threadcount'] ) : '0' ). '</TD>
					  </TR>
					</TABLE>
				  </TD>
				</TR>
			  </TABLE>
			</TD>
		  </TR>
		  <TR>
		    <TD>&nbsp;</TD>
		  </TR>
		  <TR>
		    <TD colspan="1">
		' . $db_box . '
			</TD>
			<TD width="20">
			</TD>			
			<TD valign="top">
			  <TABLE border="0" cellspacing="1" cellpadding="1" bgcolor="black" width="300">
			    <TR>
				  <TD bgcolor="#FCFCFC" colspan="2"><strong>Clean: Set correct postcount</strong></TD>
				</TR>
				<TR>
				  <TD bgcolor="#FCFCFC">
				    <Table border="0" cellspacing="0" cellpadding="0" width="100%">
					  <TR>
					    <TD valign="top">
					        <form action="mysql.php?session=' . $session . '&action=postcount" method="post" target="_self">	
						<input type="submit" value="Recount posts">
						</form>
						</TD>
					  </TR>
					</TABLE>
				  </TD>
				</TR>
                                <TR>
				  <TD bgcolor="#FCFCFC" colspan="2"><strong>Clean: Set correct threadcount</strong></TD>
				</TR>
				<TR>
				  <TD bgcolor="#FCFCFC">
				    <Table border="0" cellspacing="0" cellpadding="0" width="100%">
					  <TR>
					    <TD valign="top">
					        <form action="mysql.php?session=' . $session . '&action=threadcount" method="post" target="_self">	
						<input type="submit" value="Recount threads">
						</form>
						</TD>
					  </TR>
					</TABLE>
				  </TD>
                                </TR>
			  </TABLE>
			</TD>
		  </TR>
		  <TR>
		    <TD>&nbsp;</TD>
		  </TR>
		  <TR>
		    <TD colspan="3">
			  <TABLE border="0" cellspacing="1" cellpadding="1" bgcolor="black" width="100%">
			    <TR>
				  <TD bgcolor="#FCFCFC" colspan="2"><strong>Mysql-Cleaner</strong></TD>
				</TR>
				<TR>
				  <TD bgcolor="#FCFCFC">
				    <Table border="0" cellspacing="0" cellpadding="0" width="600">
					  <TR>
					    <TD><font color="red"><strong>Warning:</strong><BR></font>
						<hr noshade>
						<font color="red" size="1"><B>This utility is only suitable for advanced admins. You could delete active users and the content of your forum!</B></font>
						<hr noshade><form action="mysql.php?session=' . $session . '&action=delusers&save=1" method="post">
						Delete all <strong>users</strong>, who <BR>
						&raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;are <strong>longer</strong> registered than <input type="text" name="reg_days" size="4" maxlength="3" value="0"> day(s),<BR>
						&raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;haven\'t been online for <strong>at least</strong> <input type="text" name="laston_days" size="4" maxlength="3" value="0"> day(s) and<BR>
						&raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;have <strong>less</strong> than <input type="text" name="posts" size="5" maxlength="4" value="0"> posts.<BR>
                        &raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;are <strong>not</strong> activated <input type="checkbox" name="activated" value="1">.<BR><BR>
						<input type="submit" value="Delete users"> <font size="1">Note: administrators will not be deleted.</font></form>
						<form action="mysql.php?session=' . $session . '&action=delthreads&save=1" method="post">
						Delete all threads, which<BR>
						&raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; are <strong>older</strong> than <input type="text" name="age_days" size="4" maxlength="3" value="0"> day(s) and<BR>
						&raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; have <strong>less</strong> than <strong><input type="text" name="replies" size="4" maxlength="3" value="1"></strong> posts.<BR>
                        &raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; have <strong>less</strong> than <strong><input type="text" name="views" size="4" maxlength="3" value="1"></strong> views.<BR>
                        &raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; have boardid <strong>in</strong> <strong><input type="text" name="boardid" size="4" value=""></strong> (comma separated, leave blank for all boards).<BR>
                        <input type="submit" value="Delete threads">
						</form>
						</TD>
					  </TR>
					</TABLE>
				  </TD>
				</TR>
			  </TABLE>
			</TD>
		  </TR>
		</TABLE>
	');
}
elseif ( $action == "delusers" )
{
	// ttt: cool query =)
	$r_user = query("SELECT
			u.userid,
			u.username,
			u.useremail,
			u.userjoin,
			u.userposts,
			MAX( o.lastvisitedtime ) AS userlastonline
		FROM
			$pref"."user AS u
		LEFT OUTER JOIN
			$pref"."lastvisited AS o
		USING( userid )
		WHERE
			NOT u.userisadmin = 1
			AND NOT u.usernodelete = 1
			AND u.userjoin < '" . ( time() - ( $reg_days * 86400 ) ) . "'
			AND u.userposts < '$posts'
            AND u.useractivate = '".intval($activated)."'
		GROUP BY u.userid
		HAVING userlastonline IS NULL OR userlastonline < '" . ( time() - ( $laston_days * 86400 ) ) . "'"
	);

    if ( $save != 0 )
	{
		print('Are you sure you want to delete the following users, including their PMs? (' . mysql_num_rows( $r_user ) . ')<BR>
		<form action="mysql.php?session=' . $session . '&action=delusers" method="post">
		<input type="hidden" name="reg_days" value="' . $reg_days . '">
		<input type="hidden" name="laston_days" value="' . $laston_days . '">
		<input type="hidden" name="posts" value="' . $posts . '">
        <input type="hidden" name="activated" value="' . $activated . '">
		<input type=submit value="Delete these ' . mysql_num_rows( $r_user ) . ' user(s) ?"><BR><BR>
		<table border="0" cellspacing="0" cellpadding="0">
		<TD width="30" bgcolor="EEEEEE">don\'t delete</TD><TD width="30" bgcolor="EEEEEE">&nbsp;</TD><TD width="130" bgcolor="EEEEEE"><strong>Username</strong></TD><TD width="200" bgcolor="EEEEEE"><strong>E-Mail</strong> </TD><TD width="170" bgcolor="EEEEEE"><strong>Last online</strong></TD><TD width="170" bgcolor="EEEEEE"><strong>Registration</strong></TD><TD width="50" bgcolor="EEEEEE"><strong>Posts</strong></TD>
		');
		while ( $user = mysql_fetch_array( $r_user ) )
		{
			$i++;
			if ( $i % 2 == "0" )
			{
				$t_bgcolor = "EEEEEE";
			}
			else
			{
				$t_bgcolor = "";
			}
			print('
		  <TR>
			 <TD width="30" bgcolor="' . $t_bgcolor . '"><input type="checkbox" name="users['.$i.']" value="'.$user['userid'].'"></TD><TD width="30" bgcolor="' . $t_bgcolor . '"> ' . $i . '.) </TD><TD width="130" bgcolor="' .$t_bgcolor . '">' . $user['username'] . '</TD><TD width="200" bgcolor="' .$t_bgcolor . '"> ' . $user['useremail'] . ' </TD><TD width="170" bgcolor="' .$t_bgcolor . '">' . date("d.m.Y @ H:i:s",$user['userlastonline']) . '</TD><TD width="170" bgcolor="' .$t_bgcolor . '">' . date("d.m.Y @ H:i:s",$user['userjoin']) . '</TD><TD width="50" bgcolor="' .$t_bgcolor . '">' . $user['userposts'] . '</TD>
		  </TR>');
		}
		print('</table></form>');
	}
	else
	{
		$ids = '';
		while( $a_user = mysql_fetch_array( $r_user ) )
        {
            if(empty($users) || !in_array($a_user['userid'], $users))
            {
                $ids .= ',' . $a_user['userid'];
            }
        }

        $ids = substr( $ids, 1 );

        if( strlen( $ids ) > 0 ) {
			// delete users
			query( "DELETE FROM
					$pref"."user
				WHERE
					userid IN ($ids)"
			);
			@mysql_query( "OPTIMIZE TABLE $pref"."user" );
			// delete from lastvisited
			query( "DELETE FROM
					$pref"."lastvisited
				WHERE
					userid IN ($ids)"
			);
			@mysql_query( "OPTIMIZE TABLE $pref"."lastvisited" );
			// delete any sent or received PMs
			query( "DELETE FROM
					$pref"."pm
				WHERE
					pmtoid IN ($ids)
					OR pmfromid IN ($ids)"
			);
			@mysql_query( "OPTIMIZE TABLE $pref"."pm" );
		}
        
		print("Users successfully deleted.");
	}
}
elseif ( $action == "delthreads" )
{
	$r_threads = query("SELECT
			*
		FROM
			$pref"."thread
		WHERE
			threadtime < '". ( time() - ( $age_days * 86400 ) ) ."'
			AND threadreplies < '" . $replies . "'
            AND threadviews < '". $views."'
			AND threadlink = 0" // don't pick any threadlinks
			   . ((isset($boardid) && $boardid != "") ? " AND boardid IN (".addslashes($boardid).")" : "")
	);
	
	if ( $save != 0 )
	{
		print('Are you sure you want to delete the following thread(s) ? (' . mysql_num_rows( $r_threads ) . ')<BR>
		<form action="mysql.php?session=' . $session . '&action=delthreads" method="post">
		<input type="hidden" name="age_days" value="' . $age_days . '">
		<input type="hidden" name="replies" value="' . $replies . '">
        <input type="hidden" name="views" value="' . $views . '">
		<input type="hidden" name="boardid" value="' . $boardid . '">
		<input type=submit value="Delete these ' . mysql_num_rows( $r_threads ) . ' threads(s) ?"><BR><BR>
		<table border="0" cellspacing="0" cellpadding="0">
		<TD width="30" bgcolor="EEEEEE">don\'t delete</TD><TD width="30" bgcolor="EEEEEE">&nbsp;</TD><TD width="130" bgcolor="EEEEEE"><strong>Threadtopic</strong></TD><TD width="200" bgcolor="EEEEEE"><strong>Lastreply</strong> </TD><TD width="60" bgcolor="EEEEEE"><strong>Posts</strong></TD><TD width="60" bgcolor="EEEEEE"><strong>Views</strong></TD><TD width="120" bgcolor="EEEEEE"><strong>Author</strong></TD>
		');
		while ( $threads = mysql_fetch_array( $r_threads ) )
		{
			$i++;
			if ( $i % 2 == "0" )
			{
				$t_bgcolor = "EEEEEE";
			}
			else
			{
				$t_bgcolor = "";
			}
			print('
		  <TR>
			 <TD width="30" bgcolor="' . $t_bgcolor . '"><input type="checkbox" name="threads['.$i.']" value="'.$threads['threadid'].'"></TD><TD width="30" bgcolor="' . $t_bgcolor . '"> ' . $i . '.) </TD><TD width="130" bgcolor="' .$t_bgcolor . '">' . htmlentities($threads['threadtopic']) . '</TD><TD width="200" bgcolor="' .$t_bgcolor . '"> ' . date("d.m.Y @ H:i:s",$threads['threadtime']) . ' </TD><TD width="60" bgcolor="' .$t_bgcolor . '"> ' . $threads['threadreplies'] . ' </TD><TD width="60" bgcolor="' .$t_bgcolor . '"> ' . $threads['threadviews'] . ' </TD><TD width="120" bgcolor="' .$t_bgcolor . '"> ' . $threads['threadauthor'] . ' </TD>
		  </TR>');
		}
		print('</table></form>');
	}
	else
	{
        $a_boardids = array();

		$ids = '';
		while( $a_thread = mysql_fetch_array( $r_threads ) )
		  {
            if(empty($threads) || !in_array($a_thread['threadid'], $threads))
            {
                $ids .= ',' . $a_thread['threadid'];

                if(!in_array($a_thread['boardid'], $a_boardids))
                {
                    $a_boardids[] = $a_thread['boardid'];
                }
            }
          }

		$ids = substr( $ids, 1 );

		if( strlen( $ids ) > 0 ) {
			// delete threads
			query( "DELETE FROM
					$pref"."thread
				WHERE
					threadid IN ($ids)"
			);
			// delete any corresponding links
			query( "DELETE FROM
					$pref"."thread
				WHERE
					threadlink IN ($ids)"
			);
			// and finally get rid of the posts in those threads
			query( "DELETE FROM
					$pref"."post
				WHERE
					threadid IN ($ids)"
			);
			// optimize tables afterwards
			// isn't always supported, that's why we put an @ in front
			@mysql_query( "OPTIMIZE TABLE $pref"."thread" );
			@mysql_query( "OPTIMIZE TABLE $pref"."post" );

			foreach($a_boardids AS $b)
			  {
			    updateboard($b);
			  }
		}
		print("Deleting ... successful");     
	}
}
elseif ( $action == "postcount" )
{
	if( !isset( $do ) || !$do )
	{
		print('	Do you want to continue correcting the postcount of ' . getusercount() . ' users?<BR>
			<a href="mysql.php?session=' . $session . '&action=postcount&do=1">Yes</a> || <a href="mysql.php?session=' . $session . '">No</a>'
			);
		exit();
	}
	print("Postcount is being checked ... please wait ...<BR><BR>");
	$r_user = query("	SELECT user.userid, user.userposts, count(post.userid) AS postcount
				FROM " . $pref . "user AS user
				INNER JOIN " . $pref . "post AS post
				ON post.userid = user.userid
				GROUP BY user.userid");
	$a_user = array();
	$corrected = 0;
	$checked = 0;
	$wrong = 0;
	while( $a_user = mysql_fetch_array( $r_user ) )
	{
		if( $a_user['userposts'] != $a_user['postcount'] )
		{
			query("UPDATE " . $pref . "user SET userposts = '" . $a_user['postcount'] . "' WHERE userid = '" . $a_user['userid'] . "'");
			$corrected++;
			if( $a_user['userposts'] > $a_user['postcount'] )
			{
				$wrong += $a_user['userposts'] - $a_user['postcount'];
			}
			else
			{
				$wrong += $a_user['postcount'] - $a_user['userposts'];
			}
		}
		$checked++;
	}
	print('	Postcount of <strong>' . $checked . '</strong> users has been checked. (<strong>Attention: this only includes users with posts.</strong>)<br>
		It has been corrected <strong>' . $corrected . '</strong> times.<br>
		Totally <strong>' . $wrong . '</strong> Posts had been counted falsely.<br>
		<br><a href="mysql.php?session=' . $session . '">back to mysql-clean</a>');
}
elseif($action == "threadcount")
{
  	if( !isset( $do ) || !$do )
	{
		print('	Do you want to continue correcting the threadcount of ' . getboardcount() . ' boards?<BR>
			<a href="mysql.php?session=' . $session . '&action=threadcount&do=1">Yes</a> || <a href="mysql.php?session=' . $session . '">No</a>'
			);
		exit();
	}
	print("Threadcount is being checked ... please wait ...<BR><BR>");

	$a_board = array();
	$a_boards = array();

	$boards = 0;

	$r_board = query("SELECT boardid FROM $pref"."board");

	if(mysql_num_rows($r_board) >= 1)
	{
	  while($a_board = mysql_fetch_array($r_board))
	    {
	      $a_boards[] = $a_board['boardid'];
	    }

	  foreach($a_boards as $k => $v)
	    {
	      ++$boards;
	      updateboard($v);
	    }
	}
	
	print('Threadcount of <strong>' . $boards . '</strong> boards has been checked and corrected.<br>
               <br><a href="mysql.php?session=' . $session . '">back to mysql-clean</a>');
}
elseif($action == "optimize")
{
  $a_tables = array("adminlog", "avatar", "ban", "bannedwords", "board", "calendar", "category", "group", "groupboard", "lastvisited", "news", "online", "pm", "post", "qlink", "rank", "registry", "registrygroup", "session", "style", "thread", "user", "flood");

  print('Optimizing ... please wait ...<br>');

  query("OPTIMIZE TABLE $pref".join(", $pref", $a_tables));

  print('done<br><a href="mysql.php?session=' . $session . '">back to mysql-clean</a>');
}
tb_footer();
?>

