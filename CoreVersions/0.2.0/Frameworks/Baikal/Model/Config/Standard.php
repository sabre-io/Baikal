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
		"BAIKAL_ADMIN_ENABLED" => array(
			"type" => "boolean",
		),
		"BAIKAL_CAL_ENABLED" => array(
			"type" => "boolean",
		),
		"BAIKAL_CARD_ENABLED" => array(
			"type" => "boolean",
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
			"prop" => "BAIKAL_ADMIN_ENABLED",
			"label" => "Enable Web Admin",
			"popover" => array(
				"title" => "Warning !",
				"content" => "If disabled, you'll lose access to this very admin interface !",
			),
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_CAL_ENABLED",
			"label" => "Enable CalDAV"
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "BAIKAL_CARD_ENABLED",
			"label" => "Enable CardDAV"
		)));
		
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "BAIKAL_ADMIN_PASSWORDHASH",
			"label" => "Web admin password",
		)));
		
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM",
			"label" => "Web admin password confirmation",
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