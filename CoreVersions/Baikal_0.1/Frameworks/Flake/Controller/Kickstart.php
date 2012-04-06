<?php

namespace Flake\Controller;

class Kickstart extends \Flake\Core\Controller {
		
	public function execute() {
		if(array_key_exists("helloworld", self::cli()->aArgs)) {
			$this->action_helloworld();
		}
	}
	
	public static function &cli() {
		return $GLOBALS["oCli"];
	}
	
	public function __call($sName, $aArguments) {
		if(substr($sName, 0, 4) === "cli_") {
			$sCallName = substr($sName, 4);
			
			if(method_exists(self::cli(), $sCallName)) {
				return call_user_func_array(array(self::cli(), $sCallName), $aArguments);
			}
		}
		
		die("Undefined method " . $sName);
	}
	
	public function render() {
	}
	
	public function action_helloworld() {
		$this->cli_echoFlush($this->cli_header("Hello, World !"));
	}
}