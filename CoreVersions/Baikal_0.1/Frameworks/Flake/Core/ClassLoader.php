<?php

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
