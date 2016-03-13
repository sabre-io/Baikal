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


namespace Flake\Core\Render;

abstract class Container extends \Flake\Core\Controller {

    public $aSequence = [];
    public $aBlocks = [];
    public $aRendu = [];
    public $aZones = [];

    function addBlock(&$oBlock, $sZone = "_DEFAULT_") {
        $aTemp = [
            "block" => &$oBlock,
            "rendu" => "",
        ];
        $this->aSequence[] = & $aTemp;
        $this->aBlocks[$sZone][] = & $aTemp["rendu"];
    }

    function &zone($sZone) {
        if (!array_key_exists($sZone, $this->aZones)) {
            $this->aZones[$sZone] = new \Flake\Core\Render\Zone($this, $sZone);
        }

        return $this->aZones[$sZone];
    }

    function render() {
        $this->execute();
        $aRenderedBlocks = $this->renderBlocks();
        return implode("", $aRenderedBlocks);
    }

    function execute() {
        reset($this->aSequence);
        while (list($sKey, ) = each($this->aSequence)) {
            $this->aSequence[$sKey]["block"]->execute();
        }
    }

    protected function renderBlocks() {
        $aHtml = [];
        reset($this->aSequence);
        while (list($sKey, ) = each($this->aSequence)) {
            $this->aSequence[$sKey]["rendu"] = $this->aSequence[$sKey]["block"]->render();
        }

        $aHtml = [];
        reset($this->aBlocks);
        while (list($sZone, ) = each($this->aBlocks)) {
            $aHtml[$sZone] = implode("", $this->aBlocks[$sZone]);
        }

        reset($aHtml);
        return $aHtml;
    }
}
