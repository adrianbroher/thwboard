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

require 'install_common.inc.php';

include '../inc/config.inc.php';

mysql_connect($mysql_h, $mysql_u, $mysql_p);
mysql_select_db($mysql_db);

if (!$pref) {
    $pref = 'thwb_';
}

session_start();

if (!isset($_SESSION['authenticated']) && isset($_GET['step']) && $_GET['step'] != 'login') {
    header('Location: '.$_SERVER['PHP_SELF']);
    exit();
}

if (!isset($_GET['step'])) {
    $_GET['step'] = 'login';
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

if ($version < 2.8) {
    p_errormsg(lng('error'), lng('installationtooold'));
}

switch ($_GET['step']) {
    case 'update-run':
        include 'updates/'.$_SESSION['update'];

        $update = new CUpdate();

        $update->Prefix = $pref;

        if (!$update->AllowUpdate()) {
            p_errormsg(lng('error'), lng('cantexec'), 'JavaScript:history.back(0)');
        }

        if ($update->RunUpdate()) {
            p_errormsg(lng('error'), $update->GetError(), 'JavaScript:history.back(0)');
        }

        p_errormsg(lng('updatesuccess'), lng('updatesuccesstxt'));
        break;

    case 'update-show':
        include 'updates/'.$_SESSION['update'];

        $update = new CUpdate();

        $update->Prefix = $pref;

        if ($update->UpdaterVer > $cfg['updater_ver']) {
            p_errormsg(lng('error'), lng('tooold'), 'JavaScript:history.back(0)');
        }

        echo $template->render('update-show', [
            'about_handler' => 'install.php?step=about',
            'step' => 'update-run',
            'update' => $update
        ]);
        break;

    case 'update-select':
        $a_file = [];
        $dp = opendir('updates/');

        while ($file = readdir($dp)) {
            if (substr($file, -7, 7) == '.update') {
                $a_file[] = $file;
            }
        }

        if (isset($_POST['submit'])) {
            if (empty($_POST['update-selected']) || !in_array($_POST['update-selected'], $a_file)) {
                p_errormsg(lng('error'), lng('notfound'), 'JavaScript:history.back(0)');
            }

            $_SESSION['update'] = $_POST['update-selected'];

            header('Location: '.$_SERVER['PHP_SELF'].'?step=update-show');
            exit();
        }

        natsort($a_file);
        echo $template->render('update-select', [
            'about_handler' => 'install.php?step=about',
            'step' => 'update-select',
            'updates' => $a_file
        ]);
        break;

    case 'login':
        if (isset($_POST['submit'])) {
            $_SESSION['lang'] = $_POST['lang'];

            if (empty($_POST['login-username']) && empty($_POST['login-password'])) {
                p_errormsg(lng('error'), lng('noadmincredentialserror'), '?step=login');
            }

            $loginUsername = addslashes($_POST['login-username']);

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

            if (mysql_num_rows($r_user) === 0) {
                p_errormsg(lng('error'), lng('wrongadmincredentialserror'), '?step=login');
            }

            $user = mysql_fetch_array($r_user);

            if ($user['userpassword'] != md5($_POST['login-password'])) {
                p_errormsg(lng('error'), lng('wrongadmincredentialserror'), '?step=login');
            }

            $_SESSION['authenticated'] = true;

            header('Location: '.$_SERVER['PHP_SELF'].'?step=update-select');
            exit();
        }

        echo $template->render('update-login', [
            'about_handler' => 'install.php?step=about',
            'languages' => $a_lang,
            'step' => 'login'
        ]);
        break;

}
