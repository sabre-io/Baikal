<?php

namespace Formal;

class Element {
	protected $aOptions = array(
		"readonly" => FALSE,
	);
	
	protected $sValue = "";
	
	public function __construct($aOptions) {
		$this->aOptions = array_merge($aOptions, $this->aOptions);
	}
	
	public function option($sName) {
		if(array_key_exists($sName, $this->aOptions)) {
			return $this->aOptions[$sName];
		}
		
		throw new \Exception("\Formal\Element->option(): Option '" . htmlspecialchars($sName) . "' not found.");
	}
	
	public function setOption($sOptionName, $sOptionValue) {
		$this->aOptions[$sOptionName] = $sOptionValue;
	}
	
	public function value() {
		return $this->sValue;
	}
	
	public function setValue($sValue) {
		$this->sValue = $sValue;
	}
}