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

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;dbname=%s', $mysql_h, $mysql_db),
        $mysql_u,
        $mysql_p
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->prefix = $pref;

    $stmt = $pdo->query(
<<<SQL
SELECT
    keyvalue
FROM
    {$pref}registry
WHERE
    keyname = 'version'
SQL
    );

    $version = (float)$stmt->fetch(PDO::FETCH_COLUMN, 0);

    if ($version < 2.8) {
        p_errormsg(lng('error'), lng('installationtooold'));
        exit;
    }

    switch ($_GET['step']) {
        case 'update-run':
            $update = include 'updates/'.$_SESSION['update'];

            try {
                $update->upgrade($pdo);
            } catch(RuntimeException $e) {
                p_errormsg(lng('error'), $e->getMessage(), 'JavaScript:history.back(0)');
                exit;
            }

            p_errormsg(lng('updatesuccess'), lng('updatesuccesstxt'));
            exit;
            break;

        case 'update-show':
            $update = include 'updates/'.$_SESSION['update'];

            echo $template->render('update-show', [
                'about_handler' => 'install.php?step=about',
                'step' => 'update-run',
                'update' => $update,
                'schema_version' => schema_version($pdo)
            ]);
            break;

        case 'update-select':
            $updates = [];
            $dp = opendir('updates/');

            while ($file = readdir($dp)) {
                if (substr($file, -4, 4) == '.php' && substr($file, -6, 6) != '.0.php') {
                    $update = include 'updates/'.$file;
                    $updates[$file] = sprintf('%s -> %s', $update->fromVersion, $update->toVersion);
                }
            }

            if (isset($_POST['submit'])) {
                if (empty($_POST['update-selected']) || !in_array($_POST['update-selected'], array_keys($updates))) {
                    p_errormsg(lng('error'), lng('notfound'), 'JavaScript:history.back(0)');
                    exit;
                }

                $_SESSION['update'] = $_POST['update-selected'];

                header('Location: '.$_SERVER['PHP_SELF'].'?step=update-show');
                exit();
            }

            natsort($updates);
            echo $template->render('update-select', [
                'about_handler' => 'install.php?step=about',
                'step' => 'update-select',
                'updates' => $updates
            ]);
            break;

        case 'login':
            if (isset($_POST['submit'])) {
                $_SESSION['lang'] = $_POST['lang'];

                    if (empty($_POST['login-username']) && empty($_POST['login-password'])) {
                    p_errormsg(lng('error'), lng('noadmincredentialserror'), '?step=login');
                    exit;
                }

                $stmt = $pdo->prepare(
<<<SQL
SELECT
    userid
FROM
    {$pref}user
WHERE
    username = :username AND
    userpassword = :password AND
    userisadmin = 1
SQL
                );

                $stmt->bindValue(':username', $_POST['login-username'], PDO::PARAM_STR);
                $stmt->bindValue(':password', md5($_POST['login-password']), PDO::PARAM_STR);
                $stmt->execute();

                if (!$stmt->rowCount()) {
                    p_errormsg(lng('error'), lng('wrongadmincredentialserror'), '?step=login');
                    exit;
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
} catch (PDOException $e) {
    p_errormsg(lng('error'), sprintf(lng('queryerror'), '', $e->getMessage()));
}
