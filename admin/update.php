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

require('install_functions.php');

include '../inc/config.inc.php';

mysql_connect($mysql_h, $mysql_u, $mysql_p);
mysql_select_db($mysql_db);

if (!$pref) {
    $pref = 'thwb_';
}

$r_registry = thwb_query(
<<<SQL
SELECT
    keyvalue
FROM
    {$pref}registry
WHERE
    keyname = 'version'
SQL
);

list($version) = mysql_fetch_row($r_registry);
$version = (float)($version);

$loginUsername = addslashes($_POST['login-username']);

if ($version < 2.8) {
    $r_user = thwb_query(
<<<SQL
SELECT
    userpassword
FROM
    {$pref}user
WHERE
    username = '{$loginUsername}' AND
    userlevel = 1
SQL
    );
} else {
    $r_user = thwb_query(
<<<SQL
SELECT
    userpassword
FROM
    {$pref}user
WHERE
    username = '{$loginUsername}' AND
    userisadmin = 1
SQL
    );
}

if (mysql_num_rows($r_user)) {
    $user = mysql_fetch_array($r_user);

    if ($user['userpassword'] != md5($_POST['login-password'])) {
        $_POST['step'] = 'login';
    }
} else {
    $_POST['step'] = 'login';
}

switch ($_POST['step']) {
    case 'login':
        p_header();
        p_loginform();
        p_footer('update-select');
        break;

    case 'update-run':
        include $_POST['update-run'];
        $update = new CUpdate();

        $update->Prefix = $pref;

        if (!$update->AllowUpdate()) {
            p_errormsg(lng('error'), lng('cantexec'));
        }

        if ($update->RunUpdate()) {
            p_errormsg(lng('error'), $update->GetError());
        } else {
            p_errormsg(lng('updatesuccess'), lng('updatesuccesstxt'));
        }

        break;

    case 'update-show':
        $scriptname = 'updates/'.$_POST['update-selected'];

        if (!file_exists($scriptname) || !$scriptname) {
            p_errormsg(lng('error'), lng('notfound'));
        } else {
            include $scriptname;

            $update = new CUpdate();

            $update->Prefix = $pref;
            if ($update->UpdaterVer > $cfg['updater_ver']) {
                p_errormsg(lng('error'), lng('tooold'));
            } else {
                p_header();
                p_updateinfo($update);
                p_footer('update-run', [
                    'update-run' => $scriptname,
                    'login-username' => $_POST['login-username'],
                    'login-password' => $_POST['login-password']
                ]);
            }
        }
        break;

    case 'update-select':
    default:
        $a_file = [];
        $dp = opendir('updates/');

        while ($file = readdir($dp)) {
            if (substr($file, -7, 7) == '.update') {
                $a_file[] = $file;
            }
        }

        natsort($a_file);
        p_header();
        p_updatewelcome($a_file);
        p_footer('update-show', [
            'login-username' => $_POST['login-username'],
            'login-password' => $_POST['login-password']
        ]);
        break;
}
