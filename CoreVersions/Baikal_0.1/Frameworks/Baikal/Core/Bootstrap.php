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


function rmBeginSlash($sString) {
	if(substr($sString, 0, 1) === "/") {
		$sString = substr($sString, 1);
	}
	
	return $sString;
}

function rmEndSlash($sString) {
	if(substr($sString, -1) === "/") {
		$sString = substr($sString, 0, -1);
	}
	
	return $sString;
}

function appendSlash($sString) {
	if(substr($sString, -1) !== "/") {
		$sString .= "/";
	}
	
	return $sString;
}

function prependSlash($sString) {
	if(substr($sString, 0, 1) !== "/") {
		$sString = "/" . $sString;
	}
	
	return $sString;
}

function installTool() {	
	if(defined("BAIKAL_CONTEXT_INSTALL") && BAIKAL_CONTEXT_INSTALL === TRUE) {
		return;
	} else {
		$sInstallToolUrl = BAIKAL_URI . "admin/install/";
		header("Location: " . $sInstallToolUrl);
		exit(0);
	}
}

# Asserting PHP 5.3.0+
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	die('Baikal Fatal Error: Baikal requires PHP 5.3.0+ to run properly. You version is: ' . PHP_VERSION . '.');
}

# Registering Baikal classloader
define("BAIKAL_PATH_FRAMEWORKROOT", dirname(dirname(__FILE__)) . "/");
require_once(BAIKAL_PATH_FRAMEWORKROOT . '/Core/ClassLoader.php');
\Baikal\Core\ClassLoader::register();

\Baikal\Core\Tools::assertEnvironmentIsOk();

# determine Baïkal install root path
# not using realpath here to avoid symlinks resolution

define("BAIKAL_PATH_ROOT", dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/");	# ../../../../../
define("BAIKAL_PATH_CORE", BAIKAL_PATH_ROOT . "Core/");
define("BAIKAL_PATH_SPECIFIC", BAIKAL_PATH_ROOT . "Specific/");
define("BAIKAL_PATH_FRAMEWORKS", BAIKAL_PATH_CORE . "Frameworks/");
define("BAIKAL_PATH_WWWROOT", BAIKAL_PATH_CORE . "WWWRoot/");

# Define path to Baïkal SQLite file
define("BAIKAL_SQLITE_FILE", BAIKAL_PATH_SPECIFIC . "db/baikal.sqlite");

require_once(BAIKAL_PATH_CORE . "Distrib.php");

# Determine BAIKAL_URI
$sScript = substr($_SERVER["SCRIPT_FILENAME"], strlen($_SERVER["DOCUMENT_ROOT"]));
$sDirName = appendSlash(dirname($sScript));
$sBaseUrl = appendSlash(substr($sDirName, 0, -1 * strlen(BAIKAL_CONTEXT_BASEURI)));
$aParts = explode("/", $_SERVER["SERVER_PROTOCOL"]);
$sProtocol = strtolower(array_shift($aParts));
define("BAIKAL_BASEURI", $sBaseUrl);
define("BAIKAL_URI", $sProtocol . "://" . rmEndSlash($_SERVER["HTTP_HOST"]) . $sBaseUrl);
unset($sScript); unset($sDirName); unset($sBaseUrl); unset($sProtocol); unset($aParts);

# Bootstrap Flake
require_once(BAIKAL_PATH_FRAMEWORKS . "Flake/Core/Bootstrap.php");

# Check that a config file exists
if(
	!file_exists(BAIKAL_PATH_SPECIFIC . "config.php") ||
	!file_exists(BAIKAL_PATH_SPECIFIC . "config.system.php")
) {
	installTool();
} else {
	require_once(BAIKAL_PATH_SPECIFIC . "config.php");
	require_once(BAIKAL_PATH_SPECIFIC . "config.system.php");
	date_default_timezone_set(BAIKAL_TIMEZONE);
	
	# Check that Baïkal is already configured
	if(!defined("BAIKAL_CONFIGURED_VERSION")) {
		installTool();
		
	} else {
		
		# Check that running version matches configured version
		if(version_compare(BAIKAL_VERSION, BAIKAL_CONFIGURED_VERSION) > 0) {
			installTool();
			
		} else {

			# Check that admin password is set
			if(!defined("BAIKAL_ADMIN_PASSWORDHASH")) {
				installTool();
			}
			
			\Baikal\Core\Tools::assertBaikalIsOk();

			# Establishing connection with database
			$GLOBALS["DB"] = new \Flake\Core\Database\Sqlite(BAIKAL_SQLITE_FILE);

			$bShouldCheckEnv = ((!defined("BAIKAL_CONTEXT_CLI") || BAIKAL_CONTEXT_CLI === FALSE) && (!defined("BAIKAL_CONTEXT_ADMIN") || BAIKAL_CONTEXT_ADMIN === FALSE));

			if($bShouldCheckEnv === TRUE) {
				# Mapping PHP errors to exceptions; needed by SabreDAV
				function exception_error_handler($errno, $errstr, $errfile, $errline) {
					throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
				}

				set_error_handler("exception_error_handler");
			}

			unset($bShouldCheckEnv);

			# SabreDAV Autoloader 
			require_once(BAIKAL_PATH_SABREDAV . 'autoload.php');
		}
	}
}