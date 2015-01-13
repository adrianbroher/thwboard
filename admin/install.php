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
        if (empty($_POST['administrator-username'])) {
            p_errormsg(lng('error'), lng('adminnameempty'), '?step=table-create&database-hostname='.$_REQUEST['database-hostname'].'&database-username='.$_REQUEST['database-username'].'&database-password='.$_REQUEST['database-password'].'&database-name='.$_REQUEST['database-name'].'&table-prefix='.$_REQUEST['table-prefix'].'&database-clear=true&administrator-username='.$_REQUEST['administrator-username'].'&administrator-email='.$_REQUEST['administrator-email'].'&administrator-password='.$_REQUEST['administrator-password']);
        }

        if (strlen($_POST['administrator-password']) < 5) {
            p_errormsg(lng('error'), lng('adminpwtooshort'), '?step=table-create&database-hostname='.$_REQUEST['database-hostname'].'&database-username='.$_REQUEST['database-username'].'&database-password='.$_REQUEST['database-password'].'&database-name='.$_REQUEST['database-name'].'&table-prefix='.$_REQUEST['table-prefix'].'&database-clear=true&administrator-username='.$_REQUEST['administrator-username'].'&administrator-email='.$_REQUEST['administrator-email'].'&administrator-password='.$_REQUEST['administrator-password']);
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
        if (!isset($_REQUEST['administrator-username'])) {
            $_REQUEST['administrator-username'] = 'root';
        }

        if (!isset($_REQUEST['administrator-email'])) {
            $_REQUEST['administrator-email'] = '';
        }

        if (preg_match('/[^a-zA-Z1-9_]/', $_REQUEST['table-prefix'])) {
            p_errormsg(lng('error'), lng('invalidtableprefixerror'), '?step=table-prefix&database-allocation=use&database-hostname='.$_REQUEST['database-hostname'].'&database-username='.$_REQUEST['database-username'].'&database-password='.$_REQUEST['database-password'].'&database-name='.$_REQUEST['database-name'].'&table-prefix='.$_REQUEST['table-prefix'].'&database-clear='.$_REQUEST['database-clear']);
        }

        mysql_connect($_REQUEST['database-hostname'], $_REQUEST['database-username'], $_REQUEST['database-password']);
        mysql_select_db($_REQUEST['database-name']);

        create_tables($_REQUEST['table-prefix'], $_REQUEST['database-clear'] == 'true');

        p_header('administrator-create');
        p_adminprofile($_REQUEST['administrator-username'], $_REQUEST['administrator-email']);
        p_footer('administrator-create', [
            'database-hostname' => $_REQUEST['database-hostname'],
            'database-username' => $_REQUEST['database-username'],
            'database-password' => $_REQUEST['database-password'],
            'database-name' => $_REQUEST['database-name'],
            'table-prefix' => $_REQUEST['table-prefix']
        ]);
        break;

    case 'configuration-write':
        if (!WriteAccess('../inc/config.inc.php')) {
            p_errormsg(lng('error'), lng('chmoderror'), 'JavaScript:history.back(0)');
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
        mysql_connect($_REQUEST['database-hostname'], $_REQUEST['database-username'], $_REQUEST['database-password']);

        if (!isset($_REQUEST['database-name'])) {
            $_REQUEST['database-name'] = ($_REQUEST['database-allocation'] == 'use') ? $_REQUEST['database-name-use'] : $_REQUEST['database-name-new'];
        }

        if (!isset($_REQUEST['table-prefix'])) {
            $_REQUEST['table-prefix'] = 'tb_';
        }

        if (!isset($_REQUEST['database-clear'])) {
            $_REQUEST['database-clear'] = false;
        }

        if (preg_match('/[^a-zA-Z0-9_]/', $_REQUEST['database-name'])) {
            p_errormsg(lng('error'), lng('invaliddatabasenameerror'), '?step=database-select&database-hostname='.$_REQUEST['database-hostname'].'&database-username='.$_REQUEST['database-username'].'&database-password='.$_REQUEST['database-password'].'&database-allocation='.$_REQUEST['database-allocation'].'&database-name='.$_REQUEST['database-name']);
        }

        if ($_REQUEST['database-allocation'] == 'new') {
            $query = "CREATE DATABASE ".$_REQUEST['database-name'];
            mysql_query($query);

            switch (mysql_errno()) {
                case 0:
                    break;
                case 1044:
                    p_errormsg(lng('error'), lng('cantcreatedatabaseerror'), '?step=database-select&database-hostname='.$_REQUEST['database-hostname'].'&database-username='.$_REQUEST['database-username'].'&database-password='.$_REQUEST['database-password'].'&database-allocation='.$_REQUEST['database-allocation'].'&database-name='.$_REQUEST['database-name']);
                    break;
                default:
                    p_errormsg(lng('error'), sprintf(lng('queryerror'), $query, mysql_error()));
            }
        }

        mysql_select_db($_REQUEST['database-name']);

        $r_table = mysql_list_tables($_REQUEST['database-name']);
        $a_tables = [];
        $i = 0;

        while ($i < mysql_num_rows($r_table)) {
            $tables[] = mysql_tablename($r_table, $i);
            $i++;
        }

        p_header('table-create');
        p_chooseprefix($_REQUEST['database-name'], $tables, $_REQUEST['table-prefix'], $_REQUEST['database-clear'] == 'true');
        p_footer('table-create', [
            'database-hostname' => $_REQUEST['database-hostname'],
            'database-username' => $_REQUEST['database-username'],
            'database-password' => $_REQUEST['database-password'],
            'database-name' => $_REQUEST['database-name']
        ]);
        break;

    case 'database-select':
        if (!isset($_REQUEST['database-allocation'])) {
            $_REQUEST['database-allocation'] = 'use';
        }

        if (!isset($_REQUEST['database-name'])) {
            $_REQUEST['database-name'] = '';
        }

        if (empty($_REQUEST['database-hostname'])) {
            p_errormsg(lng('error'), sprintf(lng('nodatabasehosterror')), '?step=database-credentials&license-accept=true');
        }

        if (empty($_REQUEST['database-username']) && empty($_REQUEST['database-password'])) {
            p_errormsg(lng('error'), sprintf(lng('nocredentialserror')), '?step=database-credentials&license-accept=true');
        }

        $dbhandle = @mysql_connect($_REQUEST['database-hostname'], $_REQUEST['database-username'], $_REQUEST['database-password']);

        if (!$dbhandle) {
            $message = '';
            $backlink = '';

            switch (mysql_errno()) {
                case 1045:
                    $message = lng('wrongcredentialserror');
                    $backlink = '?step=database-credentials&license-accept=true&database-hostname='.$_REQUEST['database-hostname'].'&database-username='.$_REQUEST['database-username'];
                    break;
                case 2002:
                    $message = lng('cannotconnecterror');
                    $backlink = '?step=database-credentials&license-accept=true&database-hostname='.$_REQUEST['database-hostname'].'&database-username='.$_REQUEST['database-username'];
                    break;
                default:
                    $message = sprintf(lng('connecterror'), mysql_errno());
                    $backlink = 'JavaScript:history.back(0)';
                    break;
            }

            p_errormsg(lng('error'), $message, $backlink);
        }

        $r_database = thwb_query(
<<<SQL
SHOW
    DATABASES
WHERE
    `Database`
NOT IN (
    'information_schema',
    'mysql',
    'performance_schema',
    'test'
)
SQL
        );

        $databases = [];
        $i = 0;

        while ($i < mysql_num_rows($r_database)) {
            $databases[] = mysql_tablename($r_database, $i);
            $i++;
        }

        p_header('table-prefix');
        p_selectdb($databases, $_REQUEST['database-allocation'], $_REQUEST['database-name']);
        p_footer('table-prefix', [
            'database-hostname' => $_REQUEST['database-hostname'],
            'database-username' => $_REQUEST['database-username'],
            'database-password' => $_REQUEST['database-password']
        ]);
        break;

    case 'database-credentials':
        if (!isset($_REQUEST['database-hostname'])) {
            $_REQUEST['database-hostname'] = 'localhost';
        }

        if (!isset($_REQUEST['database-username'])) {
            $_REQUEST['database-username'] = '';
        }

        if ($_REQUEST['license-accept'] != 'true') {
            p_errormsg(lng('error'), lng('licaccept'), '?step=license');
        } else {
            p_header('database-select');
            p_mysqldata($_REQUEST['database-hostname'], $_REQUEST['database-username']);
            p_footer('database-select');
        }
        break;

    case 'license':
        p_header('database-credentials');
        p_license($_REQUEST['license-accept']);
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
