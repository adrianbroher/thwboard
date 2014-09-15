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

if( $get_part == 'head' )
{
    $selectionpoints['default'] = array('base'=>'default', 'title'=>'Allgemeines');
}
else
{
    $t_stats = new Template('./templates/' . $style['styletemplate'] . '/stats_default.html');
    
    $a_stats = array();
    
    // create $a_stats['userposts']
    $r_stats = mysql_query("SELECT userposts FROM " . $pref . "user WHERE userid=$g_user[userid]");
    $g_user['userposts'] = @mysql_result($r_stats, 0);
    mysql_free_result($r_stats);
    if( $g_user['userid'] != 0 && $g_user['userposts'] != 0 )
    {
        $a_stats['userposts'] = $g_user['userposts'];
    }
    else
    {
        $a_stats['userposts'] = '<i>Nicht eingeloggt oder noch kein Post get&auml;tigt.</i>';
    }
    
    // create $a_stats['firstactivate']
    $r_stats = thwb_query("SELECT userjoin FROM " . $pref . "user ORDER BY userjoin ASC LIMIT 1");
    if( mysql_num_rows($r_stats) != 0 )
    {
        $a_stats['firstactivate'] = form_date(mysql_result($r_stats, 0));
    }
    else
    {
        $a_stats['firstactivate'] = '-';
    }
    mysql_free_result($r_stats);
    
    // create $a_stats['usercount']
    $r_stats = thwb_query("SELECT count(userid) FROM " . $pref . "user");
    if( mysql_num_rows($r_stats) != 0 )
    {
        $a_stats['usercount'] = mysql_result($r_stats, 0);
    }
    else
    {
        $a_stats['usercount'] = '-';
    }
    mysql_free_result($r_stats);
    
    // create $a_stats['activeusers']
    $r_stats = thwb_query("SELECT count(userid) AS activeusers FROM " . $pref . "user WHERE userlastpost > " . (time() - 60 * 60 * 24 * 31));

    $a_stats = array_merge($a_stats, mysql_fetch_assoc($r_stats));

    mysql_free_result($r_stats);

    //   create $a_stats['admin_beitrag_text']
    // + create $a_stats['numposts']
    // + create $a_stats['admin_themen_text']
    // + create $a_stats['numthreads']
    // + create $a_stats['admin_views_text']
    // + create $a_stats['numviews']
    // + create $a_stats['admin_kategorien_text']
    // + create $a_stats['numkateg']
    // + create $a_stats['admin_board_text']
    // + create $a_stats['numboards']

    $a_stats['numboards'] = 0;
    $categories = array();
    $boards = array();
    $r_stats = thwb_query("SELECT boardid, categoryid FROM " . $pref . "board WHERE boarddisabled = 0");
    while( $datarow = mysql_fetch_array($r_stats) )
    {
        $P->set_boardid($datarow['boardid']);
        if( $P->has_permission( P_VIEW ) || $g_user['userisadmin'] )
        {
            if( !in_array($datarow['categoryid'], $categories) )
            {
                $categories[] = $datarow['categoryid'];
            }
            if( !in_array($datarow['boardid'], $boards) )
            {
                $boards[] = $datarow['boardid'];
            }
        }
    }
    $a_stats['numboards'] = count($boards);
    mysql_free_result($r_stats);
    unset($datarow);
    
    $a_stats['numkateg'] = count($categories);
    unset($categories);
    
    $r_stats = thwb_query("SELECT threadid FROM " . $pref . "thread WHERE boardid IN ('" . implode("','", $boards) . "')");
    $threads = array();
    while( $datarow = mysql_fetch_array($r_stats) )
    {
        $threads[] = $datarow['threadid'];
    }
    mysql_free_result($r_stats);
    unset($boards);
    $a_stats['numthreads'] = count($threads);
    
    $r_stats = thwb_query("SELECT count(postid) FROM " . $pref . "post WHERE threadid IN ('" . implode("','", $threads) . "')");
    if( mysql_num_rows($r_stats) != 0 )
    {
        $a_stats['numposts'] = mysql_result($r_stats, 0);
    }
    mysql_free_result($r_stats);
    $r_stats = thwb_query("SELECT sum(threadviews) FROM " . $pref . "thread WHERE threadid IN ('" . implode("','", $threads) . "')");
    if( mysql_num_rows($r_stats) != 0 )
    {
        $a_stats['numviews'] = mysql_result($r_stats, 0);
    }
    mysql_free_result($r_stats);
    unset($threads);
    if( $g_user['userisadmin'] )
    {
        $a_stats['admin_board_text'] = $a_stats['admin_kategorien_text'] = $a_stats['admin_themen_text'] = $a_stats['admin_views_text'] = $a_stats['admin_beitrag_text'] = ' (alle)';
    }
    else
    {
        $a_stats['admin_board_text'] = $a_stats['admin_kategorien_text'] = $a_stats['admin_themen_text'] = $a_stats['admin_views_text'] = $a_stats['admin_beitrag_text'] = '';
    }

    // create $a_stats['admins']
    $r_stats = thwb_query("SELECT userid, username FROM " . $pref . "user WHERE userisadmin = 1 AND usernodelete = 0 ORDER BY username ASC");
    $a_stats['admins'] = '';
    while( $datarow = mysql_fetch_array($r_stats) )
    {
        $a_stats['admins'] .= '<a href="v_profile.php?userid=' . $datarow['userid'] . '" target="_blank">' . $datarow['username'] . '</a>, ';
    }
    $a_stats['admins'] = substr($a_stats['admins'], 0, -2);
    mysql_free_result($r_stats);
    unset($datarow);
    
    // create $a_stats['uradmins']
    $r_stats = thwb_query("SELECT userid, username FROM " . $pref . "user WHERE userisadmin = 1 AND usernodelete = 1 ORDER BY username ASC");
    $a_stats['uradmins'] = '';
    while( $datarow = mysql_fetch_array($r_stats) )
    {
        $a_stats['uradmins'] .= '<a href="'.build_link('v_profile.php?userid=' . $datarow['userid']).'" target="_blank">' . $datarow['username'] . '</a>, ';
    }
    $a_stats['uradmins'] = substr($a_stats['uradmins'], 0, -2);
    mysql_free_result($r_stats);
    unset($datarow);
    
    // create $a_stats['newmember']
    $r_stats = thwb_query("SELECT userid, username FROM " . $pref . "user ORDER BY userjoin DESC LIMIT 5");
    $a_stats['newmember'] = '';
    while( $datarow = mysql_fetch_array($r_stats) )
    {
        $a_stats['newmember'] .= '<a href="'.build_link('v_profile.php?userid=' . $datarow['userid']) . '" target="_blank">' . $datarow['username'] . '</a>, ';
    }
    $a_stats['newmember'] = substr($a_stats['newmember'], 0, -2);
    mysql_free_result($r_stats);
    unset($datarow);
    
    eval($t_stats->GetTemplate('stats'));
}
