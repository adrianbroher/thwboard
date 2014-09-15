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

print '<b>Generic information</b><br>';

print '
ThWboard-version: <font color="darkblue">'.$config['version'].'</font><br>
PHP-version: <font color="darkblue">'.phpversion().'</font><br>
MySQL-version: <font color="darkblue">'.mysql_get_server_info().'</font><br>';

$ver = @file('/proc/version');
if( $ver )
{
    print 'OS-version: <font color="darkblue">'.$ver[0].'</font><br>';
}

tb_footer();
