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

use Symfony\Component\Yaml\Yaml;

ini_set("session.cookie_httponly", 1);
ini_set("log_errors", 1);
$maxtime = ini_get('max_execution_time');
if ($maxtime != 0 && $maxtime < 3600) {
    ini_set('max_execution_time', 3600); // 1 hour
}
ini_set('ignore_user_abort', true);
error_reporting(E_ALL);

define("BAIKAL_CONTEXT", true);
define("BAIKAL_CONTEXT_INSTALL", true);
define("PROJECT_CONTEXT_BASEURI", "/admin/install/");

if (file_exists(dirname(dirname(getcwd())) . "/Core")) {
    # Flat FTP mode
    define("PROJECT_PATH_ROOT", dirname(dirname(getcwd())) . "/");    #../../
} else {
    # Dedicated server mode
    define("PROJECT_PATH_ROOT", dirname(dirname(dirname(getcwd()))) . "/");    # ../../../
}

if (!file_exists(PROJECT_PATH_ROOT . 'vendor/')) {
    exit('<h1>Incomplete installation</h1><p>Ba&iuml;kal dependencies have not been installed. If you are a regular user, this means that you probably downloaded the wrong zip file.</p><p>To install the dependencies manually, execute "<strong>composer install</strong>" in the Ba&iuml;kal root folder.</p>');
}

require PROJECT_PATH_ROOT . "vendor/autoload.php";

# Bootstraping Flake
\Flake\Framework::bootstrap();

# Bootstrap BaikalAdmin
\BaikalAdmin\Framework::bootstrap();

# Create and setup a page object
$oPage = new \Flake\Controller\Page(BAIKALADMIN_PATH_TEMPLATES . "Page/index.html");
$oPage->injectHTTPHeaders();
$oPage->setTitle("Baïkal Maintainance");
$oPage->setBaseUrl(PROJECT_URI);

$oPage->zone("navbar")->addBlock(new \BaikalAdmin\Controller\Navigation\Topbar\Install());

try {
    $config = Yaml::parseFile(PROJECT_PATH_CONFIG . "baikal.yaml");
} catch (\Exception $e) {
    $config = null;
    error_log('Error reading baikal.yaml file : ' . $e->getMessage());
}

if (!$config || !isset($config['system']["configured_version"])) {
    # we have to upgrade Baïkal (existing installation)
    $oPage->zone("Payload")->addBlock(new \BaikalAdmin\Controller\Install\Initialize());
} elseif (!isset($config['system']["admin_passwordhash"])) {
    # we have to set an admin password
    $oPage->zone("Payload")->addBlock(new \BaikalAdmin\Controller\Install\Initialize());
} else {
    if ($config['system']["configured_version"] !== BAIKAL_VERSION) {
        # we have to upgrade Baïkal
        if (\Flake\Util\Tools::GET("upgradeConfirmed")) {
            $oPage->zone("Payload")->addBlock(new \BaikalAdmin\Controller\Install\VersionUpgrade());
        } else {
            $oPage->zone("Payload")->addBlock(new \BaikalAdmin\Controller\Install\UpgradeConfirmation());
        }
    } elseif (!file_exists(PROJECT_PATH_SPECIFIC . '/INSTALL_DISABLED')) {
        $oPage->zone("Payload")->addBlock(new \BaikalAdmin\Controller\Install\Database());
    } else {
        echo "Installation was already completed. Please head to the admin interface to modify any settings.\n";
        exit();
    }
}

# Render the page
echo $oPage->render();
