<?php
/**
 * ThWboard - PHP/MySQL Bulletin Board System
 * ==========================================
 *
 * Copyright (C) 2000-2006 by ThWboard Development Group
 * Copyright (C) 2015 by Marcel Metz
 *
 * This file is part of ThWboard
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program;  If not, see <http://www.gnu.org/licenses/>.
 */

$a_lang = [
    'de' => [],
    'en' => []
];

/* german */
$a_lang['de']['desc'] = 'Deutsch';

$a_lang['de']['next'] = 'Weiter';
$a_lang['de']['back'] = 'Zur&#xFC;ck';
$a_lang['de']['error'] = 'Fehler';
$a_lang['de']['login'] = 'Login';
$a_lang['de']['adminpwtooshort'] = 'Das Administratorpasswort ist zu kurz (min. 5 Zeichen)!';
$a_lang['de']['adminnameempty'] = 'Der Administratorname kann nicht leer sein!';
$a_lang['de']['updatesuccess'] = 'Update erfolgreich';
$a_lang['de']['updatesuccesstxt'] = 'Das Update wurde erfolgreich abgeschlossen.';
$a_lang['de']['notfound'] = 'Das angegeben Script wurde nicht gefunden!';
$a_lang['de']['cantexec'] = 'Das Update kann nicht durchgef&#xFC;hrt werden. (Versionskonflikt)';
$a_lang['de']['chmoderror'] = 'Die MySQL Daten konnten nicht geschrieben werden!<br>Bitte setzen Sie mit Ihrem FTP-Client CHMOD 755, CHMOD 775 oder im Zweifelsfall CHMOD 777 f&#xFC;r das Verzeichnis <b>../inc/</b> und dr&#xFC;cken Sie den Aktualisieren-Button Ihres Browsers. Alternativ k&ouml;nnen Sie die Konfigurationsdatei herunterladen, klicken Sie dazu bitte den Zur&uuml;ck-Button des Browsers und folgen Sie den Anweisungen.';
$a_lang['de']['mysqlerror'] = 'Die MySQL Datenbank "%s" konnte nicht erstellt werden: <b>%s</b>';
$a_lang['de']['connecterror'] = 'Es konnte keine MySQL Verbindung aufgebaut werden, bitte &#xFC;berpr&#xFC;fen Sie die Schreibweise des Benutzers und Passworts.<br>Die MySQL Fehlermeldung lautet: <b>%s</b>';
$a_lang['de']['nodatabasehosterror'] = 'Sie haben keinen Datenbankserver f&uuml;r eine Datenbankverbindung angegeben.';
$a_lang['de']['nocredentialserror'] = 'Sie haben keine Benutzerdaten f&uuml;r eine Datenbankverbindung angegeben.';
$a_lang['de']['wrongcredentialserror'] = 'Sie haben ung&uuml;ltige Benutzerdaten f&uuml;r eine Datenbankverbindung angegeben.';
$a_lang['de']['wrongadmincredentialserror'] = 'Sie haben ung&uuml;ltige Administrator Benutzerdaten angegeben.';
$a_lang['de']['noadmincredentialserror'] = 'Sie haben keine Administrator Benutzerdaten angegeben.';
$a_lang['de']['cannotconnecterror'] = 'Es konnte keine Verbindung zum Datenbankserver aufgebaut werden.';
$a_lang['de']['cantcreatedatabaseerror'] = 'Die genutzte Datenbankauthentifizierung hat keine Berechtigung eine Datenbank zu erstellen.';
$a_lang['de']['invaliddatabasenameerror'] = 'Der Datenbankname kann nur aus Kleinbuchstaben, Gro&szlig;buchstaben, Ziffern und dem Unterstrich bestehen.';
$a_lang['de']['invalidtableprefixerror'] = 'Das Datenbank Prefix kann nur aus Kleinbuchstaben, Gro&szlig;buchstaben, Ziffern und dem Unterstrich bestehen.';
$a_lang['de']['queryerror'] = 'Fehler beim ausf&#xFC;hres des Queries: <i>%s</i><br><br>MySQL Fehler: <b>%s</b>';
$a_lang['de']['installation'] = 'Installation';
$a_lang['de']['licagreement'] = 'Lizenzbedingungen';
$a_lang['de']['licread'] = 'Ich habe die Lizenzbestimmungen gelesen und aktzeptiere sie.';
$a_lang['de']['licaccept'] = 'Sie m&#xFC;ssen den Lizenzbestimmungen zustimmen um fortzufahren.';
$a_lang['de']['mysqldata'] = 'Abfrage der MySQL Daten';
$a_lang['de']['entermysqldata'] = 'Bitte geben Sie ihre MySQL Daten an.';
$a_lang['de']['mysqlhost'] = 'Hostname/IP Adresse';
$a_lang['de']['mysqluser'] = 'Benutzername';
$a_lang['de']['mysqlpass'] = 'Passwort';
$a_lang['de']['selectdb'] = 'Auswahl der MySQL Datenbank';
$a_lang['de']['choosedb'] = 'Bitte w&auml;hlen Sie die gew&uuml;nschte MySQL Datenbank aus, oder geben Sie einen Namen an. Existiert die Datenbank nicht, wird versucht sie zu erstellen.';
$a_lang['de']['use'] = 'Benutze';
$a_lang['de']['existingdb'] = 'existierende Datenbank';
$a_lang['de']['create'] = 'Erzeuge';
$a_lang['de']['newdbname'] = 'neue Datenbank';
$a_lang['de']['chooseprefix'] = 'MySQL Tabellenpr&#xE4;fix ausw&#xE4;hlen';
$a_lang['de']['tablelist'] = 'In der Datenbank <b>"%s"</b> befinden sich zur Zeit folgende Tabellen:';
$a_lang['de']['emptytablelist'] = 'In der Datenbank <b>"%s"</b> befinden sich zur Zeit keine Tabellen.';
$a_lang['de']['enterprefix'] = 'Bitte geben Sie ein Tabellen Prefix an, das vor die Tabellennamen geh&auml;ngt werden soll. Somit sind mehrere ThWboards in einer Tabelle m&ouml;glich.';
$a_lang['de']['tableprefix'] = 'Tabellenprefix';
$a_lang['de']['dontchange'] = 'Im Zweifelsfall nicht &#xE4;ndern';
$a_lang['de']['deleteexisting'] = 'Eventuell existierende Tabellen l&#xF6;schen?';
$a_lang['de']['createadmin'] = 'Administratorprofil erstellen';
$a_lang['de']['username'] = 'Benutzername';
$a_lang['de']['email'] = 'Email Adresse';
$a_lang['de']['password'] = 'Passwort';
$a_lang['de']['completing'] = 'Fertigstellen der Installation';
$a_lang['de']['completingtxt'] = 'Bevor die Installation fertiggestellt werden kann, m&#xFC;ssen die MySQL Daten dauerhaft gespeichert werden. Daf&#xFC;r wird CHMOD 755, CHMOD 775 oder CHMOD 777 f&#xFC;r das Verzeichnis <b>../inc/</b> ben&#xF6;tigt, damit die Konfigurationsdatei erstellt werden kann. Setzen Sie diesen CHMOD mit Ihrem FTP-Client und klicken Sie auf Weiter.<br><br><font color="darkred"><b>Hinweis:</b></font> Alternativ k&ouml;nnen Sie eine automatisch erstellte config.inc.php-Datei herunterladen und diese in das inc/ Verzeichnis des Forums uploaden. Bitte beachten Sie, dass das Forum anschließend vollst&auml;ndig installiert ist, und die Installation an dieser Stelle beendet wird. Der Installationsassistent ist nach dem Upload nicht mehr verf&uuml;gbar.<br><br><a href="%s">Konfigurationsdatei herunterladen</a>';
$a_lang['de']['denied'] = 'Installation verweigert';
$a_lang['de']['deniedtxt'] = 'Das Forum ist bereits installiert! Wenn Sie das Forum neu installieren wollen, l&#xF6;schen bitte die Datei <b>../inc/config.inc.php</b>.';
$a_lang['de']['selectupdate'] = 'Bitte w&#xE4;hlen Sie die Aktualisierung, welche sie ausf&uuml;hren m&ouml;chten, aus.';
$a_lang['de']['availableupdates'] = 'Verf&uuml;gbare Aktualisierungen';
$a_lang['de']['updateinfo'] = 'Update Informationen';
$a_lang['de']['reqver'] = 'Ben&#xF6;tigte Version';
$a_lang['de']['newver'] = 'Version nach Update';
$a_lang['de']['author'] = 'Autor';
$a_lang['de']['date'] = 'Datum';
$a_lang['de']['executable'] = 'Durchf&#xFC;hrbar?';
$a_lang['de']['notes'] = 'Hinweise';
$a_lang['de']['yes'] = 'Ja';
$a_lang['de']['no'] = 'Nein';
$a_lang['de']['na'] = '(keine)';
$a_lang['de']['infotxt'] = '
                    <b>Willkommen!</b><br>
                      <br>
                    <p>
                      Willkommen zum ThWboard Installationsassistent. Dieser Assistent
                      wird Ihnen dabei helfen, Ihr ThWboard zu installieren. Bevor
                      Sie mit der Installation starten, legen Sie bitte folgende
                      Informationen bereit:</p>
                    <ul>
                      <li>MySQL Hostname/IP Adresse</li>
                      <li>MySQL Benutzername und Passwort</li>
                      <li>MySQL Datenbankname (Falls Sie nicht das Recht haben,
                        eine zu erstellen)</li>
                    </ul>
                    Diese Informationen bekommen Sie in der Regel vom Provider
                    zugeschickt. Sollten Sie diese Informationen nicht haben,
                    kontaktieren Sie bitte den Provider/Administrator, da wir
                    diese Informationen nicht haben.<br>
                    <br>
                    Sollten w&auml;hrend der Installation Probleme auftreten,
                    besuchen Sie bitte unser <a href="http://www.thwboard.de/forum/">Support-Forum</a>.';
$a_lang['de']['finished'] = '
<b>Installation beendet!</b><br>
<br>
Sie sollten die Rechte f&uuml;r das Verzeichniss <b>..inc/</b> wieder auf CHMOD 755 setzen.<br>
Beachten Sie, dass das Verzeichnis <b>../templates/css/</b> und alle css-Dateien darin unbedingt auf CHMOD 777 gesetzt sein muss.
<br>
Die Installation wurde erfolgreich beendet. Ihr ThWboard sollte nun voll einsatzbereit sein. Hier noch ein
paar n&#xFC;tzliche Links:<br>
<ul>
<li><a target="_blank" href="../">Ihr ThWboard</a></li>
<li><a target="_blank" href="./index.php">Das Admin-Center Ihres ThWboards</a></li>
<li><a target="_blank" href="http://www.thwboard.de/forum/">Das ThWboard Support Forum</a> (f&#xFC;r Anregungen, Kritik, Lob usw.)</li>
</ul>';
$a_lang['de']['noupdates'] = '
Es sind zur Zeit leider keine Updates verf&#xFC;bar.<br>
Besuchen Sie bitte die <a href="http://www.thwboard.de/">ThWboard Homepage</a> um Updates herunterzuladen.';
$a_lang['de']['installationtooold'] = 'Die aktuelle Installation ist zu alt. Bitte benutzen sie den alten Installer um die Installation auf 2.8 oder eine sp&auml;tere Version zu aktualisieren.';



/* english */
$a_lang['en']['desc'] = 'English';

$a_lang['en']['next'] = 'Next';
$a_lang['en']['back'] = 'Back';
$a_lang['en']['error'] = 'Error';
$a_lang['en']['login'] = 'Login';
$a_lang['en']['adminpwtooshort'] = 'The administrator password is too short (min. 5 chars)!';
$a_lang['en']['adminnameempty'] = 'The administrator name can\'t be empty!';
$a_lang['en']['updatesuccess'] = 'Update successful';
$a_lang['en']['updatesuccesstxt'] = 'The update was completed successfully.';
$a_lang['en']['notfound'] = 'The specified script was not found!';
$a_lang['en']['cantexec'] = 'The update cannot be executed. (Version mismatch)';
$a_lang['en']['chmoderror'] = 'Couldn\'t write MySQL data!<br>Please set CHMOD 755, CHMOD 775 or CHMOD 777 (if unsure, choose CHMOD 777) for the directory <b>../inc/</b> and hit the refresh button. Alternatively you can download a configuration file as described in the previous step. Click the back-button of your browser to do so.';
$a_lang['en']['mysqlerror'] = 'Couldn\'t create MySQL database "%s": <b>%s</b>';
$a_lang['en']['connecterror'] = 'Couldn\'t connect to the MySQL database, please verify username and password.<br>MySQL said: <b>%s</b>';
$a_lang['en']['nodatabasehosterror'] = 'No database host was given.';
$a_lang['en']['nocredentialserror'] = 'No database credentials were given.';
$a_lang['en']['wrongcredentialserror'] = 'Invalid credentials were given.';
$a_lang['en']['wrongadmincredentialserror'] = 'Invalid administrator credentials were given.';
$a_lang['en']['noadmincredentialserror'] = 'No administrator credentials were given.';
$a_lang['en']['cannotconnecterror'] = 'Can\'t connect to the database host.';
$a_lang['en']['cantcreatedatabaseerror'] = 'The credentials given have no permissions to create a database.';
$a_lang['en']['invaliddatabasenameerror'] = 'The database name should only consist of lowercase characters, uppercase characters, digits and underscore.';
$a_lang['en']['invalidtableprefixerror'] = 'The table prefix should only consist lowercase characters, uppercase characters, digits and underscore.';
$a_lang['en']['queryerror'] = 'Query execution error: <i>%s</i><br><br>MySQL said: <b>%s</b>';
$a_lang['en']['installation'] = 'installation';
$a_lang['en']['licagreement'] = 'License agreement';
$a_lang['en']['licread'] = 'I accept the license agreement.';
$a_lang['en']['licaccept'] = 'You must agree to the license agreement in order to continue.';
$a_lang['en']['mysqldata'] = 'MySQL data';
$a_lang['en']['entermysqldata'] = 'Please provide your MySQL data.';
$a_lang['en']['mysqlhost'] = 'Hostname/IP address';
$a_lang['en']['mysqluser'] = 'Username';
$a_lang['en']['mysqlpass'] = 'Password';
$a_lang['en']['selectdb'] = 'Select MySQL database';
$a_lang['en']['choosedb'] = 'Please select the database or enter a name below. If the database does not exists, the script will attempt to create it.';
$a_lang['en']['use'] = 'Use';
$a_lang['en']['existingdb'] = 'existing database';
$a_lang['en']['create'] = 'Create';
$a_lang['en']['newdbname'] = 'new database';
$a_lang['en']['chooseprefix'] = 'Choose MySQL table prefix';
$a_lang['en']['tablelist'] = 'The database <b>"%s"</b> currently contains these tables:';
$a_lang['en']['emptytablelist'] = 'The database <b>"%s"</b> does not contain any tables.';
$a_lang['en']['enterprefix'] = 'Please enter a table prefix which will be prepended to the table names. This allows you to run multiple ThWboards in one database.';
$a_lang['en']['tableprefix'] = 'Table prefix';
$a_lang['en']['dontchange'] = 'Don\'t change if unsure';
$a_lang['en']['deleteexisting'] = 'Overwrite (delete) existing tables';
$a_lang['en']['createadmin'] = 'Create administrator profile';
$a_lang['en']['username'] = 'Username';
$a_lang['en']['email'] = 'Email';
$a_lang['en']['password'] = 'Password';
$a_lang['en']['completing'] = 'Completing the installation';
$a_lang['en']['completingtxt'] = 'Before completing the installation, the MySQL data needs to be saved permanently. In order to create the configuration file the web server requires write access to the directory <b>../inc/</b>. Please set CHMOD 755, CHMOD 775, or, if required, CHMOD 777 for this directory and click next.<br><br><b style="color:#800">Note:</b> Alternatively you may download a generated config.inc.php file and upload it into the inc/ directory. Please note that the forum is fully installed afterwards and this installation assistant will be no longer available after uploading the file.<br><br><a href="%s">Download configuration file</a>';
$a_lang['en']['denied'] = 'Installation denied';
$a_lang['en']['deniedtxt'] = 'The forum is already installed! If you want to re-install the forum, delete the file <b>../inc/config.inc.php</b> and try again.';
$a_lang['en']['selectupdate'] = 'Please select the update you want to run.';
$a_lang['en']['availableupdates'] = 'Available updates';
$a_lang['en']['updateinfo'] = 'Update information';
$a_lang['en']['reqver'] = 'Required version';
$a_lang['en']['newver'] = 'Version after update';
$a_lang['en']['author'] = 'Author';
$a_lang['en']['date'] = 'Date';
$a_lang['en']['executable'] = 'Executable?';
$a_lang['en']['notes'] = 'Notes';
$a_lang['en']['yes'] = 'Yes';
$a_lang['en']['no'] = 'No';
$a_lang['en']['na'] = 'N/A';
$a_lang['en']['infotxt'] = '
                    <b>Welcome!</b><br>
                    <br>
                    <p>
                      Welcome to the ThWboard Installation assistant. This install assistant will
                      guide you through the installation process of your ThWboard. Before you start, make
                      sure you have the following information:</p>
                    <ul>
                      <li>MySQL Hostname/IP address</li>
                      <li>MySQL Username and Password</li>
                      <li>MySQL Database name (If you aren\'t allowed to create one)</li>
                    </ul>
                    That information will be provided by your ISP/Hosting service. If you
                    do not have that information, contact your ISP or administrator since we can\'t help
                    you in that case.
                    <br>
                    <br>
                    If you experience any problems during the installation process, feel free to visit our
                    <a href="http://www.thwboard.de/forum/">Support-Forums</a>.';
$a_lang['en']['finished'] = '
<b>Installation completed!</b><br>
<br>
You should set the <b>../inc/</b> directory to CHMOD 755.<br>
Remember that you need to set the <b>../templates/css/</b> directory and all css files inide to CHMOD 777.
<br>
The installation process was successfully completed. Your ThWboard should be working now.<br>
<ul>
<li><a target="_blank" href="../">Your ThWboard</a></li>
<li><a target="_blank" href="./index.php">The Admin-Center of your ThWboard</a></li>
<li><a target="_blank" href="http://www.thwboard.de/forum/">The ThWboard Support Forums</a> (Comments, questions etc)</li>
</ul>';
$a_lang['en']['noupdates'] = '
There are currently no updates<br>
Visit the <a href="http://www.thwboard.de/">ThWboard Homepage</a> in order to download updates.';
$a_lang['en']['installationtooold'] = 'The current installation is too old. Please use the old installer to upgrade the installation to 2.8 or later.';



function lng($str)
{
    global $a_lang;

    if (!isset($_SESSION['lang'])) {
        $lng = 'en';
    } else {
        $lng = $_SESSION['lang'];
    }

    if ($a_lang[$lng][$str]) {
        return $a_lang[$lng][$str];
    } else {
        return '*** missing string `'.$str.'´';
    }
}
