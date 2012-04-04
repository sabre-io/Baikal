<?php

namespace Formal\Form;

class Morphology {
	
	protected $oElements = null;
	
	public function __construct() {
		$this->oElements = new \Flake\Core\CollectionTyped("\Formal\Element");
	}
	
	public function add(\Formal\Element $oElement) {
		$this->oElements->push($oElement);
	}
	
	public function element($sPropName) {
		$aKeys = $this->oElements->keys();
		reset($aKeys);
		foreach($aKeys as $sKey) {
			$oElement = $this->oElements->getForKey($sKey);
			
			if($oElement->option("prop") === $sPropName) {
				return $oElement;
			}
		}
		
		throw new \Exception("\Formal\Form\Morphology->element(): Element prop='" . $sPropName . "' not found");
	}
	
	public function elements() {
		return $this->oElements;
	}
}