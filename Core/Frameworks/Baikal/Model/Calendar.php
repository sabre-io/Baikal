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

class Calendar extends \Flake\Core\Model\Db {
	const DATATABLE = "calendars";
	const PRIMARYKEY = "id";
	const LABELFIELD = "displayname";

	protected $aData = array(
		"principaluri" => "",
		"displayname" => "",
		"uri" => "",
		"ctag" => 0,
		"description" => "",
		"calendarorder" => 0,
		"calendarcolor" => "",
		"timezone" => "",
		"components" => "",
	);

	public static function icon() {
		return "icon-calendar";
	}

	public static function mediumicon() {
		return "glyph-calendar";
	}

	public static function bigicon() {
		return "glyph2x-calendar";
	}

	public function getEventsBaseRequester() {
		$oBaseRequester = \Baikal\Model\Calendar\Event::getBaseRequester();
		$oBaseRequester->addClauseEquals(
			"calendarid",
			$this->get("id")
		);

		return $oBaseRequester;
	}

	public function get($sPropName) {

		if($sPropName === "todos") {
			# TRUE if components contains VTODO, FALSE otherwise
			if(($sComponents = $this->get("components")) !== "") {
				$aComponents = explode(",", $sComponents);
			} else {
				$aComponents = array();
			}

			return in_array("VTODO", $aComponents);
		}

		if($sPropName === "notes") {
			# TRUE if components contains VJOURNAL, FALSE otherwise
			if(($sComponents = $this->get("components")) !== "") {
				$aComponents = explode(",", $sComponents);
			} else {
				$aComponents = array();
			}

			return in_array("VJOURNAL", $aComponents);
		}

		return parent::get($sPropName);
	}

	public function set($sPropName, $sValue) {

		if($sPropName === "todos") {

			if(($sComponents = $this->get("components")) !== "") {
				$aComponents = explode(",", $sComponents);
			} else {
				$aComponents = array();
			}

			if($sValue === TRUE) {
				if(!in_array("VTODO", $aComponents)) {
					$aComponents[] = "VTODO";
				}
			} else {
				if(in_array("VTODO", $aComponents)) {
					unset($aComponents[array_search("VTODO", $aComponents)]);
				}
			}

			return parent::set("components", implode(",", $aComponents));
		}

		if($sPropName === "notes") {

			if(($sComponents = $this->get("components")) !== "") {
				$aComponents = explode(",", $sComponents);
			} else {
				$aComponents = array();
			}

			if($sValue === TRUE) {
				if(!in_array("VJOURNAL", $aComponents)) {
					$aComponents[] = "VJOURNAL";
				}
			} else {
				if(in_array("VJOURNAL", $aComponents)) {
					unset($aComponents[array_search("VJOURNAL", $aComponents)]);
				}
			}

			return parent::set("components", implode(",", $aComponents));
		}

		return parent::set($sPropName, $sValue);
	}

	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();

		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "uri",
			"label" => "Calendar token ID",
			"validation" => "required,tokenid",
			"popover" => array(
				"title" => "Calendar token ID",
				"content" => "The unique identifier for this calendar.",
			)
		)));

		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "displayname",
			"label" => "Display name",
			"validation" => "required",
			"popover" => array(
				"title" => "Display name",
				"content" => "This is the name that will be displayed in your CalDAV client.",
			)
		)));

		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "description",
			"label" => "Description"
		)));

		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "todos",
			"label" => "Todos",
			"help" => "If checked, todos will be enabled on this calendar.",
		)));

		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "notes",
			"label" => "Notes",
			"help" => "If checked, notes will be enabled on this calendar.",
		)));


		if($this->floating()) {
			$oMorpho->element("uri")->setOption(
				"help",
				"Allowed characters are digits, lowercase letters and the dash symbol '-'."
			);
		} else {
			$oMorpho->element("uri")->setOption("readonly", TRUE);
		}

		return $oMorpho;
	}

	public function isDefault() {
		return $this->get("uri") === "default";
	}

	public function destroy() {
		$oEvents = $this->getEventsBaseRequester()->execute();
		foreach($oEvents as $event) {
			$event->destroy();
		}

		parent::destroy();
	}
}
