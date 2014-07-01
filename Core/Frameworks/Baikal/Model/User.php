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
		# With the external auth. backends it is possible for the user table
		# to be populated by something else - so the principal might not yet
		# exist. To ensure the principals table displayname and email are 
		# auto-assigned this assumes they are also fields of the users table.
		$this->autoCreatePrincipal(TRUE);
	}

	public function autoCreatePrincipal($persistNow=FALSE) {
		if (is_null($this->oIdentityPrincipal)) {
			$this->oIdentityPrincipal = new \Baikal\Model\Principal();
			foreach( $this->oIdentityPrincipal->getAutoPopulateFields() as $field ) {
				try {
					$this->oIdentityPrincipal->set($field, $this->get($field));
				} catch (\Exception $e) {
					// the field is not defined by the backend so leave it blank
					continue;
				}
			}

			# if the principal needs to be persisted then do that 
			if ($persistNow) {
				$this->oIdentityPrincipal->set("uri", "principals/" . $this->get("username"));
				$this->oIdentityPrincipal->persist();
			}
		}
	}
	
	public function getAddressBooksBaseRequester() {
		$oBaseRequester = \Baikal\Model\AddressBook::getBaseRequester();
		$oBaseRequester->addClauseEquals(
			"principaluri",
			"principals/" . $this->get("username")
		);
		
		return $oBaseRequester;
	}
	
	public function getCalendarsBaseRequester() {
		$oBaseRequester = \Baikal\Model\Calendar::getBaseRequester();
		$oBaseRequester->addClauseEquals(
			"principaluri",
			"principals/" . $this->get("username")
		);
		
		return $oBaseRequester;
	}
	
	public function initFloating() {
		parent::initFloating();
		
		# Initializing principals
		$this->autoCreatePrincipal();
	}
	
	public function get($sPropName) {
		
		if($sPropName === "password" || $sPropName === "passwordconfirm") {
			# Special handling for password and passwordconfirm
			return "";
		}
		
		$sRes = "";
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

		# we may need to try and set the value on the principal
		$tryPrincipal = FALSE;
		
		try {
			# does the property exist on the model object ?
			parent::set($sPropName, $sPropValue);
		} catch(\Exception $e) {
			# no, it may belong to the oIdentityPrincipal model object
			$tryPrincipal = TRUE;
		}

		# update the principal with the property if is apart of it (or not of user)
		if ($tryPrincipal || in_array($sPropName, $this->oIdentityPrincipal->getAutoPopulateFields()))
			$this->oIdentityPrincipal->set($sPropName, $sPropValue);
		
		return $this;
	}
	
	public function persist() {
		
		$bFloating = $this->floating();
		
		# Persisted first, as Model users loads this data
		$this->oIdentityPrincipal->set("uri", "principals/" . $this->get("username"));
		$this->oIdentityPrincipal->persist();
		
		parent::persist();
		
		if($bFloating) {
			
			# Creating default calendar for user
			$oDefaultCalendar = new \Baikal\Model\Calendar();
			$oDefaultCalendar->set(
				"principaluri",
				"principals/" . $this->get("username")
			)->set(
				"displayname",
				"Default calendar"
			)->set(
				"uri",
				"default"
			)->set(
				"ctag",
				1
			)->set(
				"description",
				"Default calendar"
			)->set(
				"components",
				"VEVENT,VTODO"
			);
			
			$oDefaultCalendar->persist();
			
			# Creating default address book for user
			$oDefaultAddressBook = new \Baikal\Model\AddressBook();
			$oDefaultAddressBook->set(
				"principaluri",
				"principals/" . $this->get("username")
			)->set(
				"displayname",
				"Default Address Book"
			)->set(
				"uri",
				"default"
			)->set(
				"description",
				"Default Address Book for " . $this->get("displayname")
			);
			
			$oDefaultAddressBook->persist();
		}
	}
	
	public function destroy() {
		# TODO: delete all related resources (principals, calendars, calendar events, contact books and contacts)
		
		# Destroying identity principal
		$this->oIdentityPrincipal->destroy();
		
		$oCalendars = $this->getCalendarsBaseRequester()->execute();
		foreach($oCalendars as $calendar) {
			$calendar->destroy();
		}
		
		$oAddressBooks = $this->getAddressBooksBaseRequester()->execute();
		foreach($oAddressBooks as $addressbook) {
			$addressbook->destroy();
		}
		
		parent::destroy();
	}
	
	public function getMailtoURI() {
		return "mailto:" . rawurlencode($this->get("displayname") . " <" . $this->get("email") . ">");
	}
	
	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "username",
			"label" => "Username",
			"validation" => "required,unique",
			"popover" => array(
				"title" => "Username",
				"content" => "The login for this user account.<br />It has to be unique.",
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "displayname",
			"label" => "Display name",
			"validation" => "required",
			"popover" => array(
				"title" => "Display name",
				"content" => "This is the name that will be displayed in your CalDAV/CardDAV clients.",
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "email",
			"label" => "Email",
			"validation" => "required,email"
		)));

		if($this->floating()) {
			$oMorpho->element("username")->setOption("help", "May be an email, but not forcibly.");
		} else {
			$oMorpho->element("username")->setOption("readonly", true);
		}

		if( BAIKAL_DAV_AUTH_TYPE == "Digest" || BAIKAL_DAV_AUTH_TYPE == "Basic") {
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
			
				$oMorpho->element("password")->setOption("popover", array(
					"title" => "Password",
					"content" => "Write something here only if you want to change the user password."
				));
			
				$oMorpho->element("passwordconfirm")->setOption("popover", array(
					"title" => "Confirm password",
					"content" => "Write something here only if you want to change the user password."
				));
			
				$oMorpho->element("password")->setOption("placeholder", $sNotice);
				$oMorpho->element("passwordconfirm")->setOption("placeholder", $sNotice);
			}
		}
		
		return $oMorpho;
	}
	
	public static function icon() {
		return "icon-user";
	}
	
	public static function mediumicon() {
		return "glyph-user";
	}
	
	public static function bigicon() {
		return "glyph2x-user";
	}
	
	public function getPasswordHashForPassword($sPassword) {
		return md5($this->get("username") . ':' . BAIKAL_AUTH_REALM . ':' . $sPassword);
	}
}