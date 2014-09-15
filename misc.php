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


include './inc/header.inc.php';

if(!isset($action))
{
  die("denied.");
}

if( $action == 'unsubscribe' )
{
    if( $g_user['userid'] == 0 )
    {
        message('Fehler', 'Bitte loggen Sie sich erst ein!');
    }
    else
    {
        if( !$thread['threadid'] )
        {
            message('Fehler', 'Ung&uuml;ltiger Thread!');
        }
        else
        {
            thwb_query("UPDATE ".$pref."post SET postemailnotify=0 WHERE threadid='$thread[threadid]' AND userid='$g_user[userid]'");
            
            message('Hinweis', 'Ihre Abbestellung wurde erfolgreich durchgef&uuml;hrt!');
        }
    }
}
elseif( $action == 'getemail' )
{
    if( !$g_user['userisadmin'] )
    {
        message('Fehler', 'Sie haben keine Berechtigung diese Seite einzusehen.');
    }
    else
    {
        $r_user = thwb_query("SELECT useremail FROM ".$pref."user WHERE userid='".intval($userid)."'");
        $user = mysql_fetch_array($r_user);
        
        message('Info', 'Die Email-Adresse dieses Benutzers ist: ' . $user['useremail']);
    }
}

elseif( $action == 'getpostcount' )
{
    if( !$g_user['userisadmin'] )
    {
        message('Fehler', 'Sie haben keine Berechtigung diese Seite einzusehen.');
    }
    else
    {
        $r_user = thwb_query("SELECT userposts FROM ".$pref."user WHERE userid='".addslashes($userid)."'");
        $user = mysql_fetch_array($r_user);
        
        message('Info', 'Dieser Benutzer hat ' . $user['userposts'] . ' Posts.');
    }
}

elseif( $action == 'getlastpost' )
{
    $r_post = thwb_query("SELECT threadid, postid FROM $pref"."post WHERE userid='".addslashes($userid)."' ORDER BY posttime DESC LIMIT 1");
    if( mysql_num_rows($r_post) > 0 )
    {
        $post = mysql_fetch_array($r_post);
        $postid = $post['postid'];
        $threadid= $post['threadid'];

        /* which page? */
        $postnum = 0;
    
        $r_post = thwb_query("SELECT postid FROM $pref"."post WHERE threadid='$post[threadid]' ORDER BY posttime ASC");
        while( $post = mysql_fetch_array($r_post) )
        {
            $postnum++;
            if( $post['postid'] == $postid )
                break;
        }

        $pagenum = ceil($postnum / $config['vars_m_amount']);
        header('Location: '.build_link('showtopic.php?threadid='.$threadid.'&amp;pagenum='.$pagenum.'#'.$postid, true));
    }
    else
    {
        message('Fehler', 'Dieser Benutzer hat noch keinen Beitrag verfasst.');
    }
}

elseif( $action == 'clearboards' )
{
    thwb_query("UPDATE $pref"."lastvisited SET lastvisitedtime='".time()."' WHERE userid='$g_user[userid]'");
    header('Location: '.build_link('index.php', true));
}
elseif( $action == 'user_activate' )
{
  if(empty($userid) || empty($hash))
    {
      die('denied');
    }

  $r_user = thwb_query("SELECT userjoin, useractivate FROM ".$pref."user WHERE userid='".intval($userid)."'");
  
  if(!mysql_num_rows($r_user))
    {
      message("Fehler", "Der angegebene Benutzer existiert nicht.");
    }

  $a_user = mysql_fetch_array($r_user);

  if(!$a_user['useractivate'])
    {
      message("Fehler", "Der angegebene Benutzer ist bereits aktiviert.");
    }

  if($hash != md5($a_user['userjoin']))
    {
      message("Fehler", "Die angegebene Aktivierungs-ID stimmt nicht.");
    }
  
  thwb_query("UPDATE ".$pref."user SET useractivate='0' WHERE userid='".intval($userid)."'");

  message("Registrierung erfolgreich!", "Ihre Registrierung ist nun abgeschlossen. Sie k&ouml;nnen sich <a href=\"login.php\">hier</a> einloggen. Viel Spa&szlig;!");
}
else if( $action == 'change_email' )
{
    $r_user = thwb_query("SELECT userid, userpassword FROM ".$pref."user WHERE userid='".intval($userid)."'");

    if(!mysql_num_rows($r_user))
    {
        message("Fehler", "Der angegebene Benutzer existiert nicht.");
    }

    $a_user = mysql_fetch_array($r_user);

    if($a_user['userpassword'] != $hash)
    {
        message("Fehler", "Die Pr&uuml;fsumme ist nicht korrekt.");
    }

    if(!check_email($email))
    {
        message("Fehler", "Die Email-Adresse ist ung&uuml;ltig.");
    }

    thwb_query("UPDATE ".$pref."user SET useremail='".addslashes($email)."' WHERE userid='".intval($userid)."'");

    message("A&uml;nderung abgeschlossen!", "Ihre Email-Adresse wurde erfolgreich ge&auml;ndert.");
}
