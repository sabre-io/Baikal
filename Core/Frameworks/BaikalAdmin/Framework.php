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

#This class belongs to to the BaikalAdmin namespace
namespace BaikalAdmin;

#the current Framework class extends the Framework class in the directory Flake\Core\Framework
class Framework extends \Flake\Core\Framework { 
    static function bootstrap() {
        define("BAIKALADMIN_PATH_ROOT", PROJECT_PATH_ROOT . "Core/Frameworks/BaikalAdmin/");    # ./
        #this defines a constant BAIKALADMIN_PATH_ROOT. It's set to the project root path plus "Core/Frameworks/BaikalAdmin/".

        \Baikal\Framework::bootstrap();
        \Formal\Framework::bootstrap();

        $GLOBALS["ROUTER"]::setURIPath("admin/");
        #This calls the setURIPath() method on the ROUTER object (which is stored in the global scope), setting the URI path to "admin/"

        # Include BaikalAdmin Framework config
        require_once BAIKALADMIN_PATH_ROOT . "config.php";
        # This includes the config.php file from the BAIKALADMIN_PATH_ROOT directory. 
        # The require_once ensures this file is included only once.
    }
}
