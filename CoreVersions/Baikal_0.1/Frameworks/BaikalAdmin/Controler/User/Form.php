<?php

namespace BaikalAdmin\Controler\User;

class Form extends \Flake\Core\Controler {
	
	const BASEPATH = "/admin/";
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
	
	function execute() {
		
		if(($iUser = self::editRequested()) !== FALSE) {
			if($this->oForm->submitted()) {
				$this->oForm->execute();
			}
		}
		
		if($this->newRequested()) {
			if($this->oForm->submitted()) {
				$this->oForm->execute();
				
				if($this->oForm->persisted()) {
					$this->oForm->setOption(
						"action",
						$this->linkEdit(
							$this->oForm->getModelInstance()
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
				\Flake\Util\Tools::redirectUsingMeta(self::BASEPATH);
			} else {
				
				$oUser = new \Baikal\Model\User($iUser);
				$this->aMessages[] = \Formal\Core\Message::warningConfirmMessage(
					"Check twice, you're about to delete " . $oUser->get("username") . "</strong> from the database !",
					"<p>You are about to delete a user and all it's calendars / contacts. This operation cannot be undone.</p><p>So, now that you know all that, what shall we do ?</p>",
					self::linkDeleteConfirm($oUser),
					"Delete <strong><i class='" . $oUser->getIcon() . " icon-white'></i> " . $oUser->getLabel() . "</strong>",
					self::BASEPATH
				);
			}
		}
	}
	
	function initForm() {
		if($this->editRequested() || $this->newRequested()) {
			$aOptions = array(
				"closeurl" => $this::BASEPATH
			);
			
			$this->oForm = $this->oModel->formForThisModelInstance($aOptions);
		}
	}
	
	public static function editRequested() {
		if(($iUser = intval(\Flake\Util\Tools::GET("useredit"))) > 0) {
			return $iUser;
		}
		
		return FALSE;
	}
	
	public static function deleteRequested() {
		if(($iUser = intval(\Flake\Util\Tools::GET("userdel"))) > 0) {
			return $iUser;
		}
		
		return FALSE;
	}
	
	public static function deleteConfirmed() {
		if(($iUser = self::deleteRequested()) === FALSE) {
			return FALSE;
		}
		
		if(intval(\Flake\Util\tools::GET("userdelconfirm")) === 1) {
			return $iUser;
		}
		
		return FALSE;
	}
	
	public static function newRequested() {
		return (intval(\Flake\Util\Tools::GET("usernew")) === 1);
	}
	
	function render() {
		
		$sHtml = "<a id='edituser'></a>";
		$sMessages = implode("\n", $this->aMessages);

		if(($iUser = self::editRequested()) !== FALSE) {
			# We have to display the edition form for the requested user
			$oUser = new \Baikal\Model\User($iUser);
			$oView = new \BaikalAdmin\View\User\Form();
			
			$oView->setData("user", $oUser);

			$sHtml .= $this->oForm->render();
			$sHtml .= $oView->render();
			
		} elseif(self::newRequested()) {
			$sHtml .= $this->oForm->render();
		} else {
			# No form is displayed
			# Simply display messages, if any
			$sHtml .= $sMessages;
		}
		
		return $sHtml;
	}
	
	public static function linkNew() {
		return self::BASEPATH . "?usernew=1#edituser";
	}
	
	public static function linkEdit(\Baikal\Model\User $user) {
		return self::BASEPATH . "?useredit=" . $user->get("id") . "#edituser";
	}
	
	public static function linkDelete(\Baikal\Model\User $user) {
		return self::BASEPATH . "?userdel=" . $user->get("id") . "#confirm";
	}
	
	public static function linkDeleteConfirm(\Baikal\Model\User $user) {
		return self::BASEPATH . "?userdel=" . $user->get("id") . "&userdelconfirm=1#confirm";
	}
	
	public static function linkDetails(\Baikal\Model\User $user) {
		return self::BASEPATH . "?userdetails=" . $user->get("id") . "#confirm";
	}
}