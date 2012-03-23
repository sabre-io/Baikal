<?php

namespace Flake\Core;

class Template extends \Flake\Core\FLObject {
	
	private $sAbsPath = "";
	private $bPhp = FALSE;
	private $sHtml = "";
	
	public function __construct($sAbsPath, $bPhp = FALSE) {
		$this->sAbsPath = $sAbsPath;
		$this->bPhp = $bPhp;
		$this->sHtml = $this->getTemplateFile(
			$this->sAbsPath
		);
	}
	
	private function getTemplateFile($sAbsPath) {
		return file_get_contents($sAbsPath);
	}

	function parse($aMarkers = array()) {
		if($this->bPhp) {
			return \Flake\Util\Tools::parseTemplateCodePhp(
				$this->sHtml,
				$aMarkers
			);
		} else {
			return \Flake\Util\Tools::parseTemplateCode(
				$this->sHtml,
				$aMarkers
			);
		}
	}
}