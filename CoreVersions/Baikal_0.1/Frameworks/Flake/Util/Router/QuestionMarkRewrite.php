<?php

namespace Flake\Util\Router;

class QuestionMarkRewrite extends \Flake\Util\Router {
	
	public static function getCurrentRoute() {
		
		$sUrl = \Flake\Util\Tools::trimSlashes($GLOBALS["_SERVER"]["REQUEST_URI"]);		# REDIRECT_URL for ServerRewrite
		$sScriptDir = \Flake\Util\Tools::appendSlash(dirname($GLOBALS["_SERVER"]["SCRIPT_NAME"]));

		if(trim($sUrl) === "") {
			return "default";
		} else {
			$aURI = parse_url($sUrl);
			
			$sRoutePart = \Flake\Util\Tools::stripBeginSlash($aURI["query"]);
			$aMatches = array();
			
			$aRoutes = self::getRoutes();
			reset($aRoutes);
			foreach($aRoutes as $sDefinedRoute => $sDefinedControler) {
				if(strpos($sRoutePart, $sDefinedRoute) !== FALSE) {
					
					# found a match
					$iSlashCount = substr_count($sDefinedRoute, "/");
					if(!array_key_exists($iSlashCount, $aMatches)) {
						$aMatches[$iSlashCount] = array();
					}
					
					$aMatches[$iSlashCount][] = $sDefinedRoute;
				}
			}
			
			if(empty($aMatches)) {
				return "default";
			}
			
			$aBestMatches = array_pop($aMatches);	// obtains the deepest matching route (higher number of slashes)
			return array_shift($aBestMatches);		// first route amongst best matches
		}
		
		return $sRoute;
	}
	
	public static function buildRoute(/* undetermined number of parameters */) {
		$aParams = func_get_args();
		$sUrl = call_user_func_array("parent::buildRoute", $aParams);
		return "?" . $sUrl;
	}
	
	public static function getURLParams() {
		$aTokens = \Flake\Util\Tools::getUrlTokens();
		
		# stripping route and "?" tokens
		if(($iPosQuestionMark = array_search("?", $aTokens)) !== FALSE) {
			# Pos+0 = position of "?"
			# Pos+1 = position of "route"
			# Pos+2 = position of first param
			return array_slice($aTokens, $iPosQuestionMark + 2);
		}

		return array();
	}
}