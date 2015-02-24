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

include "common.inc.php";

tb_header();

function EditboxEncode($string)
{
    $string = str_replace('&', '&amp;', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);

    return $string;
}

function EditboxDecode($string)
{
    $string = str_replace('&amp;', '&', $string);
    $string = str_replace('&quot;', '"', $string);
    $string = str_replace('&lt;', '<', $string);
    $string = str_replace('&gt;', '>', $string);

    return $string;
}

function selectbox_board($boardids)
{
    global $pref;
    global $config;
    global $session;
    $r_board = query("SELECT boardid, boardname FROM " . $pref . "board");
    while ( $board = mysql_fetch_array($r_board) )
    {
    $selectbox .= "<option value=\"$board[boardid]\" " . ( stristr($boardids,";" . $board[boardid] . ";") != false ? "selected" : "" ) . ">$board[boardname]</option>";
    }
    return($selectbox);
}

function NewsForm($action, $news)
{
    global $session;
    print '<form name="announcements" method="post" action="announcements.php">
  <table border="0" cellspacing="1" cellpadding="2">
    <tr>
      <td><label for="announcement-title">Title</label></td>
      <td>
        <input class="tbinput" id="announcement-title" type="text" name="news[newstopic]" size="45" value="' . EditboxEncode($news[newstopic]) . '">
      </td>
    </tr>
    <tr>
      <td valign="top"><label for="announcement-body">Body</label></td>
      <td>
        <textarea class="tbinput" id="announcement-body" name="news[newstext]" cols="60" rows="8">' . $news[newstext] . '</textarea>
        Note: You can use ThWboard Code in announcements.
      </td>
    </tr>
    <tr>
      <td valign="top"><label for="announcement-boardids">Boards</label></</td>
      <td>
        <SELECT class="tbinput" id="announcement-boardids" name="boardids[]" size="8" multiple>' . selectbox_board($news[boardid]) . '</select>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="submit" name="submit" value="Save">
        <input type="hidden" name="newsid" value="' . $news[newsid] . '">
        <input type="hidden" name="action" value="' . $action . '">
        <input type="hidden" name="session" value="' . $session . '">
      </td>
    </tr>
  </table>
</form>';
}


// ===================================================
// ===================================================
// ===================================================
if( $action == "ListNews" )
{
    print '<a href="announcements.php?action=AddNews&session=' . $session . '">Add announcement</a>';
    print '<h3>Announcements</h3>';

    $r_news = query("SELECT newsid, newstopic, newstext, newstime FROM ".$pref."news ORDER BY newstime DESC");
    echo mysql_error();

    print '<ul id="announcements">';
    while( $news = mysql_fetch_array($r_news) )
    {
        print '<li>';
        print date('d.m.Y H:i: ', $news[newstime]) . "$news[newstopic] [ <a href=\"announcements.php?action=EditNews&session=$session&newsid=$news[newsid]\" title=\"Edit announcement ".htmlspecialchars($news['newstopic'])."\">edit</a> ] [ <a href=\"announcements.php?action=DeleteNews&session=$session&newsid=$news[newsid]\">delete</a> ]</a><br>";
        print '</li>';
    }
    print '</ul>';
}


// ===================================================
// ===================================================
// ===================================================
elseif( $action == "EditNews" )
{
    print '<a href="announcements.php?action=AddNews&session=' . $session . '">Add announcement</a> ';
    print "<a href=\"announcements.php?action=ListNews&session=" . $session . "\">List announcements</a>";
    print '<b>Edit Announcement</b><br><br>';

    $r_news = query("SELECT newsid, boardid, newstopic, newstext, newstime FROM ".$pref."news WHERE newsid=$newsid");
    $news = mysql_fetch_array($r_news);
    NewsForm("UpdateNews", $news);
}


// ===================================================
// ===================================================
// ===================================================
elseif( $action == "UpdateNews" )
{
    print '<a href="announcements.php?action=AddNews&session=' . $session . '">Add announcement</a> ';
    print "<a href=\"announcements.php?action=ListNews&session=" . $session . "\">List announcements</a>";
    print '<b>Edit Announcement</b><br><br>';

    $news[newstopic] = EditboxDecode($news[newstopic]);

    while( list(, $boardids2) = @each($boardids) )
    {
        $add_board = $add_board.$boardids2.";";
    }
    query("UPDATE ".$pref."news SET newstext='" . addslashes($news[newstext]) . "', newstopic='" . addslashes($news[newstopic]) . "', boardid=';$add_board' WHERE newsid=$newsid");
    print "Announcement saved.";
}


/*
 * ########################################################################################
 * Add an announcement
 * ########################################################################################
 */
if ('AddNews' == $_REQUEST['action']) {
    print "<a href=\"announcements.php?action=ListNews&session=" . $session . "\">List announcements</a>";
    print "<h3>New Announcement</h3>";

    if (isset($_POST['submit'])) {
        if (empty($_POST['news']['newstopic'])) {
            print "The announcement title can't be empty.";
        } elseif (empty($_POST['news']['newstext'])) {
            print "The announcement body can't be empty.";
        } elseif (empty($_POST['boardids'])) {
            print "The announcement needs to visible in at least one board.";
        } else {
            $boardIDs = array_map('intval', $_POST['boardids']);
            $boardIDs = implode(',', $boardIDs);

            $r_boards = query(
<<<SQL
SELECT
    boardid
FROM
    {$pref}board
WHERE
    boardid IN ({$boardIDs})
SQL
            );

            $boardIDs = [];
            while ($board = mysql_fetch_assoc($r_boards)) {
                $boardIDs[] = $board['boardid'];
            }

            if (empty($boardIDs)) {
                print "The announcement needs to visible in at least one board.";
            } else {
                $newsTopic = addslashes($_POST['news']['newstopic']);
                $newsBody = addslashes($_POST['news']['newsbody']);

                $boardIDs = ';' . implode(';', $boardIDs) . ';';

                query(
<<<SQL
INSERT INTO
    {$pref}news
(
    newstopic,
    boardid,
    newstext,
    newstime
) VALUES (
    '{$newsTopic}',
    '{$boardIDs}',
    '{$newsBody}',
    UNIX_TIMESTAMP()
)
SQL
                );

                print "Announcement saved.";
            }
        }
    } else {
        NewsForm('AddNews', []);
    }
}

// ===================================================
// ===================================================
// ===================================================
elseif( $action == "DeleteNews" )
{
    if( $confirm == 1 )
    {
        query("DELETE FROM ".$pref."news WHERE newsid=$newsid");

        print 'Announcement has been deleted!';
    }
    else
    {
        print 'Are you sure?<br><a href="announcements.php?session=' . $session . '&newsid=' . $newsid . '&confirm=1&action=DeleteNews">yes</a>';
    }
}

tb_footer();
