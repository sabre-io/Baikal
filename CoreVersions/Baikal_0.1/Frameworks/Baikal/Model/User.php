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

class User extends \Flake\Core\Model\Db {
	const DATATABLE = "users";
	const PRIMARYKEY = "id";
	const LABELFIELD = "username";
	
	protected $aData = array(
		"username" => "",
		"digesta1" => "",
	);
	
	protected $oIdentityPrincipal = null;
	
	public function initByPrimary($sPrimary) {
		parent::initByPrimary($sPrimary);
		
		# Initializing principals
		$this->oIdentityPrincipal = \Baikal\Model\Principal::getBaseRequester()
			->addClauseEquals("uri", "principals/" . $this->get("username"))
			->execute()
			->first();
	}
	
	public function initFloating() {
		parent::initFloating();
		
		# Initializing principals
		$this->oIdentityPrincipal = new \Baikal\Model\Principal();
	}
	
	public function get($sPropName) {
		
		if($sPropName === "password" || $sPropName === "passwordconfirm") {
			# Special handling for password and passwordconfirm
			return "";
		}
		
		try {
			# does the property exist on the model object ?
			$sRes = parent::get($sPropName);
		} catch(\Exception $e) {
			# no, it may belong to the oIdentityPrincipal model object
			$sRes = $this->oIdentityPrincipal->get($sPropName);
		}
		
		return $sRes;
	}
	
	public function set($sPropName, $sPropValue) {
		
		if($sPropName === "password" || $sPropName === "passwordconfirm") {
			# Special handling for password and passwordconfirm
			
			if($sPropName === "password" && $sPropValue !== "") {
				parent::set(
					"digesta1",
					$this->getPasswordHashForPassword($sPropValue)
				);
			}
			
			return $this;
		}
		
		try {
			# does the property exist on the model object ?
			parent::set($sPropName, $sPropValue);
		} catch(\Exception $e) {
			# no, it may belong to the oIdentityPrincipal model object
			$this->oIdentityPrincipal->set($sPropName, $sPropValue);
		}
		
		return $this;
	}
	
	public function persist() {
		
		# Persisted first, as Model users loads this data
		$this->oIdentityPrincipal->set("uri", "principals/" . $this->get("username"));
		$this->oIdentityPrincipal->persist();
		
		parent::persist();
	}
	
	public function destroy() {
		# TODO: delete all related resources (principals, calendars, calendar events, contact books and contacts)
		
		# Destroying identity principal
		$this->oIdentityPrincipal->destroy();
		
		parent::destroy();
	}
	
	public function getMailtoURI() {
		return "mailto:" . rawurlencode($this->get("displayname") . " <" . $this->get("email") . ">");
	}
	
	public function formForThisModelInstance($options = array()) {
		$sClass = get_class($this);
		$oForm = new \Formal\Form($sClass, $options);
		$oForm->setModelInstance($this);
		
		return $oForm;
	}
	
	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "username",
			"label" => "Username",
			"validation" => "required,unique"
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "displayname",
			"label" => "Display name",
			"validation" => "required"
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "email",
			"label" => "Email",
			"validation" => "required,email"
		)));
		
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "password",
			"label" => "Password",
		)));
		
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "passwordconfirm",
			"label" => "Confirm password",
			"validation" => "sameas:password",
		)));
		
		if($this->floating()) {
			$oMorpho->element("password")->setOption("validation", "required");
		} else {
			
			$sNotice = "-- Leave empty to keep current password --";
			$oMorpho->element("username")->setOption("readonly", true);
			$oMorpho->element("password")->setOption("placeholder", $sNotice);
			$oMorpho->element("passwordconfirm")->setOption("placeholder", $sNotice);
		}
		
		return $oMorpho;
	}
	
	public static function getIcon() {
		return "icon-user";
	}
	
	public function getPasswordHashForPassword($sPassword) {
		return md5($this->get("username") . ':' . BAIKAL_AUTH_REALM . ':' . $sPassword);
	}
}