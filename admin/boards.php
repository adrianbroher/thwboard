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

function BoardForm($board, $action)
{
    global $pref;
    global $session;


    $r_style = query("SELECT styleid, stylename FROM $pref"."style WHERE styleisdefault=0");

    $styles = [];
    while ($style = mysql_fetch_array($r_style)) {
        $styles[] = $style;
    }

    $r_category = query("SELECT categoryid, categoryname FROM $pref"."category");

    $categories = [];
    while ($category = mysql_fetch_assoc($r_category)) {
        $categories[] = $category;
    }

    print '<form method="post" action="boards.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td><label for="board-name">Name</label></td>
      <td>
        <input class="tbinput" id="board-name" type="text" name="board[boardname]" value="'.htmlspecialchars($board[boardname]).'">
      </td>
    </tr>
    <tr>
      <td><label for="board-description">Description</label></td>
      <td>
        <input class="tbinput" id="board-description" type="text" name="board[boarddescription]" value="'.htmlspecialchars($board[boarddescription]).'">
      </td>
    </tr>
    <tr>
      <td><label for="board-categoryid">Category</label></td>
      <td>
        <select class="tbinput" id="board-categoryid" name="board[categoryid]">';
    foreach ($categories as $category) {
        print '  <option value="'.$category['categoryid'].'"'.($category['categoryid'] == $board['categoryid'] ? ' selected="selected"' : '').'>'.$category['categoryname'].'</option>';
    }
    print '</select>
      </td>
    </tr>
    <tr>
      <td><label for="board-styleid">Style</label></td>
      <td>';

    print '
<select class="tbinput" id="board-styleid" name="board[styleid]">
<option value="0">( Use default )</option>';
    foreach ($styles as $style) {
        print '<option value="'.$style['styleid'].'">'.$style['stylename'].'</option>';
    }
print '</select>';

    print '      </td>
    </tr>
    <tr>
      <td><b><label for="board-disabled">Status</label></b><br>
        <font size="1">Here you can deactivate<BR>this board temporarily</font></td>
      <td align="top"><SELECT class="tbinput" id="board-disabled" name="board[boarddisabled]"><option value="1" ' . ( $board[boarddisabled] == 1 ? "selected" : "" ) . '>Disable board</option><option value="0" ' . ( $board[boarddisabled] == 0 ? " selected" : "" ) . '>Enable board</option></SELECT></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="action" value="' . $action . '">
        <input type="hidden" name="update" value="1">
        <input type="hidden" name="board[boardid]" value="'.$board['boardid'].'">
        <input type="hidden" name="session" value="' . $session . '">
        <input type="submit" name="Send" value="Save">
      </td>
    </tr>
  </table>
<br><br><br>
Note: You can define the default style <a href="style.php?session=' . $session . '&action=ListStyles">here</a>.
</form>';
}

if( $action == '' )
{
    $r_category = query("SELECT categoryid, categoryname, categoryorder from ".$pref."category order by categoryorder asc");

    $categories = [];
    $boards = [];
    while ($category = mysql_fetch_array($r_category)) {
        $categories[] = $category;

        $boards[$category['categoryid']] = [];

        $r_board = query("SELECT boardid, boardname, boardthreads, boardposts, boardlastpost, boarddescription, boardorder FROM ".$pref."board where categoryid='$category[categoryid]' order by boardorder asc");

        while ($board = mysql_fetch_array($r_board)) {
            $boards[$category['categoryid']][] = $board;
        }
    }

    print '<b>Change board and category order</b><br>';
    print '
<form name="form1" method="post" action="boards.php">
    <ul id="board-order">';

    foreach ($categories as $category) {
        print '
        <li>
            <div class="category">
                '.$category['categoryname'].'
                <ul class="actions">
                    <li><input type="text" name="catord['.$category['categoryid'].']" size="2" value="'.$category['categoryorder'].'"></li>
                    <li><a href="boards.php?action=category-edit&id='.$category['categoryid'].'&session='.$session.'" title="Edit category '.htmlspecialchars($category['categoryname']).'">edit</a></li>
                    <li><a href="boards.php?action=delcat&id='.$category['categoryid'].'&session='.$session.'">delete</a></li>
                </ul>
        </div>
';

        if (!empty($boards[$category['categoryid']])) {
            print '
        <ul>';

            foreach ($boards[$category['categoryid']] as $board) {
                print '
                <li class="board">
                    <dl>
                        <dt>'.$board['boardname'].'</dt>
                        <dd>'.$board['boarddescription'].'</dd>
                    </dl>
                    <ul class="actions">
                        <li><input type="text" name="boardord['.$board['boardid'].']" size="2" value="'.$board['boardorder'].'"></li>
                        <li><a href="boards.php?action=edit&id='.$board['boardid'].'&oldboardorder='.$board['boardorder'].'&session='.$session.'">edit</a></li>
                        <li><a href="boards.php?action=delete&forumid='.$board['boardid'].'&session='.$session.'">delete</a></li>
                        <li><a href="groups.php?action=grouppermtable&boardid='.$board['boardid'].'&session='.$session.'">permissions</a></li>
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
    </ul>
  <input type="hidden" name="session" value="'.$session.'">
  <input type="hidden" name="action" value="updateorder">
  <input type="submit" name="ehnet" value="Update board order">
</form>';

}


/*
 * ########################################################################################
 *        updateorder
 * ########################################################################################
 */
elseif( $action=="updateorder" ) {

  while( list($boardid, $boardorder)=each($boardord) ) {
    intval($boardorder) && query("UPDATE ".$pref."board SET boardorder=".intval($boardorder)." WHERE boardid=".intval($boardid));
  }

  while( list($categoryid, $categoryorder)=each($catord) ) {
    intval($categoryorder) && query("UPDATE ".$pref."category SET categoryorder=".intval($categoryorder)." WHERE categoryid=".intval($categoryid));
  }

  echo "Order has been updated!";

}


/*
 * ########################################################################################
 *        edit
 * ########################################################################################
 */
elseif( $action == "edit" ) {
    if( $Send )
    {
        $r_board = query("SELECT categoryid, boardorder FROM ".$pref."board WHERE boardid=$board[boardid]");
        $oldboard = mysql_fetch_array($r_board);

        if( $oldboard['categoryid'] != $board['categoryid'] )
        {
            $result = query( "SELECT max(boardorder) FROM ".$pref."board WHERE categoryid=$board[boardid]" );
            list($maxorder) = mysql_fetch_row($result);
            $maxorder++;
        }
        else
        {
            $maxorder = $oldboard['boardorder'];
        }
        $board['boardname'] = fix_umlauts($board['boardname']);
        $board['boarddescription'] = fix_umlauts($board['boarddescription']);

        query("UPDATE ".$pref."board SET boardname='". addslashes($board['boardname']) . "',
        boarddescription='" . addslashes($board['boarddescription']) . "', categoryid='$board[categoryid]',
        boardorder='$maxorder', styleid='$board[styleid]',
        boarddisabled = '$board[boarddisabled]'
         WHERE boardid=$board[boardid]");

        echo "Board has been updated!";
    }
    else
    {
        $r_board = query("SELECT boardid, boardname, boardlastpost, boardthreads, boardposts, boarddescription,
        categoryid, styleid, boarddisabled FROM ".$pref."board WHERE boardid=$id");
        $board = mysql_fetch_array($r_board);
        $board['boardname'] = $board['boardname'];
        $board['boarddescription'] = $board['boarddescription'];
        BoardForm($board, 'edit');

    }
}


/*
 * ########################################################################################
 * Add a new category
 * ########################################################################################
 */
if ($_GET['action'] == 'category-new') {
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
        print '<b>New Category</b><br>';
        print '<form method="post" action="boards.php?action=category-new">
  <label for="category-name">Name</label>
  <input id="category-name" class="tbinput" type="text" name="category-name">
  <input type="hidden" name="session" value="' . $session . '">
  <input type="submit" name="submit" value="Save">
</form>';
    }
}


/*
 * ########################################################################################
 *           delete
 * ########################################################################################
 */
elseif( $action == "delete" ) {
  if( $confirm == 1 ) {

    // delete the board
    mysql_query("DELETE FROM ".$pref."board WHERE boardid=$forumid");

    // delete messages
    $result=mysql_query("SELECT threadid FROM ".$pref."thread WHERE boardid=$forumid");
    while( $topic=mysql_fetch_array($result) ) {
      mysql_query("DELETE FROM ".$pref."post WHERE threadid=$topic[threadid]");
    }

    // delete topics
    mysql_query("DELETE FROM ".$pref."thread WHERE boardid=$forumid");

    // delete permission
    mysql_query("DELETE FROM ".$pref."groupboard WHERE boardid=$forumid");

    // lastvisited
    mysql_query("DELETE FROM $pref"."lastvisited WHERE boardid=$forumid");

    echo "Board has been deleted!<br>";

  } else {
    print '<font color=red><b>WARNING: You are going to DELETE a board!</b></font><br><br>';
    print "click <a href=\"boards.php?action=delete&forumid=$forumid&confirm=1&session=$session\">here</a> to confirm";
  }
}


/*
 * ########################################################################################
 *        newboard
 * ########################################################################################
 */
if ($_REQUEST['action'] == "newboard") {
    if (isset($_POST['Send'])) {
        if (empty($_POST['board']['boardname'])) {
            print "The board name can't be empty.";
        } else {
            $board['boardname'] = addslashes(fix_umlauts($_POST['board']['boardname']));
            $board['boarddescription'] = addslashes(fix_umlauts($_POST['board']['boarddescription']));


            $result = mysql_query(
<<<SQL
SELECT
    COUNT(boardid)
FROM
    {$pref}board
WHERE
    boardname = '{$board['boardname']}'
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
    '{$board['boardname']}',
    '{$board['boarddescription']}',
    {$_POST['board']['categoryid']},
    {$_POST['board']['styleid']},
    {$_POST['board']['boarddisabled']},
    MAX(boardorder) + 1
FROM
    {$pref}board
WHERE
    categoryid = {$_POST['board']['categoryid']}
SQL
                );

                echo "Board saved.";
            }
        }
    } else {
        print '<b>New Board</b><br><br>';
        BoardForm([], 'newboard');
    }
}


/*
 * ########################################################################################
 *        delcat
 * ########################################################################################
 */
elseif( $action == "delcat" ) {
    $r_board = query("SELECT count(boardid) AS boardcount FROM ".$pref."board WHERE categoryid=$id");
    $board = mysql_fetch_array($r_board);

    if( $board[boardcount] > 0 )
    {
        print 'Error: Cannot delete a category which contains boards!';
    }
    else
    {
        query("DELETE FROM ".$pref."category WHERE categoryid=$id");
        print 'Category has been deleted.';
    }
}



/*
 * ########################################################################################
 * Edit a category
 * ########################################################################################
 */
if ($_GET['action'] == 'category-edit') {
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
    categoryid,
    categoryname
FROM
    {$pref}category
WHERE
    categoryid = {$_GET['id']}
SQL
        );
        $category = mysql_fetch_array($r_category);

        print '<b>Edit Category</b><br>
<form method="post" action="boards.php?action=category-edit&amp;id='.htmlspecialchars($_GET['id']).'">
  <label for="category-name">Name</label>
  <input class="tbinput" id="category-name" type="text" name="category-name" value="' . $category['categoryname'] . '">
  <input type="hidden" name="session" value="' . $session . '">
  <input type="submit" name="submit" value="Save">
</form>';
    }
}

/*
 * ########################################################################################
 *        EditBoardUsers
 * ########################################################################################
 */
elseif( $action == "EditBoardUsers" )
{
    $r_board = query("SELECT boardname FROM ".$pref."board WHERE boardid=$boardid");
    $board = mysql_fetch_array($r_board);

    print '<b>Userlist for "' . $board['boardname'] . '"</b><br><br>';

    $r_boardaccess = query("SELECT boardaccess.userid, user.username FROM ".$pref."boardaccess AS boardaccess LEFT JOIN ".$pref."user AS user ON boardaccess.userid=user.userid WHERE boardaccess.boardid=$boardid");
    while( $boardaccess = mysql_fetch_array($r_boardaccess) )
    {
        print $boardaccess['username'] . ' [ <a href="boards.php?action=RemoveUserFromBoard&userid=' . $boardaccess['userid'] . '&boardid=' . $boardid . '&session=' . $session . '">remove</a> ]<br>';
    }

    print '<form name="theform" method="post" action="boards.php">
  User to add:
  <input type="text" name="username">
  <input type="hidden" name="action" value="AddUserToBoard">
  <input type="hidden" name="boardid" value="' . $boardid . '">
  <input type="hidden" name="session" value="' . $session . '">
  <input type="submit" name="Submit" value="Add User">
</form>';

}




/*
 * ########################################################################################
 *        RemoveUserFromBoard
 * ########################################################################################
 */
elseif( $action == 'RemoveUserFromBoard' )
{
    query("DELETE FROM ".$pref."boardaccess WHERE userid=$userid AND boardid=$boardid");

    if( mysql_affected_rows() == 1 )
    {
        print 'user has been removed from board access list';
    }
    else
    {
        print 'error';
    }

    print '<br><br><a href="boards.php?action=EditBoardUsers&session=' . $session . '&boardid=' . $boardid . '">back</a> to board userlist.';
}



/*
 * ########################################################################################
 *        AddUserToBoard
 * ########################################################################################
 */
elseif( $action == 'AddUserToBoard' )
{
    $r_user = query("SELECT userid FROM ".$pref."user WHERE username='$username'");
    $user = mysql_fetch_array($r_user);

    query("INSERT INTO ".$pref."boardaccess (boardid, userid) VALUES ($boardid, $user[userid])");
    print 'user has been added';

    print '<br><br><a href="boards.php?action=EditBoardUsers&session=' . $session . '&boardid=' . $boardid . '">back</a> to board userlist.';
}











tb_footer();
