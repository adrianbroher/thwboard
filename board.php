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

if( !isset($board['boardid']) || !$board['boardid'] || $board['boarddisabled'] )
{
    message("Fehler", "Board existiert nicht!");
}

$TMain = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$TTopics = new Template("templates/" . $style['styletemplate'] . "/topics.html");
$TTopicrow = new Template("templates/" . $style['styletemplate'] . "/topicrow.html");

if( !isset($pagenum) )
{
    $pagenum = 1;
}

if(!empty($time))
{
    $lastvisited = $time;
}

if(!empty($lastvisited))
{
    $lastvisited = intval($lastvisited);
    $TIME_STRING = "&amp;time=".$lastvisited;
}
else
{
  $TIME_STRING = '';
}

$r_thread = thwb_query("SELECT threadid, threadtopic, threadtime, threadauthor,
    threadreplies, threadclosed, threadtop, threadlastreplyby, threadiconid, threadlink,
    threadviews FROM ".$pref."thread WHERE
    boardid='".intval($board['boardid'])."' 
    ORDER BY threadtop DESC, threadtime DESC LIMIT
    ".intval(($pagenum - 1) * $config['vars_t_amount']).", $config[vars_t_amount]");

$i = 0;
$topicicon[0] = 'fullalpha';

$TOPICROWS = '';

$r_news = thwb_query("SELECT newsid, newstopic, newstime FROM ".$pref."news WHERE boardid LIKE '%;" . intval($board['boardid']) . ";%' ORDER BY newstime DESC LIMIT 1");
if( mysql_num_rows($r_news) > 0 )
{
    $TNewsrow = new Template('./templates/'.$style['styletemplate'].'/newstopicrow.html');
    $news = mysql_fetch_array($r_news);

    $news['newstopic'] = parse_code($news['newstopic']);
    $news['newstime'] = form_date($news['newstime'], 0);
    eval($TNewsrow->GetTemplate("TOPICROWS"));
}

if( mysql_num_rows($r_thread) < 1 )
{
    $TTopicrow = new Template('./templates/'.$style['styletemplate'].'/board_nothreads.html');
    eval($TTopicrow->GetTemplate("TOPICROWS"));
}

while( $thread = mysql_fetch_array($r_thread) )
{
    $i % 2 > 0 ? $thisrowbg = $style['CellB'] : $thisrowbg = $style['CellA'];
    
    $thread['threadauthor'] = parse_code($thread['threadauthor']);
    $thread['threadlastreplyby'] = parse_code($thread['threadlastreplyby']);

    if( $thread['threadlink'] != 0 )
    {
        if( $P->has_permission( P_OMOVE ) )
            $prepend = 'Verschoben [<a href="'.build_link('threadops.php?action=remove_link&amp;threadid='.$thread['threadid']).'">Link entf.</a>]: ';
        else
            $prepend = 'Verschoben: ';
        $thread['threadid'] = $thread['threadlink'];
        $thread['threadreplies'] = '-';
        $thread['threadviews'] = '-';
        $thread['threadtime'] = 0;
        $thread['threadlastreplyby'] = 'N/A';
    }
    elseif( $thread['threadtop'] )
    {
        $prepend = 'Fest: ';
    }
    else
    {
        $prepend = '';
    }
    
    if( $thread['threadtime'] > $lastvisited && $lastvisited != 0 )
    {
        $imagepath = 'templates/'.$style['styletemplate'].'/images/icon/'.$topicicon[($thread['threadiconid'])].'_new.png';
    }
    else
    {
        $imagepath = 'templates/'.$style['styletemplate'].'/images/icon/'.$topicicon[($thread['threadiconid'])].'.png';
    }

    $thread['threadtopic'] = parse_code($thread['threadtopic']);
    $thread['threadtime'] = form_date($thread['threadtime']);
/*  if ( substr_count( $g_user['usermarkedthreads'], ';' . $thread['threadid'] . ';' ) != 0 )
    {
        $thread['threadtopic'] = '<B>' . $thread['threadtopic'] . $style['smallfont'] .' *markiert* ' . $style['smallfontend'] . '</b>';
    }*/

    $npages = ceil(($thread['threadreplies'] + 1) / $config['vars_m_amount']);
    
    $pages = '';
    if( $npages > 1 )
    {
        for( $j = 0; $j < $npages; $j++ )
        {
            $pages .= '<a href="'.build_link('showtopic.php?threadid='.$thread['threadid'].'&amp;pagenum='.($j + 1).$TIME_STRING).'">'.($j + 1).'</a> ';
            if( $j == 2 && $npages > 6 )
            {
                $pages .= '... ';
                $j = $npages - 4;
            }
        }
        $pages = '(Seiten: ' . $pages . ')';
    }

    if( $thread['threadclosed'] == 1 )
    $imagepath = "templates/".$style['styletemplate']."/images/information_closed.png";

    eval($TTopicrow->GetTemplate("TOPICROWS"));
    $i++;
}

$topic_pages = ceil($board['boardthreads'] / $config['vars_t_amount']);


// max. number of pages visible at a time / 2 ([1] [2] ... 
define('PADDING', 3);
$pages_nav = '';

// << <
if( $pagenum - PADDING > 1 )
{
    $pages_nav = '[<a class="bglink" href="'.build_link('board.php?boardid='.$board['boardid'].'&amp;pagenum=1').'">Erste Seite</a>] ... ';
}
// pages ..
$i = $pagenum - PADDING;
if( $i < 1 )
    $i = 1;
$imax = $pagenum + PADDING;
if( $imax > $topic_pages )
    $imax = $topic_pages;

for( $i; $i <= $imax; $i++ )
{
    if( $i == $pagenum )
        $pages_nav .= '<b>-'.$i.'-</b> ';
    else
        $pages_nav .= '[<a class="bglink" href="'.build_link('board.php?boardid='.$board['boardid'].'&amp;pagenum='.$i).'">'.$i.'</a>] ';
        
}
// > >>
if( $pagenum + PADDING < $topic_pages )
{
    $pages_nav .= '... [<a class="bglink" href="'.build_link('board.php?boardid='.$board['boardid'].'&amp;pagenum='.$topic_pages).'">Letzte Seite</a>]';
}


if( $g_user['userid'] != 0 )
{
    $options_newthread = '<a href="'.build_link('newtopic.php?boardid='.$board['boardid']).'">Neues Topic</a> |';
}

if( $P->has_permission( P_POSTNEW ) )
{
    $canpostnew = 'Ja';
}
else
{
    $canpostnew = 'Nein';
}

if( $P->has_permission( P_REPLY ) )
{
    $canreply = 'Ja';
}
else
{
    $canreply = 'Nein';
}

$JUMP_MENU = jumpmenu($board['boardid']);

$navpath .= 'Themen&uuml;bersicht';
$titleprepend = $board['boardname'] . ' - ';

eval($TTopics->GetTemplate("CONTENT"));
eval($TMain->GetTemplate());
