<?php

namespace BaikalAdmin\Controller;

class Details extends \Flake\Core\Controller {

	function execute() {
	}

	function render() {
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		if(($iUser = intval($aParams[0])) === 0) {
			throw new \Exception("BaikalAdmin\Controller\Details::render(): User get-parameter not found.");
		}
		
		$oUser = new \Baikal\Model\User($iUser);
		
		return "<h2>Details for user " . $oUser->getLabel() . "</h2>";
	}
}