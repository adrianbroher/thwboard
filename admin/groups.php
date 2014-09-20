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


/* permission constants */

define('P_VIEW', 0);
define('P_REPLY', 1);
define('P_POSTNEW', 2);
define('P_CLOSE', 3);
define('P_DELTHREAD', 4);
define('P_OMOVE', 5);
define('P_DELPOST', 6);
define('P_EDIT', 7);
define('P_OCLOSE', 8);
define('P_ODELTHREAD', 9);
define('P_ODELPOST', 10);
define('P_OEDIT', 11);
define('P_TOP', 12);
define('P_EDITCLOSED', 13);
define('P_IP', 14);
define('P_EDITTOPIC', 15);
define('P_NOFLOODPROT', 16);
define('P_NOEDITLIMIT', 17);
define('P_CANSEEINVIS', 18);
define('P_NOPMLIMIT', 19);
define('P_INTEAM', 20);
define('P_CEVENT', 21);
define('P_FORCEPM', 22);

/* and descriptions. ( REQUIRED! )*/
$p_desc = array(
    P_VIEW => 'Can view board?',
    P_REPLY => 'Can reply to threads?',
    P_POSTNEW => 'Can create new threads?',
    P_CLOSE => 'Can close <u>own</u> threads?',
    P_DELTHREAD => 'Can delete <u>own</u> threads?',
    P_DELPOST => 'Can delete <u>own</u> posts?',
    P_EDIT => 'Can edit <u>own</u> posts?',
    P_OMOVE => "Can move others' threads?",
    P_OCLOSE => "Can close others' threads?",
    P_ODELTHREAD => "Can delete others' threads?",
    P_ODELPOST => "Can delete others' posts?",
    P_OEDIT => "Can edit others' posts?",
    P_TOP => "Can make threads sticky",
    P_EDITCLOSED => "Can edit posts in closed threads?",
    P_IP => "Can view IP's?",
    P_EDITTOPIC => 'Can edit thread topics?',
    P_NOFLOODPROT => 'Flood protection override?',
    P_NOEDITLIMIT => 'No edit time limit?',
    P_CANSEEINVIS => 'Can see invisible users?',
    P_NOPMLIMIT => 'No private message limit?',
    P_INTEAM => 'Listed on the teampage?',
    P_CEVENT => 'Can add new calendar entries?',
    P_FORCEPM => 'Can send PMs if recipient\'s PM box is full?'
);

$p_globalonly = array(
    P_CANSEEINVIS => 1,
    P_NOPMLIMIT => 1,
    P_INTEAM => 1,
    P_CEVENT => 1,
    P_FORCEPM => 1
);

include "common.inc.php";

function flag_make_string($arr)
{
  $str = "";

  for($i = 0; $i < count($arr); $i++)
    {
      if(isset($arr[$i]))
    {
      $str .= $arr[$i];
    }
      else
    {
      $str .= 0;
    }
    }

  return $str;
}

function flag_make_array($str)
{
  $a = array();

  for($i = 0; $i < strlen($str); $i++)
    {
      $a[] = $str[$i];
    }

  return $a;
}

function check_flag($str, $flag)
{
  return (bool) $str[$flag];
}

function flag_or($str1, $str2)
{
  $str = "";

  for($i = 0; $i < max(strlen($str1), strlen($str2)); $i++)
    {
      if($str1[$i] || $str2[$i])
    {
      $str .= "1";
    }
      else
    {
      $str .= "0";
    }
    }

  return $str;
}

function grouplist_remove(&$list, $groupid)
{
    $a_groupid = explode(',', $list);
    $a_new = array();
    while( list(, $gid) = each($a_groupid) )
    {
        if( $gid != $groupid )
            $a_new[] = $gid;
    }

    $list = implode(',', $a_new);
}

function print_perms($accessmask, $color = '', $global = 0)
{
    global $p_desc, $p_globalonly;

    reset($p_desc);
    while( list($k, $v) = each($p_desc) )
    {
        if( isset($p_globalonly[$k]) && !$global )
        {
            print '<td align="center">-</td>';
        }
        else
        {
            if( check_flag($accessmask, $k) )
            {
                print '<td align="center"><font color="'.$color.'">Y</font></td>';
            }
            else
            {
                print '<td align="center"><font color="'.$color.'">N</font></td>';
            }
        }
    }
}

function group_form($group, $action)
{
    global $session, $p_desc;

    print '<form name="theform" method="post" action="groups.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td><b>Group name (non-public)</b></td>
      <td>
        <input class="tbinput" type="text" name="name" value="'.htmlspecialchars($group['name']).'">
      </td>
    </tr>
    <tr>
      <td><b>Group title</b></td>
      <td>
        <input class="tbinput" type="text" name="title" value="'.htmlspecialchars($group['title']).'">
      </td>
    </tr>
    <tr>
      <td><b>Group title priority</b><br><font size="1">For users who are in more than one group. The title (if set)<br> of the group with the highest priority will be displayed below<br> their name. Use numbers between 0 and 999.</font></td>
      <td>
        <input class="tbinput" type="text" size="4" name="titlepriority" value="'.htmlspecialchars($group['titlepriority']).'">
      </td>
    </tr>
    <tr>
      <td>Permissions:</td>
      <td></td>
    </tr>';

    $i = 0;
    while( list($k, $v) = each($p_desc) )
    {
        print '<tr'.($i % 2 == 0 ? '' : ' bgcolor="#E2E2E2"').'>
      <td>'.$v.'</td>
      <td>
        <input type="radio" name="permission['.$k.']" value="yes"'.(check_flag($group['accessmask'], $k) ? ' checked' : '' ).'>
        Yes&nbsp;&nbsp;&nbsp;
        <input type="radio" name="permission['.$k.']" value="no"'.(check_flag($group['accessmask'], $k) ? '' : ' checked' ).'>
        No
      </td>
    </tr>';
        $i++;
    }

    print '<tr>
      <td>&nbsp;</td>
      <td></td>
    </tr><tr>
      <td colspan="2" align="center">
        <input type="hidden" name="session" value="'.$session.'">
        <input type="hidden" name="groupid" value="'.$group['groupid'].'">
        <input type="hidden" name="action" value="'.$action.'">
        <input type="submit" name="submit" value="Send">
      </td>
    </tr>
  </table>
</form>';
}

function groupboard_form($group, $board, $useglobal)
{
    global $session, $p_desc, $p_globalonly;

    print '<form name="theform" method="post" action="groups.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td colspan="2">Edit permissions for group &quot;<b>'.$group['name'].'</b>&quot; and board &quot;<b>'.$board['boardname'].'</b>&quot;</td>
      <td>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="radio" name="useglobal" value="yes"'.($useglobal ? ' checked' : '').'>
        This board uses the groups global permission rules. (The settings below will be ignored)<br>
        <input type="radio" name="useglobal" value="no"'.($useglobal ? '' : ' checked').'>
        This board uses individual permissions for this group:</td>
      <td></td>
    </tr>';

    $i = 0;
    while( list($k, $v) = each($p_desc) )
    {
        if( !isset($p_globalonly[$k]) )
        {
            print '<tr'.($i % 2 == 0 ? '' : ' bgcolor="#E2E2E2"').'>
      <td>'.$v.'</td>
      <td>
        <input type="radio" name="permission['.$k.']" value="yes"'.(check_flag($group['accessmask'], $k) ? ' checked' : '' ).'>
        Yes&nbsp;&nbsp;&nbsp;
        <input type="radio" name="permission['.$k.']" value="no"'.(check_flag($group['accessmask'], $k) ? '' : ' checked' ).'>
        No
      </td>
    </tr>';
            $i++;
        }
    }

    print '<tr>
      <td>&nbsp;</td>
      <td></td>
    </tr><tr>
      <td colspan="2" align="center">
        <input type="hidden" name="session" value="'.$session.'">
        <input type="hidden" name="groupid" value="'.$group['groupid'].'">
        <input type="hidden" name="boardid" value="'.$board['boardid'].'">
        <input type="hidden" name="action" value="updategroupboard">
        <input type="submit" name="submit" value="Send">
      </td>
    </tr>
  </table>
</form>';
}


tb_header();


if( $action == 'list' )
{
    $r_group = query("SELECT groupid, name, accessmask, nodelete FROM $pref"."group ORDER BY name ASC");

    print '
<form name="form1" method="post" action="groups.php">
  <table border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td colspan="2"><b>Special groups</b></td>
    </tr>
    <tr>
      <td>Group for new users</td>
      <td>
        <select name="default_groupid">';

    while( $group = mysql_fetch_array($r_group) )
    {
        print '<option value="'.$group['groupid'].'"'.($config['default_groupid'] == $group['groupid'] ? ' selected' : '').'>'.$group['name'].'</option>';
    }
    print '
        </select>
      </td>
    </tr>
    <tr>
      <td>Guest group (users who are not logged in)</td>
      <td>
        <select name="guest_groupid">';
    mysql_data_seek($r_group, 0);
    while( $group = mysql_fetch_array($r_group) )
    {
        print '<option value="'.$group['groupid'].'"'.($config['guest_groupid'] == $group['groupid'] ? ' selected' : '').'>'.$group['name'].'</option>';
    }
    print '
        </select>
      </td>
    </tr>
    <tr align="right">
      <td colspan="2">
        <input type="hidden" name="session" value="'.$session.'">
        <input type="hidden" name="action" value="set_default_groups">
        <input type="submit" name="done" value="Done">
      </td>
    </tr>
    <tr>
      <td colspan="2"> </td>
    </tr>
  </table>
</form>';


    print '<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td><b>Groupname</b></td>
    <td><b>Members</b></td>
    <td><b>Options</b></td>
  </tr>';

    $i = 0;
    mysql_data_seek($r_group, 0);
    while( $group = mysql_fetch_array($r_group) )
    {
        if( $group['groupid'] == $config['guest_groupid'] )
        {
            print '<tr'.($i % 2 == 0 ? '' : ' bgcolor="#E2E2E2"').'>
    <td valign="top">'.$group['name'].'</td>
    <td valign="top"><font size="1">(users who are<br>not logged in)</font></td>
    <td>';

        }
        else
        {
            $r_user = query("SELECT count(userid) AS count FROM $pref"."user WHERE INSTR(groupids, ',$group[groupid],')>0");
            $user = mysql_fetch_array($r_user);

            print '<tr'.($i % 2 == 0 ? '' : ' bgcolor="#E2E2E2"').'>
    <td valign="top">'.$group['name'].'</td>
    <td valign="top">'.$user['count'].' member(s) - <a href="groups.php?action=listmembers&session='.$session.'&groupid='.$group['groupid'].'">list</a></td>
    <td>';
        }


    print '<font size="1">
<a href="groups.php?action=boardpermtable&groupid='.$group['groupid'].'&session='.$session.'">View/Edit permissions</a><br>
<a href="groups.php?action=delete&groupid='.$group['groupid'].'&session='.$session.'">Delete group</a><br></font> ';

    print '</td>
  </tr>';
          $i++;
    }
    print '</table><br><br>';

}
elseif( $action == 'listmembers' )
{
    $r_group = query("SELECT name FROM $pref"."group WHERE groupid=$groupid");
    $group = mysql_fetch_array($r_group);

    print 'Member listing for group "<b>'.$group['name'].'</b>"<br><br>';

    $r_user = query("SELECT username, userid FROM $pref"."user WHERE INSTR(groupids, ',$groupid,')>0 ORDER BY username ASC");
    if( mysql_num_rows($r_user) == 0 )
    {
        print 'This group has no members.';
    }
    else
    {
        while( $user = mysql_fetch_array($r_user) )
        {
            print htmlspecialchars($user['username']).' - <a href="useredit.php?action=EditUser&userid='.$user['userid'].'&session='.$session.'">edit</a><br>';
        }
    }
}
elseif( $action == 'create' )
{
    print '<b>Create new group</b><br><br>';
    group_form(array(), 'insert');
}
elseif( $action == 'insert' )
{
    if( !$name )
    {
        print 'Please enter a group name.';
    }
    else
    {
        $accessmask = 0;
        /* create accessmask */
        while( list($k, $v) = each($p_desc) )
        {
            if( $permission[$k] == 'yes' )
            {
                $accessmask = flag_or($accessmask, $k);
            }
        }

        query("INSERT INTO $pref"."group (name, accessmask, title, titlepriority) VALUES
            ('".addslashes($name)."', '".$accessmask."', '".addslashes($title)."', '".$titlepriority."');");
        print 'Group has been added!';
    }
}
elseif( $action == 'delete' )
{
    $r_group = query("SELECT nodelete, name FROM $pref"."group WHERE groupid='".$groupid."'");
    $group = mysql_fetch_array($r_group);

    /* WARNING: do NOT remove this check unless you know what youre doing .. */
    if( $groupid == $config['default_groupid'] || $groupid == $config['guest_groupid'] )
    {
        print 'Sorry, you cannot delete this group (Are you trying to delete the default or guest group?).';
    }
    else
    {
        print 'You are going to delete "'.$group['name'].'". Are you sure? (Group members will be removed from group)<br><br><a href="groups.php?session='.$session.'&action=drop&groupid='.$groupid.'">Yes</a>';
    }
}



elseif( $action == 'drop' )
{
    /* put members into the default group */
    /*query("UPDATE $pref"."user SET groupid=$config[default_groupid] WHERE groupid='$groupid'");*/
    $r_user =query("SELECT userid, groupids FROM $pref"."user WHERE INSTR(groupids, ',$groupid,')>0");
    while( $user = mysql_fetch_array($r_user) )
    {
        $user['groupids'] = substr($user['groupids'], 1, strlen($user['groupids']) - 2);
        grouplist_remove($user['groupids'], $groupid);
        $user['groupids'] = ','.$user['groupids'].',';
        query("UPDATE $pref"."user SET groupids='$user[groupids]' WHERE userid=$user[userid]");
    }

    /* delete the group. */
    query("DELETE FROM $pref"."group WHERE groupid='$groupid'");

    /* delete group/board*/
    query("DELETE FROM $pref"."groupboard WHERE groupid='$groupid'");

    print 'Group has been deleted!';
}



elseif( $action == 'edit' )
{
    $r_group = query("SELECT groupid, name, accessmask, title, titlepriority FROM $pref"."group WHERE groupid='".$groupid."'");
    $group = mysql_fetch_array($r_group);

    print '<b>Edit group</b><br><br>';
    group_form($group, 'update');
}



elseif( $action == 'editgroupboard' )
{
    $r_group = query("SELECT groupid, name, accessmask FROM $pref"."group WHERE groupid='$groupid'");
    $group = mysql_fetch_array($r_group);

    $r_board = query("SELECT boardid, boardname FROM $pref"."board WHERE boardid='$boardid'");
    $board = mysql_fetch_array($r_board);

    $r_groupboard = query("SELECT groupid, accessmask FROM $pref"."groupboard WHERE groupid='$groupid' AND boardid='$boardid'");
    if( mysql_num_rows($r_groupboard) > 0 )
    {
        $groupboard = mysql_fetch_array($r_groupboard);
        $group['accessmask'] = $groupboard['accessmask'];
        groupboard_form($groupboard, $board, false);
    }
    else
    {
        groupboard_form($group, $board, false);
    }
}


elseif( $action == 'updategroupboard' )
{
    if( $useglobal == 'yes' )
    {
        query("DELETE FROM $pref"."groupboard WHERE groupid='$groupid' AND boardid='$boardid'");
        print 'Settings have been saved.<br><br><a href="groups.php?action=boardpermtable&session='.$session.'&groupid='.$groupid.'">Back</a>';
    }
    else
    {
        // delete old perms, no matter whether they exist or not ..
        query("DELETE FROM $pref"."groupboard WHERE groupid='$groupid' AND boardid='$boardid'");

        /* create accessmask */

        $accessmask = str_repeat("0", count($permission));

        while( list($k, $v) = each($permission) )
        {
          $accessmask[$k] = (($v == "yes") ? "1" : "0");
        }

        // insert new
        query("INSERT INTO $pref"."groupboard (groupid, boardid, accessmask) VALUES ('$groupid', '$boardid', '$accessmask')");

        print 'Permissions have been saved.<br><br><a href="groups.php?session='.$session.'&boardid='.$boardid.'&groupid='.$groupid.'&action=boardpermtable">Back</a>';
    }
}


elseif( $action == 'update' )
{
    if( !$name )
    {
        print '<b>Error</b><br><br>Please enter a group name.';
    }
    else
    {
        /* create accessmask */

        $accessmask = str_repeat("0", count($permission));

        while( list($k, $v) = each($p_desc) )
        {
          $accessmask[$k] = (($permission[$k] == "yes") ? "1" : "0");
        }

        query("UPDATE $pref"."group SET name='".addslashes($name)."', accessmask='$accessmask',
            title='".addslashes($title)."', titlepriority='$titlepriority' WHERE groupid=$groupid");
        print 'Group has been updated!';
    }
}

elseif( $action == 'perms' )
{
    print '<b>Board permissions</b><br><br>';
}

elseif( $action == 'boardpermtable' )
{
    $i = 0;

    // select groups global perms
    $r_group = query("SELECT name, accessmask FROM $pref"."group WHERE groupid='$groupid'");
    $group = mysql_fetch_array($r_group);

    print 'Board/permission table for group: <b>'.$group['name'].'</b><br><br>';

    print '<br><font color="#000066">Blue</font> - Board uses global permission<br>
<font color="#990000">Red</font> - Board uses custom permission
<br><br>';

    // board-perm
    $r_groupboard = query("SELECT boardid, accessmask FROM $pref"."groupboard WHERE groupid='$groupid'");
    $a_groupboard = array();
    while( $groupboard = mysql_fetch_array($r_groupboard) )
    {
        $a_groupboard[$groupboard['boardid']] = $groupboard['accessmask'];
    }

    // boards
    $r_board = query("SELECT boardid, boardname, categoryid FROM $pref"."board ORDER BY boardorder ASC");
    $a_board = array();
    while( $board = mysql_fetch_array($r_board) )
    {
        $a_board[$board['categoryid']][] = $board;
    }

    print '<table width="100%" border="0" cellspacing="0" cellpadding="4">';

    // print header
    print '<tr>';
    print '<td></td>';
    while( list($k, $v) = each($p_desc) )
    {
        print '<td width="20" align="center"><img src="./images/pbar_'.($k).'.png"></td>';
    }
    print '</tr>';

    // global perms
    print '<tr>';
    print '<td><i>Global permissions - <a href="groups.php?action=edit&groupid='.$groupid.'&session='.$session.'">Modify</a></i></td>';
    print_perms($group['accessmask'], '#000000', 1);
    print '</tr>';

    // select categories
    $r_category = query("SELECT categoryid, categoryname FROM $pref"."category ORDER BY categoryorder ASC");
    while( $category = mysql_fetch_array($r_category) )
    {
        print '<tr bgcolor="#E1E1E1"><td colspan="'.(count($p_desc) + 1).'"><b>Category: '.htmlspecialchars($category['categoryname']).'</b></td></tr>';
        while( list(, $board) = @each($a_board[$category['categoryid']]) )
        {
            print '<tr><td>'.htmlspecialchars($board['boardname']).'<br><font size="1"><a href="groups.php?session='.$session.'&action=editgroupboard&boardid='.$board['boardid'].'&groupid='.$groupid.'">Modify permissions ...</a></font></td>';
            if( isset($a_groupboard[$board['boardid']]) )
            {
                // custom perms
                print_perms($a_groupboard[$board['boardid']], '#800000');
            }
            else
            {
                // global
                print_perms($group['accessmask'], '#000080');
            }
            print '</tr>';
        }
    }

    print '</table>';
}



elseif( $action == 'grouppermtable' )
{
    $r_board = query("SELECT boardname FROM $pref"."board WHERE boardid='$boardid'");
    list($boardname) = mysql_fetch_row($r_board);
    print 'Group/permission table for board: <b>'.$boardname.'</b><br><br>';

    print '<br><font color="#000066">Blue</font> - Board uses global permission<br>
<font color="#990000">Red</font> - Board uses custom permission
<br><br>';

    /* global perms */
    $a_group = array();
    $r_group = query("SELECT groupid, name, accessmask FROM $pref"."group");
    while( $group = mysql_fetch_array($r_group) )
    {
        $a_group[$group['groupid']] = $group;
    }

    /* custom */
    $a_groupboard = array();
    $r_groupboard = query("SELECT groupid, accessmask FROM $pref"."groupboard WHERE boardid='$boardid'");
    while( $groupboard = mysql_fetch_array($r_groupboard) )
    {
        $a_groupboard[$groupboard['groupid']] = $groupboard;
    }

    print '<table width="100%" border="0" cellspacing="0" cellpadding="4">';

    /* header */
    print '<tr><td></td>';
    while( list($k, $v) = each($p_desc) )
    {
        print '<td width="20" align="center"><img src="./images/pbar_'.($k).'.png"></td>';
    }
    print '</tr>';

    /* group rows */
    while( list(, $group) = each($a_group) )
    {
        print '<tr><td>'.htmlspecialchars($group['name']).'<br><font size="1"><a href="groups.php?session='.$session.'&action=editgroupboard&boardid='.$boardid.'&groupid='.$group['groupid'].'">Modify permissions ...</a></font></td>';
        if( isset($a_groupboard[$group['groupid']]) )
        {
            // custom perms
            print_perms($a_groupboard[$group['groupid']]['accessmask'], '#800000');
        }
        else
        {
            // global
            print_perms($group['accessmask'], '#000080');
        }
        print '</tr>';
    }

    print '</table>';
}



elseif( $action == 'set_default_groups' )
{
    query("UPDATE $pref"."registry SET keyvalue='$default_groupid' WHERE keyname='default_groupid'");
    query("UPDATE $pref"."registry SET keyvalue='$guest_groupid' WHERE keyname='guest_groupid'");
    print 'Default groups have been set.';
}


tb_footer();
