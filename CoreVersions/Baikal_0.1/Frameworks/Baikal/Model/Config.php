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

namespace Baikal\Model;

class Config extends \Flake\Core\Model\NoDb {
	
	protected $sConfigFilePath = "";
	protected $aConstants = array(
		"BAIKAL_TIMEZONE" => array(
			"type" => "string",
		),
#		"BAIKAL_URI" => array(
#			"type" => "string",
#		),
		"BAIKAL_CARD_ENABLED" => array(
			"type" => "boolean",
		),
		"BAIKAL_CAL_ENABLED" => array(
			"type" => "boolean",
		),
		"BAIKAL_ADMIN_ENABLED" => array(
			"type" => "boolean",
		),
		"BAIKAL_STANDALONE_ALLOWED" => array(
			"type" => "boolean",
		),
		"BAIKAL_STANDALONE_PORT" => array(
			"type" => "integer",
		),
		"BAIKAL_ADMIN_PASSWORDHASH" => array(
			"type" => "calculated",
		)
	);
		
	protected $aData = array(
		"BAIKAL_TIMEZONE" => "",
		"BAIKAL_CARD_ENABLED" => "",
		"BAIKAL_CAL_ENABLED" => "",
		"BAIKAL_TIMEZONE" => "",
		"BAIKAL_CARD_ENABLED" => "",
		"BAIKAL_CAL_ENABLED" => "",
		"BAIKAL_ADMIN_ENABLED" => "",
		"BAIKAL_STANDALONE_ALLOWED" => "",
		"BAIKAL_STANDALONE_PORT" => "",
		"BAIKAL_ADMIN_PASSWORDHASH" => ""
	);
	
	public function __construct($sConfigFilePath) {
		# Note: no call to parent::__construct() to avoid erasing $this->aData
		$this->sConfigFilePath = $sConfigFilePath;
		$aConfig = $this->parseConfig(
			$this->getConfigAsString()
		);

		foreach(array_keys($this->aData) as $sProp) {
			if(array_key_exists($sProp, $aConfig)) {
				$this->aData[$sProp] = $aConfig[$sProp];
			}
		}
	}
	
	protected function getConfigAsString() {
		$sContent = file_get_contents($this->sConfigFilePath);
		return str_replace(LF . CR, LF, $sContent);
	}
	
	protected function parseConfig($sString) {
		
		$aRes = array();
		
		foreach(array_keys($this->aConstants) as $sConstant) {
			$aConstant = $this->aConstants[$sConstant];
			
			$aMatches = array();
			$sPattern = '/\s*define\(\s*["|\']' . $sConstant . '["|\']\s*\,\s*(.*?)\s*\);\s*/ix';
			
			$iNbRes = preg_match_all(
				$sPattern,
				$sString,
				$aMatches
			);
			
			if($iNbRes === 1) {
				# Exactly one match
				# O would be not enough, and > 1, to much to handle properly
				
				$sValue = $aMatches[1][0];	# first capture (.*?), first occurence (anyway, we asserted that there's only one)
				switch($aConstant["type"]) {
					case "string": {
						$sValue = substr($sValue, 1, -1);	# Strip quotes
						break;
					}
					case "integer": {
						$sValue = intval($sValue);	# Integer
						break;
					}
					case "boolean": {
						if(in_array(strtoupper(trim($sValue)), array("1", "TRUE"))) {
							$sValue = TRUE;
						} else {
							$sValue = FALSE;
						}
						break;
					}
					default: {
						# nothing
						break;
					}
				}
				
				$aRes[$sConstant] = $sValue;
			}
		}
		
		reset($aRes);
		return $aRes;
	}
	
	public function writable() {
		return (
			@file_exists($this->sConfigFilePath) &&
			@is_file($this->sConfigFilePath) &&
			@is_writable($this->sConfigFilePath)
		);
	}
	
	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();
		
		$oMorpho->add(new \Formal\Element\Listbox(array(
			"prop" => "BAIKAL_TIMEZONE",
			"label" => "Time zone",
			"validation" => "required",
			"options" => array(
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
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_CARD_ENABLED",
			"label" => "Enable CardDAV"
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_CAL_ENABLED",
			"label" => "Enable CalDAV"
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_ADMIN_ENABLED",
			"label" => "Enable Web Admin"
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_STANDALONE_ALLOWED",
			"label" => "Allow Standalone Baïkal execution"
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_STANDALONE_PORT",
			"label" => "Standalone Baïkal port"
		)));
		
		$sNotice = "-- Leave empty to keep current password --";
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "BAIKAL_ADMIN_PASSWORDHASH",
			"label" => "Web admin password",
			"placeholder" => $sNotice,
		)));
		
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM",
			"label" => "Web admin password confirm",
			"placeholder" => $sNotice,
		)));
				
		return $oMorpho;
	}
	
	public static function icon() {
		return "icon-cog";
	}
	
	public static function mediumicon() {
		return "glyph-cogwheel";
	}
	
	public static function bigicon() {
		return "glyph2x-cogwheel";
	}
	
	public function label() {
		return "Baïkal Settings";
	}
	
	public function floating() {
		return FALSE;
	}
	
	public function persist() {
		$aLines = explode(LF, $this->getConfigAsString());
				
		foreach(array_keys($this->aData) as $prop) {
			$iLines = count($aLines);
			$sPattern = '/\s*define\(\s*["|\']' . $prop . '["|\']\s*\,\s*(.*?)\s*\);\s*/ix';
			
			for($k = ($iLines - 1); $k >= 0; $k--) {
				if(preg_match($sPattern, $aLines[$k])) {
					debug($aLines[$k]);
				}
			}
		}
	}
	
	public function set($sProp, $sValue) {
		if($sProp === "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM") {
			return;
		}
		
		parent::set($sProp, $sValue);
	}
	
	public function get($sProp) {
		if($sProp === "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM") {
			return "yyyy";
		}
		
		return parent::get($sProp);
	}
	
	public function destroy() {
		
	}
}