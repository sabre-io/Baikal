<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
*  All rights reserved
*
*  http://baikal-server.com
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

if(!file_exists(PROJECT_PATH_ROOT . 'vendor/')) {
	die('<h1>Incomplete installation</h1><p>Ba&iuml;kal dependencies have not been installed. Please, execute "<strong>composer install</strong>" in the folder where you installed Ba&iuml;kal.');
}

require PROJECT_PATH_ROOT . 'vendor/autoload.php';

# Bootstraping Flake
\Flake\Framework::bootstrap();

# Bootstrapping Baïkal
\Baikal\Framework::bootstrap();

if(!defined("BAIKAL_CARD_ENABLED") || BAIKAL_CARD_ENABLED !== TRUE) {
	throw new ErrorException("Baikal CardDAV is disabled.", 0, 255, __FILE__, __LINE__);
}

# Backends
if( BAIKAL_DAV_AUTH_TYPE == "Basic" || preg_match('/Windows-Phone-WebDAV-Client/i', $_SERVER['HTTP_USER_AGENT']) )
    $authBackend = new \Baikal\Core\PDOBasicAuth($GLOBALS["DB"]->getPDO(), BAIKAL_AUTH_REALM);
else
    $authBackend = new \Sabre\DAV\Auth\Backend\PDO($GLOBALS["DB"]->getPDO());

$principalBackend = new \Sabre\DAVACL\PrincipalBackend\PDO($GLOBALS["DB"]->getPDO());
$carddavBackend = new \Sabre\CardDAV\Backend\PDO($GLOBALS["DB"]->getPDO()); 

# Setting up the directory tree
$nodes = array(
    new \Sabre\DAVACL\PrincipalCollection($principalBackend),
    new \Sabre\CardDAV\AddressBookRoot($principalBackend, $carddavBackend),
);

# The object tree needs in turn to be passed to the server class
$server = new \Sabre\DAV\Server($nodes);
$server->setBaseUri(BAIKAL_CARD_BASEURI);

# Plugins 
$server->addPlugin(new \Sabre\DAV\Auth\Plugin($authBackend, BAIKAL_AUTH_REALM));
$server->addPlugin(new \Sabre\CardDAV\Plugin());
$server->addPlugin(new \Sabre\DAVACL\Plugin());

# And off we go!
$server->exec();
