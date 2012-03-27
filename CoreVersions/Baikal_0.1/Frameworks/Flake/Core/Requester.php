<?php

namespace Flake\Core;

abstract class Requester extends \Flake\Core\FLObject {
	public function __construct($sModelClass) {
		$this->sModelClass = $sModelClass;
	}

	public function addClause($sField, $sValue) {
		$this->addClauseEquals($sField, $sValue);
		return $this;
	}
	
	public function limit($iStart, $iNumber = FALSE) {
		if($iNumber !== FALSE) {
			return $this->setLimitStart($iStart)->setLimitNumber($iLimitNumber);
		}
		
		return $this->setLimitStart($iStart);
	}
	
	public abstract function execute();
}