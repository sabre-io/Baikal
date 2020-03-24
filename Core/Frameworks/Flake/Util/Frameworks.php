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

namespace Flake\Util;

class Frameworks extends \Flake\Core\FLObject {
    private function __construct() {    # private constructor to force static class
    }

    function isAFramework($sName) {
        $sName = trim(\Flake\Util\Tools::trimSlashes($sName));
        if ($sName === "" || $sName === "." || $sName === "..") {
            return false;
        }

        $sFrameworkPath = PROJECT_PATH_FRAMEWORKS . $sName;

        return file_exists($sFrameworkPath) && is_dir($sFrameworkPath);
    }

    static function enabled($sFramework) {
        return false;
    }

    # TODO: Create a 'Framework' Model
    function getPath($sName) {
        if (self::isAFramework($sName)) {
            throw new \Flake\Core\Exception(htmlspecialchars($$sName) . " is not a framework.", $sName);
        }

        return \Flake\Util\Tools::appendSlash(PROJECT_PATH_FRAMEWORKS . $sName);
    }
}
