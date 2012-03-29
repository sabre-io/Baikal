<?php

namespace Flake\Core;

abstract class Model extends \Flake\Core\FLObject {
	protected $aData = array();
	
	protected function getData() {
		reset($this->aData);
		return $this->aData;
	}
	
	public function get($sPropName) {
		if(array_key_exists($sPropName, $this->aData)) {
			return $this->aData[$sPropName];
		}
		
		throw new \Exception("\Flake\Core\Model->get(): property " . htmlspecialchars($sPropName) . " does not exist on " . self::getClass());
	}
	
	public function set($sPropName, $sPropValue) {
		if(array_key_exists($sPropName, $this->aData)) {
			$this->aData[$sPropName] = $sPropValue;
			return $this;
		}
		
		throw new \Exception("\Flake\Core\Model->set(): property " . htmlspecialchars($sPropName) . " does not exist on " . self::getClass());
	}
	
	public function getLabel() {
		return $this->get($this::LABELFIELD);
	}
}