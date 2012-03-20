<?php

$sDir = realpath(dirname(__FILE__)) . "/";
require_once($sDir . "Colorize.php");
require_once($sDir . "TabulatorColumn.php");
require_once($sDir . "Tabulator.php");

class TabulatorColumnCli extends TabulatorColumn {
	
	protected $iLen = 0;
	
	public function addValue($sValue) {
		parent::addValue($sValue);
		
		$iLen = mb_strlen($sValue, "utf8");
		if($iLen > $this->iLen) {
			$this->iLen = $iLen;
		}
	}
	
	protected function wrap($sString) {
		if($this->getType() === "numeric") {
			$sPadType = STR_PAD_LEFT;
		} else {
			$sPadType = STR_PAD_RIGHT;
		}
		
		return $this->mb_str_pad($sString, $this->iLen, " ", $sPadType);
	}
	
	public function renderUnderline() {
		return $this->wrap(str_repeat("-", $this->iLen));
	}
	
	protected function mb_str_pad($input, $pad_length, $pad_string=' ', $pad_type=STR_PAD_RIGHT) {
	    $diff = intval(mb_strlen($input, "latin1") - mb_strlen($input, "utf8"));
	    return str_pad($input, $pad_length+$diff, $pad_string, $pad_type);
	}
}

class TabulatorCli extends Tabulator {
	
	public $iRepeatHeadersEvery = 20;
	
	public function getSep() {
		return "    ";
	}
	
	public function repeatHeadersEvery($iNum = FALSE) {
		if(intval($iNum) > 0) {
			$this->iRepeatHeadersEvery = intval($iNum);
		}
		
		return $this->iRepeatHeadersEvery;
	}
	
	protected function wrapHeaders($sHeaders) {
		
		$aHeaderUnderline = array();
		reset($this->aColumns);
		while(list($sName,) = each($this->aColumns)) {
			$aHeaderUnderline[] = $this->aColumns[$sName]->renderUnderline();
		}
		
		$sHeaders = Colorize::getColoredString($sHeaders, "white", "red");
		#$sUnderline = Colorize::getColoredString(implode($this->getSep(), $aHeaderUnderline), "white", "red");
		
		#return "\n" . $sHeaders . "\n" . $sUnderline . "\n";
		return "\n" . $sHeaders . "\n";
	}
	
	protected function wrapValues($sValues, $iRowNum) {
		if(($iRowNum % 2) === 0) {
			return Colorize::getColoredString($sValues, "light_gray") . "\n";
		}
		
		return $sValues . "\n";
	}
	
	function renderOpen() {
		return "";
	}
	
	function renderClose() {
		return "";
	}
}
