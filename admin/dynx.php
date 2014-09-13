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

error_reporting(7); // E_ERROR | E_WARNING | E_PARSE
set_magic_quotes_runtime(0);

if( isset($HTTP_GET_VARS) )
	extract($HTTP_GET_VARS);
if( isset($HTTP_PUT_VARS) )
	extract($HTTP_PUT_VARS);
if( isset($HTTP_POST_VARS) )
	extract($HTTP_POST_VARS);

include 'dynx_class.php';
include '../inc/config.inc.php';

$DB_Stream = new DB_Connection($mysql_h.','.$mysql_u.','.$mysql_p.','.$mysql_db);

// ===================================================
// ===================================================
// ===================================================

if( !isset( $pref ) || empty( $pref ) )
	$pref = 'thwb_';

if( !isset( $action ) || empty( $action ) )
	$action = 'login';
	
if( !isset( $l_username ) || !isset( $l_userpassword ) || !check_login( $l_username, $l_userpassword ))
{
  $action = "login";
}

// Oeffne mal bereits die Master-Server Connection
$servermastercon = new server("www.thwbxtra.kremedia.de", 1);

// ===================================================
// ===================================================
// ===================================================

switch($action)
{
	case 'addstyles':
		style_top("Styles werden installiert...");
		print "<p>Script Status</p>";
		
		if( !isset($addstyle_sql) && !isset($addstyle_dat) )
		{
			ERROR_Handler(5);
		}
		
		// ===================================================
		// Bei SQL-Eintraegen
		
		if( isset($addstyle_sql) )
		{
			while( list($styleid, $value) = each($addstyle_sql) )
			{
				if( intval($value) == 1 )
				{
					$style_data = $servermastercon->getAnswer('get_style^'.$styleid.'&ip='.$_SERVER['REMOTE_ADDR']);
					if( substr($style_data, 0, 16) == 'style_data_start' && substr($style_data, -14) == "style_data_end" )
					{
						$style_data_final = substr( strstr($style_data, "\n"), 1, -15);
						$DB_Stream->query( style2sql( $style_data_final, $pref ) );
						print "Style Nummer $styleid wurde erfolgreich in die MySQL Datenbank importiert!<br>\n";
						flush();
					}
					else
					{
						ERROR_Handler(6);
					}
				}
			}
		}
		
		// ===================================================
		// Bei .style-Eintraegen
		
		if( isset($addstyle_dat) )
		{
			while( list($styleid, $value) = each($addstyle_dat) )
			{
				if( intval($value) == 1 )
				{
					$style_data = $servermastercon->getAnswer('get_style^'.$styleid.'&ip='.$_SERVER['REMOTE_ADDR']);
					if( substr($style_data, 0, 16) == 'style_data_start' && substr($style_data, -14) == "style_data_end" )
					{
						$style_data_name =  substr( $style_data, 17, strpos( $style_data, "\n" ) - 17);
						$style_data_final = substr( strstr($style_data, "\n"), 1, -15);
						print "Style Nummer $styleid wird geschrieben, bitte warten...<br>";
						flush();
						FILEFUNC::writefile('../templates/'.$style_data_name.'.style', $style_data_final);
						print "Style Nummer $styleid wurde erfolgreich erstellt!<br>\n";
					}
					else
					{
						ERROR_Handler(6);
					}
				}
			}
		}

		print '<b>Alle Styles wurden erfolgreich eingef&uuml;gt, vielen Dank das Sie Dyn<i>X</i> benutzt haben!';
		style_bottom('login', 'Ausloggen');
	break;
	
	// ===================================================
	// ===================================================
	// ===================================================
	
	case 'liststyles':
		style_top("Styleauswahl");
		print '<p>Folgende Styles stehen zur Verf&uuml;gung:</p><img src="./images/dynx_desc2.gif" border="0">';
		print '<table width="100%" cellpadding="2" cellspacing="1">';
		print "\n<tr>\n<td><input type=\"checkbox\" name=\"selectall\" onclick=\"return SelectAll(0)\"></td><td><input type=\"checkbox\" name=\"selell\" onclick=\"return SelectAll(1)\"></td><td><b>Name</b></td>\n<td><b>Author</b></td>\n<td><b>Beschreibung</b></td>\n</tr>\n";

		$style_overview = $servermastercon->getAnswer('style_overview');
		if( substr($style_overview, 0, 16) == 'start_style_list' && substr($style_overview, -14) == "end_style_list" )
		{
			$stylearray = explode("\n", $style_overview);
			for( $i = 1; $i <= ( sizeof($stylearray) - 2); $i++)
			{
				list($styleid, $stylename, $styleautor, $styledesc) = explode("|", $stylearray[$i]);
				$q_avatar = $DB_Stream->query('SELECT styletemplate FROM '.$pref.'style WHERE stylename =\''. addslashes($stylename) .'\'');
				
				if( mysql_fetch_array($q_avatar) )
				{
					$checkformsql = '<input type="checkbox" name="disable" readonly="readonly" disabled>';
				}
				else
				{
					$checkformsql = "<input type=\"checkbox\" name=\"addstyle_sql[$styleid]\" value=\"1\">";
				}
				
				if( file_exists("../templates/".$stylename.".style") )
				{
					$checkformdat = '<input type="checkbox" name="disable" readonly="readonly" disabled>';
				}
				else
				{
					$checkformdat = "<input type=\"checkbox\" name=\"addstyle_dat[$styleid]\" value=\"1\">";
				}
				
				print "<tr bgcolor=\"".($i % 2 == 0 ? '#EFEFEF' : '#DADADA')."\"><td>$checkformdat</td><td>$checkformsql</td><td>$stylename</td><td>$styleautor</td><td>$styledesc</td></tr>\n";
			}
		}
		print '</table><img src="./images/dynx_desc.gif" border="0"><br><br>';
		print '<input type="checkbox" name="disable" readonly="readonly" disabled> = Style wurde bereits installiert.';
		style_bottom('addstyles', 'Weiter &raquo;');
	break;
	
	// ===================================================
	// ===================================================
	// ===================================================
	
	case 'login':
	default:
		style_top("Anmeldung");
		print '<p>Willkommen bei Dyn<i>X</i>, dem dynamischen Extra-Scripts des ThWboards.</p>';
		print '<p>Mit Dyn<i>X</i> haben Sie die M&ouml;glichkeit, einfach und schnell neue Styles (Farb-Pakete) herunterzuladen und zu installieren.</p>';
		print '<p><b>Bitte stellen Sie vor der Anwendung des Scripts sicher, dass Schreibrechte (chmod 777) auf folgenden Verzeichnissen bestehen:</b></p>';
		print '<ul><li><i>&lt;ThWb-Verzeichnis&gt;</i>/templates</li></ul>';
		print '<b>Bitte geben Sie hier ihre Zugangsdaten ein um fortzufahren:</b><br> 
<table cellspacing="0" cellpadding="2" border="0">
  <tr>
    <td>Benutzername</td>
	<td width="10">&nbsp;</td>
	<td><input type="text" name="l_username"></td>
  </tr>
  <tr>
    <td>Passwort</td>
	<td width="10">&nbsp;</td>
	<td><input type="password" name="l_userpassword"></td>
  </tr>
</table>';
		style_bottom('liststyles', 'Weiter &raquo;', 1);
	break;
}
?>
