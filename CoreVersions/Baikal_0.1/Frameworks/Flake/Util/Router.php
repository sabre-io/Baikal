<?php

namespace Flake\Util;

abstract class Router extends \Flake\Core\FLObject {
	
	/* ----------------------- COMMON METHODS ------------------------------*/
	
	private function __construct() {
		# private constructor for static class
	}
	
	public static function getRoutes() {
		reset($GLOBALS["ROUTES"]);
		return $GLOBALS["ROUTES"];
	}
	
	public static function getControlerForRoute($sRoute) {
		$aRoutes = $GLOBALS["ROUTER"]::getRoutes();
		return $aRoutes[$sRoute];
	}
	
	public static function getRouteForControler($sControler) {
		
		if($sControler{0} !== "\\") {
			$sControler = "\\" . $sControler;
		}
		
		$aRoutes = $GLOBALS["ROUTER"]::getRoutes();
		
		reset($aRoutes);
		while(list($sRoute,) = each($aRoutes)) {
			if(str_replace("\\Route", "\\Controler", $aRoutes[$sRoute]) === $sControler) {
				return $sRoute;
			}
		}
		
		return "";
	}
	
	public static function route(\Flake\Core\Render\Container &$oRenderContainer) {
		$sControler = $GLOBALS["ROUTER"]::getControlerForRoute(
			$GLOBALS["ROUTER"]::getCurrentRoute()
		);
		
		$sControler::execute($oRenderContainer);
	}
	
	public static function buildRouteForControler($sControler /* [, $sParam, $sParam2, ...] */) {

		$aParams = func_get_args();
		array_shift($aParams);	# stripping $sControler
		$sRouteForControler = $GLOBALS["ROUTER"]::getRouteForControler($sControler);
		
		array_unshift($aParams, $sRouteForControler);	# Injecting route as first param
		return call_user_func_array($GLOBALS["ROUTER"] . "::buildRoute", $aParams);
	}
	
	public static function buildCurrentRoute(/*[$sParam, $sParam2, ...]*/) {
		$aParams = func_get_args();
		$sCurrentRoute = $GLOBALS["ROUTER"]::getCurrentRoute();
		
		array_unshift($aParams, $sCurrentRoute);	# Injecting route as first param
		return call_user_func_array($GLOBALS["ROUTER"] . "::buildRoute", $aParams);
	}
	
	/* ----------------------- CHANGING METHODS ----------------------------*/
	
	# this method is likely to change with every Router implementation
	public static function buildRoute($sRoute /* [, $sParam, $sParam2, ...] */) {
		$aParams = func_get_args();
		array_shift($aParams);	# Stripping $sRoute 
		
		$sParams = implode("/", $aParams);
		if(trim($sParams) !== "") {
			$sParams .= "/";
		}
		
		return "/" . $sRoute . "/" . $sParams;
	}
	
	public static abstract function getCurrentRoute();
	public static abstract function getURLParams();
}