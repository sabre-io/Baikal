<?php
##############################################################################
#
#	Copyright notice
#
#	(c) 2020 Jérôme Schneider <mail@jeromeschneider.fr>
#	All rights reserved
#
#	http://sabre.io/baikal
#
#	This script is part of the Baïkal Server project. The Baïkal
#	Server project is free software; you can redistribute it
#	and/or modify it under the terms of the GNU General Public
#	License as published by the Free Software Foundation; either
#	version 2 of the License, or (at your option) any later version.
#
#	The GNU General Public License can be found at
#	http://www.gnu.org/copyleft/gpl.html.
#
#	This script is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
#	GNU General Public License for more details.
#
#	This copyright notice MUST APPEAR in all copies of the script!
#
##############################################################################

##############################################################################
# System configuration
# Should not be changed, unless YNWYD
#
# RULES
#	0. All folder pathes *must* be suffixed by "/"
#	1. All URIs *must* be suffixed by "/" if pointing to a folder
#

# If you change this value, you'll have to re-generate passwords for all your users
define("BAIKAL_AUTH_REALM", 'BaikalDAV');

# Should begin and end with a "/"
define("BAIKAL_CARD_BASEURI", PROJECT_BASEURI . "card.php/");

# Should begin and end with a "/"
define("BAIKAL_CAL_BASEURI", PROJECT_BASEURI . "cal.php/");

# Should begin and end with a "/"
define("BAIKAL_DAV_BASEURI", PROJECT_BASEURI . "dav.php/");

# Define path to Baïkal Database SQLite file
define("PROJECT_SQLITE_FILE", PROJECT_PATH_SPECIFIC . "db/db.sqlite");

# MySQL > Use MySQL instead of SQLite ?
define("PROJECT_DB_MYSQL", FALSE);

# MySQL > Host, including ':portnumber' if port is not the default one (3306)
define("PROJECT_DB_MYSQL_HOST", '');

# MySQL > Database name
define("PROJECT_DB_MYSQL_DBNAME", '');

# MySQL > Username
define("PROJECT_DB_MYSQL_USERNAME", '');

# MySQL > Password
define("PROJECT_DB_MYSQL_PASSWORD", '');

# A random 32 bytes key that will be used to encrypt data
define("BAIKAL_ENCRYPTION_KEY", '740ad6b32f1aba25c7aa7b10ca6a94a5');

# The currently configured Baïkal version
define("BAIKAL_CONFIGURED_VERSION", '0.6.1');