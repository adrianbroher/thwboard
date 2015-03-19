<?php
/**
 * ThWboard - PHP/MySQL Bulletin Board System
 * ==========================================
 *
 * Copyright (C) 2000-2006 by ThWboard Development Group
 * Copyright (C) 2015 by Marcel Metz
 *
 * This file is part of ThWboard
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program;  If not, see <http://www.gnu.org/licenses/>.
 */

require __DIR__.'/install_lang.php';
require __DIR__.'/../vendor/autoload.php';

$template = new \League\Plates\Engine(__DIR__.'/../templates/builtin/html');
$template->registerFunction('_', 'lng');

error_reporting(0); // E_ERROR | E_WARNING | E_PARSE

function create_tables(PDO $pdo, $prefix, $delete_existing)
{
    $pref = $prefix;

/*
    These are the initial databases and values.
    If you add new queries, watch out for semicolons:
    NEVER EVER use them inside a query, only at the end.
    This huge chunk is split up at the semicolons into
    individual queries, so if you've got a semicolon somewhere
    in between, you'll end up with an invalid query!

    Semicolon usage is allowed in queries, as long as
    you dont put a \r\n or \n after them. --dp

*/

    $mysql_data = "

CREATE TABLE $pref"."adminlog (
  logid int(10) unsigned NOT NULL auto_increment,
  logtype varchar(12) NOT NULL default '',
  logtime int(10) unsigned NOT NULL default '0',
  loguser varchar(64) NOT NULL default '0',
  logip varchar(16) NOT NULL default '',
  logscript varchar(32) NOT NULL default '',
  logaction varchar(32) NOT NULL default '',
  lognotes varchar(255) NOT NULL default '',
  PRIMARY KEY  (logid)
) ENGINE=MyISAM;


CREATE TABLE $pref"."avatar (
  avatarid int(8) unsigned NOT NULL auto_increment,
  avatarname varchar(128) NOT NULL default '',
  avatarurl varchar(255) NOT NULL default '',
  PRIMARY KEY  (avatarid),
  UNIQUE KEY avatarname (avatarname)
) ENGINE=MyISAM;


CREATE TABLE $pref"."ban (
  banid int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  banpubreason mediumtext NOT NULL,
  banreason mediumtext NOT NULL,
  bansetbyid int(10) unsigned NOT NULL default '0',
  banexpire int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (banid),
  KEY userid (userid)
) ENGINE=MyISAM;


CREATE TABLE $pref"."bannedwords (
  wordid int(11) NOT NULL auto_increment,
  banword varchar(128) NOT NULL default '',
  modword varchar(128) NOT NULL default '',
  ispartofword tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (wordid),
  UNIQUE KEY banword (banword)
) ENGINE=MyISAM;


CREATE TABLE $pref"."board (
  boardid int(10) unsigned NOT NULL auto_increment,
  boardname varchar(255) NOT NULL default '',
  boardlastpost int(11) unsigned NOT NULL default '0',
  boardthreads int(10) unsigned NOT NULL default '0',
  boardposts int(10) unsigned NOT NULL default '0',
  boarddescription text NOT NULL,
  categoryid int(11) unsigned NOT NULL default '0',
  boardorder int(11) unsigned NOT NULL default '0',
  styleid int(10) unsigned NOT NULL default '0',
  boardlastpostby varchar(64) NOT NULL default '',
  boardthreadtopic varchar(255) NOT NULL default '',
  boardthreadid int(10) unsigned NOT NULL default '0',
  boarddisabled tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (boardid),
  KEY boardid (boardid)
) ENGINE=MyISAM;

INSERT INTO $pref"."board (boardid, boardname, boardlastpost, boardthreads, boardposts, boarddescription, categoryid, boardorder, styleid, boardlastpostby, boardthreadtopic, boardthreadid, boarddisabled)
VALUES (
'', 'Board #1', '0', '0', '0', 'Beschreibung von Board #1', '1', '1', '0', '', '', '0', '0'
);


CREATE TABLE $pref"."calendar (
  eventid int(11) NOT NULL auto_increment,
  eventtime date default NULL,
  eventsubject varchar(255) default NULL,
  eventtext text,
  eventactive smallint(1) NOT NULL default '1',
  userid int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (eventid)
) ENGINE=MyISAM;




CREATE TABLE $pref"."category (
  categoryid int(11) unsigned NOT NULL auto_increment,
  categoryname varchar(250) NOT NULL default '',
  categoryorder int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (categoryid),
  KEY categoryid (categoryid)
) ENGINE=MyISAM;


INSERT INTO $pref"."category VALUES (1,'Kategorie 1',1);


CREATE TABLE $pref"."group (
  groupid int(10) unsigned NOT NULL auto_increment,
  name varchar(128) NOT NULL default '',
  accessmask varchar(50) NOT NULL default '',
  nodelete tinyint(1) unsigned NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  titlepriority int(11) NOT NULL default '0',
  PRIMARY KEY  (groupid)
) ENGINE=MyISAM;


INSERT INTO $pref"."group VALUES (1, 'Default Group', '1110001000000000000000', 1, '', 0);
INSERT INTO $pref"."group VALUES (2, 'Guest Group', '1000000000000000000000', 1, '', 0);
INSERT INTO $pref"."group VALUES (3, 'Admin Group', '1111111111111111111111', 1, 'Administrator', 0);
INSERT INTO $pref"."group VALUES (4, 'Moderator Group', '1111010110001001110110', 1, 'Moderator', 0);


CREATE TABLE $pref"."groupboard (
  boardid int(10) unsigned NOT NULL default '0',
  groupid int(10) unsigned NOT NULL default '0',
  accessmask varchar(50) NOT NULL default '0',
  KEY boardid (boardid,groupid)
) ENGINE=MyISAM;




CREATE TABLE $pref"."lastvisited (
  boardid int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  lastvisitedtime int(10) unsigned NOT NULL default '0',
  KEY useridboardid (userid,boardid)
) ENGINE=MyISAM;




CREATE TABLE $pref"."news (
  newsid int(11) NOT NULL auto_increment,
  newstopic varchar(250) NOT NULL default '',
  newstext mediumtext NOT NULL,
  newstime int(11) NOT NULL default '0',
  boardid varchar(255) NOT NULL default '',
  PRIMARY KEY  (newsid),
  KEY newsid (newsid)
) ENGINE=MyISAM;




CREATE TABLE $pref"."online (
  sessionid varchar(32) NOT NULL default '',
  onlineip varchar(16) NOT NULL default '',
  onlinetime int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  KEY onlinetime (onlinetime),
  KEY sessionid (sessionid)
) ENGINE=Heap;

CREATE TABLE ${pref}flood (
  userid int(10) NOT NULL default '0',
  type tinyint(1) default NULL,
  time timestamp(6) NOT NULL,
  ip varchar(16) NOT NULL default ''
) ENGINE=HEAP;

CREATE TABLE $pref"."pm (
  pmid int(10) unsigned NOT NULL auto_increment,
  pmfromid int(10) unsigned NOT NULL default '0',
  pmtoid int(10) unsigned NOT NULL default '0',
  pmtopic varchar(128) NOT NULL default '',
  pmtext mediumtext NOT NULL,
  pmtime int(10) unsigned NOT NULL default '0',
  pmflags tinyint(4) NOT NULL default '0',
  pmfolder tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (pmid),
  KEY pmid (pmid)
) ENGINE=MyISAM;




CREATE TABLE $pref"."post (
  postid int(10) unsigned NOT NULL auto_increment,
  posttime int(11) unsigned default NULL,
  posttext text NOT NULL,
  userid int(10) unsigned NOT NULL default '0',
  postguestname varchar(64) NOT NULL default '',
  threadid int(11) unsigned NOT NULL default '0',
  postemailnotify tinyint(1) unsigned NOT NULL default '0',
  postip varchar(16) NOT NULL default '',
  postsmilies tinyint(1) NOT NULL default '0',
  postcode tinyint(1) NOT NULL default '0',
  postsignature tinyint(1) NOT NULL default '0',
  postlasteditby varchar(64) NOT NULL default '',
  postlastedittime int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (postid),
  KEY postid (postid),
  KEY userid (userid),
  KEY threadid (threadid,userid)
) ENGINE=MyISAM;




CREATE TABLE $pref"."qlink (
  linkid int(3) unsigned NOT NULL auto_increment,
  linkcaption varchar(50) NOT NULL default '',
  linkalt varchar(255) NOT NULL default '',
  linkhttp text NOT NULL,
  linkcounter int(11) unsigned NOT NULL default '0',
  linkactive int(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (linkid)
) ENGINE=MyISAM;




CREATE TABLE $pref"."rank (
  rankid int(10) unsigned NOT NULL auto_increment,
  ranktitle varchar(255) NOT NULL default '',
  rankimage varchar(255) NOT NULL default '',
  rankposts int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (rankid)
) ENGINE=MyISAM;




CREATE TABLE $pref"."registry (
  keyname varchar(255) NOT NULL default '',
  keyvalue mediumtext NOT NULL,
  keytype enum('string','integer','boolean','array') NOT NULL default 'string',
  keydescription varchar(32) NOT NULL default '',
  keydetails varchar(255) NOT NULL default '',
  keygroupid int(10) unsigned NOT NULL default '0',
  keydisplayorder int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (keyname)
) ENGINE=MyISAM;


INSERT INTO $pref"."registry VALUES ('board_name','Forum-name','string','Board name','',1,'');
INSERT INTO $pref"."registry VALUES ('board_baseurl','http://localhost/','string','Board URL','Used in eMails and some board features. <b>It is vital to set this correctly!</b>',1,1);
INSERT INTO $pref"."registry VALUES ('board_admin','root@localhost','string','Board Admin/Tech eMail','',1,2);
INSERT INTO $pref"."registry VALUES ('site_name','Your site\'s name','string','Board site name','',1,3);
INSERT INTO $pref"."registry VALUES ('site_url','Your site\'s URL','string','Board site URL','',1,4);
INSERT INTO $pref"."registry VALUES ('userrating','','boolean','Enable userrating','(currently unavailable)',2,'');
INSERT INTO $pref"."registry VALUES ('privatemessages','1','boolean','Allow private messages?','',2,1);
INSERT INTO $pref"."registry VALUES ('smilies','','boolean','Allow smilies?','',2,2);
INSERT INTO $pref"."registry VALUES ('enable_quicklinks','','boolean','Enable quicklinks?','',2,3);
INSERT INTO $pref"."registry VALUES ('enable_ranks','','boolean','Enable userranks?','',2,4);
INSERT INTO $pref"."registry VALUES ('debugmode','','boolean','Enable debug-mode?','Prints debug/development messages for admins',2,5);
INSERT INTO $pref"."registry VALUES ('forumclosed','','boolean','Forum closed?','Allows you to close your forums, e.g. for maintenance. Note: Administrators can still view the forums.',5,1);
INSERT INTO $pref"."registry VALUES ('use_email','','boolean','Use eMail functions?','If your server does not support sending eMails via php\'s mail() function, disable this.',2,7);
INSERT INTO $pref"."registry VALUES ('guestprefix','~','string','Guest name prefix','The char put in front of a guest name.',2,9);
INSERT INTO $pref"."registry VALUES ('showprivateboards','1','boolean','Show private boards?','View boards that are inaccessisble for a user? (The user is still not able to view it)',2,10);
INSERT INTO $pref"."registry VALUES ('uppercase_prot','1','boolean','Use uppercase topic protection?','Prevents users from using only caps in topics. CAN BE VERY USEFUL',2,11);
INSERT INTO $pref"."registry VALUES ('allowregister','1','boolean','Allow users to register?','You can disable the register option for users here.',2,12);
INSERT INTO $pref"."registry VALUES ('vars_t_amount','30','integer','Topics per page','The number of topic rows per board-page.',3,'');
INSERT INTO $pref"."registry VALUES ('vars_m_amount','25','integer','Message amount','The number of posts per page.',3,1);
INSERT INTO $pref"."registry VALUES ('subject_maxlength','64','integer','Topic maxlength','The maximum length (in chars) allowed in a thread\'s topic.',3,2);
INSERT INTO $pref"."registry VALUES ('subject_minlength','2','integer','Topic minlength','The minimum length.',3,3);
INSERT INTO $pref"."registry VALUES ('message_maxlength','10000','integer','Message maxlength','The maximum length in a post\'s text (in chars).',3,4);
INSERT INTO $pref"."registry VALUES ('message_minlength','2','integer','Message minlength','Minimum post length.',3,5);
INSERT INTO $pref"."registry VALUES ('showpostslevel','2','integer','Show posts level','Show posts:<br>0 - Never, don\'t show any post counts.<br>1 - Show own postcount only.<br>2 - No restriction',3,6);
INSERT INTO $pref"."registry VALUES ('imageslevel','1','integer','Image usage level','Allow images:<br>0 - No restriction<br>1 - Allow images in posts only<br>2 - Do not allow any images',3,7);
INSERT INTO $pref"."registry VALUES ('max_usernamelength','24','integer','Maximum user name length','The maximum length of a username, in chars.',3,8);
INSERT INTO $pref"."registry VALUES ('min_usernamelength','2','integer','Minimum user name length','The minimum length of a username, in chars.',3,9);
INSERT INTO $pref"."registry VALUES ('pm_maxlength','8000','integer','Private message maximum length','The maximum length of a private message, in chars.',3,10);
INSERT INTO $pref"."registry VALUES ('postdelay','30','integer','Post flood protection','Minimum delay between posts, in seconds.',4,'');
INSERT INTO $pref"."registry VALUES ('editlimit','1800','integer','Edit time limit','Users will be unable to edit posts after posttime + limit. Set to 0 to disable.',4,1);
INSERT INTO $pref"."registry VALUES ('timeoffset','','integer','Time offset','Time offset, in hours (can be negative)',5,'');
INSERT INTO $pref"."registry VALUES ('closedmsg','Sorry\r\n\r\nDas Forum ist momentan leider nicht verf&uuml;gbar.','array','Forum closed message','This message appears if users try to access the closed forum.',5,2);
INSERT INTO $pref"."registry VALUES ('bannednames','','array','Nick register protection','Allows you to specify invalid nicks. Users will be unable to register these nicks, one name per line. Example: \"adm\" disallows Admin as well as fooadmbar...',5,4);
INSERT INTO $pref"."registry VALUES ('bannedemails','','array','Email register protection','Allows you to specify invalid email addresses. Use one eMail per line, eg @aol bans dude@aol.com as well as notme@aol.net',5,3);
INSERT INTO $pref"."registry VALUES ('version','2.85','string','Version','The internal version, please do not change.','','');
INSERT INTO $pref"."registry VALUES ('sig_maxlen','600','integer','Max. signature length','The maximum length of a signature, in bytes (chars).',6,1);
INSERT INTO $pref"."registry VALUES ('max_privmsg','30','integer','Max PM count','The maximum amount of private messages per user. Users will be unable to recieve PMs if their box is full.',3,11);
INSERT INTO $pref"."registry VALUES ('pmalive','','integer','PM max life','Set the maximum age of private messages, in days. Set to 0 to disable life check.',3,12);
INSERT INTO $pref"."registry VALUES ('sig_maxlines','5','integer','Max. signature lines','The maximum number of lines allowed in a signature. Note that a line can be wrapped depending on the users browser size. Thus you should set an appropriate max length in chars as well. One average line has about 120 chars at a resolution of 1024*768.',6,2);
INSERT INTO $pref"."registry VALUES ('sig_restrict','1','boolean','Enable signature restrictions?','Enable this to restrict the signature length using the options below.',6,'');
INSERT INTO $pref"."registry VALUES ('uselastthreads', '0', 'boolean', 'Enable last-threads-form?', 'Enables a select form on the index page to display threads within the last 3/5/7.. days.', '2', '13');
INSERT INTO $pref"."registry VALUES ('max_useronline','0|0','string','Do not modify or delete','Do not modify or delete','0','0');
INSERT INTO $pref"."registry VALUES ('default_groupid', '1', 'integer', 'Dont modify!', 'Dont modify!', 0, 0);
INSERT INTO $pref"."registry VALUES ('guest_groupid', '2', 'integer', 'Dont modify!', 'Dont modify!', 0, 0);
INSERT INTO $pref"."registry VALUES ('avatarheight', '64', 'integer', 'Avatar maximum height', 'The maximum height of the user-defined Avatars.', 7, 2);
INSERT INTO $pref"."registry VALUES ('avatarwidth', '64', 'integer', 'Avatar maximum width', 'The maximum width of the user-defined Avatars.', 7, 1);
INSERT INTO $pref"."registry VALUES ('useravatar', '0', 'integer', '<a name=\"avatar\">Avatar-Settings', 'Enable avatars:<br>0 - Avatars are disabled<br>1 - Only pre-installed avatars<br>2 - Only user-defined avatars<br>3 - Both', 7, 0);
INSERT INTO $pref"."registry VALUES ('usebwordprot', '3', 'integer', 'Badwords protection', 'Do not modify', '0','7');
INSERT INTO $pref"."registry VALUES ('compression', '0', 'boolean', 'Enable compression?', 'Gzip compression can save a lot of traffic, but requires a bit more cpu time', '2', '14');
INSERT INTO $pref"."registry VALUES ('slow_search', '0', 'boolean', 'Case-insensitive (slow) search?', 'Slower, but more precisely.', 2, 15);
INSERT INTO $pref"."registry VALUES ('session_timeout', '1800', 'integer', 'Session Timeout', 'Time for Sessions to expire.', 3, 13);
INSERT INTO ${pref}registry VALUES ('flood_login_count', '3', 'integer', 'Login failure count', 'Number of failed logins before the account gets locked for specific IP.<br>(0 to disable)', '8', '1' );
INSERT INTO ${pref}registry VALUES ('flood_login_timeout', '15', 'integer', 'Login failure timeout', 'Time (in minutes) for which an account gets locked after several failed logins.', '8', '2');
INSERT INTO ${pref}registry VALUES ('flood_register_count', '5', 'integer', 'Registration count', 'Number of registrations before register gets locked for specific IP.<br>(0 to disable)', '8', '3' );
INSERT INTO ${pref}registry VALUES ('flood_register_timeout', '15', 'integer', 'Registration timeout', 'Time (in minutes) for which an IP gets locked after several registrations.', '8', '4');
INSERT INTO ${pref}registry VALUES ('flood_mail_count', '3', 'integer', 'Mail count', 'Number of sent mails before mail function gets locked for specific user.<br>(0 to disable)', '8', '5' );
INSERT INTO ${pref}registry VALUES ('flood_mail_timeout', '15', 'integer', 'Mail timeout', 'Time (in minutes) for which mail function gets locked after several sent mails.', '8', '6');
INSERT INTO ${pref}registry VALUES ('debug_what', '0', 'integer', 'Error Reporting', '0 - Disabled<br>1 - PHP only<br>2 - SQL only<br>3 - both', '9', '0');
INSERT INTO ${pref}registry VALUES ('debug_mail', '0', 'boolean', 'Error Mails', 'Send error reports as email? This requires &quot;Use eMail Functions&quot; and &quot;Board Admin/Tech eMail&quot;.', '9', '1');
INSERT INTO ${pref}registry VALUES ('debug_do_log', '0', 'boolean', 'Log errors?', 'Log errors to file?<br>In order to use this feature, you must create a directory with the correct permissions and set the path below.<br><strong>It is highly recommended that this directory be secured by a .htaccess</strong>.', '9', '2');
INSERT INTO ${pref}registry VALUES ('debug_log_path', 'logs/thwb_err', 'string', 'Log file path', 'path to the log file and log file prefix.<br><strong>See above</strong>.', '9', '3');

CREATE TABLE $pref"."registrygroup (
  keygroupid int(10) unsigned NOT NULL auto_increment,
  keygroupname varchar(64) NOT NULL default '',
  keygroupdisplayorder int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (keygroupid)
) ENGINE=MyISAM;


INSERT INTO $pref"."registrygroup VALUES (1,'General','');
INSERT INTO $pref"."registrygroup VALUES (2,'Options',1);
INSERT INTO $pref"."registrygroup VALUES (3,'General vars',2);
INSERT INTO $pref"."registrygroup VALUES (4,'Post options',3);
INSERT INTO $pref"."registrygroup VALUES (5,'Misc',6);
INSERT INTO $pref"."registrygroup VALUES (6,'Signature control',4);
INSERT INTO $pref"."registrygroup VALUES (7,'Avatar settings',5);
INSERT INTO ${pref}registrygroup VALUES (8,'Flood Protection',7);
INSERT INTO ${pref}registrygroup VALUES (9,'Error Reporting',8);


CREATE TABLE $pref"."session (
  sessionid varchar(32) NOT NULL default '',
  lastaction int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  username varchar(64) NOT NULL default '',
  ip varchar(16) NOT NULL default '0',
  PRIMARY KEY  (sessionid),
  KEY sessionid (sessionid)
) ENGINE=MyISAM;




CREATE TABLE $pref"."style (
  styleid int(10) unsigned NOT NULL auto_increment,
  stylename varchar(32) NOT NULL default '',
  colorbg varchar(8) NOT NULL default '',
  colorbgfont varchar(8) NOT NULL default '',
  color4 varchar(8) NOT NULL default '',
  col_he_fo_font varchar(8) NOT NULL default '',
  color1 varchar(8) NOT NULL default '',
  CellA varchar(8) NOT NULL default '',
  CellB varchar(8) NOT NULL default '',
  border_col varchar(8) NOT NULL default '',
  color_err varchar(8) NOT NULL default '',
  col_link varchar(8) NOT NULL default '',
  col_link_v varchar(8) NOT NULL default '',
  col_link_hover varchar(8) NOT NULL default '',
  stdfont varchar(128) NOT NULL default '',
  boardimage varchar(128) NOT NULL default '',
  newtopicimage varchar(128) NOT NULL default '',
  styleispublic tinyint(1) unsigned NOT NULL default '0',
  styleisdefault tinyint(1) unsigned NOT NULL default '0',
  styletemplate varchar(32) NOT NULL default 'default',
  PRIMARY KEY  (styleid),
  KEY styleid (styleid)
) ENGINE=MyISAM;


INSERT INTO $pref"."style VALUES (1,'Default','#FAFAFA','#454545','#1B6ECC','#ECECEC','#323232','#FAFAFA','#F5F5F5','#DEDEDE','#990000','#1B6ECC','#1B6ECC','#69B271','Verdana, Helvetica','./templates/default/images/space.png','./templates/default/images/newtopic.png','',1,'default');


CREATE TABLE $pref"."thread (
  threadid int(11) unsigned NOT NULL auto_increment,
  threadtopic varchar(255) NOT NULL default '',
  threadtime int(11) unsigned NOT NULL default '0',
  threadauthor varchar(64) NOT NULL default '',
  threadreplies int(11) unsigned NOT NULL default '0',
  boardid int(11) unsigned NOT NULL default '0',
  threadlastreplyby varchar(64) NOT NULL default '',
  threadiconid smallint(6) unsigned NOT NULL default '0',
  threadviews int(10) unsigned NOT NULL default '0',
  threadlink int(10) unsigned NOT NULL default '0',
  threadclosed tinyint(1) unsigned NOT NULL default '0',
  threadtop tinyint(1) unsigned NOT NULL default '0',
  threadcreationtime int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (threadid),
  KEY threadid (threadid),
  KEY boardid (boardid,threadtop,threadtime)
) ENGINE=MyISAM;




CREATE TABLE $pref"."user (
  userid int(10) unsigned NOT NULL auto_increment,
  username varchar(64) NOT NULL default '',
  userjoin int(11) unsigned NOT NULL default '0',
  userlastpost int(11) unsigned NOT NULL default '0',
  userposts int(11) unsigned NOT NULL default '0',
  useremail varchar(128) NOT NULL default '',
  userpassword varchar(64) binary NOT NULL default '',
  userhomepage varchar(128) NOT NULL default '',
  userlocation varchar(128) NOT NULL default '',
  usericq int(11) unsigned NOT NULL default '0',
  useraim varchar(50) NOT NULL default '',
  usermsn varchar(50) NOT NULL default '',
  userbday date NOT NULL default '0000-00-00',
  styleid int(10) unsigned NOT NULL default '0',
  useroccupation varchar(128) NOT NULL default '',
  userinterests tinytext NOT NULL,
  usersignature mediumtext NOT NULL,
  userbanned tinyint(1) unsigned NOT NULL default '0',
  usertitle varchar(64) NOT NULL default '',
  userhidesig tinyint(1) unsigned NOT NULL default '0',
  userrating tinyint(3) unsigned NOT NULL default '0',
  uservotes smallint(5) unsigned NOT NULL default '0',
  userhideemail tinyint(1) unsigned NOT NULL default '0',
  userisadmin tinyint(1) unsigned NOT NULL default '0',
  userinvisible tinyint(1) unsigned NOT NULL default '0',
  usernoding tinyint(1) unsigned NOT NULL default '0',
  useravatar varchar(255) NOT NULL default '',
  groupids varchar(128) NOT NULL default '',
  usernodelete tinyint(1) unsigned NOT NULL default '0',
  useractivate tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (userid),
  UNIQUE KEY username (username),
  KEY userid (userid)
) ENGINE=MyISAM;
";

    // split at ;\r\n or ;\n
    $a_query = preg_split('/;[ \t]*\r?\n/m', $mysql_data);

    while (list(, $query) = each($a_query)) {
        $query = trim($query);

        if ($query) {
            if (strstr($query, 'CREATE TABLE') && $delete_existing) {
                ereg('CREATE TABLE ([^ ]*)', $query, $regs);
                $pdo->exec(
<<<SQL
DROP TABLE IF EXISTS
    {$regs[1]}
SQL
                );
            }

            $pdo->exec($query);
        }
    }
}

function WriteAccess($file)
{
    $fp = @fopen($file, 'w');

    if (!$fp) {
        return false;
    } else {
        fclose($fp);

        return true;
    }
}

/**
 * Checks if a column in the given table exists
 *
 * @param PDO $pdo A database connection. This connection needs to have
 *   a database selected.
 * @param string $table The name of the table, which should contain the
 *   given column.
 * @param string $column The name of the column, that should exists
 *   within the given table.
 */
function column_exists(PDO $pdo, $table, $column)
{
    $stmt = $pdo->prepare(
<<<SQL
SELECT
    *
FROM
    INFORMATION_SCHEMA.COLUMNS
WHERE
    TABLE_SCHEMA = DATABASE() AND
    TABLE_NAME = :tablename AND
    COLUMN_NAME = :columnname
SQL
    );

    $stmt->bindValue(':tablename', $table, PDO::PARAM_STR);
    $stmt->bindValue(':columnname', $column, PDO::PARAM_STR);
    $stmt->execute();

    return (0 != $stmt->rowCount());
}

function schema_version(PDO $pdo)
{
    $stmt = $pdo->query(
<<<SQL
SELECT
    keyvalue
FROM
    {$pdo->prefix}registry
WHERE
    keyname = 'version'
SQL
    );

    return $stmt->fetch(PDO::FETCH_COLUMN, 0);
}

function install_allowed()
{
    if (file_exists('../inc/config.inc.php')) {
        return 0;
    } else {
        return 1;
    }
}

/**
 * Write a configuration file to the given stream
 *
 * @param resource $stream An open writable file stream.
 * @param array $configuration An associative array containing the
 *   configuration of the board. The following keys are required and
 *   supported:
 *   - database-hostname: The host name where the database resides on.
 *   - database-username: The user name to connect to the database.
 *   - database-password: The password to connect to the database.
 *   - database-name: The name of the database/schema where the tables
 *     reside in.
 *   - table-prefix: An prefix, that is added to every table of this
 *     board installation.
 */
function p_configuration($stream, $configuration)
{
    fwrite($stream, <<<CONFIG
<?php
/* This file has been generated by the ThWboard installer.
 *
 * If you have downloaded it from the ThWboard installer, you need to
 * place it inside the 'inc/' directory of the ThWboard installation.
 * If you want to reinstall the ThWboard, you need to delete this file.
 */
\$mysql_h    = "{$configuration['database-hostname']}";
\$mysql_u    = "{$configuration['database-username']}";
\$mysql_p    = "{$configuration['database-password']}";
\$mysql_db   = "{$configuration['database-name']}";
\$pref       = "{$configuration['table-prefix']}";
\$inst_lock  = true;

CONFIG
    );
}

function p_errormsg($title, $message, $back_url = null)
{
    global $template;

    echo $template->render('install-error', [
        'about_handler' => 'install.php?step=about',
        'back_url' => $back_url,
        'language' => $_SESSION['lang'],
        'message' => $message,
        'title' => $title
    ]);
}
