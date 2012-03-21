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

# CalDAV ON/OFF switch
define("BAIKAL_STANDALONE_ENABLED", FALSE);

# CalDAV ON/OFF switch
define("BAIKAL_STANDALONE_PORT", 8888);