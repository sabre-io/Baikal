<?php

namespace Flake\Controler;

class HtmlBlock extends \Flake\Core\Controler {
	
	function __construct($sHtml) {
		$this->sHtml = $sHtml;
	}
	
	function execute() {
		
	}
	
	function render() {
		return $this->sHtml;
	}
}