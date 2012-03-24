<?php

namespace BaikalAdmin\Core;

class ClassLoader {

	public static function register() {
		return spl_autoload_register(array(get_called_class(), 'loadClass'));
	}

	public static function loadClass($sFullClassName) {
		
		$aParts = explode("\\", $sFullClassName);
		if(count($aParts) === 1) {
			return;
		}
		
		if($aParts[0] !== "BaikalAdmin") {
			return;
		}
		
		// ejecting the Radical
		$sRadical = array_shift($aParts);
		
		if($sRadical === "BaikalAdmin") {
			$sRootPath = BAIKALADMIN_PATH_ROOT;
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
