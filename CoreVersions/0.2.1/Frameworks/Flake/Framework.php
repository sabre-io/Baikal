<?php
#################################################################
#  Copyright notice
#
#  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://flake.codr.fr
#
#  This script is part of the Flake project. The Flake
#  project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace Flake;
require_once(PROJECT_PATH_ROOT . "Core/Frameworks/Flake/Core/Framework.php");	# Manual require as Classloader not included yet

class Framework extends \Flake\Core\Framework {
	
	public static function rmBeginSlash($sString) {
		if(substr($sString, 0, 1) === "/") {
			$sString = substr($sString, 1);
		}

		return $sString;
	}

	public static function rmEndSlash($sString) {
		if(substr($sString, -1) === "/") {
			$sString = substr($sString, 0, -1);
		}

		return $sString;
	}

	public static function appendSlash($sString) {
		if(substr($sString, -1) !== "/") {
			$sString .= "/";
		}

		return $sString;
	}

	public static function prependSlash($sString) {
		if(substr($sString, 0, 1) !== "/") {
			$sString = "/" . $sString;
		}

		return $sString;
	}
	
	public static function bootstrap() {
		
		# Asserting PHP 5.3.0+
		if(version_compare(PHP_VERSION, '5.3.0', '<')) {
			die('Flake Fatal Error: Flake requires PHP 5.3.0+ to run properly. Your version is: ' . PHP_VERSION . '.');
		}
		
		# Define safehash salt
		define("PROJECT_SAFEHASH_SALT", "strong-secret-salt");

		# Define absolute server path to Flake Framework
		define("FLAKE_PATH_ROOT", PROJECT_PATH_ROOT . "Core/Frameworks/Flake/");	# ./

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

		#################################################################################################

		# determine Flake install root path
		# not using realpath here to avoid symlinks resolution

		define("PROJECT_PATH_CORE", PROJECT_PATH_ROOT . "Core/");
		define("PROJECT_PATH_CORERESOURCES", PROJECT_PATH_CORE . "Resources/");
		define("PROJECT_PATH_SPECIFIC", PROJECT_PATH_ROOT . "Specific/");
		define("PROJECT_PATH_FRAMEWORKS", PROJECT_PATH_CORE . "Frameworks/");
		define("PROJECT_PATH_WWWROOT", PROJECT_PATH_CORE . "WWWRoot/");

		# Activate Flake class loader
		require_once(FLAKE_PATH_ROOT . 'Core/ClassLoader.php');
		\Flake\Core\ClassLoader::register();

		require_once(PROJECT_PATH_CORE . "Distrib.php");

		if(PROJECT_PACKAGE === "regular") {
			define("PROJECT_PATH_DOCUMENTROOT", PROJECT_PATH_ROOT . "html/");
		} elseif(PROJECT_PACKAGE === "flat") {
			define("PROJECT_PATH_DOCUMENTROOT", PROJECT_PATH_ROOT);
		} else {
			throw new \Exception("Unrecognized PROJECT_PACKAGE value.");
 		}

		# Determine PROJECT_URI
		$sScript = substr($_SERVER["SCRIPT_FILENAME"], strlen($_SERVER["DOCUMENT_ROOT"]));
		$sDirName = self::appendSlash(dirname($sScript));
		$sBaseUrl = self::appendSlash(substr($sDirName, 0, -1 * strlen(PROJECT_CONTEXT_BASEURI)));
		$sProtocol = \Flake\Util\Tools::getCurrentProtocol();
		define("PROJECT_BASEURI", $sBaseUrl);
		define("PROJECT_URI", $sProtocol . "://" . self::rmEndSlash($_SERVER["HTTP_HOST"]) . $sBaseUrl);
		unset($sScript); unset($sDirName); unset($sBaseUrl); unset($sProtocol);

		#################################################################################################
		
		require_once(FLAKE_PATH_ROOT . 'Util/Twig/lib/Twig/Autoloader.php');
		\Twig_Autoloader::register();

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

		$GLOBALS["TEMPLATESTACK"] = array();

		$aUrlInfo = parse_url(PROJECT_URI);
		define("FLAKE_DOMAIN", $_SERVER["HTTP_HOST"]);
		define("FLAKE_URIPATH", \Flake\Util\Tools::stripBeginSlash($aUrlInfo["path"]));
		unset($aUrlInfo);
		
		
		# Include Project config
		$sConfigPath = PROJECT_PATH_SPECIFIC . "config.php";
		$sConfigSystemPath = PROJECT_PATH_SPECIFIC . "config.system.php";
		
		if(file_exists($sConfigPath)) {
			require_once($sConfigPath);
		}
		
		if(file_exists($sConfigSystemPath)) {
			require_once($sConfigSystemPath);
		}
		
		self::initDb();
	}
	
	protected static function initDb() {
		
		if(defined("PROJECT_DB_MYSQL") && PROJECT_DB_MYSQL === TRUE) {
			self::initDbMysql();
		} else {
			self::initDbSqlite();
		}
	}
	
	protected static function initDbSqlite() {
		# Asserting DB filepath is set
		if(!defined("PROJECT_SQLITE_FILE")) {
			return;
		}
		
		# Asserting DB file exists
		if(!file_exists(PROJECT_SQLITE_FILE)) {
			die("<h3>DB file does not exist. To create it, please copy '<span style='font-family: monospace; background: yellow;'>Core/Resources/Db/db.empty.sqlite</span>' to '<span style='font-family: monospace;background: yellow;'>" . PROJECT_SQLITE_FILE . "</span>'</h3>");
		}
		
		# Asserting DB file is readable
		if(!is_readable(PROJECT_SQLITE_FILE)) {
			die("<h3>DB file is not readable. Please give read permissions on file '<span style='font-family: monospace; background: yellow;'>" . PROJECT_SQLITE_FILE . "</span>'</h3>");
		}
		
		# Asserting DB file is writable
		if(!is_writable(PROJECT_SQLITE_FILE)) {
			die("<h3>DB file is not writable. Please give write permissions on file '<span style='font-family: monospace; background: yellow;'>" . PROJECT_SQLITE_FILE . "</span>'</h3>");
		}
		
		if(file_exists(PROJECT_SQLITE_FILE) && is_readable(PROJECT_SQLITE_FILE) && !isset($GLOBALS["DB"])) {
			$GLOBALS["DB"] = new \Flake\Core\Database\Sqlite(PROJECT_SQLITE_FILE);
		}
	}
	
	protected static function initDbMysql() {
		
		if(!defined("PROJECT_DB_MYSQL_HOST")) {
			die("<h3>The constant PROJECT_DB_MYSQL_HOST, containing the MySQL host name, is not set.<br />You should set it in Specific/config.system.php</h3>");
		}
		
		if(!defined("PROJECT_DB_MYSQL_DBNAME")) {
			die("<h3>The constant PROJECT_DB_MYSQL_DBNAME, containing the MySQL database name, is not set.<br />You should set it in Specific/config.system.php</h3>");
		}
		
		if(!defined("PROJECT_DB_MYSQL_USERNAME")) {
			die("<h3>The constant PROJECT_DB_MYSQL_USERNAME, containing the MySQL database username, is not set.<br />You should set it in Specific/config.system.php</h3>");
		}
		
		if(!defined("PROJECT_DB_MYSQL_PASSWORD")) {
			die("<h3>The constant PROJECT_DB_MYSQL_PASSWORD, containing the MySQL database password, is not set.<br />You should set it in Specific/config.system.php</h3>");
		}
		
		try {
			$GLOBALS["DB"] = new \Flake\Core\Database\Mysql(
				PROJECT_DB_MYSQL_HOST,
				PROJECT_DB_MYSQL_DBNAME,
				PROJECT_DB_MYSQL_USERNAME,
				PROJECT_DB_MYSQL_PASSWORD
			);
		} catch(\Exception $e) {
			die("<h3>Baïkal was not able to establish a connexion to the configured MySQL database (as configured in Specific/config.system.php).</h3>");
		}
		
		# We now setup the connexion to use UTF8
		$GLOBALS["DB"]->query("SET NAMES UTF8");
	}
}