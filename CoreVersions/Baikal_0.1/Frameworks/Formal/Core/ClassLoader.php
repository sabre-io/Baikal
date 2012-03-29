<?php

namespace Formal\Core;

class ClassLoader {

	public static function register() {
		return spl_autoload_register(array(__CLASS__, 'loadClass'));
	}

	public static function loadClass($sFullClassName) {
		
		$aParts = explode("\\", $sFullClassName);
		if(count($aParts) === 1) {
			return;
		}
		
		if($aParts[0] !== "Formal") {
			return;
		}
		
		// ejecting the Radical
		$sRadical = array_shift($aParts);
		
		if($sRadical === "Formal") {
			$sRootPath = FORMAL_PATH_ROOT;
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
