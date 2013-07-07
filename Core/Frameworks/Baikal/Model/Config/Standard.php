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

class Standard extends \Baikal\Model\Config {
	
	protected $aConstants = array(
		"PROJECT_TIMEZONE" => array(
			"type" => "string",
			"comment" => "Timezone of the server; if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones",
		),
		"BAIKAL_CARD_ENABLED" => array(
			"type" => "boolean",
			"comment" => "CardDAV ON/OFF switch; default TRUE",
		),
		"BAIKAL_CAL_ENABLED" => array(
			"type" => "boolean",
			"comment" => "CalDAV ON/OFF switch; default TRUE",
		),
		"BAIKAL_DAV_AUTH_TYPE" => array(
			"type" => "string",
			"comment" => "HTTP authentication type for WebDAV; default Digest"
		),
		"BAIKAL_ADMIN_ENABLED" => array(
			"type" => "boolean",
			"comment" => "Baïkal Web Admin ON/OFF switch; default TRUE",
		),
		"BAIKAL_ADMIN_AUTOLOCKENABLED" => array(
			"type" => "boolean",
			"comment" => "Baïkal Web Admin autolock ON/OFF switch; default FALSE",
		),
		"BAIKAL_ADMIN_PASSWORDHASH" => array(
			"type" => "string",
			"comment" => "Baïkal Web admin password hash; Set via Baïkal Web Admin",
		)
	);
	
	# Default values
	protected $aData = array(
		"PROJECT_TIMEZONE" => "Europe/Paris",
		"BAIKAL_CARD_ENABLED" => TRUE,
		"BAIKAL_CAL_ENABLED" => TRUE,
		"BAIKAL_DAV_AUTH_TYPE" => "Digest",
		"BAIKAL_ADMIN_ENABLED" => TRUE,
		"BAIKAL_ADMIN_AUTOLOCKENABLED" => FALSE,
		"BAIKAL_ADMIN_PASSWORDHASH" => ""
	);
	
	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();
		
		$oMorpho->add(new \Formal\Element\Listbox(array(
			"prop" => "PROJECT_TIMEZONE",
			"label" => "Server Time zone",
			"validation" => "required",
			"options" => \Baikal\Core\Tools::timezones(),
		)));
		
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_CAL_ENABLED",
			"label" => "Enable CalDAV"
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_CARD_ENABLED",
			"label" => "Enable CardDAV"
		)));

		$oMorpho->add(new \Formal\Element\Listbox(array(
			"prop" => "BAIKAL_DAV_AUTH_TYPE",
			"label" => "WebDAV authentication type",
			"options" => array( "Digest", "Basic" )
		)));
		
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "BAIKAL_ADMIN_PASSWORDHASH",
			"label" => "Admin password",
		)));
		
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM",
			"label" => "Admin password, confirmation",
			"validation" => "sameas:BAIKAL_ADMIN_PASSWORDHASH",
		)));
		
		if(!defined("BAIKAL_ADMIN_PASSWORDHASH") || trim(BAIKAL_ADMIN_PASSWORDHASH) === "") {

			# No password set (Form is used in install tool), so password is required as it has to be defined
			$oMorpho->element("BAIKAL_ADMIN_PASSWORDHASH")->setOption("validation", "required");
		} else {
			$sNotice = "-- Leave empty to keep current password --";
			$oMorpho->element("BAIKAL_ADMIN_PASSWORDHASH")->setOption("placeholder", $sNotice);
			$oMorpho->element("BAIKAL_ADMIN_PASSWORDHASH_CONFIRM")->setOption("placeholder", $sNotice);
		}

		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_ADMIN_ENABLED",
			"label" => "Enable Web interface (recommended)",
			"popover" => array(
				"title" => "Warning !",
				"content" => "If disabled, you'll lose access to this very admin interface !",
			),
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_ADMIN_AUTOLOCKENABLED",
			"label" => "Web interface autolock",
			"popover" => array(
				"title" => "Web admin autolock",
				"content" => "If enabled, you'll have to create a file named <strong>ENABLE_ADMIN</strong> in the folder <strong>Specific/</strong> prior to every admin use.<br /><br />This enforces security, but might be uncomfortable if you use the admin frequently."
			)
		)));
		
		return $oMorpho;
	}
		
	public function label() {
		return "Baïkal Settings";
	}
	
	public function set($sProp, $sValue) {
		if($sProp === "BAIKAL_ADMIN_PASSWORDHASH" || $sProp === "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM") {
			# Special handling for password and passwordconfirm
			
			if($sProp === "BAIKAL_ADMIN_PASSWORDHASH" && $sValue !== "") {
				parent::set(
					"BAIKAL_ADMIN_PASSWORDHASH",
					\BaikalAdmin\Core\Auth::hashAdminPassword($sValue)
				);
			}
			
			return $this;
		}
		
		parent::set($sProp, $sValue);
	}
	
	public function get($sProp) {
		if($sProp === "BAIKAL_ADMIN_PASSWORDHASH" || $sProp === "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM") {
			return "";
		}
		
		return parent::get($sProp);
	}

	protected function createDefaultConfigFilesIfNeeded() {

		# Create empty config.php if needed
		if(!file_exists(PROJECT_PATH_SPECIFIC . "config.php")) {
			@touch(PROJECT_PATH_SPECIFIC . "config.php");
			$sContent = "<?php\n" . \Baikal\Core\Tools::getCopyrightNotice() . "\n\n";
			$sContent .= $this->getDefaultConfig();
			file_put_contents(PROJECT_PATH_SPECIFIC . "config.php", $sContent);
		}
		
		# Create empty config.system.php if needed
		if(!file_exists(PROJECT_PATH_SPECIFIC . "config.system.php")) {
			@touch(PROJECT_PATH_SPECIFIC . "config.system.php");
			$sContent = "<?php\n" . \Baikal\Core\Tools::getCopyrightNotice() . "\n\n";
			$sContent .= $this->getDefaultSystemConfig();
			file_put_contents(PROJECT_PATH_SPECIFIC . "config.system.php", $sContent);
		}
	}
	
	protected static function getDefaultConfig() {

		$sCode =<<<CODE
##############################################################################
# Required configuration
# You *have* to review these settings for Baïkal to run properly
#

# Timezone of your users, if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
define("PROJECT_TIMEZONE", "Europe/Paris");

# CardDAV ON/OFF switch; default TRUE
define("BAIKAL_CARD_ENABLED", TRUE);

# CalDAV ON/OFF switch; default TRUE
define("BAIKAL_CAL_ENABLED", TRUE);

# WebDAV authentication type; default Digest
define("BAIKAL_DAV_AUTH_TYPE", "Digest");

# Baïkal Web Admin ON/OFF switch; default TRUE
define("BAIKAL_ADMIN_ENABLED", TRUE);

# Baïkal Web Admin autolock ON/OFF switch; default FALSE
define("BAIKAL_ADMIN_AUTOLOCKENABLED", FALSE);

# Baïkal Web admin password hash; Set via Baïkal Web Admin
define("BAIKAL_ADMIN_PASSWORDHASH", "");
CODE;
		$sCode = trim($sCode);
		return $sCode;
	}
}