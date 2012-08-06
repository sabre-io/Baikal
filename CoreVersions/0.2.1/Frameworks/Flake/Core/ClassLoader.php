<?php
#################################################################
#  Copyright notice
#
#  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://flake.codr.fr
#
#  This script is part of the Flake project. The Flake
#  project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace Flake\Core;

class ClassLoader {

	public static function register() {
		return spl_autoload_register(array(__CLASS__, 'loadClass'));
	}

	public static function loadClass($sFullClassName) {
		
		$sClassPath = FALSE;
		
		$aParts = explode("\\", $sFullClassName);
		if(count($aParts) === 1) {
			return;
		}
		
		# Extracting the Radical
		$sRadical = $aParts[0];
		
		if(in_array($sRadical, array("Flake", "Specific", "Frameworks"))) {
			
			if($sRadical === "Flake") {
				$sRootPath = FLAKE_PATH_ROOT;
			} elseif($sRadical === "Specific") {
				$sRootPath = FLAKE_PATH_SPECIFIC;
			} else {
				$sRootPath = PROJECT_PATH_FRAMEWORKS;
			}
			
			# Stripping radical
			array_shift($aParts);
			
			# Classname is the last part
			$sClassName = array_pop($aParts);
			
			# Path to class 
			$sClassPath = $sRootPath . implode("/", $aParts) . "/" . $sClassName . ".php";
			
		} elseif(count($aParts) > 1) {
			if($aParts[1] === "Framework") {
				# It must be a Flake Framework
				$sClassPath = PROJECT_PATH_FRAMEWORKS . $sRadical . "/Framework.php";
			}
		}
		
		if($sClassPath === FALSE) {
			return;
		}

		if(file_exists($sClassPath) && is_readable($sClassPath)) {
			require_once($sClassPath);
		} else {
			echo '<h1>PHP Autoload Error. Cannot find ' . $sFullClassName . ' in ' . $sClassPath . '</h1>';
			echo "<pre>" . print_r(debug_backtrace(), TRUE) . "</pre>";
			die();
		}
	}
}
