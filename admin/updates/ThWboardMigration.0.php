<?php

require_once __DIR__.'/../Migration.class.php';

class ThWboardMigration0 extends Migration
{
    public function __construct()
    {
        parent::__construct(
            '', '2.8',
            "ThWboard Development Team",
            "This migration installs the database schema for version 2.8"
        );
    }

    public function upgrade(PDO $pdo)
    {
        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}adminlog
(
    logid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    logtype VARCHAR(12) NOT NULL DEFAULT '',
    logtime INT(10) UNSIGNED NOT NULL DEFAULT 0,
    loguser VARCHAR(64) NOT NULL DEFAULT '0',
    logip VARCHAR(16) NOT NULL DEFAULT '',
    logscript VARCHAR(32) NOT NULL DEFAULT '',
    logaction VARCHAR(32) NOT NULL DEFAULT '',
    lognotes VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (logid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}avatar
(
    avatarid INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    avatarname VARCHAR(128) NOT NULL DEFAULT '',
    avatarurl VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (avatarid),
    UNIQUE KEY avatarname (avatarname)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}ban
(
    banid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    userid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    banpubreason MEDIUMTEXT NOT NULL,
    banreason MEDIUMTEXT NOT NULL,
    bansetbyid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    banexpire INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (banid),
    KEY userid (userid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}bannedwords
(
    wordid INT(11) NOT NULL AUTO_INCREMENT,
    banword VARCHAR(128) NOT NULL DEFAULT '',
    modword VARCHAR(128) NOT NULL DEFAULT '',
    ispartofword TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (wordid),
    UNIQUE KEY banword (banword)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}board
(
    boardid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    boardname VARCHAR(255) NOT NULL DEFAULT '',
    boardlastpost INT(11) UNSIGNED NOT NULL DEFAULT 0,
    boardthreads INT(10) UNSIGNED NOT NULL DEFAULT 0,
    boardposts INT(10) UNSIGNED NOT NULL DEFAULT 0,
    boarddescription TEXT NOT NULL,
    categoryid INT(11) UNSIGNED NOT NULL DEFAULT 0,
    boardorder INT(11) UNSIGNED NOT NULL DEFAULT 0,
    styleid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    boardlastpostby VARCHAR(64) NOT NULL DEFAULT '',
    boardthreadtopic VARCHAR(255) NOT NULL DEFAULT '',
    boardthreadid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    boarddisabled TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (boardid),
    KEY boardid (boardid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}calendar
(
    eventid INT(11) NOT NULL AUTO_INCREMENT,
    eventtime DATE DEFAULT NULL,
    eventsubject VARCHAR(255) DEFAULT NULL,
    eventtext TEXT,
    eventactive SMALLINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (eventid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}category
(
    categoryid INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    categoryname VARCHAR(250) NOT NULL DEFAULT '',
    categoryorder INT(11) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (categoryid),
    KEY categoryid (categoryid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}group
(
    groupid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL DEFAULT '',
    accessmask INT(10) UNSIGNED NOT NULL DEFAULT 0,
    nodelete TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    title VARCHAR(255) NOT NULL DEFAULT '',
    titlepriority INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (groupid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}groupboard
(
    boardid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    groupid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    accessmask INT(10) UNSIGNED NOT NULL DEFAULT 0,
    KEY boardid (boardid, groupid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}lastvisited
(
    boardid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    userid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    lastvisitedtime INT(10) UNSIGNED NOT NULL DEFAULT 0,
    KEY useridboardid (userid, boardid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}news
(
    newsid INT(11) NOT NULL AUTO_INCREMENT,
    newstopic VARCHAR(250) NOT NULL DEFAULT '',
    newstext MEDIUMTEXT NOT NULL,
    newstime INT(11) NOT NULL DEFAULT 0,
    boardid VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (newsid),
    KEY newsid (newsid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}online
(
    onlineid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    onlineip VARCHAR(16) NOT NULL DEFAULT '',
    onlinetime INT(10) UNSIGNED NOT NULL DEFAULT 0,
    userid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (onlineid),
    KEY onlinetime (onlinetime)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}pm
(
    pmid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    pmfromid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    pmtoid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    pmtopic VARCHAR(128) NOT NULL DEFAULT '',
    pmtext MEDIUMTEXT NOT NULL,
    pmtime INT(10) UNSIGNED NOT NULL DEFAULT 0,
    pmflags TINYINT(4) NOT NULL DEFAULT 0,
    pmfolder TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (pmid),
    KEY pmid (pmid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}post
(
    postid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    posttime INT(11) UNSIGNED DEFAULT NULL,
    posttext TEXT NOT NULL,
    userid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    postguestname VARCHAR(64) NOT NULL DEFAULT '',
    threadid INT(11) UNSIGNED NOT NULL DEFAULT 0,
    postemailnotify TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    postip VARCHAR(16) NOT NULL DEFAULT '',
    postsmilies TINYINT(1) NOT NULL DEFAULT 0,
    postcode TINYINT(1) NOT NULL DEFAULT 0,
    postsignature TINYINT(1) NOT NULL DEFAULT 0,
    postlasteditby VARCHAR(64) NOT NULL DEFAULT '',
    postlastedittime INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (postid),
    KEY postid (postid),
    KEY userid (userid),
    KEY threadid (threadid, userid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}qlink
(
    linkid INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    linkcaption VARCHAR(50) NOT NULL DEFAULT '',
    linkalt VARCHAR(255) NOT NULL DEFAULT '',
    linkhttp TEXT NOT NULL,
    linkcounter INT(11) UNSIGNED NOT NULL DEFAULT 0,
    linkactive INT(1) UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (linkid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}rank
(
    rankid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    ranktitle VARCHAR(255) NOT NULL DEFAULT '',
    rankimage VARCHAR(255) NOT NULL DEFAULT '',
    rankposts INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (rankid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}registry
(
    keyname VARCHAR(255) NOT NULL DEFAULT '',
    keyvalue MEDIUMTEXT NOT NULL,
    keytype ENUM('string', 'integer', 'boolean', 'array') NOT NULL DEFAULT 'string',
    keydescription VARCHAR(32) NOT NULL DEFAULT '',
    keydetails VARCHAR(255) NOT NULL DEFAULT '',
    keygroupid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    keydisplayorder INT(11) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (keyname)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}registrygroup
(
    keygroupid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    keygroupname VARCHAR(64) NOT NULL DEFAULT '',
    keygroupdisplayorder INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (keygroupid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}session
(
    sessionid VARCHAR(32) NOT NULL DEFAULT '',
    lastaction INT(10) UNSIGNED NOT NULL DEFAULT 0,
    userid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    username VARCHAR(64) NOT NULL DEFAULT '',
    ip VARCHAR(16) NOT NULL DEFAULT '0',
    PRIMARY KEY (sessionid),
    KEY sessionid (sessionid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}style
(
    styleid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    stylename VARCHAR(32) NOT NULL DEFAULT '',
    colorbg VARCHAR(8) NOT NULL DEFAULT '',
    colorbgfont VARCHAR(8) NOT NULL DEFAULT '',
    color4 VARCHAR(8) NOT NULL DEFAULT '',
    col_he_fo_font VARCHAR(8) NOT NULL DEFAULT '',
    color1 VARCHAR(8) NOT NULL DEFAULT '',
    CellA VARCHAR(8) NOT NULL DEFAULT '',
    CellB VARCHAR(8) NOT NULL DEFAULT '',
    border_col VARCHAR(8) NOT NULL DEFAULT '',
    color_err VARCHAR(8) NOT NULL DEFAULT '',
    col_link VARCHAR(8) NOT NULL DEFAULT '',
    col_link_v VARCHAR(8) NOT NULL DEFAULT '',
    col_link_hover VARCHAR(8) NOT NULL DEFAULT '',
    stdfont VARCHAR(128) NOT NULL DEFAULT '',
    boardimage VARCHAR(128) NOT NULL DEFAULT '',
    newtopicimage VARCHAR(128) NOT NULL DEFAULT '',
    styleispublic TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    styleisdefault TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    styletemplate VARCHAR(32) NOT NULL DEFAULT 'default',
    PRIMARY KEY (styleid),
    KEY styleid (styleid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}thread
(
    threadid INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    threadtopic VARCHAR(255) NOT NULL DEFAULT '',
    threadtime INT(11) UNSIGNED NOT NULL DEFAULT 0,
    threadauthor VARCHAR(64) NOT NULL DEFAULT '',
    threadreplies INT(11) UNSIGNED NOT NULL DEFAULT 0,
    boardid INT(11) UNSIGNED NOT NULL DEFAULT 0,
    threadlastreplyby VARCHAR(64) NOT NULL DEFAULT '',
    threadiconid SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0,
    threadviews INT(10) UNSIGNED NOT NULL DEFAULT 0,
    threadlink INT(10) UNSIGNED NOT NULL DEFAULT 0,
    threadclosed TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    threadtop TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    threadcreationtime INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (threadid),
    KEY threadid (threadid),
    KEY boardid (boardid, threadtop, threadtime)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}user
(
    userid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(64) NOT NULL DEFAULT '',
    userjoin INT(11) UNSIGNED NOT NULL DEFAULT 0,
    userlastpost INT(11) UNSIGNED NOT NULL DEFAULT 0,
    userposts INT(11) UNSIGNED NOT NULL DEFAULT 0,
    useremail VARCHAR(128) NOT NULL DEFAULT '',
    userpassword VARCHAR(64) BINARY NOT NULL DEFAULT '',
    userhomepage VARCHAR(128) NOT NULL DEFAULT '',
    userlocation VARCHAR(128) NOT NULL DEFAULT '',
    usericq INT(11) UNSIGNED NOT NULL DEFAULT 0,
    useraim VARCHAR(50) NOT NULL DEFAULT '',
    usermsn VARCHAR(50) NOT NULL DEFAULT '',
    userbday DATE NOT NULL DEFAULT '0000-00-00',
    styleid INT(10) UNSIGNED NOT NULL DEFAULT 0,
    useroccupation VARCHAR(128) NOT NULL DEFAULT '',
    userinterests MEDIUMTEXT NOT NULL,
    usersignature MEDIUMTEXT NOT NULL,
    userbanned TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    usertitle VARCHAR(64) NOT NULL DEFAULT '',
    userhidesig TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    userrating TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
    uservotes SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
    userhideemail TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    userisadmin TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    userinvisible TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    usernoding TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    useravatar VARCHAR(255) NOT NULL DEFAULT '',
    groupids VARCHAR(128) NOT NULL DEFAULT '',
    usernodelete TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (userid),
    UNIQUE KEY username (username),
    KEY userid (userid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
INSERT INTO
    {$pdo->prefix}board
VALUES
    (DEFAULT, 'Board #1', 0, 0, 0, 'Beschreibung von Board #1', 1, 1, 0, '', '', 0, 0)
SQL
        );

        $pdo->exec(
<<<SQL
INSERT INTO
    {$pdo->prefix}category
VALUES
    (DEFAULT, 'Kategorie 1', 1)
SQL
        );

        $pdo->exec(
<<<SQL
INSERT INTO
    {$pdo->prefix}group
VALUES
    (DEFAULT, 'Default Group',   0b00000000000010000111, 1, '', 0),
    (DEFAULT, 'Guest Group',     0b00000000000000000001, 1, '', 0),
    (DEFAULT, 'Admin Group',     0b11111111111111111111, 1, 'Administrator', 0),
    (DEFAULT, 'Moderator Group', 0b10111001000110101111, 1, 'Moderator', 0)
SQL
        );

        $pdo->exec(
<<<SQL
INSERT INTO
    {$pdo->prefix}registry
VALUES
    ('version', '2.8', 'string', 'Version', 'The internal version, please do not change.', 0, 0),
    ('max_useronline', '0|0', 'string', 'Do not modify or delete', 'Do not modify or delete', 0, 0),
    ('default_groupid', '1', 'integer', 'Dont modify!', 'Dont modify!', 0, 0),
    ('guest_groupid', '2', 'integer', 'Dont modify!', 'Dont modify!', 0, 0),
    ('board_name', 'Forum-name', 'string', 'Board name', '', 1, 0),
    ('board_baseurl', 'http://localhost/', 'string', 'Board URL', 'Used in eMails', 1, 1),
    ('board_admin', 'root@localhost', 'string', 'Board Admin/Tech eMail', '', 1, 2),
    ('site_name', 'Your site\'s name', 'string', 'Board site name', '', 1, 3),
    ('site_url', 'Your site\'s URL', 'string', 'Board site URL', '',1, 4),
    ('userrating', '', 'boolean', 'Enable userrating', '(currently unavailable)', 2, 0),
    ('privatemessages', '1', 'boolean', 'Allow private messages?', '', 2, 1),
    ('smilies', '', 'boolean', 'Allow smilies?', '', 2, 2),
    ('enable_quicklinks', '', 'boolean', 'Enable quicklinks?', '', 2, 3),
    ('enable_ranks', '', 'boolean', 'Enable userranks?', '', 2, 4),
    ('debugmode', '', 'boolean', 'Enable debug-mode?', 'Prints debug/development messages for admins', 2, 5),
    ('forumclosed', '', 'boolean', 'Forum closed?', 'Allows you to close your forums, e.g. for maintenance. Note: Administrators can still view the forums.', 5, 1),
    ('use_email', '', 'boolean', 'Use eMail functions?', 'If your server does not support sending eMails via php\'s mail() function, disable this.', 2, 7),
    ('guestprefix', '~', 'string', 'Guest name prefix', 'The char put in front of a guest name.', 2, 9),
    ('showprivateboards', '1', 'boolean', 'Show private boards?', 'View boards that are inaccessisble for a user? (The user is still not able to view it)', 2, 10),
    ('uppercase_prot', '1', 'boolean', 'Use uppercase topic protection?', 'Prevents users from using only caps in topics. CAN BE VERY USEFUL', 2, 11),
    ('allowregister', '1', 'boolean', 'Allow users to register?', 'You can disable the register option for users here.', 2, 12),
    ('vars_t_amount', '30', 'integer', 'Topics per page', 'The number of topic rows per board-page.', 3, 0),
    ('vars_m_amount', '25', 'integer', 'Message amount', 'The number of posts per page.', 3, 1),
    ('subject_maxlength', '64', 'integer', 'Topic maxlength', 'The maximum length (in chars) allowed in a thread\'s topic.', 3, 2),
    ('subject_minlength', '2', 'integer', 'Topic minlength', 'The minimum length.', 3, 3),
    ('message_maxlength', '10000', 'integer', 'Message maxlength', 'The maximum length in a post\'s text (in chars).', 3, 4),
    ('message_minlength', '2', 'integer', 'Message minlength', 'Minimum post length.', 3, 5),
    ('showpostslevel', '2', 'integer', 'Show posts level', 'Show posts:<br>0 - Never, don\'t show any post counts.<br>1 - Show own postcount only.<br>2 - No restriction', 3, 6),
    ('imageslevel', '1', 'integer', 'Image usage level', 'Allow images:<br>0 - No restriction<br>1 - Allow images in posts only<br>2 - Do not allow any images', 3, 7),
    ('max_usernamelength', '24',' integer', 'Maximum user name length', 'The maximum length of a username, in chars.', 3, 8),
    ('min_usernamelength', '2', 'integer', 'Minimum user name length', 'The minimum length of a username, in chars.', 3, 9),
    ('pm_maxlength', '8000', 'integer', 'Private message maximum length', 'The maximum length of a private message, in chars.', 3, 10),
    ('postdelay', '30', 'integer', 'Post flood protection', 'Minimum delay between posts, in seconds.', 4, 0),
    ('editlimit', '1800', 'integer', 'Edit time limit', 'Users will be unable to edit posts after posttime + limit. Set to 0 to disable.', 4, 1),
    ('timeoffset', '', 'integer', 'Time offset', 'Time offset, in hours (can be negative)', 5, ''),
    ('closedmsg', 'Sorry\r\n\r\nDas Forum ist momentan leider nicht verf&uuml;gbar.', 'array', 'Forum closed message', 'This message appears if users try to access the closed forum.', 5, 2),
    ('bannednames', '', 'array', 'Nick register protection', 'Allows you to specify invalid nicks. Users will be unable to register these nicks, one name per line. Example: \'adm\' disallows Admin as well as fooadmbar...', 5, 4),
    ('bannedemails', '', 'array', 'Email register protection', 'Allows you to specify invalid email addresses. Use one eMail per line, eg @aol bans dude@aol.com as well as notme@aol.net', 5, 3),
    ('sig_maxlen', '600', 'integer', 'Max. signature length', 'The maximum length of a signature, in bytes (chars).', 6, 1),
    ('max_privmsg', '30', 'integer', 'Max PM count', 'The maximum amount of private messages per user. Users will be unable to recieve PMs if their box is full.', 3, 11),
    ('pmalive', '', 'integer', 'PM max life', 'Set the maximum age of private messages, in days. Set to 0 to disable life check.', 3, 12),
    ('sig_maxlines', '5', 'integer', 'Max. signature lines', 'The maximum number of lines allowed in a signature. Note that a line can be wrapped depending on the users browser size. Thus you should set an appropriate max length in chars as well. One average line has about 120 chars at a resolution of 1024*768.', 6, 2),
    ('sig_restrict', '1', 'boolean', 'Enable signature restrictions?', 'Enable this to restrict the signature length using the options below.', 6, 0),
    ('uselastthreads', '0', 'boolean', 'Enable last-threads-form?', 'Enables a select form on the index page to display threads within the last 3/5/7.. days.', 2, 13),
    ('avatarheight', '64', 'integer', 'Avatar maximum height', 'The maximum height of the user-defined Avatars.', 7, 2),
    ('avatarwidth', '64', 'integer', 'Avatar maximum width', 'The maximum width of the user-defined Avatars.', 7, 1),
    ('useravatar', '0', 'integer', '<a name=\'avatar\'>Avatar-Settings', 'Enable avatars:<br>0 - Avatars are disabled<br>1 - Only pre-installed avatars<br>2 - Only user-defined avatars<br>3 - Both', 7, 0),
    ('usebwordprot', '3', 'integer', 'Badwords protection', 'Do not modify', 0, 7),
    ('compression', '0', 'boolean', 'Enable compression?', 'Gzip compression can save a lot of traffic, but requires a bit more cpu time', 2, 14),
    ('slow_search', '0', 'boolean', 'Case-insensitive (slow) search?', 'Slower, but more precisely.', 2, 15)
SQL
        );

        $pdo->exec(
<<<SQL
INSERT INTO
    {$pdo->prefix}registrygroup
VALUES
    (1, 'General', 0),
    (2, 'Options', 1),
    (3, 'General vars', 2),
    (4, 'Post options', 3),
    (5, 'Misc', 6),
    (6, 'Signature control', 4),
    (7, 'Avatar settings', 5)
SQL
        );

        $pdo->exec(
<<<SQL
INSERT INTO
    {$pdo->prefix}style
VALUES
    (1, 'Default', '#FAFAFA', '#454545', '#1B6ECC', '#ECECEC', '#323232', '#FAFAFA', '#F5F5F5', '#DEDEDE', '#990000', '#1B6ECC', '#1B6ECC', '#69B271', 'Verdana', './templates/default/images/space.gif', './templates/default/images/newtopic.gif', '', 1, 'default')
SQL
        );
    }

    public function downgrade(PDO $pdo)
    {
        $tables = [
            'adminlog',
            'avatar',
            'ban',
            'bannedwords',
            'board',
            'calendar',
            'category',
            'group',
            'groupboard',
            'lastvisited',
            'news',
            'online',
            'pm',
            'post',
            'qlink',
            'rank',
            'registry',
            'registrygroup',
            'session',
            'style',
            'thread',
            'user'
        ];

        foreach ($tables as $table) {
            $pdo->exec(
<<<SQL
DROP TABLE IF EXISTS
    {$pdo->prefix}{$table}
SQL
            );
        }
    }
}

return new ThWboardMigration0();
