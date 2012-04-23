<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
*  All rights reserved
*
*  http://baikal.codr.fr
*
*  This script is part of the Baïkal Server project. The Baïkal
*  Server project is free software; you can redistribute it
*  and/or modify it under the terms of the GNU General Public
*  License as published by the Free Software Foundation; either
*  version 2 of the License, or (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

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
define("BAIKAL_ADMIN_ENABLED", TRUE);

# Standalone Server, allowed or not; default FALSE
define("BAIKAL_STANDALONE_ALLOWED", TRUE);

# Standalone Server, port number; default 8888
define("BAIKAL_STANDALONE_PORT", 8888);

# Baïkal Web interface admin password hash; Set by Core/Scripts/adminpassword.php
define("BAIKAL_ADMIN_PASSWORDHASH", "5746d6eb0ff2968c494e5d904b8ef4b6");
