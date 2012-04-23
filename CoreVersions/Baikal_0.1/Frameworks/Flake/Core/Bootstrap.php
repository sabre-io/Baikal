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

if(!defined('LF')) {
	define('LF', chr(10));
}

if(!defined('CR')) {
	define('CR', chr(13));
}

if(array_key_exists("SERVER_NAME", $_SERVER) && $_SERVER["SERVER_NAME"] === "mongoose") {
	define("MONGOOSE_SERVER", TRUE);
} else {
	define("MONGOOSE_SERVER", FALSE);
}

define("FLAKE_PATH_ROOT", dirname(dirname(__FILE__)) . "/");	# ../

// les notices PHP ne sont pas affichées
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL & ~E_NOTICE);

if(!function_exists("appendSlash")) {
	function appendSlash($sPath) {
		if($sPath{strlen($sPath) - 1} !== "/") {
			$sPath .= "/";
		}
	
		return $sPath;
	}
}

if(!function_exists("debug")) {
	function debug($mVar, $sHeader=0) {
		\Flake\Util\Tools::debug($mVar, $sHeader);
	}
}

require_once(FLAKE_PATH_ROOT . 'Core/ClassLoader.php');
\Flake\Core\ClassLoader::register();

# Include Flake Framework config
require_once(FLAKE_PATH_ROOT . "config.php");

# Determine Router class
$GLOBALS["ROUTER"] = \Flake\Util\Tools::router();

if(!\Flake\Util\Tools::isCliPhp()) {
	ini_set("html_errors", TRUE);
	session_start();
}

setlocale(LC_ALL, FLAKE_LOCALE);
date_default_timezone_set(FLAKE_TIMEZONE);

if(defined("FLAKE_DB_FILEPATH") && file_exists(FLAKE_DB_FILEPATH) && is_readable(FLAKE_DB_FILEPATH)) {
	$GLOBALS["DB"] = new \Flake\Core\Database\Sqlite();
	$GLOBALS["DB"]->init(FLAKE_DB_FILEPATH);
}

$GLOBALS["TEMPLATESTACK"] = array();

$aUrlInfo = parse_url(FLAKE_URI);
define("FLAKE_DOMAIN", $_SERVER["HTTP_HOST"]);
define("FLAKE_URIPATH", \Flake\Util\Tools::stripBeginSlash($aUrlInfo["path"]));
unset($aUrlInfo);