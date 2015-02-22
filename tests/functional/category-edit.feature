# ThWboard -- category edit feature test
# ======================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Category edit
    Categories are named groups of boards.  Administrator can rename
    existing ones.  Categories names must be unique.

    Background:
        Given the forum is installed
        And the following users exist:
            | name | password | email          | member of   | flags   |
            | root | rootroot | root@localhost | Admin Group | isadmin |
        And the following categories exist:
            | name       |
            | Category 1 |
            | Category 2 |
        And I am on "/admin/"
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Login"
        Then I should see "Categories and Boards"
        When I follow "Categories and Boards"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |

    Scenario: Rename a category with an empty name.
        When I follow "Categories and Boards"
        Then I should see "Category 2"
        And I should see "edit"
        When I follow "Edit category Category 2"
        Then I should see "Edit Category"
        And I should see "Add board"
        And I should see "Add category"
        And I should see "List categories and boards"
        And the "Name" field should contain "Category 2"
        When I fill in "Name" with ""
        And press "Save"
        Then I should see "The category name can't be empty"
        And I should see "Categories and Boards"
        When I follow "List categories and boards"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |

    Scenario: Change category name to an already existing one.
        When I follow "Categories and Boards"
        Then I should see "Category 2"
        And I should see "edit"
        When I follow "Edit category Category 2"
        Then I should see "Edit Category"
        And I should see "Add board"
        And I should see "Add category"
        And I should see "List categories and boards"
        And the "Name" field should contain "Category 2"
        When I fill in "Name" with "Category 1"
        And press "Save"
        Then I should see "The category already exists"
        And I should see "Categories and Boards"
        When I follow "List categories and boards"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | is child of        | <ROOT>     |
            | Category 2 | is child of        | <ROOT>     |
            | Category 1 | is before          | Category 2 |

    Scenario: Successfully edit a category.
        When I follow "Categories and Boards"
        Then I should see "Category 2"
        And I should see "edit"
        When I follow "Edit category Category 2"
        Then I should see "Edit Category"
        And I should see "Add board"
        And I should see "Add category"
        And I should see "List categories and boards"
        And the "Name" field should contain "Category 2"
        When I fill in "Name" with "Category B"
        And press "Save"
        Then I should see "Category saved"
        And I should see "Categories and Boards"
        When I follow "List categories and boards"
        Then the list "board-order" should fulfill the relations:
            | <ROOT>     | number of children | 2          |
            | Category 1 | is child of        | <ROOT>     |
            | Category B | is child of        | <ROOT>     |
            | Category 1 | is before          | Category B |


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
