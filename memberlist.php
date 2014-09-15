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

$config['userperpage'] = 25;
$where = '';

$TFrame = new Template("./templates/".$style['styletemplate']."/frame.html");
$TMemberlist = new Template("./templates/".$style['styletemplate']."/memberlist.html");
$TMemberrow = new Template("./templates/".$style['styletemplate']."/memberrow.html");

$orderbyoptions = array(
    'username' => 'Name',
    'useremail' => 'Email',
    'usericq' => 'ICQ Nummer',
    'userhomepage' => 'Homepage',
    'userlocation' => 'Wohnort',
    'userjoin' => 'Registrierdatum',
    'userlastpost' => 'Letzter Post'
);

if( $config['showpostslevel'] == 2 )
{
    $orderbyoptions['userposts'] = 'Postings';
}

if( !isset($orderby) )
{
    $orderby = 'username';
}

if(!isset($ordertype))
{
  $ordertype = "asc";
}

if(!empty($char))
{
  $char = str_replace('"', '', parse_code($char));
  
  if($char != "digit")
    {
      $where = "WHERE username LIKE '".addslashes($char)."%'";
    }
  else
    {
      $where = "WHERE username REGEXP '^[0-9]'";
    }
}
else
{
  $char = '';
}

if(!empty($search))
{
  $search = parse_code(substr(urldecode($search), 0, 64));

  $where = "WHERE username LIKE '".addslashes($search)."%'";
}
else
{
  $search = '';
}

if( $orderby == 'userposts' && $config['showpostslevel'] != 2 )
{
    message('Fehler', 'Das erw&uuml;nschte Sortierkriterium ist nicht verf&uuml;gbar.');
}

$charselect = '';
$a_chars = array();
for($i = 65; $i <= 90; $i++)
{
  if(chr($i) == $char)
    {
      $a_chars[] = chr($i);
    }
  else
    {
      $a_chars[] = "<a href=\"".build_link("memberlist.php?char=".chr($i))."\">".chr($i)."</a>";
    }

}

$charselect = "| " . join(" | ", $a_chars) . " |";

$t_orderbyoptions = '';
$found = 0; // HAXHAX
while( list($field, $description) = each($orderbyoptions) )
{
    $t_orderbyoptions .= '<option value="' . $field . '"' . ($field == $orderby ? ' selected' : '') . '>' . $description . '</option>';
    if( $field == $orderby )
        $found = 1;
}

if( !$found )
{
    message('Fehler', 'Ung&uuml;ltige Sortieroption');
}

if( $ordertype == "desc" )
{
    $descselected = ' selected';
    $ascselected = '';
}
else
{
    $descselected = '';
    $ascselected = ' selected';
    
    $ordertype = 'asc';
}

if( $config['showpostslevel'] != 2 && $orderby == "userposts" )
{
    $orderby = "username";
}

$r_user = thwb_query("SELECT count(userid) AS usercount FROM ".$pref."user ".$where);
$user = mysql_fetch_array($r_user);

$pages = ceil($user['usercount'] / $config['userperpage']);
$pagesstring = '';

define('PADDING', 6);

if( !isset($page) )
    $page = 1;

// erste seite
if( $page - PADDING > 1 )
{
    $pagesstring = '[<a class="hefo" href="'.build_link('memberlist.php?orderby='.$orderby.'&amp;char='.$char.'&amp;ordertype='.$ordertype.'&amp;search='.urlencode($search).'&amp;page=1').'">Erste Seite</a>] ... ';
}

$i = $page - PADDING;
if( $i < 1 )
    $i = 1;
$imax = $page + PADDING;
if( $imax > $pages )
    $imax = $pages;     

for( $i; $i <= $imax; $i++ )
{
    if( $i == $page )
        $pagesstring .= "&gt;" . ($i) . "&lt; ";
    else
        $pagesstring .= "[<a class=\"hefo\" href=\"".build_link("memberlist.php?orderby=$orderby&amp;ordertype=$ordertype&amp;search=".urlencode($search)."&amp;char=$char&amp;page=$i")."\">" . ($i) . "</a>] ";
}

// letzte seite
if( $page + PADDING < $pages )
{
    $pagesstring .= '... [<a class="hefo" href="'.build_link('memberlist.php?orderby='.$orderby.'&amp;char='.$char.'&amp;ordertype='.$ordertype.'&amp;search='.urlencode($search).'&amp;page='.$pages).'">Letzte Seite</a>]';
}


$MEMBER_ROWS = '';

$r_user = thwb_query("SELECT userid, username, useremail, usericq, userhomepage, userjoin, userposts, userlocation,
    userhideemail, userlastpost FROM ".$pref."user ".$where." ORDER BY $orderby $ordertype LIMIT " . intval($page - 1) * $config['userperpage'] . ", " . $config['userperpage']);


if(!mysql_num_rows($r_user))
{
  $MEMBER_ROWS = '<tr bgcolor="'.$style['CellA'].'"> 
          <td align="center" class="stdfont" colspan="8">Keine User gefunden!</td>
        </tr>';
  $pages = 1;
  $pagesstring = "&gt;1&lt;";
}
else
{
  while( $user = mysql_fetch_array($r_user) )
    {
      $i % 2 == 0 ? $user['bgcolor'] = $style['CellA'] : $user['bgcolor'] = $style['CellB'];
      
      $user['userjoin'] = form_date($user['userjoin']);
      $user['userlastpost'] = form_date($user['userlastpost']);
      $user['userlocation'] = chopstring(parse_code($user['userlocation']), 50);

      if( $user['userhomepage'] == "http://" )
    {
      $user['userhomepage'] = '';
    }

      $user['userhomepage'] = parse_code($user['userhomepage']);
      $user['username'] = parse_code($user['username']);
      
      if( $config['showpostslevel'] != 2)
      {
        if(!$g_user['userisadmin'])
          {
        $user['userposts'] = 'n/a';
          }
      }
      
      $user['useremail'] = get_email( $user, true );

      if( $user['userhomepage'] )
    {
      $user['userhomepage'] = '<a href="'. str_replace('"', '', $user['userhomepage']) .'" target="_blank">' . chopstring($user['userhomepage'], 35) . "</a>";
    }
      else
    {
      $user['userhomepage'] = "&nbsp;";
    }
      
      if( !$user['usericq'] )
    {
      $user['usericq'] = "&nbsp;";
    }
      
      if( !$user['userlocation'] )
    {
      $user['userlocation'] = "&nbsp;";
    }
      
      
      eval($TMemberrow->GetTemplate("MEMBER_ROWS"));
      $i++;
    }
  
}

$search = str_replace('"', '&quot;', $search);

$navpath .= 'Mitgliederliste';

eval($TMemberlist->GetTemplate("CONTENT"));
eval($TFrame->GetTemplate());
