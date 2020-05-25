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

namespace BaikalAdmin\Controller\Install;

use Symfony\Component\Yaml\Yaml;

class UpgradeConfirmation extends \Flake\Core\Controller {
    function execute() {
    }

    function render() {
        $oView = new \BaikalAdmin\View\Install\UpgradeConfirmation();

        try {
            $config = Yaml::parseFile(PROJECT_PATH_CONFIG . "baikal.yaml");
        } catch (\Exception $e) {
            error_log('Error reading baikal.yaml file : ' . $e->getMessage());
        }

        if (isset($config['system']['configured_version']) && $config['system']['configured_version'] === BAIKAL_VERSION) {
            $sMessage = "Your system is configured to use version <strong>" . $config['system']['configured_version'] . "</strong>.<br />There's no upgrade to be done.";
        } else {
            $oldVersion = "Unknown";
            if (isset($config['system']['configured_version'])) {
                $oldVersion = $config['system']['configured_version'];
            }
            $sMessage = "Upgrading Baïkal from version <strong>$oldVersion</strong> to version <strong>" . BAIKAL_VERSION . "</strong>";
        }

        $oView->setData("message", $sMessage);
        $oView->setData("projectUri", PROJECT_URI);

        return $oView->render();
    }
}
