<?php

namespace Flake\Core;

abstract class Model extends \Flake\Core\FLObject {
	protected $aData = array();
	
	public function getData() {
		reset($this->aData);
		return $this->aData;
	}
	
	public function get($sWhat) {
		if(array_key_exists($sWhat, $this->aData)) {
			return $this->aData[$sWhat];
		}
		
		return FALSE;
	}
}