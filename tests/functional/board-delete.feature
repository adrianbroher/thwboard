# ThWboard -- board delete feature test
# =====================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Board delete
    Categories are named groups of boards.  Administrator can delete those
    if they don't contain any board.

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
        And I am on "/admin/"
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Login"
        Then I should see "Categories and Boards"
        When I follow "Categories and Boards"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 3          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 1          |
            | Category 3 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 3 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Category 2 | is before          | Category 3 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is child of        | Category 2 |

    Scenario: Successfully delete a board.
        When I follow "Categories and Boards"
        Then I should see "Board 2"
        And I should see "delete"
        When I follow "Delete board Board 2"
        Then I should see "Delete Board"
        And I should see "Add board"
        And I should see "Add category"
        And I should see "List categories and boards"
        And I should see "Do you really want to delete the board?"
        When I press "Delete"
        Then I should see "Board has been deleted"
        When I follow "List categories and boards"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 3          |
            | Category 1 | number of children | 1          |
            | Category 2 | number of children | 0          |
            | Category 3 | number of children | 0          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 3 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |
            | Category 2 | is before          | Category 3 |
            | Board 1    | is child of        | Category 1 |
            | Board 2    | is not child of    | Category 2 |


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
