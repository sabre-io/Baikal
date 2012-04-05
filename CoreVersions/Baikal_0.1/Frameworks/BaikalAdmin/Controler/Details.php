<?php

namespace BaikalAdmin\Controler;

class Details extends \Flake\Core\Controler {

	function execute() {
	}

	function render() {
		$aParams = $GLOBALS["ROUTER"]::getURLParams();
		if(($iUser = intval($aParams[0])) === 0) {
			throw new \Exception("BaikalAdmin\Controler\Details::render(): User get-parameter not found.");
		}
		
		$oUser = new \Baikal\Model\User($iUser);
		
		return "<h2>Details for user " . $oUser->getLabel() . "</h2>";
	}
}