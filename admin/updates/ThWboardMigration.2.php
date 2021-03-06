<?php

require_once __DIR__.'/../Migration.class.php';

class ThWboardMigration2 extends Migration
{
    public function __construct()
    {
        parent::__construct(
            '2.81', '2.82',
            "ThWboard Development Team",
            "This migration requires an unmodified database schema based on version 2.81"
        );
    }

    public function upgrade(PDO $pdo)
    {
        if ($this->fromVersion != schema_version($pdo)) {
            throw new RuntimeException(lng('cantexec'));
        }

        $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}user
MODIFY COLUMN
    userinterests TINYTEXT NOT NULL,
ADD COLUMN
    useractivate TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER usernodelete
SQL
        );

        $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}online
ADD COLUMN
    sessionid VARCHAR(32) DEFAULT '' NOT NULL FIRST,
ADD INDEX
    sessionid (sessionid),
DROP COLUMN
    onlineid,
ENGINE = Heap
SQL
        );

        $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}group
CHANGE COLUMN
    accessmask accessmask_old INT(10) UNSIGNED NOT NULL DEFAULT 0,
ADD COLUMN
    accessmask VARCHAR(50) DEFAULT '' NOT NULL
SQL
        );

        $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}groupboard
CHANGE COLUMN
    accessmask accessmask_old INT(10) UNSIGNED NOT NULL DEFAULT 0,
ADD COLUMN
    accessmask VARCHAR(50) DEFAULT '' NOT NULL
SQL
        );

        $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}group
SET
    accessmask = RPAD(REVERSE(BIN(accessmask_old)), 22, '0')
SQL
        );

        $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}groupboard
SET
    accessmask = RPAD(REVERSE(BIN(accessmask_old)), 22, '0')
SQL
        );

        $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}group
DROP COLUMN
    accessmask_old
SQL
        );

        $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}groupboard
DROP COLUMN
    accessmask_old
SQL
        );


        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}statcache
(
    stat_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    stat_stime INT(11) UNSIGNED NOT NULL DEFAULT 0,
    stat_etime INT(11) UNSIGNED NOT NULL DEFAULT 0,
    stat_uid INT(11) UNSIGNED NOT NULL DEFAULT 0,
    stat_month VARCHAR(64) NOT NULL DEFAULT '',
    stat_auser INT(11) UNSIGNED NOT NULL DEFAULT 0,
    stat_nuser INT(11) UNSIGNED NOT NULL DEFAULT 0,
    stat_nthread INT(11) UNSIGNED NOT NULL DEFAULT 0,
    stat_npost INT(11) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (stat_id),
    KEY stat_stime (stat_stime, stat_etime),
    KEY stat_month (stat_month),
    KEY stat_uid (stat_uid)
) ENGINE = MyISAM
SQL
        );

        $pdo->exec(
<<<SQL
INSERT INTO
    {$pdo->prefix}registry
VALUES
    ('session_timeout', '1800', 'integer', 'Session timeout', 'Time for sessions to expire.', 3, 13)
SQL
        );

        $posts = $pdo->query(
<<<SQL
SELECT
    postid,
    posttext
FROM
    {$pdo->prefix}post
WHERE
    posttext LIKE '%[QUOTE]%' OR
    posttext LIKE '%[/QUOTE]%'
SQL
        )->fetchAll();

        foreach ($posts as $post) {
            $post['posttext'] = preg_replace("/\[(\/|)QUOTE\]/", "[$1quote]", $post['posttext']);

            $stmt = $pdo->prepare(
<<<SQL
UPDATE
    {$pdo->prefix}post
SET
    posttext = :posttext
WHERE
    postid = :postid
SQL
            );

            $stmt->bindValue(':posttext', $post['posttext'], PDO::PARAM_STR);
            $stmt->bindValue(':postid', $post['postid'], PDO::PARAM_INT);
            $stmt->execute();
        }

        $stmt = $pdo->prepare(
<<<SQL
UPDATE
    {$pdo->prefix}registry
SET
    keyvalue = :version
WHERE
    keyname = 'version'
SQL
        );

        $stmt->bindValue(':version', $this->toVersion, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function downgrade(PDO $pdo)
    {
        if (table_exists($pdo, $pdo->prefix.'user')) {
            $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}user
MODIFY COLUMN
    userinterests MEDIUMTEXT NOT NULL,
DROP COLUMN
    useractivate
SQL
            );
        }

        if (table_exists($pdo, $pdo->prefix.'online')) {
            $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}online
DROP INDEX
    sessionid,
DROP COLUMN
    sessionid,
ADD COLUMN
    onlineid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
ADD PRIMARY KEY
    (onlineid),
ENGINE = Heap
SQL
            );
        }

        if (table_exists($pdo, $pdo->prefix.'group')) {
            $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}group
CHANGE COLUMN
    accessmask accessmask_new VARCHAR(50) DEFAULT '' NOT NULL,
ADD COLUMN
    accessmask INT(10) UNSIGNED NOT NULL DEFAULT 0
SQL
            );

            $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}group
SET
    accessmask = CONVERT(REVERSE(accessmask_new), UNSIGNED INT)
SQL
            );

            $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}group
DROP COLUMN
    accessmask_new
SQL
            );
        }

        if (table_exists($pdo, $pdo->prefix.'groupboard')) {
            $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}groupboard
CHANGE COLUMN
    accessmask accessmask_new VARCHAR(50) DEFAULT '' NOT NULL,
ADD COLUMN
    accessmask INT(10) UNSIGNED NOT NULL DEFAULT 0
SQL
            );

            $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}groupboard
SET
    accessmask = CONVERT(REVERSE(accessmask_new), UNSIGNED INT)
SQL
            );

            $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}groupboard
DROP COLUMN
    accessmask_new
SQL
            );
        }

        $pdo->exec(
<<<SQL
DROP TABLE IF EXISTS
    {$pdo->prefix}statcache
SQL
        );

        if (table_exists($pdo, $pdo->prefix.'registry')) {
            $pdo->exec(
<<<SQL
DELETE FROM
    {$pdo->prefix}registry
WHERE
    keyname = 'session_timeout'
SQL
            );

            $stmt = $pdo->prepare(
<<<SQL
UPDATE
    {$pdo->prefix}registry
SET
    keyvalue = :version
WHERE
    keyname = 'version'
SQL
            );

            $stmt->bindValue(':version', $this->fromVersion, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}

return new ThWboardMigration2();
