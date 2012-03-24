<?php

namespace Flake\Util;

class Tools extends \Flake\Core\FLObject {

	public static function getCurrentUrl() {
		return $_SERVER["REQUEST_URI"];
	}
	
	public static function getUrlTokens() {
		$sUrl = self::getCurrentUrl();
		if($sUrl{0} === "/") {
			$sUrl = substr($sUrl, 1);
		}
		
		if(trim($sUrl) !== "") {
			return explode("/", $sUrl);
		}
		
		return array();
	}
	
	public static function deCamelCase($sString, $sGlue=" ") {
		$sSep = md5(rand());
		$sRes = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $sSep . '$0', $sString));
		if($sGlue !== "" && preg_match('/^[[:upper:]].*/', $sRes)) {
			$sRes = $sSep . $sRes;
		}
		
		return str_replace($sSep, $sGlue, $sRes);
	}
	
	public static function getAction() {
		return \Flake\Util\Tools::GET("action");
	}
	
	public static function absolutizeURL($sUrl) {
		$aUrl = parse_url($sUrl);
		if($aUrl["scheme"] !== "http" && $aUrl["scheme"] !== "https") {
			if($sUrl{0} === "/") {
				$sUrl = substr($sUrl, 1);
			}
			
			return FLAKE_BASEURL . $sUrl;
		}
		
		return $sUrl;
	}
	
	public static function serverToAbsoluteWebPath($sAbsoluteServerPath) {
		if(substr($sAbsoluteServerPath, 0, strlen(FLAKE_PATH_WWWROOT)) === FLAKE_PATH_WWWROOT) {
			return FLAKE_BASEURL . substr($sAbsoluteServerPath, strlen(FLAKE_PATH_WWWROOT));
		}
		
		return $sAbsoluteServerPath;
	}
	
	public static function serverToRelativeWebPath($sAbsPath) {
		return "/" . str_replace(FLAKE_PATH_WWWROOT, "", $sAbsPath);
	}

	public static function view_array($array_in)	{
		if (is_array($array_in))	{
			$result='<table border="1" cellpadding="1" cellspacing="0" bgcolor="white">';
			if (!count($array_in))	{$result.= '<tr><td><font face="Verdana,Arial" size="1"><b>'.htmlspecialchars("EMPTY!").'</b></font></td></tr>';}
			while (list($key,$val)=each($array_in))	{
				$result.= '<tr><td valign="top"><font face="Verdana,Arial" size="1">'.htmlspecialchars((string)$key).'</font></td><td>';
				if (is_array($array_in[$key]))	{
					$result.= \Flake\Util\Tools::view_array($array_in[$key]);
				} else
					$result.= '<font face="Verdana,Arial" size="1" color="red">'.nl2br(htmlspecialchars((string)$val)).'<br /></font>';
				$result.= '</td></tr>';
			}
			$result.= '</table>';
		} else	{
			$result  = '<table border="1" cellpadding="1" cellspacing="0" bgcolor="white">
				<tr>
					<td><font face="Verdana,Arial" size="1" color="red">'.nl2br(htmlspecialchars((string)$array_in)).'<br /></font></td>
				</tr>
			</table>';	// Output it as a string.
		}
		return $result;
	}

	public static function debug($var="",$brOrHeader=0)	{
		if($brOrHeader === 0) {
			$trail = debug_backtrace();
			$trail = array_reverse($trail);
			array_pop($trail);	// la ligne d'appel à debug
			array_pop($trail);	// la ligne d'appel à debug
			$aLastNode = array_pop($trail);	// l'appel qui nous intéresse
			$brOrHeader = $aLastNode['class'].$aLastNode['type'].$aLastNode['function'];
		}

		if ($brOrHeader)	{
			echo '<table class="typo3-debug" border="0" cellpadding="0" cellspacing="0" bgcolor="white" style="border:0px; margin-top:3px; margin-bottom:3px;"><tr><td style="background-color:#bbbbbb; font-family: verdana,arial; font-weight: bold; font-size: 10px;">'.htmlspecialchars((string)$brOrHeader).'</td></tr><tr><td>';
		}

		if (is_array($var))	{
			echo \Flake\Util\Tools::view_array($var);
		} elseif (is_object($var))	{
			echo '<b>|Object:<pre>';
			print_r($var);
			echo '</pre>|</b>';
		} elseif ((string)$var!='')	{
			echo '<b>|'.htmlspecialchars((string)$var).'|</b>';
		} else {
			echo '<b>| debug |</b>';
		}

		if ($brOrHeader) {
			echo '</td></tr></table>';
		}
	}

	public static function debug_trail()	{
		$trail = debug_backtrace();
		$trail = array_reverse($trail);
		array_pop($trail);

		$path = array();
		foreach($trail as $dat)	{
			$path[] = $dat['class'].$dat['type'].$dat['function'];
		}

		return implode(' // ',$path);
	}

	public static function POST($sVar = FALSE) {
		if($sVar !== FALSE) {
			$aData = \Flake\Util\Tools::POST();
			if(array_key_exists($sVar, $aData)) {
				return $aData[$sVar];
			}
			
			return "";
		}
		
		return is_array($GLOBALS["_POST"]) ? $GLOBALS["_POST"] : array();
	}

	public static function GET($sVar = FALSE) {
		if($sVar !== FALSE) {
			$aData = \Flake\Util\Tools::GET();
			if(array_key_exists($sVar, $aData)) {
				return $aData[$sVar];
			}
			
			return "";
		}
		
		return is_array($GLOBALS["_GET"]) ? $GLOBALS["_GET"] : array();
	}

	public static function GP($sVar = FALSE) {
		if($sVar !== FALSE) {
			$aData = \Flake\Util\Tools::GP();
			if(array_key_exists($sVar, $aData)) {
				return $aData[$sVar];
			}
			
			return "";
		}
		
		return array_merge(
			\Flake\Util\Tools::GET(),
			\Flake\Util\Tools::POST()
		);
	}
	
	public static function makeLink($sAction, $aAdditionalParams = FALSE) {

		if($aAdditionalParams === FALSE) {
			// aucun paramètre additionnel
			if(trim($sAction) === "home") {
				return FLAKE_BASEURL;
			} else {
				return FLAKE_BASEURL . "?action=" . rawurlencode($sAction);
			}
		} else {
			
			$aTemp = array();
			while(list($sKey,) = each($aAdditionalParams)) {
				if($sKey{0} === "u" && $sKey{1} === "_") {
					// il s'agit d'un message textuel; on l'encode en base 64
					$aTemp[] = rawurlencode($sKey) . "=" . rawurlencode(base64_encode($aAdditionalParams[$sKey]));
				} else {
					$aTemp[] = rawurlencode($sKey) . "=" . rawurlencode($aAdditionalParams[$sKey]);
				}
			}
			
			$sAdditionalParams = implode("&", $aTemp);
			
			if(trim($sAction) === "home") {
				return FLAKE_BASEURL . "?" . $sAdditionalParams;
			} else {
				return FLAKE_BASEURL . "?action=" . $sAction . "&" . $sAdditionalParams;
			}
		}
	}
	
	public static function safelock($sString) {
		return substr(md5(FLAKE_SAFEHASH_SALT . ":" . $sString), 0, 5);
	}
	
	public static function redirect($sUrl) {
		header("Location: " . $sUrl);
		exit(0);
	}
	
	public static function refreshPage() {
		header("Location: " . \Flake\Util\Tools::getCurrentUrl());
		exit(0);
	}
	
	public static function decode_GET() {
		$aGet = \Flake\Util\Tools::GET();
		$aKeys = array_keys($aGet);
		while(list(,$sKey) = each($aKeys)) {
			if($sKey{0} === "u" && $sKey{1} === "_") {
				$aGet[$sKey] = base64_decode($aGet[$sKey]);
			}
		}
		
		$GLOBALS["_GET"] = $aGet;
		reset($GLOBALS["_GET"]);
	}
	
	public static function validEmail($sEmail) {
		return (filter_var($sEmail, FILTER_VALIDATE_EMAIL) !== FALSE);
	}
	
	public static function filterFormInput($sInput) {
		return strip_tags($sInput);
	}
	
	public static function getHumanDate($iStamp) {
		return ucwords(strftime("%A, %d %B %Y", $iStamp));
	}
	
	public static function getHumanTime($iStamp) {
		return strftime("%Hh%M", $iStamp);
	}
	
	public static function trimExplode($string, $delim=",", $removeEmptyValues = false, $limit = 0) {
		$explodedValues = explode($delim, $string);

		$result = array_map('trim', $explodedValues);

		if ($removeEmptyValues) {
			$temp = array();
			foreach($result as $value) {
				if ($value !== '') {
					$temp[] = $value;
				}
			}
			$result = $temp;
		}

		if ($limit != 0) {
			if ($limit < 0) {
				$result = array_slice($result, 0, $limit);
			} elseif (count($result) > $limit) {
				$lastElements = array_slice($result, $limit - 1);
				$result = array_slice($result, 0, $limit - 1);
				$result[] = implode($delim, $lastElements);
			}
		}

		return $result;
	}
		
	/**
	 * Taken from TYPO3
	 * Returns true if the first part of $str matches the string $partStr
	 *
	 * @param	string		Full string to check
	 * @param	string		Reference string which must be found as the "first part" of the full string
	 * @return	boolean		True if $partStr was found to be equal to the first part of $str
	 */
	public static function isFirstPartOfStr($str,$partStr)	{
		// Returns true, if the first part of a $str equals $partStr and $partStr is not ''
		$psLen = strlen($partStr);
		if ($psLen)	{
			return substr($str,0,$psLen)==(string)$partStr;
		} else return false;
	}
	
	/**
	 * Binary-reads a file
	 *
	 * @param	string		$sPath: absolute server path to file
	 * @return	string		file contents
	 */
	public static function file_readBin($sPath) {
		$sData = "";
		$rFile = fopen($sPath, "rb");
		while(!feof($rFile)) {
			$sData .= fread($rFile, 1024);
		}
		fclose($rFile);

		return $sData;
	}
	
	/**
	 * Binary-writes a file
	 *
	 * @param	string		$sPath: absolute server path to file
	 * @param	string		$sData: file contents
	 * @param	boolean		$bUTF8: add UTF8-BOM or not ?
	 * @return	void
	 */
	public static function file_writeBin($sPath, $sData) {
		$rFile=fopen($sPath, "wb");
		fputs($rFile, $sData);
		fclose($rFile);
	}
	
	public static function sendHtmlMail($sToAddress, $sSubject, $sBody, $sFromName, $sFromAddress, $sReplyToName, $sReplyToAddress) {
		
		$sMessage = <<<TEST
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Email</title>
	</head>
	<body>
	{$sBody}
	</body>
</html>
TEST;

		$sHeaders  = "From: " . $sFromName . "<" . $sFromAddress . ">" . "\r\n";
		$sHeaders .= "Reply-To: " . $sReplyToName . "<" . $sReplyToAddress . ">" . "\r\n";
		$sHeaders .= "Bcc: " . $sReplyToName . "<" . $sReplyToAddress . ">" . "\r\n";
		$sHeaders .= "Content-Type: text/html" . "\r\n";
		
		mail($sToAddress, $sSubject, $sMessage, $sHeaders);
	}
	
	public static function shortMD5($sValue) {
		return strtolower(substr(md5($sValue), 0, 5));
	}
	
	public static function overrideFirstWithSecond($sFirst, $sSecond) {
		if(trim($sSecond) !== "") {
			return $sSecond;
		}
		
		return "" . $sFirst;
	}
	
	public static function	parseTemplateCodePhp($sCode, $aMarkers) {
		extract($aMarkers);
		ob_start();
		echo eval('?>' . $sCode . '<?');
		$sHtml = ob_get_contents();
		ob_end_clean();
		
		return $sHtml;
	}
	
	public static function stackMarkers($aMarkers) {
		array_push($GLOBALS["TEMPLATESTACK"], $aMarkers);
	}
	
	public static function unstackMarkers() {
		array_pop($GLOBALS["TEMPLATESTACK"]);
	}
	
	public static function &getMarkers() {
		if(count($GLOBALS["TEMPLATESTACK"]) === 0) {
			return FALSE;
		}
		
		return $GLOBALS["TEMPLATESTACK"][count($GLOBALS["TEMPLATESTACK"]) - 1];
	}
	
	public static function parseTemplateCode($sCode, $aMarkers) {
		
		self::stackMarkers($aMarkers);
		
		$sPattern = '/{([^\{\}\n]*)}/';
		$sCode = preg_replace_callback(
			$sPattern,
			"self::processMarkersCallBackClearNotUsed",
			$sCode,
			-1	// no limit
		);
		
		self::unstackMarkers();

		return $sCode;
	}
	
	public static function processMarkersCallBackClearNotUsed($aMatch) {
		return self::resolveForTemplate($aMatch[1], self::getMarkers());
	}
	
	public static function resolveForTemplate($sSearch, $aMarkers) {
		
		$aSearchParts = explode(".", $sSearch);
		$sSearchPart = array_shift($aSearchParts);
		
		if(\Flake\Util\Tools::is_a($aMarkers, "\Flake\Core\Model")) {
			$aMarkers = $aMarkers->getData();
		}
		
		if(!array_key_exists($sSearchPart, $aMarkers)) {
			return "";
		}
		
		if(count($aSearchParts) > 0) {
			return self::resolveForTemplate(implode(".", $aSearchParts), $aMarkers[$sSearchPart]);
		}
		
		return $aMarkers[$sSearchPart];
	}
	
	public static function is_a($object, $class) {
	    if(is_object($object)) return $object instanceof $class;
	    if(is_string($object)){
	        if(is_object($class)) $class=get_class($class);

	        if(class_exists($class)) return is_subclass_of($object, $class) || $object==$class;
	        if(interface_exists($class)) {
	            $reflect = new \ReflectionClass($object);
	            return $reflect->implementsInterface($class);
	        }

	    }
	    return false;
	}
	
	public static function HTTPStatus($iCode, $sMessage) {
		header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
		die("<h1>HTTP Status " . $iCode . " : " . $sMessage . "</h1>");
	}
	
	public static function number2Rank($a) {
		$a = intval($a);
		
		if ($a === 1) {
			return "premier";
		} elseif($a === 2) {
			return "second";
		}
		
		$sNumber = self::number2Human($a);
		
		$sLastLetter = substr($sNumber, -1, 1);
		if($sLastLetter === "e") {
			$sNumber = substr($sNumber, 0, -1);
		} elseif($sLastLetter === "q") {
			$sNumber = $sNumber . "u";
		} elseif($sLastLetter === "f") {
			$sNumber = substr($sNumber, 0, -1) . "v";
		}
		
		return $sNumber . "ième";
	}
	
	public static function number2Human($a) {
		$temp = explode('.',$a);
		if (isset($temp[1]) && $temp[1]!='') {
			return self::number2Human($temp[0]).' virgule '.self::number2Human($temp[1]) ;
		}
		
		if ($a<0) return 'moins '.self::number2Human(-$a);
		
		if ($a<17) {
			switch ($a) {
				case 0: return 'zero';
				case 1: return 'un';
				case 2: return 'deux';
				case 3: return 'trois';
				case 4: return 'quatre';
				case 5: return 'cinq';
				case 6: return 'six';
				case 7: return 'sept';
				case 8: return 'huit';
				case 9: return 'neuf';
				case 10: return 'dix';
				case 11: return 'onze';
				case 12: return 'douze';
				case 13: return 'treize';
				case 14: return 'quatorze';
				case 15: return 'quinze';
				case 16: return 'seize';
			}
		} else if ($a<20) {
			return 'dix-' . self::number2Human($a-10);
		} else if ($a<100) {
			if ($a%10==0) {
				switch($a) {
					case 20: return 'vingt';
					case 30: return 'trente';
					case 40: return 'quarante';
					case 50: return 'cinquante';
					case 60: return 'soixante';
					case 70: return 'soixante-dix';
					case 80: return 'quatre-vingt';
					case 90: return 'quatre-vingt-dix';
				}
			} elseif(substr($a, -1) == 1) {
				if( ((int)($a/10)*10)<70 ) {
					return self::number2Human((int)($a/10)*10).'-et-un';
				} elseif ($a==71) {
					return 'soixante-et-onze';
				} elseif ($a==81) {
					return 'quatre-vingt-un';
				} elseif ($a==91) {
					return 'quatre-vingt-onze';
				}
			} elseif ($a<70) {
				return self::number2Human($a-$a%10).'-'.self::number2Human($a%10);
			} elseif ($a<80) {
				return self::number2Human(60).'-'.self::number2Human($a%20);
			} else {
				return self::number2Human(80).'-'.self::number2Human($a%20);
			}
		} else if ($a==100) {
			return 'cent';
		} else if ($a<200) {
			return self::number2Human(100).' '.self::number2Human($a%100);
		} else if ($a<1000) {
			return self::number2Human((int)($a/100)).' '.self::number2Human(100).' '.self::number2Human($a%100);
		} else if ($a==1000) {
			return 'mille';
		} else if ($a<2000) {
			return self::number2Human(1000).' '.self::number2Human($a%1000).' ';
		} else if ($a<1000000) {
			return self::number2Human((int)($a/1000)).' '.self::number2Human(1000).' '.self::number2Human($a%1000);
		}
	}
	
	public static function stringToUrlToken($sString) {
		
		# Taken from TYPO3 extension realurl
				
		$space = "-";
		$sString = strtr($sString, ' -+_\'', $space . $space . $space . $space . $space); // convert spaces
		
		$sString = iconv('UTF-8', 'ASCII//TRANSLIT', $sString);
		$sString = strtolower($sString);
		
		$sString = preg_replace('/[^a-zA-Z0-9\\' . $space . ']/', '', $sString);
		$sString = preg_replace('/\\' . $space . '{2,}/', $space, $sString); // Convert multiple 'spaces' to a single one
		$sString = trim($sString, $space);
		
		return $sString;
	}
	
	public static function isCliPhp() {
		return strtolower(php_sapi_name()) === "cli";
	}
	
	public static function getIP() {

		$alt_ip = $_SERVER['REMOTE_ADDR'];

		if(isset($_SERVER['HTTP_CLIENT_IP'])) {
			$alt_ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
			
			// make sure we dont pick up an internal IP defined by RFC1918
			foreach($matches[0] AS $ip) {
				if (!preg_match('#^(10|172\.16|192\.168)\.#', $ip)) {
					$alt_ip = $ip;
					break;
				}
			}
		} else if (isset($_SERVER['HTTP_FROM'])) {
			$alt_ip = $_SERVER['HTTP_FROM'];
		}
		
		return $alt_ip;
	}
	
	public static function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}
}