<?php
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
            (c) 2000-2004-2002 by



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

require('./inc/header.inc.php');

$Trank = new Template('./templates/'.$style['styletemplate'].'/rank.html');
$Trankrow = new Template('./templates/'.$style['styletemplate'].'/rankrow.html');
$Tframe = new Template('./templates/'.$style['styletemplate'].'/frame.html');

$a_rank = array();
$r_rank = thwb_query("SELECT rankid, ranktitle, rankposts, rankimage FROM $pref"."rank ORDER BY rankposts DESC");
while( $rank = mysql_fetch_array($r_rank) )
{
    $a_rank[] = $rank;
}

if( count($a_rank) < 1 || !$config['enable_ranks'] )
{
    message('Fehler', 'R&#xE4;nge wurden vom Administrator deaktiviert.');
}

$r_user = thwb_query("SELECT COUNT(userid) FROM $pref"."user");
list($usercount) = mysql_fetch_row($r_user);

$RANKROWS = '';

while( list($i, $rank) = each($a_rank) )
{
    // users for this rank
    if( isset($a_rank[($i - 1)]) )
    {
        $r_user = thwb_query("SELECT COUNT(userid) FROM $pref"."user WHERE
            userposts >= ".$rank['rankposts']." AND userposts < ".$a_rank[($i - 1)]['rankposts']);
        list($rankusers) = mysql_fetch_row($r_user);
        
        $r_user = thwb_query("SELECT userid, username FROM $pref"."user WHERE
            userposts >= ".$rank['rankposts']." AND userposts < ".$a_rank[($i - 1)]['rankposts'].
            " ORDER BY userposts DESC LIMIT 1");
        $user = mysql_fetch_array($r_user);
    }
    else
    {
        $r_user = thwb_query("SELECT COUNT(userid) FROM $pref"."user WHERE
            userposts >= ".$rank['rankposts']);
        list($rankusers) = mysql_fetch_row($r_user);

        $r_user = thwb_query("SELECT userid, username FROM $pref"."user WHERE
            userposts >= ".$rank['rankposts'].
            " ORDER BY userposts DESC LIMIT 1");
        $user = mysql_fetch_array($r_user);
    }

    if( $rank['rankimage'] )
        $rank['rankimage'] = '<img src="'.$rank['rankimage'].'">';
    else
        $rank['rankimage'] = '&nbsp;';

    $prozent = intval($rankusers/$usercount * 100);
    $width = intval($rankusers/$usercount * 120);
    if( !$width )
        $width = 1;
    $invwidth = 120 - $width;
    
    eval($Trankrow->GetTemplate('RANKROWS'));
}

$navpath .= 'Rang&uuml;bersicht';
eval($Trank->GetTemplate('CONTENT'));
eval($Tframe->GetTemplate());
