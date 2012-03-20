<?php

##############################################################################
# In this section: Required configuration: you *have* to customize
# these settings for Baïkal to run properly
#

# Timezone of your users, if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
define("BAIKAL_TIMEZONE", "Europe/Paris");

# WEB absolute URI
define("BAIKAL_BASEURI", "http://dav.mydomain.com/");

# WEB absolute URI
define("BAIKAL_ADMIN_ENABLED", TRUE);


##############################################################################
# In this section: Optional configuration: you *may* customize these settings
#

# CardDAV ON/OFF switch
define("BAIKAL_CARD_ENABLED", TRUE);

# CalDAV ON/OFF switch
define("BAIKAL_CAL_ENABLED", TRUE);


##############################################################################
##############################################################################
##############################################################################
# System configuration
# Should not be changed, unless YNWYD
#
# RULES
#	0. All folder pathes *must* be suffixed by "/"
#

# PATH to SabreDAV
define("BAIKAL_PATH_SABREDAV", BAIKAL_PATH_FRAMEWORKS . "SabreDAV/lib/Sabre/");

# If you change this value, you'll have to re-generate passwords for all your users
define("BAIKAL_AUTH_REALM", "BaikalDAV");

# Should begin with a "/"
define("BAIKAL_CARD_BASEURI", "/card.php/");

# Should begin with a "/"
define("BAIKAL_CAL_BASEURI", "/cal.php/");

# SQLite DB path
define("BAIKAL_SQLITE_FILE", BAIKAL_PATH_SPECIFIC . "db/baikal.sqlite");
