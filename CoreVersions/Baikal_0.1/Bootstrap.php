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

if(!defined("BAIKAL_CONTEXT") || BAIKAL_CONTEXT !== TRUE) {
	die("Bootstrap.php may not be included outside the Baikal context");
}

if(version_compare(PHP_VERSION, '5.2.0', '<')) {
	die('Baikal Fatal Error: Baikal requires PHP 5.2.0+ to run properly. You version is: ' . PHP_VERSION . '.');
}

if(!defined('PDO::ATTR_DRIVER_NAME')) {
	die('Baikal Fatal Error: PDO is unavailable. It\'s required by Baikal.');
}

if(!in_array('sqlite', PDO::getAvailableDrivers())) {
	die('Baikal Fatal Error: PDO::sqlite is unavailable. It\'s required by Baikal.');
}

# determine Baïkal install root path
# adaptive, either ../../ or ../ relative to the Bootstrap
# not using realpath here as it resolves symlinks

$sTemp = dirname(dirname(__FILE__)) . "/";	#../ if Baïkal distrib is at the same level than "Core" symlink
if(@file_exists($sTemp) && (@is_dir($sTemp . "Core") || @is_link($sTemp . "Core"))) {
	define("BAIKAL_PATH_ROOT", $sTemp);	# ../
} else {
	$sTemp = dirname($sTemp) . "/"; # ../../ relative to bootstrap
	if(@file_exists($sTemp) && (@is_dir($sTemp . "Core") || @is_link($sTemp . "Core"))) {
		define("BAIKAL_PATH_ROOT", $sTemp);	# ../../
	} else {
		die('Baikal Fatal Error: Unable to determine Baikal root path.');
	}
}

define("BAIKAL_PATH_CORE", BAIKAL_PATH_ROOT . "Core/");
define("BAIKAL_PATH_SPECIFIC", BAIKAL_PATH_ROOT . "Specific/");
define("BAIKAL_PATH_FRAMEWORKS", BAIKAL_PATH_CORE . "Frameworks/");
define("BAIKAL_PATH_WWWROOT", BAIKAL_PATH_CORE . "WWWRoot/");

require_once(BAIKAL_PATH_SPECIFIC . "config.php");
require_once(BAIKAL_PATH_SPECIFIC . "config.system.php");

date_default_timezone_set(BAIKAL_TIMEZONE);

# Check if DB exists
if(!file_exists(BAIKAL_SQLITE_FILE)) {
	die("DB file does not exist.<br />To create it, please copy '<b>Core/Resources/baikal.empty.sqlite</b>' to '<b>Specific/db/baikal.sqlite</b>'.<br /><span style='color: red; font-weight: bold'>Please note the change in the file name while doing so</span> (from 'baikal.empty.sqlite' to 'baikal.sqlite').");
}

# Database
$pdo = new PDO('sqlite:' . BAIKAL_SQLITE_FILE);
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$bShouldCheckEnv = ((!defined("BAIKAL_CONTEXT_CLI") || BAIKAL_CONTEXT_CLI === FALSE) && (!defined("BAIKAL_CONTEXT_ADMIN") || BAIKAL_CONTEXT_ADMIN === FALSE));

# Check if at least one user exists
if($bShouldCheckEnv === TRUE) {
	if(($iNbUsers = intval($pdo->query('SELECT count(*) FROM users')->fetchColumn())) === 0) {
		die("No users are defined.<br />To create a user, you can use the helper <b>Core/Scripts/adduser.php</b> (requires command line access)");
	}	
}


if($bShouldCheckEnv === TRUE) {
	# Mapping PHP errors to exceptions
	function exception_error_handler($errno, $errstr, $errfile, $errline) {
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	
	set_error_handler("exception_error_handler");
} else {
	error_reporting(E_ALL ^ E_NOTICE);
}

// Autoloader 
require_once(BAIKAL_PATH_SABREDAV . 'autoload.php');
