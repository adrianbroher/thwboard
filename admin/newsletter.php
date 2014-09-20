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

$end_time = time() + substr(microtime(), 0, 10) + ini_get('max_execution_time');

include "common.inc.php";

if(empty($type) || $type != 'list')
{
    tb_header();
}

if( !$sendnewsletter )
{
    echo '<form method="post" action="newsletter.php"><B>Send newsletter</B><BR>
  <hr width="100%" noshade>
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
        <td colspan="2" bgcolor="#999999">
        <font size="2" color="white"><b>General</b></font>
      </td>
    </tr>
    <tr>
      <td width="100">
        Subject
      </td>
      <td>
      <input class="tbinput" type="text" size="40" name="subject" value="'.$config['board_name'].' - Newsletter">
      </td>
    </tr>
    <tr>
      <td valign="top">Content</td>
      <td>
        <textarea class="tbinput" name="content" rows="20" cols="40"></textarea>
      </td>
    </tr>
    <tr>
      <td valign="top">Send to</td>
      <td>
        <select class="tbinput" name="a_group[]" size="6" multiple>
          <option value="-1" selected>- All groups -</option>';
    // groups
    $r_group = query("SELECT groupid, name FROM $pref"."group ORDER BY name ASC");
    while( $group = mysql_fetch_array($r_group) )
    {
        print '<option value="'.$group['groupid'].'">'.$group['name'].'</option>';
    }

    print '
        </select><font size="1"><br>(Use CTRL to select multiple)</font>
      </td>
    </tr>
    <tr>
       <td colspan="2" bgcolor="#999999">
          <font size="2" color="white"><b>Send newsletter as</b></font>
      </td>
    </tr>
    <tr>
      <td valign="top" colspan=2>
      <input type="radio" name="type" value="mail" checked>
      eMail (<strong>Attention: this does not work reliably for big boards. Use a mass mailing program (like <a href="http://www.group-mail.com">Group Mail</a>) and the email listing feature instead.</strong>)<br>
      <input type="radio" name="type" value="pm">
      Private Message <br>
      <input type="radio" name="type" value="list">
        Generate mail address list
      </td>
    </tr>
    <tr>
    <td colspan="2">
      <input type="hidden" value="' . $session . '" name="session">
      <input type="submit" name="sendnewsletter" value="Send newsletter !">
    </td>
  </tr>
</table>
</form>';
    tb_footer();
    exit;
}

if(isset($HTTP_POST_VARS['groups']))
{
  $a_group = array(-1);
  $where_sql = $HTTP_POST_VARS['groups'];
}
else
{
  $where_sql = (($a_group[0] == -1) ? 'WHERE 1' : '');
}

if(!isset($at))
{
  $at = 0;
}

if( count($a_group) > 0 )
{
  $content = str_replace("\n", "\r\n", $content);

  if($a_group[0] != -1 )
    {
      while( list(, $groupid) = each($a_group) )
        {
          $where_sql .= " OR INSTR(groupids, ',$groupid,')>0";
        }
        $where_sql = 'WHERE '.substr($where_sql, 4);
    }

  $r_user = query("SELECT userid, useremail FROM $pref"."user $where_sql AND userid > $at");

  $usercount = $at + mysql_num_rows($r_user);

  $i = 1;

  if($type == 'list')
  {
      header('Content-type: text/plain');

      while($user = mysql_fetch_array($r_user))
      {
          echo $user['useremail']."\n";
      }

      exit;
  }

  while( $user = mysql_fetch_array($r_user) )
    {
      if(time() + substr(microtime(), 0, 10) < ($end_time - 0.25))
      {
          if($i % 100)
          {
              if( $type == "mail" )
              {
                  @mail($user['useremail'], $subject, $content, "From: ".$config['board_admin']);
              }
              else
              {
                query("INSERT INTO $pref"."pm (pmfromid, pmtoid, pmtopic, pmtext, pmtime, pmflags, pmfolder)
                    VALUES ($g_user[userid], $user[userid],'" . addslashes($subject) . "','" . addslashes($content) .
                      "',".time().", 1, 0);");
              }
          }
          else
          {
              if(time() + substr(microtime(), 0, 10) < ($end_time - 1.25))
              {
                  sleep(1.0);
              }
              else
              {
                  print('The script execution time limit has been exceeded.<br><form method="post"><input type="hidden" name="session" value="'.$session.'"><input type="hidden" name="sendnewsletter" value="'.$sendnewsletter.'"><input type="hidden" name="subject" value="'.$subject.'"><input type="hidden" name="content" value="'.$content.'"><input type="hidden" name="type" value="'.$type.'"><input type="hidden" name="at" value="'.$user['userid'].'"><input type="hidden" name="groups" value="'.$where_sql.'"><input type="submit" name="submit" value="resume &gt;&gt;"></form>');
                  tb_footer();
                  exit();
              }
          }
      }
      else
      {
          print('The script execution time limit has been exceeded.<br><form method="post"><input type="hidden" name="session" value="'.$session.'"><input type="hidden" name="sendnewsletter" value="'.$sendnewsletter.'"><input type="hidden" name="subject" value="'.$subject.'"><input type="hidden" name="content" value="'.$content.'"><input type="hidden" name="type" value="'.$type.'"><input type="hidden" name="at" value="'.$user['userid'].'"><input type="hidden" name="groups" value="'.$where_sql.'"><input type="submit" name="submit" value="resume &gt;&gt;"></form>');
          tb_footer();
          exit();
      }

      ++$i;
    }

  echo "Newsletter has been sent! (<b>$usercount</b> users)";
}
else
{
  print 'You need to select a userlevel/group ..';
}

tb_footer();
