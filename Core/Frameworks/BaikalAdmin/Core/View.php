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

namespace BaikalAdmin\Core;

/*
 * this class is responsible for manaaging the view layer in the BaikalAdmin framework.
 * Every class under BaikalAdmin\View extends this class.
 * Its sole responsibility is to provide the path to the template file for the correct view.
 */

class View extends \Flake\Core\View {
    
    /*
    * This method returns the path to the template file for the current view.
    * It gets the class name of the current view, removes the "BaikalAdmin\View\" prefix, 
    * and replaces the backslashes with forward slashes.
    * Then returns that string appended to the end of BAIKALADMIN_PATH_TEMPLATES.
    */
    function templatesPath() {
        $sViewName = get_class($this);
        $sTemplate = str_replace("\\", "/", substr($sViewName, strlen("BaikalAdmin\\View\\"))) . ".html";

        return BAIKALADMIN_PATH_TEMPLATES . $sTemplate;
    }
}
