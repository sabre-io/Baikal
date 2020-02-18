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

namespace Flake\Core;

abstract class Controller extends \Flake\Core\FLObject {
    protected $aParams = [];

    function __construct($aParams = []) {
        $this->aParams = $aParams;
    }

    function getParams() {
        return $this->aParams;
    }

    static function link(/*[$sParam, $sParam2, ...]*/) {
        return static::buildRoute();
    }

    static function buildRoute($aParams = []) {
        # TODO: il faut remplacer le mécanisme basé sur un nombre variable de paramètres en un mécanisme basé sur un seul paramètre "tableau"
        #$aParams = func_get_args();
        $sController = "\\" . get_called_class();
        #array_unshift($aParams, $sController);		# Injecting current controller as first param
        #return call_user_func_array($GLOBALS["ROUTER"] . "::buildRouteForController", $aParams);
        return $GLOBALS["ROUTER"]::buildRouteForController($sController, $aParams);
    }

    abstract function execute();

    abstract function render();
}
