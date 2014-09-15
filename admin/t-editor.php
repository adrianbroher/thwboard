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
include "fileaccess.inc.php";

tb_header();

function EditboxEncode($string)
{
	$string = str_replace('&', '&amp;', $string);
	$string = str_replace('"', '&quot;', $string);
	$string = str_replace('<', '&lt;', $string);
	$string = str_replace('>', '&gt;', $string);
	
	return $string;
}

function EditboxDecode($string)
{
	$string = str_replace('&amp;', '&', $string);
	$string = str_replace('&quot;', '"', $string);
	$string = str_replace('&lt;', '<', $string);
	$string = str_replace('&gt;', '>', $string);

	return $string;
}

$a_replace = array(
	'$style[stdfont]' => '{font}',
	'$style[stdfontend]' => '{/font}',
	'$style[smallfont]' => '{sfont}',
	'$style[smallfontend]' => '{/sfont}',
	'$style[colorbg]' => '{bgcolor}',
	'$style[colorbgfont]' => '{bgfontcolor}',
	'$style[color1]' => '{textcolor}',
	'$style[CellA]' => '{cellbg}',
	'$style[CellB]' => '{altcellbg}',
	'$style[col_he_fo_font]' => '{he_fo_color}',
	'$style[color4]' => '{tableheaderbg}',
	'$style[border_col]' => '{tablebordercolor}',
	'$style[color_err]' => '{errorcolor}',
	'$style[col_link]' => '{linkcolor}',
	'$style[col_link_v]' => '{visitedlinkcolor}',
	'$style[col_link_hover]' => '{hoverlinkcolor}'
);

if( $action == "EditTemplate" )
{
	if( !$name )
	{
		print 'no template name specified!';
	}
	elseif ( !$dir)
	{
		print 'no template-directory specified!';
	}
	else
	{
		if( !file_exists('../templates/' . $dir . '/' . $name) )
		{
			print 'template ' . $name . '.html does not exist!';
		}
		else
		{
			if( !WriteAccess('../templates/' . $dir . '/' . $name) )
			{
				print "can't edit file '$name.html': no write permission (set chmod 666 or 777)";
			}
			else
			{
				$t_data = @implode("", (@file('../templates/' . $dir . '/' . $name)));

				while( list($k, $v) = each($a_replace) )
				{
					$t_data = str_replace($k, $v, $t_data);
				}
				print '<b>Edit Template: "' . $name . '"</b><br><br>';
				print '<form action="t-editor.php" method="post">
<textarea wrap="OFF" name="t_data" class="htmlsource" cols="100" rows="32">' . EditboxEncode($t_data) . '</textarea><br><br><br>
<input type="hidden" name="action" value="UpdateTemplate">
<input type="hidden" name="name" value="' . $name . '">
<input type="hidden" name="dir" value="' . $dir . '">
<input type="hidden" name="session" value="' . $session . '">
<input type="submit" name="Submit" value="Update Template">
</form>';
				print '<b>available inserts:</b><br> {font} / {/font} - default font<br>
					{sfont} / {/sfont} - small font<br>
					{bgcolor} - body background color<br>
					{bgfontcolor} - body background font color<br>
					{textcolor} - text color within tables<br>
					{cellbg} - cell background color<br>
					{altcellbg} - 2nd cell bg color<br>
					{tableheaderbg} - table header and footer background<br>
					{he_fo_color} - font color for table headers and footers<br>
					{tablebordercolor} - color between cells<br>';
			}
		}
	}
}
elseif( $action == "UpdateTemplate" )
{
	if( !$name )
	{
		print 'no template specified!';
	}
	elseif ( !$dir)
	{
		print 'no template-directory specified!';
	}
	else
	{
		$fp = fopen('../templates/' . $dir . '/' . $name, 'w');
		if( !$fp )
		{
			print 'unable to open template file "' . $name . '" for write access. check chmod! (should be 666 or 777)';
		}
		else
		{
			while( list($k, $v) = each($a_replace) )
			{
				$t_data = str_replace($v, $k, $t_data);
			}
			$t_data = str_replace("\r\n", "\n", $t_data);
			fwrite($fp, stripslashes(EditboxDecode($t_data)));
			fclose($fp);
		
			print 'template has been updated!<br>click <a href="t-editor.php?action=EditTemplate&name=' . $name . '&session=' . $session . '&dir=' . $dir . '">here</a> to continue.';
		}
	}
}
elseif( $action == "ListTemplateSets" )
{
	print '<b>Templates Sets</b><br><br>';
	$path = "../templates";
	$tempdir = opendir($path);
	while( $tempsetdir = readdir($tempdir) )
	{
		if  ( ( $tempsetdir != ".." ) && ( $tempsetdir != "." ) &&
		( $tempsetdir != "mail" ) && ( is_dir($path."/".$tempsetdir) &&
		$tempsetdir != 'CVS' && $tempsetdir != 'css') )
		{
			print("[ <A HREF=\"t-editor.php?session=" . $session . "&action=ListTemplates&dir=$tempsetdir\">$tempsetdir</A> ]<BR><BR>");
		}
	}
}
elseif( $action == "ListTemplates" )
{
	if ( !$dir )
	{
	print 'no template-directory specified!';
	}
	else
	{
		print '<b>Templates</b><br><br>';
		$h = opendir('../templates/' . $dir . '/');
		$a_templates = array();
		while( $file = readdir($h) )
		{
			if( substr($file, -5) == '.mail' || substr($file, -5) == '.html' )
			{
				$a_templates[] = $file;
			}
		}
		
		sort($a_templates);
		
		print '<table width="100%" border="0" cellspacing="0" cellpadding="3">
	  <tr> 
	    <td><b>Filename</b></td>
	    <td><b>Options</b></td>
    	<td><b>Size</b></td>
	    <td><b>Modified</b></td>
	  </tr>';
		
		while( list($k, $file) = each($a_templates) )
		{
			if( !WriteAccess('../templates/' . $dir . '/' . $file) )
			{
				print '
	  <tr> 
	    <td'.($k % 2 == 0 ? ' bgcolor="#eeeeee"' : '').'>'.$file.'</td>
	    <td'.($k % 2 == 0 ? ' bgcolor="#eeeeee"' : '').'>Can'."'".'t edit: No permission</td>
	    <td'.($k % 2 == 0 ? ' bgcolor="#eeeeee"' : '').'>'.ceil(filesize('../templates/' . $dir . '/'.$file)/1000).' KB</td>
	    <td'.($k % 2 == 0 ? ' bgcolor="#eeeeee"' : '').'>'.date('d.m.Y, H:i', filemtime('../templates/' . $dir . '/'.$file)).'</td>
	  </tr>';
			}
			else
			{
				print '
	  <tr> 
	    <td'.($k % 2 == 0 ? ' bgcolor="#eeeeee"' : '').'>'.$file.'</td>
	    <td'.($k % 2 == 0 ? ' bgcolor="#eeeeee"' : '').'><a href="t-editor.php?action=EditTemplate&session='.$session.'&name='.$file.'&dir=' . $dir . '">Edit</a></td>
	    <td'.($k % 2 == 0 ? ' bgcolor="#eeeeee"' : '').'>'.ceil(filesize('../templates/' . $dir . '/'.$file)/1000).' KB</td>
	    <td'.($k % 2 == 0 ? ' bgcolor="#eeeeee"' : '').'>'.date('d.m.Y, H:i', filemtime('../templates/' . $dir . '/'.$file)).'</td>
	  </tr>';
			}
		}
		print '</table>';
	}
}


tb_footer();
