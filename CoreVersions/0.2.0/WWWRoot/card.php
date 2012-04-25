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
define("BAIKAL_CONTEXT_BASEURI", "/");

# Bootstraping Baikal
require_once(dirname(dirname(__FILE__)) . "/Frameworks/Baikal/Core/Bootstrap.php");

if(!defined("BAIKAL_CARD_ENABLED") || BAIKAL_CARD_ENABLED !== TRUE) {
	throw new ErrorException("Baikal CardDAV is disabled.", 0, 255, __FILE__, __LINE__);
}

# Backends
$authBackend = new Sabre_DAV_Auth_Backend_PDO($GLOBALS["DB"]->getPDO());
$principalBackend = new Sabre_DAVACL_PrincipalBackend_PDO($GLOBALS["DB"]->getPDO());
$carddavBackend = new Sabre_CardDAV_Backend_PDO($GLOBALS["DB"]->getPDO()); 

# Setting up the directory tree
$nodes = array(
    new Sabre_DAVACL_PrincipalCollection($principalBackend),
    new Sabre_CardDAV_AddressBookRoot($principalBackend, $carddavBackend),
);

# The object tree needs in turn to be passed to the server class
$server = new Sabre_DAV_Server($nodes);
$server->setBaseUri(BAIKAL_CARD_BASEURI);

# Plugins 
$server->addPlugin(new Sabre_DAV_Auth_Plugin($authBackend, BAIKAL_AUTH_REALM));
$server->addPlugin(new Sabre_CardDAV_Plugin());
$server->addPlugin(new Sabre_DAVACL_Plugin());

# And off we go!
$server->exec();