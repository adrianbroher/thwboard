<?php
/* $Id: dynx_class.php 87 2004-11-07 00:19:15Z td $ */
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

//Fehlermeldungen
$errorMessages = array(
/* 1 */	'Probleme beim Connecten zum MySQL Server:',
/* 2 */	'Fehler der MySQL Datenbank bei folgendem Query String:',
/* 3 */	'Die Styleimportierung wurde abgebrochen, da das Style eine inkompatible Version hat, n&auml;mlich die Version',
/* 4 */	'Die Style-Dateien Erstellung wurde abgebrochen, da der Zugriff auf den Ordner, bzw. der Datei verweigert wurde. Bitte &uuml;berpr&uuml;fen sie ihre CHMOD Einstellungen.',
/* 5 */	'Sie haben keine Styles ausgew&auml;hlt!',
/* 6 */	'Ein Fehler bei der Antwort des Servers ist aufgetreten, bitte versuchen Sie es sp&auml;ter nochmal.',
/* 7 */	'Der Server hat eine ung&uuml;ltige Anfrage bekommen, bitte vergiwissern Sie sich, dass Sie die aktuellste Version von DynX haben.',
/* 8 */	'Die Styleinformationen sind leider ung&uuml;ltig, bitte w&auml;hlen Sie ein anderes Style aus.',
/* 9 */ 'Die maximale Download Rate pro Tag wurde &uuml;berschritten, bitte versuchen sie es an einem anderen Tag wieder.'
);


///////////////////////
// Styles und Funktionen
// Top-Style + Bottom-Style
// Fehler-Handler, Style->SQL Konverter
///////////////////////

function style_top($title = 'Installer')
{
	print ('<html>
<head>
<title>ThWboard DynX Client - ' . $title . '</title>
<style type="text/css">
body { background-color: #FFFFFF; text-align: center; }
table.main { background-color: #909090; }

td.main { background-color: #EFEFEF; color: #7F7F7F; }
td { font-family: "Verdana"; font-size: 10pt; color: #7F7F7F; }

div.title1 { font-size: 14pt; font-family: "Verdana", "Arial", "sans-serif"; }
div.copy { font-size: 8pt; font-family: "Verdana"; color: #7F7F7F; }

.dynxbutton { font-family: Verdana; font-size: 8pt; background-color: #EFEFEF; border:2px solid #C0C0C0; }

a:link { color: #7E7E7E; text-decoration: none; font-weight: bold; }
a:visited { color: #7E7E7E; text-decoration: none; font-weight: bold; }
a:active { color: #7E7E7E; text-decoration: none; font-weight: bold; }
a:hover { color: #7E7E7E; text-decoration: none; font-weight: bold; }
</style>
</head>
<body>
<script language="JavaScript">
<!--
function SelectAll(a)
{
	var boolValue;
	if (a == 1)
	{
		boolValue = document.theform.selell.checked;
	}
	else
	{
		boolValue = document.theform.selectall.checked;
	}
	for (var i=a;i<document.theform.elements.length;i=i+2)
	{
		var e = document.theform.elements[i];
		if (e.name != "disable")
		{
			e.checked = boolValue;
		}
	}
}

--></script>
<center><table class="main" width="60%" cellpadding="2" cellspacing="1">
  <tr>
    <td class="main">
      <table width="100%" cellspacing="1" cellpadding="0">
        <tr><td width="15%"><img src="./images/dynx_logo.gif"></td><td class="main" width="85%"><div class="title1">&nbsp;ThWBoard Dyn<i>X</i></div></td></tr>
        <tr><td colspan="2" bgcolor="#909090"> </td></tr>
        <tr><td colspan="2" height="6"> </td></tr>
        <tr><td colspan="2" class="main">
          <form name="theform" method="post" action="dynx.php">
');
}

// ===================================================

function style_bottom($action, $buttontitle, $hiddeninput = 0)
{
	global $l_username, $l_userpassword;
	
	if( !$hiddeninput )
	{
		print ('
	  <input type="hidden" name="l_username" value="' . $l_username . '">
          <input type="hidden" name="l_userpassword" value="' . $l_userpassword . '">');
        }
	
	print ('	  <input type="hidden" name="action" value="' . $action . '"><br>
          <br><input type="submit" name="next" value="' . $buttontitle . '" class="dynxbutton">
	  </form>
        </td></tr>
      </table>
    </td>
  </tr>
</table></center>
<br>
<br>
<!-- Dieser Copyright-Vermerk darf weder ver&auml;ndert noch entfernt werden! -->
<div class="copy">
&copy; 2002 the <a href="http://thwboard.de">ThWboard</a> developer team
</div>
</body>
</html>');
}

// ===================================================

function check_login( $user, $pass )
{
	global $DB_Stream, $pref;
	
	$r_user = $DB_Stream->query(
		"SELECT
			userpassword
		FROM
			$pref"."user
		WHERE
			username='". addslashes($user) ."'
		AND userisadmin=1"
	);
	
	if( mysql_num_rows($r_user) )
	{
		$user = mysql_fetch_array($r_user);
		if( $user['userpassword'] == md5($pass) )
			return true;
	}
	return false;
}

function ERROR_Handler ( $erorrno, $errormsg = '' )
{
	global $errorMessages;
	
	print '
<b>Ein Fehler ist aufgetreten:</b><br>
<br>
'.$errorMessages[$erorrno - 1].'<br>
<br>
<pre>'.$errormsg.'</pre><br>
<br>
<a href="JavaScript:history.back(0)">Zur&uuml;ck</a>
';
exit();
}

// ===================================================

function style2sql( $style_file, $pref )
{
	$style_file_version = '1.0';
	$styledata = explode("\n", $style_file);
	$a_name = array();
	$a_value = array();
	while( list($nline, $line) = each($styledata) )
	{
		if( trim($line) )
		{
			$pos = strpos($line, '=');
		
			$name = substr($line, 0, $pos);
			$value = substr($line, $pos + 1);
			
			if( $name == 'styleversion' )
			{
				$ver = trim($value);
			}
			else
			{
				$a_name[] = addslashes($name);
				$a_value[] = "'".addslashes(trim($value))."'";
			}
		}
	}
	if( $ver != $style_file_version )
	{
		ERROR_Handler(3, $ver);
	}
	else
	{
		$query = "INSERT INTO $pref"."style
			(".implode(',', $a_name).")
			VALUES
			(".implode(',', $a_value).")";
	return( $query );
	}
}

///////////////////////
// Klassen
// Dateisystem, Datenbanksystem, Zeit und Server-System
///////////////////////
class FILEFUNC
{
	var $F_Stream = '';
	
	function writefile($path, $filecontext)
	{
		if( !FILEFUNC::WriteAccess($path) )
		{
			ERROR_Handler(4);
			return FALSE;
		}
		else
		{
			$this->F_Stream = @fopen($path, 'w');
			fwrite($this->F_Stream, $filecontext);
			fclose($this->F_Stream);
			return TRUE;
		}
	}
	
	function writeaccess($file)
	{
		$this->F_Stream = @fopen($file, 'w');
		if( !$this->F_Stream )
		{
			return FALSE;
		}
		else
		{
			fclose($this->F_Stream);
			return TRUE;
		}
	}
	
	function has_access( $file, $access_rights )
	{
		return 1;
	}
      
}

// ===================================================

class DB_Connection
{
	var $_errorno = 0;
	var $_errormsg = '';

	function DB_Connection( $dbconnstring )
	{
		$dbconnval = explode( ",", $dbconnstring );
		$db = @mysql_connect( $dbconnval['0'], $dbconnval['1'], $dbconnval['2'] );
		$db2 = @mysql_select_db( $dbconnval['3'], $db );
		if( mysql_error() )
		{
			$this->_errormsg = mysql_error();
			$this->_errorno = 1;
			ERROR_Handler($this->_errorno, $this->_errormsg);
		}
	}
	
	function query( $querystring )
	{
		$test = 2;
		$r_result = @mysql_query( $querystring );
		
		if( mysql_error() )
		{
			$this->_errormsg = mysql_error();
			$this->_errorno = 2;
			ERROR_Handler($this->_errorno, $this->_errormsg);
		}
		
		return $r_result;
	}
}

// ===================================================

class timer
{
	var $starttime = '';
	var $endtime = '';
	
	function timer()
	{
		$this->start();
	}
	
	function start()
	{
		$this->starttime = $this->_getmicrotime();
	}
	
	function stop()
	{
		$this->endtime = $this->_getmicrotime();
		return $this->gettime();
		$this = null;
	}
	
	function _getmicrotime()
	{
		list( $usec, $sec ) = explode( " ", microtime() );
		return( (float)$usec + (float)$sec );
	}
	
	function gettime( $multi = 100000 )
	{
		return round( ($this->endtime - $this->starttime) * $multi, 0);
	}
	
	function pause()
	{
		$this->endtime = _getmicrotime();
		return $this->gettime();
	}
}

// ===================================================

class server
{
	var $connection = '';
	var $serveruri = '';
	var $thwboardver = '2.8'; // Fuer 2.8 bisher...
	
	function server( $uri, $retries )
	{
		$this->serveruri = $uri;
		$this->pingserver();
	}
	
	// Noch dummy, bekommt in der naechsten Version einen richtigen Zweck
	function pingserver()
	{
		$fp = fsockopen( 'udp://' . $this->serveruri, 7 );
		$timer = new timer();
		fwrite( $fp, "ping" );
		$time = $timer->stop();
		fclose( $fp );
		return $time;
	}

	function getAnswer( $question )
	{
		$this->connection = fopen('http://'.$this->serveruri.'/xtrasrv/srv.php?quest='.$question.'&tbver='.$this->thwboardver, 'r');
		$answere = '';
		while( !feof( $this->connection ) )
		{
			$answere .= fread($this->connection, 1024);
		}
		fclose( $this->connection );
		
		// fuer den fall das Server-Headers mitgeschickt werden
		if( substr($answere, 0, 7) == 'X-Power' )
		{
			$pos = strpos($answere, "html");
			$answere = substr($answere, $pos + 8);
		}
		
		$answere = trim($answere);
		
		if( substr( $answere, 0, 9) == 'srv_error' )
		{
			// Identifiziere Fehlermeldung des Servers und gebe entsprechende Erklaerungen aus
			switch( substr( $answere, 10) )
			{
				case "false_command":
					ERROR_Handler(7);
					break;
				case "no_styledata":
					ERROR_Handler(8);
					break;
				case "no_quest":
					ERROR_Handler(6);
					break;
				case "no_styleid":
					ERROR_Handler(5);
					break;
				case "error_style_limit":
					ERROR_Handler(9);
					break;
				default:
					ERROR_Handler(6, $answere);
			}
		}
		
		return( $answere );
	}
	
	//ttt: Wird dies noch gebraucht? Hat Probleme gemacht in Verbindung mit fopen()...
	function getReadBytes()
	{
		$bytes = socket_get_status($this->connection);
		return $bytes;
	}
}
?>