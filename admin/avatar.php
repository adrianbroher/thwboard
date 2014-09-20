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

echo("<b>Avatars</b><br><br>\n");

if( $config['useravatar'] == 3 || $config['useravatar'] == 1 )
{
    if ( ($action == "" ) or ( $action == "ListAvatars" ) or !$action )
    {
        $a_navigation = "<a href=\"avatar.php?session=$session&action=NewAvatar\">Add new Avatar</a>&nbsp;&nbsp;&nbsp;
        <a href=\"avatar.php?session=$session&action=DeleteAll\">Delete all Avatars</a>&nbsp;&nbsp;&nbsp;
        <a href=\"avatar.php?session=$session&action=ListImportAvatars\">Import Avatars</a><br><br>";

        $avatarindex = true;
        echo '<b>==> Avatar overview</b><br><br>'.$a_navigation;
        $avatar_search = mysql_query("SELECT avatarid, avatarname, avatarurl FROM ".$pref."avatar");

        echo "<table border=0 cellspacing=0 width=\"100%\">\n";
        echo "<tr>\n";
        echo " <td><font face=\"Arial\" size=2 width=\"15%\"><b>ID</b></font></td>\n";
        echo " <td><font face=\"Arial\" size=2><b>Name</b></font></td>\n";
        echo " <td><font face=\"Arial\" size=2><b>Image</b></font></td>\n";
        echo " <td><font face=\"Arial\" size=2><b>Edit</b></font></td>\n";
        echo " <td><font face=\"Arial\" size=2><b>Delete</b></font></td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
        for ($i = 0; $i <= 4; $i++)
        {
            echo "<td></td>\n";
        }
        echo "</tr>\n";

        $e = '';
        while ( $avatar_data = mysql_fetch_array($avatar_search) )
        {
            if ( substr($avatar_data['avatarurl'], 0, 4) != "http")
            {
                $avatar_data['avatarurl'] = "../".$avatar_data['avatarurl'];
            }

            echo "<tr>\n";
            echo "<td".($e % 2 == 0 ? ' bgcolor="#eeeeee"' : '')."><b>$avatar_data[avatarid].</b></td><td"
            .($e % 2 == 0 ? ' bgcolor="#eeeeee"' : '').">$avatar_data[avatarname]</td><td"
            .($e % 2 == 0 ? ' bgcolor="#eeeeee"' : '')."><img src=\"$avatar_data[avatarurl]\" border=\"0\"></td><td"
            .($e % 2 == 0 ? ' bgcolor="#eeeeee"' : '')."><a href=\"avatar.php?session=$session&action=EditAvatar&id=$avatar_data[avatarid]\">Edit</a></td><td"
            .($e % 2 == 0 ? ' bgcolor="#eeeeee"' : '')."><a href=\"avatar.php?session=$session&action=DeleteAvatar&id=$avatar_data[avatarid]&avaname=$avatar_data[avatarname]\">Delete</a></td>";
            echo "</tr>\n";
            $e++;
        }
        echo "</table><br>\n";

        if( !$e )
        {
            echo "<center>No Avatars available!</center><br>\n";
        }

        echo $a_navigation;
    }
    elseif ( $action == "DeleteAvatar" )
    {
        if($sure == "yes")
        {
            $delete_avatar = mysql_query('DELETE FROM '.$pref.'avatar WHERE avatarid = '.$id.'');
            echo('<font color="#00C600"><b>The Avatar has been deleted!</b></font>');
        }
        else
        {
            echo('<b>==> Sure?</b><br><br>');
            echo('<form action="avatar.php" method=post>
            <input type="hidden" name="session" value="'.$session.'">
            <input type="hidden" name="action" value="DeleteAvatar">
            <input type="hidden" name="id" value="'.$id.'">
            <input type="hidden" name="sure" value="yes">
            <font color="#FF0000"><b><u>Warning</u></b>, if you continue the Avatar <b>"'.$avaname.'"</b> will be deleted!</font><br><br>
            <input type="submit" value="Delete!">');
        }
    }
    elseif ( $action == "NewAvatar" )
    {
        echo('<b>==> Add a new Avatar</b><br><br>');
        echo('<form action="avatar.php" method="post">
        <input type=hidden name=session value="'.$session.'">
        <input type=hidden name=action value="AddAvatars">
        <table width=100% border=0 cellspacing=0 cellspadding=0>
        <tr><td>Name : </td><td><input type="text" name="new_name" size=40 value=""> (Avatar1)</td></tr>
        <tr><td>URL : </td><td><input type="text" name="new_url" size=40 value=""> (http://www.avatars.com/avatar.gif, type in always the <font color="#FF0000">full</font> path!)</td></tr>
        <tr><td>&nbsp;</td><td><input type="submit" value="Save Settings"></td></tr>
        </table>');
    }
    elseif ( $action == "AddAvatars" )
    {
        $insert_avatar = mysql_query('INSERT INTO '.$pref.'avatar (avatarname, avatarurl) VALUES ("'.$new_name.'","'.$new_url.'") ');
        echo('<font color="#00C600"><b>Avatar has been added!</b></font>');
    }
    elseif ( $action == "EditAvatar" )
    {
        $avatar_search = mysql_query("SELECT avatarid, avatarname, avatarurl FROM ".$pref."avatar WHERE avatarid = $id");
        $avatar_data = mysql_fetch_array($avatar_search);
        echo('<b>==> Edit Avatar</b><br><br>');
        echo('<form action="avatar.php" method="post">
        <input type="hidden" name="session" value="'.$session.'">
        <input type="hidden" name="action" value="SaveAvatar">
        <input type="hidden" name="id" value="'.$id.'">
        <table width="100%" border="0" cellspacing="0" cellspadding="0">
        <tr><td width="150">ID :    </td><td><input type="text" name="new_id" size="4" value="'.$avatar_data[avatarid].'"></td></tr>
        <tr><td>Name :     </td><td><input type="text" name="new_name" size="40" value="'.$avatar_data[avatarname].'"></td></tr>
        <tr><td>URL :    </td><td><input type="text" name="new_url" size="40" value="'.$avatar_data[avatarurl].'"></td></tr>
        <tr><td>&nbsp;</td><td><input type="submit" value="Save Settings"></td></tr>
        </table>');
    }
    elseif ( $action == "SaveAvatar" )
    {
        $update_link = mysql_query('UPDATE '.$pref.'avatar SET avatarid = "'.$new_id.'", avatarname = "'.$new_name.'", avatarurl = "'.$new_url.'" WHERE avatarid = '.$id.'');
        echo('<font color="#00C600"><b>Avatar has been edited!</b></font><br>');
        echo('Click <A HREF="avatar.php?action=EditAvatar&id='.$id.'&session='.$session.'">here</a> to continue.');
    }
    elseif ( $action == "DeleteAll" )
    {
        if ( $sure == "yes" )
        {
            $delete_avatar = mysql_query("DELETE FROM ".$pref."avatar");
            echo('<font color="#FF7F00"><b>All Avatars have been deleted!</b></font>');
        }
        else
        {
            echo('<b>==> Sure?</b><br><br>');
            echo('<form action="avatar.php" method="post">
            <input type=hidden name=session value="'.$session.'">
            <input type=hidden name=action value="DeleteAll">
            <input type=hidden name=sure value="yes">
            <font color="#FF0000"><b><u>Warning</u></b>, if you continue <b>all</b> Avatars will be deleted!</font><br><br>
            <input type=submit value="Delete!">');
        }
    }
    elseif ($action == "ListImportAvatars")
    {
        echo('<b>==> Import Avatars</b><br><br>');
        echo "These are all Avatar-images located in the \"<i>avatar/</i>\" directory:<br><br>";
        $andy = opendir('../avatar/');
        $e = 0;
        echo "<form method=\"post\" action=\"avatar.php\">\n";
        echo "<table border=\"0\" cellspacing=\"0\" width=\"100%\">";
        echo "<tr>";
        echo " <td><font face=\"Arial\" size=\"2\"><b>Import Avatar?</b></font></td>";
        echo " <td><font face=\"Arial\" size=\"2\"><b>Filename</b></font></td>";
        echo " <td><font face=\"Arial\" size=\"2\"><b>Image</b></font></td>";
        echo "</tr>";
        echo "<tr>";
        for ($i = 0; $i <= 2; $i++)
            echo "<td height=\"5\"></td>";
        echo "</tr>";
        while( $filename = readdir($andy) )
        {
            if( ( $filename != "noavatar.gif" ) && ( substr($filename, -4) == '.gif') || ( substr($filename, -4) == '.jpg' ) || ( substr($filename, -4)== '.png' ) || ( substr($filename, -4) == '.GIF' ) || ( substr($filename, -4) == '.JPG' ) || ( substr($filename, -4) == '.PNG' ) )
            {
                $q_avatar = mysql_query('SELECT avatarurl FROM '.$pref.'avatar WHERE avatarurl=\'avatar/'.$filename.'\'');
                if( mysql_fetch_array($q_avatar) )
                {
                    $checked = '';
                    $notchecked = ' checked';
                }
                else
                {
                    $checked = ' checked';
                    $notchecked = '';
                }

                echo "<tr>";
                echo "<td".($e % 2 == 0 ? ' bgcolor="#eeeeee"' : '')."><input type=\"radio\" name=\"avabild[$filename]\" value=\"1\"$checked>Yes &nbsp;&nbsp;<input type=\"radio\" name=\"avabild[$filename]\" value=\"0\"$notchecked> No".($notchecked ? '&nbsp;&nbsp;&nbsp;&nbsp;<font size="1"><b>Already added!</b></font>' : '' )."</td>\n<td".($e % 2 == 0 ? ' bgcolor="#eeeeee"' : '').">$filename</td><td".($e % 2 == 0 ? ' bgcolor="#eeeeee"' : '')."><IMG SRC=\"../avatar/$filename\" border=\"0\"></td>";
                echo "</tr>";
                $e++;
            }
        }
        echo "</table>";
        if ( !$e )
        {
            echo "<center>No Avatars available!</center>";
        }
        else
        {
            echo "<br><input type=\"hidden\" name=\"action\" value=\"ImportAvatars\"><input type=\"hidden\" name=\"userid\" value=\"$user[userid]\"><input type=\"hidden\" name=\"session\" value=\"$session\">\n";
            echo "<input type=\"submit\" name=\"Abschicken\" value=\"Import Avatars >>\">\n";
            echo "</form>";
        }
    }
    elseif ( $action == "ImportAvatars" )
    {
        while( list($ava_url, $value) = each($avabild) )
        {
            if( $value == 1 )
            {
                $ava_name = substr ($ava_url,  0, -4);
                mysql_query('INSERT INTO '.$pref.'avatar (avatarname, avatarurl) VALUES ("'.$ava_name.'","avatar/'.$ava_url.'") ');
            }
        }
        echo('<br><font color="#00C600"><b>All Avatars imported!</b></font>');
    }
}
else
{
    echo 'Pre-installed Avatars are currently disabled. You can enable them <a href="index.php?session=' . $session . '&action=EditSettings">here</a>.';
    $avatarindex = true;
}
if (!$avatarindex)
{
    echo '<br><br><a href="avatar.php?action=ListAvatars&id='.$id.'&session='.$session.'">Back to the Avatar-Overview</a>';
}

tb_footer();
