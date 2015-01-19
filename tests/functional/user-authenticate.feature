# ThWboard -- user authentication feature test
# ============================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: User authenticate
    Some actions like posting or reading topics are limited be the
    permissions given to a user.  To enable those actions a user need to
    authenticate himself on the board.

    Background:
        Given the forum is installed
        And the following users exist:
            | name     | password         | email              | member of     | flags    |
            | root     | rootroot         | root@localhost     | Admin Group   | isadmin  |
            | regular  | regularregular   | regular@localhost  | Default Group |          |
            | inactive | inactiveinactive | inactive@localhost | Default Group | inactive |

    Scenario: Login without a username.
        Given I am on the homepage
        Then I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked
        When I fill in the following:
            | Benutzername |         |
            | Passwort     | invalid |
        And I press "Einloggen"
        Then I should see "Fehler"
        And I should see "Sie haben vergessen einen Usernamen anzugeben"
        And I should see "Der Angegebene Benutzername existiert nicht"
        Given I am on the homepage
        Then I should be on the homepage
        And I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"

    Scenario: Login without a password.
        Given I am on the homepage
        Then I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked
        When I fill in the following:
            | Benutzername | invalid |
            | Passwort     |         |
        And I press "Einloggen"
        Then I should see "Fehler"
        And I should see "Sie haben vergessen ein Passwort anzugeben"
        And I should see "Der Angegebene Benutzername existiert nicht"
        And I should see "Das Passwort ist leider falsch"
        Given I am on the homepage
        Then I should be on the homepage
        And I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"

    Scenario: Login without an invalid password.
        Given I am on the homepage
        Then I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked
        When I fill in the following:
            | Benutzername | root    |
            | Passwort     | invalid |
        And I press "Einloggen"
        Then I should see "Fehler"
        And I should see "Das Passwort ist leider falsch"
        Given I am on the homepage
        Then I should be on the homepage
        And I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"

    Scenario: Flood login.
        Given I am on the homepage
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked
        When I fill in the following:
            | Benutzername | root    |
            | Passwort     | invalid |
        And I press "Einloggen"
        Then I should see "Fehler"
        And I should see "Das Passwort ist leider falsch"
        Given I am on the homepage
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked
        When I fill in the following:
            | Benutzername | root    |
            | Passwort     | invalid |
        And I press "Einloggen"
        Then I should see "Fehler"
        And I should see "Das Passwort ist leider falsch"
        Given I am on the homepage
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked
        When I fill in the following:
            | Benutzername | root    |
            | Passwort     | invalid |
        And I press "Einloggen"
        Then I should see "Fehler"
        And I should see "Das Passwort ist leider falsch"
        Given I am on the homepage
        When I follow "Einloggen"
        Then I should see "IP wegen 3 fehlerhafter Loginversuche für 15 Minuten gesperrt."

    Scenario: Flood login.
        Given the IP "127.0.0.1" tried to log in 13 minutes ago
        And the IP "127.0.0.1" tried to log in 14 minutes ago
        And the IP "127.0.0.1" tried to log in 14 minutes ago
        And I am on the homepage
        When I follow "Einloggen"
        Then I should see "IP wegen 3 fehlerhafter Loginversuche für 15 Minuten gesperrt."

    Scenario: Flood login.
        Given the IP "127.0.0.1" tried to log in 16 minutes ago
        And the IP "127.0.0.1" tried to log in 15 minutes ago
        And the IP "127.0.0.1" tried to log in 15 minutes ago
        And I am on the homepage
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked

    Scenario: Succesfully login as a regular user.
        Given I am on the homepage
        Then I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked
        When I fill in the following:
            | Benutzername | regular        |
            | Passwort     | regularregular |
        And I press "Einloggen"
        Then I should be on "/index.php"
        And I should see "Willkommen regular!"
        And I should not see "Registrieren"
        And I should not see "Einloggen"
        And I should see "Profil"
        And I should see "Logout"
        And I should see "Private Messages"
        And I should not see "Admincenter"

    Scenario: Succesfully login as a inactive user.
        Given I am on the homepage
        Then I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked
        When I fill in the following:
            | Benutzername | inactive         |
            | Passwort     | inactiveinactive |
        And I press "Einloggen"
        Then I should see "Fehler"
        And I should see "Sie haben ihren Account noch nicht aktiviert"

    Scenario: Successfully login as an administrator.
        Given I am on the homepage
        Then I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"
        When I follow "Einloggen"
        Then I should see "Einloggen"
        And the "Benutzername" field should contain ""
        And the "Passwort" field should contain ""
        And the "Cookies" checkbox should be checked
        When I fill in the following:
            | Benutzername | root     |
            | Passwort     | rootroot |
        And I press "Einloggen"
        Then I should be on "/index.php"
        And I should see "Willkommen root!"
        And I should not see "Registrieren"
        And I should not see "Einloggen"
        And I should see "Profil"
        And I should see "Logout"
        And I should see "Private Messages"
        And I should see "Admincenter"
        When I follow "Logout"
        Then I should be on "/index.php"
        And I should see "Willkommen Gast!"
        And I should see "Registrieren"
        And I should see "Einloggen"
        And I should not see "Profil"
        And I should not see "Logout"
        And I should not see "Private Messages"
        And I should not see "Admincenter"

    Scenario: Login as a regular user without pre login on the administrator panel.
        Given I am on "/admin/"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        When I fill in the following:
            | Username | regular        |
            | Password | regularregular |
        And I press "Login"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        And I should not see "Welcome to the administrative center of your ThWboard."

    Scenario: Login as a regular user with pre login on the administrator panel.
        Given I am on the homepage
        When I follow "Einloggen"
        And I fill in the following:
            | Benutzername | regular        |
            | Passwort     | regularregular |
        And I press "Einloggen"
        Given I am on "/admin/"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        When I fill in the following:
            | Username | regular        |
            | Password | regularregular |
        And I press "Login"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        And I should not see "Welcome to the administrative center of your ThWboard."

    Scenario: Login as an administrator without pre login on the administrator panel.
        Given I am on "/admin/"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Login"
        Then I should see "Welcome to the administrative center of your ThWboard."
        When I follow "Logout"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""

    Scenario: Login as an administrator with pre login on the administrator panel.
        Given I am on the homepage
        When I follow "Einloggen"
        And I fill in the following:
            | Benutzername | root     |
            | Passwort     | rootroot |
        And I press "Einloggen"
        And I follow "Admincenter"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Login"
        Then I should see "Welcome to the administrative center of your ThWboard."
        When I follow "Logout"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""


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
