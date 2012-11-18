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

if(file_exists(getcwd() . "/Core")) {
	# Flat FTP mode
	define("PROJECT_PATH_ROOT", getcwd() . "/");	#./
} else {
	# Dedicated server mode
	define("PROJECT_PATH_ROOT", dirname(getcwd()) . "/");	#../
}

# Bootstraping Flake
require_once(PROJECT_PATH_ROOT . "Core/Frameworks/Flake/Framework.php");
\Flake\Framework::bootstrap();

# Bootstrapping Baïkal
\Baikal\Framework::bootstrap();

if(!defined("BAIKAL_CAL_ENABLED") || BAIKAL_CAL_ENABLED !== TRUE) {
	throw new ErrorException("Baikal CalDAV is disabled.", 0, 255, __FILE__, __LINE__);
}

# Backends
$authBackend = new \Sabre\DAV\Auth\Backend\PDO($GLOBALS["DB"]->getPDO());
$principalBackend = new \Sabre\DAVACL\PrincipalBackend\PDO($GLOBALS["DB"]->getPDO());
$calendarBackend = new \Sabre\CalDAV\Backend\PDO($GLOBALS["DB"]->getPDO());

# Directory structure
$nodes = array(
    new \Sabre\CalDAV\Principal\Collection($principalBackend),
    new \Sabre\CalDAV\CalendarRootNode($principalBackend, $calendarBackend),
);

# Initializing server
$server = new \Sabre\DAV\Server($nodes);
$server->setBaseUri(BAIKAL_CAL_BASEURI);

# Server Plugins
$server->addPlugin(new \Sabre\DAV\Auth\Plugin($authBackend, BAIKAL_AUTH_REALM));
$server->addPlugin(new \Sabre\DAVACL\Plugin());
$server->addPlugin(new \Sabre\CalDAV\Plugin());

# And off we go!
$server->exec();