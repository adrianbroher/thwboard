# ThWboard -- announcement delete feature test
# ============================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Announcement delete
    Announcements provide uncommentable informations for users.
    Administrator can delete them.

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
        And the following announcements exist:
            | topic               | body                                   | scope   |
            | Local Announcement  | This is an announcement for Board 1    | Board 1 |
            | Global Announcement | This is an announcement for all boards | Board 1;Board 2 |
        And I am on "/admin/"
        When I fill in the following:
            | Username | root |
            | Password | rootroot |
        And I press "Login"
        Then I should see "Announcements"
        When I follow "Announcements"
        Then I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>              | number of children | 2 |
            | Local Announcement  | is child of        | <ROOT> |
            | Global Announcement | is child of        | <ROOT> |
            | Global Announcement | is before          | Local Announcement |

    Scenario: Successfully delete an announcement.
        When I follow "Delete announcement Local Announcement"
        Then I should see "Delete Announcement"
        And I should see "Add announcement"
        And I should see "List announcements"
        And I should see "Do you really want to delete the announcement?"
        When I press "Delete"
        Then I should see "Delete Announcement"
        And I should see "Add announcement"
        And I should see "List announcements"
        And I should see "Announcement has been deleted"
        When I follow "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>              | number of children | 1 |
            | Global Announcement | is child of        | <ROOT> |


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
