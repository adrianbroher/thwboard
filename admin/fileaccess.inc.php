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

function WriteAccess($file)
{
	$fp = @fopen($file, "a");
	if( !$fp )
	{
		return FALSE;
	}
	else
	{
		fclose($fp);
		return TRUE;
	}
}
?>
