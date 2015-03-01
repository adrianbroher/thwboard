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
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Exception\ExpectationException;

/**
 * The behat features context for validating html documents.
 */
class HTMLValidateContext implements Context
{
    private $contextName = null;
    private $minkContext = null;
    private $tidy = null;

    /** Constructs a new html validation context
     *
     * @param $context_name The name of the context, that is used as mink
     *        context.
     */
    public function __construct($context_name)
    {
        $this->contextName  = $context_name;
        $this->tidy = new tidy();
    }

    /** Fetch the behat environment.
     *
     * @BeforeScenario
     *
     * @param scope
     */
    public function gatherContext(BeforeScenarioScope $scope)
    {
        $this->minkContext = $scope->getEnvironment()->getContext($this->contextName);

        if (!$this->minkContext) {
            $message = sprintf("Unable to fetch the required context: %s", $this->contextName);
            throw new ExpectationException($message, null);
        }
    }

    /** Validate the returned HTML document.
     *
     * @Then /^the returned HTML document should be valid$/
     */
    public function validateHTMLDocument()
    {
        $this->tidy->parseString($this->minkContext->getSession()->getPage()->getContent(), [
            'output-xhtml' => 'yes'
        ]);

        if (tidy_error_count($this->tidy) || tidy_warning_count($this->tidy)) {
            $message = sprintf("The following errors were found when validating the HTML document:\n%s", $this->tidy->errorBuffer);
            throw new ExpectationException($message, $this->minkContext->getSession());
        }
    }
}
