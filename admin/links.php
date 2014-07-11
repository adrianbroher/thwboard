<?php
/* $Id: links.php 87 2004-11-07 00:19:15Z td $ */
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

if( $config['enable_quicklinks'] != 1 )
{
	print 'Quicklinks are currently disabled. You can enable them <a href="index.php?session=' . $session . '&action=EditSettings">here</a>.';
}
else
{
if ($action=="EditLink") 
{
	$quicklinks_search = mysql_query("SELECT * FROM ".$pref."qlink WHERE linkid = $id");
	$data_link = mysql_fetch_array($quicklinks_search);
	if ($data_link[linkactive]==1) 
	{
		$radio_active=" selected";
		$caption_active="Activated";
		$radio_deactive="";
		$caption_deactive="Deactivate";
	} 
	else 
	{
		$radio_active="";
		$caption_active="Activate";
		$radio_deactive=" selected";
		$caption_deactive="Deactivated";
	}
	print('<b>Quicklinks / Edit "'.$data_link[linkcaption].'"</b><br><br>');
	print('<form action="links.php" method=post>
	<input type=hidden name=session value="'.$session.'">
	<input type=hidden name=action value="savelinks">
	<input type=hidden name=id value="'.$id.'">
	<table width=100% border=0 cellspacing=0 cellspadding=0>
	<TR><TD width=150>ID :	</TD><TD>'.$data_link[linkid].'</TD></TR>
	<TR><TD>URL / Link :	</TD><TD><input class="tbinput" type="text" name="new_http" 	size=40 value="'.$data_link[linkhttp].'"></TD></TR>
	<TR><TD>Caption :		</TD><TD><input class="tbinput" type="text" name="new_caption" 	size=40 value="'.$data_link[linkcaption].'"></TD></TR>
	<TR><TD>Title :			</TD><TD><textarea class="tbinput" name="new_title" cols=39 rows=5>'.$data_link[linkalt].'</textarea></TD></TR>
	<TR><TD>Counter :		</TD><TD><input class="tbinput" type="text" name="new_counter" 	size=5 value="'.$data_link[linkcounter].'"></TD></TR>
	<TR><TD>Status :		</TD><TD><Select class="tbinput" name="new_status"><option value="1"'.$radio_active.'>'.$caption_active.'</option><option value="0"'.$radio_deactive.'>'.$caption_deactive.'</option></select></TD></TR>
	<TR><TD>&nbsp;<TD><input type=submit value="Save Settings"></TR>
	</table>');
}
elseif ($action=="savelinks") 
{
	$update_link = mysql_query('UPDATE ' . $pref . 'qlink SET 
	linkhttp = "'.((preg_match("/^([a-zA-Z0-9]+:\/\/)/", $new_http)) ? $new_http : 'http://'.$new_http).'", 
	linkcaption = "'.$new_caption.'",
	linkalt = "'.$new_title.'",
	linkcounter = "'.$new_counter.'",
	linkactive = "'.$new_status.'"
	WHERE linkid = '.$id.'');
	print('<b>Quicklinks</b><br><br>
	The Quicklink has been updated!<BR>
	click <A HREF="links.php?action=EditLink&id='.$id.'&session='.$session.'">here</a> to edit this Quicklink once again.
	');
}
elseif ($action=="deletelink") 
{
	$delete_link = mysql_query('DELETE FROM ' . $pref . 'qlink WHERE linkid = '.$id.'');
	print('The Quicklink has been deleted!');
}
elseif ($action=="NewLink") 
{
	print('<b>Quicklinks / Add a new Quicklink</b><br><br>');
	print('<form action="links.php" method=post>
	<input type=hidden name=session value="'.$session.'">
	<input type=hidden name=action value="Addlinks">
	<table width=100% border=0 cellspacing=0 cellspadding=0>
	<TR><TD>URL / Link :	</TD><TD><input class="tbinput" type="text" name="new_http" 	size=40 value=""></TD></TR>
	<TR><TD>Caption :		</TD><TD><input class="tbinput" type="text" name="new_caption" 	size=40 value=""></TD></TR>
	<TR><TD>Title :			</TD><TD><textarea class="tbinput" name="new_title" cols=39 rows=5></textarea></TD></TR>
	<TR><TD>Counter :		</TD><TD><input class="tbinput" type="text" name="new_counter" 	size=5 value=""></TD></TR>
	<TR><TD>Status :		</TD><TD><Select class="tbinput" name="new_status"><option value="1" selected>Activate</option><option value="0">Deactivate</option></select></TD></TR>
	<TR><TD>&nbsp;<TD><input type=submit value="Save Settings"></TR>
	</table> ');
}
elseif ( ( $action == "" ) || ( $action == "Addlinks") ) 
{
	$insert_qlink = mysql_query('INSERT INTO ' . $pref . 'qlink (linkhttp,linkcaption,linkalt,linkcounter,linkactive) VALUES ("'.((preg_match("/^([a-zA-Z0-9]+:\/\/)/", $new_http)) ? $new_http : 'http://'.$new_http).'","'.$new_caption.'","'.$new_title.'","'.$new_counter.'","'.$new_status.'") ');
	print('Quicklink has been added!');
}
elseif ( ( $action == "" ) || ( $action == "ListLinks" ) ) 
{
	print('<b>Quicklinks</b><br><br>');
	$quicklinks_search = mysql_query("SELECT * FROM ".$pref."qlink");
	while ( $data_links = mysql_fetch_array($quicklinks_search) ) 
	{
	echo "<A HREF=\"$data_links[linkhttp]\" target=_blank>$data_links[linkhttp]</a> - $data_links[linkcaption]&nbsp;&nbsp;&nbsp;[ <a href=\"links.php?session=$session&action=EditLink&id=$data_links[linkid]\">Edit</a> ] [ <a href=\"links.php?session=$session&action=deletelink&id=$data_links[linkid]\">Delete</a> ]<BR>";
	}
	print("<BR><BR>[ <A HREF=\"links.php?session=" . $session . "&action=NewLink\">New Quicklink</A> ]");
}
}

tb_footer();
?>