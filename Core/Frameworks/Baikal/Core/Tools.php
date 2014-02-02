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
		$aZones = array(
			"Africa/Abidjan",
			"Africa/Accra",
			"Africa/Addis_Ababa",
			"Africa/Algiers",
			"Africa/Asmara",
			"Africa/Bamako",
			"Africa/Bangui",
			"Africa/Banjul",
			"Africa/Bissau",
			"Africa/Blantyre",
			"Africa/Brazzaville",
			"Africa/Bujumbura",
			"Africa/Cairo",
			"Africa/Casablanca",
			"Africa/Ceuta",
			"Africa/Conakry",
			"Africa/Dakar",
			"Africa/Dar_es_Salaam",
			"Africa/Djibouti",
			"Africa/Douala",
			"Africa/El_Aaiun",
			"Africa/Freetown",
			"Africa/Gaborone",
			"Africa/Harare",
			"Africa/Johannesburg",
			"Africa/Juba",
			"Africa/Kampala",
			"Africa/Khartoum",
			"Africa/Kigali",
			"Africa/Kinshasa",
			"Africa/Lagos",
			"Africa/Libreville",
			"Africa/Lome",
			"Africa/Luanda",
			"Africa/Lubumbashi",
			"Africa/Lusaka",
			"Africa/Malabo",
			"Africa/Maputo",
			"Africa/Maseru",
			"Africa/Mbabane",
			"Africa/Mogadishu",
			"Africa/Monrovia",
			"Africa/Nairobi",
			"Africa/Ndjamena",
			"Africa/Niamey",
			"Africa/Nouakchott",
			"Africa/Ouagadougou",
			"Africa/Porto-Novo",
			"Africa/Sao_Tome",
			"Africa/Tripoli",
			"Africa/Tunis",
			"Africa/Windhoek",
			"America/Adak",
			"America/Anchorage",
			"America/Anguilla",
			"America/Antigua",
			"America/Araguaina",
			"America/Argentina/Buenos_Aires",
			"America/Argentina/Catamarca",
			"America/Argentina/Cordoba",
			"America/Argentina/Jujuy",
			"America/Argentina/La_Rioja",
			"America/Argentina/Mendoza",
			"America/Argentina/Rio_Gallegos",
			"America/Argentina/Salta",
			"America/Argentina/San_Juan",
			"America/Argentina/San_Luis",
			"America/Argentina/Tucuman",
			"America/Argentina/Ushuaia",
			"America/Aruba",
			"America/Asuncion",
			"America/Atikokan",
			"America/Bahia",
			"America/Barbados",
			"America/Belem",
			"America/Belize",
			"America/Blanc-Sablon",
			"America/Boa_Vista",
			"America/Bogota",
			"America/Boise",
			"America/Cambridge_Bay",
			"America/Campo_Grande",
			"America/Cancun",
			"America/Caracas",
			"America/Cayenne",
			"America/Cayman",
			"America/Chicago",
			"America/Chihuahua",
			"America/Costa_Rica",
			"America/Cuiaba",
			"America/Curacao",
			"America/Danmarkshavn",
			"America/Dawson",
			"America/Dawson_Creek",
			"America/Denver",
			"America/Detroit",
			"America/Dominica",
			"America/Edmonton",
			"America/Eirunepe",
			"America/El_Salvador",
			"America/Felipe_Carrillo",
			"America/Fortaleza",
			"America/Glace_Bay",
			"America/Godthab",
			"America/Goose_Bay",
			"America/Grand_Turk",
			"America/Grenada",
			"America/Guadeloupe",
			"America/Guatemala",
			"America/Guayaquil",
			"America/Guyana",
			"America/Halifax",
			"America/Havana",
			"America/Hermosillo",
			"America/Indiana/Indianapolis",
			"America/Indiana/Knox",
			"America/Indiana/Marengo",
			"America/Indiana/Petersburg",
			"America/Indiana/Tell_City",
			"America/Indiana/Vevay",
			"America/Indiana/Vincennes",
			"America/Indiana/Winamac",
			"America/Inuvik",
			"America/Iqaluit",
			"America/Jamaica",
			"America/Juneau",
			"America/Kentucky/Louisville",
			"America/Kentucky/Monticello",
			"America/La_Paz",
			"America/Lima",
			"America/Los_Angeles",
			"America/Maceio",
			"America/Managua",
			"America/Manaus",
			"America/Marigot",
			"America/Martinique",
			"America/Matamoros",
			"America/Mazatlan",
			"America/Menominee",
			"America/Merida",
			"America/Mexico_City",
			"America/Miquelon",
			"America/Moncton",
			"America/Monterrey",
			"America/Montevideo",
			"America/Montreal",
			"America/Montserrat",
			"America/Nassau",
			"America/New_York",
			"America/Nipigon",
			"America/Nome",
			"America/Noronha",
			"America/North_Dakota/Center",
			"America/North_Dakota/New_Salem",
			"America/Ojinaga",
			"America/Panama",
			"America/Pangnirtung",
			"America/Paramaribo",
			"America/Phoenix",
			"America/Port-au-Prince",
			"America/Porto_Velho",
			"America/Port_of_Spain",
			"America/Puerto_Rico",
			"America/Rainy_River",
			"America/Rankin_Inlet",
			"America/Recife",
			"America/Regina",
			"America/Resolute",
			"America/Rio_Branco",
			"America/Santarem",
			"America/Santa_Isabel",
			"America/Santiago",
			"America/Santo_Domingo",
			"America/Sao_Paulo",
			"America/Scoresbysund",
			"America/Shiprock",
			"America/St_Barthelemy",
			"America/St_Johns",
			"America/St_Kitts",
			"America/St_Lucia",
			"America/St_Thomas",
			"America/St_Vincent",
			"America/Swift_Current",
			"America/Tegucigalpa",
			"America/Thule",
			"America/Thunder_Bay",
			"America/Tijuana",
			"America/Toronto",
			"America/Tortola",
			"America/Vancouver",
			"America/Whitehorse",
			"America/Winnipeg",
			"America/Yakutat",
			"America/Yellowknife",
			"Antarctica/Casey",
			"Antarctica/Davis",
			"Antarctica/DumontDUrville",
			"Antarctica/Mawson",
			"Antarctica/McMurdo",
			"Antarctica/Palmer",
			"Antarctica/Rothera",
			"Antarctica/South_Pole",
			"Antarctica/Syowa",
			"Antarctica/Vostok",
			"Arctic/Longyearbyen",
			"Asia/Aden",
			"Asia/Almaty",
			"Asia/Amman",
			"Asia/Anadyr",
			"Asia/Aqtau",
			"Asia/Aqtobe",
			"Asia/Ashgabat",
			"Asia/Baghdad",
			"Asia/Bahrain",
			"Asia/Baku",
			"Asia/Bangkok",
			"Asia/Beirut",
			"Asia/Bishkek",
			"Asia/Brunei",
			"Asia/Choibalsan",
			"Asia/Chongqing",
			"Asia/Colombo",
			"Asia/Damascus",
			"Asia/Dhaka",
			"Asia/Dili",
			"Asia/Dubai",
			"Asia/Dushanbe",
			"Asia/Gaza",
			"Asia/Harbin",
			"Asia/Hong_Kong",
			"Asia/Hovd",
			"Asia/Ho_Chi_Minh",
			"Asia/Irkutsk",
			"Asia/Jakarta",
			"Asia/Jayapura",
			"Asia/Jerusalem",
			"Asia/Kabul",
			"Asia/Kamchatka",
			"Asia/Karachi",
			"Asia/Kashgar",
			"Asia/Kathmandu",
			"Asia/Kolkata",
			"Asia/Krasnoyarsk",
			"Asia/Kuala_Lumpur",
			"Asia/Kuching",
			"Asia/Kuwait",
			"Asia/Macau",
			"Asia/Magadan",
			"Asia/Makassar",
			"Asia/Manila",
			"Asia/Muscat",
			"Asia/Nicosia",
			"Asia/Novokuznetsk",
			"Asia/Novosibirsk",
			"Asia/Omsk",
			"Asia/Oral",
			"Asia/Phnom_Penh",
			"Asia/Pontianak",
			"Asia/Pyongyang",
			"Asia/Qatar",
			"Asia/Qyzylorda",
			"Asia/Rangoon",
			"Asia/Riyadh",
			"Asia/Sakhalin",
			"Asia/Samarkand",
			"Asia/Seoul",
			"Asia/Shanghai",
			"Asia/Singapore",
			"Asia/Taipei",
			"Asia/Tashkent",
			"Asia/Tbilisi",
			"Asia/Tehran",
			"Asia/Thimphu",
			"Asia/Tokyo",
			"Asia/Ulaanbaatar",
			"Asia/Urumqi",
			"Asia/Vientiane",
			"Asia/Vladivostok",
			"Asia/Yakutsk",
			"Asia/Yekaterinburg",
			"Asia/Yerevan",
			"Atlantic/Azores",
			"Atlantic/Bermuda",
			"Atlantic/Canary",
			"Atlantic/Cape_Verde",
			"Atlantic/Faroe",
			"Atlantic/Madeira",
			"Atlantic/Reykjavik",
			"Atlantic/South_Georgia",
			"Atlantic/Stanley",
			"Atlantic/St_Helena",
			"Australia/Adelaide",
			"Australia/Brisbane",
			"Australia/Broken_Hill",
			"Australia/Currie",
			"Australia/Darwin",
			"Australia/Eucla",
			"Australia/Hobart",
			"Australia/Lindeman",
			"Australia/Lord_Howe",
			"Australia/Melbourne",
			"Australia/Perth",
			"Australia/Sydney",
			"Europe/Amsterdam",
			"Europe/Andorra",
			"Europe/Athens",
			"Europe/Belgrade",
			"Europe/Berlin",
			"Europe/Bratislava",
			"Europe/Brussels",
			"Europe/Bucharest",
			"Europe/Budapest",
			"Europe/Chisinau",
			"Europe/Copenhagen",
			"Europe/Dublin",
			"Europe/Gibraltar",
			"Europe/Guernsey",
			"Europe/Helsinki",
			"Europe/Isle_of_Man",
			"Europe/Istanbul",
			"Europe/Jersey",
			"Europe/Kaliningrad",
			"Europe/Kiev",
			"Europe/Lisbon",
			"Europe/Ljubljana",
			"Europe/London",
			"Europe/Luxembourg",
			"Europe/Madrid",
			"Europe/Malta",
			"Europe/Mariehamn",
			"Europe/Minsk",
			"Europe/Monaco",
			"Europe/Moscow",
			"Europe/Oslo",
			"Europe/Paris",
			"Europe/Podgorica",
			"Europe/Prague",
			"Europe/Riga",
			"Europe/Rome",
			"Europe/Samara",
			"Europe/San_Marino",
			"Europe/Sarajevo",
			"Europe/Simferopol",
			"Europe/Skopje",
			"Europe/Sofia",
			"Europe/Stockholm",
			"Europe/Tallinn",
			"Europe/Tirane",
			"Europe/Uzhgorod",
			"Europe/Vaduz",
			"Europe/Vatican",
			"Europe/Vienna",
			"Europe/Vilnius",
			"Europe/Volgograd",
			"Europe/Warsaw",
			"Europe/Zagreb",
			"Europe/Zaporozhye",
			"Europe/Zurich",
			"Indian/Antananarivo",
			"Indian/Chagos",
			"Indian/Christmas",
			"Indian/Cocos",
			"Indian/Comoro",
			"Indian/Kerguelen",
			"Indian/Mahe",
			"Indian/Maldives",
			"Indian/Mauritius",
			"Indian/Mayotte",
			"Indian/Reunion",
			"Pacific/Apia",
			"Pacific/Auckland",
			"Pacific/Chatham",
			"Pacific/Easter",
			"Pacific/Efate",
			"Pacific/Enderbury",
			"Pacific/Fakaofo",
			"Pacific/Fiji",
			"Pacific/Funafuti",
			"Pacific/Galapagos",
			"Pacific/Gambier",
			"Pacific/Guadalcanal",
			"Pacific/Guam",
			"Pacific/Honolulu",
			"Pacific/Johnston",
			"Pacific/Kiritimati",
			"Pacific/Kosrae",
			"Pacific/Kwajalein",
			"Pacific/Majuro",
			"Pacific/Marquesas",
			"Pacific/Midway",
			"Pacific/Nauru",
			"Pacific/Niue",
			"Pacific/Norfolk",
			"Pacific/Noumea",
			"Pacific/Pago_Pago",
			"Pacific/Palau",
			"Pacific/Pitcairn",
			"Pacific/Ponape",
			"Pacific/Port_Moresby",
			"Pacific/Rarotonga",
			"Pacific/Saipan",
			"Pacific/Tahiti",
			"Pacific/Tarawa",
			"Pacific/Tongatapu",
			"Pacific/Truk",
			"Pacific/Wake",
			"Pacific/Wallis",
		);
		
		reset($aZones);
		return $aZones;
	}
}