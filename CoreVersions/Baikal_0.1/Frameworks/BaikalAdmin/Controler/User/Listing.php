<?php

namespace BaikalAdmin\Controler\User;

class Listing extends \Flake\Core\Controler {
	
	function execute() {
	}
	
	function render() {
		$aRes = array();
		
		$oUsers = \BaikalAdmin\Model\User::getBaseRequester()
//				->addClauseLike("username", "om")
				->execute();
		
		$oView = new \BaikalAdmin\View\User\Listing();
		$oView->setData("users", $oUsers);
		
		return $oView->render();
	}
}