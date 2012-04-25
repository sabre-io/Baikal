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

namespace BaikalAdmin\Controller;

class Users extends \Flake\Core\Controller {
	
	protected $aMessages = array();
	
	public function __construct() {
		parent::__construct();
		
		if(($iUser = self::editRequested()) !== FALSE) {
			$this->oModel = new \Baikal\Model\User($iUser);
			$this->initForm();
		}
		
		if(($iUser = self::newRequested()) !== FALSE) {
			# building floating object
			$this->oModel = new \Baikal\Model\User();
			$this->initForm();
		}
	}
	
	public function execute() {
		if(($iUser = self::editRequested()) !== FALSE) {
			if($this->oForm->submitted()) {
				$this->oForm->execute();
			}
		}
		
		if(self::newRequested()) {
			if($this->oForm->submitted()) {
				$this->oForm->execute();
				
				if($this->oForm->persisted()) {
					$this->oForm->setOption(
						"action",
						$this->linkEdit(
							$this->oForm->modelInstance()
						)
					);
				}
			}
		}
		
		if(($iUser = self::deleteRequested()) !== FALSE) {
			
			if(self::deleteConfirmed() !== FALSE) {
				
				# catching Exception thrown when model already destroyed
					# happens when user refreshes delete-page, for instance
					
				try {
					$oUser = new \Baikal\Model\User($iUser);
					$oUser->destroy();				
				} catch(\Exception $e) {
					# user is already deleted; silently discarding
				}
				
				# Redirecting to admin home
				\Flake\Util\Tools::redirectUsingMeta(self::link());
			} else {
				
				$oUser = new \Baikal\Model\User($iUser);
				$this->aMessages[] = \Formal\Core\Message::warningConfirmMessage(
					"Check twice, you're about to delete " . $oUser->label() . "</strong> from the database !",
					"<p>You are about to delete a user and all it's calendars / contacts. This operation cannot be undone.</p><p>So, now that you know all that, what shall we do ?</p>",
					self::linkDeleteConfirm($oUser),
					"Delete <strong><i class='" . $oUser->icon() . " icon-white'></i> " . $oUser->label() . "</strong>",
					self::link()
				);
			}
		}
	}
	
	public function render() {
		$sHtml = "";
		
		# Render list of users
		$oUsers = \Baikal\Model\User::getBaseRequester()->execute();
		$oView = new \BaikalAdmin\View\Users\Listing();
		$oView->setData("users", $oUsers);
		$sHtml .= $oView->render();
		
		
		# Render form
		$sHtml .= "<a id='form'></a>";
		$sMessages = implode("\n", $this->aMessages);

		if(self::newRequested() || self::editRequested()) {
			# We have to display the User form
			$sHtml .= $this->oForm->render();
		} else {
			# No form is displayed; simply display messages, if any
			$sHtml .= $sMessages;
		}
		
		return $sHtml;
	}
	
	protected function initForm() {
		if($this->editRequested() || $this->newRequested()) {
			$aOptions = array(
				"closeurl" => self::link()
			);
			
			$this->oForm = $this->oModel->formForThisModelInstance($aOptions);
		}
	}
	
	protected static function editRequested() {
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		if((count($aParams) >= 2) && ($aParams[0] === "edit") && intval($aParams[1]) > 0) {
			return intval($aParams[1]);
		}
		
		return FALSE;
	}
	
	protected static function deleteRequested() {
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		if((count($aParams) >= 2) && ($aParams[0] === "delete") && intval($aParams[1]) > 0) {
			return intval($aParams[1]);
		}
		
		return FALSE;
	}
	
	protected static function deleteConfirmed() {
		if(($iUser = self::deleteRequested()) === FALSE) {
			return FALSE;
		}
		
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		if((count($aParams) >= 3) && $aParams[2] === "confirm") {
			return $iUser;
		}
		
		return FALSE;
	}
	
	protected static function newRequested() {
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		return (count($aParams) >= 1) && $aParams[0] === "new";
	}
	
	public static function linkNew() {
		return $GLOBALS["ROUTER"]::buildRouteForController(get_called_class(), "new") . "#form";
	}
	
	public static function linkEdit(\Baikal\Model\User $user) {
		return $GLOBALS["ROUTER"]::buildRouteForController(get_called_class(), "edit", $user->get("id")) . "#form";
	}
	
	public static function linkDelete(\Baikal\Model\User $user) {
		return $GLOBALS["ROUTER"]::buildRouteForController(
			get_called_class(),
			"delete",
			$user->get("id")
		) . "#message";
	}
	
	public static function linkDeleteConfirm(\Baikal\Model\User $user) {
		return $GLOBALS["ROUTER"]::buildRouteForController(
			get_called_class(),
			"delete",
			$user->get("id"),
			"confirm"
		) . "#message";
	}
	
	public static function linkCalendars(\Baikal\Model\User $user) {
		return $GLOBALS["ROUTER"]::buildRouteForController('\BaikalAdmin\Controller\User\Calendars', $user->get("id"));
	}
	
	public static function linkAddressBooks(\Baikal\Model\User $user) {
		return $GLOBALS["ROUTER"]::buildRouteForController('\BaikalAdmin\Controller\User\AddressBooks', $user->get("id"));
	}
}