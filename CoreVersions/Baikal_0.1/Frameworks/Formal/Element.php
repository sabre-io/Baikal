<?php

namespace Formal;

class Element {
	protected $aOptions = array(
		"readonly" => FALSE,
		"validation" => "",
		"error" => FALSE,
		"placeholder" => "",
	);
	
	protected $sValue = "";
	
	public function __construct($aOptions) {
		$this->aOptions = array_merge($this->aOptions, $aOptions);
	}
	
	public function option($sName) {
		if(array_key_exists($sName, $this->aOptions)) {
			return $this->aOptions[$sName];
		}
		
		throw new \Exception("\Formal\Element->option(): Option '" . htmlspecialchars($sName) . "' not found.");
	}
	
	public function optionArray($sOptionName) {
		$sOption = trim($this->option($sOptionName));
		if($sOption !== "") {
			$aOptions = explode(",", $sOption);
		} else {
			$aOptions = array();
		}
		
		reset($aOptions);
		return $aOptions;
	}
	
	public function setOption($sOptionName, $sOptionValue) {
		$this->aOptions[$sOptionName] = $sOptionValue;
	}
	
/*	public function addToOptionArray($sOptionName, $sOptionValue) {
		$aOptions = $this->optionArray($sOptionName);
		$aOptions[] = $sOptionValue;
		$this->aOptions[$sOptionName] = implode(",", $aOptions);
	}*/
	
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