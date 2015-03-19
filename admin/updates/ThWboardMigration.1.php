<?php

require_once __DIR__.'/../Migration.class.php';

class ThWboardMigration1 extends Migration
{
    public function __construct()
    {
        parent::__construct(
            '2.8', '2.81',
            "ThWboard Development Team",
            "This migration requires an unmodified database schema based on version 2.8"
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
    {$pdo->prefix}calendar
ADD COLUMN
    userid INT(10) UNSIGNED NOT NULL
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
        if (table_exists($pdo, $pdo->prefix.'calendar')) {
            $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}calendar
DROP COLUMN
    userid
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

return new ThWboardMigration1();
