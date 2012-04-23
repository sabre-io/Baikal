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

namespace Baikal\Model\Config;

class Standard extends \Baikal\Model\Config {
	
	protected $aConstants = array(
		"BAIKAL_TIMEZONE" => array(
			"type" => "string",
		),
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
			"type" => "string",
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
	
	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();
		
		$oMorpho->add(new \Formal\Element\Listbox(array(
			"prop" => "BAIKAL_TIMEZONE",
			"label" => "Time zone",
			"validation" => "required",
			"options" => \Baikal\Core\Tools::timezones(),
			"help" => "Time zone of the server"
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
			"label" => "Enable Web Admin",
			"popover" => array(
				"title" => "Warning !",
				"content" => "If disabled, you'll lose access to this very admin interface !",
			),
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
			"label" => "Web admin password confirmation",
			"placeholder" => $sNotice,
			"validation" => "sameas:BAIKAL_ADMIN_PASSWORDHASH",
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
}