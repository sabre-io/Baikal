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

namespace BaikalAdmin\Controller;

class Users extends \Flake\Core\Controller {
	
	protected $aMessages = array();
	
	public function execute() {
		if($this->actionEditRequested()) {
			$this->actionEdit();
		}
		
		if($this->actionNewRequested()) {
			$this->actionNew();
		}
		
		if($this->actionDeleteRequested()) {
			$this->actionDelete();
		}
	}
	
	public function render() {
		
		$oView = new \BaikalAdmin\View\Users();
		
		# List of users
		$aUsers = array();
		$oUsers = \Baikal\Model\User::getBaseRequester()->execute();
		
		reset($oUsers);
		foreach($oUsers as $user) {
			$aUsers[] = array(
				"linkcalendars" => \BaikalAdmin\Controller\Users::linkCalendars($user),
				"linkaddressbooks" => \BaikalAdmin\Controller\Users::linkAddressBooks($user),
				"linkedit" => \BaikalAdmin\Controller\Users::linkEdit($user),
				"linkdelete" => \BaikalAdmin\Controller\Users::linkDelete($user),
				"mailtouri" => $user->getMailtoURI(),
				"username" => $user->get("username"),
				"displayname" => $user->get("displayname"),
				"email" => $user->get("email"),
			);
		}
		
		$oView->setData("users", $aUsers);
		$oView->setData("calendaricon", \Baikal\Model\Calendar::icon());
		$oView->setData("usericon", \Baikal\Model\User::icon());
		
		# Messages
		$sMessages = implode("\n", $this->aMessages);
		$oView->setData("messages", $sMessages);
		
		# Form
		if($this->actionNewRequested() || $this->actionEditRequested()) {
			$sForm = $this->oForm->render();
		} else {
			$sForm = "";
		}
		
		$oView->setData("form", $sForm);
		$oView->setData("usericon", \Baikal\Model\User::icon());
		$oView->setData("controller", $this);
		
		return $oView->render();
	}
	
	protected function initForm() {
		if($this->actionEditRequested() || $this->actionNewRequested()) {
			$aOptions = array(
				"closeurl" => self::link()
			);
			
			$this->oForm = $this->oModel->formForThisModelInstance($aOptions);
		}
	}
	
	# Action edit
	protected function actionEditRequested() {
		$aParams = $this->getParams();
		if(array_key_exists("edit", $aParams) && intval($aParams["edit"]) > 0) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	protected function actionEdit() {
		$aParams = $this->getParams();
		$this->oModel = new \Baikal\Model\User(intval($aParams["edit"]));
		$this->initForm();
		
		if($this->oForm->submitted()) {
			$this->oForm->execute();
		}
	}
	
	# Action delete
	
	protected function actionDeleteRequested() {
		$aParams = $this->getParams();
		if(array_key_exists("delete", $aParams) && intval($aParams["delete"]) > 0) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	protected function actionDeleteConfirmed() {
		if($this->actionDeleteRequested() === FALSE) {
			return FALSE;
		}
		
		$aParams = $this->getParams();
		
		if(array_key_exists("confirm", $aParams) && intval($aParams["confirm"]) === 1) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	protected function actionDelete() {
		$aParams = $this->getParams();
		$iUser = intval($aParams["delete"]);
		
		if($this->actionDeleteConfirmed() !== FALSE) {
			
			# catching Exception thrown when model already destroyed
				# happens when user refreshes delete-page, for instance
				
			try {
				$oUser = new \Baikal\Model\User($iUser);
				$oUser->destroy();				
			} catch(\Exception $e) {
				# user is already deleted; silently discarding
			}
			
			# Redirecting to admin home
			\Flake\Util\Tools::redirectUsingMeta($this->link());
		} else {
			
			$oUser = new \Baikal\Model\User($iUser);
			$this->aMessages[] = \Formal\Core\Message::warningConfirmMessage(
				"Check twice, you're about to delete " . $oUser->label() . "</strong> from the database !",
				"<p>You are about to delete a user and all it's calendars / contacts. This operation cannot be undone.</p><p>So, now that you know all that, what shall we do ?</p>",
				$this->linkDeleteConfirm($oUser),
				"Delete <strong><i class='" . $oUser->icon() . " icon-white'></i> " . $oUser->label() . "</strong>",
				$this->link()
			);
		}
	}
	
	# Action new
	protected function actionNewRequested() {
		$aParams = $this->getParams();
		if(array_key_exists("new", $aParams) && intval($aParams["new"]) === 1) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	protected function actionNew() {
		$this->oModel = new \Baikal\Model\User();
		$this->initForm();
		
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
	
	public function linkNew() {
		return self::buildRoute(array(
			"new" => 1
		)) . "#form";
	}
	
	public static function linkEdit(\Baikal\Model\User $user) {
		return self::buildRoute(array(
			"edit" => $user->get("id")
		)) . "#form";
	}
	
	public static function linkDelete(\Baikal\Model\User $user) {
		return self::buildRoute(array(
			"delete" => $user->get("id")
		)) . "#message";
	}
	
	public static function linkDeleteConfirm(\Baikal\Model\User $user) {
		return self::buildRoute(array(
			"delete" => $user->get("id"),
			"confirm" => 1
		)) . "#message";
	}
	
	public static function linkCalendars(\Baikal\Model\User $user) {
		return \BaikalAdmin\Controller\User\Calendars::buildRoute(array(
			"user" => $user->get("id"),
		));
	}
	
	public static function linkAddressBooks(\Baikal\Model\User $user) {
		return \BaikalAdmin\Controller\User\AddressBooks::buildRoute(array(
			"user" => $user->get("id"),
		));
	}
}