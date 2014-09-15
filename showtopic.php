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

if ( !isset($thread['threadid']) || ( !$thread['threadid'] ) || ( $board['boarddisabled'] == 1 ) )
{
    message('Error', 'Thread existiert nicht');
}

// ttt: this is a link, forward!
if( $thread['threadlink'] ) {
    header('Location: '.build_link('showtopic.php?threadid='. $thread['threadlink'], true));
    exit;
}

$postings = $thread['threadmessages'] = ++$thread['threadreplies'];
$post_pages = ceil($postings / $config['vars_m_amount']);
$lastpost = '';

if( !isset($pagenum) )
{
    $pagenum = 1;
}
else if( $pagenum == 'lastpage' )
{
    $pagenum = $post_pages;
}

if( $config['enable_ranks'] )
{
    $a_rank = array();
    $r_rank = thwb_query("SELECT ranktitle, rankimage, rankposts FROM ".$pref."rank ORDER BY rankposts DESC");
    while( $rank = mysql_fetch_array($r_rank) )
    {
        $a_rank[$rank['rankposts']] = $rank['ranktitle'] . ( $rank['rankimage'] ? '<br /><img src="' . $rank['rankimage'] . '" border="0">' : '');
    }
}

$a_group = array();
$r_group = thwb_query("SELECT groupid, title FROM $pref"."group ORDER BY titlepriority DESC");
while( $group = mysql_fetch_array($r_group) )
{
    $a_group[] = $group;
}

$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$Tpostings = new Template("templates/" . $style['styletemplate'] . "/postings.html");
$Tpostingrow =new Template("templates/" . $style['styletemplate'] . "/postingrow.html");
$Tpostingoptions = new Template('templates/'.$style['styletemplate'].'/postingoptions.html');

$i = ($pagenum - 1) * $config['vars_m_amount'];

$r_post = thwb_query("SELECT
        post.posttime,
        post.posttext,
        post.userid,
        post.postid,
        post.postlasteditby,
        post.postlastedittime,
        post.postsmilies,
        post.postcode,
        post.postguestname,
        post.postip,
        user.username,
        user.usersignature,
        user.usertitle,
        user.userposts,
        user.userrating,
        user.useravatar,
        user.groupids,
        user.userhomepage,
        user.userisadmin
    FROM
        ".$pref."post as post
    LEFT JOIN
        ".$pref."user as user ON (post.userid=user.userid)
    WHERE
        post.threadid=$thread[threadid]
    ORDER BY
        post.posttime ASC
    LIMIT
        ".(intval($pagenum) - 1) * $config['vars_m_amount'].", $config[vars_m_amount]");

if( !$r_post )
{
    message('Sorry', 'Thread existiert nicht!');
}

if( isset($highlight) )
{
    $a_highword = explode(' ', $highlight);
    if( count($a_highword) > 20 )
        $a_highword = array();
}

$count = mysql_num_rows($r_post);

$POSTINGROWS = '';
while( $post = mysql_fetch_array($r_post) )
{
    $post['username'] = parse_code($post['username']);
    $post['posttext'] = parse_code($post['posttext'], 1, ($post['postcode'] ? 1 : 0), ($post['postcode'] ? 1 : 0), ($post['postsmilies'] ? 1 : 0));
    $post['useravatar'] = parse_code( $post['useravatar'] );
    $post['posttime'] = form_date($post['posttime']);
    $post['postnumber'] = sprintf("%03d", $i);
    if( $post['userid'] == 0 )
    {
        $post['groupids'] = ','.$config['guest_groupid'].',';
    }

    if( !$post['usertitle'] )
    {
        reset($a_group);
        while( list(, $group) = each($a_group) )
        {
            if( strstr($post['groupids'], ','.$group['groupid'].',' ) )
            {
                $post['usertitle'] = $group['title'].'<br />';
                break;
            }
        }
    }
    else
    {
        $post['usertitle'] .= '<br />';
    }

    if( $config['enable_ranks'] )
    {
        reset($a_rank);
            
        while( list($posts, $rank) = each($a_rank) )
        {
            if( $post['userposts'] >= $posts )
            {
                $post['userrank'] = $rank.'<br />';
                break;
            }
        }
        
        if(empty($post['userrank']))
          {
            $post['userrank'] = '';
          }
    }
    else
    {
      //        $user['userrank'] = '';
        $post['userrank'] = '';
    }


    if( $post['userid'] == 0 )
    {
        $post['username'] = $post['postguestname'];
        $postingoptions = '(Gast)';
    }
    else
    {
        $postingoptions = '';
        eval($Tpostingoptions->GetTemplate('postingoptions'));
    }
    

    $post['avatar'] = "";

    if ( ( $config['useravatar'] >= 1 ) && ( $post['useravatar'] != "" ) && ( $post['useravatar'] != "notallowed" ) )
    {
        if ( $post['userhomepage'] ) 
        {
            $post['avatar'] .= "<a href=\"$post[userhomepage]\" target=\"_blank\">";
        }
        
        $post['avatar'] .= "<img src=\"$post[useravatar]\" alt=\"Avatar von $post[username]\" title=\"Avatar von $post[username]\" border=\"0\">";
        
        if ( $post['userhomepage'] ) 
        {
            $post['avatar'] .= "</a>";
        }

         $post['avatar'] .="\n";
    }
    
    if( $post['username'] == '' )
    {
        $post['username'] == '(N/A)';
    }
    
    if( defined('ENABLE_VOTING') )
    {
        if( $post['userrating'] != 0 )
        {
            $post['userrating'] = sprintf("%.1f", ($post['userrating'] / 10));
        }
        else
        {
            $post['userrating'] = "-";
        }
    }
    else
    {
        $post['userrating'] = "";
    }

    if( isset($highlight) )
    {
        $post['posttext'] = highlight_words($post['posttext'], $a_highword);
    }

    // show a signature?
    if( $post['usersignature'] )
    {
        if( isset($g_user['userhidesig']) && $g_user['userhidesig'] != 1 )
        {
            $post['posttext'] .= "<br/>--<br/>" . parse_code($post['usersignature'], 1, ($config['imageslevel'] ? 0 : 1), 1, $config['smilies']);
        }
    }

    if( $post['postlastedittime'] )
    {
        $post['posteditnotes'] = "<hr size=\"1\">$style[smallfont]Dieser Post wurde am " . date("d.m.Y", $post['postlastedittime'] + $config['timeoffset'] * 3600) . " um " . date("H:i", $post['postlastedittime'] + $config['timeoffset'] * 3600) . " Uhr von $post[postlasteditby] editiert.$style[smallfontend]";
    }
    else
    {
        $post['posteditnotes'] = "";
    }

    if(!--$count)
      {
        $lastpost = '<a name="bottom"></a>';
      }

    eval($Tpostingrow->GetTemplate("POSTINGROWS"));
    $i++;
}

// max. number of pages visible at a time / 2 ([1] [2] ...
define('PADDING', 3);
$pages_nav = '';

$linkappend = '';
if( !empty($time) )
    $linkappend = '&time='.$time;
if( isset($highlight) )
    $linkappend .= '&highlight='.urlencode($highlight);

// << <
if( $pagenum - PADDING > 1 )
{
    $pages_nav = '[ <a class="hefo" href="'.build_link('showtopic.php?threadid='.$thread['threadid'].'&amp;pagenum=1'.$linkappend).'">Erste Seite</a> ] ... ';
}
// pages ..
$i = $pagenum - PADDING;
if( $i < 1 )
    $i = 1;
$imax = $pagenum + PADDING;
if( $imax > $post_pages )
    $imax = $post_pages;

for( $i; $i <= $imax; $i++ )
{
    if( $i == $pagenum )
        $pages_nav .= '-'.$i.'- ';
    else
        $pages_nav .= '[ <a class="hefo" href="'.build_link('showtopic.php?threadid='.$thread['threadid'].'&amp;pagenum='.$i.$linkappend).'">'.$i.'</a> ] ';
}
// > >>
if( $pagenum + PADDING < $post_pages )
{
    $pages_nav .= '... [ <a class="hefo" href="'.build_link('showtopic.php?threadid='.$thread['threadid'].'&amp;pagenum=lastpage'.$linkappend).'">Letzte Seite</a> ]';
}



// replyform nur auf der letzten seite..
$REPLYFORM = '';
if ( $pagenum == $post_pages && $P->has_permission( P_REPLY ) && !$thread['threadclosed'] )
{
    if( isset($replyto) )
    {
        $r_post = thwb_query("SELECT post.posttext, post.threadid, post.postguestname, user.username FROM ".$pref."post AS post
            LEFT JOIN ".$pref."user AS user ON post.userid=user.userid WHERE postid='".intval($replyto)."'");
        $post = mysql_fetch_array($r_post);
        
        // verify postid to prevent quotes from restricted forums
        if( $post['threadid'] != $thread['threadid'] )
        {
            message("Error", "Invalid postid!");
        }
        
        $post['posttext'] = htmlspecialchars($post['posttext']);

        if( !$post['username'] )
        {
            $replytext = '[quote][b]' . $post['postguestname'] . ' postete[/b]'."\n".$post['posttext'].'[/quote]'."\n";
        }
        else
        {
            $replytext = '[quote][b]' . $post['username'] . ' postete[/b]'."\n".$post['posttext'].'[/quote]'."\n";
        }
    }
    else
        $replytext = '';

    if( $config['smilies'] )
    {
        $smilies_on_off = "AN";
    }
    else
    {
        $smilies_on_off = "AUS";
    }

    if( $config['use_email'] )
    {
        $notifyavailable = '';
    }
    else
    {
        $notifyavailable = ' (Derzeit nicht verf&uuml;gbar)';
    }
    
    if( $g_user['userid'] )
    {
        $replyusername = "$style[stdfont]$g_user[userhtmlname]$style[stdfontend]$style[smallfont] [ <a href=\"".build_link('logout.php?uid='.$g_user['userid'])."\">Logout</a> ]$style[smallfontend]";
    }
    else
    {
        if( $g_user['userid'] == 0 && $P->has_permission( P_REPLY ) )
        {
            $replyusername = '<input class="tbinput" name="post[postguestname]" type="text">' . $style['smallfont'] .
            ' (Minimal ' . $config['min_usernamelength'] . ', maximal ' . $config['max_usernamelength'] .
            ' Zeichen, keine Sonderzeichen) <b>Das Forum speichert ihre IP-Addresse!</b>' . $style['smallfontend'];
        }
        else
        {
            $replyusername = '';
        }
    }

    $Treply = new Template("./templates/".$style['styletemplate']."/replyform.html");
    eval($Treply->GetTemplate("REPLYFORM"));
}

thwb_query("UPDATE ".$pref."thread SET threadviews=threadviews+1 WHERE threadid='$thread[threadid]'");

$JUMP_MENU = jumpmenu($board['boardid']);

$navpath .= 'Threadansicht';
$titleprepend = htmlspecialchars($thread['threadtopic']) . ' - ';


eval($Tpostings->GetTemplate("CONTENT"));
eval($Tframe->GetTemplate());
