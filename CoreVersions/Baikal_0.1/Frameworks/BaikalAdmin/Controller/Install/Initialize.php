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

namespace BaikalAdmin\Controller\Install;

class Initialize extends \Flake\Core\Controller {
	
	protected $aMessages = array();
	protected $oModel;
	protected $oForm;	# \Formal\Form
	
	public function __construct() {
		parent::__construct();
		
		$this->createDefaultConfigFilesIfNeeded();
		$this->oModel = new \Baikal\Model\Config\Standard(BAIKAL_PATH_SPECIFIC . "config.php");
		
		# Assert that config file is writable
		if(!$this->oModel->writable()) {
			throw new \Exception("Config file is not writable;" . __FILE__ . " > " . __LINE__);
		}
		
		$this->oForm = $this->oModel->formForThisModelInstance(array(
			"close" => FALSE
		));
	}
	
	public function execute() {
		if($this->oForm->submitted()) {
			$this->oForm->execute();
			
			if($this->oForm->persisted()) {
				$sContent = file_get_contents(BAIKAL_PATH_SPECIFIC . "config.system.php");
				$sContent .= "\n\ndefine(\"BAIKAL_CONFIGURED_VERSION\", \"" . BAIKAL_VERSION . "\");\n";
				file_put_contents(BAIKAL_PATH_SPECIFIC . "config.system.php", $sContent);
			}
		}
	}

	public function render() {
		$sBigIcon = \BaikalAdmin\Model\Install::bigicon();
		$sBaikalVersion = BAIKAL_VERSION;

		$sHtml = <<<HTML
<header class="jumbotron subhead" id="overview">
	<h1><i class="{$sBigIcon}"></i>Baïkal initialization wizard</h1>
	<p class="lead">Configure your new Baïkal <strong>{$sBaikalVersion}</strong> installation.</p>
</header>
HTML;
		
		if($this->oForm->persisted()) {
			$sHtml .= "<p>Baïkal is now configured. You may now <a class='btn btn-success' href='" . BAIKAL_URI . "admin/'>Access the Baïkal admin</a></h2>";
			
		} else {
			# Display the config form
			$sHtml .= $this->oForm->render();
		}

		return $sHtml;
	}
	
	protected function tagConfiguredVersion() {
		file_put_contents(BAIKAL_PATH_SPECIFIC . "config.php", $sContent);
	}
	
	protected function createDefaultConfigFilesIfNeeded() {

		# Create empty config.php if needed
		if(!file_exists(BAIKAL_PATH_SPECIFIC . "config.php")) {
			@touch(BAIKAL_PATH_SPECIFIC . "config.php");
			$sContent = "<?php\n" . \Baikal\Core\Tools::getCopyrightNotice() . "\n\n";
			$sContent .= $this->getDefaultConfig();
			file_put_contents(BAIKAL_PATH_SPECIFIC . "config.php", $sContent);
		}
		
		# Create empty config.system.php if needed
		if(!file_exists(BAIKAL_PATH_SPECIFIC . "config.system.php")) {
			@touch(BAIKAL_PATH_SPECIFIC . "config.system.php");
			$sContent = "<?php\n" . \Baikal\Core\Tools::getCopyrightNotice() . "\n\n";
			$sContent .= $this->getDefaultSystemConfig();
			file_put_contents(BAIKAL_PATH_SPECIFIC . "config.system.php", $sContent);
		}
	}
	
	protected function getDefaultConfig() {

		$sCode =<<<CODE
##############################################################################
# Required configuration
# You *have* to review these settings for Baïkal to run properly
#

# Timezone of your users, if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
define("BAIKAL_TIMEZONE", "Europe/Paris");

# CardDAV ON/OFF switch; default TRUE
define("BAIKAL_CARD_ENABLED", TRUE);

# CalDAV ON/OFF switch; default TRUE
define("BAIKAL_CAL_ENABLED", TRUE);

# Baïkal Web Admin ON/OFF switch; default TRUE
define("BAIKAL_ADMIN_ENABLED", TRUE);

# Baïkal Web admin password hash; Set by Core/Scripts/adminpassword.php or via Baïkal Web Admin
define("BAIKAL_ADMIN_PASSWORDHASH", "");
CODE;
		$sCode = trim($sCode);
		return $sCode;
	}
	
	protected function getDefaultSystemConfig() {
		$sCode =<<<CODE
##############################################################################
# System configuration
# Should not be changed, unless YNWYD
#
# RULES
#	0. All folder pathes *must* be suffixed by "/"
#	1. All URIs *must* be suffixed by "/" if pointing to a folder
#

# Standalone Server, allowed or not; default FALSE
define("BAIKAL_STANDALONE_ALLOWED", FALSE);

# Standalone Server, port number; default 8888
define("BAIKAL_STANDALONE_PORT", 8888);

# PATH to SabreDAV
define("BAIKAL_PATH_SABREDAV", BAIKAL_PATH_FRAMEWORKS . "SabreDAV/lib/Sabre/");

# If you change this value, you'll have to re-generate passwords for all your users
define("BAIKAL_AUTH_REALM", "BaikalDAV");

# Should begin and end with a "/"
define("BAIKAL_CARD_BASEURI", "/card.php/");

# Should begin and end with a "/"
define("BAIKAL_CAL_BASEURI", "/cal.php/");

# SQLite DB path
define("BAIKAL_SQLITE_FILE", BAIKAL_PATH_SPECIFIC . "db/baikal.sqlite");
CODE;
		$sCode = trim($sCode);
		return $sCode;
	}
}