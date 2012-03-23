<?php

namespace Flake\Controler;

class Page extends \Flake\Core\Render\Container {
	
	var $sTitle = "";
	var $sMetaKeywords = "";
	var $sMetaDescription = "";
	var $sTemplatePath = "";
	
	function __construct($sTemplatePath) {
		$this->sTemplatePath = $sTemplatePath;
	}
	
	function setTitle($sTitle) {
		$this->sTitle = $sTitle;
	}
	
	function setMetaKeywords($sKeywords) {
		$this->sMetaKeywords = $sKeywords;
	}
	
	function setMetaDescription($sDescription) {
		$this->sMetaDescription = $sDescription;
	}
	
	function getTitle() {
		return $this->sTitle;
	}
	
	function getMetaKeywords() {
		$sString = str_replace(array("le", "la", "les", "de", "des", "un", "une"), " ", $this->sMetaKeywords);
		$sString = \Flake\Util\Tools::stringToUrlToken($sString);
		return implode(", ", explode("-", $sString));
	}
	
	function getMetaDescription() {
		return $this->sMetaDescription;
	}
	
	function setBaseUrl($sBaseUrl) {
		$this->sBaseUrl = $sBaseUrl;
	}
	
	function getBaseUrl() {
		return $this->sBaseUrl;
	}
		
	function renderBlocks() {
		$aHtml = array();
		reset($this->aSequence);
		while(list($sKey,) = each($this->aSequence)) {
			$this->aSequence[$sKey]["rendu"] = $this->aSequence[$sKey]["block"]->render();
		}
		
		$aHtml = array();
		reset($this->aBlocks);
		while(list($sZone,) = each($this->aBlocks)) {
			$aHtml[$sZone] = implode("", $this->aBlocks[$sZone]);
		}
		
		reset($aHtml);
		return $aHtml;
	}
	
	function injectHTTPHeaders() {
		header("Content-Type: text/html; charset=utf-8");
	}
	
	function render() {
		$this->execute();
		
		$aRenderedBlocks = $this->renderBlocks();
		$aRenderedBlocks["pagetitle"] = $this->getTitle();
		$aRenderedBlocks["pagemetakeywords"] = $this->getMetaKeywords();
		$aRenderedBlocks["pagemetadescription"] = $this->getMetaDescription();
		$aRenderedBlocks["baseurl"] = $this->getBaseUrl();
		
		$oTemplate = new \Flake\Core\Template($this->sTemplatePath);
		$sHtml = $oTemplate->parse(
			$aRenderedBlocks
		);

		return $sHtml;
	}

	function execute() {
		reset($this->aSequence);
		while(list($sKey,) = each($this->aSequence)) {
			$this->aSequence[$sKey]["block"]->execute();
		}
	}
	
	function addCss($sCssAbsPath) {
		
		$sCompiledPath = PATH_buildcss;
		$sFileName = basename($sCssAbsPath);
		
		$sCompiledFilePath = $sCompiledPath . \Flake\Util\Tools::shortMD5($sFileName) . "_" . $sFileName;
		
		if(substr(strtolower($sCompiledFilePath), -4) !== ".css") {
			$sCompiledFilePath .= ".css";
		}
		
		if(!file_exists($sCompiledPath)) {
			@mkdir($sCompiledPath);
			if(!file_exists($sCompiledPath)) {
				die("Page: Cannot create " . $sCompiledPath);
			}
		}
		
		\Frameworks\LessPHP\Delegate::compileCss($sCssAbsPath, $sCompiledFilePath);
		$sCssUrl = \Flake\Util\Tools::serverToRelativeWebPath($sCompiledFilePath);
		
		$sHtml = "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $sCssUrl . "\" media=\"all\"/>";
		$this->zone("head")->addBlock(new \Flake\Controler\HtmlBlock($sHtml));
	}
}