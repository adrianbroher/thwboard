# ThWboard -- user creation feature test
# ======================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: User create
    Users are persons, who are allowed to use the forum after
    registering themselfs.  Administrators can create users without
    going through the registration process.
    When creating a user the user needs to have a unique name within
    the board, a valid email address by syntax and a password, which was
    repeated correctly to avoid typing errors.

    Background:
        Given the forum is installed
        And the following users exist:
            | name | password | email          | member of   | flags   |
            | root | rootroot | root@localhost | Admin Group | isadmin |
        And I am on "/admin/"
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Login"
        Then I should see "Add user"

    Scenario: Create a user with an empty name.
        When I follow "Add user"
        Then the returned HTML document should be valid
        And I should see "New User"
        And the "Name" field should contain ""
        And the "Email" field should contain ""
        And the "Password" field should contain ""
        And the "Verify password" field should contain ""
        When I fill in "Name" with ""
        And I fill in "Email" with "invalid@localhost"
        And I fill in "Password" with "invalidinvalid"
        And I fill in "Verify password" with "invalidinvalid"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "The user name can't be empty"
        And I should see "List users"
        When I follow "List users"
        And I press "Search"
        Then the returned HTML document should be valid
        And the table "users" should contain:
            | | User ID | Username | Email address  | Options        |
            | | 1       | root     | root@localhost | edit \| delete |

    Scenario: Create a duplicate user name.
        When I follow "Add user"
        Then the returned HTML document should be valid
        And I should see "New User"
        And the "Name" field should contain ""
        And the "Email" field should contain ""
        And the "Password" field should contain ""
        And the "Verify password" field should contain ""
        When I fill in "Name" with "root"
        And I fill in "Email" with "invalid@localhost"
        And I fill in "Password" with "invalidinvalid"
        And I fill in "Verify password" with "invalidinvalid"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "The user name already exists"
        And I should see "List users"
        When I follow "List users"
        And I press "Search"
        Then the returned HTML document should be valid
        And the table "users" should contain:
            | | User ID | Username | Email address  | Options        |
            | | 1       | root     | root@localhost | edit \| delete |

    Scenario: Create a user with an empty email.
        When I follow "Add user"
        Then the returned HTML document should be valid
        And I should see "New User"
        And the "Name" field should contain ""
        And the "Email" field should contain ""
        And the "Password" field should contain ""
        And the "Verify password" field should contain ""
        When I fill in "Name" with "invalid"
        And I fill in "Email" with ""
        And I fill in "Password" with "invalidinvalid"
        And I fill in "Verify password" with "invalidinvalid"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "The user email can't be empty"
        And I should see "List users"
        When I follow "List users"
        And I press "Search"
        Then the returned HTML document should be valid
        And the table "users" should contain:
            | | User ID | Username | Email address  | Options        |
            | | 1       | root     | root@localhost | edit \| delete |

    Scenario: Create a duplicate user email.
        When I follow "Add user"
        Then the returned HTML document should be valid
        And I should see "New User"
        And the "Name" field should contain ""
        And the "Email" field should contain ""
        And the "Password" field should contain ""
        And the "Verify password" field should contain ""
        When I fill in "Name" with "invalid"
        And I fill in "Email" with "root@localhost"
        And I fill in "Password" with "invalidinvalid"
        And I fill in "Verify password" with "invalidinvalid"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "The user email is already registered"
        And I should see "List users"
        When I follow "List users"
        And I press "Search"
        Then the returned HTML document should be valid
        And the table "users" should contain:
            | | User ID | Username | Email address  | Options        |
            | | 1       | root     | root@localhost | edit \| delete |

    Scenario: Create a user with no password.
        When I follow "Add user"
        Then the returned HTML document should be valid
        And I should see "New User"
        And the "Name" field should contain ""
        And the "Email" field should contain ""
        And the "Password" field should contain ""
        And the "Verify password" field should contain ""
        When I fill in "Name" with "invalid"
        And I fill in "Email" with "invalid@localhost"
        And I fill in "Password" with ""
        And I fill in "Verify password" with ""
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "The user passwords can't be empty"
        And I should see "List users"
        When I follow "List users"
        And I press "Search"
        Then the returned HTML document should be valid
        And the table "users" should contain:
            | | User ID | Username | Email address  | Options        |
            | | 1       | root     | root@localhost | edit \| delete |

    Scenario: Create a user with a mismatching password.
        When I follow "Add user"
        Then the returned HTML document should be valid
        And I should see "New User"
        And the "Name" field should contain ""
        And the "Email" field should contain ""
        And the "Password" field should contain ""
        And the "Verify password" field should contain ""
        When I fill in "Name" with "regular"
        And I fill in "Email" with "regular@localhost"
        And I fill in "Password" with "onepassword"
        And I fill in "Verify password" with "anotherpassword"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "The given passwords did not match"
        And I should see "List users"
        When I follow "List users"
        And I press "Search"
        Then the returned HTML document should be valid
        And the table "users" should contain:
            | | User ID | Username | Email address  | Options        |
            | | 1       | root     | root@localhost | edit \| delete |

    Scenario: Successfully create an user.
        When I follow "Add user"
        Then the returned HTML document should be valid
        And I should see "New User"
        And the "Name" field should contain ""
        And the "Email" field should contain ""
        And the "Password" field should contain ""
        And the "Verify password" field should contain ""
        When I fill in "Name" with "regular"
        And I fill in "Email" with "regular@localhost"
        And I fill in "Password" with "regularregular"
        And I fill in "Verify password" with "regularregular"
        And I press "Save"
        Then the returned HTML document should be valid
        And I should see "User saved"
        And I should see "List users"
        When I follow "List users"
        And I press "Search"
        Then the returned HTML document should be valid
        And the table "users" should contain:
            | | User ID | Username | Email address     | Options        |
            | | 1       | root     | root@localhost    | edit \| delete |
            | | 2       | regular  | regular@localhost | edit \| delete |


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
