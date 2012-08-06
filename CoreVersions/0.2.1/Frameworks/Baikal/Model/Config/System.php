<?php
#################################################################
#  Copyright notice
#
#  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://baikal.codr.fr
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

namespace Baikal\Model\Config;

class System extends \Baikal\Model\Config {
	
	protected $aConstants = array(
		"BAIKAL_PATH_SABREDAV" => array(
			"type" => "litteral",
		),
		"BAIKAL_AUTH_REALM" => array(
			"type" => "string",
		),
		"BAIKAL_CARD_BASEURI" => array(
			"type" => "litteral",
		),
		"BAIKAL_CAL_BASEURI" => array(
			"type" => "litteral",
		),
		"BAIKAL_STANDALONE_ALLOWED" => array(
			"type" => "boolean",
		),
		"BAIKAL_STANDALONE_PORT" => array(
			"type" => "integer",
		),
		"PROJECT_SQLITE_FILE" => array(
			"type" => "litteral",
		),
	);
		
	protected $aData = array(
		"BAIKAL_PATH_SABREDAV" => "",
		"BAIKAL_AUTH_REALM" => "",
		"BAIKAL_CARD_BASEURI" => "",
		"BAIKAL_CAL_BASEURI" => "",
		"BAIKAL_STANDALONE_ALLOWED" => "",
		"BAIKAL_STANDALONE_PORT" => "",
		"PROJECT_SQLITE_FILE" => "",
	);
	
	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_CAL_BASEURI",
			"label" => "CalDAV base URI",
			"validation" => "required",
			"help" => "The absolute web path to cal.php",
			"popover" => array(
				"title" => "CalDAV base URI",
				"content" => "If Baïkal is hosted in a subfolder, this path should reflect it.<br /><strong>Whatever happens, it should begin and end with a slash.</strong>",
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_CARD_BASEURI",
			"label" => "CardDAV base URI",
			"validation" => "required",
			"help" => "The absolute web path to card.php",
			"popover" => array(
				"title" => "CardDAV base URI",
				"content" => "If Baïkal is hosted in a subfolder, this path should reflect it.<br /><strong>Whatever happens, it should begin and end with a slash.</strong>"
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_AUTH_REALM",
			"label" => "Auth realm",
			"validation" => "required",
			"help" => "Token used in authentication process.<br />If you change this, you'll have to reset all your users passwords.<br />You'll also loose access to this admin interface.",
			"popover" => array(
				"title" => "Auth realm",
				"content" => "If you change this, you'll loose your access to this interface.<br />In other words: <strong>you should not change this, unless YKWYD.</strong>"
			)
		)));
		
		if(\Flake\Util\Frameworks::enabled("BaikalStandalone")) {
			$oMorpho->add(new \Formal\Element\Checkbox(array(
				"prop" => "BAIKAL_STANDALONE_ALLOWED",
				"label" => "Allow Standalone Baïkal execution"
			)));

			$oMorpho->add(new \Formal\Element\Text(array(
				"prop" => "BAIKAL_STANDALONE_PORT",
				"label" => "Standalone Baïkal port"
			)));
		}
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "BAIKAL_PATH_SABREDAV",
			"label" => "Path to SabreDAV",
			"validation" => "required",
			"inputclass" => "input-xxlarge",
			"help" => "The absolute server path to SabreDAV API",
			"popover" => array(
				"title" => "Path to SabreDAV",
				"content" => "If Baïkal is hosted in a subfolder, this path should reflect it.<br /><strong>Whatever happens, it should begin and end with a slash.</strong>",
				"position" => "top"
			)
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_SQLITE_FILE",
			"label" => "SQLite file path",
			"validation" => "required",
			"inputclass" => "input-xxlarge",
			"help" => "The absolute server path to the SQLite file",
		)));
				
		return $oMorpho;
	}
		
	public function label() {
		return "Baïkal Settings";
	}
}