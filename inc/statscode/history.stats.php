<?php
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
            (c) 2000, 2001 by
               Paul Baecher         <paul@thewall.de>
               Felix Gonschorek   <funner@thewall.de>

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

if( !function_exists("isscached") )
{
function isscached($s, $e, $u)
{
    global $pref;
    return mysql_num_rows(thwb_query("SELECT stat_id FROM " . $pref . "statcache WHERE stat_stime = $s AND stat_etime = $e AND stat_uid = $u LIMIT 1"));
}
}

if( !function_exists("cachestats") )
{
function cachestats($s, $e, $u, $data)
{
    global $pref;
    thwb_query("INSERT INTO " . $pref . "statcache (stat_stime, stat_etime, stat_uid, stat_month, stat_auser, stat_nuser, stat_nthread, stat_npost) VALUES (
            " . $s . ",
            " . $e . ",
            " . $u . ",
            '" . $data['month'] . "',
            " . $data['auser'] . ",
            " . $data['nuser'] . ",
            " . $data['nthread'] . ",
            " . $data['npost'] . "
            )");
}
}

if( !function_exists("getcachedstats") )
{
function getcachedstats($s, $e, $u)
{
    global $pref;
    $r_query = thwb_query("SELECT stat_month as month, stat_auser as auser, stat_nuser as nuser, stat_nthread as nthread, stat_npost as npost FROM " .
            $pref . "statcache WHERE stat_stime = $s AND stat_etime = $e AND stat_uid = $u DESC LIMIT 1");

    return mysql_fetch_array($r_query);
}
}


if( $get_part == 'head' )
{
    $selectionpoints['history'] = array('base'=>'history', 'title'=>'History');
}
else
{
    $t_stats = new Template('./templates/' . $style['styletemplate'] . '/stats_history.html');
    $monate = array("", "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");

    $historyrow = '';
    $data = array();

    $exit = false;
    $negcount = 0;
    $r_firstactive = thwb_query("SELECT userjoin FROM " . $pref . "user ORDER BY userjoin ASC LIMIT 1");
    $maxtime = mysql_result($r_firstactive, 0);
    mysql_free_result($r_firstactive);
    $dc = 'CellA';
    $t_row = new Template('./templates/' . $style['styletemplate'] . '/stats_historyrow.html');
    $iscurmonth = true;
    while( $exit != true )
    {
      $endtime = mktime(0, 0, 0, date("m", time()) + 1 - $negcount, 0, date("Y", time()));
      $starttime = mktime(0, 0, 0, date("m", time()) - $negcount, 1, date("Y", time()));

        if( $iscurmonth || !isscached($starttime, $endtime, $g_user['userid']) )
        {
            $r_stats = thwb_query("SELECT count(u.userid) as nuser FROM " . $pref . "user as u WHERE u.userjoin > $starttime AND u.userjoin <= $endtime");
            if( mysql_num_rows($r_stats) != 0 )
            {
                $data = mysql_fetch_array($r_stats);
                $data['month'] = $monate[date("n", $starttime)] . " " . date("Y", $starttime);
            }
            $r_stats = thwb_query("SELECT count(p.userid) as auser FROM " . $pref . "post as p WHERE userid != 0 AND p.posttime > $starttime AND p.posttime <= $endtime GROUP BY p.userid");
            $data['auser'] = mysql_num_rows($r_stats);

            $r_stats = thwb_query("SELECT count(p.postid) as npost FROM " . $pref . "post as p WHERE p.posttime > $starttime AND p.posttime <= $endtime");
            if( mysql_num_rows($r_stats) != 0 )
            {
                $data = array_merge($data, mysql_fetch_array($r_stats));
            }
            $r_stats = thwb_query("SELECT count(t.threadid) as nthread FROM " . $pref . "thread as t WHERE t.threadcreationtime > $starttime AND t.threadcreationtime <= $endtime");
            if( mysql_num_rows($r_stats) != 0 )
            {
                $data = array_merge($data, mysql_fetch_array($r_stats));
            }
            if( !$iscurmonth )
            {
                cachestats($starttime, $endtime, $g_user['userid'], $data);
            }
        }
        else
        {
            $data = getcachedstats($starttime, $endtime, $g_user['userid']);
        }
        eval($t_row->GetTemplate("historyrow"));
        if( $dc == 'CellA' )
        {
            $dc = 'CellB';
        }
        else
        {
            $dc = 'CellA';
        }
        if( $starttime <= $maxtime )
        {
            $exit = true;
        }
        else
        {
            $negcount++;
        }
        $iscurmonth = false;
    }

    eval($t_stats->GetTemplate('stats'));
}
