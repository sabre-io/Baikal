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

namespace Flake\Core;

class ClassLoader {

	public static function register() {
		return spl_autoload_register(array(__CLASS__, 'loadClass'));
	}

	public static function loadClass($sFullClassName) {
		
		$aParts = explode("\\", $sFullClassName);
		if(count($aParts) === 1) {
			return;
		}
		
		if($aParts[0] !== "Flake" && $aParts[0] !== "Specific" && $aParts[0] !== "Frameworks") {
			return;
		}
		
		// ejecting the Radical
		$sRadical = array_shift($aParts);
		
		if($sRadical === "Flake") {
			$sRootPath = FLAKE_PATH_ROOT;
		} elseif($sRadical === "Specific") {
			$sRootPath = FLAKE_PATH_SPECIFIC;	# When prefix does not point another namespaced framework, we use "Specific"
		} elseif($sRadical === "Frameworks") {
			$sRootPath = FLAKE_PATH_FRAMEWORKS;
		}
		
		$sClassName = array_pop($aParts);
		$sBasePath = $sRootPath . implode("/", $aParts) . "/";
		$sClassPath = $sBasePath . $sClassName . ".php";

		if(file_exists($sClassPath) && is_readable($sClassPath)) {
			require_once($sClassPath);
		} else {
			echo '<h1>PHP Autoload Error. Cannot find ' . $sFullClassName . '</h1>';
			echo "<pre>" . print_r(debug_backtrace(), TRUE) . "</pre>";
			die();
		}
	}
}
