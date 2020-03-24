<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://formal.codr.fr
#
#  This script is part of the Formal project. The Formal
#  project is free software; you can redistribute it
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

namespace Formal;

abstract class Element {
    protected $aOptions = [
        "class"           => "",
        "inputclass"      => "input-xlarge",
        "readonly"        => false,
        "validation"      => "",
        "error"           => false,
        "placeholder"     => "",
        "help"            => "",
        "popover"         => "",
        "refreshonchange" => false,
    ];

    protected $sValue = "";

    function __construct($aOptions) {
        $this->aOptions = array_merge($this->aOptions, $aOptions);
    }

    function option($sName) {
        if (array_key_exists($sName, $this->aOptions)) {
            return $this->aOptions[$sName];
        }

        throw new \Exception("\Formal\Element->option(): Option '" . htmlspecialchars($sName) . "' not found.");
    }

    function optionArray($sOptionName) {
        $sOption = trim($this->option($sOptionName));
        if ($sOption !== "") {
            $aOptions = explode(",", $sOption);
        } else {
            $aOptions = [];
        }

        reset($aOptions);

        return $aOptions;
    }

    function setOption($sOptionName, $sOptionValue) {
        $this->aOptions[$sOptionName] = $sOptionValue;
    }

    function value() {
        return $this->sValue;
    }

    function setValue($sValue) {
        $this->sValue = $sValue;
    }

    function __toString() {
        return get_class($this) . "<" . $this->option("label") . ">";
    }

    function renderWitness() {
        return '<input type="hidden" name="witness[' . $this->option("prop") . ']" value="1" />';
    }

    function posted() {
        $aPost = \Flake\Util\Tools::POST("witness");
        if (is_array($aPost)) {
            $sProp = $this->option("prop");

            return (array_key_exists($sProp, $aPost)) && (intval($aPost[$sProp]) === 1);
        }

        return false;
    }

    abstract function render();
}
