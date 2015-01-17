# ThWboard -- forum update feature test
# =====================================
#
# Copyright (C) 2015 by Marcel Metz
#
# See the end of the file for license conditions.

Feature: Forum update
    When having a forum instance installed the user needs to update the
    database schema when updating the forum instance.

    Background:
        Given the forum is installed
        And the forum is downgraded to 2.84
        And the following users exist:
            | name | password | email          | member of   | flags   |
            | root | rootroot | root@localhost | Admin Group | isadmin |

    Scenario: Enter invalid database credentials.
        Given I am on "/admin/update.php"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        And I should see "English" selected in the select "Language"
        And I should see the following <value> available in the select "Language":
            | value   |
            | Deutsch |
            | English |
        And I press "Next"
        Then I should see "No administrator credentials were given."
        When I follow "Back"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        And I should see "English" selected in the select "Language"
        And I should see the following <value> available in the select "Language":
            | value   |
            | Deutsch |
            | English |
        When I fill in the following:
            | Username | invalid |
            | Password | invalid |
        And I press "Next"
        Then I should see "Invalid administrator credentials were given."

    Scenario: Select another language.
        Given I am on "/admin/update.php"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        And I should see "English" selected in the select "Language"
        And I should see the following <value> available in the select "Language":
            | value   |
            | Deutsch |
            | English |
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I select "Deutsch" from "Language"
        And I press "Next"
        Then I should see "Bitte wählen Sie die Aktualisierung, welche sie ausführen möchten, aus."

    Scenario: Select an already applied update.
        Given I am on "/admin/update.php"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        And I should see "English" selected in the select "Language"
        And I should see the following <value> available in the select "Language":
            | value   |
            | Deutsch |
            | English |
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Next"
        Then I should see "Updates"
        And I should see "Please select the update you want to run."
        And I should see the following <value> available in the select "Available updates":
            | value                      |
            | stats_history.update       |
            | thwb_26b_27.update         |
            | thwb_27_271.update         |
            | thwb_28_281.update         |
            | thwb_271_272.update        |
            | thwb_272_273.update        |
            | thwb_273_28.update         |
            | thwb_281_282.update        |
            | thwb_282_283.update        |
            | thwb_283_284.update        |
            | thwb_284_285.update        |
            | thwb_lastvisitedfix.update |
            | thwb_quotes_fix.update     |
        When I select "thwb_283_284.update" from "Available updates"
        And I press "Next"
        Then I should see "Update information"
        And I should see "Required version 2.83"
        And I should see "Version after update 2.84"
        And I should see "Author ThWboard Development Team"
        And I should see "Date $Date: 2004-11-07 01:19:15 +0100 (Sun, 07 Nov 2004) $"
        And I should see "Executable? No"
        And I should see "Notes N/A"
        When I press "Next"
        Then I should see "The update cannot be executed. (Version mismatch)"

    Scenario: Successfully finish the update.
        Given I am on "/admin/update.php"
        Then I should see "Login"
        And the "Username" field should contain ""
        And the "Password" field should contain ""
        And I should see "English" selected in the select "Language"
        And I should see the following <value> available in the select "Language":
            | value   |
            | Deutsch |
            | English |
        When I fill in the following:
            | Username | root     |
            | Password | rootroot |
        And I press "Next"
        Then I should see "Updates"
        And I should see "Please select the update you want to run."
        And I should see the following <value> available in the select "Available updates":
            | value                      |
            | stats_history.update       |
            | thwb_26b_27.update         |
            | thwb_27_271.update         |
            | thwb_28_281.update         |
            | thwb_271_272.update        |
            | thwb_272_273.update        |
            | thwb_273_28.update         |
            | thwb_281_282.update        |
            | thwb_282_283.update        |
            | thwb_283_284.update        |
            | thwb_284_285.update        |
            | thwb_lastvisitedfix.update |
            | thwb_quotes_fix.update     |
        When I select "thwb_284_285.update" from "Available updates"
        And I press "Next"
        Then I should see "Update information"
        And I should see "Required version 2.84"
        And I should see "Version after update 2.85"
        And I should see "Author ThWboard Development Team"
        And I should see "Date $Date: 2004-11-07 01:19:15 +0100 (Sun, 07 Nov 2004) $"
        And I should see "Executable? Yes"
        And I should see "Notes N/A"
        When I press "Next"
        Then I should see "Update successful"


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
