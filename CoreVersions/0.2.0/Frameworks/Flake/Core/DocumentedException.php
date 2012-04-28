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

namespace Flake\Core;

class DocumentedException extends \Exception {
	
	protected $aMarkers = array();
	
	# Redefine the exception signature
	public function __construct($message, $aMarkers = array()) {
		$this->aMarkers = $aMarkers;
		parent::__construct($message, 0, null);
	}

	# custom string representation of object
	public function __toString() {
		$aDoc = $this->getDocumentation();
		debug($aDoc);
		return "<span style='color: red;'>" . htmlspecialchars($this->message) . "</span>";
	}
	
	protected function getSoftrefPath() {
		$sSoftRef = $this->getMessage();
		$aTrace = $this->getTrace();
		
		if($sSoftRef{0} === "\\") {
			# An absolute softref has been given
			return $sSoftRef;
		}
		
		if(isset($aTrace[0]["class"])) {
			return "\\" . $aTrace[0]["class"] . "#" . $sSoftRef;
		}
		
		return $sSoftRef;
	}
	
	protected function getDocumentation() {
		# Determine the documentation softref
		$sSoftRefPath = $this->getSoftrefPath();
		return $sSoftRefPath;
		
		/*
		$aParts = explode("#", \Flake\Util\Tools::trimStrings($sSoftRefPath, "\\"));
		
		$aSegments = explode("\\", $aParts[0]);
		$sKey = $aParts[1];
		
		# Is it a Framework ?
		if(\Flake\Util\Frameworks::isAFramework($aSegments[0])) {
			$sPath = \Flake\Util\Frameworks::getPath($aSegments[0]);
			die($sPath);
		}
		
		debug($aParts);
		return $sSoftRefPath;
		*/
	}
}
