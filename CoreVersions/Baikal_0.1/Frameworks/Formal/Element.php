<?php

namespace Formal;

class Element {
	protected $aOptions = array(
		"readonly" => FALSE,
		"error" => FALSE,
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
	
	public function optionArray($sName) {
		$sOption = trim($this->option($sName));
		if($sOption !== "") {
			$aOptions = explode(",", $sOption);
		}
		
		reset($aOptions);
		return $aOptions;
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
	
	public function __toString() {
		return get_class($this) . "<" . $this->option("label") . ">";
	}
}