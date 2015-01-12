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

if (version_compare(PHP_VERSION, '4.0.6', '<')) {
    die(
        'ThWboard install can not be performed because the PHP version used is to old ( < 4.0.6 ).<br>'.
        'Please ask your webmaster or server administrator for updating the PHP version.'
    );
}

require('install_functions.php');

if (!isset($_POST['action'])) {
    $_POST['action'] = '';
}

if (!install_allowed() && $_POST['action'] != 'about' && $_POST['action'] != '') {
    $_POST['action'] = 'deny';
}

switch ($_POST['action']) {
    case 'generate_config':
        header('Content-Type: application/octetstream');
        header('Content-Disposition: filename="config.inc.php"');
        header('Pragma: no-cache');
        header('Expires: 0');

        p_configuration(STDOUT, [
            'database-hostname' => $_POST['hostname'],
            'database-username' => $_POST['user'],
            'database-password' => $_POST['pass'],
            'database-name' => $_POST['db'],
            'table-prefix' => $_POST['prefix']
        ]);
        break;

    case 'createadmin':
        if (strlen($_POST['admin_pass']) < 5) {
            p_errormsg(lng('error'), lng('adminpwtooshort'));
        }

        mysql_connect($_POST['hostname'], $_POST['user'], $_POST['pass']);
        mysql_select_db($_POST['db']);

        thwb_query(
<<<SQL
INSERT INTO
    {$_POST['prefix']}user
(
    username,
    useremail,
    userpassword,
    userisadmin,
    userjoin,
    groupids,
    usernodelete
)
VALUES
(
    '{$_POST['admin_user']}',
    '{$_POST['admin_mail']}',
    MD5('{$_POST['admin_pass']}'),
    '1',
    UNIX_TIMESTAMP(),
    ',3,',
    '1'
)
SQL
        );

        p_header();
        p_prewrite(
            $_POST['hostname'],
            $_POST['user'],
            $_POST['pass'],
            $_POST['db'],
            $_POST['prefix']
        );
        p_footer('writeconfig', [
            'hostname' => $_POST['hostname'],
            'user' => $_POST['user'],
            'pass' => $_POST['pass'],
            'db' => $_POST['db'],
            'prefix' => $_POST['prefix']
        ]);
        break;

    case 'createtables':
        mysql_connect($_POST['hostname'], $_POST['user'], $_POST['pass']);
        mysql_select_db($_POST['db']);

        create_tables($_POST['delete_existing']);

        p_header();
        p_adminprofile();
        p_footer('createadmin', [
            'hostname' => $_POST['hostname'],
            'user' => $_POST['user'],
            'pass' => $_POST['pass'],
            'db' => $_POST['db'],
            'prefix' => $_POST['prefix']
        ]);
        break;

    case 'writeconfig':
        if (!WriteAccess('../inc/config.inc.php')) {
            p_errormsg(lng('error'), lng('chmoderror'));
        } else {
            $fp = @fopen('../inc/config.inc.php', 'w');
            p_configuration($fp, [
                'database-hostname' => $_POST['hostname'],
                'database-username' => $_POST['user'],
                'database-password' => $_POST['pass'],
                'database-name' => $_POST['db'],
                'table-prefix' => $_POST['prefix']
            ]);
            fclose($fp);

            p_header();
            p_done();
            p_footer();
        }
        break;

    case 'setprefix':
        mysql_connect($_POST['hostname'], $_POST['user'], $_POST['pass']);

        $db = '';

        if ($_POST['name_db'] && $_POST['selected_db'] == '_usefield') {
            $db = $_POST['name_db'];
        } else {
            $db = $_POST['selected_db'];
        }

        if (!db_exists($db)) {
            thwb_query("CREATE DATABASE ".$db);

            if (!db_exists($db)) {
                p_errormsg(lng('error'), sprintf(lng('mysqlerror'), $db, mysql_error()));
            }
        }

        mysql_select_db($db);

        $r_table = mysql_list_tables($db);
        $a_tables = [];
        $i = 0;

        while ($i < mysql_num_rows($r_table)) {
            $tables[] = mysql_tablename($r_table, $i);
            $i++;
        }

        p_header();
        p_chooseprefix($db, $tables);
        p_footer('createtables', [
            'hostname' => $_POST['hostname'],
            'user' => $_POST['user'],
            'pass' => $_POST['pass'],
            'db' => $db
        ]);
        break;

    case 'selectdb':
        $dbhandle = @mysql_connect($_POST['hostname'], $_POST['user'], $_POST['pass']);

        if (!$dbhandle) {
            p_errormsg(lng('error'), sprintf(lng('connecterror'), mysql_error()));
        }

        $r_database = mysql_listdbs();

        $databases = [];
        $i = 0;

        while ($i < mysql_num_rows($r_database)) {
            $databases[] = mysql_tablename($r_database, $i);
            $i++;
        }

        p_header();
        p_selectdb($databases);
        p_footer('setprefix', [
            'hostname' => $_POST['hostname'],
            'user' => $_POST['user'],
            'pass' => $_POST['pass']
        ]);
        break;

    case 'mysqldata':
        if ($_POST['accept'] != 'yes') {
            p_errormsg(lng('error'), lng('licaccept'));
        } else {
            p_header();
            p_mysqldata();
            p_footer('selectdb');
        }
        break;

    case 'license':
        p_header();
        p_license();
        p_footer('mysqldata');
        break;

    case 'about':
        p_header();
        p_about();
        p_footer();
        break;

    case 'deny':
        p_header();
        p_deny_install();
        p_footer();
        break;

    case 'welcome':
        p_header();
        p_welcome();
        p_footer('license');
        break;

    default:
        p_header();
        p_selectlang();
        p_footer('welcome');
}
