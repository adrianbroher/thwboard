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





/* user search stuff */
define('FLD_INT', 1);
define('FLD_STR', 2);
define('FLD_BOOL', 4);
define('FLD_PASSWD', 8);
define('FLD_TEXT', 16);

$a_searchfield = array(
    array('userid', 'User ID', FLD_INT),
    array('username', 'Username', FLD_STR),
    array('useremail', 'Email address', FLD_STR),
    array('usersignature', 'Signature', FLD_STR),
    array('userposts', 'Postcount', FLD_INT),
    array('userjoin', 'Registration date', FLD_DATE),
    array('userlastpost', 'Last post', FLD_DATE),
    array('useractivate', 'Not activated?', FLD_BOOL)
);

$a_editfield = array(
    'username' => array('Username', '', FLD_STR),
    'userpassword' => array('Password', '(Current password is hidden. Do not specify<br> a password unless you want to change it.)', FLD_PASSWD),
    'useremail' => array('User email address', '', FLD_STR),
    'userposts' => array('Postcount', '', FLD_INT),
    'usertitle' => array('Custom title', 'Overrides any ranks', FLD_STR),
    'userhomepage' => array('Homepage', '', FLD_STR),
    'userlocation' => array('Location', '', FLD_STR),
    'usericq' => array('ICQ #', '', FLD_INT),
    'useraim' => array('AIM Name', '', FLD_STR),
    'usermsn' => array('MSN Name', '', FLD_STR),
    'useroccupation' => array('Occupation', '', FLD_STR),
    'useravatar' => array('Avatar', 'Type in "<b>notallowed</b>" to forbid this user to use avatars.', FLD_STR),
    'userinterests' => array('Interests', '', FLD_TEXT),
    'usersignature' => array('Signature', '', FLD_TEXT),
    /* opts */
    'userinvisible' => array('Invisible?', '', FLD_BOOL),
    'userhidesig' => array('Hide signatures?', '', FLD_BOOL),
    'userhideemail' => array('Hide own email?', '', FLD_BOOL),
    'usernoding' => array('Disable "new PM" popup?', '', FLD_BOOL),
    'useractivate' => array('Needs activation?', '', FLD_BOOL)
);



function delete_user($userid, $username)
{
    global $pref, $config;

    query("DELETE FROM ".$pref."ban WHERE userid=$userid");
    query("DELETE FROM ".$pref."online WHERE userid=$userid");
    query("DELETE FROM ".$pref."pm WHERE pmtoid=$userid OR pmfromid=$userid");
    query("DELETE FROM ".$pref."session WHERE userid=$userid");
    query("DELETE FROM ".$pref."user WHERE userid=$userid");
    query("DELETE FROM $pref"."lastvisited WHERE userid=$userid");
    query("UPDATE ".$pref."post SET postguestname='$config[guestprefix]" . addslashes($username) . "', userid=0 WHERE userid=$userid");
}



function in_group($groupids, $groupid)
{
    $groupids = substr($groupids, 1, strlen($groupids) - 2);
    $a_groupid = explode(',', $groupids);
    while( list(, $gid) = each($a_groupid) )
    {
        if( $gid == $groupid )
            return 1;
    }
    return 0;
}


/*
 * ########################################################################################
 * Add an user
 * ########################################################################################
 */
if ('AddUser' == $_REQUEST['action']) {
    if ($_POST['username']) {
        if (!$_POST['username'] || !$_POST['userpassword']) {
            print "Please specify username, email and password.";
        } else {
            $userName = addslashes($_POST['username']);

            $r_user = query(
<<<SQL
SELECT
    userid
FROM
    {$pref}user
WHERE
    username = '{$userName}'
SQL
            );

            if (mysql_num_rows($r_user) > 0) {
                print "Sorry, this username already exists.";
            } else {
                if ($_POST['userpassword'] != $_POST['userpassword2']) {
                    print "Sorry, the passwords do not match.";
                } else {
                    $userEmail = addslashes($_POST['useremail']);
                    $userPassword = addslashes($_POST['userpassword']);

                    query(
<<<SQL
INSERT INTO
    {$pref}user
(
    username,
    useremail,
    userpassword,
    userjoin,
    groupids
) VALUES (
    '{$userName}',
    '{$userEmail}',
    MD5('{$userPassword}'),
    UNIX_TIMESTAMP(),
    ',{$config['default_groupid']},'
)
SQL
                    );

                    print "User saved.";
                }
            }
        }
    } else {
        print '<b>New user</b><br>
<br>
<form class="entity-form" name="user" method="post" action="./useredit.php">
    <div>
        <label for="user-name">Name</label>
        <input id="user-name" type="text" name="username">
    </div>
    <div>
        <label for="user-email">Email</label>
        <input id="user-email" type="text" name="useremail">
    </div>
    <div>
        <label for="user-password">Password</label>
        <input id="user-password" type="password" name="userpassword">
    </div>
    <div>
        <label for="user-password-verify">Verify password</label>
        <input id="user-password-verify" type="password" name="userpassword2">
    </div>
    <div>
        <input type="hidden" name="action" value="AddUser">
        <input type="hidden" name="session" value="' . $session . '">
        <input type="submit" name="submit" value="Save">
    </div>
</form>';
    }
}




/*
 * ==========================================================
 *              DeleteUser
 * ==========================================================
 */
elseif( $action == "DeleteUser" )
{
    if( $username )
    {
        $r_user = query("SELECT userid, usernodelete FROM ".$pref."user WHERE username='" . addslashes($username) . "'");
        if( mysql_num_rows($r_user) < 1 )
        {
            print 'Sorry, user not found.';
        }
        else
        {
            $user = mysql_fetch_array($r_user);

            if( $user['usernodelete'] == 1 )
            {
                print 'Sorry, you cannot delete this user (this user is a god admin).';
            }
            else
            {
                delete_user($user['userid'], $username);

                print 'User deleted.';
            }
        }
    }
    else
    {
        print '<b>Delete user</b><br>
<br>
<form name="theform" method="post" action="./useredit.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td>Username</td>
      <td>
        <input class="tbinput" type="text" name="username">
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="action" value="DeleteUser">
        <input type="hidden" name="session" value="' . $session . '">
        <input type="submit" name="submit" value="Delete User &gt;&gt;">
      </td>
    </tr>
  </table>
</form>';
    }
}



/*
 * ==========================================================
 *              Filter
 * ==========================================================
 */
elseif( $action == "Filter" )
{
    print '
<form name="form1" method="post" action="useredit.php">
  <b>Advanced usersearch</b><br><br>
  Note: Leave all fields blank to list all users (not recommended)<br>
  <br>
  <table border="0" width="700" cellspacing="1" cellpadding="3">
    <tr>
      <td><i>Searchfield</i></td>
      <td><i>Search type</i></td>
      <td><i>Value</i></td>
    </tr>';

    while( list(, $field) = each($a_searchfield) )
    {
        print '
    <tr>
      <td><b>'.$field[1].'</b></td>
      <td>';

          if( $field[2] == FLD_INT )
        {
            print '
        <select class="tbinput" name="searchtype['.$field[0].']">
          <option value="below">below</option>
          <option value="equal">equal</option>
          <option value="above">above</option>
        </select>';
        }
        elseif( $field[2] == FLD_STR )
        {
            print '
        <select class="tbinput" name="searchtype['.$field[0].']">
          <option value="contains">contains</option>
          <option value="exactly">exactly</option>
        </select>';
        }
        elseif( $field[2] == FLD_BOOL )
        {
          print '<input class="tbinput" name="searchtype['.$field[0].']" type="hidden" value="1">';
        }
        else
        {
            print '
        <select class="tbinput" name="searchtype['.$field[0].']">
          <option value="before">before</option>
          <option value="after">after</option>
        </select>';
        }

        print '
      </td>
      <td>';

        if( $field[2] == FLD_INT )
        {
            print '
        <input class="tbinput" type="text" name="searchvalue['.$field[0].']" size="8" maxlength="8">';
        }
        elseif( $field[2] == FLD_STR )
        {
            print '
        <input class="tbinput" type="text" name="searchvalue['.$field[0].']">';
        }
        elseif( $field[2] == FLD_BOOL )
        {
            print '<input class="tbinput" name="searchvalue['.$field[0].']" type="checkbox" value="exactly">';
        }
        else
        {
            print '
        <input class="tbinput" type="text" name="searchvalue['.$field[0].']"> <i>(dd.mm.yyyy)</i>';
        }

        print '
      </td>
    </tr>
';
    }

print '
    <tr>
      <td colspan="3">
        &nbsp;
      </td>
    </tr>
    <tr>
      <td>
        <b>Action</b>
      </td>
      <td colspan="2">
        <select class="tbinput" name="subaction">
          <option value="list">List matching users</option>
          <option value="count">Count users</option>
        </select>
      </td>
    </tr>
    <tr align="center">
      <td colspan="3">
        <input type="hidden" name="advanced" value="1">
        <input type="hidden" name="action" value="ListUsers">
        <input type="hidden" name="session" value="'.$session.'">
        <input type="submit" name="Abschicken" value="Search">
      </td>
    </tr>
  </table>
</form>
';
}




/*
 * ==========================================================
 *              ListUsers
 * ==========================================================
 */
elseif( $action == "ListUsers" )
{
    $where = 'WHERE 1';

    while( list($field, $value) = each($searchvalue) )
    {
        if( !empty($value) )
        {
            $where .= ' AND '.$field;
            $value = addslashes($value);
            switch($searchtype[$field])
            {
                case 'below':
                    $where .= " < '$value'";
                    break;
                case 'equal':
                case 'exactly':
                    $where .= " = '$value'";
                    break;
                case 'above':
                    $where .= " > '$value'";
                    break;
                case 'contains':
                    $where .= " LIKE '%$value%'";
                    break;
                case 'before':
                case 'after':
                    if( strlen($value) != 10 )
                    {
                        print '<font color="red">Warning: bad date string "'.$value.'" (use dd.mm.yyyy!)</font><br><br>';
                    }
                    $day = (int)(substr($value, 0, 2));
                    $mon = (int)(substr($value, 3, 2));
                    $year = (int)(substr($value, 6, 4));

                    $timestamp = mktime(0, 0, 0, $mon, $day, $year);
                    if( $searchtype[$field] == 'before' )
                        $where .= " < $timestamp";
                    else
                        $where .= " > $timestamp";
            } // switch
        } // if
    } // while

    if( $subaction == 'list' )
    {
        $r_user = query("SELECT username, userid, useremail FROM ".$pref."user $where");

        print "<b>Search result</b><br><br>";
        if( mysql_num_rows($r_user) < 1 )
        {
            print 'Sorry, no user(s) found.';
        }
        else
        {
            print '
<form class="entity-form" name="form1" method="post" action="useredit.php">
  <table id="users" summary="List of forum users" width="600" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td>&nbsp;</td>
      <td><i>User ID</i></td>
      <td><i>Username</i></td>
      <td><i>Email address</i></td>
      <td><i>Options</i></td>
    </tr>';

            $i = 0;
            while( $user = mysql_fetch_array($r_user) )
            {
                print '
    <tr bgcolor="'.($i % 2 == 0 ? '#E5E5E5' : '#F2F2F2').'">
      <td>
        <input type="checkbox" name="a_userid[]" value="'.$user['userid'].'">
      </td>
      <td><b>'.$user['userid'].'</b></td>
      <td>'.htmlspecialchars($user['username']).'</td>
      <td>'.$user['useremail'].'</td>
      <td><a href="useredit.php?action=EditUser&amp;userid='.$user['userid'].'&amp;session='.$session.'">edit</a> |
          <a href="useredit.php?action=DeleteUser&amp;username='.$user['username'].'&amp;session='.$session.'">delete</a></td>
    </tr>';
                $i++;
            }

            print '
  </table>
  <input type="hidden" name="session" value="'.$session.'">
    <fieldset>
        <legend>Delete selected users</legend>
        <div>
            <input id="sure" type="checkbox" name="sure" style="width:auto; display:inline" value="1">
            <label for="sure" style="display:inline">I want to delete the selected users</label>
        </div>
        <div>
            <input type="submit" name="mass_delete" value="Delete">
        </div>
    </fieldset>
    <fieldset>
        <legend>Send private message to selected users</legend>
        <div>
            <label for="pmtopic">Subject</label>
            <input id="pmtopic" type="text" name="pmtopic">
        </div>
        <div>
            <label for="pmtext">Text</label>
            <textarea id="pmtext" name="pmtext" rows="5" cols="50"></textarea>
        </div>
        <div>
            <input type="submit" name="mass_pm" value="Send">
        </div>
    </fieldset>
</form>';
        }
    }
    elseif( $subaction == 'count' )
    {
        $r_user = query("SELECT COUNT(userid) AS usercount FROM ".$pref."user $where");
        $user = mysql_fetch_array($r_user);

        print '<b>Search result</b><br><br>'.$user['usercount'].' user(s)';
    }
}



/*
 * ==========================================================
 *              mass_delete
 * ==========================================================
 */
elseif( isset($mass_delete) )
{
    if( !isset($sure) )
    {
        print '<b>Error</b><br><br>Please check the "sure" box';
    }
    else
    {
        if( !empty($a_userid) )
        {
            $deleted = 0;
            print '<b>Delete user</b><br><br>';
            $r_user = query("SELECT username, userid, usernodelete FROM $pref"."user WHERE userid IN(".implode(',', $a_userid).")");
            while( $user = mysql_fetch_array($r_user) )
            {
              if($user['usernodelete'])
                {
                  print $user['username']. '(id #'.$user['userid'].') is a god admin, skipping...<br>';
                  continue;
                }
                print 'Deleting '.$user['username'].' (id #'.$user['userid'].')<br>';
                delete_user($user['userid'], $user['username']);
                $deleted++;
            }

            print '<br>Deleted <b>'.$deleted.'</b> users.';
        }
        else
        {
            print '<b>Error</b><br><br>No users selected';
        }
    }
}





/*
 * ==========================================================
 *              mass_pm
 * ==========================================================
 */
elseif( isset($mass_pm) )
{
    if( !empty($a_userid) )
    {
        if( !$pmtopic || !$pmtext )
        {
            print '<b>Error</b><br><br>Both fields (subject, text) are required';
        }
        else
        {
            $sent = 0;
            while( list(, $userid) = each($a_userid) )
            {
                query("INSERT INTO $pref"."pm (pmfromid, pmtoid, pmtopic, pmtext, pmtime, pmflags, pmfolder)
VALUES
($g_user[userid], $userid,'" . addslashes($pmtopic) . "','" . addslashes($pmtext) . "',".time().", 1, 0);");
                $sent++;
            }
            print '<b>Message sent</b><br><br>Private message has been sent to <b>'.$sent.'</b> users';
        }
    }
    else
    {
        print '<b>Error</b><br><br>No users selected';
    }
}




/*
 * ==========================================================
 *              move_into_group
 * ==========================================================
 */
elseif( isset($move_into_group) )
{
    if( !empty($a_userid) )
    {
        /* select users */

    }
    else
    {
        print '<b>Error</b><br><br>No users selected';
    }
}




/*
 * ==========================================================
 *              UpdateUser
 * ==========================================================
 */
elseif( $action == "UpdateUser" )
{
    $r_user = query("SELECT username FROM ".$pref."user WHERE userid=$userid");
    $tuser = mysql_fetch_array($r_user);

    if( substr($user['userhomepage'], 0, 7) != "http://" )
    {
        $user['userhomepage'] = "http://" . $user['userhomepage'];
    }

    $query = '';
    while( list($k, $v) = each($user) )
    {
        if( $a_editfield[$k][2] != FLD_PASSWD )
        {
            $v = addslashes($v);
            $query .= ", $k='$v'";
        }
        else
        {
            if( $v )
            {
                $v = md5($v);
                $query .= ", $k='$v'";
            }
        }
    }
    $query = substr($query, 1);

    if( $user['username'] != $tuser['username'] )
    {
        $user['username'] = addslashes($user['username']);
        $tuser['username'] = addslashes($tuser['username']);

        query("UPDATE ".$pref."thread SET threadlastreplyby='$user[username]' WHERE threadlastreplyby='$tuser[username]'");
        query("UPDATE ".$pref."board SET boardlastpostby='$user[username]' WHERE boardlastpostby='$tuser[username]'");

        query("UPDATE $pref"."thread SET threadauthor='$user[username]' WHERE threadauthor='$tuser[username]'");
    }

    if( !empty($groupids) )
        $groupids = ','.implode(',', $groupids).',';
    else
        $groupids = ',,';

    $query .= ", groupids='$groupids'";

    query("UPDATE ".$pref."user SET $query WHERE userid=$userid");

    print 'User has been updated!';
}




/*
 * ==========================================================
 *              EditUser
 * ==========================================================
 */
elseif( $action == "EditUser" )
{
    $r_user = query("SELECT * FROM ".$pref."user WHERE userid=$userid");
    $user = mysql_fetch_array($r_user);


    print '
<form name="form1" method="post" action="useredit.php">
  <b>Edit user</b><br>
  <br>
  <table border="0" cellspacing="1" cellpadding="3">';

    /* static */
    if( $user['usernodelete'] == 0 || $user['userisadmin'] == 0 )
    {
        print '
    <tr>
      <td><b>User status</b></td>
      <td>
        <select class="tbinput" name="user[userisadmin]">
          <option value="0"'.($user['userisadmin'] == 0 ? ' selected' : '').'>Regular</option>
          <option style="color: darkblue" value="1"'.($user['userisadmin'] == 1 ? ' selected' : '').'>Administrator</option>
        </select>
      </td>
    </tr>';
    }
    else
    {
        print '
    <tr>
      <td><b>User status</b></td>
      <td>
        <font color="#990000">Cannot be changed (User is god admin).</font>
        <input type="hidden" name="user[userisadmin]" value="1">
      </td>
    </tr>';
    }

    print '
    <tr>
      <td valign="top"><b>User group</b><br><font size="1">Use CTRL to select multiple</font></td>
      <td>
        <select class="tbinput" name="groupids[]" size="5" multiple>';

        $r_group = query("SELECT name, groupid FROM $pref"."group ORDER BY groupid ASC");
        while( $group = mysql_fetch_array($r_group) )
        {
            print '<option value="'.$group['groupid'].'"'.(in_group($user['groupids'], $group['groupid']) ? ' selected' : '').'>'.$group['name'].'</option>';
        }
    print '
        </select>';

    /* dyn */
    while( list($k, $field) = each($a_editfield) )
    {
        print '
    <tr>
      <td valign="top"><b>'.$field[0].'</b>'.($field[1] != '' ? '<br><font size="1">'.$field[1].'</font>' : '').'</td>
      <td valign="middle">';
        switch( $field[2] )
        {
            case FLD_TEXT:
                print '<textarea name="user['.$k.']" rows="5" cols="50">'.htmlspecialchars($user[$k]).'</textarea>';
                break;
            case FLD_STR:
                print '<input class="tbinput" type="text" name="user['.$k.']" value="'.htmlspecialchars($user[$k]).'">';
                break;
            case FLD_PASSWD:
                print '<input class="tbinput" type="password" name="user['.$k.']" value="">';
                break;
            case FLD_INT:
                print '<input class="tbinput" type="text" name="user['.$k.']" value="'.htmlspecialchars($user[$k]).'" size="8" maxlen="8">';
                break;
            case FLD_BOOL:
                print '<input type="radio" name="user['.$k.']" value="1"'.($user[$k] ? ' checked' : '').'>Yes&nbsp;&nbsp;&nbsp;<input type="radio" name="user['.$k.']" value="0"'.(!$user[$k] ? ' checked' : '').'>No';
                break;
        }
        print '
      </td>
    </tr>';
    }
/*
*/

    print '
    <tr>
      <td colspan="2" align="center">
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="hidden" value="UpdateUser" name="action">
        <input type="hidden" name="userid" value="'.$userid.'">
        <input type="hidden" name="session" value="'.$session.'">
        <input type="submit" name="Abschicken" value="Update user">
      </td>
    </tr>
  </table>
</form>';
}


tb_footer();
