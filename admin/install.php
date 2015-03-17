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

if (version_compare(PHP_VERSION, '5.4', '<')) {
    die(
        'ThWboard install can not be performed because the PHP version used is to old ( < 5.4 ).<br>'.
        'Please ask your webmaster or server administrator for updating the PHP version.'
    );
}

session_start();

require 'install_common.inc.php';

if (!isset($_GET['step'])) {
    $_GET['step'] = '';
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
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

        p_configuration(STDOUT, $_SESSION);
        break;

    case 'configuration-write':
        if (isset($_POST['submit'])) {
            if (!WriteAccess('../inc/config.inc.php')) {
                p_errormsg(lng('error'), lng('chmoderror'), 'JavaScript:history.back(0)');
                exit;
            } else {
                $fp = @fopen('../inc/config.inc.php', 'w');
                p_configuration($fp, $_SESSION);
                fclose($fp);

                echo $template->render('install-done', [
                    'about_handler' => 'install.php?step=about'
                ]);
            }
        } else {
            echo $template->render('install-configuration-write', [
                'about_handler' => 'install.php?step=about',
                'download_url' => 'install.php?step=configuration-download',
                'step' => 'configuration-write'
            ]);
        }
        break;

    case 'administrator-create':
        if (!isset($_SESSION['administrator-username'])) {
            $_SESSION['administrator-username'] = 'root';
        }

        if (!isset($_SESSION['administrator-email'])) {
            $_SESSION['administrator-email'] = '';
        }

        if (isset($_POST['submit'])) {
            $_SESSION['administrator-username'] = $_POST['administrator-username'];
            $_SESSION['administrator-email'] = $_POST['administrator-email'];
            $_SESSION['administrator-password'] = $_POST['administrator-password'];

            if (empty($_SESSION['administrator-username'])) {
                p_errormsg(lng('error'), lng('adminnameempty'), '?step=administrator-create');
                exit;
            }

            if (strlen($_SESSION['administrator-password']) < 5) {
                p_errormsg(lng('error'), lng('adminpwtooshort'), '?step=administrator-create');
                exit;
            }

            mysql_connect($_SESSION['database-hostname'], $_SESSION['database-username'], $_SESSION['database-password']);
            mysql_select_db($_SESSION['database-name']);

            thwb_query(
<<<SQL
INSERT INTO
    {$_SESSION['table-prefix']}user
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
    '{$_SESSION['administrator-username']}',
    '{$_SESSION['administrator-email']}',
    MD5('{$_SESSION['administrator-password']}'),
    '1',
    UNIX_TIMESTAMP(),
    ',3,',
    '1'
)
SQL
           );

            header('Location: '.$_SERVER['PHP_SELF'].'?step=configuration-write');
            exit();
        }

        echo $template->render('install-administrator-create', [
            'about_handler' => 'install.php?step=about',
            'email' => $_SESSION['administrator-email'],
            'step' => 'administrator-create',
            'username' => $_SESSION['administrator-username']
        ]);
        break;

    case 'table-create':
        mysql_connect($_SESSION['database-hostname'], $_SESSION['database-username'], $_SESSION['database-password']);
        mysql_select_db($_SESSION['database-name']);

        if (!isset($_SESSION['table-prefix'])) {
            $_SESSION['table-prefix'] = 'tb_';
        }

        if (!isset($_SESSION['database-clear'])) {
            $_SESSION['database-clear'] = false;
        }

        if (isset($_POST['submit'])) {
            $_SESSION['table-prefix'] = $_POST['table-prefix'];
            $_SESSION['database-clear'] = (isset($_POST['database-clear']) && $_POST['database-clear'] == 'true');

            if (preg_match('/[^a-zA-Z1-9_]/', $_POST['table-prefix'])) {
                p_errormsg(lng('error'), lng('invalidtableprefixerror'), '?step=table-create');
                exit;
            }

            create_tables($_SESSION['table-prefix'], $_SESSION['database-clear']);

            header('Location: '.$_SERVER['PHP_SELF'].'?step=administrator-create');
            exit();
        }

        $r_table = mysql_list_tables($_SESSION['database-name']);
        $a_tables = [];
        $i = 0;

        while ($i < mysql_num_rows($r_table)) {
            $tables[] = mysql_tablename($r_table, $i);
            $i++;
        }

        echo $template->render('install-table-prefix', [
            'about_handler' => 'install.php?step=about',
            'database_name' => $_SESSION['database-name'],
            'database_overwrite' => $_SESSION['database-clear'],
            'step' => 'table-create',
            'table_prefix' => $_SESSION['table-prefix'],
            'tables' => $tables
        ]);
        break;

    case 'database-select':
        if (!isset($_SESSION['database-allocation'])) {
            $_SESSION['database-allocation'] = 'use';
        }

        if (!isset($_SESSION['database-name'])) {
            $_SESSION['database-name'] = '';
        }

        mysql_connect($_SESSION['database-hostname'], $_SESSION['database-username'], $_SESSION['database-password']);

        if (isset($_POST['submit'])) {
            $_SESSION['database-allocation'] = $_POST['database-allocation'];
            $_SESSION['database-name'] = ($_SESSION['database-allocation'] == 'use') ? $_POST['database-name-use'] : $_POST['database-name-new'];

            if (preg_match('/[^a-zA-Z0-9_]/', $_SESSION['database-name'])) {
                p_errormsg(lng('error'), lng('invaliddatabasenameerror'), '?step=database-select');
                exit;
            }

            if ($_SESSION['database-allocation'] == 'new') {
                $query = "CREATE DATABASE ".$_SESSION['database-name'];
                mysql_query($query);

                switch (mysql_errno()) {
                    case 0:
                        break;
                    case 1044:
                        p_errormsg(lng('error'), lng('cantcreatedatabaseerror'), '?step=database-select');
                        exit;
                        break;
                    default:
                        p_errormsg(lng('error'), sprintf(lng('queryerror'), $query, mysql_error()));
                        exit;
                }

                $_SESSION['database-allocation'] = 'use';
            }

            header('Location: '.$_SERVER['PHP_SELF'].'?step=table-create');
            exit();
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

        echo $template->render('install-database-select', [
            'about_handler' => 'install.php?step=about',
            'allocation' => $_SESSION['database-allocation'],
            'database_name' => $_SESSION['database-name'],
            'databases' => $databases,
            'step' => 'database-select'
        ]);
        break;

    case 'database-credentials':
        if (!isset($_SESSION['database-hostname'])) {
            $_SESSION['database-hostname'] = 'localhost';
        }

        if (!isset($_SESSION['database-username'])) {
            $_SESSION['database-username'] = '';
        }

        if (isset($_POST['submit'])) {
            if (empty($_POST['database-hostname'])) {
                p_errormsg(lng('error'), sprintf(lng('nodatabasehosterror')), '?step=database-credentials');
                exit;
            }

            if (empty($_POST['database-username']) && empty($_POST['database-password'])) {
                p_errormsg(lng('error'), sprintf(lng('nocredentialserror')), '?step=database-credentials');
                exit;
            }

            $_SESSION['database-hostname'] = $_POST['database-hostname'];
            $_SESSION['database-username'] = $_POST['database-username'];
            $_SESSION['database-password'] = $_POST['database-password'];

            $dbhandle = @mysql_connect($_SESSION['database-hostname'], $_SESSION['database-username'], $_SESSION['database-password']);

            if (!$dbhandle) {
                $message = '';
                $backlink = '';

                switch (mysql_errno()) {
                    case 1045:
                        $message = lng('wrongcredentialserror');
                        $backlink = '?step=database-credentials';
                        break;
                    case 2002:
                        $message = lng('cannotconnecterror');
                        $backlink = '?step=database-credentials';
                        break;
                    default:
                        $message = sprintf(lng('connecterror'), mysql_errno());
                        $backlink = 'JavaScript:history.back(0)';
                        break;
                }

                p_errormsg(lng('error'), $message, $backlink);
                exit;
            }

            header('Location: '.$_SERVER['PHP_SELF'].'?step=database-select');
            exit();
        }

        echo $template->render('install-database-credentials', [
            'about_handler' => 'install.php?step=about',
            'hostname' => $_SESSION['database-hostname'],
            'step' => 'database-credentials',
            'username' => $_SESSION['database-username']
        ]);
        break;

    case 'license':
        if (!isset($_SESSION['license-accept'])) {
            $_SESSION['license-accept'] = false;
        }

        if (isset($_POST['submit'])) {
            $_SESSION['license-accept'] = (isset($_REQUEST['license-accept']) && $_REQUEST['license-accept'] == 'true');

            if (!$_SESSION['license-accept']) {
                p_errormsg(lng('error'), lng('licaccept'), '?step=license');
                exit;
            }

            header('Location: '.$_SERVER['PHP_SELF'].'?step=database-credentials');
            exit();
        }

        echo $template->render('install-license', [
            'about_handler' => 'install.php?step=about',
            'accept' => $_SESSION['license-accept'],
            'license' => implode('', file('../COPYING')),
            'step' => 'license'
        ]);
        break;

    case 'about':
        echo $template->render('install-about', [
            'about_handler' => 'install.php?step=about',
        ]);
        break;

    case 'deny':
        p_errormsg(lng('denied'), lng('deniedtxt'));
        exit;
        break;

    case 'welcome':
        if (isset($_POST['submit'])) {
            header('Location: '.$_SERVER['PHP_SELF'].'?step=license');
            exit();
        }

        echo $template->render('install-welcome', [
            'about_handler' => 'install.php?step=about',
            'step' => 'welcome'
        ]);
        break;

    case 'install-language':
    default:
        if (isset($_POST['submit'])) {
            $_SESSION['lang'] = $_POST['lang'];

            header('Location: '.$_SERVER['PHP_SELF'].'?step=welcome');
            exit();
        }

        echo $template->render('install-selectlanguage', [
            'about_handler' => 'install.php?step=about',
            'languages' => $a_lang,
            'step' => 'install-language'
        ]);
}
