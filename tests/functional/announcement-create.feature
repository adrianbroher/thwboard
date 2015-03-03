# ThWboard -- announcement creation feature test
# ==============================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Announcement create
    Announcements provide uncommentable informations for users.
    Administrators can create them.  Announcements can be visible
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
        Then the returned HTML document should be valid
        And I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>              | number of children | 2 |
            | Local Announcement  | is child of        | <ROOT> |
            | Global Announcement | is child of        | <ROOT> |
            | Global Announcement | is before          | Local Announcement |

    Scenario: Create an announcement with an empty title.
        When I follow "Add announcement"
        Then the returned HTML document should be valid
        And I should see "New Announcement"
        And I should see "List announcements"
        And I should not see "Add announcement"
        And the "Title" field should contain ""
        And the "Body" field should contain ""
        And I should see nothing selected in the select "Boards"
        And I should see the following <value> available in the select "Boards":
            | value |
            | Board 1 |
            | Board 2 |
        When I fill in the following:
            | Body | This announcement message should never be created |
        And I select "Board 1" from "Boards"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "The announcement title can't be empty"
        And I should not see "Add announcement"
        And I should see "List announcements"
        When I follow "List announcements"
        Then the returned HTML document should be valid
        And I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>              | number of children | 2 |
            | Local Announcement  | is child of        | <ROOT> |
            | Global Announcement | is child of        | <ROOT> |
            | Global Announcement | is before          | Local Announcement |

    Scenario: Create an announcement with an empty body.
        When I follow "Add announcement"
        Then the returned HTML document should be valid
        And I should see "New Announcement"
        And I should see "List announcements"
        And I should not see "Add announcement"
        And the "Title" field should contain ""
        And the "Body" field should contain ""
        And I should see nothing selected in the select "Boards"
        And I should see the following <value> available in the select "Boards":
            | value |
            | Board 1 |
            | Board 2 |
        When I fill in the following:
            | Title | Announcement never created |
        And I select "Board 2" from "Boards"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "The announcement body can't be empty"
        And I should not see "Add announcement"
        And I should see "List announcements"
        When I follow "List announcements"
        Then the returned HTML document should be valid
        And I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>              | number of children | 2 |
            | Local Announcement  | is child of        | <ROOT> |
            | Global Announcement | is child of        | <ROOT> |
            | Global Announcement | is before          | Local Announcement |

    Scenario: Create an announcement with no board selected.
        When I follow "Add announcement"
        Then the returned HTML document should be valid
        And I should see "New Announcement"
        And I should see "List announcements"
        And I should not see "Add announcement"
        And the "Title" field should contain ""
        And the "Body" field should contain ""
        And I should see nothing selected in the select "Boards"
        And I should see the following <value> available in the select "Boards":
            | value |
            | Board 1 |
            | Board 2 |
        When I fill in the following:
            | Title | Announcement never created |
            | Body  | This announcement message should never be created |
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "The announcement needs to visible in at least one board"
        And I should not see "Add announcement"
        And I should see "List announcements"
        When I follow "List announcements"
        Then the returned HTML document should be valid
        And I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>              | number of children | 2 |
            | Local Announcement  | is child of        | <ROOT> |
            | Global Announcement | is child of        | <ROOT> |
            | Global Announcement | is before          | Local Announcement |

    Scenario: Create an announcement with a board selected.
        When I follow "Add announcement"
        Then the returned HTML document should be valid
        And I should see "New Announcement"
        And I should see "List announcements"
        And I should not see "Add announcement"
        And the "Title" field should contain ""
        And the "Body" field should contain ""
        And I should see nothing selected in the select "Boards"
        And I should see the following <value> available in the select "Boards":
            | value |
            | Board 1 |
            | Board 2 |
        When I fill in the following:
            | Title | Another Local Announcement |
            | Body  | This is another local announcement, only for Board 2 |
        And I select "Board 2" from "Boards"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "Announcement saved"
        And I should not see "Add announcement"
        And I should see "List announcements"
        When I follow "List announcements"
        Then the returned HTML document should be valid
        And I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>                     | number of children | 3 |
            | Local Announcement         | is child of        | <ROOT> |
            | Global Announcement        | is child of        | <ROOT> |
            | Another Local Announcement | is child of        | <ROOT> |
            | Global Announcement        | is before          | Local Announcement |
            | Another Local Announcement | is before          | Global Announcement |

    Scenario: Create an announcement with all boards selected.
        When I follow "Add announcement"
        Then the returned HTML document should be valid
        And I should see "New Announcement"
        And I should not see "Add announcement"
        And I should see "List announcements"
        And the "Title" field should contain ""
        And the "Body" field should contain ""
        And I should see nothing selected in the select "Boards"
        And I should see the following <value> available in the select "Boards":
            | value |
            | Board 1 |
            | Board 2 |
        When I fill in the following:
            | Title | Another Global Announcement |
            | Body  | This is another global announcement |
        And I select "Board 1" from "Boards"
        And I additionally select "Board 2" from "Boards"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "Announcement saved"
        And I should not see "Add announcement"
        And I should see "List announcements"
        When I follow "List announcements"
        Then the returned HTML document should be valid
        And I should see "Add announcement"
        And I should not see "List announcements"
        And the list "announcements" should fulfill the relations:
            | <ROOT>                      | number of children | 3 |
            | Local Announcement          | is child of        | <ROOT> |
            | Global Announcement         | is child of        | <ROOT> |
            | Another Global Announcement | is child of        | <ROOT> |
            | Global Announcement         | is before          | Local Announcement |
            | Another Global Announcement | is before          | Global Announcement |


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
