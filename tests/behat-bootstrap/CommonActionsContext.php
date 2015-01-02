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

use Behat\Gherkin\Node\TableNode;

use Behat\Mink\Exception\ElementNotFoundException,
    Behat\Mink\Exception\ElementTextException;
use Behat\MinkExtension\Context\MinkContext;

/**
 * A behat context that implements common actions.
 */
class CommonActionsContext extends MinkContext
{
    /** Checks if an option is selected in a select
     *
     * @Then /^(?:|I )should see "(?P<value>[^"]*)" selected in the select "(?P<field>[^"]*)"$/
     *
     * @param $value The option string to be searched for in the select widget.
     * @param $field The select widget field selector.
     */
    public function assertSelectHasOptionSelected($value, $field)
    {
        $value = $this->fixStepArgument($value);
        $field = $this->fixStepArgument($field);

        $select = $this->assertSession()->fieldExists($field);

        $selectedOption = $select->find('xpath', '//option[@selected="selected"]');

        if ($selectedOption === null) {
            $selectedOption = $select->find('xpath', '//option[1]');
        }

        if ($selectedOption === null) {
            throw new ElementNotFoundException($this->getSession(), 'Selected option ');
        }

        if ($selectedOption->getText() !== $value) {
            $message = sprintf('The option "%s" should be selected in the select element matching id|name|label|value "%s", but was not.', $value, $field);
            throw new ElementTextException($message, $this->getSession(), $select);
        }
    }

    /** Checks if all given options can be found within a select
     *
     * @Then /^(?:|I )should see the following <value> available in the select "(?P<field>[^"]*)":$/
     */
    public function assertSelectContainsOptions($field, TableNode $table)
    {
        $field = $this->fixStepArgument($field);

        $select = $this->assertSession()->fieldExists($field);
        $options = $select->findAll('xpath', '//option');

        $expectedOptions  = array_map(function ($e) { return $e['value']; }, $table->getHash());
        $availableOptions = array_map(function ($e) { return $e->getText(); }, $options);

        if ($expectedOptions !== $availableOptions) {
            $message = sprintf('The options in the select element matching id|name|label|value "%s" are not identical to given values.', $field);
            throw new ElementTextException($message, $this->getSession(), $select);
        }
    }

    /** Check a radio button by label.
     *
     * @When /^(?:|I )check the "(?P<field>[^”]*)" radio button$/
     * */
    public function doCheckRadioButton($field)
    {
        $n_field = $this->fixStepArgument($field);

        $radioButton = $this->assertSession()->fieldExists($n_field);
        $value = $radioButton->getAttribute('value');

        $this->getSession()->getPage()->fillField($field, $value);
    }
}
