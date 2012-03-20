<?php

$sDir = realpath(dirname(__FILE__)) . "/";
require_once($sDir . "TabulatorColumn.php");
require_once($sDir . "Tabulator.php");

class TabulatorColumnHtml extends TabulatorColumn {
	
	protected function wrap($sString) {
		if(in_array($this->getType(), array("numeric", "duration"))) {
			$sAlign = "right";
		} else {
			$sAlign = "left";
		}
		
		return "<td class=\"col-" . $this->getName() . " align-" . $sAlign . "\">" . $sString . "</td>";
	}
	
	public function renderUnderline() {
		return $this->wrap(str_repeat("-", $this->iLen));
	}
}

class TabulatorHtml extends Tabulator {
	
	public function getSep() {
		return "";
	}
		
	protected function wrapHeaders($sHeaders) {
		return "<tr class=\"headers\">" . $sHeaders . "</tr>";
	}
	
	protected function wrapValues($sValues, $iRowNum) {
		$sClass = (($iRowNum % 2) === 0) ? "even" : "odd"; 
		return "<tr class=\"values " . $sClass . "\">" . $sValues . "</tr>";
	}
	
	function renderOpen() {
		return "<table class=\"books\">";
	}
	
	function renderClose() {
		return "</table>";
	}
	
	public function repeatHeadersEvery() {
		return 30;
	}
}
