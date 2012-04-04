<?php

namespace Flake\Core;

abstract class Controler extends \Flake\Core\FLObject {
	
	protected $aParams = array();
	
	public function __construct($aParams = array()) {
		$this->aParams = $aParams;
	}
	
	abstract function execute();
	abstract function render();
	
	public static function buildRoute(/*[$sParam, $sParam2, ...]*/) {
		$aParams = func_get_args();
		$sControler = "\\" . get_called_class();
		array_unshift($aParams, $sControler);		# Injecting current controler as first param
		return call_user_func_array($GLOBALS["ROUTER"] . "::buildRouteForControler", $aParams);
	}
}