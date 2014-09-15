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

include "common.inc.php";
tb_header();
if ($action=="ListMails") {
print('<b>E-Mail Manager / Select E-Mail Msg</b><br><br>
send_passwort.mail [ <A HREF="mails.php?session='.$session.'&action=Show_pwmail">edit</a> ]<BR>
newreply.mail [ <A HREF="mails.php?session='.$session.'&action=Show_newreply">edit</a> ]<BR>
register.mail [ <A HREF="mails.php?session='.$session.'&action=Show_register">edit</a> ]<BR>
');

} elseif ($action=="Show_pwmail") {
$mail_send_password = fopen("../templates/mail/send_password.mail", 'r');
$mail_send_password_size = filesize("../templates/mail/send_password.mail");
$mail_send_password_content = fread($mail_send_password,$mail_send_password_size);
$mail_send_password_content = str_replace('$user[username]','{username}',$mail_send_password_content);
$mail_send_password_content = str_replace('$config[board_name]','{boardname}',$mail_send_password_content);
$mail_send_password_content = str_replace('$user[userpassword]','{userpw}',$mail_send_password_content);
$mail_send_password_content = str_replace('$config[board_baseurl]/login.php','{link_to_login}',$mail_send_password_content);
print('<b>E-Mail Manager / Edit send_passwort.mail</b><br><br>');
print('<I>Content "send_password.mail"</I><BR>
<form action="mails.php" method=post>
<input type=hidden name=action value="save_pwmail">
<input type=hidden name=session value="'.$session.'">
<textarea name="new_send_password" cols=50 rows=25 style="width:450px">'.$mail_send_password_content.'</textarea><P>
<B>available inserts:</B><BR>
{boardname} - Name of Board<BR>
{link_to_login} - Direct Link to the login.php<BR>
{username} - Username<BR>
{userpw} - The new passwort<P>
<input type=submit value="Save Settings">
');
} elseif ($action=="save_pwmail") {

$mail_send_password = fopen("../templates/mail/send_password.mail", 'w');
$new_send_password = str_replace('{username}','$user[username]',$new_send_password);
$new_send_password = str_replace('{boardname}','$config[board_name]',$new_send_password);
$new_send_password = str_replace('{userpw}','$user[userpassword]',$new_send_password);
$new_send_password = str_replace('{link_to_login}','$config[board_baseurl]/login.php',$new_send_password);
$new_send_password = str_replace("\'","'",$new_send_password);
$new_send_password = str_replace('\"','"',$new_send_password);
fwrite($mail_send_password, $new_send_password);
print('Settings saved...');
} elseif ($action=="Show_newreply") {

$mail_newreply = fopen("../templates/mail/newreply.mail", 'r');
$mail_newreply_size = filesize("../templates/mail/newreply.mail");
$mail_newreply_content = fread($mail_newreply,$mail_newreply_size);
$mail_newreply_content = str_replace('$email[threadtopic]','{thread_topic}',$mail_newreply_content);
$mail_newreply_content = str_replace('$config[board_baseurl]/showtopic.php?thread[threadid]=$thread[threadid]','{link_to_thread}',$mail_newreply_content);
$mail_newreply_content = str_replace('$config[board_baseurl]/misc.php?action=unsubscribe&thread[threadid]=$thread[threadid]','{link_to_unsubscribe}',$mail_newreply_content);
$mail_newreply_content = str_replace('$config[board_name]','{boardname}',$mail_newreply_content);
print('<b>E-Mail Manager / Edit newreply.mail</b><br><br>');
print('<I>Content "newreply.mail"</I><BR>
<form action="mails.php" method=post>
<input type=hidden name=action value="save_newreply">
<input type=hidden name=session value="'.$session.'">
<textarea name="new_newreply" cols=50 rows=25 style="width:450px">'.$mail_newreply_content.'</textarea><P>
<B>available inserts:</B><BR>
{boardname} - Name of Board<BR>
{thread_topic} - Topic of the Thread<BR>
{link_to_unsubscribe} - Link to unsubscribe the E-Mail-Notify<BR>
{link_to_thread} - Direct link to the Post<P>
<input type=submit value="Save Settings">');
} elseif ($action=="save_newreply") {

$mail_newreply = fopen("../templates/mail/newreply.mail", 'w');
$new_newreply = str_replace('{thread_topic}','$email[threadtopic]',$new_newreply);
$new_newreply = str_replace('{link_to_thread}','$config[board_baseurl]/showtopic.php?thread[threadid]=$thread[threadid]',$new_newreply);
$new_newreply = str_replace('{link_to_unsubscribe}','$config[board_baseurl]/misc.php?action=unsubscribe&thread[threadid]=$thread[threadid]',$new_newreply);
$new_newreply = str_replace('{boardname}','$config[board_name]',$new_newreply);
$new_newreply = str_replace("\'","'",$new_newreply);
$new_newreply = str_replace('\"','"',$new_newreply);
fwrite($mail_newreply, $new_newreply);
print('Settings saved...');

} elseif ($action=="Show_register") {

$mail_register = fopen("../templates/mail/register.mail", 'r');
$mail_register_size = filesize("../templates/mail/register.mail");
$mail_register_content = fread($mail_register,$mail_register_size);
$mail_register_content = str_replace('$register[username]','{username}',$mail_register_content);
$mail_register_content = str_replace('$register[userpassword]','{userpw}',$mail_register_content);
$mail_register_content = str_replace('$config[board_baseurl]/login.php','{link_to_login}',$mail_register_content);
$mail_register_content = str_replace('$config[board_baseurl]/','{link_to_board}',$mail_register_content);
$mail_register_content = str_replace('$config[board_name]','{boardname}',$mail_register_content);
print('<b>E-Mail Manager / Edit newreply.mail</b><br><br>');
print('<I>Content "newreply.mail"</I><BR>
<form action="mails.php" method=post>
<input type=hidden name=action value="save_register">
<input type=hidden name=session value="'.$session.'">
<textarea name="new_register" cols=50 rows=25 style="width:450px">'.$mail_register_content.'</textarea><P>
<B>available inserts:</B><BR>
{username} - The New Username<BR>
{userpw} - The new user password<BR>
{link_to_login} - Direct Link to login<BR>
{link_to_board} - Direct Link to the Board<BR>
{boardname} - Name of the Board<BR>
<input type=submit value="Save Settings">');

} elseif ($action=="save_register") {

$mail_register = fopen("../templates/mail/register.mail", 'w');
$new_register = str_replace('{username}','$register[username]',$new_register);
$new_register = str_replace('{userpw}','$register[userpassword]',$new_register);
$new_register = str_replace('{link_to_login}','$config[board_baseurl]/login.php',$new_register);
$new_register = str_replace('{link_to_board}','$config[board_baseurl]/',$new_register);
$new_register = str_replace('{boardname}','$config[board_name]',$new_register);
$new_register = str_replace("\'","'",$new_register);
$new_register = str_replace('\"','"',$new_register);
fwrite($mail_register, $new_register);
print('Settings saved...');

} elseif ($action=="Show_register") {


} else {




$mail_register = fopen("../templates/mail/register.mail", 'r');
$mail_register_size = filesize("../templates/mail/register.mail");
$mail_register_content = fread($mail_register,$mail_register_size);


print('<b>E-Mail Manager</b><br><br>');
print('
<I>Content "register.mail"</I><BR>
<textarea name="new_register" cols=50 rows=15 style="width:450px">'.$mail_register_content.'</textarea><P>
available inserts:

');
}


tb_footer();
