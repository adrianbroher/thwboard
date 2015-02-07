# ThWboard -- board creation feature test
# =======================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Board create
    Boards a named groups of threads.  Administrator can create those so
    that users can write threads to a certain topic.  Board names must be
    unique within the whole forum.

    Background:
        Given the forum is installed
        And the following users exist:
            | name | password | email          | member of   | flags   |
            | root | rootroot | root@localhost | Admin Group | isadmin |
        And the following categories exist:
            | name       |
            | Category 1 |
            | Category 2 |
        And the following boards exist:
            | name       | description            | category   |
            | Board 1    | Description of Board 1 | Category 1 |
        And I am on "/admin/"
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Login"
        Then I should see "Add board"
        And I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |

    Scenario: Create a board with an empty name.
        When I follow "Add board"
        Then I should see "New Board"
        And the "Name" field should contain ""
        And the "Description" field should contain ""
        And I should see "Category 1" selected in the select "Category"
        And I should see the following <value> available in the select "Category":
            | value      |
            | Category 1 |
            | Category 2 |
        And I should see "( Use default )" selected in the select "Style"
        And I should see the following <value> available in the select "Style":
            | value           |
            | ( Use default ) |
        And I should see "Enable board" selected in the select "Status"
        And I should see the following <value> available in the select "Status":
            | value         |
            | Disable board |
            | Enable board  |
        When I fill in the following:
            | Name        |                     |
            | Description | A short description |
        And I select "Category 1" from "Category"
        And I select "( Use default )" from "Style"
        And I select "Enable board" from "Status"
        And I press "Save"
        Then I should see "The board name can't be empty"
        And I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |

    Scenario: Create a duplicate board.
        When I follow "Add board"
        Then I should see "New Board"
        And the "Name" field should contain ""
        And the "Description" field should contain ""
        And I should see "Category 1" selected in the select "Category"
        And I should see the following <value> available in the select "Category":
            | value      |
            | Category 1 |
            | Category 2 |
        And I should see "( Use default )" selected in the select "Style"
        And I should see the following <value> available in the select "Style":
            | value           |
            | ( Use default ) |
        And I should see "Enable board" selected in the select "Status"
        And I should see the following <value> available in the select "Status":
            | value         |
            | Disable board |
            | Enable board  |
        When I fill in the following:
            | Name        | Board 1                         |
            | Description | A short description for Board 1 |
        And I select "Category 1" from "Category"
        And I select "( Use default )" from "Style"
        And I select "Enable board" from "Status"
        And I press "Save"
        Then I should see "The board already exists"
        And I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |

    Scenario: Successfully create a board in the first category.
        When I follow "Add board"
        Then I should see "New Board"
        And the "Name" field should contain ""
        And the "Description" field should contain ""
        And I should see "Category 1" selected in the select "Category"
        And I should see the following <value> available in the select "Category":
            | value      |
            | Category 1 |
            | Category 2 |
        And I should see "( Use default )" selected in the select "Style"
        And I should see the following <value> available in the select "Style":
            | value           |
            | ( Use default ) |
        And I should see "Enable board" selected in the select "Status"
        And I should see the following <value> available in the select "Status":
            | value         |
            | Disable board |
            | Enable board  |
        When I fill in the following:
            | Name        | Board 2                         |
            | Description | A short description for Board 2 |
        And I select "Category 1" from "Category"
        And I select "( Use default )" from "Style"
        And I select "Enable board" from "Status"
        And I press "Save"
        Then I should see "Board saved"
        And I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 2          |
            | Category 2 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 1 |

    Scenario: Successfully create a board in the second category.
        When I follow "Add board"
        Then I should see "New Board"
        And the "Name" field should contain ""
        And the "Description" field should contain ""
        And I should see "Category 1" selected in the select "Category"
        And I should see the following <value> available in the select "Category":
            | value      |
            | Category 1 |
            | Category 2 |
        And I should see "( Use default )" selected in the select "Style"
        And I should see the following <value> available in the select "Style":
            | value           |
            | ( Use default ) |
        And I should see "Enable board" selected in the select "Status"
        And I should see the following <value> available in the select "Status":
            | value         |
            | Disable board |
            | Enable board  |
        When I fill in the following:
            | Name        | Board 2                         |
            | Description | A short description for Board 2 |
        And I select "Category 2" from "Category"
        And I select "( Use default )" from "Style"
        And I select "Enable board" from "Status"
        And I press "Save"
        Then I should see "Board saved"
        And I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 1          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |


# ----------------------------------------------------------------------
# This file is part of ThWboard
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program;  If not, see <http://www.gnu.org/licenses/>.
