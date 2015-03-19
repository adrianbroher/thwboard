<?php

require_once __DIR__.'/../Migration.class.php';

class ThWboardMigration3 extends Migration
{
    public function __construct()
    {
        parent::__construct(
            '2.82', '2.83',
            "ThWboard Development Team",
            "This migration requires an unmodified database schema based on version 2.82"
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
ADD COLUMN
    usernoipcheck TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
SQL
        );

        $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}style
SET
    boardimage = './templates/default/images/space.png'
WHERE
    boardimage = './templates/default/images/space.gif'
SQL
        );

        $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}style
SET
    boardimage = './templates/default/images/newtopic.png'
WHERE
    boardimage = './templates/default/images/newtopic.gif'
SQL
        );

        $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}style
SET
    stdfont = 'Verdana, Helevetica'
WHERE
    stdfont = 'Verdana'
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
DROP COLUMN
    usernoipcheck
SQL
            );
        }

        if (table_exists($pdo, $pdo->prefix.'style')) {
            $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}style
SET
    boardimage = './templates/default/images/space.gif'
WHERE
    boardimage = './templates/default/images/space.png'
SQL
            );

            $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}style
SET
    boardimage = './templates/default/images/newtopic.gif'
WHERE
    boardimage = './templates/default/images/newtopic.png'
SQL
            );

            $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}style
SET
    stdfont = 'Verdana'
WHERE
    stdfont = 'Verdana, Helevetica'
SQL
            );
        }

        if (table_exists($pdo, $pdo->prefix.'registry')) {
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

return new ThWboardMigration3();
