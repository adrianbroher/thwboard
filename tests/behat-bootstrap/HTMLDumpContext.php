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

use Behat\Behat\Context\Context,
    Behat\Behat\Hook\Scope\AfterStepScope,
    Behat\Behat\Tester\Result\ExecutedStepResult;

/**
 * The behat features context for creating dumps after failed steps.
 */
class HTMLDumpContext implements Context
{
    private $htmlDumpPath = null;
    private $contextName = null;

    /** Constructs a new html dump context
     *
     * @param $context_name The name of the context, that is used as mink
     *        context.
     * @param $html_dump_path The path to the directory where the html
     *        documents should be dumped into.
     */
    public function __construct($context_name, $html_dump_path)
    {
        $this->contextName  = $context_name;
        $this->htmlDumpPath = $html_dump_path;
    }

    /** Write a html dump after a failing step.
     *
     * @AfterStep
     */
    public function dumpPageAfterFailedStep(AfterStepScope $event)
    {
        if ($event->getTestResult() instanceof ExecutedStepResult && !$event->getTestResult()->isPassed()) {
            if (!is_dir($this->htmlDumpPath)) {
                mkdir($this->htmlDumpPath, 0755, true);
            }

            $minkContext = $event->getEnvironment()->getContext($this->contextName);

            if (!$minkContext) {
                return;
            }

            $filePath = $this->htmlDumpPath . '/' . date('Y-m-d-H-i-s') . '_' . uniqid() . '.html';

            file_put_contents($filePath, "<!-- HTML dump from behat\nDate: " . date('Y-m-d H:i:s') . "\nUrl:  " . $minkContext->getSession()->getCurrentUrl() . "\n-->\n" . $minkContext->getSession()->getPage()->getContent());

            $message = "\nHTML saved to: file://" . $filePath;

            $exception = $event->getTestResult()->getException();

            $refObj = new ReflectionObject($exception);
            $refObjProp = $refObj->getProperty('message');
            $refObjProp->setAccessible(true);
            $refObjProp->setValue($exception, $exception->getMessage() . $message);
        }
    }
}
