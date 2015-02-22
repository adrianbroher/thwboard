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

/**
 * replaces german umlauts with the according html entities.
 *
 * this is a cleaner way than just to replace every html
 * entity in board / category names and descriptions
 **/

function fix_umlauts($str)
{
    $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);

    foreach ($trans as $key => $value)
    {
        $trans[$key] = '&#'.ord($key).';';
    }

    strtr($str, $trans);

    return $str;
}

function BoardForm($board, $handler)
{
    global $pref;
    global $session;


    $r_style = query(
<<<SQL
SELECT
    styleid AS ID,
    stylename AS name
FROM
    {$pref}style
WHERE
    styleisdefault = 0
SQL
    );

    $styles = [(object)['ID' => 0, 'name' => '( Use default )']];
    while ($style = mysql_fetch_object($r_style)) {
        $styles[] = $style;
    }

    $r_category = query(
<<<SQL
SELECT
    categoryid AS ID,
    categoryname AS name
FROM
    {$pref}category
SQL
    );

    $categories = [];
    while ($category = mysql_fetch_object($r_category)) {
        $categories[] = $category;
    }

    print '<form method="post" action="'.htmlspecialchars($handler).'">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td><label for="board-name">Name</label></td>
      <td>
        <input class="tbinput" id="board-name" type="text" name="board-name" value="'.htmlspecialchars($board->name).'">
      </td>
    </tr>
    <tr>
      <td><label for="board-description">Description</label></td>
      <td>
        <input class="tbinput" id="board-description" type="text" name="board-description" value="'.htmlspecialchars($board->description).'">
      </td>
    </tr>
    <tr>
      <td><label for="board-categoryid">Category</label></td>
      <td>
        <select class="tbinput" id="board-categoryid" name="board-categoryid">';
    foreach ($categories as $category) {
        print '  <option value="'.$category->ID.'"'.($category->ID == $board->categoryID ? ' selected="selected"' : '').'>'.$category->name.'</option>';
    }
    print '</select>
      </td>
    </tr>
    <tr>
      <td><label for="board-styleid">Style</label></td>
      <td>';

    print '
<select class="tbinput" id="board-styleid" name="board-styleid">';
    foreach ($styles as $style) {
        print '<option value="'.$style->ID.'">'.$style->name.'</option>';
    }
print '</select>';

    print '      </td>
    </tr>
    <tr>
      <td><b><label for="board-disabled">Status</label></b><br>
        <font size="1">Here you can deactivate<BR>this board temporarily</font></td>
      <td align="top"><SELECT class="tbinput" id="board-disabled" name="board-disabled"><option value="1" ' . ( $board->disabled == 1 ? "selected" : "" ) . '>Disable board</option><option value="0" ' . ( $board->disabled == 0 ? " selected" : "" ) . '>Enable board</option></SELECT></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="submit" name="submit" value="Save">
      </td>
    </tr>
  </table>
<br><br><br>
Note: You can define the default style <a href="style.php?session=' . $session . '&action=ListStyles">here</a>.
</form>';
}


function CategoryForm($category, $handler)
{
    print '<form method="post" action="'.htmlspecialchars($handler).'">
  <label for="category-name">Name</label>
  <input id="category-name" class="tbinput" type="text" name="category-name" value="'.htmlspecialchars($category->name).'">
  <input type="submit" name="submit" value="Save">
</form>';
}

function NavigationBar($currentAction)
{
    global $session;

    $actions = [];

    if ('' != $currentAction) {
        $actions['List categories and boards'] = 'boards.php?session='.$session;
    }

    if ('category-new' != $currentAction) {
        $actions['Add category'] = 'boards.php?action=category-new&session='.$session;
    }

    if ('board-new' != $currentAction) {
        $actions['Add board'] = 'boards.php?action=board-new&session='.$session;
    }

    if (!empty($actions)) {
        echo '<ul class="actions">';

        foreach ($actions as $label => $uri) {
            echo '<li><a href="'.htmlspecialchars($uri).'">'.htmlspecialchars($label).'</a></li> ';
        }

        echo '</ul>';
    }
}


if ($_REQUEST['action'] == '') {
    $r_category = query(
<<<SQL
SELECT
    categoryid AS ID,
    categoryname AS name
FROM
    {$pref}category
ORDER BY
    categoryorder ASC
SQL
    );

    $categories = [];
    $boards = [];
    while ($category = mysql_fetch_object($r_category)) {
        $categories[] = $category;

        $boards[$category->ID] = [];

        $r_board = query(
<<<SQL
SELECT
    boardid AS ID,
    boardname AS name,
    boarddescription AS description
FROM
    {$pref}board
WHERE
    categoryid = {$category->ID}
ORDER BY
    boardorder ASC
SQL
        );

        while ($board = mysql_fetch_object($r_board)) {
            $boards[$category->ID][] = $board;
        }
    }

    NavigationBar($_REQUEST['action']);

    print '<b>Forum structure</b><br>
<ul id="board-order">';

    foreach ($categories as $key => $category) {
        print '
    <li>
        <div class="category">
            '.$category->name.'
            <ul class="actions">
                <li>
                    <form method="post" action="boards.php?action=category-reorder&amp;id='.$category->ID.'&amp;session='.$session.'">';

        if ($key !== reset(array_keys($categories))) {
            echo '<button type="submit" name="direction" title="Move category '.htmlspecialchars($category->name).' up" value="up">move up</button> ';
        }

        if ($key !== end(array_keys($categories))) {
            echo '<button type="submit" name="direction" title="Move category '.htmlspecialchars($category->name).' down" value="down">move down</button>';
        }

        print '
                    </form>
                </li>
                <li><a href="boards.php?action=category-edit&id='.$category->ID.'&session='.$session.'" title="Edit category '.htmlspecialchars($category->name).'">edit</a></li>
                <li><a href="boards.php?action=category-delete&id='.$category->ID.'&session='.$session.'" title="Delete category '.htmlspecialchars($category->name).'">delete</a></li>
            </ul>
        </div>
';

        if (!empty($boards[$category->ID])) {
            print '
        <ul>';

            foreach ($boards[$category->ID] as $key => $board) {
                print '
            <li class="board">
                <dl>
                    <dt>'.$board->name.'</dt>
                    <dd>'.$board->description.'</dd>
                </dl>
                <ul class="actions">
                    <li>
                        <form method="post" action="boards.php?action=board-reorder&amp;id='.$board->ID.'&amp;session='.$session.'">';

                if ($key !== reset(array_keys($boards[$category->ID]))) {
                    print '<button type="submit" name="direction" title="Move board '.htmlspecialchars($board->name).' up" value="up">move up</button> ';
                }

                if ($key !== end(array_keys($boards[$category->ID]))) {
                    print '<button type="submit" name="direction" title="Move board '.htmlspecialchars($board->name).' down" value="down">move down</button>';
                }

                print'
                        </form>
                    </li>
                    <li><a href="boards.php?action=board-edit&id='.$board->ID.'&session='.$session.'" title="Edit board '.htmlspecialchars($board->name).'">edit</a></li>
                    <li><a href="boards.php?action=board-delete&id='.$board->ID.'&session='.$session.'" title="Delete board '.htmlspecialchars($board->name).'">delete</a></li>
                    <li><a href="groups.php?action=grouppermtable&boardid='.$board->ID.'&session='.$session.'">permissions</a></li>
                </ul>
            </li>';
            }

            print '
        </ul>';
        }

        print '
    </li>';
    }

    print '
</ul>';
}


/*
 * ########################################################################################
 * Reorder the boards
 * ########################################################################################
 */
if ($_GET['action'] == 'board-reorder') {
    NavigationBar($_GET['action']);

    if (in_array($_POST['direction'], ['up', 'down'])) {
        $direction = ['up' => '<=', 'down' => '>='][$_POST['direction']];
        $ordering = ['up' => 'DESC', 'down' => 'ASC'][$_POST['direction']];

        $r_board = query(
<<<SQL
SELECT
    b.boardid AS ID
FROM
    {$pref}board AS b
JOIN
    {$pref}board AS bn
ON
    b.categoryid = bn.categoryid AND
    b.boardorder {$direction} bn.boardorder AND
    bn.boardid = {$_GET['id']}
ORDER BY
    b.boardorder ${ordering}
LIMIT
    2
SQL
        );

        $boards = [];
        while ($board = mysql_fetch_object($r_board)) {
            $boards[] = $board->ID;
        }

        if (2 == sizeof($boards)) {
            query(
<<<SQL
UPDATE
    {$pref}board AS l
JOIN
    {$pref}board AS r
ON (
    l.boardid = {$boards[0]} AND
    r.boardid = {$boards[1]}
) OR (
    l.boardid = {$boards[1]} AND
    r.boardid = {$boards[0]}
)
SET
    l.boardorder = r.boardorder,
    r.boardorder = l.boardorder
SQL
            );

            echo "Order updated.";
        }
    }
}


/*
 * ########################################################################################
 * Reorder the categories
 * ########################################################################################
 */
if ($_GET['action'] == 'category-reorder') {
    NavigationBar($_GET['action']);

    if (in_array($_POST['direction'], ['up', 'down'])) {
        $direction = ['up' => '<=', 'down' => '>='][$_POST['direction']];
        $ordering = ['up' => 'DESC', 'down' => 'ASC'][$_POST['direction']];

        $r_category = query(
<<<SQL
SELECT
    c.categoryid AS ID
FROM
    {$pref}category AS c
JOIN
    {$pref}category AS cn
ON
    c.categoryorder {$direction} cn.categoryorder AND
    cn.categoryid = {$_GET['id']}
ORDER BY
    c.categoryorder ${ordering}
LIMIT
    2
SQL
        );

        $categories = [];
        while ($category = mysql_fetch_object($r_category)) {
            $categories[] = $category->ID;
        }

        if (2 == sizeof($categories)) {
            query(
<<<SQL
UPDATE
    {$pref}category AS l
JOIN
    {$pref}category AS r
ON (
    l.categoryid = {$categories[0]} AND
    r.categoryid = {$categories[1]}
) OR (
    l.categoryid = {$categories[1]} AND
    r.categoryid = {$categories[0]}
)
SET
    l.categoryorder = r.categoryorder,
    r.categoryorder = l.categoryorder
SQL
            );

            echo "Order updated.";
        }
    }
}


/*
 * ########################################################################################
 * Edit a board
 * ########################################################################################
 */
if ($_GET['action'] == 'board-edit') {
    NavigationBar($_GET['action']);

    if (isset($_POST['submit'])) {
        if (empty($_POST['board-name'])) {
            print "The board name can't be empty.";
        } else {
            $r_board = query(
<<<SQL
SELECT
    categoryid AS categoryID,
    boardorder AS `order`
FROM
    {$pref}board
WHERE
    boardid = {$_GET['id']}
SQL
            );
            $oldboard = mysql_fetch_object($r_board);

            if ($oldboard->categoryID != $_POST['board-categoryid']) {
                $result = query(
<<<SQL
SELECT
    MAX(boardorder) AS maxorder
FROM
    {$pref}board
WHERE
    categoryid = {$_POST['board-categoryid']}
SQL
                );
                list($maxorder) = mysql_fetch_row($result);
                $maxorder++;
            } else {
                $maxorder = $oldboard->order;
            }

            $boardName = addslashes(fix_umlauts($_POST['board-name']));
            $boardDescription = addslashes(fix_umlauts($_POST['board-description']));

            $result = mysql_query(
<<<SQL
SELECT
    COUNT(boardid)
FROM
    {$pref}board
WHERE
    boardname = '{$boardName}' AND
    boardid <> {$_GET['id']}
SQL
            );

            if (mysql_result($result, 0) != 0) {
                print "The board already exists";
            } else {
                query(
<<<SQL
UPDATE
    {$pref}board
SET
    boardname = '{$boardName}',
    boarddescription = '{$boardDescription}',
    categoryid = {$_POST['board-categoryid']},
    boardorder = {$maxorder},
    styleid = {$_POST['board-styleid']},
    boarddisabled = {$_POST['board-disabled']}
WHERE
    boardid = {$_GET['id']}
SQL
                );

                echo "Board saved.";
            }
        }
    } else {
        $r_board = query(
<<<SQL
SELECT
    boardid AS ID,
    boardname AS name,
    boarddescription AS description,
    categoryid AS categoryID,
    styleid AS styleID,
    boarddisabled AS disabled
FROM
    {$pref}board
WHERE
    boardid = {$_GET['id']}
SQL
        );
        $board = mysql_fetch_object($r_board);

        print '<b>Edit Board</b><br><br>';
        BoardForm($board, 'boards.php?action=board-edit&id='.$board->ID.'&session='.$session);
    }
}


/*
 * ########################################################################################
 * Add a new category
 * ########################################################################################
 */
if ($_GET['action'] == 'category-new') {
    NavigationBar($_GET['action']);

    if (isset($_POST['submit'])) {
        if (empty($_POST['category-name'])) {
            print "The category name can't be empty";
        } else {
            $catname = addslashes(fix_umlauts($_POST['category-name']));

            $result = mysql_query(
<<<SQL
SELECT
    COUNT(categoryid)
FROM
    {$pref}category
WHERE
    categoryname = '$catname'
SQL
            );

            if (mysql_result($result, 0) != 0) {
                print "The category already exists";
            } else {
                query(
<<<SQL
INSERT INTO
    {$pref}category
(
    categoryname,
    categoryorder
)
SELECT
    '{$catname}',
    MAX(categoryorder) + 1
FROM
    {$pref}category
SQL
                );
                print "Category saved.";
            }
        }
    } else {
        $category = (object)[
            'ID' => 0,
            'name' => ''
        ];

        print '<b>New Category</b><br>';
        CategoryForm($category, 'boards.php?action=category-new&session='.$session);
    }
}


/*
 * ########################################################################################
 * Delete a board
 * ########################################################################################
 */
if ($_GET['action'] == 'board-delete') {
    NavigationBar($_GET['action']);

    print '<b>Delete board</b><br><br>';

    if (isset($_POST['submit'])) {
        // delete the board
        mysql_query(
<<<SQL
DELETE FROM
    {$pref}board
WHERE
    boardid = {$_GET['id']}
SQL
        );

        // delete messages
        $result = mysql_query(
<<<SQL
SELECT
    threadid
FROM
    {$pref}thread
WHERE
    boardid = {$_GET['id']}
SQL
        );

        while ($topic = mysql_fetch_array($result)) {
            mysql_query(
<<<SQL
DELETE FROM
    {$pref}post
WHERE
    threadid = {$topic['threadid']}
SQL
            );
        }

        // delete topics
        mysql_query(
<<<SQL
DELETE FROM
    {$pref}thread
WHERE
    boardid = {$_GET['id']}
SQL
        );

        // delete permission
        mysql_query(
<<<SQL
DELETE FROM
    {$pref}groupboard
WHERE
    boardid = {$_GET['id']}
SQL
        );

        // lastvisited
        mysql_query(
<<<SQL
DELETE FROM
    {$pref}lastvisited
WHERE
    boardid = {$_GET['id']}
SQL
        );

        print 'Board has been deleted.';
    } else {
        print '
<form method="post" action="boards.php?action=board-delete&amp;id='.$_GET['id'].'&amp;session='.$session.'">
  Do you really want to delete the board?
  <input type="submit" name="submit" value="Delete">
</form>';
    }
}


/*
 * ########################################################################################
 * Add a new board
 * ########################################################################################
 */
if ($_GET['action'] == 'board-new') {
    NavigationBar($_GET['action']);

    if (isset($_POST['submit'])) {
        if (empty($_POST['board-name'])) {
            print "The board name can't be empty.";
        } else {
            $boardName = addslashes(fix_umlauts($_POST['board-name']));
            $boardDescription = addslashes(fix_umlauts($_POST['board-description']));

            $result = mysql_query(
<<<SQL
SELECT
    COUNT(boardid)
FROM
    {$pref}board
WHERE
    boardname = '{$boardName}'
SQL
            );

            if (mysql_result($result, 0) != 0) {
                print "The board already exists";
            } else {
                query(
<<<SQL
INSERT INTO
    {$pref}board
(
    boardname,
    boarddescription,
    categoryid,
    styleid,
    boarddisabled,
    boardorder
)
SELECT
    '{$boardName}',
    '{$boardDescription}',
    {$_POST['board-categoryid']},
    {$_POST['board-styleid']},
    {$_POST['board-disabled']},
    MAX(boardorder) + 1
FROM
    {$pref}board
WHERE
    categoryid = {$_POST['board-categoryid']}
SQL
                );

                echo "Board saved.";
            }
        }
    } else {
        $board = (object)[
            'ID' => 0,
            'name' => '',
            'description' => '',
            'categoryID' => 0,
            'styleID' => 0,
            'disabled' => 0
        ];

        print '<b>New Board</b><br><br>';
        BoardForm($board, 'boards.php?action=board-new&session='.$session);
    }
}


/*
 * ########################################################################################
 * Delete a category
 * ########################################################################################
 */
if ($_GET['action'] == 'category-delete') {
    NavigationBar($_GET['action']);

    print '<b>Delete category</b><br><br>';

    $r_board = query(
<<<SQL
SELECT
    COUNT(boardid) AS boardcount
FROM
    {$pref}board
WHERE
    categoryid = {$_GET['id']}
SQL
    );
    $board = mysql_fetch_array($r_board);

    if ($board['boardcount'] > 0) {
        print "The category can't be deleted because it contains boards.";
    }

    if (isset($_POST['submit'])) {
        query(
<<<SQL
DELETE FROM
    {$pref}category
WHERE
    categoryid = {$_GET['id']}
SQL
        );
        print 'Category has been deleted.';
    } else {
        print '
<form method="post" action="boards.php?action=category-delete&amp;id='.$_GET['id'].'&amp;session='.$session.'">
  Do you really want to delete the category?
  <input type="submit" name="submit" value="Delete">
</form>';
    }
}


/*
 * ########################################################################################
 * Edit a category
 * ########################################################################################
 */
if ($_GET['action'] == 'category-edit') {
    NavigationBar($_GET['action']);

    if (isset($_POST['submit'])) {
        if (empty($_POST['category-name'])) {
            print "The category name can't be empty";
        } else {
            $categoryName = addslashes(fix_umlauts($_POST['category-name']));

            $result = mysql_query(
<<<SQL
SELECT
    COUNT(categoryid)
FROM
    {$pref}category
WHERE
    categoryname = '{$categoryName}'
SQL
            );

            if (mysql_result($result, 0) != 0) {
                print "The category already exists";
            } else {
                query(
<<<SQL
UPDATE
    {$pref}category
SET
    categoryname = '{$categoryName}'
WHERE
    categoryid = {$_GET['id']}
SQL
                );

                print "Category saved.";
            }
        }
    } else {
        $r_category = query(
<<<SQL
SELECT
    categoryid AS ID,
    categoryname AS name
FROM
    {$pref}category
WHERE
    categoryid = {$_GET['id']}
SQL
        );
        $category = mysql_fetch_object($r_category);

        print '<b>Edit Category</b><br>';
        CategoryForm($category, 'boards.php?action=category-edit&id='.$category->ID.'&session='.$session);
    }
}

tb_footer();
