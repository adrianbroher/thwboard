ThWboard NEWS -- history of user-visible changes.
=================================================

Copyright (C) 2000-2004 by ThWboard Development Group

See the end of the file for license conditions.

Beta 2.85
---------

### Changes

- Create RSS feeds for whole board or limited by board. (theDon, Luki)
- Export user emails as text list. (theDon, Luki)
- Notify users when the private message box quota is exausted by 95% or
  more. (theDon)
- Improved login flood protection. (theDon)
- Improved post flood protection. (theDon)
- Implemented thread merge function. (theDon)
- Extended debug reports. (Luki, theDon)
- Added debug report sinks. (Luki, theDon)
- List subscribed threads. (theDon)
- Unsubscribe threads. (theDon)
- List posts since last visit for registered users. (Luki, theDon)
- Added non-activated users to database cleaning tool. (theDon)
- Added unread threads to database cleaning tool. (theDon)
- Added a checklist to the database cleaning tool to uncheck users and
  threads (theDon)
- Added permission to allow users sending private messages even if the
  target inbox is full. (theDon, MrNase)
- Substitute [img] URI [/img] with an img tag. (theDon)
- Remove session IDs when sending private messages. (theDon)
- Added basic PHP5-compability. (theDon)
- Allow email changes with an activation e-mail. (theDon)
- The restore password feature now requires an confirmation. (theDon)
- The user list uses alphabetical order when limiting the search to the
  first character. (theDon)
- Only check the dimensions of the avatar in case url_fopen_wrapper is
  enabled. (theDon)
- Show a notice if your own e-mail is hidden. (theDon)
- Replaced some english terms with their germain equivalent in the
  index. (theDon)
- Show a error message when trying to log when already logged in.
  (theDon)
- Write css cache when setting a style as default. (theDon)
- Parse image ThWB tags within private messages. (theDon)

### Bugfixes

- Do not destroy still valid sessions in certain cases. (theDon)
- Limit the search by board does not affect the search. (theDon)
- The search page links can exceed the allowed length. (theDon)
- ThWB tags were not displayed properly withing the documentation.
  (theDon)
- Post with in forum links are cut off after the link. (Paul)
- Do not store duplicate entries within the online list. (theDon)
- Search highlights are not placed properly. (theDon)
- Guest flood protection was not applied when creating new topics.
  (theDon)
- German Umlauts are not encoded properly. (theDon)
- The user activation email is not displayed properly for AOL webmail
  users. (theDon)
- When deleting the first post of a thread the thread author is not
  updated. (theDon)
- Fix some PHP notices and warnings. (theDon)


Beta 2.84
---------

### Changes

- Added post and thread recount feature to database cleaning tool.
  (theDon)
- Added optimization feature to database cleaning tool.
  (theDon, Hack written by MrNase)
- Remove session IDs from postings. (theDon)
- Made the category titles within the index toggle the visibility of the
  category. (theDon)
- Added links to every post pointing to the top and the bottom of
  a thread page. (theDon)
- Allow use of ThWB tags in the calendar. (theDon)
- Administrators now can set the session timeout. (theDon)
- Do not allow user a registration with user names containing ThWB tags.
  (theDon)
- Optimized html templates. (Dominik Hahn (MrNase))
- Moved CSS code into separate file. (theDon)
- Quicklinks now assume http:// as default scheme. (theDon)

### Bugfixes

- Deleting posts or threads does not update user and board post count
  properly. (theDon)
- Remove highlight parameter in search result list. (theDon)
- Unable to send newsletter. (theDon)
- Session ID is lost in cases. (theDon)
- Private message do not display newlines. (theDon)
- XSS in thread list. (theDon)
- Unable to filter user list names with a leading digit. (theDon)
- No private message copy were stored in the outbox in case the target
  inbox where full. (theDon)
- Unable to delete or move threads. (theDon)
- Only compare the first 24 bits of a IP to fix issues with AOL users.
  (theDon)
- Do not show an error if the IP does not match but there is a valid
  session cookie. (theDon)


Beta 2.83
---------

### Changes

- Only administrators can see PHP error messages. (theDon)
- The IP check can be disabled for specific users. (theDon)

### Bugfixes

- Do not display all users on the staff page. (theDon)
- Unable to set group permissions for certain boards. (theDon)
- Fix some PHP notices and warnings. (theDon)


Beta 2.82
---------

### Changes

- The user list can now be searched or filtered by the leading character.
  (theDon)
- Users now can optionally use an url session id instead of a cookie.
  (theDon)
- Users now recieve an email on registration with an activation link to
  verify the email ownership. (theDon, Hack written by MrNase)
- The whole board now runs error free with error_reporting set to E_ALL.
  (theDon)
- Extra scripts and icons now provided as .tar.bz2 archive in the extras
  directory. (theDon)
- The database cleanup tool now also deletes thread links and opening
  posts. (Sebastian)
- The number of group permissions flags was increased to 50.
  (theDon, Daniel)
- Limited the size of user interests to 255 or less characters depending
  on the used utf-8 characters. (Sebastian)
- When sending news letters the sending process is interupted for some
  time after sending a block of mails. (theDon)
- Removed smilies from board closed message. (Sebastian)
- URLs will be completely converted to hyperlinks even if they contain
  commas or a numeric schema. (Sebastian)
- When changing the topic of a linked thread the link topic is updated
  accordingly. (Sebastian)
- The edit timeout is now used to limit the post deletion time.
  (Sebastian)
- If the thread topic overflows in the forum index a hot tip with the
  complete topic is displayed. (Luki)
- When registering, logging in or when posting as a guest a notice
  regarding the privacy and logging of the IP address is shown. (Daniel)
- The weekday title in the calendar does use the secondary background
  color. (theDon)
- All displayed pathes in the administration interface are now relative
  to the board installation path and not relative to the administration
  interface. (theDon)

### Bugfixes

- Can not read announcements after entering the board by jump menu.
- Quote ThWB tags should not be case-sensitive. (Sebastian)
- Thread topic uppercase protection does not work sometimes. (Sebastian)
- Removed bug that prevents user from logging in. (Sebastian)
- The Regexp for page highlights allows beyond post selections. (Paul)
- Private boards are not searchable. (Paul)
- \#thw4168 - Fixed bug. (Paul)
- Users can inject HTML into their profile. (Superhausi, Sebastian)
- Users can upload malicious avatar names. (Superhausi)
- The notallowed avatar placeholder is added sometimes into the post.
  (Superhausi)
- \#thw3917 - Bad words filter leads sometimes to an empty topic.
  (Superhausi)
- \#thw4596, \#thw4587 - Removed various XSS injections.
  (Superhausi, Paul, Sebastian, theDon, Tendor)
- Users are not redirected when replying, quoting or forwaring a private
  message. (Superhausi, Sebastian)
- Quicklinks required register_globals enabled. (Superhausi, Sebastian)
- Fixed an SQL injection within the administration interface.
  (Superhausi)
- Fixed an SQL injection within the announcement view. (Paul)
- Fixed an SQL injection within the calendar administration interface.
  (theDon)
- Fixed an SQL injection within the calendar event view. (theDon)
- Removed an XSS injection in the login process. (Tendor)
- The non-activated user database cleaning tool works again. (Sebastian)
- The age in the user profile is one year off. (Sebastian)
- Users have a stale session cookie after changing their email address.
  (Sebastian)
- Linked threads do not redirect to real thread. (Sebastian)
- Relaxed the email format validation when registering. (Sebastian)
- Adding quotes in the user homepage allows XSS. (theDon)
- Removed an XSS injection in the calendar. (theDon)
- Removed an XSS injection with the image ThWB tag. (theDon)
- The time parameter in the thread list allows XSS. (theDon)
- The permissions object is not always initialized properly. (theDon)
- Users can sometimes create empty posts. (theDon)
- The calendar does not always highlight the current day. (theDon)
- The number of active users in the statistiscs page is not correct.
  (theDon)
- When sending a newsletter to a large amount of users the script can
  reach time out. (theDon)
- ThWB img tag is now parsed according to the set image level. (theDon)
- Some calendar events are shown on the wrong day. (theDon)
- Some labels in the bad words administrator interface are not translated.
  (theDon)
- \#thw4590 - AIM and MSG are not escaped properly in the user profile.
  (Paul)
- \#thw4594 - Homepage in user profile is not saved properly. (Paul)
- When updating a board a thread link can be set as most recent thread.
  (theDon)
- The calendar weekday header is not displayed propely with some styles.
  (theDon)
- Administrators are unable to upload a style. (theDon)
- When quoting a username containing ThWB tags the quote output is
  broken. (theDon)
- Administrators can not see the postcount of users when the show postings
  level is set to 2. (theDon)
- The search results does highlight HTML tags. (theDon)
- URLs after are not converted to links after a closing quote ThWB tag.
  (Sebastian)
- \#thw4596, \#thw4587 - Unable to send private messages (Paul)
- The last thread title in the board list does not interpret ThWB tags.
  (Sebastian)
- Unicode characters are not handled properly when sending a post. (theDon)


Beta 2.81
---------

### Changes

- Users can create calendar events. (Superhausi)
- Installations can now be run without writing permissions in the config
  directory. (Paul)
- Thread links now can be deleted. (Paul)
- Templates now can contain PHP code snipplets. (Daniel)
- Added missing navigation path to various features. (Daniel)
- User homepages now open in a new browser window/tab. (Daniel)
- Only logged in users can see a users email. (Sebastian)
- Added noparse ThWB tag. (Paul)

### Bugfixes

- When changing the password the user is logged out. (Daniel)
- When changin the email address an error is shown. (Daniel)
- Fixed color, quotes and code ThWB tag. (Daniel)
- Fixed smiley recognition. (Daniel)
- Users can create posts that exeed the maximum length or fall below the
  minimum length. (Daniel)
- Help links in post form were pointing to the right document. (Daniel)
- Do not display http:// in the user profile when there is no homepage
  set. (Daniel)
- The template editor adds new lines when saving. (Daniel)
- Can not create calendar events before 1970. (Paul)


Beta 2.80
---------

### Changes

- Installer now supports multiple languages. (Paul)
- Installer now compatible with PHP 4.2. (Paul)
- Added a news script, that displays opening posts of a certain board as
  news entries. (Paul)
- Added previous avatar hack to code. (Morpheus, Hack written by Andy)
- Extended user help. (Paul, Hack written by Jonas)
- Added overview and statistics for user ranks below the help/ranks.
  (Paul, Hack written by Hallenbeck)
- Added php ThWB tag, which allows users to markup php code. (Morpheus)
- Added php ThWB mail tag, which converts email to email hyperlinks. (Paul)
- Smilies can now be used in private messages when enabled. (Morpheus)
- Private messages can now be sent as emails instead of sending them to
  an internal inbox. (Morpheus)
- Administrators now can toggle output GZIP compression in the
  administration center. (Paul)
- Added database cleanup tool to remove unread threads or not completely
  registered users. (Morpheus)
- All threads can now be closed after a timeout. (Morpheus)
- All threads can now be deleted after a timeout. (Morpheus)
- The first administrator created when installing the board can not be
  deleted or demoted anymore. (Superhausi)
- The user search within the administration center now exposes more
  search parameters. (Paul)
- Added bad words protection.  User posts, names and topics are checked
  for a blacklist of words and substituted with administrator configurable
  alternatives. (Daniel)
- Users are redirected to the previous visited URL after logging in.
  (Paul)
- Guests are part of a dedicated group. (Paul)
- Updated the user profile editor in the administration center to expose
  all current user properties. (Morpheus)
- Added input field focus to some forms. (Paul)
- Added new group permissions. (Paul)
- When searching the search terms are highlighted in the search result.
  (Paul)
- Template related images are located within the template directory
  structure. (Paul)
- Added the navigation path to various forum messages like error
  messages, status notifications and similar notifications. (Daniel)
- ThWB tags are not parsed within code tags anymore. (Paul)
- Merged cookies to a single cookie. (Daniel)
- Changed announcement listing layout to match the other board parts.
  (Paul)
- Added categories as targets in jump dropdown menu. (Paul)


### Bugfixes

- When navigating through the search results after a board limited
  search some pages are skipped. (Paul)
- Fixed a PHP parse error in the installer for older PHP versions. (Paul)
- The last post date in a thread was not updated when deleting the last
  post (Paul)
- The moderator title is not displayed properly sometimes. (Paul)
- Img ThWB tags wrapped within url tags are not displayed properly. (Paul)
- Fixed some XSS bugs. (Paul)
- When registering or changing users can set an invalid email address.
  (Paul)
- When adding quote ThWB tags whitespace after the quote is retained.
  (Paul)
- A sent private message is still marked as not send. (Paul)
- The displayed sender name on a private message is not the actual sender.
  (Paul)
- Some URLs are not parsed properly. (Morpheus)
- Users can enter longer thread topic than they are allowed to save.
  (Paul)
- The page navigation is not displayed properly in the thread list. (Paul)
- The background in the redirect page is not scrolling. (Paul)
- The message template is not displayed on Opera. (Paul)
- The permission to send private messages to full inboxes is not
  honoured. (Paul)
- Threads within hidden boards are listed in search results. (Paul)


Beta 2.73
---------

### Changes

- Administrators now can configure which group should be the default one.
  (Paul)

### Bugfixes

- Guests can still create threads even if their name is banned. (Paul)
- The search results for the 'new threads since last visit' do not show
  all new threads. (Paul)
- When deleting a user not all related data is deleted. (Paul)
- Fixed the information text for the 'show post level' setting. (Paul)
- User are able to select hidden boards in the search mask. (Paul)
- Unable to recieve update informations when running the .php3 version
  of the board. (Paul)


Beta 2.72
---------

### Changes

- Administrators now can import styles locally from the server. (Paul)
- After logging out users will not be listed in the 'who is online'
  list anymore. (Paul)

### Bugfixes

- Banned users and administrators are unable to use the search. (Paul)
- Administrators can again delete users from the administration interface.
  (Paul)
- The last post entry in the user profile does not point to the actual
  last post by the user. (Paul)


Beta 2.71
---------

### Changes

- The board now records the peak value and time of simultaneously online
  users. (Paul)
- Added a dropdown menu to list the threads in the last x days quickly.
  This can be disabled in the administration center.
  (Paul, Hack written by Jonas)
- Added a news style 'Experience', containing templates and style file.
  (Andy)

### Bugfixes

- Administrators can not access boards. (Paul)
- User levels can not be changed. (Paul)
- The 'About' link in the installer can still be accessed when the board
  is installed. (Paul)
- Quicklinks can not edited. (Morpheus)
- Users can be listed multiple times in the 'who is online' list.
  (Morpheus)


Beta 2.70
---------

### Changes

- Added table prefixes to allow multiple installations within a single
  database. (Morpheus)
- Added user attributes to store AIM and MSN user names. (Morpheus)
- Added the feature to hide some boards for specific groups. (Morpheus)
- The thread title now can be edited within the first post of a thread.
  (Morpheus)
- The guest name prefix now can be edited by the administrator.
  (Morpheus)
- Added the newsletter hack to the code. (Adrian)
- Added a link to the user profile to search for posts written by that
  user. (Paul)
- Added a link to the user profile to access the last post written by that
  user. (Paul)
- Added a permission groups system. (Paul)
- Updated installer. (Paul)
- Added a featuire to check for updates of the software. (Paul)
- Configurations are now stored within the database. (Morpheus)
- Added the last visit date to the user attributes. (Paul)
- Users now can mark all boards or single boards as read. (Paul)
- Added debug notifications and debug levels. (Andy, Morpheus)
- The board can now be closed temporary with a notice. (Morpheus)
- Added a link to the user profile to send that user a private message.
- Boards can now be hidden from guests. (Morpheus)
- When creating announcements the announcement can be assigned to a board.
  (Morpheus)
- Redesigned the administration center. (Paul)
- Added options to limit the signature size. (Paul)
- Added importing and exporting styles by upload or download a style file.
  (Paul)
- Administrators can access the board even it is closed. (Paul)

### Bugfixes

- Fixed cookie set issue when logging in. (Paul)
- HTML encoded character are not properly decoded when editing a post.
  (Morpheus)
- Threads are not deleted when the first post is deleted. (Morpheus)
- The number of guests is not counted properly. (Morpheus)
- When the activation email is disabled users are not logged in
  automatically when registering themselves. (Morpheus)
- The member list, thread list and thread view are not paginated properly.
  (Paul)
- Code ThWB tag does not honour indentations. (Paul)
- Unable to edit your own email address. (Paul)


----------------------------------------------------------------------
This file is part of ThWboard

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program;  If not, see <http://www.gnu.org/licenses/>.
