<?php

require_once __DIR__.'/../Migration.class.php';

class ThWboardMigration5 extends Migration
{
    public function __construct()
    {
        parent::__construct(
            '2.84', '2.85',
            "ThWboard Development Team",
            "This migration requires an unmodified database schema based on version 2.84"
        );
    }

    public function upgrade(PDO $pdo)
    {
        if ($this->fromVersion != schema_version($pdo)) {
            throw new RuntimeException(lng('cantexec'));
        }

        $pdo->exec(
<<<SQL
CREATE TABLE
    {$pdo->prefix}flood
(
    userid INT(10) NOT NULL DEFAULT 0,
    type TINYINT(1) DEFAULT NULL,
    time TIMESTAMP(6) NOT NULL,
    ip VARCHAR(16) NOT NULL DEFAULT ''
) ENGINE = HEAP
SQL
        );

        $pdo->exec(
<<<SQL
INSERT INTO
    {$pdo->prefix}registrygroup
VALUES
    (8, 'Flood Protection', 7),
    (9, 'Error Reporting', 8)
SQL
        );

        $pdo->exec(
<<<SQL
INSERT INTO
    {$pdo->prefix}registry
VALUES
    ('flood_login_count', '3', 'integer', 'Login failure count', 'Number of failed logins before the account gets locked for specific IP.<br>(0 to disable)', 8, 1),
    ('flood_login_timeout', '15', 'integer', 'Login failure timeout', 'Time (in minutes) for which an account gets locked after several failed logins.', 8, 2),
    ('flood_register_count', '5', 'integer', 'Registration count', 'Number of registrations before register gets locked for specific IP.<br>(0 to disable)', 8, 3),
    ('flood_register_timeout', '15', 'integer', 'Registration timeout', 'Time (in minutes) for which an IP gets locked after several registrations.', 8, 4),
    ('flood_mail_count', '3', 'integer', 'Mail count', 'Number of sent mails before mail function gets locked for specific user.<br>(0 to disable)', 8, 5),
    ('flood_mail_timeout', '15', 'integer', 'Mail timeout', 'Time (in minutes) for which mail function gets locked after several sent mails.', 8, 6),
    ('debug_what', '0', 'integer', 'Error Reporting', '0 - Disabled<br>1 - PHP only<br>2 - SQL only<br>3 - both', 9, 0),
    ('debug_mail', '0', 'boolean', 'Error Mails', 'Send error reports as email? This requires &quot;Use eMail Functions&quot; and &quot;Board Admin/Tech eMail&quot;.', 9, 1),
    ('debug_do_log', '0', 'boolean', 'Log errors?', 'Log errors to file?<br>In order to use this feature, you must create a directory with the correct permissions and set the path below.<br><strong>It is highly recommended that this directory be secured by a .htaccess</strong>.', 9, 2),
    ('debug_log_path', 'logs/thwb_err', 'string', 'Log file path', 'path to the log file and log file prefix.<br><strong>See above</strong>.', 9, 3)
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
        $pdo->exec(
<<<SQL
DROP TABLE IF EXISTS
    {$pdo->prefix}flood
SQL
        );

        if (table_exists($pdo, $pdo->prefix.'registrygroup')) {
            $pdo->exec(
<<<SQL
DELETE FROM
    {$pdo->prefix}registrygroup
WHERE
    keygroupname IN (
        'Flood Protection',
        'Error Reporting'
    )
SQL
            );
        }

        if (table_exists($pdo, $pdo->prefix.'registry')) {
            $pdo->exec(
<<<SQL
DELETE FROM
    {$pdo->prefix}registry
WHERE
    keyname IN (
        'flood_login_count',
        'flood_login_timeout',
        'flood_register_count',
        'flood_register_timeout',
        'flood_mail_count',
        'flood_mail_timeout',
        'debug_what',
        'debug_mail',
        'debug_do_log',
        'debug_log_path'
    )
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

return new ThWboardMigration5();
