<?php

namespace BaikalAdmin\Controler\User;

class Form extends \Flake\Core\Controler {
	
	const BASEPATH = "/admin/";
	protected $aMessages = array();
	
	public function __construct() {
		parent::__construct();
		if(($iUser = self::editRequested()) !== FALSE) {
			$this->oModel = new \BaikalAdmin\Model\User($iUser);
		}
		
		$this->initForm();
	}
	
	function execute() {
		
		if(($iUser = self::editRequested()) !== FALSE) {
			if($this->oForm->submitted()) {
				$this->oForm->execute();
/*				
				$aPost = \Flake\Util\Tools::POST();
				$aErrors = array();
				
				if(($sDisplayName = trim($aPost["displayname"])) === "") {
					$aErrors[] = array(
						"prop" => "displayname",
						"msg" => "Display name is mandatory.",
					);
				}
				
				if(($sEmail = trim($aPost["email"])) === "") {
					$aErrors[] = array(
						"prop" => "email",
						"msg" => "Email is mandatory.",
					);
				} elseif(!\Flake\Util\Tools::validEmail($sEmail)) {
					$aErrors[] = array(
						"prop" => "email",
						"msg" => "Email is not valid.",
					);
				}
				
				if(count($aErrors) === 0) {
					$oUser = new \BaikalAdmin\Model\User($iUser);
					$oUser->set("displayname", $sDisplayName);
					$oUser->set("email", $sEmail);
					$oUser->persist();
					
					$this->aMessages[] = self::infoMessage(
						"Success",
						"Changes on <i class='icon-user'></i> <strong>" . $oUser->get("username") . "</strong> have been saved."
					);
				}
*/
			}
		}
		
		if(($iUser = self::deleteRequested()) !== FALSE) {
			
			if(self::deleteConfirmed() !== FALSE) {
				
				# catching Exception thrown when model already destroyed
					# happens when user refreshes delete-page, for instance
					
				try {
					$oUser = new \BaikalAdmin\Model\User($iUser);
					$oUser->destroy();				
				} catch(\Exception $e) {
					# user is already deleted; silently discarding
				}
				
				# Redirecting to admin home
				\Flake\Util\Tools::redirectUsingMeta(self::BASEPATH);
			} else {
				
				$oUser = new \BaikalAdmin\Model\User($iUser);
				$this->aMessages[] = self::warningConfirmMessage(
					"Check twice, you're about to delete " . $oUser->get("username") . "</strong> from the database !",
					"<p>You are about to delete a user and all it's calendars / contacts. This operation cannot be undone.</p><p>So, now that you know all that, what shall we do ?</p>",
					self::linkDeleteConfirm($oUser),
					"Delete user <strong>" . $oUser->get("username") . "</strong>",
					self::BASEPATH
				);
			}
		}
	}
	
	function initForm() {
		$aOptions = array(
			"closeurl" => $this::BASEPATH
		);
		
		if($this->editRequested()) {
			$this->oForm = $this->oModel->formForInstance($aOptions);
		} else {
			$this->oForm = \BaikalAdmin\Model\User::formEmpty(array(
				"closeurl" => $this::BASEPATH
			));
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
	
	function render() {
		
		$sHtml = "<a id='edituser'></a>";
		$sMessages = implode("\n", $this->aMessages);

		if(($iUser = self::editRequested()) !== FALSE) {
			$oUser = new \BaikalAdmin\Model\User($iUser);
			$oView = new \BaikalAdmin\View\User\Form();
			
			$oView->setData("user", $oUser);
			$oView->setData("messages", $sMessages);
			
			$sHtml .= $this->oForm->render();
			$sHtml .= $oView->render();
			
		} else {
			$sHtml .= $sMessages;
		}
		
		return $sHtml;
	}
	
	public static function linkEdit(\BaikalAdmin\Model\User $user) {
		return self::BASEPATH . "?useredit=" . $user->get("id") . "#edituser";
	}
	
	public static function linkDelete(\BaikalAdmin\Model\User $user) {
		return self::BASEPATH . "?userdel=" . $user->get("id") . "#confirm";
	}
	
	public static function linkDeleteConfirm(\BaikalAdmin\Model\User $user) {
		return self::BASEPATH . "?userdel=" . $user->get("id") . "&userdelconfirm=1#confirm";
	}
	
	public static function linkDetails(\BaikalAdmin\Model\User $user) {
		return self::BASEPATH . "?userdetails=" . $user->get("id") . "#confirm";
	}
	
	protected static function infoMessage($sHeader, $sDescription) {
		$sHtml =<<<HTML
<div id="info" class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	{$sDescription}
</div>
HTML;
		return $sHtml;
	}
	
	protected static function warningConfirmMessage($sHeader, $sDescription, $sActionUrl, $sActionLabel, $sCancelUrl, $sCancelLabel="Cancel") {
		$sHtml =<<<HTML
<div id="confirm" class="alert alert-block alert-error">
	<!--a class="close" data-dismiss="alert" href="#">&times;</a-->
	<h3 class="alert-heading">{$sHeader}</h3>
	{$sDescription}
	<p>
		<a class="btn btn-danger" href="{$sActionUrl}">{$sActionLabel}</a> <a class="btn" href="{$sCancelUrl}">Cancel</a>
	</p>
</div>
HTML;
		return $sHtml;
	}
}