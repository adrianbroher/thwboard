# ThWboard -- board reordering feature test
# =========================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Board reorder
    When creating and editing boards their contents is maybe out of place
    in the current location within the structure.  Administrators can
    move boards and categories to another place within the structure.

    Background:
        Given the forum is installed
        And the following users exist:
            | name | password | email          | member of   | flags   |
            | root | rootroot | root@localhost | Admin Group | isadmin |
        And the following categories exist:
            | name       |
            | Category 1 |
            | Category 2 |
            | Category 3 |
        And the following boards exist:
            | name       | description            | category   |
            | Board 1    | Description of Board 1 | Category 1 |
            | Board 2    | Description of Board 2 | Category 2 |
            | Board 3    | Description of Board 3 | Category 2 |
            | Board 4    | Description of Board 4 | Category 1 |
            | Board 5    | Description of Board 5 | Category 1 |
        And I am on "/admin/"
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Login"
        And I should see "Categories and Boards"
        When I follow "Categories and Boards"
        Then the returned HTML document should be valid
        And the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 3          |
            | Category 1 | number of children | 3          |
            | Category 2 | number of children | 2          |
            | Category 3 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 3 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Category 2 | is before          | Category 3 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |
            | Board 3    | is child of        | Category 2 |
            | Board 4    | is child of        | Category 1 |
            | Board 5    | is child of        | Category 1 |
            | Board 1    | is before          | Board 4    |
            | Board 4    | is before          | Board 5    |
            | Board 2    | is before          | Board 3    |

    Scenario: Move board down
        When I follow "Categories and Boards"
        Then the returned HTML document should be valid
        And I should see "Forum structure"
        And I should see "Add board"
        And I should see "Add category"
        And I should not see "List categories and boards"
        And I should not see the button "Move category Category 1 up"
        And I should see the button "Move category Category 1 down"
        And I should not see the button "Move board Board 1 up"
        And I should see the button "Move board Board 1 down"
        And I should see the button "Move board Board 4 up"
        And I should see the button "Move board Board 4 down"
        And I should see the button "Move board Board 5 up"
        And I should not see the button "Move board Board 5 down"
        And I should see the button "Move category Category 2 up"
        And I should see the button "Move category Category 2 down"
        And I should not see the button "Move board Board 2 up"
        And I should see the button "Move board Board 2 down"
        And I should see the button "Move board Board 3 up"
        And I should not see the button "Move board Board 3 down"
        And I should see the button "Move category Category 3 up"
        And I should not see the button "Move category Category 3 down"
        When I press "Move board Board 4 down"
        Then the returned HTML document should be valid
        And I should see "Order updated"
        And I should see "Categories and Boards"
        When I follow "List categories and boards"
        Then the returned HTML document should be valid
        And I should see "Forum structure"
        And I should see "Add board"
        And I should see "Add category"
        And I should not see "List categories and boards"
        And the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 3          |
            | Category 1 | number of children | 3          |
            | Category 2 | number of children | 2          |
            | Category 3 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 3 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Category 2 | is before          | Category 3 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |
            | Board 3    | is child of        | Category 2 |
            | Board 4    | is child of        | Category 1 |
            | Board 5    | is child of        | Category 1 |
            | Board 1    | is before          | Board 5    |
            | Board 5    | is before          | Board 4    |
            | Board 2    | is before          | Board 3    |
        And I should not see the button "Move category Category 1 up"
        And I should see the button "Move category Category 1 down"
        And I should not see the button "Move board Board 1 up"
        And I should see the button "Move board Board 1 down"
        And I should see the button "Move board Board 5 up"
        And I should see the button "Move board Board 5 down"
        And I should see the button "Move board Board 4 up"
        And I should not see the button "Move board Board 4 down"
        And I should see the button "Move category Category 2 up"
        And I should see the button "Move category Category 2 down"
        And I should not see the button "Move board Board 2 up"
        And I should see the button "Move board Board 2 down"
        And I should see the button "Move board Board 3 up"
        And I should not see the button "Move board Board 3 down"
        And I should see the button "Move category Category 3 up"
        And I should not see the button "Move category Category 3 down"

    Scenario: Move board up
        When I follow "Categories and Boards"
        Then the returned HTML document should be valid
        And I should see "Forum structure"
        And I should see "Add board"
        And I should see "Add category"
        And I should not see "List categories and boards"
        And I should not see the button "Move category Category 1 up"
        And I should see the button "Move category Category 1 down"
        And I should not see the button "Move board Board 1 up"
        And I should see the button "Move board Board 1 down"
        And I should see the button "Move board Board 4 up"
        And I should see the button "Move board Board 4 down"
        And I should see the button "Move board Board 5 up"
        And I should not see the button "Move board Board 5 down"
        And I should see the button "Move category Category 2 up"
        And I should see the button "Move category Category 2 down"
        And I should not see the button "Move board Board 2 up"
        And I should see the button "Move board Board 2 down"
        And I should see the button "Move board Board 3 up"
        And I should not see the button "Move board Board 3 down"
        And I should see the button "Move category Category 3 up"
        And I should not see the button "Move category Category 3 down"
        When I press "Move board Board 4 up"
        Then the returned HTML document should be valid
        And I should see "Order updated"
        And I should see "Categories and Boards"
        When I follow "List categories and boards"
        Then the returned HTML document should be valid
        And I should see "Forum structure"
        And I should see "Add board"
        And I should see "Add category"
        And I should not see "List categories and boards"
        And the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 3          |
            | Category 1 | number of children | 3          |
            | Category 2 | number of children | 2          |
            | Category 3 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 3 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Category 2 | is before          | Category 3 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |
            | Board 3    | is child of        | Category 2 |
            | Board 4    | is child of        | Category 1 |
            | Board 5    | is child of        | Category 1 |
            | Board 4    | is before          | Board 1    |
            | Board 1    | is before          | Board 5    |
            | Board 2    | is before          | Board 3    |
        And I should not see the button "Move category Category 1 up"
        And I should see the button "Move category Category 1 down"
        And I should not see the button "Move board Board 4 up"
        And I should see the button "Move board Board 4 down"
        And I should see the button "Move board Board 1 up"
        And I should see the button "Move board Board 1 down"
        And I should see the button "Move board Board 5 up"
        And I should not see the button "Move board Board 5 down"
        And I should see the button "Move category Category 2 up"
        And I should see the button "Move category Category 2 down"
        And I should not see the button "Move board Board 2 up"
        And I should see the button "Move board Board 2 down"
        And I should see the button "Move board Board 3 up"
        And I should not see the button "Move board Board 3 down"
        And I should see the button "Move category Category 3 up"
        And I should not see the button "Move category Category 3 down"

    Scenario: Move category down
        When I follow "Categories and Boards"
        Then the returned HTML document should be valid
        And I should see "Forum structure"
        And I should see "Add board"
        And I should see "Add category"
        And I should not see "List categories and boards"
        And I should not see the button "Move category Category 1 up"
        And I should see the button "Move category Category 1 down"
        And I should not see the button "Move board Board 1 up"
        And I should see the button "Move board Board 1 down"
        And I should see the button "Move board Board 4 up"
        And I should see the button "Move board Board 4 down"
        And I should see the button "Move board Board 5 up"
        And I should not see the button "Move board Board 5 down"
        And I should see the button "Move category Category 2 up"
        And I should see the button "Move category Category 2 down"
        And I should not see the button "Move board Board 2 up"
        And I should see the button "Move board Board 2 down"
        And I should see the button "Move board Board 3 up"
        And I should not see the button "Move board Board 3 down"
        And I should see the button "Move category Category 3 up"
        And I should not see the button "Move category Category 3 down"
        When I press "Move category Category 2 down"
        Then the returned HTML document should be valid
        And I should see "Order updated"
        And I should see "Categories and Boards"
        When I follow "List categories and boards"
        Then the returned HTML document should be valid
        And I should see "Forum structure"
        And I should see "Add board"
        And I should see "Add category"
        And I should not see "List categories and boards"
        And the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 3          |
            | Category 1 | number of children | 3          |
            | Category 2 | number of children | 2          |
            | Category 3 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 3 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 3 |
            | Category 3 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |
            | Board 3    | is child of        | Category 2 |
            | Board 4    | is child of        | Category 1 |
            | Board 5    | is child of        | Category 1 |
            | Board 1    | is before          | Board 4    |
            | Board 4    | is before          | Board 5    |
            | Board 2    | is before          | Board 3    |
        And I should not see the button "Move category Category 1 up"
        And I should see the button "Move category Category 1 down"
        And I should not see the button "Move board Board 1 up"
        And I should see the button "Move board Board 1 down"
        And I should see the button "Move board Board 4 up"
        And I should see the button "Move board Board 4 down"
        And I should see the button "Move board Board 5 up"
        And I should not see the button "Move board Board 5 down"
        And I should see the button "Move category Category 3 up"
        And I should see the button "Move category Category 3 down"
        And I should see the button "Move category Category 2 up"
        And I should not see the button "Move category Category 2 down"
        And I should not see the button "Move board Board 2 up"
        And I should see the button "Move board Board 2 down"
        And I should see the button "Move board Board 3 up"
        And I should not see the button "Move board Board 3 down"

    Scenario: Move category up
        When I follow "Categories and Boards"
        Then the returned HTML document should be valid
        And I should see "Forum structure"
        And I should see "Add board"
        And I should see "Add category"
        And I should not see "List categories and boards"
        And I should not see the button "Move category Category 1 up"
        And I should see the button "Move category Category 1 down"
        And I should not see the button "Move board Board 1 up"
        And I should see the button "Move board Board 1 down"
        And I should see the button "Move board Board 4 up"
        And I should see the button "Move board Board 4 down"
        And I should see the button "Move board Board 5 up"
        And I should not see the button "Move board Board 5 down"
        And I should see the button "Move category Category 2 up"
        And I should see the button "Move category Category 2 down"
        And I should not see the button "Move board Board 2 up"
        And I should see the button "Move board Board 2 down"
        And I should see the button "Move board Board 3 up"
        And I should not see the button "Move board Board 3 down"
        And I should see the button "Move category Category 3 up"
        And I should not see the button "Move category Category 3 down"
        When I press "Move category Category 3 up"
        Then the returned HTML document should be valid
        And I should see "Order updated"
        And I should see "Categories and Boards"
        When I follow "List categories and boards"
        Then the returned HTML document should be valid
        And I should see "Forum structure"
        And I should see "Add board"
        And I should see "Add category"
        And I should not see "List categories and boards"
        And the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 3          |
            | Category 1 | number of children | 3          |
            | Category 2 | number of children | 2          |
            | Category 3 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 3 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 3 |
            | Category 3 | is before          | Category 2 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |
            | Board 3    | is child of        | Category 2 |
            | Board 4    | is child of        | Category 1 |
            | Board 5    | is child of        | Category 1 |
            | Board 1    | is before          | Board 4    |
            | Board 4    | is before          | Board 5    |
            | Board 2    | is before          | Board 3    |
        And I should not see the button "Move category Category 1 up"
        And I should see the button "Move category Category 1 down"
        And I should not see the button "Move board Board 1 up"
        And I should see the button "Move board Board 1 down"
        And I should see the button "Move board Board 4 up"
        And I should see the button "Move board Board 4 down"
        And I should see the button "Move board Board 5 up"
        And I should not see the button "Move board Board 5 down"
        And I should see the button "Move category Category 3 up"
        And I should see the button "Move category Category 3 down"
        And I should see the button "Move category Category 2 up"
        And I should not see the button "Move category Category 2 down"
        And I should not see the button "Move board Board 2 up"
        And I should see the button "Move board Board 2 down"
        And I should see the button "Move board Board 3 up"
        And I should not see the button "Move board Board 3 down"


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
