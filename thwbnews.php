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
 * this script supports two modes of operation:
 *  (a) the script is directly requested by a client.
 *      in this case, parameters might be given that
 *      denote the output type (either html or rss)
 *      and might limit the boards for which the content
 *      is generated.
 *
 *  (b) the script is included by another script.
 *      in this case, a fixed boardid is used and output
 *      is restricted to html.
 *      to use this feature, the including script must
 *      define
 *          THWB_NEWS_INCLUDED  to  1
 *          THWB_NEWS_PATH      to  `.' if the including
 *              is located in the same directory as this
 *              script (which must be the board directory)
 *              or to the path (relative from the including
 *              script) to the board directory.
 *      the script's output (which is, in this case, only
 *      the gathered data) is contained in a global variable
 *      denoted by $THWB_NEWS_OUTPUT_VAR, which defaults to
 *      $THWB_NEWS_OUTPUT.
 **/

define('ALLOW_HTML', 0);            //!< set this to one to enabled HTML output.
define('CFG_INCLUDE_BOARD', -1);    //!< boardid to use for include mode.
define('CFG_NEWS_ITEMS', 10);       //!< maximum number of news items to display.
define('CFG_NEWS_LENGTH', 120);     //!< newstext is shortened to this amount of characters.

/**
 * strips thwb code tags out of the argument
 *
 * @param str   string to strip
 **/

function strip_code(&$str, $no_tags = false)
{
    /**
     * ``simple'' tags
     **/

    $a_tags = array('php', 'code', 'b', 'i', 'u', '-', 'noparse', 'mail', 'color');
    $tags = array();

    if($no_tags)
    {
        $a_tags[] = 'url';
        $a_tags[] = 'img';
        $a_tags[] = 'quote';
    }

    foreach($a_tags as $tag)
    {
        $tags[] = '['.$tag.']';
        $tags[] = '[/'.$tag.']';
    }

    $str = str_replace($tags, '', $str);

    /**
     * ``complex'' tags
     **/

    $a_ctags = array('mail', 'color');

    if($no_tags)
    {
        $a_ctags[] = 'url';
    }

    $str = preg_replace('/\[('.join('|', $a_ctags).')="(.*)"\]/', '', $str);
}

/**
 * cuts the given string at the first possible point after CFG_NEWS_LENGTH
 **/

function cut_str(&$str)
{
    if(strlen($str) <= CFG_NEWS_LENGTH)
    {
        return;
    }

    $um = first_cut('url', $str);
    $im = first_cut('img', $str);
    $qm = first_cut('quote', $str);

    $cut = max($um, $im, $qm);

    $str = substr($str, 0, $cut);

    $str .= ' [...]';
}

/**
 * returns the minimum length to retain url, quote and img tags.
 **/

function first_cut($tag, $str)
{
    if(strlen($str) <= CFG_NEWS_LENGTH)
    {
        return;
    }

    $sstr = substr($str, 0, CFG_NEWS_LENGTH);

    $qs = array();
    $eqs = array();
    $qd = 0;
    $i = '';
    $found = false;

    if($tag != 'url')
    {
        preg_match_all('/\['.$tag.'\]/', $sstr, $qs);
    }
    else
    {
        preg_match_all('/\['.$tag.'\]/', $sstr, $qs);

        $qs2 = array();

        preg_match_all('/\[url="(.*)"\]/', $sstr, $qs);

        $qs = array_merge($qs, $qs2);
    }

    preg_match_all('/\[\/'.$tag.'\]/', $sstr, $eqs);

    if(($qd = (count($qs) - count($eqs))) > 0)
    {
        // we need $qd tags more.

        $i = substr($str, 'CFG_NEWS_LENGTH');

        while($qd > 0)
        {
            $i = strstr($i, '[/'.$tag.']');

            --$qd;

            $i = substr($i, (1 + strlen('[/'.$tag.']')));
        }

        $found = true;
    }

    return ((($found) ? (strlen($str) - strlen($i)) : 0));
}

$incpref = ((defined('THWB_NEWS_INCLUDED')) ? THWB_NEWS_PATH : '.');

if (!@include($incpref.'/inc/config.inc.php')) {
    print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    ';
    print '<html>';
    print '<head>';
    print '<title>ThWboard wurde noch nicht installiert</title>';
    print '</head>';
    print '<body>';
    print 'Das Forum ist noch nicht eingerichtet! Bitte <a href="./admin/install.php">installieren</a> Sie zuerst die Forensoftware um das Forum nutzen zu k&ouml;nnen.';
    print '</body>';
    print '</html>';
    exit;
}

require $incpref.'/inc/functions.inc.php';
require $incpref.'/inc/thwbcode.inc.php';

if( !$pref )
    $pref = 'thwb_';

$mysql = @mysql_connect($mysql_h, $mysql_u, $mysql_p);

$db = @mysql_select_db($mysql_db);

$mysql_h = ''; $mysql_u = ''; $mysql_p = ''; $mysql_db = '';

if(!$mysql || !$db )
{
    print '<b>Sorry</b><br><br>Es gibt momentan leider ein kleines Datenbank-Problem, bitte versuche es sp&#xE4;ter noch einmal.';
    ob_end_flush();
    exit;
}

/**
 * read registry
 **/

$r_config = thwb_query("SELECT keyname, keyvalue FROM ${pref}registry");
$config = array();

while($a_config = mysql_fetch_assoc($r_config))
{
    $config[$a_config['keyname']] = $a_config['keyvalue'];
}

if(substr($config['board_baseurl'], -1) == '/')
{
    $config['board_baseurl'] = substr($config['board_baseurl'], 0, -1);
}

if(defined('THWB_NEWS_INCLUDED') && empty($THWB_NEWS_OUTPUT_VAR))
{
    $THWB_NEWS_OUTPUT_VAR = 'THWB_NEWS_OUTPUT';
}

/**
 * construct Permission object.
 **/

$P = new Permission($config['guest_groupid']);

/**
 * now determine which boards we want to scan.
 **/

$a_boardids = array();

if(defined(THWB_NEWS_INCLUDED))
{
    $a_boardids[] = CFG_INCLUDE_BOARD;
}
else
{
    if(!empty($_GET['limit']))
    {
        $a_boardids = explode(',', $_GET['limit']);
    }
}

$r_boards = thwb_query("SELECT boardid FROM ${pref}board ".
                       "WHERE boarddisabled = 0".
                       ((count($a_boardids) ?
                       " AND boardid IN (".join(',', $a_boardids).")": '')));

$a_boards = array();

while($a_board = mysql_fetch_assoc($r_boards))
{
    if(count($a_boardids) && !in_array($a_board['boardid'], $a_boardids))
    {
        continue;
    }

    $P->set_boardid($a_board['boardid']);

    if(!$P->has_permission(0))
    {
        continue;
    }

    $a_boards[] = $a_board['boardid'];
}

if(!count($a_boards) && !defined('THWB_NEWS_INCLUDED'))
{
    print('<pre><strong>Fehler</strong>
          Keine Boards gefunden.</pre>');
    exit;
}

/**
 * determine the order
 **/

$orderby = 'threadcreationtime';

if(!empty($_GET['lastchanged']))
{
    $orderby = 'threadtime';
}

/**
 * now fetch the threads
 **/

$r_threads = thwb_query("SELECT threadtopic, threadid
                        FROM ${pref}thread
                        WHERE boardid IN (".join(',', $a_boards).")
                        AND threadlink = 0
                        ORDER BY $orderby DESC
                        LIMIT ".CFG_NEWS_ITEMS);

$a_threads = array();

while($a_thread = mysql_fetch_assoc($r_threads))
{
    /**
     * ok, we got the threads, now get the posts
     **/

    $r_post = thwb_query("SELECT p.postid, p.posttext, p.userid, u.username, p.posttime
                         FROM ${pref}post AS p, ${pref}user AS u
                         WHERE p.threadid = $a_thread[threadid]
                         AND p.userid = u.userid
                         ORDER BY p.posttime ASC
                         LIMIT 1");

    $a_post = mysql_fetch_assoc($r_post);

    strip_code($a_post['posttext'], (!empty($_GET['type']) && $_GET['type'] == 'rss'));

    cut_str($a_post['posttext']);

    /**
     * if we're doing something else than generating rss output, we need the replycount
     **/

    if(defined(THWB_NEWS_INCLUDED) || empty($_GET['type']) || $_GET['type'] == 'html')
    {
        $r_comments = thwb_query("SELECT COUNT(postid) AS commentcount
                                 FROM ${pref}post
                                 WHERE threadid = $a_thread[threadid]");

        $a_comments = mysql_fetch_assoc($r_comments);

        if(!(--$a_comments['commentcount']))
        {
            $a_comments['commentcount'] = 'keine';
        }

        $a_comments['commentplural'] = (($a_comments['commentcount'] == 1) ? '' : 'e');
    }
    else
    {
        $a_comments = array();
    }

    $a_threads[] = array_merge($a_thread, $a_post, $a_comments);
}

/**
 * now we've got all the information we need, so we can now generate the output.
 **/

if(defined(THWB_NEWS_INCLUDED))
{
    /**
     * do not output anything, simply return the data to the parent page
     **/

    $$THWB_NEWS_OUTPUT = parse_code($a_threads, 1);
}
else if(empty($_GET['type']) || $_GET['type'] == 'html')
{
    /**
     * html output
     *
     * ... and for that, we need $style set.
     **/

    if(!ALLOW_HTML)
    {
        print('<pre><strong>Fehler</strong>
              HTML-Ausgabe deaktiviert.</pre>');
        exit;
    }

    $r_style = thwb_query("SELECT styleid, styletemplate, colorbg, color1, CellA, CellB, color4, colorbgfont, col_he_fo_font, color_err,
        col_link, col_link_v, col_link_hover, stdfont,
        boardimage, newtopicimage, border_col FROM
        ".$pref."style WHERE styleisdefault=1");

    $style = mysql_fetch_assoc($r_style);
    $style['smallfont'] = '<span class="smallfont">';
    $style['smallfontend'] = '</span>';
    $style['font'] = $style['stdfont'];
    $style['stdfont'] = '<span class="stdfont">';
    $style['stdfontend'] = '</span>';

    $TFrame = new Template($incpref.'/templates/default/thwbnews.html');
    $Trow = new Template($incpref.'/templates/default/thwbnewsrow.html');

    $CONTENT = '';

    foreach($a_threads as $post)
    {
        $post['posttime'] = form_date($post['posttime']);
        $post['posttext'] = parse_code($post['posttext'], 1, 1, 1);

        eval($Trow->GetTemplate('CONTENT'));
    }

    eval($TFrame->GetTemplate());
}
else if($_GET['type'] == 'rss')
{
    /**
     * rss output
     **/

    header ('Cache-Control: no-cache, pre-check=0, post-check=0, max-age=0');
    header ('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
    header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header ('Content-Type: text/xml; charset=iso-8859-1');


    echo '<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">
    <channel>
        <title>'.$config[board_name].'</title>
        <link>'.$config[board_baseurl].'</link>
        <description>ThWboard generated RSS feed for '.$config[board_name].'</description>
        <language>de</language>';

    foreach($a_threads as $thread)
    {
        $thread['posttext'] = parse_code($thread['posttext'], 1);

        echo '
            <item>
                <title>'.$thread['threadtopic'].'</title>
                <link>'.$config['board_baseurl'].'/showtopic.php?threadid='.$thread['threadid'].'</link>
                <description>'.$thread['posttext'].'</description>
            </item>';
    }

    echo "\n".'    </channel>
</rss>';
}
else
{
    print('<pre><strong>Fehler</strong>
          Keine Boards gefunden.</pre>');
    exit;
}
