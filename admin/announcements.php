<?php

/**
 * ThWboard - PHP/MySQL Bulletin Board System
 * ==========================================
 *
 * Copyright (C) 2000-2004 by ThWboard Development Group
 * Copyright (C) 2015 by Marcel Metz
 *
 * This file is part of ThWboard
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program;  If not, see <http://www.gnu.org/licenses/>.
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

function NewsForm($handler, $announcement)
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

?>
<form class="entity-form" name="announcements" method="post" action="<?= htmlspecialchars($handler) ?>">
    <div>
        <label for="announcement-title">Title</label>
        <input id="announcement-title" type="text" name="announcement-title" size="45" value="<?= htmlspecialchars($announcement->title) ?>">
    </div>
    <div>
        <label for="announcement-body">Body</label>
        <textarea id="announcement-body" name="announcement-body" cols="60" rows="8"><?= htmlspecialchars($announcement->body) ?></textarea><br>
                Note: You can use ThWboard Code in announcements.
    </div>
    <div>
        <label for="announcement-boardids">Boards</label>
        <select id="announcement-boardids" name="announcement-boardids[]" size="8" multiple>
<?php foreach ($boards as $board): ?>
            <option value="<?= htmlspecialchars($board->ID) ?>"<?= (stristr($announcement->boardIDs, ";".$board->ID.";") != false ? ' selected="selected"' : '') ?>><?= htmlspecialchars($board->name) ?></option>
<?php endforeach ?>
        </select>
    </div>
    <div>
        <input type="submit" name="submit" value="Save">
    </div>
</form>
<?php
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

/*
 * ########################################################################################
 * List announcements
 * ########################################################################################
 */
if ('list' == $action) {
    $r_announcement = query(
<<<SQL
SELECT
    newsid AS ID,
    newstopic AS title,
    newstext AS body,
    newstime AS ctime
FROM
    {$pref}news
ORDER BY
    newstime DESC
SQL
    );

    $announcements = [];
    while ($announcement = mysql_fetch_object($r_announcement)) {
        $announcements[] = $announcement;
    }
?>
<a href="announcements.php?action=new&amp;session=<?= htmlspecialchars($session) ?>">Add announcement</a>
<h3>Announcements</h3>
<ul id="announcements">
<?php foreach ($announcements as $announcement): ?>
    <li>
        <?= date("d.m.Y H:i: ", $announcement->ctime) ?>
        <?= htmlspecialchars($announcement->title) ?>
        <ul class="actions">
            <li><a href="announcements.php?action=edit&amp;id=<?= htmlspecialchars($announcement->ID) ?>&amp;session=<?= $session ?>" title="Edit announcement <?= htmlspecialchars($announcement->title) ?>">edit</a></li>
            <li><a href="announcements.php?action=delete&amp;id=<?= htmlspecialchars($announcement->ID) ?>&amp;session=<?= $session ?>" title="Delete announcement <?= htmlspecialchars($announcement->title) ?>">delete</a></li>
        </ul>
    </li>
<?php endforeach ?>
</ul>
<?php
}


/*
 * ########################################################################################
 * Edit an announcement
 * ########################################################################################
 */
if ('edit' == $action) {
    print "<a href=\"announcements.php?action=new&amp;session=" . $session . "\">Add announcement</a> ";
    print "<a href=\"announcements.php?session=" . $session . "\">List announcements</a>";
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
    boardid AS ID
FROM
    {$pref}board
WHERE
    boardid IN ({$boardIDs})
SQL
            );

            $boardIDs = [];
            while ($board = mysql_fetch_object($r_boards)) {
                $boardIDs[] = $board->ID;
            }

            if (empty($boardIDs)) {
                print "The announcement needs to visible in at least one board.";
            } else {
                $title = addslashes(EditboxDecode($_POST['announcement-title']));
                $body  = addslashes($_POST['announcement-body']);

                $boardIDs = ';' . implode(';', $boardIDs) . ';';

                query(
<<<SQL
UPDATE
    {$pref}news
SET
    newstext  = '{$body}',
    newstopic = '{$title}',
    boardid   = '{$boardIDs}'
WHERE
    newsid = {$_GET['id']}
SQL
                );

                print "Announcement saved.";
            }
        }
    } else {
        $r_announcement = query(
<<<SQL
SELECT
    newsid AS ID,
    newstopic AS title,
    newstext AS body,
    boardid AS boardIDs
FROM
    {$pref}news
WHERE
    newsid = {$_GET['id']}
SQL
        );
        $announcement = mysql_fetch_object($r_announcement);
        NewsForm('announcements.php?action=edit&id='.$announcement->ID.'&session='.$session, $announcement);
    }
}

/*
 * ########################################################################################
 * Add an announcement
 * ########################################################################################
 */
if ('new' == $action) {
    print "<a href=\"announcements.php?session=" . $session . "\">List announcements</a>";
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
    boardid AS ID
FROM
    {$pref}board
WHERE
    boardid IN ({$boardIDs})
SQL
            );

            $boardIDs = [];
            while ($board = mysql_fetch_object($r_boards)) {
                $boardIDs[] = $board->ID;
            }

            if (empty($boardIDs)) {
                print "The announcement needs to visible in at least one board.";
            } else {
                $title = addslashes($_POST['announcement-title']);
                $body  = addslashes($_POST['announcement-body']);

                $boardIDs = ';' . implode(';', $boardIDs) . ';';

                query(
<<<SQL
INSERT INTO
    {$pref}news
(
    newstopic,
    newstext,
    boardid,
    newstime
) VALUES (
    '{$title}',
    '{$body}',
    '{$boardIDs}',
    UNIX_TIMESTAMP()
)
SQL
                );

                print "Announcement saved.";
            }
        }
    } else {
        $announcement = (object)[
            'ID' => 0,
            'title' => '',
            'body' => '',
            'boardIDs' => ';'
        ];
        NewsForm('announcements.php?action=new&session='.$session, $announcement);
    }
}

/*
 * ########################################################################################
 * Delete an announcement
 * ########################################################################################
 */
if ('delete' == $action) {
    print "<a href=\"announcements.php?action=new&amp;session=" . $session . "\">Add announcement</a> ";
    print "<a href=\"announcements.php?session=" . $session . "\">List announcements</a>";
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
<form method="post" action="announcements.php?action=delete&amp;id=<?= htmlspecialchars($_GET['id']) ?>&amp;session=<?= htmlspecialchars($session) ?>">
    Do you really want to delete the announcement?
    <input type="submit" name="submit" value="Delete">
</form>
<?php
    }
}

tb_footer();
