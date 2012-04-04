<?php

##############################################################################
# In this section: Required configuration: you *have* to customize
# these settings for Baïkal to run properly
#

# Timezone of your users, if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
define("BAIKAL_TIMEZONE", "Europe/Paris");

# Absolute Baïkal URI; end with slash; includes protocol (http:// or https://), port (optional) and subfolders if any
define("BAIKAL_URI", "http://baikal.jeromeschneider.fr/");

##############################################################################
# In this section: Optional configuration: you *may* customize these settings
#

# CardDAV ON/OFF switch; default TRUE
define("BAIKAL_CARD_ENABLED", TRUE);

# CalDAV ON/OFF switch; default TRUE
define("BAIKAL_CAL_ENABLED", TRUE);

# Baïkal Web Admin interface ON/OFF; default FALSE
define("BAIKAL_ADMIN_ENABLED", TRUE);

# Standalone Server, allowed or not; default FALSE
define("BAIKAL_STANDALONE_ALLOWED", TRUE);

# Standalone Server, port number; default 8888
define("BAIKAL_STANDALONE_PORT", 8888);

# Baïkal Web interface admin password hash; Set by Core/Scripts/adminpassword.php
define("BAIKAL_ADMIN_PASSWORDHASH", "5746d6eb0ff2968c494e5d904b8ef4b6");
