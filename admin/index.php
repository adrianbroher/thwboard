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

$t = 1;

if( $action == "logout" )
{
    query("DELETE FROM ${pref}session WHERE userid='".$g_user['userid']."'");
    header("Location: index.php");
   exit;
}

tb_header();

/*
 * ==========================================
 *              <root>
 * ==========================================
 */
if( $action == "" )
{
    print '<b>ThWboard - Administrative Center</b><br><br>';

    print 'Welcome to the administrative center of your ThWboard.<br>Please keep in mind that thwboard is still <u>beta software</u> and may sometimes not work as expected.<br>';

    print '<br><br><br>Visit the <a href="http://www.thwboard.de">ThWboard-Page</a> for new versions and updates.<br>
        ThWboard is copyright 2004 by the ThWboard Development Group<br><br>';

    print '<font style="color: #008;">ThWboard version information</font><font size="1"><br>';
    print 'Your version: <b>'.$config['version'].'</b>';
    print '</font><br>';

    print '
<font style="color: #008;">System version information</font><font size="1"><br>
PHP: <b>'.phpversion().'</b><br>
MySQL: <b>'.mysql_get_server_info().'</b><br>
</font><br>
';

    $loadavg = @file('/proc/loadavg');
    if( $loadavg )
    {
        $load = explode(' ', $loadavg[0]);
        print '
<font style="color: #008;">Average system load</font><font size="1"><br>
Last minute: <b>'.$load[0].'</b><br>
Last 5 minutes: <b>'.$load[1].'</b><br>
Last 15 minutes: <b>'.$load[2].'</b><br>
</font>
';
    }

    print '<br><br><table summary="The 50 most recent actions done in the administrator interface" width="100%" border="0" cellspacing="0" cellpadding="3">
  <caption>Last 50 Admin-actions</caption>
  <tr>
    <td><b>Date/Time</b></td>
    <td><b>Logtype</b></td>
    <td><b>Username</b></td>
    <td><b>User IP</b></td>
    <td><b>Scriptname</b></td>
    <td><b>Action</b></td>
    <td><b>Misc Notes</b></td>
  </tr>';
    $result = query( "SELECT logtype, logtime, loguser, logip, logaction, logscript, lognotes FROM ".$pref."adminlog ORDER BY logtime DESC limit 50" );
    while( $adminlog = mysql_fetch_array($result) )
    {
        print "<tr>
        <td>".date('d.m.Y - H:i', $adminlog[logtime])."</td>
        <td>$adminlog[logtype]</td>
        <td>$adminlog[loguser]</td>
        <td>$adminlog[logip]</td>
        <td>$adminlog[logscript]</td>
        <td>$adminlog[logaction]</td>
        <td>$adminlog[lognotes]</td>
      </tr>";
    }
    print '</table>';
}


/*
 * ==========================================
 *              Settings
 * ==========================================
 */
elseif( $action == "EditSettings" )
{


    print '<b>Forum Settings</b><br><br>';
    print '
<form method="post" action="index.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="4">';

    // key/val
    $a_registry = array();
    $r_registry = query("SELECT keyname, keyvalue, keytype, keydescription, keydetails, keygroupid,
        keydisplayorder
        FROM $pref"."registry ORDER BY keydisplayorder ASC");
    //print(mysql_num_rows($r_registry));
    while( $registry = mysql_fetch_array($r_registry) )
    {
        $a_registry[$registry['keygroupid']][] = $registry;
    }

    // groups
    $i = 0;
    $r_registrygroup = query("SELECT keygroupname, keygroupid, keygroupdisplayorder FROM $pref"."registrygroup
        ORDER BY keygroupdisplayorder ASC");
    //print(mysql_num_rows($r_registrygroup));
    while( $registrygroup = mysql_fetch_array($r_registrygroup) )
    {
        print '
    <tr>
      <td colspan="2" bgcolor="#999999">
        <font size="2" color="white"><b>'.$registrygroup['keygroupname'].'</b></font>
      </td>
    </tr>';

        while( list(, $registry) = @each($a_registry[$registrygroup['keygroupid']]) )
        {
            if( $registry['keygroupid'] == 0 ) // 0 -> hide
                continue;

            print '
    <tr>
      <td'.($i % 2 == 0 ? ' bgcolor="#eeeeee"' : '').' valign="top" width="50%"><b>'.$registry['keydescription'].'</b>';
              if( $registry['keydetails'] )
            {
                print '<font size="1"><br>'.$registry['keydetails'].'</font>';
            }
            print '</td>';

            print'
      <td'.($i % 2 == 0 ? ' bgcolor="#eeeeee"' : '').' valign="top" width="50%">';

            switch( $registry['keytype'] )
            {
                case 'boolean':
                    print '
<input type="radio" name="Xconfig['.$registry['keyname'].']" value="1"' . ( $registry['keyvalue'] ? " checked" : "" ) . '>
Yes
<input type="radio" name="Xconfig['.$registry['keyname'].']" value="0"' . ( !$registry['keyvalue'] ? " checked" : "" ) . '>
No';
                    break;

                case 'integer':
                    print '<input class="tbinput" type="text" size="6" name="Xconfig['.$registry['keyname'].']" value="'.intval($registry['keyvalue']).'">';
                    break;

                case 'array':
                    print '<textarea class="tbinput" cols="60" rows="8" name="Xconfig['.$registry['keyname'].']">'.htmlspecialchars($registry['keyvalue']).'</textarea>';
                    break;


                case 'string':
                    print '<input type="text" class="tbinput" name="Xconfig['.$registry['keyname'].']" value="'.htmlspecialchars($registry['keyvalue']).'">';
                    break;
            }

            print '
      </td>
    </tr>';
            $i++;
        }
    }

    print '</table>
    <br>
    <center>
    <input type="hidden" name="session" value="'.$session.'">
    <input type="hidden" name="action" value="WriteSettings">
    <input type="submit" name="update_settings" value="Save settings">
    </center>
    </form>';
}



/*
 * ==========================================
 *              WriteSettings
 * ==========================================
 */
elseif( $action == "WriteSettings" )
{
    //var_dump($Xconfig);
    while( list($key, $value) = each($Xconfig) )
    {
        query("UPDATE $pref"."registry SET keyvalue='".addslashes($value)."' WHERE keyname='".$key."'");
    }
    print '<b>Settings saved</b><br>
    <br>
    Settings have been saved.';
}


tb_footer();
