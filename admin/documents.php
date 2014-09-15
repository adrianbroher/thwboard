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


if( $action == "ListDocs" )
{
	print '<b>Available documentation files</b> (../doc/)<br><br>
	NEW: German documentation is available <a href="http://www.thwboard.de/documentation.p'.'h'.'p" target="_blank">here</a>.<br><br>';
	
	$d = opendir("../doc/");
	while( $file = readdir($d) )
	{
		if( $file != "." && $file != ".." && $file != 'CVS' )
		{
			echo "~ $file [ <a href=\"documents.php?session=$session&action=ReadDoc&doc=$file\">read</a> ]<br>";
		}
	}
}
elseif( $action == "ReadDoc" )
{
	if( substr($doc, 0, 1) == "/" || strstr($doc, "..") )
	{
		print 'Error: this file cannot be viewed.';
	}
	else
	{
		print '<b>Read Documentation file</b><br><br><hr><blockquote><pre>';
		include "../doc/$doc";
		print '</pre></blockquote><hr>';
	}
	
	print '<a href="documents.php?session=' . $session . '&action=ListDocs">return</a> to the document index';
}

tb_footer();
