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

abstract class View extends \Flake\Core\FLObject {
    protected $aData;

    function __construct() {
        $this->aData = [];
    }

    function setData($sName, $mData) {
        $this->aData[$sName] = $mData;
    }

    function getData() {
        return $this->aData;
    }

    function get($sWhat) {
        if (array_key_exists($sWhat, $this->aData)) {
            return $this->aData[$sWhat];
        }

        return false;
    }

    function render() {
        $sTemplatePath = $this->templatesPath();
        $oTemplate = new \Flake\Core\Template($this->templatesPath());

        return $oTemplate->parse($this->getData());
    }

    abstract function templatesPath();
}
