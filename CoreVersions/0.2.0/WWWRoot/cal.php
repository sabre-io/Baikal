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

define("BAIKAL_CONTEXT", TRUE);
define("PROJECT_CONTEXT_BASEURI", "/");
define("PROJECT_PATH_ROOT", dirname(getcwd()) . "/");	#../

# Bootstraping Flake
require_once(PROJECT_PATH_ROOT . "Core/Frameworks/Flake/Framework.php");
\Flake\Framework::bootstrap();

# Bootstrapping Baïkal
\Baikal\Framework::bootstrap();

if(!defined("BAIKAL_CAL_ENABLED") || BAIKAL_CAL_ENABLED !== TRUE) {
	throw new ErrorException("Baikal CalDAV is disabled.", 0, 255, __FILE__, __LINE__);
}

# Backends
$authBackend = new Sabre_DAV_Auth_Backend_PDO($GLOBALS["DB"]->getPDO());
$principalBackend = new Sabre_DAVACL_PrincipalBackend_PDO($GLOBALS["DB"]->getPDO());
$calendarBackend = new Sabre_CalDAV_Backend_PDO($GLOBALS["DB"]->getPDO());

# Directory structure
$nodes = array(
    new Sabre_CalDAV_Principal_Collection($principalBackend),
    new Sabre_CalDAV_CalendarRootNode($principalBackend, $calendarBackend),
);

# Initializing server
$server = new Sabre_DAV_Server($nodes);
$server->setBaseUri(BAIKAL_CAL_BASEURI);

# Server Plugins
$server->addPlugin(new Sabre_DAV_Auth_Plugin($authBackend, BAIKAL_AUTH_REALM));
$server->addPlugin(new Sabre_DAVACL_Plugin());
$server->addPlugin(new Sabre_CalDAV_Plugin());


# And off we go!
$server->exec();