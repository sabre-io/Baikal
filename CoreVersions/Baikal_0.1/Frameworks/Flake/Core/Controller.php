<?php

namespace Flake\Core;

abstract class Controller extends \Flake\Core\FLObject {
	
	protected $aParams = array();
	
	public function __construct($aParams = array()) {
		$this->aParams = $aParams;
	}
	
	abstract function execute();
	abstract function render();
	static function link(/*[$sParam, $sParam2, ...]*/) {
		
	}
	
	public static function buildRoute(/*[$sParam, $sParam2, ...]*/) {
		$aParams = func_get_args();
		$sController = "\\" . get_called_class();
		array_unshift($aParams, $sController);		# Injecting current controller as first param
		return call_user_func_array($GLOBALS["ROUTER"] . "::buildRouteForController", $aParams);
	}
}