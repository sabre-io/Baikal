<?php

namespace Flake\Core;

abstract class Controler extends \Flake\Core\FLObject {
	
	protected $aParams = array();
	
	public function __construct($aParams = array()) {
		$this->aParams = $aParams;
	}
	
	abstract function execute();
	abstract function render();
	
	public static function buildRouteWithParams() {
		$aParams = func_get_args();
		$sControler = "\\" . get_called_class();
		array_unshift($aParams, $sControler);
		return call_user_func_array("\Flake\Util\Router::buildRouteForControlerWithParams", $aParams);
	}
}