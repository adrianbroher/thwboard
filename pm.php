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

function prevent_pm_flood()
{
    global $g_user, $P, $pref, $config;

    if($P->has_permission(P_NOFLOODPROT))
    {
        return;
    }

    $r_lastpm = thwb_query("SELECT pmtime FROM ".$pref."pm WHERE pmfromid='$g_user[userid]' ORDER BY pmtime DESC LIMIT 1");

    if(!mysql_num_rows($r_lastpm))
    {
        return;
    }

    $a_lastpm = mysql_fetch_array($r_lastpm);

    if($a_lastpm['pmtime'] >= (time() - $config['postdelay']))
    {
        message("Fehler", "Sie k&ouml;nnen nur alle $config[postdelay] Sekunden eine PM verschicken.");
    }

    return;
}

$config['pmqouta'] = 1;

if(!isset($action))
{
  $action = '';
}

// clearing DB from old Pms
if ( $config['pmalive'] > 0 && (time() % 60 > 55) )
{
    $limit_date = time()-($config['pmalive']*(60*60*24));
    thwb_query("DELETE FROM ".$pref."pm WHERE pmtime < ".$limit_date."");
}

if( !$config['privatemessages'])
{
    $navpath .= "Private Messages";

    message("Sorry", "Private Messages wurden vom Administrator deaktiviert.");
}

if( $g_user['userid'] == 0 )
{
    $navpath .= "Private Messages";

    message("Nur f&uuml;r Mitglieder", "Diese Funktion ist nur f&uuml;r Mitglieder. Sie k&ouml;nnen sich <a href=\"register.php\">hier</a> kostenlos registrieren.");
}

if( $action == "do_deleteall" )
{
  if(strcmp($HTTP_POST_VARS['action'], 'do_deleteall'))
  {
    die('Sorry, postvars only...');
  }

  $pm = $HTTP_POST_VARS['pm'];

  thwb_query("DELETE FROM ".$pref."pm WHERE pmtoid=$g_user[userid]");
  header("Location: ".build_link('pm.php', true));
}
elseif( $action == "deleteall" )
{
    $navpath .= "Private Messages";

    message('Bestaetigung', '<form name="theform" method="post" action="'.build_link("pm.php").'">
  M&ouml;chten Sie wirklich alle Nachrichten l&ouml;schen?<br><br>
  <input type="hidden" name="pm[pmid]" value="' . 0 . '">
  <input type="hidden" name="action" value="do_deleteall">
  <input class="tbbutton" type="submit" name="Submit" value="L&ouml;schen &gt;&gt;">
</form>');
}
elseif( isset($deletepm) )
{
    thwb_query("DELETE FROM ".$pref."pm WHERE ( pmtoid=$g_user[userid] ) AND ( pmid = ".intval($deletepm)." )");
    header("Location: ".build_link('pm.php', true));
}
elseif( isset($deletepms) )
{
    $deletepms = $HTTP_POST_VARS['deletepms'];

    if( $deletepms )
    {
        while( list($k, $v) = each($deletepms) )
            $deletepms[$k] = intval($v);

        thwb_query("DELETE FROM ".$pref."pm WHERE pmtoid=$g_user[userid] AND pmid IN(" . addslashes(implode(',', $deletepms)) . ")");
        header("Location: ".build_link('pm.php', true));
        exit;
    }
    else
    {
        $navpath .= "Private Messages";

        message('Fehler', 'Bitte markieren Sie erst eine oder mehrere Nachricht(en).');
    }
}
elseif( $action == "new" )
{
    if( isset($send) && $send )
    {
        // http://www.securiteam.com/securitynews/5FP0C204KE.html
        $action = $HTTP_POST_VARS['action'];
        $errmsg = '';

        $pm['pmtext'] = strip_session($pm['pmtext']);

        if( strlen($pm['username']) < 1 )
        {
            $errmsg .= "Bitte geben Sie einen Empf&auml;nger an<br>";
        }

        if( strlen(preg_replace("/^\s+|&#32;$/", '', parse_code($pm['pmtopic']))) < 3 )
        {
            $errmsg .= "Betreff ist zu kurz! (mindestens 3 Zeichen)<br>";
        }

        if( strlen(preg_replace("/^\s+|&#32;$/", '', parse_code($pm['pmtext']))) < 3 )
        {
            $errmsg .= "Der Text ist zu kurz! (mindestens 3 Zeichen)<br>";
        }

        if( strlen($pm['pmtext']) > $config['pm_maxlength'] )
        {
            $errmsg .= "Der Text ist zu lang! (maximal $config[pm_maxlength] Zeichen)<br>";
        }

        $r_user = thwb_query("SELECT userid, useremail, groupids FROM ".$pref."user WHERE username='" . addslashes($pm['username']) . "'");
        if( mysql_num_rows($r_user) < 1 )
        {
            $errmsg.="Der Empf&auml;nger existiert nicht!<br>";
        }
        else
        {
            $user = mysql_fetch_array($r_user);
            if( $user['groupids'] == ',,' )
                $user['groupids'] = '-1';
            else
                $user['groupids'] = substr($user['groupids'], 1, strlen($user['groupids']) - 2);
        }

        if( strlen($errmsg) > 0 )
        {
            $navpath .= "Private Messages";

            message("Fehler", "Es sind leider folgende Fehler aufgetreten:<br><br><font color='$style[color_err]'>$errmsg</font>");
        }

        if(!isset($pm['pmsaveinoutbox']))
          {
            $pm['pmsaveinoutbox'] = 0;
          }

        $r_frompm = thwb_query("SELECT count(pmid) FROM ".$pref."pm WHERE pmtoid=$g_user[userid]");
        list($frompmcount) = mysql_fetch_row($r_frompm);

        if ( $pm['pmmethod'] == 'pm' )
        {
            // flood check

            prevent_pm_flood();

            // full inbox?

            $r_topm = thwb_query("SELECT count(pmid) FROM ".$pref."pm WHERE pmtoid=$user[userid]");
            list($topmcount) = mysql_fetch_row($r_topm);

            $toP = new Permission($user['groupids']);

            if( $topmcount >= $config['max_privmsg'] && !($P->has_permission( P_FORCEPM ) || $toP->has_permission( P_NOPMLIMIT )) )
            {
                $navpath .= "Private Messages";

                message('Fehler', 'Die Nachricht konnte nicht versendet werden: Die Private Message Box des Empf&auml;ngers ist voll.');
            }
            else
            {
                // send msg!
                thwb_query("INSERT INTO ".$pref."pm (pmfromid, pmtoid, pmtopic, pmtext, pmtime, pmflags, pmfolder)
                    VALUES ($g_user[userid], $user[userid],'" . addslashes($pm['pmtopic']) . "','" . addslashes($pm['pmtext']) . "',".time().", 1, 0);");

                if((($frompmcount < $config['max_privmsg']) || $P->has_permission(P_NOPMLIMIT)) && $pm['pmsaveinoutbox'] == 1)
                {
                    thwb_query("INSERT INTO ".$pref."pm (pmtoid, pmfromid, pmtopic, pmtext, pmtime, pmflags, pmfolder)
                    VALUES ($g_user[userid], $user[userid],'" . addslashes($pm['pmtopic']) . "','" . addslashes($pm['pmtext']) . "',".time().",0 , 1);");
                }
                $navpath .= "Private Messages";

                message("Message verschickt", "Ihre Private Message wurde verschickt!<br><a href=\"".build_link('pm.php')."\">Private Message Center</a>");
            }
        }
        elseif ( $pm['pmmethod'] == 'email' )
        {
          // SEND PM as E-Mail
          $Pmmail = new Template("templates/mail/pmsg.mail");

          possible_flood(FLOOD_MAIL, $g_user['userid']);

          if(is_flooding(FLOOD_MAIL, $g_user['userid']))
          {
              message('Fehler', 'Sie k&ouml;nnen nur '.$config['flood_mail_count'].' E-Mails pro '.$config['flood_mail_timeout'].' Minuten verschicken.');
          }

          $mail_body = "";

          eval($Pmmail->GetTemplate("mail_body"));
          @mail($user['useremail'],"Private Nachricht: ".$pm['pmtopic'], $mail_body, "From: ".$g_user['username']." <".$g_user['useremail'].">");

          if( ( $frompmcount < $config['max_privmsg'] ) AND ( $pm['pmsaveinoutbox'] == 1 ) )
            {
              thwb_query("INSERT INTO ".$pref."pm (pmtoid, pmfromid, pmtopic, pmtext, pmtime, pmflags, pmfolder)
                VALUES ($g_user[userid], $user[userid],'" . addslashes($pm['pmtopic']." *E-Mail*") . "','" . addslashes($pm['pmtext']) . "',".time().",0 , 1);");
            }
          $navpath .= "Private Messages";

          message("Message verschickt", "Ihre Private Message wurde als E-Mail verschickt!<br><a href=\"".build_link('pm.php')."\">Private Message Center</a>");
        }
    }
    else
    {
        $TFrame = new Template("templates/" . $style['styletemplate'] . "/frame.html");
        $TMsg = new Template("templates/" . $style['styletemplate'] . "/newprivmsg.html");
        if ( $config['use_email'] == 1 )
            $print_emailradio = "<input type=radio name=pm[pmmethod] value=email> E-Mail";
        else
            $print_emailradio = '';

        $pm = array();

        if(!isset($recipient))
          {
            $recipient = '';
          }

        if(!isset($pm['pmtopic']))
          {
            $pm['pmtopic'] = '';
          }

        if(!isset($pm['pmtext']))
          {
            $pm['pmtext'] = '';
          }

        $recipient = htmlspecialchars($recipient);

        if( isset($replyto) && $replyto )
        {
            $r_pm = thwb_query("SELECT pm.pmtopic, pm.pmfolder, user.username
                FROM ".$pref."pm AS pm, ".$pref."user AS user
                WHERE pm.pmid=".intval($replyto)." AND pm.pmfromid=user.userid AND pmtoid=$g_user[userid]");

            $pm = mysql_fetch_array($r_pm);

            if( !strstr($pm['pmtopic'], 'RE: ') )
            {
                $pm['pmtopic'] = 'RE: ' . $pm['pmtopic'];
            }

            $pm['pmtopic'] = htmlspecialchars($pm['pmtopic']);
            $recipient = htmlspecialchars($pm['username']);
            $pm['pmtext'] = '';
        }
        elseif ( isset($forward) && $forward )
        {
            $r_pm = thwb_query("SELECT pm.pmtopic, pm.pmfolder, pm.pmtext, user.username
                FROM ".$pref."pm AS pm, ".$pref."user AS user
                WHERE pm.pmid=".intval($forward)." AND pm.pmfromid=user.userid AND pmtoid=$g_user[userid]");

            $pm = mysql_fetch_array($r_pm);

            if( !strstr($pm['pmtopic'], 'FW: ') )
            {
                $pm['pmtopic'] = 'FW: ' . $pm['pmtopic'];
            }
            $pm['pmtopic'] = htmlspecialchars($pm['pmtopic']);
            $pm['pmtext'] = "[quote][i][noparse]Zitat von $pm[username]:[/noparse][/i]\n". htmlspecialchars($pm['pmtext']) ."[/quote]";
            $recipient = "";

        }
        elseif ( isset($insert) && $insert )
        {
            $r_pm = thwb_query("SELECT pm.pmtopic, pm.pmfolder, pm.pmtext, user.username
                FROM ".$pref."pm AS pm, ".$pref."user AS user
                WHERE pm.pmid=".intval($insert)." AND pm.pmfromid=user.userid AND pmtoid=$g_user[userid]");

            $pm = mysql_fetch_array($r_pm);

            if( !strstr($pm['pmtopic'], 'RE: ') )
            {
                $pm['pmtopic'] = 'RE: ' . $pm['pmtopic'];
            }
            $pm['pmtopic'] = htmlspecialchars($pm['pmtopic']);
            $pm['pmtext'] = "[quote][i]$pm[username] schrieb:[/i]\n". htmlspecialchars($pm['pmtext']) ."[/quote]";
            $recipient = htmlspecialchars($pm['username']);
        }

        $navpath .= 'Neue Privatnachricht';

        eval($TMsg->GetTemplate("CONTENT"));
        eval($TFrame->GetTemplate());
    }

}
elseif( $action == "show" )
{
    $TFrame = new Template("templates/" . $style['styletemplate'] . "/frame.html");
    $TMsg = new Template("templates/" . $style['styletemplate'] . "/showprivmsg.html");

    $r_pm = thwb_query("SELECT pm.pmid, pm.pmfromid, pm.pmtoid, pm.pmtopic, pm.pmtext, pm.pmtime,
    pm.pmflags, pm.pmfolder, user.username, user.usertitle
        FROM ".$pref."pm as pm, ".$pref."user as user
        WHERE pm.pmid='".intval($pm['pmid'])."' AND pm.pmfromid=user.userid
        ORDER BY pm.pmtime DESC");

    $pm = mysql_fetch_array($r_pm);
    $pm['tousername'] = $g_user['username'];

    if( $pm['pmtoid'] != $g_user['userid'] )
    {
        message("Error", "You don't have permission to access this page");
    }

    if( $pm['pmfolder'] == 1 )
    {
        $pm['pmfromid'] = $g_user['userid'];
        $pm['userid'] = $g_user['userid'];
        $pm['tousername'] = $pm['username'];
        $pm['username'] = $g_user['username'];
        $pm['usertitle'] = ((empty($g_user['usertitle'])) ? "" : $g_user['usertitle']);
    }

    $pm['pmtopic'] = parse_code($pm['pmtopic']);
    $pm['pmtext'] = parse_code($pm['pmtext'], 1, ($config['imageslevel'] != 2), 1, 1, $config['smilies']);
    $pm['pmtime'] = form_date($pm['pmtime']);
    $pm['username'] = parse_code($pm['username']);


    // als gelesen markieren!!
    thwb_query("UPDATE ".$pref."pm SET pmflags=0 WHERE pmid='".intval($pm['pmid'])."'");

    $navpath .= 'Privatnachricht Ansehen';

    eval($TMsg->GetTemplate("CONTENT"));
    eval($TFrame->GetTemplate());
}
else
{

    $Tpmrow = new Template("templates/" . $style['styletemplate'] . "/pmrow.html");
    $Tpmnomsg = new Template("templates/" . $style['styletemplate'] . "/pmnomessages.html");
    $TFrame = new Template("templates/" . $style['styletemplate'] . "/frame.html");
    $i_in = $i_out = 0;

    // private msgs
    $r_pm = thwb_query("SELECT pm.pmid, pm.pmfromid, pm.pmtopic, pm.pmtext, pm.pmtime,
        pm.pmflags, pm.pmfolder, user.username
        FROM ".$pref."pm as pm, ".$pref."user as user
        WHERE pm.pmtoid=$g_user[userid] AND pm.pmfromid=user.userid
        ORDER BY pm.pmtime DESC");

    $msgcount = mysql_num_rows($r_pm);
    if( $msgcount + 2 >= $config['max_privmsg'] )
    {
        $msgcount = '<font color="' . $style['color_err'] . '"><b>' . $msgcount . '</b></font>';
    }

    if( !isset($folder) || !$folder )
    {
        $folder = 0;
        $inboxsel = ' selected';
        $from_to = 'Von';
    }
    else
    {
        $folder = 1;
        $outboxsel = ' selected';
        $from_to = 'An';
    }

    $inbox_mails = 0;
    $inbox_mails_new = 0;
    $outbox_mails = 0;
    $PMROWS_INBOX = '';
    $PMROWS_OUTBOX = '';
    while( $pm = mysql_fetch_array($r_pm) )
    {
        if( $pm['pmfolder'] == 0 )
        {
            // Writing Inbox
            $i_in % 2 == 0 ? $cellbg = $style['CellA'] : $cellbg = $style['CellB'];

            $pm['pmtopic'] = parse_code($pm['pmtopic']);
            $pm['pmtime'] = form_date($pm['pmtime']);

            if( $pm['pmflags'] == 1 )
            {
                $pm['pmflags'] = "*new*";
                $inbox_mails_new++;
            }
            else
            {
                $pm['pmflags'] = "&nbsp;";
            }
            $pm['username'] = parse_code($pm['username']);

            eval($Tpmrow->GetTemplate("PMROWS_INBOX"));
            $inbox_mails++;

            $i_in++;
        }
        else
        {
            // Writing Outbox
            $i_out % 2 == 0 ? $cellbg = $style['CellA'] : $cellbg = $style['CellB'];

            $pm['pmtopic'] = parse_code($pm['pmtopic']);
            $pm['pmtime'] = form_date($pm['pmtime']);

            if( $pm['pmflags'] == 1 )
            {
                $pm['pmflags'] = "*new*";
                $outbox_mails_new++;
            }
            else
            {
                $pm['pmflags'] = "&nbsp;";
            }
            $pm['username'] = parse_code($pm['username']);

            eval($Tpmrow->GetTemplate("PMROWS_OUTBOX"));
            $outbox_mails++;

            $i_out++;
        }
    }

    if( $inbox_mails == 0 )
    {
        eval($Tpmnomsg->GetTemplate("PMROWS_INBOX"));
    }
    if( $outbox_mails == 0 )
    {
        eval($Tpmnomsg->GetTemplate("PMROWS_OUTBOX"));
    }

    $full_box = $inbox_mails + $outbox_mails;
    $diskquota = round( ( $full_box / $config['max_privmsg'] ) * 100);

    if ($inbox_mails_new == 0 )
    {
        $msg_new_msgs['inbox'] = ", davon keine ungelesen";
    }
    elseif ( $inbox_mails_new == 1 )
    {
        $msg_new_msgs['inbox'] = ", davon eine Nachricht ungelesen";
    }
    else
    {
        $msg_new_msgs['inbox'] = ", davon $inbox_mails_new Nachricht ungelesen";
    }
    // Generating Grafic Diskquota
    /*
    ttt: doesn't seem to be in use, needs fixing anyway
    ----
    $QUOTA = '';
    if ( $config['pmquota'] == 1 )
    {
        if ($config['pmalive'] > 0)
        {
            $pic['start_sentence'] = "Achtung: Alle Nachrichten, die älter sind als ".$config['pmalive']." Tage werden automatisch gelöscht";
        }
        else
        {
            $pic['start_sentence'] = "Bitte achten Sie auf das Nachrichtenlimit. Sollte ihr Konto überfüllt sein, können Sie keine Nachrichten mehr empfangen.";
        }
        if ( $diskquota == 0 )
        {
            $pic['imageline'] = "&nbsp;";
        }
        elseif ( $diskquota < 25 )
        {
            $pic['start_width'] = 10;
            $pic['end_width'] = 10;
            $pic['middle_width'] = (300*($diskquota/100))-($pic['start_width']+$pic['end_width']);
        }
        else
        {
            $pic['start_width'] = 29;
            $pic['end_width'] = 29;
            $pic['middle_width'] = (300*($diskquota/100))-($pic[start_width]+$pic[end_width]);
        }
        $Tquota = new Template("templates/" . $style['styletemplate'] . "/pmquota.html");
        eval($Tquota->GetTemplate("QUOTA"));
    }*/
    // Initiating Template
    $Tpm = new Template("templates/" . $style['styletemplate'] . "/pm.html");

    $navpath .= 'Privatnachrichten &Uuml;bersicht';

    eval($Tpm->GetTemplate("CONTENT"));
    eval($TFrame->GetTemplate());
}
