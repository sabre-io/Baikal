<?php

namespace Flake\Core\Render;

class Zone extends \Flake\Core\FLObject {
	function __construct(&$oZonableObject, $sZone) {
		$this->oZonableObject =& $oZonableObject;
		$this->sZone = $sZone;
	}
	
	function addBlock(&$oBlock) {
		$this->oZonableObject->addBlock(
			$oBlock,
			$this->sZone
		);
	}
}