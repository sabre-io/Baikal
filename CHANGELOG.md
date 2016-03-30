ChangeLog
=========

0.4.4 (2016-03-30)
------------------

* #517: Added the Sync plugin. Should speed things up and fix syncing bugs
  with Em Client.
* Ships with sabre/dav 3.1.2


0.4.3 (2016-03-23)
------------------

* #513: Fixed error during installation, introduced with 0.4.2.
* Ships with sabre/dav 3.1.2


0.4.2 (2016-03-21)
------------------

* #510: Fix PHP warning on fresh install.
* #507: Disable installer if Baikal was already configured.
* Ships with sabre/dav 3.1.2


0.4.1 (2016-03-17)
------------------

* #506: Fix Sqlite upgrades from people that are upgrading from 0.2.7 or
  earlier.
* Ships with sabre/dav 3.1.2


0.4.0 (2016-03-15)
------------------

* #472: If you were using Sqlite, Cal- CardDAV clients might have missed
  updates from the server.
* #482 / #490: Fix installation issue if Sqlite is not enabled. (@fabianwolf)
* #491: Added CalDAV Scheduling plugin.
* Added Property Storage plugin.
* Removed features related to locking the admin interface. This means that you
  no longer have to create the `ENABLE_ADMIN` / `ENABLE_INSTALL` files.
* Ships with sabre/dav 3.1.2


0.3.5 (2016-02-24)
------------------

* #466: Fixed a small issue that can effect new installions. Existing working
  installations do not need to upgrade.
* Ships with sabre/dav 3.1.1


0.3.4 (2016-02-20)
------------------

* Last build was broken, contained data in the `Specific/` directory. This
  affected new installs but not upgrades.
* Ships with sabre/dav 3.1.1


0.3.3 (2016-02-18)
-----------------

* #457: The realm was not correctly set from configuration for Digest auth.
* Reduced memory usage in upgrade script. Should help with upgrading large
  databases.
* Removed `BAIKAL_PATH_SABREDAV` setting. It was no longer used.
* Ships with sabre/dav 3.1.1


0.3.2 (2016-02-16)
------------------

* #453: Fixed a bug during a 0.2.7 to 0.3.1 upgrade if a users' me-card was
  set. (@untitaker).
* Ships with sabre/dav 3.1.1


0.3.1 (2016-02-15)
------------------

* #451: Fixed a fatal crasher. (@paolomainardi)
* Ships with sabre/dav 3.1.1


0.3.0 (2016-02-14)
------------------

* Now requires PHP 5.5.
* Upgraded to sabre/dav 3.1
  * Support for WebDAV-Sync.
  * Support for Calendar subscriptions.
  * Support for iCalender and vCard export plugins.
* Created a central `dav.php` that does both carddav and caldav. `cal.php`
  and `card.php` are still there, but will be removed in a future version.
* Added ability for users to change the calendar color.
* Moved server logic to a new class: `Baikal\Core\Server`.
* List of timezones is not generated from `DateTimeZone` class.
* Simplified packaging scripts into a Makefile.
* Fixed: bug when using a MySQL schema name that contains a whitespace.
* Twig is now a composer dependency.
* Moved documentation to sabre.io.
* #381: SQLite database woes. The database is now created from scratch when
  installing.
* #320: Allow underscores in calendar/addressbook uris.
* Ships with sabre/dav 3.1.1


0.2.7 (2014-02-02)
------------------

* @jeromeschneider: New error detection: composer has not been installed.
* @Busch: added rewrite rules for apache2 hosts.
* @jeromeschneider: Corrected the http/https protocol detection.
* @josteink, @janpieper, @jaltek, @Jentsch, @GeeF and @fhemberger: Improved
  INSTALL.md.
* @janpieper: Added CalDAV and CardDAV instructions for BlackBerry OS 10.
* James Lay: Add Quick & Dirty install guide for Ubuntu 12.04 in INSTALL.md.
* @torzak: Auth more compatible with Synology software.
* @skyhook19: Added Thunderbird CardDAV setup instructions.
* @cimm: Update OS X and iOS installation instructions.
* @Jentsch: Removing eXecutable flag from non executables like text files or
  php scripts.
* @Jentsch: Adding eXecutable flag to a bash script.
* @altima: Improved CardDAV and CalDAV auth compatibility with Windows Phones.
* @fhemberger: Make calendar description optional.
* @fhemberger: Make HTML5 shiv a local resource.
* @fhemberger: Improve application security.
* @janpieper and @evert: Fixing #139: Prevent PHP from parsing your MySQL
  credentials.
* Ships with sabre/dav 1.8.6


0.2.6 (2013-07-07)
------------------

* No changes
* Ships with sabre/dav 1.8.6


0.2.5 (2013-07-07)
------------------

* Baïkal releases are now based on composer thanks to @evert.
* Formal and Flake are not longer submodules
* Moved a few classes around.
* Ships with sabre/dav 1.8.6


0.2.4 (2012-11-18)
------------------

* Ships with sabre/dav 1.8.0


0.2.3 (2012-11-08)
-----------------

* Ships with sabre/dav 1.5.7
