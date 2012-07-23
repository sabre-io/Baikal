# 1. About this package

This package contains a release of Baïkal.
Go to http://baikal.codr.fr to get more information about this package, and upgrades.

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