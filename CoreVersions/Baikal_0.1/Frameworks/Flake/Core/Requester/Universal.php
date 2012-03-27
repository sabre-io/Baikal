<?php

namespace Flake\Core\Requester;

class Universal extends \Flake\Core\Requester {

	protected $aClauses = array(
		"equals" => array(),
		"notequals" => array(),
		"like" => array(),
		"likebeginning" => array(),
		"likeend" => array(),
		"notlike" => array(),
		"notlikebeginning" => array(),
		"notlikeend" => array(),
		"in" => array(),
		"notin" => array(),
	);
	
	protected $sModelClass = "";
	protected $sOrderField = "";
	protected $sOrderDirection = "ASC";
	protected $iLimitStart = FALSE;
	protected $iLimitNumber = FALSE;
	protected $bHasBeenExecuted = FALSE;
	
	public function __call($sName, $aArguments) {
		if(substr(strtolower($sName), 0, 9) === "addclause") {
			array_unshift($aArguments, substr(strtolower($sName), 9));
			return call_user_func_array(array($this, "addClause"), $aArguments);
		} elseif(substr(strtolower($sName), 0, 10) === "getclauses") {
			return call_user_func_array(array($this, "getClauses"), array(substr(strtolower($sName), 10)));
		}
		
		throw new \Exception("__get(): method " . htmlspecialchars($sName) . " not found");
	}
	
	public function addClause($sClauseType, $sPropName, $sPropValue) {
		if(is_numeric($sClauseType) || !array_key_exists($sClauseType, $this->aClauses)) {
			throw new \Exception("Undefined clause type: " . htmlspecialchars($sClauseType));
		}
		
		if(!array_key_exists($sPropName, $this->aClauses[$sClauseType])) {
			$this->aClauses[$sClauseType][$sPropName] = array();
		}
		
		$this->aClauses[$sClauseType][$sPropName][] = $sPropValue;
		
		return $this;
	}
	
	public function getClauses($sClauseType) {
		if(is_numeric($sClauseType) || !array_key_exists($sClauseType, $this->aClauses)) {
			throw new \Exception("Undefined clause type: " . htmlspecialchars($sClauseType));
		}
		
		reset($this->aClauses[$sClauseType]);
		return $this->aClauses[$sClauseType];
	}
	
	public function orderBy($sOrderField, $sOrderDirection = "ASC") {
		$this->sOrderField = $sOrderField;
		$this->sOrderDirection = $sOrderDirection;
		return $this;
	}

	public function setLimitStart($iLimitStart) {
		$this->iLimitStart = $iLimitStart;
		return $this;
	}

	public function setLimitNumber($iLimitNumber) {
		$this->iLimitNumber = $iLimitNumber;
		return $this;
	}

	protected function &reify($aData) {
		die(__FILE__ . ":" . __LINE__ . ": À implémenter");
		
		$sTemp = $this->sModelClass;
		return new $sTemp($aData[$sTemp::getPrimaryKey()]);
	}

	public function hasBeenExecuted() {
		return $this->bHasBeenExecuted;
	}

	public function execute() {
		$oCollection = new \Flake\Core\CollectionTyped($this->sModelClass);
		$this->bHasBeenExecuted = TRUE;
		return $oCollection;
	}
}