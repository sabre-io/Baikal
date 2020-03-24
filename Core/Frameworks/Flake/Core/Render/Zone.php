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

class Zone extends \Flake\Core\FLObject {
    private $oZonableObject;
    private $sZone;

    function __construct(&$oZonableObject, $sZone) {
        $this->oZonableObject = &$oZonableObject;
        $this->sZone = $sZone;
    }

    function addBlock($oBlock) {
        $this->oZonableObject->addBlock(
            $oBlock,
            $this->sZone
        );
    }
}
