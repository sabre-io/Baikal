<?php

namespace Flake\Util;

class Router extends \Flake\Core\FLObject {
	
	public static function getCurrentRoute() {
		
		$sUrl = trim($GLOBALS["_SERVER"]["REDIRECT_URL"]);
		
		if($sUrl{0} === "/") {
			$sUrl = substr($sUrl, 1);
		}
		
		if($sUrl{strlen($sUrl) - 1} === "/") {
			$sUrl = substr($sUrl, 0, -1);
		}
		
		$aParts = explode("/", $sUrl);
		$sRoute = $aParts[0];
		
		if(trim($sUrl) === "") {
			$sRoute = "default";
		} else {
			if(!array_key_exists($sRoute, $GLOBALS["ROUTES"])) {
				$sRoute = "default";
			}
		}
		
		return $sRoute;
	}
	
	public static function getControlerForRoute($sRoute) {		
		return $GLOBALS["ROUTES"][$sRoute];
	}
	
	public static function getRouteForControler($sControler) {
		
		if($sControler{0} !== "\\") {
			$sControler = "\\" . $sControler;
		}
		
		reset($GLOBALS["ROUTES"]);
		while(list($sRoute,) = each($GLOBALS["ROUTES"])) {
			if(str_replace("\\Route", "\\Controler", $GLOBALS["ROUTES"][$sRoute]) === $sControler) {
				return $sRoute;
			}
		}
		
		return "";
	}
	
	public static function route(\Flake\Core\Render\Container &$oRenderContainer) {
		$sControler = self::getControlerForRoute(
			self::getCurrentRoute()
		);
		
		$sControler::execute($oRenderContainer);
	}
	
	public static function buildRouteForControlerWithParams() {
		$aParams = func_get_args();
		$sControler = array_shift($aParams);
		$sRouteForControler = self::getRouteForControler($sControler);
		return "/" . $sRouteForControler . "/" . implode("/", $aParams);
	}
	
/*	public static function buildRouteWithParams() {
		$aParams = func_get_args();
		
		list(, $aCall) = debug_backtrace(FALSE);
		$sClass = $aCall["class"];
		$sClass = str_replace("\\Route", "", $sClass);
		
		$sRouteForControler = self::getRouteForControler($sClass);
		return "/" . $sRouteForControler . "/" . implode("/", $aParams);
	}*/
}