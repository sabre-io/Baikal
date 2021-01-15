<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
*  All rights reserved
*
*  http://sabre.io/baikal
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

ini_set("session.cookie_httponly", 1);
ini_set("display_errors", 0);
ini_set("log_errors", 1);

define("BAIKAL_CONTEXT", true);
define("PROJECT_CONTEXT_BASEURI", "/");

if (file_exists(getcwd() . "/Core")) {
    # Flat FTP mode
    define("PROJECT_PATH_ROOT", getcwd() . "/");    #./
} else {
    # Dedicated server mode
    define("PROJECT_PATH_ROOT", dirname(getcwd()) . "/");    #../
}

if (!file_exists(PROJECT_PATH_ROOT . 'vendor/')) {
    exit('<h1>Incomplete installation</h1><p>Ba&iuml;kal dependencies have not been installed. If you are a regular user, this means that you probably downloaded the wrong zip file.</p><p>To install the dependencies manually, execute "<strong>composer install</strong>" in the Ba&iuml;kal root folder.</p>');
}

require PROJECT_PATH_ROOT . 'vendor/autoload.php';

# Bootstraping Flake
\Flake\Framework::bootstrap();

# Bootstrapping Baïkal
\BaikalAdmin\Framework::bootstrap();

# Create and setup a page object
$oPage = new \Flake\Controller\Page(PROJECT_PATH_ROOT . "Core/Resources/Web/Baikal/Templates/Page/index.html");
$oPage->injectHTTPHeaders();
$oPage->setTitle("Baïkal server");
$oPage->setBaseUrl(PROJECT_URI);

# Draw page
$oPage->zone("navbar")->addBlock(new \Baikal\Controller\Navigation\Topbar\Anonymous());
$oPage->zone("Payload")->addBlock(new \Baikal\Controller\Main());

# Render the page
echo $oPage->render();
