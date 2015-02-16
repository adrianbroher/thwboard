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
    print '<form method="post" action="boards.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td>Forumname</td>
      <td>
        <input class="tbinput" type="text" name="board[boardname]" value="'.htmlspecialchars($board[boardname]).'">
      </td>
    </tr>
    <tr>
      <td>Description</td>
      <td>
        <input class="tbinput" type="text" name="board[boarddescription]" value="'.htmlspecialchars($board[boarddescription]).'">
      </td>
    </tr>
    <tr>
      <td>Category</td>
      <td>';
    listbox("board[categoryid]", "categoryid", "categoryname", "".$pref."category", $board['categoryid']);
    print '      </td>
    </tr>
    <tr>
      <td>Style</td>
      <td>';

    print '
<select class="tbinput" name="board[styleid]">
<option value="0">( Use default )</option>';
    $r_style = query("SELECT styleid, stylename FROM $pref"."style WHERE styleisdefault=0");
    while( $style = mysql_fetch_array($r_style) )
    {
        print '<option value="'.$style['styleid'].'">'.$style['stylename'].'</option>';
    }
print '</select>';

//    listbox("board[styleid]", "styleid", "stylename", "".$pref."style", $board['styleid'], '<option value="0">-- USE DEFAULT --</option>');
    print '      </td>
    </tr>
    <tr>
      <td><b>Status</b><br>
        <font size="1">Here you can deactivate<BR>this board temporarily</font></td>
      <td align="top"><SELECT class="tbinput" name="board[boarddisabled]"><option value="1" ' . ( $board[boarddisabled] == 1 ? "selected" : "" ) . '>Disable board</option><option value="0" ' . ( $board[boarddisabled] == 0 ? " selected" : "" ) . '>Enable board</option></SELECT></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="action" value="' . $action . '">
        <input type="hidden" name="update" value="1">
        <input type="hidden" name="board[boardid]" value="'.$board['boardid'].'">
        <input type="hidden" name="session" value="' . $session . '">
        <input type="submit" name="Send" value="Send">
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
  <table border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td colspan="2"><i>Display order</i></td>
      <td colspan="2" align="center"> <i>Options </i></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>';
    foreach ($categories as $category) {
        print '
    <tr>
      <td align="center">
        <input type="text" name="catord['.$category['categoryid'].']" size="2" value="'.$category['categoryorder'].'">
      </td>
      <td>&nbsp;</td>
      <td colspan="2"><a href="boards.php?action=RenameCategory&categoryid='.$category['categoryid'].'&session='.$session.'">edit</a>
        | <a href="boards.php?action=delcat&id='.$category['categoryid'].'&session='.$session.'">delete</a></td>
      <td>&nbsp;</td>
      <td><b>'.$category['categoryname'].'</b></td>
    </tr>
';
        foreach ($boards[$category['categoryid']] as $board) {
            print '
    <tr>
      <td>&nbsp;</td>
      <td align="center">
        <input type="text" name="boardord['.$board['boardid'].']" size="2" value="'.$board['boardorder'].'">
      </td>
      <td colspan="2"><a href="boards.php?action=edit&id='.$board['boardid'].'&oldboardorder='.$board['boardorder'].'&session='.$session.'">edit</a>
        | <a href="boards.php?action=delete&forumid='.$board['boardid'].'&session='.$session.'">delete</a>
        | <a href="groups.php?action=grouppermtable&boardid='.$board['boardid'].'&session='.$session.'">permissions</a></td>
      <td>&nbsp;</td>
      <td>'.$board['boardname'].'<br>
        <font size="1">'.$board['boarddescription'].'</font></td>
    </tr>';
        }
    }
    print '
  </table>
  <br>
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
 *        addcat
 * ########################################################################################
 */
elseif( $action == "addcat" ) {
  if( $catname ) {
    $result=query( "SELECT max(categoryorder) FROM ".$pref."category" );
    list($maxorder)=mysql_fetch_row($result);

    $maxorder++;
    $catname = fix_umlauts($catname);

    query( "INSERT INTO ".$pref."category (categoryname, categoryorder) VALUES ('" . addslashes($catname) . "', $maxorder);" );
    print "Category saved.";
  } else {
    print '<b>New Category</b><br>';
    print '<form method="post" action="boards.php">
  <label for="category-name">Name</label>
  <input id="category-name" class="tbinput" type="text" name="catname">
  <input type="hidden" name="action" value="addcat">
  <input type="hidden" name="session" value="' . $session . '">
  <input type="submit" name="Abschicken" value="Save">
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
elseif( $action == "newboard" )
{
    if( isset($Send) )
    {
        // add forum
        $result = query( "SELECT max(boardorder) FROM ".$pref."board WHERE categoryid=$board[categoryid]" );
        list($maxorder) = mysql_fetch_row($result);
        $maxorder++;
        $board['boardname'] = fix_umlauts($board['boardname']);
        $board['boarddescription'] = fix_umlauts($board['boarddescription']);
        query("INSERT INTO ".$pref."board (boardname, boarddescription, categoryid, boardorder, styleid,
            boarddisabled) VALUES (
            '" . addslashes($board[boardname]) . "', '" . addslashes($board[boarddescription]) . "',
            '$board[categoryid]', '$maxorder', '$board[styleid]',
            '$board[boarddisabled]')");

        print 'Forum has been added. Please verify board order.';
    }
    else
    {
        print '<b>Add Board</b><br><br>';
        BoardForm(array(), 'newboard');
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
 *        RenameCategory
 * ########################################################################################
 */
elseif( $action == "RenameCategory" )
{
    $r_category = query("SELECT categoryid, categoryname FROM ".$pref."category WHERE categoryid=$categoryid");
    $category = mysql_fetch_array($r_category);
    $category['categoryname'] = $category['categoryname'];
    print '<b>Rename Category</b><br><form method="post" action="boards.php">
  Rename to: <input class="tbinput" type="text" name="newname" value="' . $category[categoryname] . '">
  <input type="hidden" name="action" value="SetCategoryName">
  <input type="hidden" name="session" value="' . $session . '">
  <input type="hidden" name="categoryid" value="' . $category[categoryid] . '">
  <input type="submit" value="rename">
</form>';
}



/*
 * ########################################################################################
 *        SetCategoryName
 * ########################################################################################
 */
elseif( $action == "SetCategoryName" )
{
    query("UPDATE ".$pref."category SET categoryname='" . addslashes(fix_umlauts($newname)) . "' WHERE categoryid=$categoryid");

    print 'category has been renamed.';
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
