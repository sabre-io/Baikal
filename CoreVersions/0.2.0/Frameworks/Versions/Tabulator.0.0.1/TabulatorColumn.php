<?php

abstract class TabulatorColumn {
	private $sHeader = "";
	private $sName = "";
	private $sType = "text";
	private $aValues = array();
	protected $aCallbacks = array();
	
	public function	__construct($sName, $sHeader = "", $sType = "text") {
		$this->sHeader = $sHeader;
		$this->iLen = mb_strlen($sHeader, "utf8");
		$this->sName = $sName;
		$this->sType = $sType;
	}

	public function getName() {
		return $this->sName;
	}

	public function getHeader() {
		return $this->sHeader;
	}

	public function getType() {
		return $this->sType;
	}

	public function	getValueAtIndex($iIndex) {
		if($this->getType() === "duration") {
			return $this->humanDuration(
				$this->aValues[$iIndex] * 60
			);
		}
		
		return $this->aValues[$iIndex];
	}

	public function	addValue($sValue) {
		$this->aValues[] = $sValue;
	}

	public function renderValueAtIndex($iIndex) {
		return $this->wrap($this->getValueAtIndex($iIndex));
	}

	public function renderHeader() {
		$sString = $this->getHeader();
		
		if(array_key_exists("headerinnerwrap", $this->aCallbacks)) {
			$sString = call_user_func($this->aCallbacks["headerinnerwrap"], $this, $sString);
		}
		
		return $this->wrapHeader($sString);
	}
	
	public function humanDuration($iSeconds) {
		
		$units = array(
#			"w" => 7*24*3600,
#			"j" => 24*3600,
			"h" => 3600,
			"m" => 60,
			"s" => 1,
		);

		// specifically handle zero
        if($iSeconds == 0) {
			return "0 second";
		}

		$s = "";

		foreach ( $units as $name => $divisor ) {
			if ( $quot = intval($iSeconds / $divisor) ) {
				$s .= $quot . $name;
				#$s .= (abs($quot) > 1 ? "s" : "") . "";
				$iSeconds -= $quot * $divisor;
			}
		}
		
		return $s;
        #return substr($s, 0, -2);
	}
	
	public function setHeaderInnerWrapCallback($aCallback) {
		$this->aCallbacks["headerinnerwrap"] = $aCallback;
	}
	
	protected function wrapHeader($sString) {
		return $this->wrap($sString);
	}

	abstract protected function wrap($sString);
	abstract public function renderUnderline();
}