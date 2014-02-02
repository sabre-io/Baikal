# 1. About this package

This package contains a release of Baïkal.
Go to http://baikal-server.com to get more information about this package, and upgrades.

# 2. Problem with authentication (CalDAV and CardDAV)

On webservers where PHP is served as FastCGI (check your phpinfo()
to find out if that's the case for you), Apache does not pass HTTP
Auth informations to PHP, and thus preventing Cal/CardDAV to auth
requests properly.  
Baïkal tries to address this issue by re-routing HTTP Auth informations
using Apaches mod_rewrite. This is done by the Apache config directives
found in the /.htaccess file that comes with Baïkal (or /html/.htaccess
for the non-ftp package).  
Note: if this file is empty / does not exist, you should try to add it manually  
(sometimes FTP clients decide to not send files with names beginning with a dot ".")

# 3. Troubleshooting calendar access (please check 2. first)

To troubleshoot user auth / data access, you may use curl to debug server responses. On a command line, run:

	curl -so - --digest --user username:password http://hostname/cal.php/calendars/username/default

(Be sure that the username exists, has the right password set, and has a calendar named default first).

If you see this, auth works for this username/password/calendar, so it has to be client-related:

	GET is only implemented on File objects

If you see this, password is wrong (case-sensitive) (despite the message indicating that it's the username):

	Incorrect username

If you see this, username is wrong:

	The supplied username was not on file

If you see this, auth works but the "principals" part of the URL is wrong (the /username/ after "calendars" in the URL):

	Principal with name username not found

If you see this, auth works but the calendar does not exist for this user:

	Calendar with name 'defaults' could not be found

If you see this, auth works and the calendar exists, but the provided user has no permission to access this calendar:

	Sabre_DAVACL_Exception_NeedPrivileges

If you see one of these, the URL is not well formed / invalid:

	File not found: XXXXX in 'root'
	
Or
	
	The requested URL XXXXX was not found on this server.

If you see nothing at all, curl cannot resolve your host.

# 4. Database is readonly (sqlite)

Using SQLite (the default setup), if you have troubles when putting data in the database,
(an exception "unable to open database file" is thrown) check that:
  * the system user running your Apache/PHP has write permissions on Specific/db/ (*the folder*)
  * the system user running your Apache/PHP has write permissions on Specific/db/db.sqlite (*the file*)
