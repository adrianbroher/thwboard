<?php
/* $Id: sysinfo.php 87 2004-11-07 00:19:15Z td $ */
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

print '<b>System Info</b><br><br>Please post this information when reporting bugs.';

$php_version = phpversion();
$r = query("SELECT VERSION() as version");
list( $mysql_version ) = mysql_fetch_row($r);

$a_info = array(
	'PHP version' => phpversion(),
	'MySQL version' => $mysql_version,
	'Board version' => $config['version'],
	'Magic quotes GPC' => get_magic_quotes_gpc()
);

print '<pre>[code]'."\n";
while( list($k, $v) = each($a_info) )
{
	printf('%-20s [b]<b>%s</b>[/b]'."\n", $k, $v);
}
print '[/code]</pre>';

/*$a_dir = array('../', '../templates/default/', '../templates/mail/', './');

while( list(, $dir) = each($a_dir) )
{
	$dp = opendir($dir);
	$a_file[$dir] = array();
	while( $file = readdir($dp) )
	{
		if( !is_dir($dir.$file) && $file[0] != '.' )
		{
			$a_file[$dir][] = $file;
		}
	}
	closedir($dp);
	sort($a_file[$dir]);
}*/

tb_footer();

?>