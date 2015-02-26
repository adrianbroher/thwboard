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

function EditboxDecode($string)
{
    $string = str_replace('&amp;', '&', $string);
    $string = str_replace('&quot;', '"', $string);
    $string = str_replace('&lt;', '<', $string);
    $string = str_replace('&gt;', '>', $string);

    return $string;
}

function NewsForm($handler, $news)
{
    global $pref;

    $r_board = query(
<<<SQL
SELECT
    boardid AS ID,
    boardname AS name
FROM
    {$pref}board
SQL
    );

    $boards = [];
    while ($board = mysql_fetch_object($r_board)) {
        $boards[] = $board;
    }

    print '<form name="announcements" method="post" action="'.htmlspecialchars($handler).'">
  <table border="0" cellspacing="1" cellpadding="2">
    <tr>
      <td><label for="announcement-title">Title</label></td>
      <td>
        <input class="tbinput" id="announcement-title" type="text" name="announcement-title" size="45" value="' . htmlspecialchars($news['newstopic']) . '">
      </td>
    </tr>
    <tr>
      <td valign="top"><label for="announcement-body">Body</label></td>
      <td>
        <textarea class="tbinput" id="announcement-body" name="announcement-body" cols="60" rows="8">' . htmlspecialchars($news['newstext']) . '</textarea>
        Note: You can use ThWboard Code in announcements.
      </td>
    </tr>
    <tr>
      <td valign="top"><label for="announcement-boardids">Boards</label></</td>
      <td>
        <SELECT class="tbinput" id="announcement-boardids" name="announcement-boardids[]" size="8" multiple>';

    foreach ($boards as $board) {
        print "<option value=\"".htmlspecialchars($board->ID)."\" " . (stristr($news['boardid'], ";".$board->ID.";") != false ? 'selected="selected"' : '') . ">".htmlspecialchars($board->name)."</option>";
    }

    print '</select>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="submit" name="submit" value="Save">
      </td>
    </tr>
  </table>
</form>';
}


/*
 * ########################################################################################
 * List announcements
 * ########################################################################################
 */
if ('ListNews' == $_GET['action']) {
    print "<a href=\"announcements.php?action=new&session=".$session."\">Add announcement</a>";
    print "<h3>Announcements</h3>";

    $r_news = query(
<<<SQL
SELECT
    newsid,
    newstopic,
    newstext,
    newstime
FROM
    {$pref}news
ORDER BY
    newstime DESC
SQL
    );

    print "<ul id=\"announcements\">";

    while ($news = mysql_fetch_array($r_news)) {
        print "<li>";
        print date("d.m.Y H:i: ", $news['newstime']) . $news['newstopic'] . " [ <a href=\"announcements.php?action=edit&session=".$session."&amp;id=".$news['newsid']."\" title=\"Edit announcement ".htmlspecialchars($news['newstopic'])."\">edit</a> ] [ <a href=\"announcements.php?action=delete&session=".$session."&amp;id=".$news['newsid']."\" title=\"Delete announcement ".htmlspecialchars($news['newstopic'])."\">delete</a> ]</a><br>";
        print "</li>";
    }

    print "</ul>";
}


/*
 * ########################################################################################
 * Edit an announcement
 * ########################################################################################
 */
if ('edit' == $_GET['action']) {
    print "<a href=\"announcements.php?action=new&amp;session=" . $session . "\">Add announcement</a> ";
    print "<a href=\"announcements.php?action=ListNews&amp;session=" . $session . "\">List announcements</a>";
    print "<h3>Edit Announcement</h3>";

    if (isset($_POST['submit'])) {
        if (empty($_POST['announcement-title'])) {
            print "The announcement title can't be empty.";
        } elseif (empty($_POST['announcement-body'])) {
            print "The announcement body can't be empty.";
        } elseif (empty($_POST['announcement-boardids'])) {
            print "The announcement needs to visible in at least one board.";
        } else {
            $boardIDs = array_map('intval', $_POST['announcement-boardids']);
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
                $newsTopic = addslashes(EditboxDecode($_POST['announcement-title']));
                $newsBody = addslashes($_POST['announcement-body']);

                $boardIDs = ';' . implode(';', $boardIDs) . ';';

                query(
<<<SQL
UPDATE
    {$pref}news
SET
    newstext  = '{$newsBody}',
    newstopic = '{$newsTopic}',
    boardid   = '{$boardIDs}'
WHERE
    newsid = {$_GET['id']}
SQL
                );

                print "Announcement saved.";
            }
        }
    } else {
        $r_news = query(
<<<SQL
SELECT
    newsid,
    boardid,
    newstopic,
    newstext,
    newstime
FROM
    {$pref}news
WHERE
    newsid = {$_GET['id']}
SQL
        );
        $news = mysql_fetch_array($r_news);
        NewsForm('announcements.php?action=edit&id='.$news['newsid'].'&session='.$session, $news);
    }
}

/*
 * ########################################################################################
 * Add an announcement
 * ########################################################################################
 */
if ('new' == $_GET['action']) {
    print "<a href=\"announcements.php?action=ListNews&session=" . $session . "\">List announcements</a>";
    print "<h3>New Announcement</h3>";

    if (isset($_POST['submit'])) {
        if (empty($_POST['announcement-title'])) {
            print "The announcement title can't be empty.";
        } elseif (empty($_POST['announcement-body'])) {
            print "The announcement body can't be empty.";
        } elseif (empty($_POST['announcement-boardids'])) {
            print "The announcement needs to visible in at least one board.";
        } else {
            $boardIDs = array_map('intval', $_POST['announcement-boardids']);
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
                $newsTopic = addslashes($_POST['announcement-title']);
                $newsBody = addslashes($_POST['announcement-body']);

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
        NewsForm('announcements.php?action=new&session='.$session, []);
    }
}

/*
 * ########################################################################################
 * Delete an announcement
 * ########################################################################################
 */
if ('delete' == $_GET['action']) {
    print "<a href=\"announcements.php?action=new&amp;session=" . $session . "\">Add announcement</a> ";
    print "<a href=\"announcements.php?action=ListNews&amp;session=" . $session . "\">List announcements</a>";
    print "<h3>Delete Announcement</h3>";

    if ($_POST['submit']) {
        query(
<<<SQL
DELETE FROM
    {$pref}news
WHERE
    newsid = {$_GET['id']}
SQL
        );

        print "Announcement has been deleted!";
    } else {
?>
<form method="post" action="announcements.php?action=delete&amp;id=<?= $_GET['id'] ?>&amp;session=<?= $session ?>">
    Do you really want to delete the announcement?
    <input type="submit" name="submit" value="Delete">
</form>
<?php
    }
}

tb_footer();
