# ThWboard -- board edit feature test
# ===================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Board edit
    Boards a named groups of threads.  Administrator can edit those so
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
            | Board 2    | Description of Board 2 | Category 2 |
            | Board 3    | Description of Board 3 | Category 2 |
        And I am on "/admin/"
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Login"
        Then I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 2          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |
            | Board 3    | is child of        | Category 2 |
            | Board 2    | is before          | Board 3    |

    Scenario: Edit a board with an empty name.
        When I follow "Edit boards/categories"
        Then I should see "Board 2"
        And I should see "edit"
        When I follow "Edit board Board 2"
        Then I should see "Edit Board"
        And the "Name" field should contain "Board 2"
        And the "Description" field should contain "Description of Board 2"
        And I should see "Category 2" selected in the select "Category"
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
        When I fill in "Name" with ""
        And I press "Save"
        Then I should see "The board name can't be empty"
        And I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 2          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |
            | Board 3    | is child of        | Category 2 |
            | Board 2    | is before          | Board 3    |

    Scenario: Edit a duplicate board.
        When I follow "Edit boards/categories"
        Then I should see "Board 2"
        And I should see "edit"
        When I follow "Edit board Board 2"
        Then I should see "Edit Board"
        And the "Name" field should contain "Board 2"
        And the "Description" field should contain "Description of Board 2"
        And I should see "Category 2" selected in the select "Category"
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
        When I fill in "Name" with "Board 3"
        And I press "Save"
        Then I should see "The board already exists"
        And I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 2          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |
            | Board 3    | is child of        | Category 2 |
            | Board 2    | is before          | Board 3    |

    Scenario: Successfully rename a board.
        When I follow "Edit boards/categories"
        Then I should see "Board 2"
        And I should see "edit"
        When I follow "Edit board Board 2"
        Then I should see "Edit Board"
        And the "Name" field should contain "Board 2"
        And the "Description" field should contain "Description of Board 2"
        And I should see "Category 2" selected in the select "Category"
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
        When I fill in "Name" with "Board B"
        And I press "Save"
        Then I should see "Board saved"
        And I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 2          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |
            | Board B    | is child of        | Category 2 |
            | Board 3    | is child of        | Category 2 |
            | Board B    | is before          | Board 3    |

    Scenario: Successfully move a board.
        When I follow "Edit boards/categories"
        Then I should see "Board 2"
        And I should see "edit"
        When I follow "Edit board Board 2"
        Then I should see "Edit Board"
        And the "Name" field should contain "Board 2"
        And the "Description" field should contain "Description of Board 2"
        And I should see "Category 2" selected in the select "Category"
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
        When I select "Category 1" from "Category"
        And I press "Save"
        Then I should see "Board saved"
        And I should see "Edit boards/categories"
        When I follow "Edit boards/categories"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | number of children | 2          |
            | Category 2 | number of children | 1          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 1 |
            | Board 3    | is child of        | Category 2 |
            | Board 1    | is before          | Board 2    |


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
