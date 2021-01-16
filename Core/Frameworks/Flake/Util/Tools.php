<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://flake.codr.fr
#
#  This script is part of the Flake project. The Flake
#  project is free software; you can redistribute it
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

namespace Flake\Util;

class Tools extends \Flake\Core\FLObject {
    private function __construct() {    # private constructor to force static class
    }

    static function getCurrentUrl() {
        if (MONGOOSE_SERVER) {
            $sUrl = $GLOBALS["_SERVER"]["REQUEST_URI"];
            if (array_key_exists("QUERY_STRING", $GLOBALS["_SERVER"]) && trim($GLOBALS["_SERVER"]["QUERY_STRING"]) !== "") {
                $sUrl .= "?" . $GLOBALS["_SERVER"]["QUERY_STRING"];
            }
        } else {
            $sUrl = $GLOBALS["_SERVER"]["REQUEST_URI"];    # Would be REDIRECT_URL for ServerRewrite
        }

        return $sUrl;
    }

    static function getCurrentProtocol() {
        if (isset($GLOBALS['_SERVER']['HTTP_X_FORWARDED_PROTO']) && !empty($GLOBALS['_SERVER']['HTTP_X_FORWARDED_PROTO'])) {
            return $GLOBALS['_SERVER']['HTTP_X_FORWARDED_PROTO'];
        }

        if ((!empty($GLOBALS["_SERVER"]["HTTPS"]) && $GLOBALS["_SERVER"]['HTTPS'] !== 'off') || intval($_SERVER['SERVER_PORT']) === 443) {
            return "https";
        }

        return "http";
    }

    static function deCamelCase($sString, $sGlue = " ") {
        $sSep = md5(rand());
        $sRes = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $sSep . '$0', $sString));
        if ($sGlue !== "" && preg_match('/^[[:upper:]].*/', $sRes)) {
            $sRes = $sSep . $sRes;
        }

        return str_replace($sSep, $sGlue, $sRes);
    }

    static function serverToRelativeWebPath($sAbsPath) {
        return "/" . str_replace(PROJECT_PATH_WWWROOT, "", $sAbsPath);
    }

    static function view_array($array_in) {
        if (is_array($array_in)) {
            $result = '<table border="1" cellpadding="1" cellspacing="0" bgcolor="white">';
            if (!count($array_in)) {
                $result .= '<tr><td><font face="Verdana,Arial" size="1"><b>' . htmlspecialchars("EMPTY!") . '</b></font></td></tr>';
            }
            foreach ($array_in as $key => $val) {
                $result .= '<tr><td valign="top"><font face="Verdana,Arial" size="1">' . htmlspecialchars((string) $key) . '</font></td><td>';
                if (is_array($array_in[$key])) {
                    $result .= \Flake\Util\Tools::view_array($array_in[$key]);
                } else {
                    if (is_object($val)) {
                        if (method_exists($val, "__toString")) {
                            $sWhat = nl2br(htmlspecialchars((string) $val));
                        } else {
                            $sWhat = nl2br(htmlspecialchars(get_class($val)));
                        }
                    } elseif (is_bool($val)) {
                        $sWhat = ($val === true ? "boolean:TRUE" : "boolean:FALSE");
                    } else {
                        $sWhat = nl2br(htmlspecialchars((string) $val));
                    }

                    $result .= '<font face="Verdana,Arial" size="1" color="red">' . $sWhat . '<br /></font>';
                }

                $result .= '</td></tr>';
            }
            $result .= '</table>';
        } else {
            $result = '<table border="1" cellpadding="1" cellspacing="0" bgcolor="white">
				<tr>
					<td><font face="Verdana,Arial" size="1" color="red">' . nl2br(htmlspecialchars((string) $array_in)) . '<br /></font></td>
				</tr>
			</table>';    // Output it as a string.
        }

        return $result;
    }

    static function debug($var = "", $brOrHeader = 0) {
        if ($brOrHeader === 0) {
            try {
                $trail = debug_backtrace();
                $trail = array_reverse($trail);
                array_pop($trail);    // la ligne d'appel à debug
                array_pop($trail);    // la ligne d'appel à debug
                $aLastNode = array_pop($trail);    // l'appel qui nous intéresse

                if (array_key_exists("class", $aLastNode)) {
                    $sClass = @strval($aLastNode["class"]);
                } else {
                    $sClass = "";
                }

                if (array_key_exists("type", $aLastNode)) {
                    $sType = @strval($aLastNode["type"]);
                } else {
                    $sType = "";
                }

                $brOrHeader = $sClass . $sType . @strval($aLastNode['function']);
            } catch (\Exception $e) {
                $brOrHeader = "Undetermined context";
            }
        }

        if ($brOrHeader) {
            echo '<table border="0" cellpadding="0" cellspacing="0" bgcolor="white" style="border:0px; margin-top:3px; margin-bottom:3px;"><tr><td style="background-color:#bbbbbb; font-family: verdana,arial; font-weight: bold; font-size: 10px;">' . htmlspecialchars((string) $brOrHeader) . '</td></tr><tr><td>';
        }

        if (is_array($var)) {
            echo \Flake\Util\Tools::view_array($var);
        } elseif (is_object($var)) {
            echo '<b>|Object:<pre>';
            print_r($var);
            echo '</pre>|</b>';
        } elseif ((string) $var != '') {
            echo '<b>|' . htmlspecialchars((string) $var) . '|</b>';
        } else {
            echo '<b>| debug |</b>';
        }

        if ($brOrHeader) {
            echo '</td></tr></table>';
        }
    }

    static function debug_trail() {
        $trail = debug_backtrace();
        $trail = array_reverse($trail);
        array_pop($trail);

        $path = [];
        foreach ($trail as $dat) {
            $path[] = $dat['class'] . $dat['type'] . $dat['function'];
        }

        return implode(' // ', $path);
    }

    static function POST($sVar = false) {
        if ($sVar !== false) {
            $aData = \Flake\Util\Tools::POST();
            if (array_key_exists($sVar, $aData)) {
                return $aData[$sVar];
            }

            return "";
        }

        return is_array($GLOBALS["_POST"]) ? $GLOBALS["_POST"] : [];
    }

    static function GET($sVar = false) {
        if ($sVar !== false) {
            $aData = \Flake\Util\Tools::GET();
            if (array_key_exists($sVar, $aData)) {
                return $aData[$sVar];
            }

            return "";
        }

        return is_array($GLOBALS["_GET"]) ? $GLOBALS["_GET"] : [];
    }

    static function GP($sVar = false) {
        if ($sVar !== false) {
            $aData = \Flake\Util\Tools::GP();
            if (array_key_exists($sVar, $aData)) {
                return $aData[$sVar];
            }

            return "";
        }

        return array_merge(
            \Flake\Util\Tools::GET(),
            \Flake\Util\Tools::POST()
        );
    }

    static function safelock($sString) {
        return substr(md5(PROJECT_SAFEHASH_SALT . ":" . $sString), 0, 5);
    }

    static function redirect($sUrl) {
        header("Location: " . $sUrl);
        exit(0);
    }

    static function redirectUsingMeta($sUrl) {
        $sDoc = "<html><head><meta http-equiv='refresh' content='0; url=" . $sUrl . "'></meta></head><body></body></html>";
        echo $sDoc;
        exit(0);
    }

    static function refreshPage() {
        header("Location: " . \Flake\Util\Tools::getCurrentUrl());
        exit(0);
    }

    static function validEmail($sEmail) {
        return (filter_var($sEmail, FILTER_VALIDATE_EMAIL) !== false);
    }

    static function filterFormInput($sInput) {
        return strip_tags($sInput);
    }

    static function getHumanDate($iStamp) {
        return ucwords(strftime("%A, %d %B %Y", $iStamp));
    }

    static function getHumanTime($iStamp) {
        return strftime("%Hh%M", $iStamp);
    }

    static function trimExplode($string, $delim = ",", $removeEmptyValues = false, $limit = 0) {
        $explodedValues = explode($delim, $string);

        $result = array_map('trim', $explodedValues);

        if ($removeEmptyValues) {
            $temp = [];
            foreach ($result as $value) {
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
     * Returns true if the first part of $str matches the string $partStr.
     *
     * @param	string		Full string to check
     * @param	string		Reference string which must be found as the "first part" of the full string
     *
     * @return	bool		True if $partStr was found to be equal to the first part of $str
     */
    static function isFirstPartOfStr($str, $partStr) {
        // Returns true, if the first part of a $str equals $partStr and $partStr is not ''
        $psLen = strlen($partStr);
        if ($psLen) {
            return substr($str, 0, $psLen) == (string) $partStr;
        } else {
            return false;
        }
    }

    /**
     * Binary-reads a file.
     *
     * @param	string		$sPath: absolute server path to file
     *
     * @return	string		file contents
     */
    static function file_readBin($sPath) {
        $sData = "";
        $rFile = fopen($sPath, "rb");
        while (!feof($rFile)) {
            $sData .= fread($rFile, 1024);
        }
        fclose($rFile);

        return $sData;
    }

    /**
     * Binary-writes a file.
     *
     * @param	string		$sPath: absolute server path to file
     * @param	string		$sData: file contents
     * @param	bool		$bUTF8: add UTF8-BOM or not ?
     *
     * @return	void
     */
    static function file_writeBin($sPath, $sData) {
        $rFile = fopen($sPath, "wb");
        fputs($rFile, $sData);
        fclose($rFile);
    }

    static function sendHtmlMail($sToAddress, $sSubject, $sBody, $sFromName, $sFromAddress, $sReplyToName, $sReplyToAddress) {
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

        $sHeaders = "From: " . $sFromName . "<" . $sFromAddress . ">" . "\r\n";
        $sHeaders .= "Reply-To: " . $sReplyToName . "<" . $sReplyToAddress . ">" . "\r\n";
        $sHeaders .= "Bcc: " . $sReplyToName . "<" . $sReplyToAddress . ">" . "\r\n";
        $sHeaders .= "Content-Type: text/html" . "\r\n";

        mail($sToAddress, $sSubject, $sMessage, $sHeaders);
    }

    static function shortMD5($sValue) {
        return strtolower(substr(md5($sValue), 0, 5));
    }

    static function overrideFirstWithSecond($sFirst, $sSecond) {
        if (trim($sSecond) !== "") {
            return $sSecond;
        }

        return "" . $sFirst;
    }

    static function parseTemplateCode($sCode, $aMarkers) {
        $tplName = md5($sCode);
        $loader = new \Twig\Loader\ArrayLoader([$tplName => $sCode]);
        $env = new \Twig\Environment($loader);
        $env->setCache(false);

        return $env->render($tplName, $aMarkers);
    }

    static function is_a($object, $class) {
        if (is_object($object)) {
            return $object instanceof $class;
        }
        if (is_string($object)) {
            if (is_object($class)) {
                $class = get_class($class);
            }

            if (class_exists($class, true)) {    # TRUE to autoload class
                return @is_subclass_of($object, $class) || $object == $class;
            }

            if (interface_exists($class)) {
                $reflect = new \ReflectionClass($object);

                return $reflect->implementsInterface($class);
            }
        }

        return false;
    }

    static function HTTPStatus($iCode, $sMessage) {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        exit("<h1>HTTP Status " . $iCode . " : " . $sMessage . "</h1>");
    }

    static function number2Rank($a) {
        $a = intval($a);

        if ($a === 1) {
            return "premier";
        } elseif ($a === 2) {
            return "second";
        }

        $sNumber = self::number2Human($a);

        $sLastLetter = substr($sNumber, -1, 1);
        if ($sLastLetter === "e") {
            $sNumber = substr($sNumber, 0, -1);
        } elseif ($sLastLetter === "q") {
            $sNumber = $sNumber . "u";
        } elseif ($sLastLetter === "f") {
            $sNumber = substr($sNumber, 0, -1) . "v";
        }

        return $sNumber . "ième";
    }

    static function number2Human($a) {
        $temp = explode('.', $a);
        if (isset($temp[1]) && $temp[1] != '') {
            return self::number2Human($temp[0]) . ' virgule ' . self::number2Human($temp[1]);
        }

        if ($a < 0) {
            return 'moins ' . self::number2Human(-$a);
        }

        if ($a < 17) {
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
        } elseif ($a < 20) {
            return 'dix-' . self::number2Human($a - 10);
        } elseif ($a < 100) {
            if ($a % 10 == 0) {
                switch ($a) {
                    case 20: return 'vingt';
                    case 30: return 'trente';
                    case 40: return 'quarante';
                    case 50: return 'cinquante';
                    case 60: return 'soixante';
                    case 70: return 'soixante-dix';
                    case 80: return 'quatre-vingt';
                    case 90: return 'quatre-vingt-dix';
                }
            } elseif (substr($a, -1) == 1) {
                if (((int) ($a / 10) * 10) < 70) {
                    return self::number2Human((int) ($a / 10) * 10) . '-et-un';
                } elseif ($a == 71) {
                    return 'soixante-et-onze';
                } elseif ($a == 81) {
                    return 'quatre-vingt-un';
                } elseif ($a == 91) {
                    return 'quatre-vingt-onze';
                }
            } elseif ($a < 70) {
                return self::number2Human($a - $a % 10) . '-' . self::number2Human($a % 10);
            } elseif ($a < 80) {
                return self::number2Human(60) . '-' . self::number2Human($a % 20);
            } else {
                return self::number2Human(80) . '-' . self::number2Human($a % 20);
            }
        } elseif ($a == 100) {
            return 'cent';
        } elseif ($a < 200) {
            return self::number2Human(100) . ' ' . self::number2Human($a % 100);
        } elseif ($a < 1000) {
            return self::number2Human((int) ($a / 100)) . ' ' . self::number2Human(100) . ' ' . self::number2Human($a % 100);
        } elseif ($a == 1000) {
            return 'mille';
        } elseif ($a < 2000) {
            return self::number2Human(1000) . ' ' . self::number2Human($a % 1000) . ' ';
        } elseif ($a < 1000000) {
            return self::number2Human((int) ($a / 1000)) . ' ' . self::number2Human(1000) . ' ' . self::number2Human($a % 1000);
        }
    }

    static function stringToUrlToken($sString) {
        # Taken from TYPO3 extension realurl

        $space = "-";
        $sString = strtr($sString, ' -+_\'', $space . $space . $space . $space . $space); // convert spaces

        # De-activated; @see https://github.com/netgusto/Baikal/issues/244
        #if(function_exists("iconv")) {
        #	$sString = iconv('UTF-8', 'ASCII//TRANSLIT', $sString);
        #}

        $sString = strtolower($sString);

        $sString = preg_replace('/[^a-zA-Z0-9\\' . $space . ']/', '', $sString);
        $sString = preg_replace('/\\' . $space . '{2,}/', $space, $sString); // Convert multiple 'spaces' to a single one
        $sString = trim($sString, $space);

        return $sString;
    }

    static function isCliPhp() {
        return strtolower(php_sapi_name()) === "cli";
    }

    static function getIP() {
        $alt_ip = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $alt_ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            // make sure we dont pick up an internal IP defined by RFC1918
            foreach ($matches[0] as $ip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $ip)) {
                    $alt_ip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_FROM'])) {
            $alt_ip = $_SERVER['HTTP_FROM'];
        }

        return $alt_ip;
    }

    static function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    ###########
    static function appendSlash($sString) {
        return self::appendString($sString, "/");
    }

    static function prependSlash($sString) {
        return self::prependString($sString, "/");
    }

    static function stripBeginSlash($sString) {
        return self::stripBeginString($sString, "/");
    }

    static function stripEndSlash($sString) {
        return self::stripEndString($sString, "/");
    }

    static function trimSlashes($sString) {
        return self::stripBeginSlash(self::stripEndSlash($sString));
    }

    ###########
    static function appendString($sString, $sAppend) {
        if (substr($sString, -1 * strlen($sAppend)) !== $sAppend) {
            $sString .= $sAppend;
        }

        return $sString;
    }

    static function prependString($sString, $sAppend) {
        if (substr($sString, 0, 1 * strlen($sAppend)) !== $sAppend) {
            $sString = $sAppend . $sString;
        }

        return $sString;
    }

    static function stripBeginString($sString, $sAppend) {
        if (substr($sString, 0, 1 * strlen($sAppend)) === $sAppend) {
            $sString = substr($sString, strlen($sAppend));
        }

        return $sString;
    }

    static function stripEndString($sString, $sAppend) {
        if (substr($sString, -1 * strlen($sAppend)) === $sAppend) {
            $sString = substr($sString, 0, -1 * strlen($sAppend));
        }

        return $sString;
    }

    static function trimStrings($sString, $sAppend) {
        return self::stripBeginString(self::stripEndString($sString, $sAppend), $sAppend);
    }

    static function stringEndsWith($sHaystack, $sNeedle) {
        return substr($sHaystack, strlen($sNeedle) * -1) === $sNeedle;
    }

    ###########

    static function router() {
        return "\Flake\Util\Router\QuestionMarkRewrite";
    }

    static function arrayIsAssoc($aArray) {
        if (!is_array($aArray)) {
            throw new \Exception("\Flake\Util\Tools::arrayIsAssoc(): parameter has to be an array.");
        }

        # Taken from http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-sequential#answer-4254008
        # count() will return 0 if numeric, and > 0 if assoc, even partially
        return (bool) count(array_filter(array_keys($aArray), 'is_string'));
    }

    static function arrayIsSeq($aArray) {
        return !self::arrayIsAssoc($aArray);
    }

    static function echoAndCutClient($sMessage = '') {
        ignore_user_abort(true);
        #		set_time_limit(0);

        header("Connection: close");
        header("Content-Length: " . strlen($sMessage));
        echo $sMessage;
        echo str_repeat("\r\n", 10); // just to be sure
        flush();
    }

    static function milliseconds() {
        return intval((microtime(true) * 1000));
    }

    static function stopWatch($sWhat) {
        #		return;
        $iStop = \Flake\Util\Tools::milliseconds();

        $trail = debug_backtrace();
        $aLastNode = $trail[0];    // l'appel qui nous intéresse
        $sFile = basename($aLastNode["file"]);
        $iLine = intval($aLastNode["line"]);

        if (!array_key_exists("FLAKE_STOPWATCHES", $GLOBALS)) {
            $GLOBALS["FLAKE_STOPWATCHES"] = [];
        }

        if (!array_key_exists($sWhat, $GLOBALS["FLAKE_STOPWATCHES"])) {
            $GLOBALS["FLAKE_STOPWATCHES"][$sWhat] = $iStop;
        } else {
            $iTime = $iStop - $GLOBALS["FLAKE_STOPWATCHES"][$sWhat];
            echo "<h3 style='color: silver'><span style='display: inline-block; width: 400px;'>@" . $sFile . "+" . $iLine . ":</span>" . $sWhat . ":" . $iTime . " ms</h1>";
            flush();
        }
    }

    # Taken from http://www.php.net/manual/en/function.gzdecode.php#82930
    static function gzdecode($data, &$filename = '', &$error = '', $maxlength = null) {
        $len = strlen($data);
        if ($len < 18 || strcmp(substr($data, 0, 2), "\x1f\x8b")) {
            $error = "Not in GZIP format.";

            return null;  // Not GZIP format (See RFC 1952)
        }
        $method = ord(substr($data, 2, 1));  // Compression method
        $flags = ord(substr($data, 3, 1));  // Flags
        if ($flags & 31 != $flags) {
            $error = "Reserved bits not allowed.";

            return null;
        }
        // NOTE: $mtime may be negative (PHP integer limitations)
        $mtime = unpack("V", substr($data, 4, 4));
        $mtime = $mtime[1];
        $xfl = substr($data, 8, 1);
        $os = substr($data, 8, 1);
        $headerlen = 10;
        $extralen = 0;
        $extra = "";
        if ($flags & 4) {
            // 2-byte length prefixed EXTRA data in header
            if ($len - $headerlen - 2 < 8) {
                return false;  // invalid
            }
            $extralen = unpack("v", substr($data, 8, 2));
            $extralen = $extralen[1];
            if ($len - $headerlen - 2 - $extralen < 8) {
                return false;  // invalid
            }
            $extra = substr($data, 10, $extralen);
            $headerlen += 2 + $extralen;
        }
        $filenamelen = 0;
        $filename = "";
        if ($flags & 8) {
            // C-style string
            if ($len - $headerlen - 1 < 8) {
                return false; // invalid
            }
            $filenamelen = strpos(substr($data, $headerlen), chr(0));
            if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
                return false; // invalid
            }
            $filename = substr($data, $headerlen, $filenamelen);
            $headerlen += $filenamelen + 1;
        }
        $commentlen = 0;
        $comment = "";
        if ($flags & 16) {
            // C-style string COMMENT data in header
            if ($len - $headerlen - 1 < 8) {
                return false;    // invalid
            }
            $commentlen = strpos(substr($data, $headerlen), chr(0));
            if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
                return false;    // Invalid header format
            }
            $comment = substr($data, $headerlen, $commentlen);
            $headerlen += $commentlen + 1;
        }
        $headercrc = "";
        if ($flags & 2) {
            // 2-bytes (lowest order) of CRC32 on header present
            if ($len - $headerlen - 2 < 8) {
                return false;    // invalid
            }
            $calccrc = crc32(substr($data, 0, $headerlen)) & 0xffff;
            $headercrc = unpack("v", substr($data, $headerlen, 2));
            $headercrc = $headercrc[1];
            if ($headercrc != $calccrc) {
                $error = "Header checksum failed.";

                return false;    // Bad header CRC
            }
            $headerlen += 2;
        }
        // GZIP FOOTER
        $datacrc = unpack("V", substr($data, -8, 4));
        $datacrc = sprintf('%u', $datacrc[1] & 0xFFFFFFFF);
        $isize = unpack("V", substr($data, -4));
        $isize = $isize[1];
        // decompression:
        $bodylen = $len - $headerlen - 8;
        if ($bodylen < 1) {
            // IMPLEMENTATION BUG!
            return null;
        }
        $body = substr($data, $headerlen, $bodylen);
        $data = "";
        if ($bodylen > 0) {
            switch ($method) {
            case 8:
                // Currently the only supported compression method:
                $data = gzinflate($body, $maxlength);
                break;
            default:
                $error = "Unknown compression method.";

                return false;
            }
        }  // zero-byte body content is allowed
        // Verifiy CRC32
        $crc = sprintf("%u", crc32($data));
        $crcOK = $crc == $datacrc;
        $lenOK = $isize == strlen($data);
        if (!$lenOK || !$crcOK) {
            $error = ($lenOK ? '' : 'Length check FAILED. ') . ($crcOK ? '' : 'Checksum FAILED.');

            return false;
        }

        return $data;
    }
}
