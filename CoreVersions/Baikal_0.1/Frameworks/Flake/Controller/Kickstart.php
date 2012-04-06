<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
*  All rights reserved
*
*  http://baikal.codr.fr
*
*  This script is part of the Baïkal Server project. The Baïkal
*  Server project is free software; you can redistribute it
*  and/or modify it under the terms of the GNU General Public
*  License as published by the Free Software Foundation; either
*  version 2 of the License, or (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

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