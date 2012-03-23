<?php

namespace Flake\Controler;

class HtmlBlockTemplated extends \Flake\Core\Controler {
	
	function __construct($sTemplatePath, $aMarkers = array()) {
		$this->sTemplatePath = $sTemplatePath;
		$this->aMarkers = $aMarkers;
	}
	
	function render() {
		$oTemplate = new \Flake\Core\Template($this->sTemplatePath);
		$sHtml = $oTemplate->parse(
			$this->aMarkers
		);
		
		return $sHtml;
	}
}