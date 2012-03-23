<?php

namespace Flake\Core;

class View extends \Flake\Core\FLObject {
	protected $aData;
	
	public function __construct() {
		$this->aData = array();
	}
	
	public function setData($sName, $mData) {
		$this->aData[$sName] = $mData;
	}
	
	public function getData() {
		return $this->aData;
	}
	
	public function get($sWhat) {
		if(array_key_exists($sWhat, $this->aData)) {
			return $this->aData[$sWhat];
		}
		
		return FALSE;
	}
}