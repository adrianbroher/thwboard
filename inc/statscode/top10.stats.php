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

if( $get_part == 'head' )
{
    $selectionpoints['top10']   = array('base'=>'top10',   'title'=>'Top 10 - Listen');
}
else
{
  $TStats = new Template('./templates/'.$style['styletemplate'].'/stats_top10.html');
  $TRow = new Template('./templates/'.$style['styletemplate'].'/stats_top10row.html');

  $stats_top10_row = '';
  $data = array();

  $P = new Permission($g_user['groupids']);

  if($config['showpostslevel'] == 2 || $g_user['userisadmin'])
    {

  /** top 10 posters **/

  $data['title'] = 'Top 10 Poster';

  $name = array();
  $value = array();
  $a_posters = array();

  $i = 0;

  $r_posters = thwb_query("SELECT userid, username, userposts FROM $pref"."user ORDER BY userposts DESC LIMIT 10");
  while($a_posters = mysql_fetch_array($r_posters))
    {
      $name[$i] = '<a href="'.build_link('v_profile.php?userid='.$a_posters['userid']).'">'.$a_posters['username'].'</a>';
      $value[$i] = $a_posters['userposts'];
      $i++;
    }

  for(;$i <= 9; $i++)
    {
      $name[$i] = 'n/a';
      $value[$i] = 'n/a';
    }

  mysql_free_result($r_posters);

  eval($TRow->GetTemplate("stats_top10_row"));

  $stats_top10_row .= "<br>";
    }

  /** top 10 boards **/

  $data['title'] = 'Top 10 Boards (nach Posts)';

  $name = array();
  $value = array();
  $a_boards = array();

  $i = 0;

  $r_boards = thwb_query("SELECT b.boardid, b.boardname, b.boardposts, c.categoryname FROM $pref"."board AS b LEFT OUTER JOIN $pref"."category AS c ON c.categoryid = b.categoryid ORDER BY boardposts DESC LIMIT 10");
  while($a_boards = mysql_fetch_array($r_boards))
    {
      $P->set_boardid($a_boards['boardid']);

      if(!$P->has_permission(P_VIEW))
    {
      continue;
    }

      $name[$i] = '<a href="'.build_link('board.php?boardid='.$a_boards['boardid']).'">'.$a_boards['boardname'].'</a>'.' (Kategorie: '.$a_boards['categoryname'].')';
      $value[$i] = $a_boards['boardposts'];
      $i++;
    }

  for(;$i <= 9; $i++)
    {
      $name[$i] = 'n/a';
      $value[$i] = 'n/a';
    }

  mysql_free_result($r_boards);

  eval($TRow->GetTemplate("stats_top10_row"));

  $stats_top10_row .= "<br>";


  /** top 10 threads (by posts) **/

  $data['title'] = 'Top 10 Threads (nach Posts)';

  $name = array();
  $value = array();
  $a_postthreads = array();

  $i = 0;

  $r_postthreads = thwb_query("SELECT t.threadreplies, t.threadtopic, t.threadid, t.boardid, b.boardname FROM $pref"."thread AS t LEFT OUTER JOIN $pref"."board AS b ON t.boardid = b.boardid ORDER BY threadreplies DESC LIMIT 10");
  while($a_postthreads = mysql_fetch_array($r_postthreads))
    {
      $P->set_boardid($a_postthreads['boardid']);

      if(!$P->has_permission(P_VIEW))
    {
      continue;
    }

      $name[$i] = '<a href="'.build_link('showtopic.php?threadid='.$a_postthreads['threadid']).'">'.parse_code($a_postthreads['threadtopic']).'</a> (Board: <a href="'.build_link('board.php?boardid='.$a_postthreads['boardid']).'">'.$a_postthreads['boardname'].'</a>)';
      $value[$i] = $a_postthreads['threadreplies'] + 1;
      $i++;
    }

  for(;$i <= 9; $i++)
    {
      $name[$i] = 'n/a';
      $value[$i] = 'n/a';
    }

  mysql_free_result($r_postthreads);

  eval($TRow->GetTemplate("stats_top10_row"));

  $stats_top10_row .= "<br>";

  /** top 10 threads (by views) **/

  $data['title'] = 'Top 10 Threads (nach Views)';

  $name = array();
  $value = array();
  $a_viewthreads = array();

  $i = 0;

  $r_viewthreads = thwb_query("SELECT t.threadviews, t.threadtopic, t.threadid, t.boardid, b.boardname FROM $pref"."thread AS t LEFT OUTER JOIN $pref"."board AS b ON t.boardid = b.boardid ORDER BY threadviews DESC LIMIT 10");
  while($a_viewthreads = mysql_fetch_array($r_viewthreads))
    {
      $P->set_boardid($a_viewthreads['boardid']);

      if(!$P->has_permission(P_VIEW))
    {
      continue;
    }

      $name[$i] = '<a href="'.build_link('showtopic.php?threadid='.$a_viewthreads['threadid']).'">'.parse_code($a_viewthreads['threadtopic']).'</a> (Board: <a href="'.build_link('board.php?boardid='.$a_viewthreads['boardid']).'">'.$a_viewthreads['boardname'].'</a>)';
      $value[$i] = $a_viewthreads['threadviews'];
      $i++;
    }

  for(;$i <= 9; $i++)
    {
      $name[$i] = 'n/a';
      $value[$i] = 'n/a';
    }

  mysql_free_result($r_viewthreads);

  eval($TRow->GetTemplate("stats_top10_row"));

  eval($TStats->GetTemplate("stats"));
}
