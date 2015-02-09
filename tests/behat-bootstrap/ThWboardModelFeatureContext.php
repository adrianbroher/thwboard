<?php

/**
 * ThWboard - PHP/MySQL Bulletin Board System
 * ==========================================
 *
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

use Behat\Gherkin\Node\TableNode;

use Behat\Behat\Context\Context;

/**
 * The behat features context to prepare and manipulate the ThWboard model.
 */
class ThWboardModelFeatureContext implements Context
{
    /** Create a new ThWboard model manipulation context.
     *
     * @param string database_hostname The hostname of the server, where
     *   the database resides.
     * @param string database_username The username to connect to the
     *   database.
     * @param string database_password The password to connect to the
     *   database.
     * @param string database_name The name of the database/schema, that
     *   should be used for testing. <i>The contents of this database
     *   is dropped during the the test run, so don't use a production
     *   level database.</i>
     * @param string table_prefix The prefix, that should be prepended to
     *   the ThWboard table names.
     */
    public function __construct($database_hostname, $database_username, $database_password, $database_name, $table_prefix)
    {
        $this->database_hostname = $database_hostname;
        $this->database_username = $database_username;
        $this->database_password = $database_password;
        $this->database_name = $database_name;
        $this->table_prefix = $table_prefix;

        $this->pdo = new PDO(
            sprintf(
                'mysql:host=%s;dbname=%s',
                $this->database_hostname,
                $this->database_name
            ),
            $this->database_username,
            $this->database_password
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /** Installs the forum.
     *
     * @Given /^the forum is installed$/
     */
    public function installBoard()
    {
        require_once __DIR__.'/../../admin/install_functions.php';

        $dbh = mysql_connect($this->database_hostname, $this->database_username, $this->database_password);
        mysql_select_db($this->database_name, $dbh);

        create_tables($this->table_prefix, true);

        mysql_close($dbh);

        $fp = @fopen(__DIR__.'/../../inc/config.inc.php', 'w');
        p_configuration($fp, [
            'database-hostname' => $this->database_hostname,
            'database-username' => $this->database_username,
            'database-password' => $this->database_password,
            'database-name' => $this->database_name,
            'table-prefix' => $this->table_prefix
        ]);
        fclose($fp);
    }

    /** Uninstalls the forum.
     *
     * Deletes <i>ALL</i> tables in the database.
     *
     * @Given /^the forum is not installed$/
     */
    public function uninstallBoard()
    {
        $r_tables = $this->pdo->query(
<<<SQL
SHOW TABLES
SQL
        );

        foreach ($r_tables as $table) {
            $this->pdo->exec(
<<<SQL
DROP TABLE
    {$table[0]}
SQL
            );
        }

        @unlink(__DIR__.'/../../inc/config.inc.php');
    }

    /** Downgrades the forum schema to the given version.
     *
     * @Given /^the forum is downgraded to (?P<version>[0-9]+\.[0-9]+)$/
     */
    public function downgradeBoard($version)
    {
        if (2.84 != $version) {
            throw new InvalidArgumentException("Can only downgrade to version 2.84, requested {$version}");
        }

        $r_version = $this->pdo->query(
<<<SQL
SELECT
    keyvalue
FROM
    {$this->table_prefix}registry
WHERE
    keyname = 'version'
SQL
        );

        if ($version == $r_version->fetchColumn()) {
            return;
        }

        $this->pdo->exec(
<<<SQL
DROP TABLE IF EXISTS
    {$this->table_prefix}flood
SQL
        );

        $this->pdo->exec(
<<<SQL
DELETE FROM
    {$this->table_prefix}registrygroup
WHERE
    keygroupname IN (
        'Flood Protection',
        'Error Reporting'
    )
SQL
        );

        $this->pdo->exec(
<<<SQL
DELETE FROM
    {$this->table_prefix}registry
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

        $this->pdo->exec(
<<<SQL
UPDATE
    {$this->table_prefix}registry
SET
    keyvalue = '2.84'
WHERE
    keyname = 'version'
SQL
        );

        $this->pdo->exec(
<<<SQL
ALTER TABLE
    {$this->table_prefix}registrygroup
    AUTO_INCREMENT = 8;
SQL
        );
    }

    /** Set up the boards required for a scenario.
     *
     * @Given /^the following boards exist:$/
     */
    public function setUpBoards(TableNode $table)
    {
        $this->pdo->exec(
<<<SQL
DELETE FROM
    {$this->table_prefix}board
SQL
        );
        $this->pdo->exec(
<<<SQL
ALTER TABLE
    {$this->table_prefix}board
    AUTO_INCREMENT = 1
SQL
        );

        if (0 !== count(array_diff(['name', 'description', 'category'], $table->getRow(0)))) {
            throw new InvalidArgumentException("Table missing one of the required columns (name, description, category)");
        }

        $boards = [];

        foreach ($table->getHash() as $board) {
            $r_category = $this->pdo->query(
<<<SQL
SELECT
    categoryid
FROM
    {$this->table_prefix}category
WHERE
    categoryname = '{$board['category']}'
SQL
            );

            if (1 !== $r_category->rowCount()) {
                throw new DomainException("Board '{$board['name']}' is assigned to the non existing category '{$board['category']}'");
            }

            $board['categoryid'] = $r_category->fetchColumn();

            $boards[] = $board;
        }

        $order = 1;

        foreach ($boards as $board) {
            $this->pdo->exec(
<<<SQL
INSERT INTO
    {$this->table_prefix}board
(
    boardname,
    boarddescription,
    categoryid,
    boardorder
) VALUES (
    '{$board['name']}',
    '{$board['description']}',
    {$board['categoryid']},
    {$order}
)
SQL
            );
            $order++;
        }
    }

    /** Set up the categories required for a scenario.
     *
     * @Given /^the following categories exist:$/
     */
    public function setUpCategories(TableNode $table)
    {
        $this->pdo->exec(
<<<SQL
DELETE FROM
    {$this->table_prefix}category
SQL
        );
        $this->pdo->exec(
<<<SQL
ALTER TABLE
    {$this->table_prefix}category
    AUTO_INCREMENT = 1
SQL
        );

        if (0 !== count(array_diff(['name'], $table->getRow(0)))) {
            throw new InvalidArgumentException("Table missing one of the required columns (name)");
        }

        $order = 1;

        foreach ($table->getHash() as $category) {
            $this->pdo->exec(
<<<SQL
INSERT INTO
    {$this->table_prefix}category
(
    categoryname,
    categoryorder
) VALUES (
    '{$category['name']}',
    {$order}
)
SQL
            );
            $order++;
        }
    }

    /** Set up the users required for a scenario.
     *
     * @Given /^the following users exist:$/
     */
    public function setUpUsers(TableNode $table)
    {
        $this->pdo->exec(
<<<SQL
DELETE FROM
    {$this->table_prefix}user
SQL
        );
        $this->pdo->exec(
<<<SQL
ALTER TABLE
    {$this->table_prefix}user
    AUTO_INCREMENT = 1
SQL
        );

        if (0 !== count(array_diff(['name', 'password', 'email', 'member of'], $table->getRow(0)))) {
            throw new InvalidArgumentException("Table missing one of the required columns (name, password, email, member of)");
        }

        $users = [];

        foreach ($table->getHash() as $user) {
            $r_group = $this->pdo->query(
<<<SQL
SELECT
    groupid
FROM
    {$this->table_prefix}group
WHERE
    name = '{$user['member of']}'
SQL
            );

            if (1 !== $r_group->rowCount()) {
                throw new DomainException("User '{$user['name']}' is assigned to the non existing group '{$user['member of']}'");
            }

            $user['groupid'] = $r_group->fetchColumn();

            $user['isadmin'] = (isset($user['flags']) && in_array('isadmin', array_map('trim', explode(',', $user['flags'])))) ? 1 : 0;
            $user['inactive'] = (isset($user['flags']) && in_array('inactive', array_map('trim', explode(',', $user['flags'])))) ? 1 : 0;

            $users[] = $user;
        }

        foreach ($users as $user) {
            $this->pdo->exec(
<<<SQL
INSERT INTO
    {$this->table_prefix}user
(
    username,
    userjoin,
    useremail,
    userpassword,
    groupids,
    usernodelete,
    useractivate,
    userisadmin
) VALUES (
    '{$user['name']}',
    UNIX_TIMESTAMP(),
    '{$user['email']}',
    MD5('{$user['password']}'),
    ';{$user['groupid']};',
    {$user['isadmin']},
    {$user['inactive']},
    {$user['isadmin']}
)
SQL
            );
        }
    }

    /** Insert a flood entry for an attempted login.
     *
     * @Given /^the IP "(?P<ip>[^"]*)" tried to log in (?P<time>[0-9]+) minutes? ago$/
     */
    public function attemptLogin($ip, $time)
    {
        $this->pdo->exec(
<<<SQL
INSERT INTO
    {$this->table_prefix}flood
VALUES (
    0,
    0,
    (NOW() - INTERVAL {$time} MINUTE),
    '{$ip}'
)
SQL
        );
    }
}
