<?php
#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://baikal-server.com
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace Baikal\Model;

abstract class Config extends \Flake\Core\Model\NoDb {

	protected $sConfigFilePath = "";
	protected $aConstants = array();
	protected $aData = array();

	public function __construct($sConfigFilePath) {
		# Note: no call to parent::__construct() to avoid erasing $this->aData
		$this->sConfigFilePath = $sConfigFilePath;
		$aConfig = $this->parseConfig(
			$this->getConfigAsString()
		);

		foreach(array_keys($this->aData) as $sProp) {
			if(array_key_exists($sProp, $aConfig)) {
				$this->aData[$sProp] = $aConfig[$sProp];
			}
		}
	}

	protected function getConfigAsString() {
		if(file_exists($this->sConfigFilePath)) {
			$sContent = file_get_contents($this->sConfigFilePath);
			return str_replace(LF . CR, LF, $sContent);
		} else {

			$sConfig = "<?php\n" . \Baikal\Core\Tools::getCopyrightNotice() . "\n\n";
			$sConfig .= static::getDefaultConfig();

			return $sConfig;
		}
	}

	protected function parseConfig($sString) {

		$aRes = array();

		foreach(array_keys($this->aConstants) as $sConstant) {
			$aConstant = $this->aConstants[$sConstant];

			$aMatches = array();
			$sPattern = '/\s*define\(\s*["|\']' . $sConstant . '["|\']\s*\,\s*(.*?)\s*\);\s*/ix';

			$iNbRes = preg_match_all(
				$sPattern,
				$sString,
				$aMatches
			);

			if($iNbRes === 1) {
				# Exactly one match
				# O would be not enough, and > 1, to much to handle properly

				$sValue = $aMatches[1][0];	# first capture (.*?), first occurence (anyway, we asserted that there's only one)
				switch($aConstant["type"]) {
					case "string": {
						$sValue = substr($sValue, 1, -1);	# Strip quotes
						break;
					}
					case "integer": {
						$sValue = intval($sValue);	# Integer
						break;
					}
					case "boolean": {
						if(in_array(strtoupper(trim($sValue)), array("1", "TRUE"))) {
							$sValue = TRUE;
						} else {
							$sValue = FALSE;
						}
						break;
					}
					case "litteral": {
						$sValue = trim($sValue);
						break;
					}
					default: {
						# nothing
						break;
					}
				}
				
				$aRes[$sConstant] = $sValue;
				
			} elseif($iNbRes > 1) {
				throw new \Exception("Baikal\Model\Config->parseConfig(): constant '" . $sConstant . "' has been found multiple times in the config file; stopping execution");
			} else {
				# $iNbRes === 0
				# We do nothing, to keep the default value (the one already set in $aData)
			}
		}

		reset($aRes);
		return $aRes;
	}

	public function writable() {
		return (
			@file_exists($this->sConfigFilePath) &&
			@is_file($this->sConfigFilePath) &&
			@is_writable($this->sConfigFilePath)
		);
	}

	public static function icon() {
		return "icon-cog";
	}

	public static function mediumicon() {
		return "glyph-cogwheel";
	}

	public static function bigicon() {
		return "glyph2x-cogwheel";
	}
	
	public function floating() {
		return FALSE;
	}

	public function persist() {
		$aLines = explode(LF, $this->getConfigAsString());
		$iLines = count($aLines);
		
		foreach(array_keys($this->aData) as $prop) {
			$sPattern = '/\s*define\(\s*["|\']' . $prop . '["|\']\s*\,\s*(.*?)\s*\);\s*/ix';
			$sValue = $this->aData[$prop];
			
			
			# We replace value by it's native PHP notation
			switch($this->aConstants[$prop]["type"]) {
				case "string": {
					$sValue = var_export($sValue, TRUE);	# Add quotes, and escape " and all string-termination chars
					break;
				}
				case "integer": {
					$sValue = intval($sValue);	# Cast as integer
					break;
				}
				case "boolean": {

					if(intval($sValue) === 1) {	# Note as a BOOLEAN PHP constant
						$sValue = "TRUE";
					} else {
						$sValue = "FALSE";
					}

					break;
				}
				case "litteral": {
					$sValue = trim($sValue);
					break;
				}
				default: {
					$sValue = "''";
					break;
				}
			}
			
			$mFound = FALSE;
			for($k = ($iLines - 1); $k >= 0; $k--) {
				if(preg_match($sPattern, $aLines[$k])) {
					
					# Found the last matching line
					$mFound = $k;
					break;
				}
			}
			
			if($mFound === FALSE) {
				# Adding line at the end of the file
				$aLines[] = "\n" . "# " . $this->aConstants[$prop]["comment"] . "\ndefine(\"" . $prop . "\", " . $sValue . ");";
			} else {
				$aLines[$mFound] = "define(\"" . $prop . "\", " . $sValue . ");";
			}
		}

		$sLines = implode("\n", $aLines);
		$sSandboxedCode = str_replace(array("<?php", "<?", "?>"), "", $sLines);
		$sRand = (string)rand();
		$sCode = "if(0) {" . $sSandboxedCode . "}; echo '" . $sRand . "';";
		ob_start();
		eval($sCode);
		$sRes = ob_get_contents();
		ob_end_clean();

		if($sRes !== $sRand) {
			echo "<pre>" . htmlspecialchars($sLines) . "</pre>";
			throw new \Exception("Parse error in new config file. Aborting, nothing has been changed.");
		}
		
		# We asserted that the syntax is OK;
		# We now check that all the constants are present, and with the right value
		$aNewConfig = $this->parseConfig($sLines);
		$aWrittenConfig = $this->aData;

		asort($aNewConfig);
		asort($aWrittenConfig);
		
		if($aNewConfig != $aWrittenConfig) {
			throw new \Exception("New config does not correspond to expected config. Aborting, nothing has been changed.");
		}

		file_put_contents($this->sConfigFilePath, $sLines);
	}

	public function destroy() {

	}

	protected static function getDefaultConfig() {
	}
}