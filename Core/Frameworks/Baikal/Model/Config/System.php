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

namespace Baikal\Model\Config;

class System extends \Baikal\Model\Config {
	
	protected $aConstants = array(
		"BAIKAL_PATH_SABREDAV" => array(
			"type" => "litteral",
			"comment" => "PATH to SabreDAV",
		),
		"BAIKAL_AUTH_REALM" => array(
			"type" => "string",
			"comment" => "If you change this value, you'll have to re-generate passwords for all your users",
		),
		"BAIKAL_CARD_BASEURI" => array(
			"type" => "litteral",
			"comment" => 'Should begin and end with a "/"',
		),
		"BAIKAL_CAL_BASEURI" => array(
			"type" => "litteral",
			"comment" => 'Should begin and end with a "/"',
		),
		"BAIKAL_DAV_BASEURI" => array(
			"type" => "litteral",
			"comment" => 'Should begin and end with a "/"',
		),
		"PROJECT_SQLITE_FILE" => array(
			"type" => "litteral",
			"comment" => "Define path to Baïkal Database SQLite file",
		),
		"PROJECT_DB_MYSQL" => array(
			"type" => "boolean",
			"comment" => "MySQL > Use MySQL instead of SQLite ?",
		),
		"PROJECT_DB_MYSQL_HOST" => array(
			"type" => "string",
			"comment" => "MySQL > Host, including ':portnumber' if port is not the default one (3306)",
		),
		"PROJECT_DB_MYSQL_DBNAME" => array(
			"type" => "string",
			"comment" => "MySQL > Database name",
		),
		"PROJECT_DB_MYSQL_USERNAME" => array(
			"type" => "string",
			"comment" => "MySQL > Username",
		),
		"PROJECT_DB_MYSQL_PASSWORD" => array(
			"type" => "string",
			"comment" => "MySQL > Password",
		),
		"BAIKAL_ENCRYPTION_KEY" => array(
			"type" => "string",
			"comment" => "A random 32 bytes key that will be used to encrypt data",
		),
		"BAIKAL_CONFIGURED_VERSION" => array(
			"type" => "string",
			"comment" => "The currently configured Baïkal version",
		),
	);
	
	# Default values
	protected $aData = array(
		"BAIKAL_PATH_SABREDAV" => 'PROJECT_PATH_FRAMEWORKS . "SabreDAV/lib/Sabre/"',
		"BAIKAL_AUTH_REALM" => "BaikalDAV",
		"BAIKAL_CARD_BASEURI" => 'PROJECT_BASEURI . "card.php/"',
		"BAIKAL_CAL_BASEURI" => 'PROJECT_BASEURI . "cal.php/"',
		"BAIKAL_DAV_BASEURI" => 'PROJECT_BASEURI . "dav.php/"',
		"PROJECT_SQLITE_FILE" => 'PROJECT_PATH_SPECIFIC . "db/db.sqlite"',
		"PROJECT_DB_MYSQL" => FALSE,
		"PROJECT_DB_MYSQL_HOST" => "",
		"PROJECT_DB_MYSQL_DBNAME" => "",
		"PROJECT_DB_MYSQL_USERNAME" => "",
		"PROJECT_DB_MYSQL_PASSWORD" => "",
		"BAIKAL_ENCRYPTION_KEY" => "",
		"BAIKAL_CONFIGURED_VERSION" => "",
	);
	
	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_CAL_BASEURI",
			"label" => "CalDAV base URI",
			"validation" => "required",
			"help" => "The absolute web path to cal.php",
			"popover" => array(
				"title" => "CalDAV base URI",
				"content" => "If Baïkal is hosted in a subfolder, this path should reflect it.<br /><strong>Whatever happens, it should begin and end with a slash.</strong>",
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_CARD_BASEURI",
			"label" => "CardDAV base URI",
			"validation" => "required",
			"help" => "The absolute web path to card.php",
			"popover" => array(
				"title" => "CardDAV base URI",
				"content" => "If Baïkal is hosted in a subfolder, this path should reflect it.<br /><strong>Whatever happens, it should begin and end with a slash.</strong>"
			)
		)));
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_DAV_BASEURI",
			"label" => "CalDAV/CardDAV base URI",
			"validation" => "required",
			"help" => "The absolute web path to dav.php",
			"popover" => array(
				"title" => "DAV base URI",
				"content" => "If Baïkal is hosted in a subfolder, this path should reflect it.<br /><strong>Whatever happens, it should begin and end with a slash.</strong>"
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_AUTH_REALM",
			"label" => "Auth realm",
			"validation" => "required",
			"help" => "Token used in authentication process.<br />If you change this, you'll have to reset all your users passwords.<br />You'll also loose access to this admin interface.",
			"popover" => array(
				"title" => "Auth realm",
				"content" => "If you change this, you'll loose your access to this interface.<br />In other words: <strong>you should not change this, unless YKWYD.</strong>"
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_PATH_SABREDAV",
			"label" => "Path to SabreDAV",
			"validation" => "required",
			"inputclass" => "input-xxlarge",
			"help" => "The absolute server path to SabreDAV API",
			"popover" => array(
				"title" => "Path to SabreDAV",
				"content" => "If Baïkal is hosted in a subfolder, this path should reflect it.<br /><strong>Whatever happens, it should begin and end with a slash.</strong>",
				"position" => "top"
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_SQLITE_FILE",
			"label" => "SQLite file path",
			"validation" => "required",
			"inputclass" => "input-xxlarge",
			"help" => "The absolute server path to the SQLite file",
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "PROJECT_DB_MYSQL",
			"label" => "Use MySQL",
			"help" => "If checked, Baïkal will use MySQL instead of SQLite.",
			"refreshonchange" => TRUE,
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_DB_MYSQL_HOST",
			"label" => "MySQL host",
			"help" => "Host ip or name, including ':portnumber' if port is not the default one (3306)"
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_DB_MYSQL_DBNAME",
			"label" => "MySQL database name",
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_DB_MYSQL_USERNAME",
			"label" => "MySQL username",
		)));
		
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "PROJECT_DB_MYSQL_PASSWORD",
			"label" => "MySQL password",
		)));
		
		return $oMorpho;
	}
		
	public function label() {
		return "Baïkal Settings";
	}

	protected static function getDefaultConfig() {

		$sBaikalVersion = BAIKAL_VERSION;

		$sCode =<<<CODE
##############################################################################
# System configuration
# Should not be changed, unless YNWYD
#
# RULES
#	0. All folder pathes *must* be suffixed by "/"
#	1. All URIs *must* be suffixed by "/" if pointing to a folder
#

# PATH to SabreDAV
define("BAIKAL_PATH_SABREDAV", PROJECT_PATH_FRAMEWORKS . "SabreDAV/lib/Sabre/");

# If you change this value, you'll have to re-generate passwords for all your users
define("BAIKAL_AUTH_REALM", "BaikalDAV");

# Should begin and end with a "/"
define("BAIKAL_CARD_BASEURI", PROJECT_BASEURI . "card.php/");

# Should begin and end with a "/"
define("BAIKAL_CAL_BASEURI", PROJECT_BASEURI . "cal.php/");

# Should begin and end with a "/"
define("BAIKAL_DAV_BASEURI", PROJECT_BASEURI . "dav.php/");

# Define path to Baïkal Database SQLite file
define("PROJECT_SQLITE_FILE", PROJECT_PATH_SPECIFIC . "db/db.sqlite");

# MySQL > Use MySQL instead of SQLite ?
define("PROJECT_DB_MYSQL", FALSE);

# MySQL > Host, including ':portnumber' if port is not the default one (3306)
define("PROJECT_DB_MYSQL_HOST", "");

# MySQL > Database name
define("PROJECT_DB_MYSQL_DBNAME", "");

# MySQL > Username
define("PROJECT_DB_MYSQL_USERNAME", "");

# MySQL > Password
define("PROJECT_DB_MYSQL_PASSWORD", "");

# A random 32 bytes key that will be used to encrypt data
define("BAIKAL_ENCRYPTION_KEY", "");

# The currently configured Baïkal version
define("BAIKAL_CONFIGURED_VERSION", "{$sBaikalVersion}");

CODE;
		$sCode = trim($sCode);
		return $sCode;
	}
}
