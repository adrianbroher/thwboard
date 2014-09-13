<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<h1>thwboard update</h1>
2.6 -&gt; 2.7<br><br>
<?php

if( $action == '' )
{
	print '<font color="red">Bevor dieses Script gestartet wird, sollte die ".$pref."user Datenbank gesichert werden!<br>
Es ist zu beachten, dass w&#xE4;hrend des Update-Vorgangs immer 100 User pro Zyklus bearbeitet werden. Wird einmal auf "Zur&#xFC;ck" geklickt (Beim Browser) oder aktualisiert, so sind die User Passw&#xF6;rter falsch und entsprechende User m&#xFC;ssen sich ein neues Passwort generieren lassen.</font><br>
	<form name="theform" method="post" action="">
  <table width="400" border="0" cellspacing="0" cellpadding="4">
    <tr> 
      <td height="35">mysql host</td>
      <td height="35"> 
        <input type="text" name="mysql_host">
      </td>
    </tr>
    <tr> 
      <td>mysql database</td>
      <td> 
        <input type="text" name="mysql_database">
      </td>
    </tr>
    <tr> 
      <td>mysql user</td>
      <td> 
        <input type="text" name="mysql_user">
      </td>
    </tr>
    <tr> 
      <td>mysql password</td>
      <td> 
        <input type="password" name="mysql_password">
      </td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td> 
         <input type="hidden" name="action" value="convert">
         <input type="submit" name="Abschicken" value="Update">
      </td>
    </tr>
  </table>
</form>';
}
else
{
	if( !isset($offset) || !$offset )
	{
		$offset = 0;
	}

	$c = mysql_connect($mysql_host, $mysql_user, $mysql_password);
	$d = mysql_select_db($mysql_database);
	if( !$c || !$d )
	{
		print 'Verbindung nicht m&#xF6;lich oder falsche Datenbank. (Userdaten Inkorrekt)';
	}
	else
	{	
		$r_user = mysql_query("SELECT userid, userpassword FROM ".$pref."user ORDER BY userid LIMIT $offset, 100");
		echo mysql_error();
		if( mysql_num_rows($r_user) < 1 )
		{
			print 'Update erfolgreich.<br><br><font color="red">Dieses Script sollte nun gel&#xF6;scht werden.</font>';
		}
		else
		{
			while( $user = mysql_fetch_array($r_user) )
			{
				mysql_query("UPDATE ".$pref."user SET userpassword='".md5($user['userpassword'])."' WHERE userid='".$user['userid']."'");
			}
			
			$offset += 100;
			print mysql_num_rows($r_user).' User bearbeitet, <a href="update2627.php?action=convert&mysql_host='.$mysql_host.'&mysql_database='.$mysql_database.'&mysql_user='.$mysql_user.'&mysql_password='.$mysql_password.'&offset='.$offset.'">hier</a> klicken um weitere User zu bearbeiten.<br><br>(einfach "hier" weiterklicken)';
		}
	}	 	
}

?>
</body>
</html>
