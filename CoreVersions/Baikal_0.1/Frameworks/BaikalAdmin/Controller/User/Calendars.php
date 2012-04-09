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

namespace BaikalAdmin\Controller\User;

class Calendars extends \Flake\Core\Controller {
	
	protected $aMessages = array();
	protected $oModel;	# \Baikal\Model\Calendar 
	protected $oUser;	# \Baikal\Model\User
	protected $oForm;	# \Formal\Form
	
	public function __construct() {
		parent::__construct();
		
		if(($iUser = $this->currentUserId()) === FALSE) {
			throw new \Exception("BaikalAdmin\Controller\Details::render(): User get-parameter not found.");
		}
		
		$this->oUser = new \Baikal\Model\User($iUser);
		
		if(($iCalendar = self::editRequested()) !== FALSE) {
			$this->oModel = new \Baikal\Model\Calendar($iCalendar);
			$this->initForm();
		}

		if(($iUser = self::newRequested()) !== FALSE) {
			# building floating object
			$this->oModel = new \Baikal\Model\Calendar();
			$this->oModel->set(
				"principaluri",
				$this->oUser->get("uri")
			);
			
			$this->oModel->set(
				"components",
				"VEVENT"
			);
			
			$this->oModel->set(
				"ctag",
				"1"
			);
			
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
		
		if(($iCalendar = self::deleteRequested()) !== FALSE) {
			
			if(self::deleteConfirmed() !== FALSE) {
				
				# catching Exception thrown when model already destroyed
					# happens when user refreshes page on delete-URL, for instance
					
				try {
					$oModel = new \Baikal\Model\Calendar($iCalendar);
					$oModel->destroy();				
				} catch(\Exception $e) {
					# already deleted; silently discarding
				}
				
				# Redirecting to admin home
				\Flake\Util\Tools::redirectUsingMeta(self::linkHome());
			} else {
				
				$oModel = new \Baikal\Model\Calendar($iCalendar);
				$this->aMessages[] = \Formal\Core\Message::warningConfirmMessage(
					"Check twice, you're about to delete " . $oModel->label() . "</strong> from the database !",
					"<p>You are about to delete a calendar and all it's scheduled events. This operation cannot be undone.</p><p>So, now that you know all that, what shall we do ?</p>",
					self::linkDeleteConfirm($oModel),
					"Delete <strong><i class='" . $oModel->icon() . " icon-white'></i> " . $oModel->label() . "</strong>",
					self::linkHome()
				);
			}
		}
	}

	public function render() {
		
		$sHtml = "";
		
		# Render list of users
		$oCalendars = $this->oUser->getCalendarsBaseRequester()->execute();
		
		$oView = new \BaikalAdmin\View\Calendars\Listing();
		$oView->setData("user", $this->oUser);
		$oView->setData("calendars", $oCalendars);
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
				"closeurl" => $this->linkHome()
			);
			
			$this->oForm = $this->oModel->formForThisModelInstance($aOptions);
		}
	}
	
	protected static function currentUserId() {
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		if(($iUser = intval($aParams[0])) === 0) {
			return FALSE;
		}
		
		return $iUser;
	}
	
	protected static function newRequested() {
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		return $aParams[1] === "new";
	}
	
	protected static function editRequested() {
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		if(($aParams[1] === "edit") && intval($aParams[2]) > 0) {
			return intval($aParams[2]);
		}
		
		return FALSE;
	}
	
	protected static function deleteRequested() {
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		if(($aParams[1] === "delete") && intval($aParams[2]) > 0) {
			return intval($aParams[2]);
		}
		
		return FALSE;
	}
	
	protected static function deleteConfirmed() {
		if(($iPrimary = self::deleteRequested()) === FALSE) {
			return FALSE;
		}
		
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		if($aParams[3] === "confirm") {
			return $iPrimary;
		}
		
		return FALSE;
	}
	
	public static function linkNew() {
		return $GLOBALS["ROUTER"]::buildRouteForController(
			get_called_class(),
			self::currentUserId(),
			"new"
		) . "#form";
	}
	
	public static function linkEdit(\Baikal\Model\Calendar $oCalendar) {
		return $GLOBALS["ROUTER"]::buildRouteForController(
			get_called_class(),
			self::currentUserId(),
			"edit",
			$oCalendar->get("id")
		) . "#form";
	}
	
	public static function linkDelete(\Baikal\Model\Calendar $oCalendar) {
		return $GLOBALS["ROUTER"]::buildRouteForController(
			get_called_class(),
			self::currentUserId(),
			"delete",
			$oCalendar->get("id")
		) . "#message";
	}
	
	public static function linkDeleteConfirm(\Baikal\Model\Calendar $oCalendar) {
		return $GLOBALS["ROUTER"]::buildRouteForController(
			get_called_class(),
			self::currentUserId(),
			"delete",
			$oCalendar->get("id"),
			"confirm"
		) . "#message";
	}
	
	public static function linkHome() {
		return $GLOBALS["ROUTER"]::buildRouteForController(
			get_called_class(),
			self::currentUserId()
		);
	}
}