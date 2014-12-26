$Id: changelog 75 2004-10-29 19:24:22Z dp $

Beta 2.85
========

Bug: Bilder in PMs werden wieder geparsed. (theDon)
Bug: Diverse Notices (theDon)
Bug: unter Umständen wurden noch gültige Sessions gelöscht. (theDon)
Bug: Suche kann wieder auf einzelne Boards eingegrenzt werden. (theDon)
Bug: Suche: URL für Seitenwechsel war unter Umständen zu lang. (theDon)
Bug: ThWB-Tags wurden in der Faq / Formatierungsmöglichkeiten falsch dargestellt. (theDon)
Bug: Postings werden nicht länger nach foruminternen Links abgeschnitten (Paul)
Bug: Redundante Einträge in der Online-Tabelle sind jetzt Vergangenheit. (theDon)
Bug: Such-Highlight funktionierte teilweise nicht korrekt. (theDon)
Bug: Guest-Flood-Protection griff nicht bei neuen Themen. (theDon)
Bug: Suche: Seitenlinks waren teilweise zu lang. (theDon)
Bug: Umlaute in Boardnamen & Beschreibung. (theDon)
Bug: Diverse kleine Bugs im Zusammenhang mit URLs und Bildern. (theDon)
Bug: Aktivierungsemails wurden bei AOL falsch dargestellt. (theDon)
Bug: Beim Löschen des ersten Posts eines Threads wurde der Threadautor nicht aktualisiert. (theDon)

Änderung: ``Passwort vergessen'' jetzt mit Aktivierungslink. (theDon)
Änderung: In der Memberliste werden bei der Auswahl eines Anfangsbuchstabens die User in alphabetischer Reihenfolge angezeigt. (theDon)
Änderung: Wenn die eigene Email-Adresse versteckt ist, wird dies gekennzeichnet. (theDon)
Änderung: Session-Handling komplett überarbeitet. (theDon)
Änderung: Install-Script benutzt $HTTP_POST_VARS. (theDon)
Änderung: GZip-Kompression läuft jetzt über Output-Buffering. (theDon)
Änderung: Session-Rewrite. (theDon)
Änderung: Message-Amount - Beschreibung missverständlich. (theDon)
Änderung: Englische Bezeichnungen aus der Boardübersicht entfernt. (theDon)
Änderung: Wenn url_fopen_wrappers aus ist, wird die Avatargröße nicht mehr überprüft. (ermöglicht externe Avatare bei neueren PHP-Versionen, weil da url_fopen_wrappers standardmäßig aus ist.) (theDon)

Neu: RSS-Feed (gleichzeit als neues newsscript einsetzbar) (theDon / Luki)
Neu: Newsletter: E-Mail-Adressen-Export als Liste. (theDon / Luki)
Neu: Hinweis, wenn mehr als 95% des Platzes für PMs verbraucht ist. (theDon)
Neu: Verbesserte Flood-Protection. (theDon)
Neu: Threads verschmelzen. (theDon)
Neu: Erweiterter Error-Report-Modus. (Luki / theDon)
Neu: Übersicht über alle Themen, für die eine Email-Benachrichtigung erfolgt. Dies behebt auch den Bug, dass Benachrichtigungen nicht wieder abbestellt werden konnten. (theDon)
Neu: ``Beiträge seit letztem Besuch''. (Luki / theDon)
Neu: Beim MySQL-Cleaner im Admincenter kann man jetzt auch nach Views und nicht aktivierten Usern filtern. (theDon)
Neu: Beim MySQL-Cleaner wird eine Liste mit den zu löschenden Usern / Threads angezeigt, bei der bestimmte User / Threads abgewählt werden können. (theDon)
Neu: Beim Versuch sich einzuloggen erscheint ein Fehler, wenn man bereits eingeloggt ist. (theDon)
Neu: Neue Permission: `User kann PM senden, obwohl die Inbox des Empfängers voll ist.' (theDon / Dominik Hahn (MrNase))
Neu: CSS-Files werden jetzt auch geschrieben, wenn man einen Style als Standard einsetzt. (theDon)
Neu: [img] URI [/img] wird jetzt auch als Bild erkannt. (theDon)
Neu: Session-IDs werden auch aus PMs entfernt. (theDon)
Neu: PHP5-Kompatibilität. (theDon)
Neu: Email-Adressenänderung mit Aktivierungsemail. (theDon)

Beta 2.84
========

Bug: Beim Löschen von Posts / Threads wurde falsch gezählt. (theDon)
Bug: Unnötiges &highlight= bei der Suche. (theDon)
Bug: Newsletter konnte unter Umständen nicht verschickt werden. (theDon)
Bug: Quicklinks können jetzt auch ohne http:// beginnen. (theDon)	
Bug: Session-ID wurde unter Umständen nicht korrekt weitergegeben. (theDon)
Bug: Keine Zeilenumbrüche in PMs. (theDon)
Bug: XSS in board.php. (theDon)
Bug: `0-9' In der Memberlist funktionierte nicht. (theDon)
Bug: Es wurde bei voller PM-Box keine PM-Kopie im Postausgang gespeichert, wenn der User `Kein PM-Limit' hatte. (theDon)
Bug: Threads konntent nicht gelöscht oder verschoben werden. (theDon)

Änderung: Es werden nur noch die ersten 24 Bits der IP verglichen, dies sollte Probleme mit AOL beheben. (theDon)
Änderung: ThWBCode im Kalender möglich. (theDon)
Änderung: Bei IP-Mismatches wird, falls ein gültiges cookie vorhanden ist, kein Fehler angezeigt. (theDon)
Änderung: Die Zeit für session timeouts ist jetzt einstellbar. (theDon)
Änderung: Templates: Optimierungen. (Dominik Hahn (MrNase))
Änderung: CSS werden ausgelagert. (theDon)
Änderung: User mit ThWB-Code-Tags im Namen können sich nicht registrieren. (theDon)

Neu: Im MySQL-Cleaner können jetzt die Post- und Threadcounts für die Boards neu gezählt werden. (theDon)
Neu: Im MySQL-Cleaner können jezt die Tabellen optimiert werden. (theDon, Hack von MrNase)
Neu: In Posts werden Session-IDs  aus Boardlinks entfernt. (theDon)
Neu: Kategorien in der Boardübersicht anklickbar. (theDon)
Neu: In Threads kann direkt zum Seitenanfang/-ende gesprungen werden. (theDon)

Beta 2.83
========

Bug: Alle User wurden auf der Teampage aufgeführt. (theDon)
Bug: Es konnten keine Gruppenrechte für bestimmte Boards festgelegt werden. (theDon)
Bug: diverse Notices und andere Fehler. (theDon)

Änderung: PHP-Fehler werden nur noch für Admins angezeigt. (theDon)
Änderung: Die IP-Überprüfung bei den Sessions ist jetzt für bestimmte User abschaltbar. Dies kann für den jeweiligen User ein Sicherheitsrisiko bedeuten, behebt jedoch Probleme mit AOL. (theDon)	

Beta 2.82
========
	
      Neu: Die Memberliste lässt sich jetzt direkt durchsuchen und man kann zu einem bestimmten Buchstaben springen. (theDon)
      Neu: Als Alternative zum Cookie kann man auch wahlweise sessions benutzen. (theDon)

      Bug: Announcements nicht lesbar nach Board-"Jump"
      Bug: [quote] case-sensitive (Sebastian)
      Bug: Uppercase-Protection fixed (Sebastian)
      Bug: Bug beim erstmaligen Einloggen behoben (Sebastian)
      Bug: Regexp exploit bei der Suche (Paul)
      Bug: Interne Foren nicht länger durchsuchbar (Paul)
      Bug: #4168 gefixt (Paul)
      Bug: HTML im Profil (Superhausi/Sebastian)
      Bug: Avatar-Sicherheitsluecke (Superhausi)
      Bug: Avatar "notallowed" (Superhausi)
      Bug: #3917 - Topic Badwords / leeres Topic (Superhausi)
      Bug: diverse GET/POST HTML-Injections entfernt (Superhausi)
      Bug: PMs bei Antwort/Zitat/Weiterleitung gefixt (Superhausi/Sebastian)
      Bug: Quicklinks und register_globals (Superhausi/Sebastian)
      Bug: SQL-Injection im Admin-Center (Superhausi)
      Bug: SQL-Injection in den Announcements (Paul)
      Bug: event in admin/calendar.php und showevent.php ermöglicht SQL-Injection (theDon)
      Bug: source in do_login.php ermöglicht HTML-Injection (Tendor)
      Bug: MySQL-User-Cleanup funktioniert wieder (Sebastian)
      Bug: Altersanzeige im Profil war falsch (Sebastian)
      Bug: Cookie-Problem nach E-Mail-Adressen-Aenderung (Sebastian)
      Bug: Weiterleitung bei Link-Thread (Sebastian)
      Bug: manche E-Mail-Adressen wurden nicht akzeptiert, ist hoffentlich jetzt besser (Sebastian)
      Bug: Diverse XSS Bugs wurden behoben (Paul/Sebastian)
      Bug: boardlastthread wurde nicht geparst (Sebastian)
      Bug: #4590 - AIM/MSG wurden nicht escaped (Paul)
      Bug: #4594 - Homepage im Profil wurde zerstückelt (Paul)
      Bug: #4596/#4587 - PM schicken geht wieder (Paul)
      Bug: URLs nicht geparst nach [/quote] etc. (Sebastian)
      Bug: Diverse XSS fixes #4604, #4605 (Paul)
      Bug: time in board.php ermöglicht XSS (theDon)
      Bug: " Im Homepage-Feld (Profil) ermöglicht XSS (theDon)
      Bug: Deaktivierte Events werden im Kalender nicht mehr angezeigt (theDon)
      Bug: [img]-Tags im Profil u. in PMs werden jetzt abhängig vom Imageslevel geparsed (theDon)
      Bug: Style-Upload im Admincenter geht wieder (theDon)
      Bug: Die Zeile mit den Wochentagen im Kalender wurde mit bestimmten Styles nicht korrekt angezeigt. (theDon)
      Bug: Im Kalender konnte HTML-Code eingegeben werden. (theDon)
      Bug: Die Suche highlighted jetzt nicht mehr innerhalb von HTML-Tags. (theDon, Fix von Tendor)
      Bug: Usernamen mit ThWb-Tags führten zu Fehlern beim Zitieren (Sebastian)
      Bug: Badwords-Protection im Admincenter verwendete teilweise deutsche Wörter. (theDon)
      Bug: Beim Verschicken eines Newsletters konnte es bei großen Foren zu einem timeout kommen. (theDon)
      Bug: XSS-Vulnerability bei Bildern.(theDon)
      Bug: Administratoren konnten bei showpostlevel != 2 in der Memberlist keine Postcounts sehen. (theDon)
      Bug: Aktive User wurden in der Statistik falsch gezählt. (theDon)
      Bug: Kalendereinträge wurden evtl um einige Tage verschoben angezeigt. (theDon)
      Bug: Im Kalender wurde unter Umständen das aktuelle Datum nicht markiert. (theDon)
      Bug: Unicode-Sonderzeichen werden jetzt korrekt behandelt. (theDon)
      Bug: Leere Posts sollten endgültig beseitigt sein. (theDon)
      Bug: Diverse XSS-Fixes. (theDon, Fixes von Tendor)
      Bug: $P wurde nicht immer initialisiert. (theDon)
      Bug: updateboard() trägt unter Umständen einen threadlink als boardlastpost ein. (theDon)

 Änderung: Beim Registrieren wird jetzt eine Mail mit Aktivierungslink verschickt. (theDon, Hack von MrNase)
 Änderung: Das Board läuft jetzt fehlerfrei mit error_reporting(E_ALL). (theDon)
 Änderung: Prinzipiell unbegrenzt viele Gruppenrechte möglich, standartmäßig bis zu 50. (theDon/Daniel)
 Änderung: Die Scripts und Icons unter extras/ sind jetzt tar.(gz|bz2) - komprimiert. Weiterhin gibt es jetzt auch eine tar.bz2-Version. (theDon)
 Änderung: Die Verzeichnisse bei der Versionsinformation im Admincenter sind jetzt relativ zum Boardpfad, nicht zum Admincenter. (theDon)
 Änderung: In der Boardübersicht bekommt man für den zuletzt geänderten thread ein hover-info mit dem vollen Namen. (theDon, Fix von Luki)
 Änderung: Das Versenden eines Newsletters wird zur Entlastung des Servers hin und wieder mit sleep() unterbrochen. (theDon)
 Änderung: URL-Parsing verbessert. (theDon, Fix von Tendor)
 Änderung: <script language> in <script type> geändert (theDon)
 Änderung: statt mysql_create_db() wird jetzt `CREATE DATABASE' verwendent (MySQL-4 Kompatibilität) (theDon)
 Änderung: Die Zeile mit den Wochentagen im Kalender hat jetzt nicht mehr die Rahmenfarbe. (theDon)
 Änderung: keine Smilies mehr bei closed-message (Sebastian)
 Änderung: URLs mit Kommas und Protokolle mit Zahlen gehen jetzt (Sebastian)
 Änderung: Aendert man das Topic eines verlinkten Threads, aendert sich auch das Topic des Links (Sebastian)
 Änderung: userinterests auf tinytext begrenzt (Sebastian)
 Änderung: MySQL-Thread-Cleanup loescht auch Threadlinks und Posts (Sebastian)
 Änderung: edittimeout gilt auch fuer das Loeschen von Posts (Sebastian)
 Änderung: Bei Registrierung, Login und Posten eines Gastes wird jetzt ein Hinweis auf bestehendes Datenschutzrecht angezeigt und die Erlaubnis
           zur Speicherung der IP-Addresse bei der Registrierung abgefragt (Daniel)

Beta 2.81
=========

      Neu: User können Kalender-Events hinzufügen (Superhausi)
      Neu: Installation jetzt auch ohne Schreibrecht möglich (Paul)
      Neu: Entfernen von Thread-Links möglich (Paul)
      Neu: In Templates ist jetzt auch das Ausführen von PHP-Code möglich,
           Tutorial dazu bald verfügbar (Daniel)

      Bug: Beim ändern des PW im Profil wurde man ausgeloggt - fixed (Daniel)
      Bug: Beim ändern der Email-Adresse im Profil wird Fehler ausgegeben - fixed (Daniel)
      Bug: Hilfeverweis beim editieren war falsch verlinkt (Daniel)
      Bug: Codetag und Color-Tag korrigiert (Daniel)
      Bug: Register-global-fix (Daniel)
      Bug: Keine Fehler mehr bei geschlossenem Forum (Paul)
      Bug: Lizenzproblem bei Installation behoben (Daniel, Paul)
      Bug: http:// Anzeige bei leerer HP-Zeile im Profil (Daniel)
      Bug: Zeilenumbruch-Bug im Admin-Template-Editor (Daniel)
      Bug: [ im Zitat oder Code löst Design-Probleme aus (Daniel)
      Bug: post < minlength oder > maxlength möglich (Daniel)
      Bug: kleine Fehler in showtopic.php (Sebastian)
	  Bug: Kalender Fehler bei Daten < 1970 (Paul)
	
 Änderung: Navpath bei Login-Fehlermeldungen hinzugefügt (Daniel)
 Änderung: Diverse Rechtschreibfehler korrigiert (Paul)
 Änderung: Codeparsing fixes, [noparse] tag eingeführt, parsing related Kram ausgelagert (Paul)
 Änderung: ThWbCode - Smilies, Codes, [quote]-fixes, pregs erweitert (Sebastian)
 Änderung: Bei einigen Funktionen fehlenden navpath hinzugefügt (Daniel)
 Änderung: Userhomepage im Profil öffnet sich nun in einem neuen Browser-Fenster (Daniel)
 Änderung: Smilie/Code arrays ausgelagert (Paul)
 Änderung: Bei Post-Fehlern Navpath hinzugefügt (Daniel)
 Änderung: E-Mail-Adressen zum Schutz vor E-Mail-Grabbern nur noch für eingeloggte User sichtbar (Sebastian)
 Änderung: Uppercase Topic Protection verbessert (Paul)

Beta 2.8
=========

      Neu: Avatar hack integriert (Morpheus, Hack von Andy)
      Neu: Stark erweiterte Endbenutzer-Hilfe innerhalb des Forums (Paul, Hack von Jonas)
      Neu: Rank Übersicht und Statistik unter Hilfe/Ränge (Paul, Hack von Hallenbeck)
      Neu: Thread-erstell Zeit wird seperat gespeichert [Newshack] (Paul)
      Neu: Mysql-Cleaner inkl Statistiken des Forums und der Mysql-DB (Morpheus)
      Neu: Auto Close und Autodelete ( Morpheus )
      Neu: PHP-Hightlightning [php] ~ [/php] ( Morpheus )
      Neu: [mail][/mail] (Paul)
      Neu: Installer multilingual, PHP 4.2 kompatibel (Paul)
      Neu: News Script (News aus dem Forum auf einer anderen Seite) (Paul)
      Neu: Smilies können nun auch in den PMs genutzt werden falls im Admincenter eingeschaltet (Morpheus)
      Neu: PMs können nun auch als E-Mails versendet werden (Morpheus)	
      Neu: (Admin-)Benutzersuche stark überarbeitet: Mehrere Kriterien verwendbar (Paul)
      Neu: Benutzerprofil im Admincenter aktualisiert (Morpheus)
      Neu: Eingabefeld-Fokus auf einigen Seiten (Paul)
      Neu: Gzip Kompression einstellbar im Admincenter (Paul)
      Neu: Einige neue Gruppenrechte (Paul)
      Neu: Badwords protection (Daniel)
      Neu: Erster Admin kann nicht gelöscht / degradiert werden (Superhausi)
      Neu: Nach dem Login wird man zur vorherigen Seite weitergeleitet (Paul)
      Neu: Gäste haben jetzt eine eigene Gruppe (Paul)
      Neu: Suchwörter werden jetzt im Ergebis farblich markiert [vgl. Google] (Paul)
	
      Bug: Benutzernamen Überprüfung rewrite (Fixes bzgl. gebannte Namen) (Paul)
      Bug: Seiten bei Suchnavigation in bestimmten Boards werden jetzt korrekt verlinkt (Paul)
      Bug: Parse Error in installfunctions.php in Verbindung mit aelteren PHP Versionen behoben (Paul)
      Bug: Geloeschte Posts: Index wurde nicht geupdatet wenn der Post der letzte im Thread war (Paul)
      Bug: Moderator-Titel wird wieder angezeigt (Paul)
      Bug: Verschachtelte [img]/[url] Tags funktionieren jetzt (Paul)
      Bug: Cross Site Scripting fixes (Paul)
      Bug: Beim [quote] Tag werden Whitespaces jetzt sinngemäß entfernt (Paul)
      Bug: Jump Menü - Man kann nun auf Kategorieren springen (Paul)
      Bug: Gesendete PMs werden jetzt korrekt eingefärbt (Paul)
      Bug: Name bei gesendeten PMs wird jetzt korrekt angezeigt (Paul)
      Bug: Suchfunktion funktionierte beim Blaettern im Ergebnis nicht richtig (Paul)
      Bug: Bei Registrierung und im Profil muss eine gültige E-Mail-Adresse angegeben werden
           (irgendwas@irgendwo.[2,4]) (Daniel)
      Bug: Bei bestimmten Anwendungen wurde die URL nicht richtig geparst und ein HTML-Fehler produziert (Morpheus)
      Bug: Threadtopic nicht länger als Editierfeld für Normaluser (Paul)
      Bug: Template/Style Fehler in topics.html: Seitennavigation wurde nicht korrekt eingefärbt (Paul)
      Bug: Template/Style Fehler in redirect.html: Hintergrund war statisch (Paul)
      Bug: Template/Style Fehler in message.html: Darstellung in Opera war inkorrekt (Paul)
      Bug: Benutzerrecht für unendlich PMs funktioniert jetzt (Paul)
      Bug: Deaktivierte Foren werden bei der Suche ausgeschlossen (Paul)

 Änderung: Bilder sind jetzt im templates/-Verzeichnis (Paul)
 Änderung: Announcement-Layout angepasst (Paul)
 Änderung: eregi functionen in PREG umgewandelt (Morpheus)
 Änderung: bei vielen Messages (Fehlermeldungen, u.ä.) Navpath hinzugefügt (Daniel)
 Änderung: Cookies reduziert auf ein einzelnes (Daniel)
 Änderung: ThWboard-Code wird nicht länger in [code] Tags ausgeführt (Paul)


Beta 2.73
=========

      Bug: Gäste können nicht länger neue Topics erstellen wenn der Name gebannt ist (Paul)
      Bug: Suchfunktion in Verbindung mit Threads der letzen Tage funktioniert jetzt korrekt (Paul)
      Bug: User löschen funktioniert jetzt problemlos (Paul)
      Bug: Hilfe Text für Showpostslevel bei Basic Settings korrigiert (Paul)
      Bug: Für Gäste unsichtbare Boards können nicht länger durchsucht werden (Paul)
      Bug: Update Info funktioniert jetzt in der .php3 Version (Paul)

 Änderung: Admin kann einstellen welche Gruppe die standard Gruppe sein soll (Paul)

Beta 2.72
=========

      Neu: Style import auch lokal vom Server möglich (Paul)

      Bug: Suchfunktion funktioniert wieder für Admins und User, die keinen Zugriff haben (Paul)
      Bug: User lassen sich wieder löschen im Admin Center (Paul)
      Bug: Letzten Post von User anzeigen funktioniert jetzt korrekt (Paul)

 Änderung: User werden in der User online Liste gelöscht nachdem sie ausgeloggt sind (Paul)

Beta 2.71
=========

      Neu: Maximale Anzahl der User, die gleichzeitig online sind wird jetzt gespeichert/angezeigt (Paul)
      Neu: Jumpmenü für Threads der letzten x Tage im Index eingebaut, im Admincenter abschaltbar (Paul, Hack von Jonas)
      Neu: Neuer Style: Experience (Templates & .style, Andy)

      Bug: Administrator hatte keinen Zugriff auf Boards (Paul)
      Bug: Userlevel lässt sich jetzt wieder ändern (Paul)
      Bug: [phpInstaller] Man kann auf "About" klicken, auch wenn das Forum schon installiert ist (Paul)
      Bug: Quicklinks sind wieder editierbar (Morpheus)
      Bug: User werden nicht doppelt angezeigt in der Useronline Liste (Morpheus)

 Änderung: MySQL Daten ohne `-Zeichen.

Beta 2.7
========

      Neu: Support für mehrere Boards in einer Datenbank (Morpheus)
      Neu: Eintragung des AIM und MSN (Morpheus)
      Neu: Möglichkeit zur visuellen Deativierung privater Foren (Morpheus)
      Neu: Titel kann im Thread editiert werden (Morpheus)
      Neu: Gast Prefix änderbar im Admin-Home (Morpheus)
      Neu: Newsletter-Hack wurde Standart (Adrian)
      Neu: Weitere Optionen im Profil (Suchen, Letzter Post) (Paul)
      Neu: Admin Center: Navigation im Source leichter modifizierbar (Paul)
      Neu: Umfangreiches Gruppensystem (Paul)
      Neu: Installer überarbeitet (Paul)
      Neu: Update System integriert (Paul)
      Neu: Configs sind nun in der Datenbank (Morpheus)
      Neu: Lastvisited jetzt in der DB (Paul)
      Neu: Alle Boards als gelesen markieren (Paul)
      Neu: Debug-Funktionen fest integriert, mit verschiedenen Stufen (Andy, Morpheus)
      Neu: Board kann temporär geschlossen werden (Morpheus)
      Neu: PMs können nun aus dem Profil heraus verschickt werden
      Neu: Boards können nun für Gäste unsichtbar gemacht werden (Morpheus)
      Neu: Announcements können nun einzelnen Boards zugeordnet werden (Morpheus)
      Neu: Admin-Center Redesign (Paul)
      Neu: Optionen zur Signaturlängenbeschränkung (Paul)
      Neu: Template Sets integriert (Morpheus)
      Neu: Style Im/Export verbessert: Download/Upload (Paul)

      Bug: Bug beim Editieren von Posts mit "&nbsp;" und anderen specialchars entfernt (Morpheus)
      Bug: Bug beim Editieren von Posts mit "</TEXTAREA>" entfernt (Morpheus)
      Bug: Thread wird nun gelöscht wenn der erste Post gelöscht wird (Morpheus)
      Bug: Gäste werden nun korrekt angezeigt / gezählt (Morpheus)
      Bug: Bei Deactivierung der E-Mail-Funktionen wurden die Benutzer wurden nicht automtisch eingeloggt (Morpheus)
      Bug: Zeilensprung funktioniert bei den Netscape Browsern wieder (Morpheus)
      Bug: Mehrere Blätterfunktionen gefixt (Memberlist, Board, etc) (Paul)
      Bug: [code]-Tag funktioniert jetzt mit Einrückungen (Tab und Spaces) (Paul)
      Bug: Email Adresse ändern funktioniert jetzt wieder (Paul)

 Änderung: Admin kann bei geschlossenem Forum trozdem das Forum betreten (Paul)
 Änderung: Login-Code modifiziert, Cookie problem -eventuell- gelöst? (Testen!) (Paul)
