<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://flake.codr.fr
#
#  This script is part of the Flake project. The Flake
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

namespace Flake\Controller;

class Cli extends \Flake\Core\Render\Container {
    /**
     * @var array
     */
    private $aArgs;

    function render() {
        $this->sys_init();
        $this->init();

        $this->echoFlush($this->notice("process started @" . strftime("%d/%m/%Y %H:%M:%S")));
        $this->execute();
        $this->echoFlush($this->notice("process ended @" . strftime("%d/%m/%Y %H:%M:%S")) . "\n\n");
    }

    function execute() {
        foreach ($this->aSequence as $aStep) {
            $aStep["block"]->execute();
        }
    }

    public $sLog = "";

    function sys_init() {
        $this->rawLine("Command line: " . (implode(" ", $_SERVER["argv"])));
        $this->initArgs();
    }

    function init() {
    }

    function initArgs() {
        $sShortOpts = "";
        $sShortOpts .= "h";        // help; pas de valeur
        $sShortOpts .= "w:";    // author; valeur obligatoire

        $aLongOpts = [
            "help",        // help; pas de valeur
            "helloworld",    // author; pas de valeur
        ];

        $this->aArgs = getopt($sShortOpts, $aLongOpts);
    }

    function getScriptPath() {
        return realpath($_SERVER['argv'][0]);
    }

    function getSyntax() {
        return $this->getScriptPath();
    }

    function syntaxError() {
        $sStr = $this->rawLine("Syntax error.\nUsage: " . $this->getSyntax());
        exit("\n\n" . $sStr . "\n\n");
    }

    function log($sStr) {
        $this->sLog .= $sStr;
    }

    function header($sMsg) {
        $sStr = "\n" . str_repeat("#", 80);
        $sStr .= "\n" . "#" . str_repeat(" ", 78) . "#";
        $sStr .= "\n" . "#" . str_pad(strtoupper($sMsg), 78, " ", STR_PAD_BOTH) . "#";
        $sStr .= "\n" . "#" . str_repeat(" ", 78) . "#";
        $sStr .= "\n" . str_repeat("#", 80);
        $sStr .= "\n";

        $this->log($sStr);

        return $sStr;
    }

    function subHeader($sMsg) {
        $sStr = "\n\n# " . str_pad(strtoupper($sMsg) . " ", 78, "-", STR_PAD_RIGHT) . "\n";
        $this->log($sStr);

        return $sStr;
    }

    function subHeader2($sMsg) {
        $sStr = "\n# # " . str_pad($sMsg . " ", 76, "-", STR_PAD_RIGHT) . "\n";
        $this->log($sStr);

        return $sStr;
    }

    function textLine($sMsg) {
        $sStr = ". " . $sMsg . "\n";
        $this->log($sStr);

        return $sStr;
    }

    function rawLine($sMsg) {
        $sStr = $sMsg . "\n";
        $this->log($sStr);

        return $sStr;
    }

    function notice($sMsg) {
        $sStr = "\n" . str_pad($sMsg, 80, ".", STR_PAD_BOTH) . "\n";
        $this->log($sStr);

        return $sStr;
    }

    function getLog() {
        return $this->sLog;
    }

    function file_writeBin($sPath, $sData, $bUTF8 = true) {
        $rFile = fopen($sPath, "wb");

        if ($bUTF8 === true) {
            fputs($rFile, "\xEF\xBB\xBF" . $sData);
        } else {
            fputs($rFile, $sData);
        }

        fclose($rFile);
    }

    function echoFlush($sString = "") {
        echo $sString;
        ob_flush();
        flush();
    }
}
