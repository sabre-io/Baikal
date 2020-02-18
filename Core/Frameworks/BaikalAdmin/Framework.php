<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://sabre.io/baikal
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
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

namespace BaikalAdmin;

class Framework extends \Flake\Core\Framework {
    static function bootstrap() {
        define("BAIKALADMIN_PATH_ROOT", PROJECT_PATH_ROOT . "Core/Frameworks/BaikalAdmin/");    # ./

        \Baikal\Framework::bootstrap();
        \Formal\Framework::bootstrap();

        $GLOBALS["ROUTER"]::setURIPath("admin/");

        # Include BaikalAdmin Framework config
        require_once BAIKALADMIN_PATH_ROOT . "config.php";
    }
}
