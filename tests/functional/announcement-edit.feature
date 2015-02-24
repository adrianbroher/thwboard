# ThWboard -- announcement edit feature test
# ==========================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Announcement edit
    Announcements provide uncommentable informations for users.
    Administrators can edit them.  Announcements can be visible
    globally within the forum or they can be limited to a subset of
    boards.

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
            | topic               | body                                   | scope           |
            | Local Announcement  | This is an announcement for Board 1    | Board 1         |
            | Global Announcement | This is an announcement for all boards | Board 1;Board 2 |
        And I am on "/admin/"
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Login"
        Then I should see "Announcements"
        When I follow "Announcements"
        Then I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>              | number of children | 2                  |
            | Local Announcement  | is child of        | <ROOT>             |
            | Global Announcement | is child of        | <ROOT>             |
            | Global Announcement | is before          | Local Announcement |

    Scenario: Edit an announcement with an empty title.
        When I follow "Edit announcement Local Announcement"
        Then I should see "Edit Announcement"
        And I should see "List announcements"
        And I should see "Add announcement"
        And the "Title" field should contain "Local Announcement"
        And the "Body" field should contain "This is an announcement for Board 1"
        And I should see "Board 1" selected in the select "Boards"
        And I should see the following <value> available in the select "Boards":
            | value   |
            | Board 1 |
            | Board 2 |
        When I fill in "Title" with ""
        And I press "Save"
        Then I should see "The announcement title can't be empty"
        And I should see "Add announcement"
        And I should see "List announcements"
        When I follow "List announcements"
        Then I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>              | number of children | 2                  |
            | Local Announcement  | is child of        | <ROOT>             |
            | Global Announcement | is child of        | <ROOT>             |
            | Global Announcement | is before          | Local Announcement |

    Scenario: Edit an announcement with an empty body.
        When I follow "Edit announcement Global Announcement"
        Then I should see "Edit Announcement"
        And I should see "List announcements"
        And I should see "Add announcement"
        And the "Title" field should contain "Global Announcement"
        And the "Body" field should contain "This is an announcement for all boards"
        And I should see <value> selected in the select "Boards":
            | value   |
            | Board 1 |
            | Board 2 |
        And I should see the following <value> available in the select "Boards":
            | value   |
            | Board 1 |
            | Board 2 |
        When I fill in "Body" with ""
        And I press "Save"
        Then I should see "The announcement body can't be empty"
        And I should see "Add announcement"
        And I should see "List announcements"
        When I follow "List announcements"
        Then I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>              | number of children | 2                  |
            | Local Announcement  | is child of        | <ROOT>             |
            | Global Announcement | is child of        | <ROOT>             |
            | Global Announcement | is before          | Local Announcement |

    Scenario: Edit an announcement by changing the board selected.
        When I follow "Edit announcement Local Announcement"
        Then I should see "Edit Announcement"
        And I should see "List announcements"
        And I should see "Add announcement"
        And the "Title" field should contain "Local Announcement"
        And the "Body" field should contain "This is an announcement for Board 1"
        And I should see "Board 1" selected in the select "Boards"
        And I should see the following <value> available in the select "Boards":
            | value   |
            | Board 1 |
            | Board 2 |
        When I select "Board 2" from "Boards"
        And I press "Save"
        Then I should see "Announcement saved"
        And I should see "Add announcement"
        And I should see "List announcements"
        When I follow "List announcements"
        Then I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>                     | number of children | 2                  |
            | Local Announcement         | is child of        | <ROOT>             |
            | Global Announcement        | is child of        | <ROOT>             |
            | Global Announcement        | is before          | Local Announcement |

    Scenario: Edit an announcement by selecting all boards.
        When I follow "Edit announcement Local Announcement"
        Then I should see "Edit Announcement"
        And I should see "List announcements"
        And I should see "Add announcement"
        And the "Title" field should contain "Local Announcement"
        And the "Body" field should contain "This is an announcement for Board 1"
        And I should see "Board 1" selected in the select "Boards"
        And I should see the following <value> available in the select "Boards":
            | value   |
            | Board 1 |
            | Board 2 |
        When I select "Board 1" from "Boards"
        And I additionally select "Board 2" from "Boards"
        And I press "Save"
        Then I should see "Announcement saved"
        And I should see "Add announcement"
        And I should see "List announcements"
        When I follow "List announcements"
        Then I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>                      | number of children | 2                  |
            | Local Announcement          | is child of        | <ROOT>             |
            | Global Announcement         | is child of        | <ROOT>             |
            | Global Announcement         | is before          | Local Announcement |


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
