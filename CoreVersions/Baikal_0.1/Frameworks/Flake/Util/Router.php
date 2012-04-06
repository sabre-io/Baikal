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
	
	public static function getControllerForRoute($sRoute) {
		return str_replace("\\Route", "\\Controller", self::getRouteClassForRoute($sRoute));
	}
	
	public static function getRouteClassForRoute($sRoute) {
		$aRoutes = $GLOBALS["ROUTER"]::getRoutes();
		return $aRoutes[$sRoute];
	}
	
	public static function getRouteForController($sController) {
		
		if($sController{0} !== "\\") {
			$sController = "\\" . $sController;
		}
		
		$aRoutes = $GLOBALS["ROUTER"]::getRoutes();
		
		reset($aRoutes);
		while(list($sRoute,) = each($aRoutes)) {
			if(str_replace("\\Route", "\\Controller", $aRoutes[$sRoute]) === $sController) {
				return $sRoute;
			}
		}
		
		return "";
	}
	
	public static function route(\Flake\Core\Render\Container &$oRenderContainer) {
		$sRouteClass = $GLOBALS["ROUTER"]::getRouteClassForRoute(
			$GLOBALS["ROUTER"]::getCurrentRoute()
		);
		
		$sRouteClass::execute($oRenderContainer);
	}
	
	public static function buildRouteForController($sController /* [, $sParam, $sParam2, ...] */) {

		$aParams = func_get_args();
		array_shift($aParams);	# stripping $sController
		$sRouteForController = $GLOBALS["ROUTER"]::getRouteForController($sController);
		
		array_unshift($aParams, $sRouteForController);	# Injecting route as first param
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
	
	# should be abstract, but is not, because of PHP's strict standards
	public static function getCurrentRoute() {
		
	}
	
	# should be abstract, but is not, because of PHP's strict standards
	public static function getURLParams() {
		
	}
}