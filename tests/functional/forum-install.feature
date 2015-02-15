# ThWboard -- forum installation feature test
# ===========================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Forum install
    Before an forum instance can be used it requires some inital
    configuration.  This includes the set up of the database, the database
    connection and creating the first user with administrative
    permissions.

    Background:
        Given the forum is not installed

    Scenario Outline: Visit a not yet installed instance of the forum.
        Given I am on "<page>"
        Then I should see "Das Forum ist noch nicht eingerichtet!"

        Examples:
            | page                  |
            | /                     |
            | /admin/               |
            | /board.php            |
            | /calendar.php         |
            | /changeemail.php      |
            | /do_editprofile.php   |
            | /do_login.php         |
            | /do_newcevent.php     |
            | /do_register.php      |
            | /do_send_password.php |
            | /edit.php             |
            | /editprofile.php      |
            | /help.php             |
            | /index.php            |
            | /listavatar.php       |
            | /listthreads.php      |
            | /login.php            |
            | /logout.php           |
            | /memberlist.php       |
            | /misc.php             |
            | /newcevent.php        |
            | /news.php             |
            | /newtopic.php         |
            | /pm.php               |
            | /postops.php          |
            | /qlinks.php           |
            | /rank.php             |
            | /register.php         |
            | /reply.php            |
            | /search.php           |
            | /send_password.php    |
            | /showevent.php        |
            | /showtopic.php        |
            | /stats.php            |
            | /team.php             |
            | /threadops.php        |
            | /thwbnews.php         |
            | /v_profile.php        |

    Scenario: Visit the about page.
        Given I am on the homepage
        When I follow "installieren"
        Then I should see "Welcome to phpInstaller!"
        When I follow "About phpInstaller"
        Then I should see "About phpInstaller"
        And I should see "GPL"
        And I should see "Credits"

    Scenario: Select another language.
        Given I am on the homepage
        When I follow "installieren"
        Then I should see "English" selected in the select "Please select your language:"
        And I should see the following <value> available in the select "Please select your language:":
            | value   |
            | Deutsch |
            | English |
        When I select "Deutsch" from "Please select your language:"
        And I press "Next"
        Then I should see "Willkommen zum ThWboard Installationsassistent."

    Scenario: Reject the license agreement.
        Given I am on the homepage
        When I follow "installieren"
        And I press "Next"
        Then I should see "Welcome to the ThWboard installation assistant."
        When I press "Next"
        Then I should see "License agreement"
        And I should see "GNU GENERAL PUBLIC LICENSE VERSION 2"
        And the checkbox "I accept the license agreement." should be unchecked
        When I press "Next"
        Then I should see "You must agree to the license agreement in order to continue."
        When I follow "Back"
        Then I should see "License agreement"
        And I should see "GNU GENERAL PUBLIC LICENSE VERSION 2"
        And the checkbox "I accept the license agreement." should be unchecked

    Scenario: Enter invalid database credentials.
        Given I am on the homepage
        When I follow "installieren"
        And I press "Next"
        And I press "Next"
        And I check "I accept the license agreement."
        And I press "Next"
        Then I should see "Please provide your MySQL data."
        And the "MySQL hostname/IP" field should contain "localhost"
        And the "MySQL username" field should contain ""
        And the "MySQL password" field should contain ""
        When I press "Next"
        Then I should see "No database credentials were given."
        When I follow "Back"
        Then I should see "Please provide your MySQL data."
        And the "MySQL hostname/IP" field should contain "localhost"
        And the "MySQL username" field should contain ""
        And the "MySQL password" field should contain ""
        When I fill in the following:
            | MySQL username | notexisting |
            | MySQL password | invalid     |
        When I press "Next"
        Then I should see "Invalid credentials were given."
        When I follow "Back"
        Then I should see "Please provide your MySQL data."
        And the "MySQL hostname/IP" field should contain "localhost"
        And the "MySQL username" field should contain "notexisting"
        And the "MySQL password" field should contain ""

    Scenario: Enter invalid database host.
        Given I am on the homepage
        When I follow "installieren"
        And I press "Next"
        And I press "Next"
        And I check "I accept the license agreement."
        And I press "Next"
        Then I should see "Please provide your MySQL data."
        And the "MySQL hostname/IP" field should contain "localhost"
        And the "MySQL username" field should contain ""
        And the "MySQL password" field should contain ""
        When I fill in the following:
            | MySQL hostname/IP |                     |
            | MySQL username    | %DATABASE_USERNAME% |
            | MySQL password    | %DATABASE_PASSWORD% |
        And I press "Next"
        Then I should see "No database host was given."
        When I follow "Back"
        And the "MySQL hostname/IP" field should contain "localhost"
        When I fill in the following:
            | MySQL hostname/IP | notexisting         |
            | MySQL username    | %DATABASE_USERNAME% |
            | MySQL password    | %DATABASE_PASSWORD% |
        And I press "Next"
        Then I should see "Can't connect to the database host."
        When I follow "Back"
        Then I should see "Please provide your MySQL data."
        And the "MySQL hostname/IP" field should contain "notexisting"
        And the "MySQL username" field should contain "%DATABASE_USERNAME%"
        And the "MySQL password" field should contain ""

    Scenario: Create a database with insufficient permissions.
        Given I am on the homepage
        When I follow "installieren"
        And I press "Next"
        And I press "Next"
        And I check "I accept the license agreement."
        And I press "Next"
        When I fill in the following:
            | MySQL hostname/IP | %DATABASE_HOSTNAME% |
            | MySQL username    | %DATABASE_USERNAME% |
            | MySQL password    | %DATABASE_PASSWORD% |
        And I press "Next"
        Then I should see "Select MySQL database"
        And the "mode_db" field should contain "use"
        And I should see the following <value> available in the select "existing database":
            | value           |
            | %DATABASE_NAME% |
        And the "new database" field should contain ""
        When I check the "Create" radio button
        And I fill in "new database" with "anewdatabase"
        And I press "Next"
        Then I should see "The credentials given have no permissions to create a database."
        When I follow "Back"
        Then I should see "Select MySQL database"
        And the "mode_db" field should contain "create"
        And I should see the following <value> available in the select "existing database":
            | value           |
            | %DATABASE_NAME% |
        And the "new database" field should contain "anewdatabase"
        When I fill in "new database" with "an unescaped database name"
        And I press "Next"
        Then I should see "The database name should only consist of lowercase characters, uppercase characters, digits and underscore."
        When I follow "Back"
        Then I should see "Select MySQL database"
        And the "mode_db" field should contain "create"
        And I should see the following <value> available in the select "existing database":
            | value           |
            | %DATABASE_NAME% |
        And the "new database" field should contain "an unescaped database name"

    Scenario: Set an invalid table prefix.
        Given I am on the homepage
        When I follow "installieren"
        And I press "Next"
        And I press "Next"
        And I check "I accept the license agreement."
        And I press "Next"
        When I fill in the following:
            | MySQL hostname/IP | %DATABASE_HOSTNAME% |
            | MySQL username    | %DATABASE_USERNAME% |
            | MySQL password    | %DATABASE_PASSWORD% |
        And I press "Next"
        When I check the "Use" radio button
        And I select "%DATABASE_NAME%" from "existing database"
        And I press "Next"
        Then I should see "Choose MySQL table"
        And I should see "does not contain any tables."
        And the "Table prefix" field should contain "tb_"
        And the checkbox "Overwrite (delete) existing tables" should be unchecked
        When I fill in "Table prefix" with "invalid prefix_"
        And I press "Next"
        Then I should see "The table prefix should only consist lowercase characters, uppercase characters, digits and underscore."
        When I follow "Back"
        Then I should see "Choose MySQL table"
        And I should see "does not contain any tables."
        And the "Table prefix" field should contain "invalid prefix_"
        And the checkbox "Overwrite (delete) existing tables" should be unchecked
        When I check "Overwrite (delete) existing tables"
        And I press "Next"
        Then I should see "The table prefix should only consist lowercase characters, uppercase characters, digits and underscore."
        When I follow "Back"
        Then I should see "Choose MySQL table"
        And I should see "does not contain any tables."
        And the "Table prefix" field should contain "invalid prefix_"
        And the checkbox "Overwrite (delete) existing tables" should be checked

    Scenario: Set an invalid admin password.
        Given I am on the homepage
        When I follow "installieren"
        And I press "Next"
        And I press "Next"
        And I check "I accept the license agreement."
        And I press "Next"
        When I fill in the following:
            | MySQL hostname/IP | %DATABASE_HOSTNAME% |
            | MySQL username    | %DATABASE_USERNAME% |
            | MySQL password    | %DATABASE_PASSWORD% |
        And I press "Next"
        When I check the "Use" radio button
        And I select "%DATABASE_NAME%" from "existing database"
        And I press "Next"
        When I check "Overwrite (delete) existing tables"
        And I press "Next"
        Then I should see "Create administrator profile"
        And the "Username" field should contain "root"
        And the "Email" field should contain ""
        And the "Password" field should contain ""
        When I fill in the following:
            | Username | root           |
            | Email    | root@localhost |
            | Password |                |
        And I press "Next"
        Then I should see "The administrator password is too short (min. 5 chars)!"
        When I follow "Back"
        Then I should see "Create administrator profile"
        And the "Username" field should contain "root"
        And the "Email" field should contain "root@localhost"
        And the "Password" field should contain ""
        When I fill in the following:
            | Username | root           |
            | Email    | root@localhost |
            | Password | root           |
        And I press "Next"
        Then I should see "The administrator password is too short (min. 5 chars)!"

    Scenario: Set an empty admin name.
        Given I am on the homepage
        When I follow "installieren"
        And I press "Next"
        And I press "Next"
        And I check "I accept the license agreement."
        And I press "Next"
        When I fill in the following:
            | MySQL hostname/IP | %DATABASE_HOSTNAME% |
            | MySQL username    | %DATABASE_USERNAME% |
            | MySQL password    | %DATABASE_PASSWORD% |
        And I press "Next"
        When I check the "Use" radio button
        And I select "%DATABASE_NAME%" from "existing database"
        And I press "Next"
        When I check "Overwrite (delete) existing tables"
        And I press "Next"
        Then I should see "Create administrator profile"
        And the "Username" field should contain "root"
        And the "Email" field should contain ""
        And the "Password" field should contain ""
        When I fill in the following:
            | Username |                |
            | Email    | root@localhost |
            | Password | rootroot       |
        And I press "Next"
        Then I should see "The administrator name can't be empty!"
        When I follow "Back"
        Then I should see "Create administrator profile"
        And the "Username" field should contain ""
        And the "Email" field should contain "root@localhost"
        And the "Password" field should contain ""

    Scenario: Successfully finish the installation.
        Given I am on the homepage
        When I follow "installieren"
        And I press "Next"
        And I press "Next"
        And I check "I accept the license agreement."
        And I press "Next"
        When I fill in the following:
            | MySQL hostname/IP | %DATABASE_HOSTNAME% |
            | MySQL username    | %DATABASE_USERNAME% |
            | MySQL password    | %DATABASE_PASSWORD% |
        And I press "Next"
        When I check the "Use" radio button
        And I select "%DATABASE_NAME%" from "existing database"
        And I press "Next"
        When I check "Overwrite (delete) existing tables"
        And I press "Next"
        When I fill in the following:
            | Username | root           |
            | Email    | root@localhost |
            | Password | rootroot       |
        And I press "Next"
        Then I should see "Completing the installation"
        And I should see "Download configuration file"
        When I press "Next"
        Then I should see "Installation completed!"
        When I go to "/"
        Then I should not see "Das Forum ist noch nicht eingerichtet!"

    Scenario: Run the installer on an already installed forum.
        Given the forum is installed
        And I am on "/admin/install.php"
        When I press "Next"
        Then I should see "The forum is already installed!"


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
