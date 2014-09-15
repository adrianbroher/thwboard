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

if( $action == "bans" )
{
    print '<b>User bans</b><br><br>';
    
    if( !isset($addban) )
    {
        print '
<a href="users.php?session='.$session.'&action=show_banlist">Display current banlist</a>
<form name="form2" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr> 
      <td colspan="2"><i>Set ban</i></td>
    </tr>
    <tr> 
      <td><b>Username</b></td>
      <td> 
        <input class="tbinput" type="text" name="username">
      </td>
    </tr>
    <tr> 
      <td valign="top"><b>Ban type</b></td>
      <td> 
        <input type="radio" name="type" value="perm">
        Permanent ban<br>
        <input type="radio" name="type" value="timebased" checked>
        Timebased ban: Expires in 
        <input class="tbinput" type="text" name="duration" size="8" maxlength="8">
        <select class="tbinput" name="duration_multiplier">
          <option value="days">Days</option>
          <option value="hours">Hours</option>
          <option value="minutes">Minutes</option>
        </select>
      </td>
    </tr>
    <tr> 
      <td valign="top"><b>Public reason</b></td>
      <td> 
        <textarea  name="pubreason" rows="5" cols="50"></textarea>
      </td>
    </tr>
    <tr> 
      <td valign="top"><b>Admin notes/reason</b><br><font size="1">(This message is visible to admins only)</font></td>
      <td> 
        <textarea name="reason" rows="5" cols="50"></textarea>
      </td>
    </tr>
    <tr align="center"> 
      <td colspan="2"> 
        <input type="hidden" name="action" value="bans">
        <input type="hidden" name="addban" value="1">
        <input type="hidden" name="session" value="' . $session . '">
        <input type="submit" name="Abschicken" value="Set ban">
      </td>
    </tr>
  </table>
</form>';
    }

    if( isset($addban) )
    {
           $r_user = mysql_query("SELECT userid, userbanned FROM ".$pref."user where username='" . addslashes($username) . "'");
           $user = mysql_fetch_array($r_user);

           if( !$user['userid'] )
           {
               print '<font color=red>user not found!</font><br><br>';
           }
        elseif( $user['userbanned'] == 1 )
        {
            print 'Error: This user is already banned';
        }
        else
        {
            query("UPDATE ".$pref."user SET userbanned=1 WHERE userid='$user[userid]'");

            if( $type == 'timebased' )
            {
                $exp = $duration;
                switch( $duration_multiplier )
                {
                    case 'days':
                        $exp *= 60 * 60 * 24;
                        break;
                    case 'hours':
                        $exp *= 60 * 60;
                        break;
                    case 'minutes':
                        $exp *= 60;
                        break;
                }
                $exp += time();
            }
            else
            {
                $exp = 0;
            }

            query("INSERT INTO ".$pref."ban (userid, banpubreason, banreason, banexpire) VALUES
                ($user[userid], '".addslashes($pubreason)."', '".addslashes($reason)."', $exp)");
            
            print 'User has been banned.';
        }
    }

}




elseif( $action == 'show_banlist' )
{
    print '<b>Banlist</b><br><br>';

    $result = query( "SELECT user.username, user.userid, ban.banreason, ban.banpubreason, ban.banexpire, ban.bansetbyid
    FROM ".$pref."user AS user, ".$pref."ban AS ban WHERE user.userbanned=1 AND user.userid=ban.userid ORDER BY ban.banexpire ASC, user.username DESC");
   if( mysql_num_rows( $result ) < 1 )
   {
       print 'There are no bans.';
   }
   else
   {
           print '<table width="100%" border="0" cellspacing="0" cellpadding="3">';
        print '
  <tr>
    <td><b>Username</b></td>
    <td><b>Expires</b></td>
    <td><b>Public Reason</b></td>
    <td><b>Reason</b></td>
    <td><b>Options</b></td>
  </tr>';
        while( $banlist = mysql_fetch_array( $result ) )
        {
            if( $banlist['banexpire'] == 0 )
            {
                $banlist['banexpire'] = '<font color="darkblue">(never)</font>';
            }
            else
            {
                $banlist['banexpire'] = date("d-m-Y, H:i", $banlist['banexpire']);
            }
            if( strlen($banlist['banpubreason']) > 20 )
            {
                $banlist['banpubreason'] = substr($banlist['banpubreason'], 0, 20) . '...';
            }
            if( strlen($banlist['banreason']) > 20 )
            {
                $banlist['banreason'] = substr($banlist['banreason'], 0, 20) . '...';
            }
            print '
  <tr>
    <td><nobr>' . htmlspecialchars($banlist['username']) . '</nobr></td>
    <td><nobr>' . $banlist['banexpire'] . '</nobr></td>
    <td><font size="1">' . $banlist['banpubreason'] . '</font></td>
    <td><font size="1">' . $banlist['banreason'] . '</font></td>
    <td><a href="users.php?action=delban&userid=' . $banlist['userid'] . '&session=' .$session. '">unban</a> |
      <a href="users.php?action=show_reasons&userid='.$banlist['userid'].'&session='.$session.'">show reason(s)</a></td>
  </tr>';
        }
        print '</table><br><br>';
    }
}




elseif( $action == 'delban' )
{
    query("UPDATE ".$pref."user SET userbanned=0 WHERE userid=$userid" );
    query("DELETE FROM ".$pref."ban WHERE userid=$userid");
    
    print '<b>Unbanned</b><br><br>User has been unbanned';
}




elseif( $action == 'show_reasons' )
{
    print '<b>Ban reasons</b><br><br>';
    
    $r_ban = query("SELECT banreason, banpubreason FROM $pref"."ban WHERE userid=$userid");
    $ban = mysql_fetch_array($r_ban);
    
    print '<i>Public reason:</i><br>'.$ban['banpubreason'];
    print '<i><br><br><br><br>Private reason:</i><br>'.$ban['banreason'];
}

tb_footer();
