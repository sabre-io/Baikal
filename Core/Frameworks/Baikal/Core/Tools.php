<?php
#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://baikal-server.com
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
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

namespace Baikal\Core;

class Tools {
	public static function &db() {
		return $GLOBALS["pdo"];
	}
	
	public static function assertEnvironmentIsOk() {
		# Asserting Baikal Context
		if(!defined("BAIKAL_CONTEXT") || BAIKAL_CONTEXT !== TRUE) {
			die("Bootstrap.php may not be included outside the Baikal context");
		}
		
		# Asserting PDO
		if(!defined('PDO::ATTR_DRIVER_NAME')) {
			die('Baikal Fatal Error: PDO is unavailable. It\'s required by Baikal.');
		}

		# Asserting PDO::SQLite or PDO::MySQL
		$aPDODrivers = \PDO::getAvailableDrivers();
		if(!in_array('sqlite', $aPDODrivers) && !in_array('mysql', $aPDODrivers)) {
			die('<strong>Baikal Fatal Error</strong>: Both <strong>PDO::sqlite</strong> and <strong>PDO::mysql</strong> are unavailable. One of them at least is required by Baikal.');
		}
	}
	
	public static function configureEnvironment() {
		set_exception_handler('\Baikal\Core\Tools::handleException');
		ini_set("error_reporting", E_ALL);
	}
	
	public static function handleException($exception) {
		echo "<pre>" . $exception . "<pre>";
	}
	
	public static function assertBaikalIsOk() {
		
		# DB connexion has not been asserted earlier by Flake, to give us a chance to trigger the install tool
		# We assert it right now
		if(!\Flake\Framework::isDBInitialized() && (!defined("BAIKAL_CONTEXT_INSTALL") || BAIKAL_CONTEXT_INSTALL === FALSE)) {
			throw new \Exception("<strong>Fatal error</strong>: no connection to a database is available.");
		}
		
		# Asserting that the database is structurally complete
		#if(($aMissingTables = self::isDBStructurallyComplete($GLOBALS["DB"])) !== TRUE) {
		#	throw new \Exception("<strong>Fatal error</strong>: Database is not structurally complete; missing tables are: <strong>" . implode("</strong>, <strong>", $aMissingTables) . "</strong>");
		#}
		
		# Asserting config file exists
		if(!file_exists(PROJECT_PATH_SPECIFIC . "config.php")) {
			throw new \Exception("Specific/config.php does not exist. Please use the Install tool to create it.");
		}
		
		# Asserting config file is readable
		if(!is_readable(PROJECT_PATH_SPECIFIC . "config.php")) {
			throw new \Exception("Specific/config.php is not readable. Please give read permissions to httpd user on file 'Specific/config.php'.");
		}
		
		# Asserting config file is writable
		if(!is_writable(PROJECT_PATH_SPECIFIC . "config.php")) {
			throw new \Exception("Specific/config.php is not writable. Please give write permissions to httpd user on file 'Specific/config.php'.");
		}
		
		# Asserting system config file exists
		if(!file_exists(PROJECT_PATH_SPECIFIC . "config.system.php")) {
			throw new \Exception("Specific/config.system.php does not exist. Please use the Install tool to create it.");
		}
		
		# Asserting system config file is readable
		if(!is_readable(PROJECT_PATH_SPECIFIC . "config.system.php")) {
			throw new \Exception("Specific/config.system.php is not readable. Please give read permissions to httpd user on file 'Specific/config.system.php'.");
		}
		
		# Asserting system config file is writable
		if(!is_writable(PROJECT_PATH_SPECIFIC . "config.system.php")) {
			throw new \Exception("Specific/config.system.php is not writable. Please give write permissions to httpd user on file 'Specific/config.system.php'.");
		}
	}

	public static function getRequiredTablesList() {
		return array(
			"addressbooks",
			"calendarobjects",
			"calendars",
			"cards",
			"groupmembers",
			"locks",
			"principals",
			"users",
		);
	}
	
	public static function isDBStructurallyComplete(\Flake\Core\Database $oDB) {
		
		$aRequiredTables = self::getRequiredTablesList();
		$aPresentTables = $oDB->tables();

		$aIntersect = array_intersect($aRequiredTables, $aPresentTables);
		if(count($aIntersect) !== count($aRequiredTables)) {
			return array_diff($aRequiredTables, $aIntersect);
		}
		
		return TRUE;
	}
	
	public static function bashPrompt($prompt) {
		print $prompt;
		@flush();
		@ob_flush();
		$confirmation = @trim(fgets(STDIN));
		return $confirmation;
	}
	
	public static function bashPromptSilent($prompt = "Enter Password:") {
		$command = "/usr/bin/env bash -c 'echo OK'";

		if(rtrim(shell_exec($command)) !== 'OK') {
			trigger_error("Can't invoke bash");
			return;
		}

		$command = "/usr/bin/env bash -c 'read -s -p \""
		. addslashes($prompt)
		. "\" mypassword && echo \$mypassword'";

		$password = rtrim(shell_exec($command));
		echo "\n";
		return $password;
	}
	
	public static function getCopyrightNotice($sLinePrefixChar = "#", $sLineSuffixChar = "", $sOpening = FALSE, $sClosing = FALSE) {
		
		if($sOpening === FALSE) {
			$sOpening = str_repeat("#", 78);
		}
		
		if($sClosing === FALSE) {
			$sClosing = str_repeat("#", 78);
		}
		
		$iYear = date("Y");
		
		$sCode =<<<CODE
Copyright notice

(c) {$iYear} Jérôme Schneider <mail@jeromeschneider.fr>
All rights reserved

http://baikal-server.com

This script is part of the Baïkal Server project. The Baïkal
Server project is free software; you can redistribute it
and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

The GNU General Public License can be found at
http://www.gnu.org/copyleft/gpl.html.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

This copyright notice MUST APPEAR in all copies of the script!
CODE;
		$sCode = "\n" . trim($sCode) . "\n";
		$aCode = explode("\n", $sCode);
		foreach(array_keys($aCode) as $iLineNum) {
			$aCode[$iLineNum] = trim($sLinePrefixChar . "\t" . $aCode[$iLineNum]);
		}
		
		if(trim($sOpening) !== "") {
			array_unshift($aCode, $sOpening);
		}
		
		if(trim($sClosing) !== "") {
			$aCode[] = $sClosing;
		}
		
		return implode("\n", $aCode);
	}
	
	public static function timezones() {
		$aZones = \DateTimeZone::listIdentifiers();
		
		reset($aZones);
		return $aZones;
	}
}
