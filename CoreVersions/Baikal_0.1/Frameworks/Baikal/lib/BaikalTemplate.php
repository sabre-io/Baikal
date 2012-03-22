<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
*  All rights reserved
*
*  http://baikal.codr.fr
*
*  This script is part of the Baïkal Server project. The Baïkal
*  Server project is free software; you can redistribute it
*  and/or modify it under the terms of the GNU General Public
*  License as published by the Free Software Foundation; either
*  version 2 of the License, or (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

class BaikalTemplate {
	
	private $sAbsPath = "";
	private $sHtml = "";
	
	public function __construct($sAbsPath) {
		$this->sAbsPath = $sAbsPath;
		$this->sHtml = self::getTemplateFile(
			$this->sAbsPath
		);
	}
	
	public function parse($aMarkers = array()) {
		return self::parseTemplateCodePhp(
			$this->sHtml,
			$aMarkers
		);
	}
	
	protected static function getTemplateFile($sAbsPath) {
		return file_get_contents($sAbsPath);
	}
	
	protected static function parseTemplateCodePhp($sCode, $aMarkers) {
		extract($aMarkers);
		ob_start();
		echo eval('?>' . $sCode . '<?');
		$sHtml = ob_get_contents();
		ob_end_clean();
		
		return $sHtml;
	}
}