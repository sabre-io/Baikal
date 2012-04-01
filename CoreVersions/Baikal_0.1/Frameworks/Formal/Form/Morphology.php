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
		$this->oElements->reset();
		foreach($this->oElements as $oElement) {
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