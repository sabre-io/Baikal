<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
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

namespace Flake\Util\Router;

class QuestionMarkRewrite extends \Flake\Util\Router {
    static function getCurrentRoute() {
        $aMatches = [];
        $sRouteTokens = implode("/", self::getRouteTokens());

        $aRoutes = self::getRoutes();
        reset($aRoutes);
        foreach ($aRoutes as $sDefinedRoute => $sDefinedController) {
            if (strpos($sRouteTokens, $sDefinedRoute) === 0) {
                # found a match
                $iSlashCount = substr_count($sDefinedRoute, "/");
                if (!array_key_exists($iSlashCount, $aMatches)) {
                    $aMatches[$iSlashCount] = [];
                }

                $aMatches[$iSlashCount][] = $sDefinedRoute;
            }
        }

        if (empty($aMatches)) {
            return "default";
        }

        $aBestMatches = array_pop($aMatches);    // obtains the deepest matching route (higher number of slashes)

        return array_shift($aBestMatches);        // first route amongst best matches
    }

    static function buildRoute($sRoute, $aParams = []/* [, $sParam, $sParam2, ...] */ ) {
        #		$aParams = func_get_args();
        #		array_shift($aParams);	# Stripping $sRoute

        #		$sParams = implode("/", $aParams);

        $aParamsSegments = [];
        reset($aParams);
        foreach ($aParams as $sParamName => $sParamValue) {
            $aParamsSegments[] = rawurlencode($sParamName) . "/" . rawurlencode($sParamValue);
        }

        $sParams = implode("/", $aParamsSegments);

        if (trim($sParams) !== "") {
            $sParams .= "/";
        }

        if ($sRoute === "default" && empty($aParams)) {
            $sUrl = "/";
        } else {
            $sUrl = "/" . $sRoute . "/" . $sParams;
        }

        $sUriPath = self::getUriPath();
        if ($sUriPath === "" || $sUriPath === "/") {
            if ($sUrl !== "/") {
                $sUrl = "?" . $sUrl;
            }
        } else {
            if ($sUrl !== "/") {
                $sUrl = "/" . self::getUriPath() . "?" . $sUrl;
            } else {
                $sUrl = "/" . self::getUriPath();
            }
        }

        return $sUrl;
    }

    protected static function getUrlTokens() {
        $sQuery = "";
        $sUrl = \Flake\Util\Tools::stripBeginSlash(\Flake\Util\Tools::getCurrentUrl());
        $aUrlParts = parse_url($sUrl);

        $aParams = [];
        if (array_key_exists("query", $aUrlParts)) {
            $aParams = explode("/", "?" . $aUrlParts["query"]);
        }

        return $aParams;
    }

    protected static function getRouteTokens() {
        $aUrlTokens = self::getUrlTokens();

        if (!empty($aUrlTokens)) {
            return array_slice($aUrlTokens, 1);
        }

        return [];
    }

    static function getURLParams() {
        $aTokens = self::getRouteTokens();

        # stripping route
        if (!empty($aTokens)) {
            $sRouteUrl = implode("/", $aTokens);
            $sCurrentRoute = $GLOBALS["ROUTER"]::getCurrentRoute();

            if (strpos($sRouteUrl, $sCurrentRoute) === false) {
                throw new \Exception("Flake\Util\Router\QuestionMarkRewrite::getURLParams(): unrecognized route.");
            }

            $sParams = \Flake\Util\Tools::trimSlashes(substr($sRouteUrl, strlen($sCurrentRoute)));

            $aParams = [];
            if ($sParams !== "") {
                $aParams = explode("/", $sParams);
            }

            reset($aParams);
            foreach ($aParams as $sParam => $sValue) {
                $aParams[$sParam] = rawurldecode($sValue);
            }

            return $aParams;
        }

        return [];
    }
}
