<?php

namespace Flake\Core\Model;

abstract class NoDb extends \Flake\Core\Model {
	
	public function __construct($aData = array()) {
		$this->aData = $aData;
	}
	
}