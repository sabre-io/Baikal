# 1. About this package

This package contains a release of Baïkal.
Go to http://baikal.codr.fr to get more information about this package, and upgrades.

# 2. What is Baïkal ?

Baïkal is an open source lightweight CalDAV and CardDAV server. It's developped
by Jérôme Schneider (http://codr.fr) and based on the SabreDAV library. Baïkal
is distributed under the GPL license. 

To get more info about the GPL license, visit 
http://www.opensource.org/licenses/gpl-license.php.

# 3. Baïkal requirements

Baïkal is based on PHP 5.3.0, and uses a SQLite3 with PHP PDO. For more
information regarding these requirements see the INSTALL.md file in this folder.

# 4. What should you do if you have a problem ?

  1. Read the available documentation carefully

  2. Search the web carefully about Baïkal CalDAV CardDAV

  3. Mail me (Jérôme Schneider) at mail@jeromeschneider.fr
     When mailing, see the following guidelines... 
     - Be verbose; Always include the version of used Baïkal and
       server environment (phpinfo())...
     - Be as specific and clear as possible - questions like "my
       installation does not work - what can I do???" will be ignored.
     - Write in english or french, please.

  4. If you have identified a genuine new bug, report it at
     the mail address given in point 3 of this list

# 5. How to get started

Please see the INSTALL.md in this folder.

# 6. Troubleshooting

## Problem with Cal/CardDAV auth
On webservers where PHP is served as FastCGI (check your phpinfo()
to find out if that's the case for you), Apache does not pass HTTP
Auth informations to PHP, and thus preventing Cal/CardDAV to auth
requests properly. Baïkal tries to address this issue by re-routing
HTTP Auth informations using Apaches mod_rewrite. This is done by the
Apache config directives found in the /.htaccess file that comes with Baïkal
(or /html/.htaccess for the non-ftp package).
Note: if this file is empty / does not exist, you should try to add it manually
(sometimes FTP clients decide to not send files with names beginning with a dot ".")

# 7. Credits
Jérôme Schneider (@jeromeschneider) is admin and lead developper.
Many thanks to Daniel Aleksandersen (@zcode) for greatly improving the quality of the project page (http://baikal.codr.fr). Much appreciated, Daniel :)

-- Jérôme Schneider <mail@jeromeschneider.fr>  Sat, 12 May 2012 23:45:00 +0100