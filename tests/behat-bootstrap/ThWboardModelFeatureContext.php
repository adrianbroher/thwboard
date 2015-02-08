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
}
