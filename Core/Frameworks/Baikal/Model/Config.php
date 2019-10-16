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

    protected $sConfigFilePath = "";
    protected $aData = [];

    function __construct($sConfigFilePath) {

        # Note: no call to parent::__construct() to avoid erasing $this->aData
        $this->sConfigFilePath = $sConfigFilePath;

        try {
            $config = Yaml::parseFile($this->sConfigFilePath);
            $aConfig = $config['parameters'];
        } catch (\Exception $e) {
            error_log('Error reading "' . $this->sConfigFilePath . '" file : ' . $e->getMessage());
            $aConfig = static::getDefaultConfig();
        }


        foreach (array_keys($this->aData) as $sProp) {
            if (array_key_exists($sProp, $aConfig)) {
                $this->aData[$sProp] = $aConfig[$sProp];
            }
        }
    }

    protected function getConfigAsString() {
        if (file_exists($this->sConfigFilePath)) {
            return Yaml::parseFile($this->sConfigFilePath);
        } else {
            return static::getDefaultConfig();
        }
    }

    function writable() {
        return (
            @file_exists($this->sConfigFilePath) &&
            @is_file($this->sConfigFilePath) &&
            @is_writable($this->sConfigFilePath)
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
        $yaml = Yaml::dump([
            'parameters' => $this->aData
        ]);
        file_put_contents($this->sConfigFilePath, $yaml);
    }

    function destroy() {

    }

    protected static function getDefaultConfig() {
    }
}
