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

/**
 * as of php5, $HTTP_*_VARS are disabled
 * so we have to recreate them here
 * 
 * this is actually pretty evil, but it does work.
 **/

if(substr(phpversion(), 0, 1) > 4)
{
    $a_globals = array(
        'HTTP_SERVER_VARS' => '_SERVER', 
        'HTTP_COOKIE_VARS' => '_COOKIE', 
        'HTTP_POST_VARS' => '_POST',
        'HTTP_GET_VARS' => '_GET',
        'HTTP_ENV_VARS' => '_ENV'
        );

    foreach($a_globals as $k => $v)
    {
        global $$k;

        $$k = $$v;
    }

    unset($a_globals);
}

include('functions.inc.php');
if( !@include('./../inc/config.inc.php') )
{
	print 'Das Forum ist noch nicht installiert! Klicken Sie <a href="./install.php">hier</a>, um mit der Installation zu beginnen.';
	exit;
}

// php 4.1+
if( isset($HTTP_GET_VARS) )
	extract($HTTP_GET_VARS, EXTR_SKIP);
if( isset($HTTP_PUT_VARS) )
	extract($HTTP_PUT_VARS, EXTR_SKIP);
if( isset($HTTP_POST_VARS) )
	extract($HTTP_POST_VARS, EXTR_SKIP);

if( get_magic_quotes_gpc() && is_array($GLOBALS) )
{
	$HTTP_GET_VARS = r_stripslashes($HTTP_GET_VARS);
	$HTTP_POST_VARS = r_stripslashes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = r_stripslashes($HTTP_COOKIE_VARS);
	$GLOBALS = r_stripslashes($GLOBALS);
}

error_reporting(7); // E_ERROR | E_WARNING | E_PARSE
set_magic_quotes_runtime(0);

if( $REMOTE_ADDR == '127.0.0.1' )
{
	$REMOTE_ADDR = $HTTP_X_FORWARDED_FOR;
}

$mysql = @mysql_connect($mysql_h, $mysql_u, $mysql_p);
$db = @mysql_select_db($mysql_db, $mysql);

if( $l_username )
{
	$r_user = query("SELECT userid, username FROM ".$pref."user WHERE username='".addslashes($l_username)."' AND userpassword='" . md5($l_password) . "' AND userisadmin=1");
	if( mysql_num_rows($r_user) == 1 )
	{
		$user = mysql_fetch_array($r_user);
		
		$session = md5(time() . "Kfjasdl(84939qjKJASDldf.y<.yj48hh" . microtime());
		query("INSERT INTO ".$pref."session (sessionid, lastaction, userid, username, ip)
			VALUES ('$session', " . time() . ", '$user[userid]', '".addslashes($user['username'])."', '$REMOTE_ADDR')");

		// delete some old records
		query("DELETE FROM ".$pref."session WHERE lastaction < " . (time() - 60 * 10));
	}
}

$r_session = query("SELECT sessionid, userid, username FROM ".$pref."session WHERE lastaction > " . (time() - 60 * 10) . " AND sessionid='".addslashes($session)."'");
$g_user = mysql_fetch_array($r_session);

$session = $g_user[sessionid];

if( !$g_user[userid] )
{
	loginform();
	exit;
}

query("UPDATE ".$pref."session SET lastaction=" . time() . " WHERE sessionid='$session'");

// log!
 query( "INSERT INTO ".$pref."adminlog (logtype, logtime, loguser, logip, logscript, logaction)
       VALUES ('LOG_ADMIN', ".time().", '".addslashes($g_user['username'])."', '$REMOTE_ADDR', '".basename($PHP_SELF)."', '".addslashes($action)."')" ); 

$r_registry = query("SELECT keyname, keyvalue, keytype FROM " . $pref . "registry");
while ( $registry = mysql_fetch_array($r_registry) )
{
	switch( $registry['keytype'] )
	{
		case 'integer':
		case 'boolean':
			$config[$registry['keyname']] = intval($registry['keyvalue']);
			break;
			
		case 'array':
			$array = explode("\n", $registry['keyvalue']);
			while( list($k, $v) = @each($array) )
				$array[$k] = '"'.addslashes(trim($v)).'"';
			eval("\$config[\$registry['keyname']] = array(".implode(',', $array).");");
			break;
				
		default:
			$config[$registry['keyname']] = $registry['keyvalue'];
	}
}
