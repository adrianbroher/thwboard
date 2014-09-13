<?php
/*
          phpInstaller - PHP Script Installer
        ==============================================
          (c) 2000-2004 by ThWboard Development Group


          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================
*/
require('install_functions.php');

include '../inc/config.inc.php';

mysql_connect($mysql_h, $mysql_u, $mysql_p);
mysql_select_db($mysql_db);

if( !$pref )
{
	$pref = 'thwb_';
}

$r_registry = thwb_query("SELECT keyvalue FROM $pref"."registry WHERE keyname='version'");
list($version) = mysql_fetch_row($r_registry);
$version = (float)($version);

if( $version < 2.8 )
{
	$r_user = thwb_query("SELECT userpassword FROM $pref"."user WHERE username='" . addslashes( $l_username ) . "' AND userlevel=1");
}
else
{
	$r_user = thwb_query("SELECT userpassword FROM $pref"."user WHERE username='" . addslashes( $l_username ) . "' AND userisadmin=1");
}
if( mysql_num_rows($r_user) )
{
	$user = mysql_fetch_array($r_user);
	if( $user['userpassword'] != md5($l_userpassword) )
	{
		$action = 'login';
	}
}
else
{
	$action = 'login';
}

switch($action)
{
	case 'login':
		p_header();
		p_loginform();
		p_footer('welcome');
		break;

	case 'startupdate':
		include $scriptname;
		$update = new CUpdate;

		$update->Prefix = $pref;
		if( !$update->AllowUpdate() )
		{
			p_errormsg(lng('error'),
				lng('cantexec'));
		}
		
		if( $update->RunUpdate() )
		{
			p_errormsg(lng('error'),
				$update->GetError());
		}
		else
		{
			p_errormsg(lng('updatesuccess'),
				lng('updatesuccesstxt'));
		}
		
		break;

	case 'update':
		$scriptname = 'updates/'.$scriptname;
		if( !file_exists($scriptname) || !$scriptname )
		{
			p_errormsg(lng('error'), lng('notfound'));
		}
		else
		{
			include $scriptname;
			
			$update = new CUpdate;
			
			$update->Prefix = $pref;
			if( $update->UpdaterVer > $cfg['updater_ver'] )
			{
				p_errormsg(lng('error'), lng('tooold'));
			}
			else
			{
				p_header();
				p_updateinfo($update);
				p_footer('startupdate', array(
					'scriptname' => $scriptname,
					'l_username' => $l_username,
					'l_userpassword' => $l_userpassword
				));
			}
		}
		break;

	case 'welcome':
	default:
		$a_file = array();
		$dp = opendir('updates/'); 
		while( $file = readdir($dp) )
		{
			if( substr($file, -7, 7) == '.update' )
			{
				$a_file[] = $file;
			}
		}

		natsort($a_file);
		p_header();
		p_updatewelcome($a_file);
		p_footer('update', array(
			'l_username' => $l_username,
			'l_userpassword' => $l_userpassword
		));
		break;
}
?>
