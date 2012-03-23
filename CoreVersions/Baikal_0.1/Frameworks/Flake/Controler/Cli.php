<?php

namespace Flake\Controler;

class Cli extends \Flake\Core\Render\Container {
	
	function render() {
		$this->sys_init();
		$this->init();
		
		$this->echoFlush($this->notice("process started @" . strftime("%d/%m/%Y %H:%M:%S")));
		$this->execute();
		$this->echoFlush($this->notice("process ended @" . strftime("%d/%m/%Y %H:%M:%S")) . "\n\n");
	}

	function execute() {
		reset($this->aSequence);
		while(list($sKey,) = each($this->aSequence)) {
			$this->aSequence[$sKey]["block"]->execute();
		}
	}
	
	/**************************************************************************/
	
	var $sLog = "";

	function sys_init() {
		$this->rawLine("Command line: " . (implode(" ", $_SERVER["argv"])));
		$this->initArgs();
	}

	function init() {
	}

	function initArgs() {
		$sShortOpts = "";
		$sShortOpts .= "h";		// help; pas de valeur
		$sShortOpts .= "w:";	// author; valeur obligatoire

		$aLongOpts = array(
			"help",		// help; pas de valeur
			"helloworld",	// author; pas de valeur
		);

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
		die("\n\n" . $sStr . "\n\n");
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

	function file_writeBin($sPath, $sData, $bUTF8 = TRUE) {
		
		$rFile = fopen($sPath, "wb");
		
		if($bUTF8 === TRUE) {
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
