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

include "./inc/header.inc.php";

function is_firstpost($threadid, $postid)
{
    global $pref;
    $r_post = thwb_query("SELECT postid FROM $pref"."post WHERE threadid='$threadid' ORDER BY posttime ASC");
    $post = mysql_fetch_array($r_post);

    if( $post['postid'] == $postid )
        return 1;
    else
        return 0;
}

// http://www.securiteam.com/securitynews/5FP0C204KE.html
$newpost['posttext'] = ((isset($HTTP_POST_VARS['newpost'])) ? $HTTP_POST_VARS['newpost']['posttext'] : '');

if(!isset($newpost['postcode']))
{
  $newpost['postcode'] = 0;
}

if(!isset($newpost['postsmilies']))
{
  $newpost['postsmilies'] = 0;
}

if(!isset($newpost['postemailnotify']))
{
  $newpost['postemailnotify'] = 0;
}

$navpath .= 'Post editieren';

if( $g_user['userid'] == 0 )
{
    message("Fehler", "Nur registrierte/eingeloggte Benutzer k&ouml;nnen Beitr&#xE4;ge editieren!");
}

if(empty($thread))
{
    die('pwnd.');
}

if( $thread['threadclosed'] == 1 )
{
    if( !$P->has_permission( P_EDITCLOSED ) )
    {
        message('Fehler', 'Dieser Thread ist leider geschlossen. Es k&ouml;nnen keine Beitr&auml;ge mehr editiert
 werden.');
    }
}

$r_post = thwb_query("SELECT postid, posttext, userid, threadid, postcode, postsmilies, postemailnotify, posttime FROM ".$pref."post WHERE postid='$post[postid]'");
$post = mysql_fetch_array($r_post);

$post['threadtopic'] = htmlspecialchars($thread['threadtopic']);
$post['posttext'] = htmlspecialchars($post['posttext']);

// First Post of Thread ?
// dp: dieser weg ist besser als via time-vergleich
$firstpost = is_firstpost($thread['threadid'], $post['postid']);

if( $firstpost && $P->has_permission( P_EDITTOPIC) )
{
    $post['printtopic'] = '<input class="tbinput" type="text" name="newpost[threadtopic]" size="50" value="'.$post['threadtopic'].'" maxlength="75">';
}
else
{
    $post['printtopic'] = "$post[threadtopic]";
}

if( ($post['userid'] == $g_user['userid'] && $P->has_permission( P_EDIT )) || $P->has_permission( P_OEDIT ) )
{
    if( $config['editlimit'] && !$P->has_permission( P_NOEDITLIMIT ) && ($post['posttime'] + $config['editlimit']) < time() )
    {
        message('Fehler', 'Sie k&ouml;nnen diesen Post nicht mehr editieren. (Zeitlimit &#xFC;berschritten)');
    }

    if( !isset($Submit) )
    {
        if( $post['postcode'] )
        {
            $codechecked = ' checked';
        }
        else
        {
            $codechecked = '';
        }

        if( $post['postsmilies'] )
        {
            $smilieschecked = ' checked';
        }
        else
        {
            $smilieschecked = '';
        }

        if( $post['postemailnotify'] )
        {
            $mailchecked = ' checked';
        }
        else
        {
            $mailchecked = '';
        }

        if( $config['smilies'] )
        {
            $smilies_on_off = "AN";
        }
        else
        {
            $smilies_on_off = "AUS";
        }

        $Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
        $Tform = new Template("templates/" . $style['styletemplate'] . "/edit.html");



        eval($Tform->GetTemplate("CONTENT"));
        eval($Tframe->GetTemplate());
    }
    else
    {
      $msg = '';

        $newpost['posttext'] = strip_session($newpost['posttext']);

        // Bannedwords-Protection
        if( $config["usebwordprot"] >= BWORD_POST )
        {
            $post["posttext"] = check_banned($post["posttext"]);
        }
        if( $firstpost && $P->has_permission( P_EDITTOPIC) )
        {
            if( $config["usebwordprot"] == BWORD_TOPIC || $config["usebwordprot"] == BWORD_ALL )
            {
                $thread["threadtopic"] = check_banned($thread["threadtopic"]);
            }
            if( strlen($newpost['threadtopic']) > $config['subject_maxlength'] )
            {
                $msg .= "Das Subject ist zu lang!<br>";
            }
            if( strlen(preparse_code($newpost['threadtopic'])) < $config['subject_minlength'] )
            {
                $msg .= "Das Subject ist zu kurz!<br>";
            }
        }
        if( strlen($newpost['posttext']) < $config['message_minlength'] )
        {
            $msg .= "Ihr Text ist zu kurz!<br>";
        }
        if( strlen($newpost['posttext']) > $config['message_maxlength'] )
        {
            $msg .= "Ihr Text ist zu lang!<br>";
        }

        if( isset($msg) && strlen($msg) > 0 )
        {
            message("Fehler", "Es sind leider folgende Fehler aufgetreten:<br><br><font color='$style[color_err]'>$msg</font>");
        }

        $c_time = time();

        thwb_query("UPDATE ".$pref."post SET posttext='" . addslashes(preparse_code($newpost['posttext'])) . "',
        postlasteditby='$g_user[username]', postlastedittime='" . time() . "',
        postsmilies='" . ($newpost['postsmilies'] ? 1 : 0) . "',
        postcode='" . ($newpost['postcode'] ? 1 : 0) . "',
        postemailnotify='" . (isset($newpost['postemailnotify']) && $newpost['postemailnotify'] ? 1 : 0) . "' WHERE postid='$post[postid]'");

        // topic updaten, (auch board), nicht als normaluser
        if( $firstpost && $P->has_permission( P_EDITTOPIC ) )
        {
            $newpost['threadtopic'] = addslashes(preparse_code($newpost['threadtopic']));

            // topic setten
            thwb_query("UPDATE ".$pref."thread SET threadtopic = '". $newpost['threadtopic'] ."' WHERE threadid = '$post[threadid]'");

            // board updaten
            updateboard($thread['boardid']);

            // eventuell vorhandene threadlinks updaten
            $r_link = thwb_query( "SELECT threadid, boardid FROM ".$pref."thread WHERE threadlink = ". $post['threadid'] );
            if( mysql_num_rows( $r_link ) )
            {
                $a_link = mysql_fetch_array( $r_link );
                thwb_query( "UPDATE ".$pref."thread SET threadtopic = '". $newpost['threadtopic'] ."' WHERE threadid = ". $a_link['threadid'] );
                // nicht updaten, threadlinks werden eh nicht im boardlastpost angezeigt
                // updateboard( $a_link['boardid'] );
            }
        }

        header("Location: ".build_link("showtopic.php?threadid=$thread[threadid]&pagenum=lastpage#".$post['postid'], true));
    }
}
else
{
    message("Fehler", "Sie k&ouml;nnen diesen Post nicht editieren");
}
