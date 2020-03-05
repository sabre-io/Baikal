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
# Required configuration
# You *have* to review these settings for Baïkal to run properly
#

# Timezone of your users, if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
define("PROJECT_TIMEZONE", 'Europe/Paris');

# CardDAV ON/OFF switch; default TRUE
define("BAIKAL_CARD_ENABLED", TRUE);

# CalDAV ON/OFF switch; default TRUE
define("BAIKAL_CAL_ENABLED", TRUE);

# CalDAV invite From: mail address (comment or leave blank to disable notifications)
define("BAIKAL_INVITE_FROM", 'noreply@localhost');

# WebDAV authentication type; default Digest
define("BAIKAL_DAV_AUTH_TYPE", 'Digest');

# Baïkal Web admin password hash; Set via Baïkal Web Admin
define("BAIKAL_ADMIN_PASSWORDHASH", 'd648e7e076801c3bc5836d52b7252dda');