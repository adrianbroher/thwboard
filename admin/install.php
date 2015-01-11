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

if (!isset($_GET['step'])) {
    $_GET['step'] = '';
}

if (!install_allowed() && $_GET['step'] != 'about' && $_GET['step'] != '') {
    $_GET['step'] = 'deny';
}

switch ($_GET['step']) {
    case 'configuration-download':
        header('Content-Type: application/octetstream');
        header('Content-Disposition: filename="config.inc.php"');
        header('Pragma: no-cache');
        header('Expires: 0');

        p_configuration(STDOUT, $_POST);
        break;

    case 'administrator-create':
        if (strlen($_POST['administrator-password']) < 5) {
            p_errormsg(lng('error'), lng('adminpwtooshort'));
        }

        mysql_connect($_POST['database-hostname'], $_POST['database-username'], $_POST['database-password']);
        mysql_select_db($_POST['database-name']);

        thwb_query(
<<<SQL
INSERT INTO
    {$_POST['table-prefix']}user
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
    '{$_POST['administrator-username']}',
    '{$_POST['administrator-email']}',
    MD5('{$_POST['administrator-password']}'),
    '1',
    UNIX_TIMESTAMP(),
    ',3,',
    '1'
)
SQL
        );

        p_header('configuration-write');
        p_prewrite('install.php?step=configuration-download&database-hostname='.$_POST['database-hostname'].'&database-username='.$_POST['database-username'].'&database-password='.$_POST['database-password'].'&database-name='.$_POST['database-name'].'&table-prefix='.$_POST['table-prefix']);
        p_footer('configuration-write', [
            'database-hostname' => $_POST['database-hostname'],
            'database-username' => $_POST['database-username'],
            'database-password' => $_POST['database-password'],
            'database-name' => $_POST['database-name'],
            'table-prefix' => $_POST['table-prefix']
        ]);
        break;

    case 'table-create':
        mysql_connect($_POST['database-hostname'], $_POST['database-username'], $_POST['database-password']);
        mysql_select_db($_POST['database-name']);

        create_tables($_POST['table-prefix'], $_POST['database-clear'] == 'true');

        p_header('administrator-create');
        p_adminprofile();
        p_footer('administrator-create', [
            'database-hostname' => $_POST['database-hostname'],
            'database-username' => $_POST['database-username'],
            'database-password' => $_POST['database-password'],
            'database-name' => $_POST['database-name'],
            'table-prefix' => $_POST['table-prefix']
        ]);
        break;

    case 'configuration-write':
        if (!WriteAccess('../inc/config.inc.php')) {
            p_errormsg(lng('error'), lng('chmoderror'));
        } else {
            $fp = @fopen('../inc/config.inc.php', 'w');
            p_configuration($fp, $_POST);
            fclose($fp);

            p_header();
            p_done();
            p_footer();
        }
        break;

    case 'table-prefix':
        mysql_connect($_POST['database-hostname'], $_POST['database-username'], $_POST['database-password']);

        $db = '';

        if ($_POST['database-name-new'] && $_POST['database-name-use'] == '_usefield') {
            $db = $_POST['database-name-new'];
        } else {
            $db = $_POST['database-name-use'];
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

        p_header('table-create');
        p_chooseprefix($db, $tables);
        p_footer('table-create', [
            'database-hostname' => $_POST['database-hostname'],
            'database-username' => $_POST['database-username'],
            'database-password' => $_POST['database-password'],
            'database-name' => $db
        ]);
        break;

    case 'database-select':
        $dbhandle = @mysql_connect($_POST['database-hostname'], $_POST['database-username'], $_POST['database-password']);

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

        p_header('table-prefix');
        p_selectdb($databases);
        p_footer('table-prefix', [
            'database-hostname' => $_POST['database-hostname'],
            'database-username' => $_POST['database-username'],
            'database-password' => $_POST['database-password']
        ]);
        break;

    case 'database-credentials':
        if ($_POST['license-accept'] != 'true') {
            p_errormsg(lng('error'), lng('licaccept'));
        } else {
            p_header('database-select');
            p_mysqldata();
            p_footer('database-select');
        }
        break;

    case 'license':
        p_header('database-credentials');
        p_license();
        p_footer('database-credentials');
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
        p_header('license');
        p_welcome();
        p_footer('license');
        break;

    default:
        p_header('welcome');
        p_selectlang();
        p_footer('welcome');
}
