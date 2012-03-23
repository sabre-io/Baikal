<?php

namespace Flake\Core\Render;

class Container extends \Flake\Core\FLObject {
	
	var $aSequence = array();
	var $aBlocks = array();
	var $aRendu = array();
	var $aZones = array();

	function addBlock(&$oBlock, $sZone = "_DEFAULT_") {
		$aTemp = array(
			"block" => &$oBlock,
			"rendu" => "",
		);
		$this->aSequence[] =& $aTemp;
		$this->aBlocks[$sZone][] =& $aTemp["rendu"];
	}
	
	function &zone($sZone) {
		if(!array_key_exists($sZone, $this->aZones)) {
			$this->aZones[$sZone] = new \Flake\Core\Render\Zone($this, $sZone);
		}
		
		return $this->aZones[$sZone];
	}
}