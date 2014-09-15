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

print '<b>Badwords Protection</b><br><br>';

if( $action == "FormAction" && isset($UpdateRegistry) )
{
	query("UPDATE ".$pref."registry SET keyvalue='".$usebwordprot."' WHERE keyname='usebwordprot'");
	if( mysql_error() )
	{
		print 'A problem occured while saving the configuration data, configuration has NOT been saved!';
	}
	else
	{
		print 'Configuration saved!<br><a href="bwords.php?session='.$session.'">Back to the badwords overview</a>';
	}
}
else if( $action == "FormAction" && isset($mass_delete) )
{
	if( !isset($a_todelete) )
	{
		print 'No data selected!<br><a href="bwords.php?session='.$session.'">Back to the badwords overview</a>';
	}
	else
	{
		print 'Do you want to delete the following badwords?<br>';
		print '<form name="form1" method="post" action="bwords.php">';
		print '<table border="0" bgcolor="#ffffff" cellpadding="2" cellspacing="1">';
		print '<tr>';
		print '<td><font face="Arial"><i>badword</i></font></td><td><font face="Arial"><i>spare word</i></font></td>';
		print '</tr>';
		$r_bword = query("SELECT banword, modword FROM ".$pref."bannedwords WHERE wordid IN (".implode(",", $a_todelete).")");
		if( mysql_num_rows($r_bword) != 0 )
		{
			while( $eintrag = mysql_fetch_array($r_bword) )
			{
				print '<tr><td bgcolor="#E5E5E5">'.htmlspecialchars($eintrag['banword']).'</td>';
				print '<td bgcolor="#E5E5E5">'.htmlspecialchars($eintrag['modword']).'</td>';
				print '</tr>';
			}
			print '</table>';
			print '<br>';
			print '<input type="hidden" name="deletevalues" value="'.implode(",", $a_todelete).'">';
			print '<input type="hidden" name="session" value="'.$session.'">';
			print '<input type="hidden" name="action" value="FormAction">';
			print '<input type="submit" name="confirmDelete" value="Delete &raquo;">';
			print '</form>';
		}
		else
		{
			print '<tr><td colspan="4" bgcolor="#E5E5E5" align="center"><i>No data selected!</i></td></tr>';
			print '</table>';
			print '<br>';
			print '<input type="button" disabled name="confirmDelete" value="Delete &raquo;">';
		}
	}
}
else if( $action == "FormAction" && isset($AddEntry) )
{
	print 'Add new word:';
	print '<br>';
	print '<form action="bwords.php" name="Form1" method="post">';
	print '<table border="0" bgcolor="#ffffff" cellpadding="2" cellspacing="1">';
	print '<tr><td bgcolor="#E5E5E5">badword:</td><td bgcolor="#F2F2F2"><input type="text" name="banword"></td></tr>';
	print '<tr><td bgcolor="#E5E5E5">spare word:</td><td bgcolor="#F2F2F2"><input type="text" name="modword"></td></tr>';
	print '</table>';
	print '<br>';
	print '<input type="hidden" name="session" value="'.$session.'">';
	print '<input type="hidden" name="action" value="FormAction">';
	print '<input type="submit" name="AddEntryConfirm" value="Add &raquo;">';
	print '</form>';
}
else if( $action == "FormAction" && isset($AddEntryConfirm) )
{
	if( !isset($banword) || $banword == "" )
	{
		print 'No word to add! Please enter a badword.';
	}
	else
	{
		print 'Following term will be saved:<br>';
		print '<table border="0" bgcolor="#ffffff" collpadding="2" cellspacing="1">';
		print '<tr><td><font face="Arial"><i>bannedword</i></font></td><td><font face="Arial"><i>spare word</i></font></td></tr>';
		print '<tr><td bgcolor="#E5E5E5">'.$banword.'</td><td bgcolor="#E5E5E5">'.$modword.'</td></tr>';
		print '</table>';
		print '<br>';
		mysql_query("INSERT INTO ".$pref."bannedwords (banword, modword) VALUES ('".$banword."', '".$modword."')");
		if( !mysql_error() )
		{
			print 'Term added.<br><a href="bwords.php?session='.$session.'">Back to the badwords overview</a>';
		}
		else
		{
			print 'Term could not be added, maybe already in database?<br><a href="bwords.php?session='.$session.'">Back to the badwords overview</a>';
		}
	}
}
else if( $action == "FormAction" && isset($confirmDelete) )
{
	query("DELETE FROM ".$pref."bannedwords WHERE wordid IN (".$deletevalues.")");
	print 'The entries have been deleted.<br>';
	$r_bwords = query("SELECT wordid FROM ".$pref."bannedwords");
	if( mysql_num_rows($r_bwords) < 1 )
	{
		query("UPDATE ".$pref."registry SET keyvalue='0' WHERE keyname='usebwordprot'");
		if( mysql_error() )
		{
			print 'A problem occured while saving the configuration data, configuration has NOT been saved!';
		}
		else
		{
			print 'There are no more data entries. Badwords protection disabled.<br><a href="bwords.php?session='.$session.'">Back to the badwords overview</a>';
		}
	}
	mysql_free_result($r_bwords);
}
else if( $action == "FormAction" && isset($EditConfirm) )
{
	query("UPDATE ".$pref."bannedwords SET modword = '".$value."' WHERE wordid=".$wordid);
	if( mysql_error() )
	{
		print 'An error occured, the term could not been updated!';
	}
	else
	{
		print 'Badword has successful been updated.';
	}
}
else if( $action == "EditEntry" )
{
	$r_result = query("SELECT banword, modword FROM ".$pref."bannedwords WHERE wordid=".$value);
	if( mysql_num_rows($r_result) != 0 )
	{
		$result = mysql_fetch_array($r_result);
		print 'The following badword is to be edited. Only the spare word can be modified.<br>';
		print '<form name="form1" method="post" action="bwords.php">';
		print '<table border="0" bgcolor="#ffffff" cellpadding="2" cellspacing="1">';
		print '<tr>';
		print '<td bgcolor="#E5E5E5">badword:</td><td bgcolor="#F2F2F2">'.$result['banword'].'</td></tr>';
		print '<td bgcolor="#E5E5E5">spare word:</td><td bgcolor="#F2F2F2"><input type="text" name="value" value="'.$result['modword'].'"></td></tr>';
		print '</table>';
		print '<br>';
		print '<input type="hidden" name="session" value="'.$session.'">';
		print '<input type="hidden" name="action" value="FormAction">';
		print '<input type="hidden" name="wordid" value="'.$value.'">';
		print '<input type="submit" name="EditConfirm" value="Modify &raquo;">';
		print '</form>';
	}
}
else if( $action == "DelEntry" )
{
	print 'Do you want to delete the following badword?<br>';
	print '<form name="form1" method="post" action="bwords.php">';
	print '<table border="0" bgcolor="#ffffff" cellpadding="2" cellspacing="1">';
	print '<tr>';
	print '<td><font face="Arial"><i>badword</i></font></td><td><font face="Arial"><i>spare word</i></font></td>';
	print '</tr>';
	$r_bword = query("SELECT banword, modword FROM ".$pref."bannedwords WHERE wordid=".$value." LIMIT 1");
	if( mysql_num_rows($r_bword) == 1 )
	{
		$eintrag = mysql_fetch_array($r_bword);
		print '<tr><td bgcolor="#E5E5E5">'.htmlspecialchars($eintrag['banword']).'</td>';
		print '<td bgcolor="#E5E5E5">'.htmlspecialchars($eintrag['modword']).'</td>';
		print '</tr></table>';
		print '<br>';
		print '<input type="hidden" name="deletevalues" value="'.$value.'">';
		print '<input type="hidden" name="session" value="'.$session.'">';
		print '<input type="hidden" name="action" value="FormAction">';
		print '<input type="submit" name="confirmDelete" value="Delete &raquo;">';
		print '</form>';
	}
	else
	{
		print '<tr><td colspan="4" bgcolor="#E5E5E5" align="center"><i>No badword has been provided. No action performed.</i></td></tr>';
		print '</table>';
		print '<br>';
		print '<input type="button" disabled name="confirmDelete" value="Delete &raquo;">';
		print '</form>';
	}
}
else if( !isset($action) || $action == "ListBWords" )
{
	print '<script language="JavaScript">
	function SelectAll()
  {
    for (var i=0;i<document.form1.elements.length;i++)
    {
      var e = document.form1.elements[i];
      var boolValue = document.form1.selectall.checked;
      if (e.name != "selectall" && e.name != "session" && e.name != "action" && e.name != "mass_delete" && e.name != "AddEntry")
	  {
        e.checked = boolValue;
	  }
    }
  }</script>';
	print '<form name="form1" method="post" action="bwords.php">';
	print '<table border="0" bgcolor="#ffffff" cellpadding="2" cellspacing="1">';
	print '<tr>';
	print '<td><font face="Arial"><i>Badwords protection applies to</i></font></td></tr>';
	print '<tr><td bgcolor="#E5E5E5">';
	print '<input type="radio" name="usebwordprot" value="0"'.($config['usebwordprot'] == 0 ? 'checked' : '').'>None (disabled)</option><br>';
	print '<input type="radio" name="usebwordprot" value="1"'.($config['usebwordprot'] == 1 ? 'checked' : '').'>Only in thread titles</option><br>';
	print '<input type="radio" name="usebwordprot" value="2"'.($config['usebwordprot'] == 2 ? 'checked' : '').'>Only in posts</option><br>';
	print '<input type="radio" name="usebwordprot" value="3"'.($config['usebwordprot'] == 3 ? 'checked' : '').'>Both in thread titles and posts</option>';
	print '</td></tr>';
	print '<tr><td bgcolor="#F5F5F5" align="center">';
	print '<input type="hidden" name="session" value="'.$session.'">';
	print '<input type="hidden" name="action" value="FormAction">';
	print '<input type="submit" name="UpdateRegistry" value="save configuration &raquo;">';
	print '</td></tr></table>';
	print '</form>';
	print '<br>';
	print '<form name="form1" method="post" action="bwords.php">';
	print '<table border="0" bgcolor="#ffffff" cellpadding="2" cellspacing="1">';
	print '<tr>';
	print '<td> </td><td><font face="Arial"><i>badword</i></font></td><td><font face="Arial"><i>spare word</i></font></td><td> </td>';
	print '</tr>';
	
	$r_bwords = query("SELECT wordid, banword, modword FROM ".$pref."bannedwords ORDER BY wordid");
	if( mysql_num_rows($r_bwords) < 1 )
	{
		print '<tr><td colspan="4" bgcolor="#E5E5E5" align="center"><i>no badwords.</i></td></tr>';
		print '<tr><td><input type="checkbox" readonly="readonly" disabled name="selectall" onclick="SelectAll()"></td><td colspan="3" color="#C0C0C0">Select All</td></tr>';
		print '</table>';
		print '<br>';
		print '<input type="hidden" name="session" value="'.$session.'">';
		print '<input type="hidden" name="action" value="FormAction">';
		print '<input type="button" name="mass_delete" readonly="readonly" disabled value="Delete selected">';
		print '<input type="submit" name="AddEntry" value="Add new word">';
		print '</form>';
	}
	else
	{
		$i = 0;
		while( $eintrag = mysql_fetch_array($r_bwords) )
		{
			print '<tr>';
			print '<td bgcolor="'.($i % 2 == 0 ? '#E5E5E5' : '#F2F2F2').'" align="center"><input type="checkbox" name="a_todelete[]" value="'.$eintrag['wordid'].'"></td>';
			print '<td bgcolor="'.($i % 2 == 0 ? '#E5E5E5' : '#F2F2F2').'">'.htmlspecialchars($eintrag['banword']).'</td>';
			print '<td bgcolor="'.($i % 2 == 0 ? '#E5E5E5' : '#F2F2F2').'">'.htmlspecialchars($eintrag['modword']).'</td>';
			print '<td bgcolor="'.($i % 2 == 0 ? '#E5E5E5' : '#F2F2F2').'"><a href="bwords.php?action=EditEntry&value='.$eintrag['wordid'].'&session='.$session.'">Edit</a> |';
			print ' <a href="bwords.php?action=DelEntry&value='.$eintrag['wordid'].'&session='.$session.'">Delete</a></td>';
			print '</tr>';
			$i++;
		}
	mysql_free_result($r_bwords);
	print '<tr><td><input type="checkbox" name="selectall" onclick="return SelectAll()"></td><td colspan="3">Select All</td></tr>';
	print '</table>';
	print '<br>';
	print '<input type="hidden" name="session" value="'.$session.'">';
	print '<input type="hidden" name="action" value="FormAction">';
	print '<input type="submit" name="mass_delete" value="Delete selected">';
	print ' <input type="submit" name="AddEntry" value="Add new word">';
	print '</form>';
	}
}
else
{
	print '<font color="#FF0000"><b>False parameter string! No Option called.</b></font>';
}

tb_footer();
