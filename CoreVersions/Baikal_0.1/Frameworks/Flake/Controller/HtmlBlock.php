<?php

namespace Flake\Controller;

class HtmlBlock extends \Flake\Core\Controller {
	
	function __construct($sHtml) {
		$this->sHtml = $sHtml;
	}
	
	function execute() {
		
	}
	
	function render() {
		return $this->sHtml;
	}
}