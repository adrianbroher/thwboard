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

function logaction( $logaction ) {
    global $g_user, $thread, $REMOTE_ADDR, $PHP_SELF, $pref;
    thwb_query( "INSERT INTO ".$pref."adminlog (logtype, logtime, loguser, logip, logscript,
    logaction, lognotes) VALUES ('LOG_MOD',
    ".time().",
    '$g_user[username]',
    '$REMOTE_ADDR',
    '".basename($PHP_SELF)."',
    '".addslashes($logaction)."',
    'thread id: $thread[threadid]')" );
}


include "./inc/header.inc.php";

if( isset($action) && $action != 'remove_link' )
{
    $action = ((isset($HTTP_POST_VARS['action'])) ? $HTTP_POST_VARS['action'] : '');
}

if( !isset($action) || $action == '')
{
    message('Fehler', 'Bitte w&#xE4;hlen Sie eine Aktion aus dem Men&#xFC; aus.');
}

if( !isset($thread['threadid']) || !$thread['threadid'] )
{
    message("Fehler","ERROR: \$thread[threadid] not set!");
}

/*
 * ===============================================================
 *  action: close thread 
 * ===============================================================
 */
if( $action == "close" )
{
    $navpath .= "Thread &ouml;ffnen/schlie&szlig;en";
    $r_thread = thwb_query("SELECT threadid, threadclosed, threadauthor FROM ".$pref."thread WHERE threadid=$thread[threadid]");
    $thread = mysql_fetch_array($r_thread);

    if( ($g_user['username'] == $thread['threadauthor'] && $P->has_permission( P_CLOSE ) ) || $P->has_permission( P_OCLOSE ) )
    {
        logaction("closed/opened");

        if( $thread['threadclosed'] == 1 )
        {
            thwb_query("UPDATE ".$pref."thread SET threadclosed=0 WHERE threadid=$thread[threadid]");
            message("&nbsp;", "Thread wurde wieder ge&ouml;ffnet!<br><a href=\"".build_link("showtopic.php?thread[threadid]=$thread[threadid]")."\">Zur&uuml;ck zum Thread</a>");
        }
        else
        {
            thwb_query("UPDATE ".$pref."thread SET threadclosed=1 WHERE threadid=$thread[threadid]");
            message("&nbsp;", "Thread wurde geschlossen!<br><a href=\"".build_link("showtopic.php?thread[threadid]=$thread[threadid]")."\">Zur&uuml;ck zum Thread</a>");
        }
    }
    else
    {
        $navpath .= "Thread &ouml;ffnen/schlie&szlig;en";
        message("Fehler", "Sie haben keine Berechtigung diesen Thread zu schlie&szlig;en.");
    }
}


/*
 * ===============================================================
 *  action: 
 * ===============================================================
 */
if( $action == "top" )
{
    $navpath .= "Thread festmachen";
    $r_thread = thwb_query("SELECT threadid, threadtop, threadauthor FROM ".$pref."thread WHERE threadid=$thread[threadid]");
    $thread = mysql_fetch_array($r_thread);

    if( $P->has_permission( P_TOP ) )
    {
        logaction("topped/untopped");

        if( $thread['threadtop'] == 1 )
        {
            thwb_query("UPDATE ".$pref."thread SET threadtop=0 WHERE threadid=$thread[threadid]");
            message("&nbsp;", "Thread wurde gel&ouml;st!<br><a href=\"".build_link("showtopic.php?thread[threadid]=$thread[threadid]")."\">Zur&uuml;ck zum Thread</a>");
        }
        else
        {
            thwb_query("UPDATE ".$pref."thread SET threadtop=1 WHERE threadid=$thread[threadid]");
            message("&nbsp;", "Thread wurde festgemacht!<br><a href=\"".build_link("showtopic.php?thread[threadid]=$thread[threadid]")."\">Zur&uuml;ck zum Thread</a>");
        }
    }
    else
    {
        $navpath .= "Thread festmachen";
        message("Fehler", "Sie haben keine Berechtigung diesen Thread festzumachen!");
    }
}


/*
 * ===============================================================
 *  action: 
 * ===============================================================
 */
if( $action == "move" )
{
    $navpath .= "Thread verschieben";
    if( $P->has_permission( P_OMOVE ) )
    {
        $TFrame = new Template("./templates/".$style['styletemplate']."/frame.html");
        $TMoveform = new Template("./templates/".$style['styletemplate']."/movethreadform.html");
        
        $boards = array();
        
        $r_board = thwb_query("SELECT boardid, boardname, categoryid FROM ".$pref."board ORDER BY boardorder");
        while( $board = mysql_fetch_array($r_board) )
        {
            $boards["$board[categoryid]"][] = "<option value=\"$board[boardid]\">-- $board[boardname]</option>";
        }

        $MOVEOPTIONS = '';
        
        $r_category = thwb_query("SELECT categoryid, categoryname FROM ".$pref."category ORDER BY categoryorder");
        while( $category = mysql_fetch_array($r_category) )
        {
            $MOVEOPTIONS .= "<option value=\"0\">$category[categoryname]</options>";
            while( list($k, $v) = @each($boards["$category[categoryid]"]) )
            {
                $MOVEOPTIONS .= "$v";
            }
        }
        
        eval($TMoveform->GetTemplate("CONTENT"));
        eval($TFrame->GetTemplate());
    }
    else
    {
        $navpath .= "Thread verschieben";
        message("Fehler", "Sie haben keine Berechtigung diesen Thread zu verschieben.");
    }
}


/*
 * ===============================================================
 *  action: 
 * ===============================================================
 */
if( $action == "do_move" )
{
    if( $P->has_permission( P_OMOVE ) )
    {
        if( !$into || $into == 0)
        {
            $navpath .= "Thread verschieben";
            message("&nbsp","Bitte w&auml;hle ein Forum aus!");
        }

        logaction("moved");

        $r_thread = thwb_query("SELECT threadid, threadtime, threadtopic, threadauthor, threadlastreplyby, boardid, threadreplies FROM ".$pref."thread WHERE threadid=$thread[threadid]");
        $thread = mysql_fetch_array($r_thread);

        $thread['threadreplies']++;

        // topics und posts vom source board abziehen....
        thwb_query("UPDATE ".$pref."board SET boardthreads=boardthreads-1, boardposts=boardposts-$thread[threadreplies] WHERE boardid=$thread[boardid]");

        //..... und zum $into board adden
        thwb_query("UPDATE ".$pref."board SET boardthreads=boardthreads+1, boardposts=boardposts+$thread[threadreplies] WHERE boardid='".intval($into)."'");

        thwb_query("UPDATE ".$pref."thread SET boardid=".intval($into)." WHERE threadid=$thread[threadid]");
        updateboard($thread['boardid']);
        
        if( isset($createlink) && $createlink == 1 )
        {
            // fake (link) zum thread machen, im alten board
            thwb_query("INSERT INTO
                ".$pref."thread (threadtopic, threadtime, threadauthor, boardid, threadlastreplyby, threadlink, threadclosed)
                VALUES (
                    '" . addslashes($thread['threadtopic']) . "',
                    '$thread[threadtime]',
                    '" . addslashes($thread['threadauthor']) . "',
                    '$thread[boardid]',
                    '" . addslashes($thread['threadlastreplyby']) . "',
                    '$thread[threadid]',
                    '1')");
        }
        
        // last* vom zielboard updaten.
        updateboard($into);

        if( $redir == "currentforum" )
        {
            header("Location: ".build_link("board.php?board[boardid]=$thread[boardid]", true));
        }
        else
        {
            header("Location: ".build_link("showtopic.php?thread[threadid]=$thread[threadid]", true));
        }
    }
    else
    {
        $navpath .= "Thread verschieben";
        message("Fehler", "Sie haben keine Berechtigung diesen Thread zu verschieben.");
    }
}


/*
 * ===============================================================
 *  action: delete thread 
 * ===============================================================
 */
if( $action == "delete" )
{
    $r_thread = thwb_query("SELECT threadid, threadauthor FROM ".$pref."thread WHERE threadid=$thread[threadid]");
    $thread = mysql_fetch_array($r_thread);
    
    if( ( $g_user['username'] == $thread['threadauthor'] && $P->has_permission( P_DELTHREAD ) ) || $P->has_permission( P_ODELTHREAD) )
    {
        $navpath .= "Thread l&ouml;schen";
        message("&nbsp;", 'Soll dieser Thread wirklich GEL&Ouml;SCHT werden?<br>
<form name="theform" method="post" action="'.build_link("threadops.php").'">
  <input type="hidden" name="action" value="do_delete">
  <input type="hidden" name="thread[threadid]" value="' . $thread['threadid'] . '">
  <input class="tbbutton" type="submit" name="Submit" value="L&ouml;schen &gt;&gt;">
</form>');
    }
    else
    {
        $navpath .= "Thread l&ouml;schen";
        message("Fehler", "Sie haben keine Berechtigung diesen Thread zu l&ouml;schen.");
    }
}


/*
 * ===============================================================
 *  action: do_delete thread 
 * ===============================================================
 */
if( $action == "do_delete" )
{
    $r_thread = thwb_query("SELECT threadid, threadreplies, threadauthor, boardid FROM ".$pref."thread WHERE threadid=$thread[threadid]");
    $thread = mysql_fetch_array($r_thread);
    
    if( ( $g_user['username'] == $thread['threadauthor'] && $P->has_permission( P_DELTHREAD ) ) || $P->has_permission( P_ODELTHREAD) )
    {
        $thread['threadreplies']++;
    
        // substract posts/thread from board
        thwb_query("UPDATE ".$pref."board SET boardposts=boardposts-$thread[threadreplies], boardthreads=boardthreads-1 WHERE boardid=$board[boardid]");

        // del
        thwb_query("DELETE FROM ".$pref."thread WHERE threadid=$thread[threadid] OR threadlink=$thread[threadid]");
        thwb_query("DELETE FROM ".$pref."post WHERE threadid=$thread[threadid]");

        updateboard($thread['boardid']);

        logaction("deleted");
        $navpath .= "Thread l&ouml;schen";
        message("&nbsp;", 
            "Thread wurde gel&ouml;scht!<br><a href=\"".build_link("board.php?board[boardid]=$board[boardid]")."\">Zur&uuml;ck zum Board</a>");
    }
    else
    {
        $navpath .= "Thread l&ouml;schen";
        message("Fehler", "Sie haben keine Berechtigung diesen Thread zu l&ouml;schen.");
    }
}



/*
 * ===============================================================
 *  action: remove_link
 * ===============================================================
 */
if( $action == 'remove_link' )
{
    message("&nbsp;", 'Soll dieser Thread-Link wirklich entfernt werden?<br>
<form name="theform" method="post" action="'.build_link('threadops.php').'">
  <input type="hidden" name="action" value="do_remove_link">
  <input type="hidden" name="thread[threadid]" value="' . $thread['threadid'] . '">
  <input class="tbbutton" type="submit" name="Submit" value="Link entfernen &gt;&gt;">
</form>');
}




/*
 * ===============================================================
 *  action: do_remove_link
 * ===============================================================
 */
if( $action == 'do_remove_link' )
{
    if( $P->has_permission( P_OMOVE ) )
    {
        $r_thread = thwb_query("SELECT threadid, boardid FROM $pref"."thread WHERE threadid=$thread[threadid]");
        $thread = mysql_fetch_array($r_thread);
    
        thwb_query("DELETE FROM $pref"."thread WHERE threadid=$thread[threadid]");
        
        logaction('removed thread link');
        updateboard($thread['threadid']);
        
        message('Info', 'Der Thread-Link wurde erfolgreich entfernt.<br><a href="'.build_link('board.php?board[boardid]='.$board['boardid']).'">Zur&uuml;ck zum Board</a>');
    }
    else
    {
        message('Fehler', 'Sie haben keine Berechtigung, diesen Thread-Link zu entfernen');
    }
}

/**
 * action: merge
 * merges to threads
 **/

if($action == 'merge')
{
    $navpath .= "Threads verschmelzen";
    if( $P->has_permission( P_ODELTHREAD ) )
    {
        $TFrame = new Template("./templates/".$style['styletemplate']."/frame.html");
        $TMoveform = new Template("./templates/".$style['styletemplate']."/mergethreadform.html");
        
        eval($TMoveform->GetTemplate("CONTENT"));
        eval($TFrame->GetTemplate());
    }
    else
    {
        $navpath .= "Threads verschmelzen";
        message("Fehler", "Sie haben keine Berechtigung, um Threads zu verschmelzen.");
    }

}

/**
 * action: do_merge
 * does the dirty work
 **/

if($action == 'do_merge')   
{
    $navpath .= "Threads verschmelzen";

    if(! $P->has_permission( P_ODELTHREAD ) )
    {
        message("Fehler", "Sie haben keine Berechtigung, um Threads zu verschmelzen.");
    }

    $src = addslashes($thread['threadid']);
    $tgt = addslashes($target['threadid']);

    $r_threads = thwb_query('SELECT threadtime, threadreplies, boardid, threadid 
                            FROM '.$pref.'thread 
                            WHERE threadid IN (\''.$src.'\', \''.$tgt.'\')
                            ORDER BY threadid DESC');

    if(mysql_num_rows($r_threads) != 2)
    {
        message('Fehler', 'Der Ziel-Thread existiert nicht.');
    }

    $a_src = mysql_fetch_assoc($r_threads);
    $a_tgt = mysql_fetch_assoc($r_threads);

    // mysql messed it all up.

    if($a_src['threadid'] != intval($src))
    {
        $a_tmp = $a_src;
        $a_src = $a_tgt;
        $a_tgt = $a_tmp;
    }

    if(!($a_src['threadtime'] > $a_tgt['threadtime']))
    {           
        message('Fehler', 'Der Ziel-Thread ist j&uuml;nger als der Quell-Thread. Dies w&uuml;rde die Beitragsreihenfolge zerst&ouml;ren');
    }

    // do it.

    thwb_query('UPDATE '.$pref.'post 
               SET threadid = \''.$tgt.'\' 
               WHERE threadid = \''.$src.'\'');

    thwb_query('DELETE FROM '.$pref.'thread 
               WHERE threadid = \''.$src.'\'');

    thwb_query('UPDATE '.$pref.'thread 
               SET threadreplies = '.($a_tgt['threadreplies'] + $a_src['threadreplies'] + 1).' 
               WHERE threadid = \''.$tgt.'\'');

    updatethread($tgt);
    updateboard($a_src['boardid']);
    updateboard($a_tgt['boardid']);

    logaction('merge with '.$tgt);

    message('Info', 'Die Threads wurden erfolgreich verschmolzen.<br><a href="'.build_link('showtopic.php?threadid='.$tgt).'">Zum Ziel-Thread</a>.');
}

/*
 * ===============================================================
 *  action: markthread thread 
 * ===============================================================
 */
/*if( $action == 'markthread' )
{
    $navpath .= "Thread markieren";
    if( $g_user['userid'] != 0 )
    {
        if ( substr_count( $g_user['usermarkedthreads'], ';'.$thread['threadid'].';' ) != 0 )
        {
            $usermarkedthreads = str_replace(';'.$thread['threadid'].';', ';', $g_user['usermarkedthreads'] );
            if ( strlen( $usermarkedthreads ) == 1 )
            {
                $usermarkedthreads = '';
            }
            mysql_query("UPDATE ".$pref."user SET usermarkedthreads = '".$usermarkedthreads."' WHERE userid = '".$g_user['userid']."'");
            message("Markierung entfernt", "Die Markierung des Threads wurde erfolgreich entfernt!");
        }
        else
        {
            $usermarkedthreads = substr($g_user['usermarkedthreads'], 1);
            $a_usermarkedthreads = explode(';', $usermarkedthreads);
            $c_usermarkedthreads =  count( $a_usermarkedthreads );
            if ( $c_usermarkedthreads <= $config['max_markedthreads'] )
            {
                if ( !$g_user['usermarkedthreads'] )
                {
                    mysql_query("UPDATE ".$pref."user SET usermarkedthreads = ';".$thread['threadid'].";' WHERE userid = '".$g_user['userid']."'");
                    message("Der Thread wurde markiert", "Der gewählte Thread wurde erfolgreich markiert!");
                }
                else
                {
                    mysql_query("UPDATE ".$pref."user SET usermarkedthreads = '".$g_user['usermarkedthreads'].$thread['threadid'].";' WHERE userid = '".$g_user['userid']."'");
                    message("Der Thread wurde markiert", "Der gewählte Thread wurde erfolgreich markiert!");
                }
            }
            else
            {
                message("Fehler", "Sie können nicht mehr als ".$config['max_markedthreads']." Threads markieren.");
            }
        }
        
    }
    else
    {
        message('Fehler', 'Als Gast können Sie keine Threads markieren.');
    }
}*/
