<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://formal.codr.fr
#
#  This script is part of the Formal project. The Formal
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

namespace Formal\Form;

class Morphology {
    protected $oElements = null;

    function __construct() {
        $this->oElements = new \Flake\Core\CollectionTyped("\Formal\Element");
    }

    function add(\Formal\Element $oElement) {
        $this->oElements->push($oElement);
    }

    protected function keyForPropName($sPropName) {
        $aKeys = $this->oElements->keys();
        reset($aKeys);
        foreach ($aKeys as $sKey) {
            $oElement = $this->oElements->getForKey($sKey);

            if ($oElement->option("prop") === $sPropName) {
                return $sKey;
            }
        }

        return false;
    }

    function &element($sPropName) {
        if (($sKey = $this->keyForPropName($sPropName)) === false) {
            throw new \Exception("\Formal\Form\Morphology->element(): Element prop='" . $sPropName . "' not found");
        }

        $oElement = $this->oElements->getForKey($sKey);

        return $oElement;
    }

    function remove($sPropName) {
        if (($sKey = $this->keyForPropName($sPropName)) === false) {
            throw new \Exception("\Formal\Form\Morphology->element(): Element prop='" . $sPropName . "' not found");
        }

        $this->oElements->remove($sKey);
    }

    function elements() {
        return $this->oElements;
    }
}
