<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
*  All rights reserved
*
*  http://baikal.codr.fr
*
*  This script is part of the Baïkal Server project. The Baïkal
*  Server project is free software; you can redistribute it
*  and/or modify it under the terms of the GNU General Public
*  License as published by the Free Software Foundation; either
*  version 2 of the License, or (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

define("BAIKALADMIN_PATH_ROOT", dirname(dirname(__FILE__)) . "/");

# Bootstrap Baïkal Core
require_once(dirname(dirname(dirname(__FILE__))) . "/Baikal/Core/Bootstrap.php");	# ../../, symlink-safe

# Bootstrap Formal
require_once(dirname(dirname(dirname(__FILE__))) . "/Formal/Core/Bootstrap.php");

# Registering BaikalAdmin classloader
require_once(dirname(__FILE__) . '/ClassLoader.php');
\BaikalAdmin\Core\ClassLoader::register();

# Relative to BAIKAL_URI; so that BAIKAL_URI . BAIKALADMIN_URIPATH corresponds to the full URL to Baïkal admin
define("BAIKALADMIN_URIPATH", "admin/");
$GLOBALS["ROUTER"]::setURIPath(BAIKALADMIN_URIPATH);

# Include BaikalAdmin Framework config
require_once(BAIKALADMIN_PATH_ROOT . "config.php");