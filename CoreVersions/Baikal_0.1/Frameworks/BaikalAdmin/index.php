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

ini_set("display_errors", 1);
error_reporting(E_ALL);

define("BAIKAL_CONTEXT", TRUE);
define("BAIKAL_CONTEXT_ADMIN", TRUE);

# Bootstrap BaikalAdmin
require_once(dirname(__FILE__) . "/Core/Bootstrap.php");

# Evaluate assertions
\BaikalAdmin\Core\Auth::assertEnabled();
\BaikalAdmin\Core\Auth::assertAuthentified();

# Create and setup a page object
$oPage = new \Flake\Controler\Page(BAIKALADMIN_PATH_TEMPLATES . "Page/index.html");
$oPage->injectHTTPHeaders();
$oPage->setTitle(FLAKE_BASEURL);
$oPage->setBaseUrl(FLAKE_BASEURL);

# Route the request
$GLOBALS["ROUTER"]::route($oPage);

# Render the page
echo $oPage->render();