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

require_once __DIR__.'/../../vendor/autoload.php';

$php_code_coverage = null;
$php_code_coverage_db = null;

if (getenv('PHP_CODE_COVERAGE')) {
    $php_code_coverage_db = file(getenv('PHP_CODE_COVERAGE'), FILE_IGNORE_NEW_LINES);

    $php_code_coverage_db_tmp = [];

    foreach($php_code_coverage_db as $entry) {
        $entry = explode('=', $entry, 2);
        $php_code_coverage_db_tmp[$entry[0]] = unserialize($entry[1]);
    }

    $php_code_coverage_db = $php_code_coverage_db_tmp;
    unset($php_code_coverage_db_tmp);

    $php_code_coverage = new PHP_CodeCoverage();

    $php_code_coverage_filter = $php_code_coverage->filter();

    $php_code_coverage_filter->addFileToBlacklist(__FILE__);

    foreach ($php_code_coverage_db as $entry) {
        $php_code_coverage_filter->addFileToWhitelist($entry['fullname']);
    }

    $php_code_coverage->start(basename(getenv('PHP_CODE_COVERAGE')));

    register_shutdown_function(
        function ($php_code_coverage, $php_code_coverage_db) {
            $php_code_coverage->stop();

            foreach ($php_code_coverage->getData(true) as $file => $coverage) {
                foreach($coverage as $line => $id) {
                    if (!isset($php_code_coverage_db[strtolower($file)]['coverage'][$line]) || !is_array($php_code_coverage_db[strtolower($file)]['coverage'][$line])) {
                        $php_code_coverage_db[strtolower($file)]['coverage'][$line] = [];
                    }

                    $id = (!is_array($id)) ? [$id] : $id;

                    $php_code_coverage_db[strtolower($file)]['coverage'][$line] = array_unique(array_merge($id, $php_code_coverage_db[strtolower($file)]['coverage'][$line]));
                }
            }

            $php_code_coverage_db_tmp = [];

            foreach ($php_code_coverage_db as $file => $data) {
                $php_code_coverage_db_tmp[] = strtolower($file).'='.serialize($data);
            }

            $php_code_coverage_db = $php_code_coverage_db_tmp;
            unset($php_code_coverage_db_tmp);

            file_put_contents(getenv('PHP_CODE_COVERAGE'), implode("\n", $php_code_coverage_db)."\n");
        },
        $php_code_coverage,
        $php_code_coverage_db
    );
}
