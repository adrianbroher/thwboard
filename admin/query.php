<?php
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
            (c) 2000-2004-2002 by



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
require('./common.inc.php');
tb_header();


function unhtmlspecialchars($s)
{
	$s = str_replace('&gt;', '>', $s);
	$s = str_replace('&lt;', '<', $s);
	$s = str_replace('&#039;', "'", $s);
	$s = str_replace('&quot;', '"', $s);
	$s = str_replace('&amp;', '&', $s);
	
	return $s;
}

if( $action == '' )
{
	print '
<b>Execute MySQL Query</b><br>
<br>
MySQL Query<br>
<form name="form1" method="post" action="query.php">
  <textarea class="tbinput" name="query" rows="8" cols="50">SELECT * FROM '.$pref.'board</textarea><br><br>
  <input type="hidden" name="session" value="'.$session.'">
  <input type="hidden" name="action" value="exec_query">
  <input type="submit" name="btn" value="   Send Query   ">
</form>
<br>
<br>
<br>
<br>
<b>Note:</b> You may want to disable this feature. For security reasons, there 
is no appropriate option here, at the admin center. To disable this feature, connect 
with your ftp client and simply delete this file (&quot;query.php&quot;).';
}



elseif( $action == 'exec_query' )
{
	if( stristr($query, 'DROP') && !$confirm )
	{
		print '
<b>Confirm query</b><br>
<br>
You are attempting to drop a table/database. Are you sure?<br>
<br>
Query: <i>'.$query.'</i><br>
<br>
<form method="POST" action="query.php">
  <input type="hidden" name="session" value="'.$session.'">
  <input type="hidden" name="query" value="'.htmlspecialchars($query).'">
  <input type="hidden" name="confirm" value="1">
  <input type="hidden" name="action" value="exec_query">
  <input type="submit" name="btn" value="   I know what Im doing   ">
</form>';
	}
	else
	{
		$r = mysql_query($query);
		if( mysql_error() )
		{
			print '
<b>Error</b><br>
<br>
<b>Query:</b> <i><font color="darkblue">'.$query.'</font></i><br>
<br>
<b>MySQL:</b> <font color="darkred">'.mysql_error().' ('.mysql_errno().')</font>';
		}
		else
		{
			if( stristr($query, 'SELECT') )
			{
				if( !stristr($query, 'LIMIT') )
				{
					$query .= ' LIMIT 0, 30';
				}

				if( mysql_num_rows($r) < 1 )
				{
					print 'Your query was OK, but no rows were selected. (mysql_num_rows() == 0)';
				}
				else
				{
					$fields = mysql_num_fields($r);
					$r_field = 

					$i = 0;
					print '<table width="100%" border="0" cellspacing="1" cellpadding="4">';

					print '<tr><td></td><td></td>';
					for( $j = 0; $j < $fields; $j++ )
					{
						print '<td bgcolor="#A8D3FF"><b>'.mysql_field_name($r, $j).'</b></td>';
					}
					print '</tr>';

					while( $table = mysql_fetch_row($r) )
					{
						print '<tr>
						<td'.($i % 2 == 0 ? ' bgcolor="#eeeeee"' : ' bgcolor="#dfdfdf"').' valign="top"><a href="">edit</a></td>
						<td'.($i % 2 == 0 ? ' bgcolor="#eeeeee"' : ' bgcolor="#dfdfdf"').' valign="top"><a href="">delete</a></td>';
						for( $j = 0; $j < $fields; $j++ )
						{
							print '<td'.($i % 2 == 0 ? ' bgcolor="#eeeeee"' : ' bgcolor="#dfdfdf"').' valign="top">'.htmlspecialchars($table[$j]).'</td>';
						}
						$i++;
						print '</tr>';
					}
					print '</table>';
				}
			}
			else
			{
				print 'Query sucessfull.';
			}
		}
	} 
}


tb_footer();
?>
