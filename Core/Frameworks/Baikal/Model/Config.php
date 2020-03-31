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

namespace Baikal\Model;

use Symfony\Component\Yaml\Yaml;

abstract class Config extends \Flake\Core\Model\NoDb {
    protected $sConfigFileSection = "";
    protected $aData = [];

    function __construct($sConfigFileSection) {
        # Note: no call to parent::__construct() to avoid erasing $this->aData
        $this->sConfigFileSection = $sConfigFileSection;

        try {
            $config = Yaml::parseFile(PROJECT_PATH_CONFIG . "baikal.yaml");
            if (isset($config[$sConfigFileSection])) {
                $aConfig = $config[$sConfigFileSection];
            } else {
                error_log('Section ' . $sConfigFileSection
                        . ' not found in config file. Using default values.');
                $aConfig = [];
            }

            foreach (array_keys($this->aData) as $sProp) {
                if (array_key_exists($sProp, $aConfig)) {
                    $this->aData[$sProp] = $aConfig[$sProp];
                }
            }
        } catch (\Exception $e) {
            error_log('Error reading baikal.yaml file : ' . $e->getMessage());
            // Keep default values in $aData
        }
    }

    protected function getConfigAsString() {
        if (file_exists(PROJECT_PATH_CONFIG . "baikal.yaml")) {
            return Yaml::parseFile(PROJECT_PATH_CONFIG . "baikal.yaml")[$this->sConfigFileSection];
        } else {
            return $this->aData;
        }
    }

    function writable() {
        return (
            @file_exists(PROJECT_PATH_CONFIG . "baikal.yaml") &&
            @is_file(PROJECT_PATH_CONFIG . "baikal.yaml") &&
            @is_writable(PROJECT_PATH_CONFIG . "baikal.yaml")
        );
    }

    static function icon() {
        return "icon-cog";
    }

    static function mediumicon() {
        return "glyph-cogwheel";
    }

    static function bigicon() {
        return "glyph2x-cogwheel";
    }

    function floating() {
        return false;
    }

    function persist() {
        if (file_exists(PROJECT_PATH_CONFIG . "baikal.yaml")) {
            $config = Yaml::parseFile(PROJECT_PATH_CONFIG . "baikal.yaml");
        } else {
            $config = [];
        }
        $config[$this->sConfigFileSection] = $this->aData;
        $yaml = Yaml::dump($config);
        file_put_contents(PROJECT_PATH_CONFIG . "baikal.yaml", $yaml);
    }

    function destroy() {
    }
}
