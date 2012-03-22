<?php

##############################################################################
# In this section: Required configuration: you *have* to customize
# these settings for Baïkal to run properly
#

# Timezone of your users, if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
define("BAIKAL_TIMEZONE", "Europe/Paris");

##############################################################################
# In this section: Optional configuration: you *may* customize these settings
#

# CardDAV ON/OFF switch; default TRUE
define("BAIKAL_CARD_ENABLED", TRUE);

# CalDAV ON/OFF switch; default TRUE
define("BAIKAL_CAL_ENABLED", TRUE);

# Baïkal Web Admin interface ON/OFF; default FALSE
define("BAIKAL_ADMIN_ENABLED", FALSE);

# Standalone Server, allowed or not; default FALSE
define("BAIKAL_STANDALONE_ALLOWED", FALSE);

# Standalone Server, port number; default 8888
define("BAIKAL_STANDALONE_PORT", 8888);
