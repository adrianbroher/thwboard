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

define('THWB_NOSESSION_PAGE', true);

include "./inc/header.inc.php";

$TFrame = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$TNews = new Template("templates/" . $style['styletemplate'] . "/news.html");
$TNewsrow = new Template("templates/" . $style['styletemplate'] . "/newsrow.html");

if(!isset($board['boardid']))
{
  $board['boardid'] = -1;
}

$r_news = thwb_query("SELECT newsid, newstopic, newstext, newstime FROM ".$pref."news WHERE boardid LIKE '%;" . $board['boardid'] . ";%' ORDER BY newstime DESC ");
$NEWSROWS = '';
$i = 0;
while( $news = mysql_fetch_array($r_news) )
{
	$i++;
	
	$news['newstopic'] = parse_code($news['newstopic'], 0, 0, 1, 1);
	$news['newstext'] = parse_code($news['newstext'], 1, 1, 1, 1);
	$news['newstime'] = form_date($news['newstime']);
	
	$bgcolor = $i % 2 == 0 ? $style['CellA'] : $style['CellB'];
	eval($TNewsrow->GetTemplate("NEWSROWS"));
}

$navpath .= 'Announcements';

eval($TNews->GetTemplate("CONTENT"));
eval($TFrame->GetTemplate());
