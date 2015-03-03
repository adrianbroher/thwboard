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

function r_stripslashes(&$array)
{
    while( list($k, $v) = each($array) )
    {
        if( $k != 'argc' && $k != 'argv' && (strtoupper($k) != $k || ''.intval($k) == "$k") )
        {
            if( is_string($v) )
            {
                $array[$k] = stripslashes($v);
            }
            if( is_array($v) )
            {
                $array[$k] = r_stripslashes($v);
            }
        }
    }
    return $array;
}

function query($query)
{
    $result = mysql_query($query);
    $rows = mysql_affected_rows();
    $error = mysql_error();

    if( $error )
    {
        print "\n<font color=red><b>MySQL: $error</b></font><br>";
        print "<pre><b>query:</b> $query\n\nAffected Rows: $rows</pre>";
        exit;

    }

    return $result;
}


function get_templatesetarray()
{
    $a_templateset = array();

    $dp = opendir('../templates/');
    while( $file = readdir($dp) )
    {
        if( $file != '.' && $file != '..' && $file != 'mail' && $file != 'CVS' && $file != 'css')
        {
            if( is_dir('../templates/'.$file) )
            {
                $a_templateset[] = $file;
            }
        }
    }

    return $a_templateset;
}


function getusercount()
{
    global $pref;
    $r_user = query("SELECT count(userid) AS usercount FROM ".$pref."user");
    $user = mysql_fetch_array($r_user);

    return $user['usercount'];
}

function getactiveusers()
{
    global $pref;
    $r_user = query("SELECT count(userid) AS usercount FROM ".$pref."user WHERE userlastpost > " . (time() - 60 * 60 * 24 * 31));
    $user = mysql_fetch_array($r_user);

    return $user['usercount'];
}

function getboardcount()
{
  global $pref;
  $r_board = query("SELECT COUNT(boardid) AS boardcount FROM $pref"."board");
  $board = mysql_fetch_array($r_board);

  return $board['boardcount'];
}

function tb_header($redir_url = '')
{
    global $session, $config, $HTTP_SERVER_VARS;
    print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>ThWboard Admin Center</title>';

    if( $redir_url )
    {
        print '
        <meta http-equiv="Refresh" content="1; URL='.$redir_url.'">';
    }

    print '
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <style type="text/css">
a:link { color:#3366CC; text-decoration: underline}
a:visited { color:#3366CC; text-decoration: underline}
a:active { color:#A63600; text-decoration: underline}
a:hover { color:#4477DD; text-decoration: none }
body { font-family: Verdana, Arial, Helvetica; font-size: 10pt; margin: 0; }
td { font-family: Verdana, Arial, Helvetica; font-size: 10pt }
select { font-family: Verdana, Arial, Helvetica; font-size: 8pt }
textarea { font-family: Verdana, Arial, Helvetica; font-size: 8pt }
input { font-family: Verdana, Arial, Helvetica; font-size: 8pt }
.htmlsource { font-family: "Verdana, Helvetica", Courier, mono; font-size: 8pt; color: #2255BB }

h1 {
    background-color: #000;
    color: #FFF;
    font-size: x-small;
    margin: 0;
    padding: .5em;
}

#navigation div {
    background-color: #E9EFF5;
    border-top: 1px solid #FFFFFF;
    border-bottom: 1px solid #C0D1E3;
    color: #4E7DB1;
    font-size: 10px;
    font-weight: bold;
    padding: .3em;
}

#navigation ul {
    font-size: 10px;
    list-style-position: inside;
    margin: 0;
    padding-left: .3em;
}

#navigation li {
    padding: .3em;
    padding-bottom: .6em;
}

#navigation a:link, #navigation a:visited, #navigation a:active {
    color:#3F648E;
    text-decoration: none;
}

#navigation a:hover {
    color:#3F648E;
    text-decoration: underline;
}

#board-order {
    padding-left: 0;
    list-style-type: none;
}

#board-order .category {
    font-size: small;
    font-weight: bold;
}

#board-order .board {
    font-size: small;
    font-weight: bold;
}

#board-order dl {
    margin: 0px;
}

#board-order dl dd {
    font-size: small;
    margin-left: 0;
    font-weight: normal;
}

ul.actions {
    margin: 0px;
    padding: 0px;
    margin-bottom: 1em;
    margin-top: .2em;
}

ul.actions li {
    display: inline;
    font-size: small;
    font-weight: normal;
}

.actions form {
    display: inline;
}

.actions button {
    background: none;
    border: none;
    color: #36C;
    font-family: inherit;
    font-size: small;
    font-weight: normal;
    cursor: pointer;
    overflow: visible;
    padding: 0;
    text-align: left;
    text-decoration: underline;
    width: auto;
}

.actions button:hover {
    text-decoration: none;
}';

    if( !preg_match('/opera/Ui', $HTTP_SERVER_VARS['HTTP_USER_AGENT']) )
    {
        print '
.tbinput {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt; border: #999999; border-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; background-color: #F9F9F9}';
    }

    print '
        </style>
    </head>
    <body bgcolor="#FCFCFC" text="#575757">
        <h1>ThWboard admin center</h1>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr id="navigation">
                <td bgcolor="#E0E8F1" width="160" valign="top">
                    <div>Links</div>
                    <ul>
                        <li><a href="index.php?session='.$session.'">Home</a></li>
                        <li><a href=".." target="_blank">Your forums</a></li>
                        <li><a href="http://www.thwboard.de" target="_blank">ThWboard homepage</a></li>
                    </ul>
                    <div>General</div>
                    <ul>
                        <li><a href="documents.php?session='.$session.'&amp;action=ListDocs">Documentation</a></li>
                        <li><a href="index.php?session='.$session.'&amp;action=EditSettings">Basic settings</a></li>
                        <li><a href="rank.php?session='.$session.'&amp;action=ViewRanks">Ranks</a></li>
                        <li><a href="announcements.php?session='.$session.'">Announcements</a></li>
                        <li><a href="links.php?session='.$session.'&amp;action=ListLinks">Quicklinks</a></li>
                    </ul>
                    <div>Structure</div>
                    <ul>
                        <li><a href="boards.php?session='.$session.'">Categories and Boards</a></li>
                    </ul>
                    <div>Extensions</div>
                    <ul>
                        <li><a href="calendar.php?session='.$session.'">Calendar</a></li>
                        <li><a href="newsletter.php?session='.$session.'">Newsletter</a></li>
                        <li><a href="bwords.php?session='.$session.'">Badwords protection</a></li>
                        <li><a href="avatar.php?session='.$session.'&amp;action=ListAvatars">List avatars</a></li>
                        <li><a href="avatar.php?session='.$session.'&amp;action=ListImportAvatars">Import avatars</a></li>
                        <li><a href="index.php?session='.$session.'&amp;action=EditSettings#avatar">Avatar settings</a></li>
                    </ul>
                    <div>Group management</div>
                    <ul>
                        <li><a href="groups.php?session='.$session.'&amp;action=list">View / edit groups</a></li>
                        <li><a href="groups.php?session='.$session.'&amp;action=create">Create group</a></li>
                    </ul>
                    <div>User management</div>
                    <ul>
                        <li><a href="users.php?session='.$session.'&amp;action=bans">User bans</a></li>
                        <li><a href="useredit.php?session='.$session.'&amp;action=Filter">Search / edit users</a></li>
                        <li><a href="useredit.php?session='.$session.'&amp;action=AddUser">Add user</a></li>
                        <li><a href="useredit.php?session='.$session.'&amp;action=DeleteUser">Delete user</a></li>
                    </ul>
                    <div>Appearance</div>
                    <ul>
                        <li><a href="style.php?session='.$session.'&amp;action=ListStyles">View / edit styles</a></li>
                        <li><a href="style.php?session='.$session.'&amp;action=NewStyle">Create style</a></li>
                        <li><a href="style.php?session='.$session.'&amp;action=ImportStyle">Import style</a></li>
                        <li><a href="dynx.php">Download styles</a></li>
                    </ul>
                    <div>Templates</div>
                    <ul>
                        <li><a href="t-editor.php?session='.$session.'&amp;action=ListTemplateSets">Template editor</a></li>
                        <li><a href="mails.php?session='.$session.'&amp;action=ListMails">Email editor</a></li>
                    </ul>
                    <div>Misc</div>
                    <ul>
                        <li><a href="versioninfo.php?session='.$session.'">Version info</a></li>
                        <li><a href="query.php?session='.$session.'">thwbMyAdmin</a></li>
                        <li><a href="mysql.php?session='.$session.'">Mysql-Clean</a></li>
                    </ul>
                    <div>Logout</div>
                    <ul>
                        <li><a href="index.php?action=logout&amp;session='.$session.'">Logout</a></li>
                    </ul>
                </td>
                <td bgcolor="#000000" width="1"></td>
                <td valign="top">
                    <table width="100%" border="0" cellspacing="0" cellpadding="8">
                        <tr>
                            <td>';
}

function tb_footer()
{
    print '
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr bgcolor="#000000">
    <td height="1" width="160"></td>
    <td height="1" width="1"></td>
    <td height="1"></td>
  </tr>
  <tr>
    <td width="160">&nbsp;</td>
    <td width="1"></td>
    <td align="right"><font size="1">--&gt; ThWboard (c) 2000-2004 ThWboard Development Group&nbsp;&nbsp;&nbsp;</font></td>
  </tr>
</table>
</body>
</html>
';
}

function format_db_date($string_date)
{
  if ( $string_date == '')
  {
    $output = "<B>Error :</B> Bad Time String !";
  }
  else
  {
    $output = substr($string_date,8,2) . "." . substr($string_date,5,2) . "." . substr($string_date,0,4);
  }
return $output;
}

/*
returns a string for use in <select> containing all categories and boards, ordered correctly
*/
function get_ordered_board_list()
{
    global $pref;
    $s = '';

    $a_board = array();
    $r_board = query("SELECT boardid, boardname, categoryid FROM $pref"."board ORDER BY boardorder ASC");
    while( $board = mysql_fetch_array($r_board) )
    {
        $a_board[$board['categoryid']][] = $board;
    }

    $r_category = query("SELECT categoryid, categoryname FROM $pref"."category ORDER BY categoryorder ASC");
    while( $category = mysql_fetch_array($r_category) )
    {
        if( $a_board[$category['categoryid']] )
        {
            $s .= '<option value="0">'.$category['categoryname'].'</option>';
            while( list(, $board) = each($a_board[$category['categoryid']]) )
            {
                $s .= '<option value="'.$board['boardid'].'"> -- '.$board['boardname'].'</option>';
            }
        }
    }

    return $s;
}

function updateboard($boardid)
{
    global $pref;
    // updates last posttime/thread/author of a board ..
    $r_thread = query("SELECT threadid, threadtopic, threadtime, threadlastreplyby FROM ".$pref."thread WHERE threadlink='0' AND boardid='".intval($boardid)."'  GROUP BY threadid ORDER BY threadtime DESC LIMIT 1");

    if( mysql_num_rows($r_thread) < 1 )
    {
        query("UPDATE ".$pref."board SET
            boardlastpost='0',
            boardthreadid='0',
            boardthreadtopic='',
            boardlastpostby='',
                        boardposts='0',
                        boardthreads='0'
        WHERE boardid='".intval($boardid)."'");
    }
    else
    {
        $thread = mysql_fetch_array($r_thread);

        $r_thread = query("SELECT COUNT(threadid) AS threadcount, SUM(threadreplies) AS postcount FROM ".$pref."thread WHERE boardid=$boardid");
        $thread = array_merge($thread, mysql_fetch_array($r_thread));

        $thread['postcount'] += $thread['threadcount']; // threads without replies.

        query("UPDATE ".$pref."board SET
            boardlastpost='$thread[threadtime]',
            boardthreadid='$thread[threadid]',
            boardthreadtopic='" . addslashes($thread['threadtopic']) . "',
            boardlastpostby='" . addslashes($thread['threadlastreplyby']) . "',
                        boardposts='".addslashes($thread['postcount'])."',
                        boardthreads='".addslashes($thread['threadcount'])."'
        WHERE boardid='".intval($boardid)."'");
    }
}

function loginform()
{
    global $HTTP_SERVER_VARS;
    print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>ThWboard Admin Center</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
body { margin: 0; padding: 0; }
input          { font-family: Verdana, Arial; font-size: 8pt }
</style>
</head>
<body bgcolor="#F2F2F2" text="#575757">
<table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td bgcolor="#000000"><font color="#FFFFFF" face="Verdana, Arial" size="1">ThWboard
      Admin Center :: Login</font></td>
  </tr>
  <tr>
    <td>
      <form name="login" method="post" action="index.php">
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <table border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td bgcolor="#DBDBDB" width="1" height="1"></td>
            <td bgcolor="#DBDBDB" height="1"></td>
            <td bgcolor="#DBDBDB" width="1" height="1"></td>
          </tr>
          <tr>
            <td bgcolor="#DBDBDB" width="1"></td>
            <td>
              <table border="0" cellspacing="0" cellpadding="4">
                <tr>
                  <td><font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif" color="#3366CC">Login</font></b></font>
                    <table border="0" cellspacing="0" cellpadding="2">
                      <tr>
                        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif">-&gt;
                          <label for="login-username">Username</label>&nbsp;&nbsp;&nbsp;</font></td>
                        <td>
                          <input id="login-username" type="text" name="l_username" size="12">
                        </td>
                      </tr>
                      <tr>
                        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif">-&gt;
                          <label for="login-password">Password</label>&nbsp;&nbsp;&nbsp;</font></td>
                        <td>
                          <input id="login-password" type="password" name="l_password" size="12">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td align="right">
                    <input type="submit" name="go" value="Login">
                  </td>
                </tr>
              </table>
            </td>
            <td bgcolor="#FFFFFF" width="1"></td>
          </tr>
          <tr>
            <td bgcolor="#DBDBDB" width="1" height="1"></td>
            <td bgcolor="#FFFFFF" height="1"></td>
            <td bgcolor="#FFFFFF" width="1" height="1"></td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
<script type="text/javascript">
<!--
if (document.login) document.login.l_username.focus();
// -->
</script>
</body>
</html>';
}
