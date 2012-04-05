<?php

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

$GLOBALS["DB"] = new \Flake\Core\Database\Sqlite();
$GLOBALS["DB"]->init(FLAKE_DB_FILEPATH);

$GLOBALS["TEMPLATESTACK"] = array();